<?php

namespace Controllers;

use Database\Connection\Connection;
use Database\Connection\DatabaseException;
use Services\EventService;

class EventController extends Controller {
    /**
     * Show a list of events
     * @throws DatabaseException
     */
    public function list(): void {
        $events = $this->db->executeQuery(<<<SQL
            SELECT e.id, e.name, e.description, e.start_date, e.end_date, 
                   e.current_participant_count, ec.name AS category_name
            FROM events e
            JOIN event_categories ec ON e.category_id = ec.id
            WHERE e.start_date >= NOW()
            ORDER BY e.start_date ASC, e.end_date DESC
            SQL,
        );

        $this->render('events/list', ['events' => $events]);
    }

    /**
     * Show a single event.
     * @throws DatabaseException
     */
    public function show(int $id): void {
        $event = $this->db->executeQuery(<<<SQL
            SELECT e.id, e.name, e.description, e.start_date, e.end_date, 
                   e.current_participant_count, ec.name AS category_name
            FROM events e
            JOIN event_categories ec ON e.category_id = ec.id
            WHERE e.id = :id
            SQL,
            ['id' => $id],
        );

        if (!$event) {
            $this->json(['error' => 'Event not found'], 404);
        } else {
            $this->render('events/show', ['event' => $event]);
        }
    }

    /**
     * @throws DatabaseException
     */
    function addEvent(Connection $db, array $eventData): void {
        $query = <<<SQL
          INSERT INTO events 
          (user_id, category_id, name, description, max_participant_count, start_date, end_date) 
          VALUES (:user_id, :category_id, :name, :description, :max_participant_count, :start_date, :end_date)
        SQL;
        $db->executeQuery($query, $eventData);
    }
}