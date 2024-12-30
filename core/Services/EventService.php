<?php

namespace Services;

class EventService extends Service {
    public function find(int $id): array|null {
        return $this->db->query(<<<SQL
            SELECT e.id, e.name, e.description, e.start_date, e.end_date, e.max_participant_count,
                   e.current_participant_count, e.cancelled, ec.name AS category_name
            FROM events e
            JOIN event_categories ec ON e.category_id = ec.id
            WHERE e.id = :id
            SQL,
            ['id' => $id],
        );
    }

    public function listCategories(): array {
        return $this->db->query(<<<SQL
            SELECT id, name, sort_order, created_at, updated_at FROM event_categories
            ORDER BY sort_order
        SQL);
    }

    public function listUpcomingEvents(): array {
        return $this->db->query(<<<SQL
            SELECT e.id, e.name, e.description, e.start_date, e.end_date, e.max_participant_count,
                   e.current_participant_count, e.cancelled, ec.name AS category_name
            FROM events e
            JOIN event_categories ec ON e.category_id = ec.id
            WHERE e.cancelled = false AND e.start_date >= NOW()
            ORDER BY e.start_date ASC, e.end_date DESC
            SQL,
        );
    }

    public function getTrendingEvents(): array {
        return $this->db->query(<<<SQL
          SELECT e.id, e.name, e.description, e.start_date, e.end_date, e.max_participant_count,
                 e.current_participant_count, e.cancelled, ec.name AS category_name
          FROM events e
          JOIN event_categories ec ON e.category_id = ec.id
          WHERE e.cancelled = false
          ORDER BY e.current_participant_count DESC, e.start_date
          LIMIT 5
        SQL);
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
}