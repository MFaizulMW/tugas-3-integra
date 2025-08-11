<?php
namespace App\Models;

use App\Config\Database;

class BaseModel {
    protected $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }
}
