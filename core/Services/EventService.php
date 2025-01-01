<?php

namespace Services;

class EventService extends Service {
    public function find(int $id, bool $useCached = true, bool $includeDeleted = false): array|null {
        static $cachedEvents = [];
        if ($useCached && isset($cachedEvents[$id])) return $cachedEvents[$id];
        $deletedAtQuery = $includeDeleted ? '' : 'AND deleted_at IS NULL';
        try {
            $event = $this->db->query(<<<SQL
                SELECT e.*, ec.name AS category_name
                FROM events e
                JOIN event_categories ec ON e.category_id = ec.id
                WHERE e.id = :id $deletedAtQuery
                SQL,
                ['id' => $id],
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
                SELECT e.*, ec.name AS category_name
                FROM events e
                JOIN event_categories ec ON e.category_id = ec.id
                WHERE e.start_date >= NOW() $cancelledQuery
                    AND e.deleted_at IS NULL
                ORDER BY e.start_date ASC, e.end_date DESC
                SQL,
                empty($cancelledQuery) ? [] : ['user_id' => $userId],
            );
        } catch (\Exception) { }
        return [];
    }

    public function getTrendingEvents(): array {
        try {
            $userId = resolve(Auth::class)->getUserId();
            return $this->db->query(<<<SQL
                SELECT e.*, ec.name AS category_name
                FROM events e
                JOIN event_categories ec ON e.category_id = ec.id
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

    public function isJustCreatedByEventData(array $eventData): bool {
        try {
            $result = $this->db->query(<<<SQL
                SELECT 1 FROM events
                WHERE name = :name AND start_date = :start_date AND end_date = :end_date AND user_id = :user_id
                    AND cancelled = false AND deleted_at IS NULL AND category_id = :category_id
                    AND max_participant_count = :max_participant_count
                SQL,
                [
                    'name' => $eventData['name'],
                    'start_date' => $eventData['start_date'],
                    'end_date' => $eventData['end_date'],
                    'user_id' => $eventData['user_id'],
                    'category_id' => $eventData['category_id'],
                    'max_participant_count' => $eventData['max_participant_count'],
                ],
            );
            return !empty($result);
        } catch (\Exception) { }
        return false;
    }

    private function notifyEventCancellation(int $id): void {

    }

    public function cancelEvent(int $id): ?bool {
        try {
            $success = $this->db->execute(<<<SQL
                UPDATE events SET cancelled = true WHERE id = ? AND deleted_at IS NULL
            SQL, [$id]);
            if ($success) $this->notifyEventCancellation($id);
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
}