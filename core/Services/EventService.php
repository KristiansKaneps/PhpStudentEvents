<?php

namespace Services;

class EventService extends Service {
    public function find(int $id): array|null {
        return $this->db->executeQuery(<<<SQL
            SELECT e.id, e.name, e.description, e.start_date, e.end_date, 
                   e.current_participant_count, ec.name AS category_name
            FROM events e
            JOIN event_categories ec ON e.category_id = ec.id
            WHERE e.id = :id
            SQL,
            ['id' => $id],
        );
    }

    public function listUpcomingEvents(): array {
        return $this->db->executeQuery(<<<SQL
            SELECT e.id, e.name, e.description, e.start_date, e.end_date, 
                   e.current_participant_count, ec.name AS category_name
            FROM events e
            JOIN event_categories ec ON e.category_id = ec.id
            WHERE e.cancelled = false AND e.start_date >= NOW()
            ORDER BY e.start_date ASC, e.end_date DESC
            SQL,
        );
    }

    public function getTrendingEvents(): array {
        return $this->db->executeQuery(<<<SQL
          SELECT e.id, e.name, e.description, e.start_date, e.end_date,
                 e.current_participant_count, ec.name AS category_name
          FROM events e
          JOIN event_categories ec ON e.category_id = ec.id
          WHERE e.cancelled = false
          ORDER BY e.current_participant_count DESC, e.start_date
          LIMIT 5
        SQL);
    }

    public function addEvent(array $eventData): void {
        $query = <<<SQL
          INSERT INTO events 
          (user_id, category_id, name, description, max_participant_count, start_date, end_date) 
          VALUES (:user_id, :category_id, :name, :description, :max_participant_count, :start_date, :end_date)
        SQL;
        $this->db->executeQuery($query, $eventData);
    }
}