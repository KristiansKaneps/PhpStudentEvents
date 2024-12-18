<?php

namespace Controllers;

use Services\EventService;

class EventController extends Controller {
    public function show(EventService $eventService, int $id): void {
        $event = $eventService->find($id);
        if (!$event) {
            $this->json(['error' => 'Event not found'], 404);
        } else {
            $this->render('pages/event', ['event' => $event]);
        }
    }
}