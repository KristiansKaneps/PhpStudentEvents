<?php

namespace Controllers;

use Database\Database;
use JetBrains\PhpStorm\NoReturn;
use Router\Request;
use Router\Router;
use Services\Auth;
use Services\NotificationService;
use Types\NotificationType;

abstract class Controller {
    protected readonly Database $db;

    protected readonly Auth $auth;
    protected readonly NotificationService $notificationService;

    public function __construct() {
        $this->db = Database::getInstance();
        $this->auth = resolve(Auth::class);
        $this->notificationService = resolve(NotificationService::class);
    }

    /**
     * Render a view template inside a layout.
     * @param string $view Path to the view file (e.g., 'pages/index').
     * @param array $data Data to pass to the view.
     * @param string|null $layout Layout file (default: 'layouts/main') or null if no layout is needed.
     */
    protected function render(string $view, array $data = [], string|null $layout = 'layouts/main'): void {
        extract($data);

        if ($layout === null) {
            require VIEW_DIR . "$view.php";
            return;
        }

        ob_start();
        require VIEW_DIR . "$view.php";
        $content = ob_get_clean();

        require VIEW_DIR . "$layout.php";
    }

    /**
     * Flash a value from session.
     * @param mixed $key Key (or key-value pairs if an array).
     * @param mixed|null $value Value (or hops if the key is an array).
     * @param int $hops How many hops (or redirects) should this flash data be available for? (default: 0)
     *                  (unused if the key is an array).
     * @return void
     */
    protected function flash(mixed $key, mixed $value = null, int $hops = 0): void {
        if (is_array($key)) {
            foreach ($key as $k => $v)
                $this->flash($k, $v, max($hops, empty($value) ? 0 : (is_int($value) ? $value : 0)));
        } else if (is_object($key) && get_class($key) === Request::class) {
            $this->flash($key->data, $value, $hops);
        } else {
            $_SESSION['flash'][$key]['data'] = $value;
            $_SESSION['flash'][$key]['hops'] = $hops;
        }
    }

    /**
     * Show a toast notification from session.
     * @param int|string|NotificationType $type Notification type.
     * @param string $text Notification text.
     * @param int|null $timeout Notification timeout in web (or `null` if default timeout of 3500ms).
     * @param int|null $eventId Corresponding event ID (if any).
     * @return void
     */
    protected function toast(int|string|NotificationType $type, string $text, int|null $timeout = null, int|null $eventId = null): void {
        $this->notificationService->createToastNotification($type, $text, $timeout, $eventId);
    }

    /**
     * Show a success toast notification from session.
     * @param string $text Notification text.
     * @param int|null $timeout Notification timeout in web (or `null` if default timeout of 3500ms).
     * @param int|null $eventId Corresponding event ID (if any).
     * @return void
     */
    protected function toastSuccess(string $text, int|null $timeout = null, int|null $eventId = null): void {
        $this->notificationService->createToastNotification(NotificationType::SUCCESS, $text, $timeout, $eventId);
    }

    /**
     * Show an error toast notification from session.
     * @param string $text Notification text.
     * @param int|null $timeout Notification timeout in web (or `null` if default timeout of 3500ms).
     * @param int|null $eventId Corresponding event ID (if any).
     * @return void
     */
    protected function toastError(string $text, int|null $timeout = null, int|null $eventId = null): void {
        $this->notificationService->createToastNotification(NotificationType::ERROR, $text, $timeout, $eventId);
    }

    /**
     * Show an info toast notification from session.
     * @param string $text Notification text.
     * @param int|null $timeout Notification timeout in web (or `null` if default timeout of 3500ms).
     * @param int|null $eventId Corresponding event ID (if any).
     * @return void
     */
    protected function toastInfo(string $text, int|null $timeout = null, int|null $eventId = null): void {
        $this->notificationService->createToastNotification(NotificationType::INFO, $text, $timeout, $eventId);
    }

    /**
     * Return a JSON response.
     * @param mixed $data
     * @param int $status
     * @return void
     */
    #[NoReturn] protected function json(mixed $data, int $status = 200): void {
        header('Content-Type: application/json');
        http_response_code($status);
        echo json_encode($data);
        exit;
    }

    /**
     * Redirect to a given URL.
     * @param string $urlOrRoute Route to redirect to.
     * @param mixed $parameters Route parameters (for example, `.../{id}`).
     * @return void
     */
    #[NoReturn] protected function redirect(string $urlOrRoute, mixed $parameters = null): void {
        $url = Router::route($urlOrRoute, $parameters);
        header("Location: $url");
        exit;
    }

    #[NoReturn] protected function error(int $statusCode, string $template = 'unknown.html'): void {
        http_response_code($statusCode);
        include VIEW_DIR . DIRECTORY_SEPARATOR . 'error' . DIRECTORY_SEPARATOR . $template;
        exit;
    }

    #[NoReturn] protected function unauthorized(): void {
        $this->error(401, '401.html');
    }

    #[NoReturn] protected function forbidden(): void {
        $this->error(403, '403.html');
    }

    #[NoReturn] protected function notFound(): void {
        $this->error(404, '404.html');
    }

    #[NoReturn] protected function badRequest(): void {
        $this->error(405, '405.html');
    }

    #[NoReturn] protected function internalError(): void {
        $this->error(500, 'unknown.html');
    }
}