<?php
use Controllers\EventController;
use Controllers\HomeController;
use Router\Router;

Router::get('/', [HomeController::class, 'index'])->name('home', 'index');
Router::get('/event/list', [EventController::class, 'list'])->name('event.list');
Router::get('/event/show/{id}', [EventController::class, 'show'])->name('event.show');
