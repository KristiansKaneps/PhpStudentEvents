<?php

namespace Database\Connection;

use Exception;

class DatabaseException extends Exception {
    public function __construct(string $msg) {
        parent::__construct($msg);
    }

    public static function from(Exception $exception): self {
        return new self($exception->getMessage());
    }
}