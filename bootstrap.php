<?php
require_once __DIR__ . '/vendor/autoload.php';

use App\Core\Database;
use App\Core\SchemaManager;

// Initialize the database connection
$database = Database::getInstance();

// Initialize schema manager and create tables if needed
$schemaManager = new SchemaManager($database);
$schemaManager->initializeSchema();