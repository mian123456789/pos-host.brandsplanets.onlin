<?php
declare(strict_types=1);

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Accept');
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

$storageDir = dirname(__DIR__) . '/storage';
$storageFile = $storageDir . '/pos-state.json';

if (!is_dir($storageDir)) {
    mkdir($storageDir, 0755, true);
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (!is_file($storageFile)) {
        echo json_encode(['state' => null, 'updatedAt' => null]);
        exit;
    }

    readfile($storageFile);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $raw = file_get_contents('php://input');
    $payload = json_decode($raw, true);

    if (!is_array($payload) || !isset($payload['state']) || !is_array($payload['state'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid state payload']);
        exit;
    }

    $payload['updatedAt'] = $payload['updatedAt'] ?? gmdate('c');
    file_put_contents($storageFile, json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES), LOCK_EX);

    echo json_encode(['ok' => true, 'updatedAt' => $payload['updatedAt']]);
    exit;
}

http_response_code(405);
echo json_encode(['error' => 'Method not allowed']);
