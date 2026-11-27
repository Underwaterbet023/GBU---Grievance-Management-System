<?php
// Copy this file to config/config.php and update with your local database credentials
return [
    'db' => [
        'host' => 'localhost',
        'name' => 'gbu_grievance',
        'user' => 'root',
        'pass' => '',
        'charset' => 'utf8mb4',
    ],
    // Optional n8n webhook for AI grievance summarization (leave empty to disable)
    'n8n_webhook_url' => '',
];
