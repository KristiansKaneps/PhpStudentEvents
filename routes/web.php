<?php

use Controllers\AuthController;
use Controllers\EventController;
use Controllers\HomeController;
use Controllers\ProfileController;
use Router\Router;

// Home Controller
Router::get('/', [HomeController::class, 'index'])->name('home', 'index');
Router::get('/events', [HomeController::class, 'eventList'])->name('events');
Router::get('/about', [HomeController::class, 'about'])->name('about');
Router::get('/contact', [HomeController::class, 'contact'])->name('contact');

// Auth Controller
Router::define(['GET', 'POST'], '/login', [AuthController::class, 'login'])->name('login');
Router::define(['GET', 'POST'], '/register', [AuthController::class, 'register'])->name('register');
Router::define(['GET', 'POST'], '/logout', [AuthController::class, 'logout'])->name('logout');

// Profile Controller
Router::get('/profile', [ProfileController::class, 'profile'])->name('profile');
Router::post('/profile/update', [ProfileController::class, 'profile'])->name('profile.update');

// Event Controller
Router::get('/event/{id}', [EventController::class, 'show'])->name('event.show');
