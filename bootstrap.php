<?php
require_once __DIR__ . '/vendor/autoload.php';

use App\Core\Database;
use App\Core\SchemaManager;

$database = Database::getInstance();

$schemaManager = new SchemaManager($database);
$schemaManager->initializeSchema();