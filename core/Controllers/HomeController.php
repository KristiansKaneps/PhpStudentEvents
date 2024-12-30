<?php

namespace Controllers;

use Services\EventService;

class HomeController extends Controller {
    public function index(EventService $eventService): void {
        $this->render('pages/index', ['events' => $eventService->getTrendingEvents()]);
    }

    public function about(): void {
        $this->render('pages/about');
    }

    public function contact(): void {
        $this->render('pages/contact');
    }
}