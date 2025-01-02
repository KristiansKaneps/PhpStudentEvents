<?php

namespace Services;

class EventService extends Service {
    public function find(int $id, bool $useCached = true, bool $includeDeleted = false): array|null {
        static $cachedEvents = [];
        if ($useCached && isset($cachedEvents[$id])) return $cachedEvents[$id];
        $deletedAtQuery = $includeDeleted ? '' : 'AND deleted_at IS NULL';
        try {
            $auth = resolve(Auth::class);
            $userId = $auth->getUserId();
            $event = $this->db->query(<<<SQL
                SELECT e.*, ec.name AS category_name, (ep.event_id IS NOT NULL) AS participates
                FROM events e
                JOIN event_categories ec ON e.category_id = ec.id
                LEFT JOIN event_participants ep ON e.id = ep.event_id AND ep.user_id = :user_id
                WHERE e.id = :id $deletedAtQuery
                SQL,
                ['id' => $id, 'user_id' => $userId],
            );
            $event = empty($event) ? null : $event[0];
            $cachedEvents[$id] = $event;
            return $event;
        } catch (\Exception) { }
        return null;
    }

    public function listCategories(): array {
        try {
            return $this->db->query(<<<SQL
                SELECT id, name, sort_order, created_at, updated_at FROM event_categories
                ORDER BY sort_order
            SQL);
        } catch (\Exception) { }
        return [];
    }

    public function listUpcomingEvents(): array {
        try {
            $auth = resolve(Auth::class);
            $userId = $auth->getUserId();
            $cancelledQuery = $auth->hasAdminRole() ? '' : 'AND (e.user_id = :user_id OR e.cancelled = false)';
            return $this->db->query(<<<SQL
                SELECT e.*, ec.name AS category_name, (ep.event_id IS NOT NULL) AS participates
                FROM events e
                JOIN event_categories ec ON e.category_id = ec.id
                LEFT JOIN event_participants ep ON e.id = ep.event_id AND ep.user_id = :user_id
                WHERE e.start_date >= NOW() $cancelledQuery
                    AND e.deleted_at IS NULL
                ORDER BY e.start_date ASC, e.end_date DESC
                SQL,
                ['user_id' => $userId],
            );
        } catch (\Exception) {
        }
        return [];
    }

    public function getTrendingEvents(): array {
        try {
            $userId = resolve(Auth::class)->getUserId();
            return $this->db->query(<<<SQL
                SELECT e.*, ec.name AS category_name, (ep.event_id IS NOT NULL) AS participates
                FROM events e
                JOIN event_categories ec ON e.category_id = ec.id
                LEFT JOIN event_participants ep ON e.id = ep.event_id AND ep.user_id = :user_id
                WHERE e.cancelled = false AND (e.user_id = :user_id OR e.cancelled = false)
                    AND e.deleted_at IS NULL
                ORDER BY e.current_participant_count DESC, e.start_date
                LIMIT 5
                SQL,
                ['user_id' => $userId],
            );
        } catch (\Exception) { }
        return [];
    }

    public function getParticipatedEventIds(?int $userId = null): array {
        try {
            $auth = resolve(Auth::class);
            $userId = $auth->getUser($userId);
            $eventParticipants = $this->db->query(<<<SQL
                SELECT event_id FROM event_participants
                WHERE user_id = ?
                SQL,
                ['user_id' => $userId],
            );
            return array_map(function ($record) {
                return $record['event_id'];
            }, $eventParticipants);
        } catch (\Exception) { }
        return [];
    }

    const CREATE_EVENT_RESULT_SUCCESS = 0;
    const CREATE_EVENT_RESULT_EXCEPTION = 1;

    public function createEvent(array $eventData): int {
        try {
            $query = <<<SQL
                INSERT INTO events 
                (user_id, category_id, name, description, max_participant_count, start_date, end_date) 
                VALUES (:user_id, :category_id, :name, :description, :max_participant_count, :start_date, :end_date)
            SQL;
            if ($this->db->execute($query, $eventData))
                return self::CREATE_EVENT_RESULT_SUCCESS;
        } catch (\Exception) { }
        return self::CREATE_EVENT_RESULT_EXCEPTION;
    }

    public function getParticipants(int $id): array {
        try {
            return $this->db->query('SELECT user_id FROM event_participants WHERE event_id = ?', [$id]);
        } catch (\Exception) { }
        return [];
    }

    public function cancelEvent(int $id): ?bool {
        try {
            $success = $this->db->execute(<<<SQL
                UPDATE events SET cancelled = true WHERE id = ? AND deleted_at IS NULL
            SQL, [$id]);
            return $success ? ($this->db->rowCount() > 0 ? true : null) : false;
        } catch (\Exception) { }
        return false;
    }

    public function deleteEvent(int $id): ?bool {
        try {
            $success = $this->cancelEvent($id) !== false && $this->db->execute(<<<SQL
                UPDATE events SET deleted_at = now() WHERE id = ? AND deleted_at IS NULL
            SQL, [$id]);
            return $success ? ($this->db->rowCount() > 0 ? true : null) : false;
        } catch (\Exception) { }
        return false;
    }

    const ADD_PARTICIPANT_RESULT_SUCCESS = 0;
    const ADD_PARTICIPANT_RESULT_ALREADY_PARTICIPATES = 1;
    const ADD_PARTICIPANT_RESULT_MAX_PARTICIPANTS_REACHED = 2;
    const ADD_PARTICIPANT_RESULT_EVENT_NOT_FOUND = 3;
    const ADD_PARTICIPANT_RESULT_USER_NOT_FOUND = 4;
    const ADD_PARTICIPANT_RESULT_EXCEPTION = 5;

    public function addParticipant(int $eventId, int $userId): int {
        try {
            $eventService = resolve(EventService::class);
            $event = $eventService->find($eventId);
            if (empty($event))
                return self::ADD_PARTICIPANT_RESULT_EVENT_NOT_FOUND;

            if (!resolve(Auth::class)->userExists($userId))
                return self::ADD_PARTICIPANT_RESULT_USER_NOT_FOUND;

            $isParticipant = !empty($this->db->query(<<<SQL
                SELECT 1 FROM event_participants WHERE event_id = ? AND user_id = ?
                SQL,
                [$eventId, $userId]
            ));
            if ($isParticipant)
                return self::ADD_PARTICIPANT_RESULT_ALREADY_PARTICIPATES;

            $maxParticipantCount = $event['max_participant_count'];
            $currentParticipantCount = $event['current_participant_count'];

            if ($maxParticipantCount !== 0 && $currentParticipantCount >= $maxParticipantCount)
                return self::ADD_PARTICIPANT_RESULT_MAX_PARTICIPANTS_REACHED;

            $this->db->beginTransaction();

            $success = $this->db->execute(
                'UPDATE events SET current_participant_count = current_participant_count + 1 WHERE id = ?',
                [$eventId]
            );
            if (!$success)
                return self::ADD_PARTICIPANT_RESULT_EXCEPTION;

            $success = $this->db->execute(
                'INSERT INTO event_participants (user_id, event_id) VALUES (:user_id, :event_id)',
                ['user_id' => $userId, 'event_id' => $eventId]
            );
            if (!$success)
                return self::ADD_PARTICIPANT_RESULT_EXCEPTION;

            $this->db->commitTransaction();
            return self::ADD_PARTICIPANT_RESULT_SUCCESS;
        } catch (\Exception) { }
        return self::ADD_PARTICIPANT_RESULT_EXCEPTION;
    }

    const REMOVE_PARTICIPANT_RESULT_SUCCESS = 0;
    const REMOVE_PARTICIPANT_RESULT_IS_NOT_PARTICIPANT = 1;
    const REMOVE_PARTICIPANT_RESULT_EVENT_NOT_FOUND = 2;
    const REMOVE_PARTICIPANT_RESULT_EXCEPTION = 3;

    public function removeParticipant($eventId, $userId): int {
        try {
            $eventService = resolve(EventService::class);
            $event = $eventService->find($eventId);
            if (empty($event))
                return self::REMOVE_PARTICIPANT_RESULT_EVENT_NOT_FOUND;

            $isNotParticipant = empty($this->db->query(<<<SQL
                SELECT 1 FROM event_participants WHERE event_id = ? AND user_id = ?
                SQL,
                [$eventId, $userId]
            ));
            if ($isNotParticipant)
                return self::REMOVE_PARTICIPANT_RESULT_IS_NOT_PARTICIPANT;

            $this->db->beginTransaction();

            $success = $this->db->execute(
                'UPDATE events SET current_participant_count = current_participant_count - 1 WHERE id = ?',
                [$eventId]
            );
            if (!$success)
                return self::REMOVE_PARTICIPANT_RESULT_EXCEPTION;

            $success = $this->db->execute(
                'DELETE FROM event_participants WHERE user_id = :user_id AND event_id = :event_id',
                ['user_id' => $userId, 'event_id' => $eventId]
            );
            if (!$success)
                return self::REMOVE_PARTICIPANT_RESULT_EXCEPTION;

            $this->db->commitTransaction();
            return self::REMOVE_PARTICIPANT_RESULT_SUCCESS;
        } catch (\Exception) { }
        return self::REMOVE_PARTICIPANT_RESULT_EXCEPTION;
    }
}