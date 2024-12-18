<?php

namespace Services;

use Database\Connection\Connection;
use DI\Dependency;

abstract class Service implements Dependency {
    protected readonly Connection $db;

    public function __construct() {
        $this->db = Connection::getInstance();
    }
}