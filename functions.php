<?php
// ------------------------------------------------------------
// Number Info API - Helper Functions (FIXED)
// Nexxon Exploits Edition
// ------------------------------------------------------------

define('DATA_DIR', '/tmp/nexxon_data');
define('KEYS_FILE', DATA_DIR . '/keys.json');
define('ADMIN_PASSWORD', 'shubham@77');
define('BACKEND_URL', 'https://nexxonexploitsvip.vercel.app/search');
define('COPYRIGHT', 'Shubham - Owner');

// Ensure data directory exists
if (!file_exists(DATA_DIR)) {
    @mkdir(DATA_DIR, 0755, true);
}

// Ensure keys.json exists
if (!file_exists(KEYS_FILE)) {
    @file_put_contents(KEYS_FILE, json_encode([]));
}

function load_keys() {
    if (!file_exists(KEYS_FILE)) return [];
    $data = @file_get_contents(KEYS_FILE);
    $keys = json_decode($data, true);
    return is_array($keys) ? $keys : [];
}

function save_keys($keys) {
    $dir = dirname(KEYS_FILE);

    if (!is_dir($dir)) {
        if (!mkdir($dir, 0775, true)) {
            return false;
        }
    }

    $json = json_encode(
        $keys,
        JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
    );

    if ($json === false) {
        return false;
    }

    return file_put_contents(KEYS_FILE, $json, LOCK_EX) !== false;
}

function generate_key($prefix = 'NXX') {
    $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $random = '';
    for ($i = 0; $i < 24; $i++) {
        $random .= $chars[random_int(0, strlen($chars) - 1)];
    }
    return $prefix . '_' . $random;
}

function find_key($keys, $key_value) {
    foreach ($keys as $k) {
        if (isset($k['key']) && $k['key'] === $key_value) {
            return $k;
        }
    }
    return null;
}

function find_key_by_id($keys, $id) {
    foreach ($keys as $k) {
        if (isset($k['id']) && $k['id'] === $id) {
            return $k;
        }
    }
    return null;
}

function is_key_valid($k) {
    if (($k['status'] ?? 'active') !== 'active') {
        return [false, 'API Key is inactive. Contact admin.'];
    }
    
    if (!empty($k['expires_at'])) {
        $expires = strtotime($k['expires_at'] . ' 23:59:59');
        if (time() > $expires) {
            return [false, 'API Key expired. Contact admin for renewal.'];
        }
    }
    
    $daily_limit = intval($k['daily_limit'] ?? 0);
    if ($daily_limit > 0) {
        $today = date('Y-m-d');
        $usage_date = $k['usage_date'] ?? '';
        $today_usage = ($usage_date === $today) ? intval($k['today_usage'] ?? 0) : 0;
        
        if ($today_usage >= $daily_limit) {
            return [false, 'Daily limit exceeded (' . $daily_limit . ' requests). Contact admin for more credit.'];
        }
    }
    
    return [true, 'OK'];
}

function increment_usage($keys, $key_value) {
    $today = date('Y-m-d');
    
    foreach ($keys as &$k) {
        if (isset($k['key']) && $k['key'] === $key_value) {
            $usage_date = $k['usage_date'] ?? '';
            
            if ($usage_date !== $today) {
                $k['today_usage'] = 0;
                $k['usage_date'] = $today;
            }
            
            $k['today_usage'] = intval($k['today_usage'] ?? 0) + 1;
            $k['total_usage'] = intval($k['total_usage'] ?? 0) + 1;
            $k['last_used'] = date('Y-m-d H:i:s') . ' UTC';
            break;
        }
    }
    
    return $keys;
}

function is_admin_logged_in() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

function require_admin() {
    if (!is_admin_logged_in()) {
        header('Location: admin.php?action=login');
        exit;
    }
}

/**
 * Call backend API with multiple fallback methods
 */
function call_backend($number) {
    $url = BACKEND_URL . '?q=' . urlencode($number);
    
    $userAgent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36';
    
    // ============================================
    // METHOD 1: cURL (Primary)
    // ============================================
    if (function_exists('curl_init')) {
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_CONNECTTIMEOUT => 15,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS => 5,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_ENCODING => '',
            CURLOPT_USERAGENT => $userAgent,
            CURLOPT_HTTPHEADER => [
                'Accept: application/json, text/plain, */*',
                'Accept-Language: en-US,en;q=0.9',
                'Cache-Control: no-cache',
                'Pragma: no-cache'
            ]
        ]);
        
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_error = curl_error($ch);
        curl_close($ch);
        
        // Debug logging
        @error_log("Nexxon Backend [cURL]: HTTP=$http_code, Error=$curl_error");
        
        if ($http_code === 200 && !empty($response)) {
            $data = json_decode($response, true);
            if (is_array($data)) {
                return $data;
            }
        }
    }
    
    // ============================================
    // METHOD 2: file_get_contents (Fallback)
    // ============================================
    $context = stream_context_create([
        'http' => [
            'method' => 'GET',
            'timeout' => 30,
            'ignore_errors' => true,
            'user_agent' => $userAgent,
            'header' => "Accept: application/json\r\n" .
                       "Accept-Language: en-US,en;q=0.9\r\n"
        ],
        'ssl' => [
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        ]
    ]);
    
    $response = @file_get_contents($url, false, $context);
    
    if ($response !== false && !empty($response)) {
        $data = json_decode($response, true);
        if (is_array($data)) {
            @error_log("Nexxon Backend [fgc]: Success");
            return $data;
        }
    }
    
    // ============================================
    // METHOD 3: fsockopen (Last resort)
    // ============================================
    $parsed = parse_url($url);
    $host = $parsed['host'] ?? '';
    $path = ($parsed['path'] ?? '/') . '?' . ($parsed['query'] ?? '');
    
    if ($host) {
        $fp = @fsockopen('ssl://' . $host, 443, $errno, $errstr, 15);
        if ($fp) {
            $request = "GET $path HTTP/1.1\r\n";
            $request .= "Host: $host\r\n";
            $request .= "User-Agent: $userAgent\r\n";
            $request .= "Accept: application/json\r\n";
            $request .= "Connection: close\r\n\r\n";
            
            fwrite($fp, $request);
            
            $response = '';
            while (!feof($fp)) {
                $response .= fgets($fp, 4096);
            }
            fclose($fp);
            
            // Split headers and body
            $parts = explode("\r\n\r\n", $response, 2);
            if (count($parts) === 2) {
                $body = $parts[1];
                $data = json_decode($body, true);
                if (is_array($data)) {
                    @error_log("Nexxon Backend [fsockopen]: Success");
                    return $data;
                }
            }
        }
    }
    
    @error_log("Nexxon Backend: ALL METHODS FAILED for $url");
    return null;
}

function clean_address($address) {
    if (empty($address)) return 'N/A';
    $address = str_replace('!', ' ', $address);
    $address = preg_replace('/\s+/', ' ', $address);
    return trim($address);
}
