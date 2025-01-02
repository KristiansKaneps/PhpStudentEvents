<?php

use Controllers\AuthController;
use Controllers\EventController;
use Controllers\HomeController;
use Controllers\ProfileController;
use Router\Router;

// Home Controller
Router::get('/', [HomeController::class, 'index'])->name('home', 'index');
Router::get('/about', [HomeController::class, 'about'])->name('about');

// Auth Controller
Router::define(['GET', 'POST'], '/login', [AuthController::class, 'login'])->name('login');
Router::define(['GET', 'POST'], '/register', [AuthController::class, 'register'])->name('register');
Router::define(['GET', 'POST'], '/logout', [AuthController::class, 'logout'])->name('logout');

// Profile Controller
Router::get('/profile', [ProfileController::class, 'profile'])->name('profile');
Router::post('/profile/update', [ProfileController::class, 'profile'])->name('profile.update');
Router::get('/profile/{userId}', [ProfileController::class, 'profile'])->name('profile.other');
Router::post('/profile/update/{userId}', [ProfileController::class, 'profile'])->name('profile.update.other');

// Event Controller
Router::get('/events', [EventController::class, 'list'])->name('event.list');
Router::get('/event/{id}', [EventController::class, 'view'])->name('event.view');
Router::post('/event/create', [EventController::class, 'create'])->name('event.create');
Router::post('/event/cancel/{id}', [EventController::class, 'listCancel'])->name('event.list.cancel');
Router::post('/event/delete/{id}', [EventController::class, 'listDelete'])->name('event.list.delete');
Router::post('/event/participant/add/{id}', [EventController::class, 'listAddParticipant'])->name('event.participant.add');
Router::post('/event/participant/remove/{id}', [EventController::class, 'listRemoveParticipant'])->name('event.participant.remove');
