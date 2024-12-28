<?php

namespace Services;

use Database\Database;
use DI\Dependency;

abstract class Service implements Dependency {
    protected readonly Database $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }
}