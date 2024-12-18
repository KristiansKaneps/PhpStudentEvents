<?php

namespace Controllers;

use Database\Connection\Connection;
use JetBrains\PhpStorm\NoReturn;

abstract class Controller {
    protected readonly Connection $db;

    public function __construct() {
        $this->db = Connection::getInstance();
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
        header("Location: $url");
        exit;
    }
}