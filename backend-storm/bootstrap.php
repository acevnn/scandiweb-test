<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Config\Database;
use App\Services\DataImporter;

$pdo = new Database()->getConnection();
$importer = new DataImporter($pdo);
$importer->import(__DIR__ . '/data/data.json');

echo "Data import complete.";
