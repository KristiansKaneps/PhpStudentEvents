<?php

namespace Services;

use Database\Connection\DatabaseException;

class EventService extends Service {
    /**
     * @throws DatabaseException
     */
    public function getEvents(): array {
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
}