<?php

namespace Controllers;

use Services\EventService;

class HomeController extends Controller {
    public function index(EventService $eventService): void {
        $this->render('pages/index', ['events' => $eventService->getTrendingEvents()]);
    }

    public function eventList(EventService $eventService): void {
        $this->render('pages/events', ['events' => $eventService->listUpcomingEvents()]);
    }

    public function contact(): void {
        $this->render('pages/contact');
    }

    public function about(): void {
        $this->render('pages/about');
    }
}