<?php

namespace Controllers;

use Database\Database;
use JetBrains\PhpStorm\NoReturn;
use Router\Request;
use Router\Router;

abstract class Controller {
    protected readonly Database $db;

    public function __construct() {
        $this->db = Database::getInstance();
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
     * @param mixed $key Key.
     * @param mixed|null $value Value.
     * @return void
     */
    protected function flash(mixed $key, mixed $value = null): void {
        if (is_array($key)) {
            foreach ($key as $k => $v)
                $this->flash($k, $v);
        } else if (is_object($key) && get_class($key) === Request::class) {
            $this->flash($key->data);
        } else {
            $_SESSION['flash'][$key] = $value;
        }
    }

    /**
     * Show a toast notification from session.
     * @param string $type Notification type (one of: `success`, `info`, `error`).
     * @param string $text Notification text.
     * @return void
     */
    protected function toast(string $type, string $text): void {
        if (!isset($_SESSION['flash']['toast'][$type]))
            $_SESSION['flash']['toast'][$type] = [];
        $_SESSION['flash']['toast'][$type][] = $text;
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
     * @param string $url
     * @return void
     */
    #[NoReturn] protected function redirect(string $url): void {
        $url = Router::route($url);
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