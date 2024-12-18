<?php

namespace Controllers;

use Services\EventService;

class HomeController extends Controller {
    private EventService $eventService;

    public function __construct() {
        parent::__construct();
        $this->eventService = resolve(EventService::class);
    }

    public function index(): void {
        $this->render('pages/index', ['events' => $this->eventService->getEvents()]);
    }
}