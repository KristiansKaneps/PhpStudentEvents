<?php

namespace Controllers;

use Router\Request;
use Services\Auth;
use Services\EventService;

class EventController extends Controller {
    public function list(EventService $eventService): void {
        $this->render('pages/events/list', ['events' => $eventService->listUpcomingEvents(), 'categories' => $eventService->listCategories()]);
    }

    public function view(EventService $eventService, int $id): void {
        $event = $eventService->find($id);
        if (empty($event)) {
            $this->notFound();
        } else {
            $this->render('pages/events/view', ['event' => $event, 'categories' => $eventService->listCategories()]);
        }
    }

    public function create(Auth $auth, EventService $eventService, Request $request): void {
        if (!$auth->hasOrganizerRole()) {
            $this->unauthorized();
        }

        $validationError = false;

        if ($request->empty('name')) {
            $validationError = true;
            $this->flash('error_name', t('validation.required', ['name' => t('form.label.event.name')]));
        }
        if ($request->empty('description')) {
            $validationError = true;
            $this->flash('error_description', t('validation.required', ['name' => t('form.label.event.description')]));
        }
        if (!$request->empty('max_participant_count') && $request->input('max_participant_count') < 0) {
            $validationError = true;
            $this->flash('error_max_participant_count', t('validation.event.max_participant_count_negative'));
        }
        if ($request->empty('start_date')) {
            $validationError = true;
            $this->flash('error_start_date', t('validation.required', ['name' => t('form.label.event.start_date')]));
        } else if (strtotime($request->input('start_date')) <= strtotime('now')) {
            $validationError = true;
            $this->flash('error_start_date', t('validation.event.start_in_past'));
        }
        if ($request->empty('end_date')) {
            $validationError = true;
            $this->flash('error_end_date', t('validation.required', ['name' => t('form.label.event.end_date')]));
        }
        if (!$request->empty('start_date') && !$request->empty('end_date') && strtotime($request->input('start_date')) >= strtotime($request->input('end_date'))) {
            $validationError = true;
            $this->flash('error_start_date', t('validation.event.end_before_start'));
            $this->flash('error_end_date', t('validation.event.end_before_start'));
        }

        if ($validationError) {
            $this->flash($request->data);
            $this->render('pages/events/list', ['events' => $eventService->listUpcomingEvents(), 'categories' => $eventService->listCategories()]);
            return;
        }

        $request->setInput('user_id', $auth->getUserId());
        $result = $eventService->createEvent($request->retainInput(['user_id', 'start_date', 'end_date', 'category_id', 'name', 'description', 'max_participant_count']));

        switch ($result) {
            case EventService::CREATE_EVENT_RESULT_SUCCESS:
                $this->toast('success', t('toast.success.event_created'));
                $this->render('pages/events/list', ['events' => $eventService->listUpcomingEvents(), 'categories' => $eventService->listCategories()]);
                return;
            case EventService::CREATE_EVENT_RESULT_EXCEPTION:
                $this->flash($request->data);
                $this->toast('error', t('toast.error.event_created'));
        }

        $this->render('pages/events/list', ['events' => $eventService->listUpcomingEvents(), 'categories' => $eventService->listCategories()]);
    }
}