<?php

namespace Controllers;

use JetBrains\PhpStorm\NoReturn;
use Router\Request;
use Services\Auth;
use Services\EventService;
use Services\NotificationService;
use Types\NotificationType;

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

    #[NoReturn] public function create(Auth $auth, EventService $eventService, Request $request): void {
        if (!$auth->hasOrganizerRole()) {
            $this->unauthorized();
        }

        $validationError = false;

        if ($request->empty('name')) {
            $validationError = true;
            $this->flash('error_name', t('validation.required', ['name' => t('form.label.event.name')]), 1);
        }
        if ($request->empty('description')) {
            $validationError = true;
            $this->flash('error_description', t('validation.required', ['name' => t('form.label.event.description')]), 1);
        }
        if (!$request->empty('max_participant_count') && $request->input('max_participant_count') < 0) {
            $validationError = true;
            $this->flash('error_max_participant_count', t('validation.event.max_participant_count_negative'), 1);
        }
        if ($request->empty('start_date')) {
            $validationError = true;
            $this->flash('error_start_date', t('validation.required', ['name' => t('form.label.event.start_date')]), 1);
        } else if (strtotime($request->input('start_date')) <= strtotime('now')) {
            $validationError = true;
            $this->flash('error_start_date', t('validation.event.start_in_past'), 1);
        }
        if ($request->empty('end_date')) {
            $validationError = true;
            $this->flash('error_end_date', t('validation.required', ['name' => t('form.label.event.end_date')]), 1);
        }
        if (!$request->empty('start_date') && !$request->empty('end_date') && strtotime($request->input('start_date')) >= strtotime($request->input('end_date'))) {
            $validationError = true;
            $this->flash('error_start_date', t('validation.event.end_before_start'), 1);
            $this->flash('error_end_date', t('validation.event.end_before_start'), 1);
        }

        if ($validationError) {
            $this->flash($request->data, 1);
            $this->redirect('event.list');
        }

        $request->setInput('user_id', $auth->getUserId());
        $eventData = $request->retainInput(['user_id', 'start_date', 'end_date', 'category_id', 'name', 'description', 'max_participant_count']);

        $result = $eventService->createEvent($eventData);
        switch ($result) {
            case EventService::CREATE_EVENT_RESULT_SUCCESS:
                $this->toastSuccess(t('toast.success.event_created'));
                break;
            case EventService::CREATE_EVENT_RESULT_EXCEPTION:
                $this->flash($request->data, 1);
                $this->toastError(t('toast.error.event_created'));
        }

        $this->redirect('event.list');
    }

    private function notifyEventCancellation(NotificationService $notificationService, array $event, array $participants): void {
        foreach ($participants as $participant) {
            $userId = $participant['user_id'];
            $notificationService->createToastNotification(NotificationType::INFO, t('toast.info.event_participant_cancelled', $event), 10000, null, $userId);
        }
    }

    #[NoReturn] public function listCancel(Auth $auth, EventService $eventService, NotificationService $notificationService, int $id): void {
        if (!$auth->hasOrganizerRole()) {
            $this->unauthorized();
        }

        $event = $eventService->find($id);

        if (empty($event)) {
            $this->notFound();
        }

        if ($event['user_id'] !== $auth->getUserId() && !$auth->hasAdminRole()) {
            $this->unauthorized();
        }

        $result = $eventService->cancelEvent($id); // `null` means that it did nothing (no message should be displayed)
        if ($result === true) {
            $participants = $eventService->getParticipants($event['id']);
            $this->notifyEventCancellation($notificationService, $event, $participants);
            $this->toastSuccess(t('toast.success.event_cancelled', ['name' => $event['name']]));
        } else if ($result === false) {
            $this->toastError(t('toast.error.event_cancelled', ['name' => $event['name']]));
        }

        $this->redirect('event.list');
    }

    #[NoReturn] public function listDelete(Auth $auth, EventService $eventService, NotificationService $notificationService, int $id): void {
        if (!$auth->hasOrganizerRole()) {
            $this->unauthorized();
        }

        $event = $eventService->find($id);

        if (empty($event)) {
            $this->notFound();
        }

        if ($event['user_id'] !== $auth->getUserId() && !$auth->hasAdminRole()) {
            $this->unauthorized();
        }

        $result = $eventService->deleteEvent($id); // `null` means that it did nothing (no message should be displayed)
        if ($result === true) {
            $participants = $eventService->getParticipants($event['id']);
            $this->notifyEventCancellation($notificationService, $event, $participants);
            $this->toastSuccess(t('toast.success.event_deleted', ['name' => $event['name']]));
        } else if ($result === false) {
            $this->toastError(t('toast.error.event_deleted', ['name' => $event['name']]));
        }

        $this->redirect('event.list');
    }

    #[NoReturn] public function listAddParticipant(Auth $auth, EventService $eventService, NotificationService $notificationService, int $id, ?int $userId = null): void {
        if (!$auth->hasUserRole()) {
            $this->unauthorized();
        }

        $userId = $userId === null ? $auth->getUserId() : $userId;

        if ($userId !== $auth->getUserId() && !$auth->hasAdminRole()) {
            $this->unauthorized();
        }

        $result = $eventService->addParticipant($id, $auth->getUserId());

        $event = $eventService->find($id);

        switch ($result) {
            case EventService::ADD_PARTICIPANT_RESULT_SUCCESS:
                $event['current_participant_count']++;
                $this->toastSuccess(t('toast.success.participation_created', ['name' => $event['name']]));
                $notificationService->createToastNotification(NotificationType::INFO, t('toast.info.event_participant_joined', $event), 10000, $event['id'], $event['user_id']);
                break;
            case EventService::ADD_PARTICIPANT_RESULT_ALREADY_PARTICIPATES:
                $this->toastError(t('toast.error.participation_created', ['name' => $event['name']]));
                break;
            case EventService::ADD_PARTICIPANT_RESULT_MAX_PARTICIPANTS_REACHED:
                $this->toastError(t('toast.error.participation_max_reached', ['name' => $event['name']]));
                break;
            case EventService::ADD_PARTICIPANT_RESULT_EVENT_NOT_FOUND:
                $this->toastError(t('toast.error.event_not_found', ['id' => $id]));
                break;
            case EventService::ADD_PARTICIPANT_RESULT_USER_NOT_FOUND:
                $this->toastError(t('toast.error.user_not_found', ['id' => $userId]));
                break;
            case EventService::ADD_PARTICIPANT_RESULT_EXCEPTION:
                $this->toastError(t('toast.error.participation_create_error', ['name' => $event['name']]));
        }

        $this->redirect('event.list');
    }

    #[NoReturn] public function listRemoveParticipant(Auth $auth, EventService $eventService, NotificationService $notificationService, int $id, ?int $userId = null): void {
        if (!$auth->hasUserRole()) {
            $this->unauthorized();
        }

        $userId = $userId === null ? $auth->getUserId() : $userId;

        if ($userId !== $auth->getUserId() && !$auth->hasAdminRole()) {
            $this->unauthorized();
        }

        $result = $eventService->removeParticipant($id, $auth->getUserId());

        $event = $eventService->find($id);

        switch ($result) {
            case EventService::REMOVE_PARTICIPANT_RESULT_SUCCESS:
                $event['current_participant_count']--;
                $this->toastSuccess(t('toast.success.participation_removed', ['name' => $event['name']]));
                $notificationService->createToastNotification(NotificationType::INFO, t('toast.info.event_participant_left', $event), 10000, $event['id'], $event['user_id']);
                break;
            case EventService::REMOVE_PARTICIPANT_RESULT_IS_NOT_PARTICIPANT:
                $this->toastError(t('toast.error.participation_removed', ['name' => $event['name']]));
                break;
            case EventService::REMOVE_PARTICIPANT_RESULT_EVENT_NOT_FOUND:
                $this->toastError(t('toast.error.event_not_found', ['id' => $id]));
                break;
            case EventService::REMOVE_PARTICIPANT_RESULT_EXCEPTION:
                $this->toastError(t('toast.error.participation_remove_error', ['name' => $event['name']]));
        }

        $this->redirect('event.list');
    }
}