<?php
// ------------------------------------------------------------
// Number Info API Endpoint
// Shubham Hacker Edition
// ------------------------------------------------------------

require_once 'functions.php';

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

$api_key = $_GET['key'] ?? '';
$number = trim($_GET['num'] ?? '');

// Response builder
function respond($data, $code = 200) {
    http_response_code($code);
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;
}

// Validate key
if (empty($api_key)) {
    respond([
        'success' => false,
        'error' => 'API key required',
        'usage' => '/api.php?num=NUMBER&key=YOUR_KEY',
        'api_info' => [
            'developed_by' => COPYRIGHT,
            'organization' => 'Shubham Hacker'
        ]
    ], 401);
}

$keys = load_keys();
$key_data = find_key($keys, $api_key);

if (!$key_data) {
    respond([
        'success' => false,
        'error' => 'Invalid API key. Contact admin.',
        'api_info' => [
            'developed_by' => COPYRIGHT,
            'organization' => 'Shubham Hacker'
        ]
    ], 403);
}

// Check validity
list($valid, $message) = is_key_valid($key_data);
if (!$valid) {
    respond([
        'success' => false,
        'error' => $message,
        'api_info' => [
            'developed_by' => COPYRIGHT,
            'organization' => 'Shubham Hacker'
        ]
    ], 403);
}

// Validate number
if (empty($number)) {
    respond([
        'success' => false,
        'error' => 'Missing num parameter',
        'example' => '/api.php?num=8800952843&key=YOUR_KEY',
        'api_info' => [
            'developed_by' => COPYRIGHT,
            'organization' => 'Shubham Hacker'
        ]
    ], 400);
}

if (!preg_match('/^\d{10}$/', $number)) {
    respond([
        'success' => false,
        'error' => 'Invalid phone number. Must be 10 digits.',
        'api_info' => [
            'developed_by' => COPYRIGHT,
            'organization' => 'Shubham Hacker'
        ]
    ], 400);
}

// Call backend
$backend_data = call_backend($number);

if (!$backend_data) {
    respond([
        'success' => false,
        'error' => 'Backend API unavailable. Try again.',
        'api_info' => [
            'developed_by' => COPYRIGHT,
            'organization' => 'Shubham Hacker'
        ]
    ], 502);
}

// Increment usage
$keys = increment_usage($keys, $api_key);
save_keys($keys);

// Build response
if (($backend_data['status'] ?? '') === 'success') {
    $data = $backend_data['data'] ?? [];
    
    $result = [
        'success' => true,
        'number' => $number,
        'data' => [
            'name' => $data['name'] ?? 'N/A',
            'father_name' => $data['fname'] ?? 'N/A',
            'mobile' => $data['mobile'] ?? $number,
            'alternate_number' => $data['alt'] ?? 'N/A',
            'address' => clean_address($data['address'] ?? ''),
            'circle' => $data['circle'] ?? 'N/A',
            'id' => $data['id'] ?? 'N/A',
            'email' => $data['email'] ?? 'N/A'
        ],
        'checked_at' => date('Y-m-d H:i:s') . ' UTC',
        'api_info' => [
            'developed_by' => COPYRIGHT,
            'organization' => 'Shubham Hacker',
            'purpose' => 'For Educational Purposes Only'
        ]
    ];
    
    respond($result, 200);
} else {
    respond([
        'success' => false,
        'error' => 'No data found for this number',
        'number' => $number,
        'api_info' => [
            'developed_by' => COPYRIGHT,
            'organization' => 'Shubham Hacker'
        ]
    ], 404);
}
