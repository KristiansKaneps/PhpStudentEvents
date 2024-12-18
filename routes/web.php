<?php
use Controllers\EventController;
use Controllers\HomeController;
use Router\Router;

// Home Controller
Router::get('/', [HomeController::class, 'index'])->name('home', 'index');
Router::get('/events', [HomeController::class, 'eventList'])->name('events');
Router::get('/about', [HomeController::class, 'about'])->name('about');
Router::get('/contact', [HomeController::class, 'contact'])->name('contact');

// Event Controller
Router::get('/event/{id}', [EventController::class, 'show'])->name('event.show');
