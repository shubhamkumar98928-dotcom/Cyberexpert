<?php
session_start();
require_once 'functions.php';

// Handle login
if (($_GET['action'] ?? '') === 'login') {
    $error = null;
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (($_POST['password'] ?? '') === ADMIN_PASSWORD) {
            $_SESSION['admin_logged_in'] = true;
            header('Location: admin.php');
            exit;
        }
        $error = 'Invalid password';
    }
    renderLoginPage($error);
    exit;
}

// Handle logout
if (($_GET['action'] ?? '') === 'logout') {
    session_destroy();
    header('Location: admin.php?action=login');
    exit;
}

// Require login
require_admin();

// Handle AJAX API calls
$action = $_GET['action'] ?? '';

if ($action === 'api_keys') {
    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'keys' => load_keys()]);
    exit;
}

if ($action === 'api_generate') {
    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'key' => generate_key()]);
    exit;
}

if ($action === 'api_create' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    $input = json_decode(file_get_contents('php://input'), true);
    
    $name = trim($input['name'] ?? '');
    $key_value = trim($input['key'] ?? '');
    $daily_limit = intval($input['daily_limit'] ?? 0);
    $expires_at = trim($input['expires_at'] ?? '');
    
    if (!$name || !$key_value) {
        echo json_encode(['success' => false, 'error' => 'Name and Key required']);
        exit;
    }
    
    $keys = load_keys();
    
    if (find_key($keys, $key_value)) {
        echo json_encode(['success' => false, 'error' => 'Key already exists']);
        exit;
    }
    
    $new_key = [
        'id' => bin2hex(random_bytes(8)),
        'name' => $name,
        'key' => $key_value,
        'daily_limit' => $daily_limit,
        'status' => 'active',
        'expires_at' => $expires_at ?: null,
        'today_usage' => 0,
        'total_usage' => 0,
        'usage_date' => date('Y-m-d'),
        'created_at' => date('Y-m-d H:i:s') . ' UTC',
        'last_used' => null
    ];
   $keys[] = $new_key;

if (!save_keys($keys)) {
    echo json_encode([
        'success' => false,
        'error' => 'Key save nahi ho rahi. data/keys.json writable nahi hai.'
    ]);
    exit;
}

echo json_encode([
    'success' => true,
    'key' => $new_key
]);
exit;
}
    

if ($action === 'api_update' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    $input = json_decode(file_get_contents('php://input'), true);
    
    $id = $input['id'] ?? '';
    $keys = load_keys();
    
    foreach ($keys as &$k) {
        if ($k['id'] === $id) {
            $k['name'] = trim($input['name'] ?? $k['name']);
            $k['daily_limit'] = intval($input['daily_limit'] ?? $k['daily_limit']);
            $k['status'] = $input['status'] ?? $k['status'];
            $k['expires_at'] = !empty($input['expires_at']) ? $input['expires_at'] : null;
            save_keys($keys);
            echo json_encode(['success' => true, 'key' => $k]);
            exit;
        }
    }
    
    echo json_encode(['success' => false, 'error' => 'Key not found']);
    exit;
}

if ($action === 'api_delete' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    $input = json_decode(file_get_contents('php://input'), true);
    $id = $input['id'] ?? '';
    
    $keys = load_keys();
    $keys = array_values(array_filter($keys, fn($k) => $k['id'] !== $id));
    save_keys($keys);
    
    echo json_encode(['success' => true]);
    exit;
}

// Render page
$page = $_GET['page'] ?? 'home';
renderAdminPage($page);

// ============================================================
// TEMPLATE FUNCTIONS
// ============================================================

function renderLoginPage($error) {
    ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="theme-color" content="#0a0e1a">
    <title>Admin · Shubham</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; -webkit-tap-highlight-color: transparent; }
        html, body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: #05070f;
            min-height: 100vh;
            color: #e2e8f0;
            overflow-x: hidden;
            -webkit-font-smoothing: antialiased;
        }
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background: 
                radial-gradient(ellipse at 20% 15%, rgba(99,102,241,0.22) 0%, transparent 50%),
                radial-gradient(ellipse at 80% 85%, rgba(168,85,247,0.22) 0%, transparent 50%);
            pointer-events: none;
            z-index: 0;
        }
        .screen {
            position: relative;
            z-index: 1;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            padding: 24px 20px;
        }
        .status-bar-space { height: 20px; }
        
        .login-header {
            padding: 40px 0 32px;
            text-align: center;
        }
        .app-icon-wrap {
            width: 88px;
            height: 88px;
            margin: 0 auto 24px;
            background: linear-gradient(135deg, #6366f1, #a855f7);
            border-radius: 26px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 20px 50px rgba(99,102,241,0.4);
            position: relative;
        }
        .app-icon-wrap::before {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: 26px;
            background: inherit;
            filter: blur(24px);
            opacity: 0.55;
            z-index: -1;
            transform: scale(1.08);
        }
        .app-icon-wrap svg {
            width: 42px;
            height: 42px;
            fill: #fff;
        }
        .login-title {
            font-size: 24px;
            font-weight: 800;
            color: #fff;
            letter-spacing: -0.5px;
            margin-bottom: 6px;
        }
        .login-sub {
            font-size: 13px;
            color: #94a3b8;
            font-weight: 500;
        }

        .error-banner {
            background: rgba(239,68,68,0.12);
            border: 1px solid rgba(239,68,68,0.3);
            color: #fca5a5;
            padding: 14px 18px;
            border-radius: 14px;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            animation: shake 0.4s;
        }
        .error-banner svg { width: 18px; height: 18px; fill: #fca5a5; flex-shrink: 0; }
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-6px); }
            75% { transform: translateX(6px); }
        }

        .form-field {
            margin-bottom: 18px;
        }
        .field-label {
            display: block;
            font-size: 12px;
            font-weight: 700;
            color: #cbd5e1;
            letter-spacing: 0.4px;
            text-transform: uppercase;
            margin-bottom: 10px;
            padding-left: 4px;
        }
        .input-wrap {
            position: relative;
        }
        .input-wrap svg.icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            width: 20px;
            height: 20px;
            fill: #64748b;
            pointer-events: none;
        }
        .input-wrap input {
            width: 100%;
            padding: 17px 18px 17px 52px;
            background: rgba(15,20,35,0.9);
            border: 1.5px solid #1e2d48;
            border-radius: 16px;
            color: #e2e8f0;
            font-size: 15px;
            outline: none;
            font-family: inherit;
            transition: all 0.25s;
            letter-spacing: 0.3px;
        }
        .input-wrap input:focus {
            border-color: #6366f1;
            background: rgba(15,20,35,1);
            box-shadow: 0 0 0 4px rgba(99,102,241,0.15);
        }
        .input-wrap input::placeholder { color: #475569; }

        .login-btn {
            width: 100%;
            padding: 17px;
            background: linear-gradient(135deg, #6366f1, #a855f7);
            color: #fff;
            border: none;
            border-radius: 16px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            font-family: inherit;
            letter-spacing: 0.3px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            transition: all 0.25s;
            box-shadow: 0 12px 30px rgba(99,102,241,0.4);
            margin-top: 10px;
        }
        .login-btn svg { width: 18px; height: 18px; fill: #fff; }
        .login-btn:active { transform: scale(0.98); }

        .login-footer {
            margin-top: auto;
            padding-top: 40px;
            text-align: center;
            color: #475569;
            font-size: 11px;
            font-weight: 500;
            letter-spacing: 0.3px;
        }
    </style>
</head>
<body>
    <div class="screen">
        <div class="status-bar-space"></div>
        
        <div class="login-header">
            <div class="app-icon-wrap">
                <svg viewBox="0 0 24 24"><path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm0 10.99h7c-.53 4.12-3.28 7.79-7 8.94V12H5V6.3l7-3.11v8.8z"/></svg>
            </div>
            <h1 class="login-title">Admin Access</h1>
            <p class="login-sub">Shubham Hacker · Secure Gateway</p>
        </div>

        <?php if ($error): ?>
        <div class="error-banner">
            <svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
            <?php echo htmlspecialchars($error); ?>
        </div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-field">
                <label class="field-label">Password</label>
                <div class="input-wrap">
                    <svg class="icon" viewBox="0 0 24 24"><path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1 1.71 0 3.1 1.39 3.1 3.1v2z"/></svg>
                    <input type="password" name="password" placeholder="Enter your password" required autofocus>
                </div>
            </div>
            <button type="submit" class="login-btn">
                <svg viewBox="0 0 24 24"><path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4z"/></svg>
                Unlock Panel
            </button>
        </form>

        <div class="login-footer">
            ⚡ <?php echo COPYRIGHT; ?>
        </div>
    </div>
</body>
</html>
    <?php
}

function renderAdminPage($page) {
    ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="theme-color" content="#05070f">
    <title>Shubham Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; -webkit-tap-highlight-color: transparent; }
        html, body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: #05070f;
            color: #e2e8f0;
            min-height: 100vh;
            overflow-x: hidden;
            -webkit-font-smoothing: antialiased;
        }
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background: 
                radial-gradient(ellipse at 15% 10%, rgba(99,102,241,0.15) 0%, transparent 45%),
                radial-gradient(ellipse at 85% 90%, rgba(168,85,247,0.15) 0%, transparent 45%);
            pointer-events: none;
            z-index: 0;
        }
        .app {
            position: relative;
            z-index: 1;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            padding-bottom: 88px;
        }

        /* ===== TOP APP BAR ===== */
        .top-bar {
            position: sticky;
            top: 0;
            z-index: 50;
            background: rgba(5,7,15,0.85);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border-bottom: 1px solid rgba(99,102,241,0.12);
            padding: 14px 20px;
        }
        .top-bar-inner {
            max-width: 900px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
        }
        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            min-width: 0;
        }
        .brand-icon {
            width: 42px;
            height: 42px;
            background: linear-gradient(135deg, #6366f1, #a855f7);
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 8px 24px rgba(99,102,241,0.4);
            flex-shrink: 0;
            position: relative;
        }
        .brand-icon::before {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: 14px;
            background: inherit;
            filter: blur(16px);
            opacity: 0.5;
            z-index: -1;
        }
        .brand-icon svg { width: 22px; height: 22px; fill: #fff; }
        .brand-text h1 {
            font-size: 15px;
            font-weight: 800;
            color: #fff;
            letter-spacing: -0.3px;
            line-height: 1.1;
        }
        .brand-text p {
            font-size: 10px;
            color: #64748b;
            font-weight: 600;
            letter-spacing: 1.2px;
            text-transform: uppercase;
            margin-top: 2px;
        }
        .top-actions {
            display: flex;
            gap: 8px;
            flex-shrink: 0;
        }
        .icon-btn {
            width: 42px;
            height: 42px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(30,41,59,0.7);
            border: 1px solid rgba(99,102,241,0.15);
            border-radius: 14px;
            cursor: pointer;
            transition: all 0.25s;
            color: #cbd5e1;
            text-decoration: none;
            font-family: inherit;
            -webkit-tap-highlight-color: transparent;
        }
        .icon-btn svg { width: 20px; height: 20px; fill: currentColor; }
        .icon-btn:active { transform: scale(0.94); }
        .icon-btn.danger {
            color: #fca5a5;
            border-color: rgba(239,68,68,0.25);
            background: rgba(239,68,68,0.1);
        }

        /* ===== MAIN CONTENT ===== */
        .main {
            flex: 1;
            max-width: 900px;
            margin: 0 auto;
            width: 100%;
            padding: 24px 20px;
        }

        /* ===== HERO CARD ===== */
        .hero-card {
            background: linear-gradient(135deg, rgba(99,102,241,0.15), rgba(168,85,247,0.1));
            border: 1px solid rgba(99,102,241,0.25);
            border-radius: 24px;
            padding: 28px 24px;
            margin-bottom: 24px;
            position: relative;
            overflow: hidden;
            animation: fadeUp 0.5s ease-out;
        }
        .hero-card::before {
            content: '';
            position: absolute;
            top: -50%; right: -30%;
            width: 240px; height: 240px;
            background: radial-gradient(circle, rgba(99,102,241,0.3) 0%, transparent 70%);
            border-radius: 50%;
            pointer-events: none;
        }
        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: linear-gradient(135deg, #6366f1, #a855f7);
            color: #fff;
            font-size: 10px;
            font-weight: 800;
            letter-spacing: 1.2px;
            text-transform: uppercase;
            padding: 5px 12px;
            border-radius: 20px;
            margin-bottom: 14px;
            box-shadow: 0 6px 20px rgba(99,102,241,0.4);
        }
        .hero-badge svg { width: 11px; height: 11px; fill: #fff; }
        .hero-title {
            font-size: 22px;
            font-weight: 800;
            color: #fff;
            letter-spacing: -0.5px;
            margin-bottom: 6px;
            position: relative;
            z-index: 1;
        }
        .hero-sub {
            font-size: 13px;
            color: #94a3b8;
            font-weight: 500;
            position: relative;
            z-index: 1;
        }

        /* ===== PAGE HEADER ===== */
        .page-header {
            margin-bottom: 24px;
            animation: fadeUp 0.5s ease-out;
        }
        .page-header h2 {
            font-size: 24px;
            font-weight: 800;
            color: #fff;
            letter-spacing: -0.6px;
            margin-bottom: 6px;
        }
        .page-header p {
            font-size: 13px;
            color: #94a3b8;
            font-weight: 500;
        }
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(15px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* ===== FORM CARD ===== */
        .card {
            background: rgba(15,20,35,0.75);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(99,102,241,0.15);
            border-radius: 22px;
            padding: 24px 20px;
            margin-bottom: 18px;
            animation: fadeUp 0.55s ease-out;
        }

        .field {
            margin-bottom: 20px;
        }
        .field-label {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 12px;
            font-weight: 700;
            color: #cbd5e1;
            letter-spacing: 0.3px;
            margin-bottom: 10px;
            padding-left: 2px;
        }
        .field-label svg { width: 15px; height: 15px; fill: #a78bfa; flex-shrink: 0; }
        .field input, .field select {
            width: 100%;
            padding: 15px 18px;
            background: rgba(5,10,20,0.7);
            border: 1.5px solid #1e2d48;
            border-radius: 14px;
            color: #e2e8f0;
            font-size: 14px;
            font-family: inherit;
            outline: none;
            transition: all 0.25s;
            -webkit-appearance: none;
            appearance: none;
        }
        .field input:focus, .field select:focus {
            border-color: #6366f1;
            background: rgba(5,10,20,0.9);
            box-shadow: 0 0 0 4px rgba(99,102,241,0.12);
        }
        .field input::placeholder { color: #475569; }
        .field-hint {
            font-size: 11px;
            color: #64748b;
            margin-top: 7px;
            font-weight: 500;
            padding-left: 2px;
        }

        .input-with-btn {
            display: flex;
            gap: 10px;
        }
        .input-with-btn input {
            flex: 1;
            font-family: 'JetBrains Mono', monospace;
            font-size: 12px;
            letter-spacing: 0.5px;
        }
        .gen-btn {
            padding: 0 18px;
            background: linear-gradient(135deg, #6366f1, #a855f7);
            border: none;
            border-radius: 14px;
            color: #fff;
            font-weight: 700;
            font-size: 12px;
            cursor: pointer;
            font-family: inherit;
            display: flex;
            align-items: center;
            gap: 6px;
            transition: all 0.25s;
            box-shadow: 0 6px 20px rgba(99,102,241,0.35);
            white-space: nowrap;
        }
        .gen-btn svg { width: 15px; height: 15px; fill: #fff; }
        .gen-btn:active { transform: scale(0.96); }

        /* ===== EXPIRY PRESETS ===== */
        .preset-row {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 8px;
            margin-bottom: 14px;
        }
        .preset-btn {
            padding: 11px 8px;
            background: rgba(30,41,59,0.5);
            border: 1.5px solid #1e2d48;
            border-radius: 12px;
            color: #94a3b8;
            font-size: 12px;
            font-weight: 700;
            cursor: pointer;
            font-family: inherit;
            transition: all 0.25s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
            -webkit-tap-highlight-color: transparent;
        }
        .preset-btn svg { width: 13px; height: 13px; fill: currentColor; }
        .preset-btn:active { transform: scale(0.96); }
        .preset-btn.active {
            background: linear-gradient(135deg, #6366f1, #a855f7);
            color: #fff;
            border-color: transparent;
            box-shadow: 0 6px 18px rgba(99,102,241,0.4);
        }

        .timer-status {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 14px 16px;
            background: rgba(99,102,241,0.08);
            border: 1px solid rgba(99,102,241,0.2);
            border-radius: 14px;
            margin-top: 4px;
        }
        .timer-status svg { width: 18px; height: 18px; fill: #a78bfa; flex-shrink: 0; }
        .timer-status-text {
            font-family: 'JetBrains Mono', monospace;
            font-size: 12px;
            font-weight: 600;
            color: #c4b5fd;
            letter-spacing: 0.3px;
        }
        .timer-status.unlimited { background: rgba(245,158,11,0.08); border-color: rgba(245,158,11,0.25); }
        .timer-status.unlimited svg { fill: #fbbf24; }
        .timer-status.unlimited .timer-status-text { color: #fbbf24; }
        .timer-status.expired { background: rgba(239,68,68,0.08); border-color: rgba(239,68,68,0.25); }
        .timer-status.expired svg { fill: #f87171; }
        .timer-status.expired .timer-status-text { color: #f87171; }

        /* ===== PRIMARY ACTION ===== */
        .btn-primary {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, #6366f1, #a855f7);
            border: none;
            border-radius: 16px;
            color: #fff;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            font-family: inherit;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            transition: all 0.25s;
            box-shadow: 0 10px 30px rgba(99,102,241,0.4);
            letter-spacing: 0.3px;
        }
        .btn-primary svg { width: 18px; height: 18px; fill: #fff; }
        .btn-primary:active { transform: scale(0.98); }

        /* ===== SEARCH BAR ===== */
        .search-wrap {
            position: relative;
            margin-bottom: 20px;
            animation: fadeUp 0.5s ease-out;
        }
        .search-wrap svg {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            width: 20px;
            height: 20px;
            fill: #64748b;
            pointer-events: none;
        }
        .search-wrap input {
            width: 100%;
            padding: 15px 18px 15px 50px;
            background: rgba(15,20,35,0.75);
            border: 1.5px solid rgba(99,102,241,0.15);
            border-radius: 16px;
            color: #e2e8f0;
            font-size: 14px;
            font-family: inherit;
            outline: none;
            transition: all 0.25s;
        }
        .search-wrap input:focus {
            border-color: #6366f1;
            background: rgba(15,20,35,0.95);
            box-shadow: 0 0 0 4px rgba(99,102,241,0.12);
        }
        .search-wrap input::placeholder { color: #475569; }

        /* ===== KEY CARDS ===== */
        .keys-list {
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .key-card {
            background: rgba(15,20,35,0.75);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1.5px solid rgba(99,102,241,0.15);
            border-radius: 20px;
            padding: 20px;
            animation: fadeUp 0.45s ease-out;
            transition: all 0.3s;
            position: relative;
            overflow: hidden;
        }
        .key-card::before {
            content: '';
            position: absolute;
            left: 0; top: 0;
            width: 3px; height: 100%;
            background: linear-gradient(180deg, #6366f1, #a855f7);
            opacity: 0;
            transition: opacity 0.3s;
        }
        .key-card:active::before { opacity: 1; }
        .key-card.expired {
            border-color: rgba(239,68,68,0.25);
            background: rgba(30,15,20,0.6);
        }
        .key-card.expired::before { background: linear-gradient(180deg, #ef4444, #dc2626); }

        .key-head {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 12px;
            margin-bottom: 14px;
        }
        .key-name-wrap {
            display: flex;
            align-items: center;
            gap: 10px;
            min-width: 0;
            flex: 1;
        }
        .key-avatar {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            background: linear-gradient(135deg, #6366f1, #a855f7);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: 15px;
            font-weight: 800;
            color: #fff;
            box-shadow: 0 6px 20px rgba(99,102,241,0.35);
        }
        .key-card.expired .key-avatar {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            box-shadow: 0 6px 20px rgba(239,68,68,0.35);
        }
        .key-name-text {
            min-width: 0;
            flex: 1;
        }
        .key-name-text h3 {
            font-size: 15px;
            font-weight: 700;
            color: #fff;
            letter-spacing: -0.2px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .key-name-text p {
            font-size: 11px;
            color: #64748b;
            font-weight: 600;
            margin-top: 3px;
        }

        .status-chip {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 5px 11px;
            border-radius: 20px;
            font-size: 10px;
            font-weight: 800;
            letter-spacing: 0.8px;
            text-transform: uppercase;
            flex-shrink: 0;
        }
        .status-chip svg { width: 11px; height: 11px; fill: currentColor; }
        .status-chip.active {
            background: rgba(16,185,129,0.12);
            color: #10b981;
            border: 1px solid rgba(16,185,129,0.3);
        }
        .status-chip.expired {
            background: rgba(239,68,68,0.12);
            color: #ef4444;
            border: 1px solid rgba(239,68,68,0.3);
        }
        .status-chip.inactive {
            background: rgba(148,163,184,0.1);
            color: #94a3b8;
            border: 1px solid rgba(148,163,184,0.25);
        }

        .key-code {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 11px 14px;
            background: rgba(5,10,20,0.7);
            border: 1px solid #1e2d48;
            border-radius: 12px;
            margin-bottom: 14px;
        }
        .key-code-text {
            flex: 1;
            font-family: 'JetBrains Mono', monospace;
            font-size: 11px;
            color: #c4b5fd;
            letter-spacing: 0.3px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .key-copy {
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(51,65,85,0.5);
            border: 1px solid #2a3a5a;
            border-radius: 9px;
            cursor: pointer;
            flex-shrink: 0;
            transition: all 0.25s;
            -webkit-tap-highlight-color: transparent;
            color: #cbd5e1;
        }
        .key-copy svg { width: 14px; height: 14px; fill: currentColor; }
        .key-copy:active { transform: scale(0.92); background: #6366f1; border-color: #6366f1; color: #fff; }

        .stat-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 8px;
            margin-bottom: 16px;
        }
        .stat-box {
            background: rgba(5,10,20,0.6);
            border: 1px solid #1e2d48;
            border-radius: 12px;
            padding: 11px 6px;
            text-align: center;
        }
        .stat-box-label {
            font-size: 9px;
            color: #64748b;
            font-weight: 700;
            letter-spacing: 0.8px;
            text-transform: uppercase;
            margin-bottom: 5px;
        }
        .stat-box-value {
            font-family: 'JetBrains Mono', monospace;
            font-size: 14px;
            color: #fff;
            font-weight: 700;
            letter-spacing: -0.3px;
        }
        .stat-box-value.dim {
            font-size: 11px;
            color: #94a3b8;
        }
        .stat-box-value.infinity { color: #fbbf24; font-size: 18px; }
        .stat-box-value.timer { font-size: 11px; color: #c4b5fd; }
        .stat-box-value.timer.expired { color: #f87171; }

        .key-actions {
            display: flex;
            gap: 8px;
        }
        .action-btn {
            flex: 1;
            padding: 11px;
            border-radius: 12px;
            border: none;
            font-size: 12px;
            font-weight: 700;
            cursor: pointer;
            font-family: inherit;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            transition: all 0.25s;
            -webkit-tap-highlight-color: transparent;
            letter-spacing: 0.2px;
        }
        .action-btn svg { width: 15px; height: 15px; fill: currentColor; }
        .action-btn:active { transform: scale(0.97); }
        .action-btn.edit {
            background: rgba(99,102,241,0.12);
            color: #a5b4fc;
            border: 1px solid rgba(99,102,241,0.25);
        }
        .action-btn.delete {
            background: rgba(239,68,68,0.12);
            color: #fca5a5;
            border: 1px solid rgba(239,68,68,0.25);
        }

        /* ===== EMPTY STATE ===== */
        .empty {
            text-align: center;
            padding: 60px 20px;
            animation: fadeUp 0.5s ease-out;
        }
        .empty-icon-wrap {
            width: 80px;
            height: 80px;
            margin: 0 auto 20px;
            background: rgba(99,102,241,0.08);
            border: 1px solid rgba(99,102,241,0.15);
            border-radius: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .empty-icon-wrap svg { width: 36px; height: 36px; fill: #6366f1; opacity: 0.6; }
        .empty h3 {
            font-size: 16px;
            font-weight: 700;
            color: #cbd5e1;
            margin-bottom: 6px;
        }
        .empty p {
            font-size: 13px;
            color: #64748b;
            margin-bottom: 22px;
        }
        .empty-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 13px 22px;
            background: linear-gradient(135deg, #6366f1, #a855f7);
            border-radius: 14px;
            color: #fff;
            font-size: 13px;
            font-weight: 700;
            text-decoration: none;
            box-shadow: 0 10px 30px rgba(99,102,241,0.4);
        }
        .empty-btn svg { width: 16px; height: 16px; fill: #fff; }

        /* ===== BOTTOM NAV BAR ===== */
        .bottom-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: rgba(5,7,15,0.92);
            backdrop-filter: blur(28px);
            -webkit-backdrop-filter: blur(28px);
            border-top: 1px solid rgba(99,102,241,0.15);
            padding: 10px 0 max(10px, env(safe-area-inset-bottom));
            z-index: 100;
        }
        .bottom-nav-inner {
            max-width: 900px;
            margin: 0 auto;
            display: flex;
            justify-content: space-around;
            padding: 0 16px;
        }
        .nav-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 4px;
            padding: 8px 16px;
            border-radius: 14px;
            text-decoration: none;
            color: #64748b;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 0.3px;
            transition: all 0.25s;
            flex: 1;
            max-width: 140px;
            -webkit-tap-highlight-color: transparent;
            position: relative;
        }
        .nav-item svg {
            width: 22px;
            height: 22px;
            fill: currentColor;
            transition: all 0.25s;
        }
        .nav-item.active {
            color: #a5b4fc;
        }
        .nav-item.active svg { fill: #a5b4fc; }
        .nav-item.active::after {
            content: '';
            position: absolute;
            top: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 30px;
            height: 3px;
            background: linear-gradient(135deg, #6366f1, #a855f7);
            border-radius: 0 0 4px 4px;
        }
        .nav-item:active { transform: scale(0.94); }

        /* ===== TOAST ===== */
        .toast {
            position: fixed;
            bottom: 100px;
            left: 50%;
            transform: translateX(-50%) translateY(120px);
            background: rgba(20,28,48,0.98);
            backdrop-filter: blur(20px);
            border: 1.5px solid rgba(99,102,241,0.35);
            border-radius: 16px;
            padding: 14px 22px;
            color: #fff;
            font-size: 13px;
            font-weight: 700;
            box-shadow: 0 20px 50px rgba(0,0,0,0.6);
            opacity: 0;
            transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1);
            z-index: 1000;
            display: flex;
            align-items: center;
            gap: 10px;
            letter-spacing: 0.2px;
            max-width: 90vw;
        }
        .toast svg { width: 18px; height: 18px; fill: currentColor; flex-shrink: 0; }
        .toast.show {
            transform: translateX(-50%) translateY(0);
            opacity: 1;
        }
        .toast.success {
            border-color: rgba(16,185,129,0.5);
            color: #6ee7b7;
            box-shadow: 0 20px 50px rgba(16,185,129,0.2);
        }
        .toast.error {
            border-color: rgba(239,68,68,0.5);
            color: #fca5a5;
            box-shadow: 0 20px 50px rgba(239,68,68,0.2);
        }

        /* ===== MODAL ===== */
        .modal {
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.85);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            z-index: 999;
            display: flex;
            align-items: flex-end;
            justify-content: center;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s;
        }
        .modal.show {
            opacity: 1;
            pointer-events: auto;
        }
        .modal-sheet {
            background: rgba(15,20,35,0.98);
            backdrop-filter: blur(30px);
            -webkit-backdrop-filter: blur(30px);
            border-top-left-radius: 28px;
            border-top-right-radius: 28px;
            padding: 24px 22px calc(24px + env(safe-area-inset-bottom));
            width: 100%;
            max-width: 900px;
            max-height: 88vh;
            overflow-y: auto;
            transform: translateY(100%);
            transition: transform 0.4s cubic-bezier(0.16, 1, 0.3, 1);
            border-top: 1px solid rgba(99,102,241,0.25);
        }
        .modal.show .modal-sheet {
            transform: translateY(0);
        }
        .modal-grabber {
            width: 40px;
            height: 4px;
            background: #334155;
            border-radius: 4px;
            margin: 0 auto 20px;
        }
        .modal-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 22px;
        }
        .modal-title {
            font-size: 18px;
            font-weight: 800;
            color: #fff;
            display: flex;
            align-items: center;
            gap: 10px;
            letter-spacing: -0.3px;
        }
        .modal-title svg { width: 20px; height: 20px; fill: #a78bfa; }
        .modal-close {
            width: 38px;
            height: 38px;
            background: rgba(51,65,85,0.5);
            border: 1px solid #2a3a5a;
            border-radius: 12px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.25s;
            color: #94a3b8;
        }
        .modal-close svg { width: 16px; height: 16px; fill: currentColor; }
        .modal-close:active { transform: scale(0.92); background: #ef4444; border-color: #ef4444; color: #fff; }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 480px) {
            .main { padding: 18px 16px; }
            .card { padding: 20px 16px; border-radius: 20px; }
            .hero-card { padding: 22px 20px; }
            .hero-title { font-size: 19px; }
            .page-header h2 { font-size: 21px; }
            .key-card { padding: 17px; }
            .stat-box-value { font-size: 13px; }
            .stat-box-value.timer { font-size: 10px; }
            .brand-text h1 { font-size: 13px; }
            .brand-icon { width: 38px; height: 38px; }
            .icon-btn { width: 38px; height: 38px; border-radius: 12px; }
            .icon-btn svg { width: 18px; height: 18px; }
            .preset-row { grid-template-columns: repeat(3, 1fr); }
            .preset-btn { font-size: 11px; padding: 9px 6px; }
        }
    </style>
</head>
<body>
    <div class="app">
        <!-- ===== TOP APP BAR ===== -->
        <div class="top-bar">
            <div class="top-bar-inner">
                <div class="brand">
                    <div class="brand-icon">
                        <svg viewBox="0 0 24 24"><path d="M7 2v11h3v9l7-12h-4l4-8z"/></svg>
                    </div>
                    <div class="brand-text">
                        <h1>SHUBHAM</h1>
                        <p>Admin Panel</p>
                    </div>
                </div>
                <div class="top-actions">
                    <a href="index.php" class="icon-btn" title="Home">
                        <svg viewBox="0 0 24 24"><path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/></svg>
                    </a>
                    <a href="admin.php?action=logout" class="icon-btn danger" title="Logout">
                        <svg viewBox="0 0 24 24"><path d="M17 7l-1.41 1.41L18.17 11H8v2h10.17l-2.58 2.58L17 17l5-5zM4 5h8V3H4c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h8v-2H4V5z"/></svg>
                    </a>
                </div>
            </div>
        </div>

        <!-- ===== MAIN ===== -->
        <div class="main">
            <!-- Hero -->
            <div class="hero-card">
                <div class="hero-badge">
                    <svg viewBox="0 0 24 24"><path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z"/></svg>
                    VIP ACCESS
                </div>
                <h2 class="hero-title">Welcome back, Admin</h2>
                <p class="hero-sub">Full control over API keys and system settings</p>
            </div>

            <?php if ($page === 'home'): ?>
            <!-- ===== CREATE KEY ===== -->
            <div class="page-header">
                <h2>Create API Key</h2>
                <p>Generate a new key with custom limits</p>
            </div>

            <div class="card">
                <div class="field">
                    <label class="field-label">
                        <svg viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                        Key Name
                    </label>
                    <input type="text" id="keyName" placeholder="e.g., John Doe, Client A">
                    <div class="field-hint">Display name for your reference</div>
                </div>

                <div class="field">
                    <label class="field-label">
                        <svg viewBox="0 0 24 24"><path d="M12.65 10C11.83 7.67 9.61 6 7 6c-3.31 0-6 2.69-6 6s2.69 6 6 6c2.61 0 4.83-1.67 5.65-4H17v4h4v-4h2v-4H12.65zM7 14c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2z"/></svg>
                        API Key
                    </label>
                    <div class="input-with-btn">
                        <input type="text" id="keyValue" placeholder="Type or generate">
                        <button type="button" class="gen-btn" onclick="generateKey()">
                            <svg viewBox="0 0 24 24"><path d="M17.65 6.35C16.2 4.9 14.21 4 12 4c-4.42 0-7.99 3.58-7.99 8s3.57 8 7.99 8c3.73 0 6.84-2.55 7.73-6h-2.08c-.82 2.33-3.04 4-5.65 4-3.31 0-6-2.69-6-6s2.69-6 6-6c1.66 0 3.14.69 4.22 1.78L13 11h7V4l-2.35 2.35z"/></svg>
                            Generate
                        </button>
                    </div>
                    <div class="field-hint">Use this key in API requests</div>
                </div>

                <div class="field">
                    <label class="field-label">
                        <svg viewBox="0 0 24 24"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/></svg>
                        Daily Limit
                    </label>
                    <input type="number" id="dailyLimit" placeholder="0 = Unlimited" value="0" min="0">
                    <div class="field-hint">0 = Unlimited requests per day</div>
                </div>

                <div class="field" style="margin-bottom: 14px;">
                    <label class="field-label">
                        <svg viewBox="0 0 24 24"><path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67z"/></svg>
                        Expiry
                    </label>
                    <div class="preset-row">
                        <button type="button" class="preset-btn" onclick="setExpiry(1, this)">
                            <svg viewBox="0 0 24 24"><path d="M19 4h-1V2h-2v2H8V2H6v2H5c-1.11 0-1.99.9-1.99 2L3 20c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V10h14v10z"/></svg>
                            1 Day
                        </button>
                        <button type="button" class="preset-btn" onclick="setExpiry(7, this)">
                            <svg viewBox="0 0 24 24"><path d="M19 4h-1V2h-2v2H8V2H6v2H5c-1.11 0-1.99.9-1.99 2L3 20c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V10h14v10z"/></svg>
                            7 Days
                        </button>
                        <button type="button" class="preset-btn" onclick="setExpiry(30, this)">
                            <svg viewBox="0 0 24 24"><path d="M19 4h-1V2h-2v2H8V2H6v2H5c-1.11 0-1.99.9-1.99 2L3 20c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V10h14v10z"/></svg>
                            30 Days
                        </button>
                        <button type="button" class="preset-btn" onclick="setExpiry(90, this)">
                            <svg viewBox="0 0 24 24"><path d="M19 4h-1V2h-2v2H8V2H6v2H5c-1.11 0-1.99.9-1.99 2L3 20c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V10h14v10z"/></svg>
                            90 Days
                        </button>
                        <button type="button" class="preset-btn" onclick="setExpiry(365, this)">
                            <svg viewBox="0 0 24 24"><path d="M19 4h-1V2h-2v2H8V2H6v2H5c-1.11 0-1.99.9-1.99 2L3 20c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V10h14v10z"/></svg>
                            1 Year
                        </button>
                        <button type="button" class="preset-btn active" onclick="setExpiry(0, this)">
                            <svg viewBox="0 0 24 24"><path d="M14.4 6L14 4H5v17h2v-7h5.6l.4 2h7V6z"/></svg>
                            Never
                        </button>
                    </div>
                    <input type="date" id="expiresAt">
                    <div class="timer-status unlimited" id="timerStatus">
                        <svg viewBox="0 0 24 24"><path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67z"/></svg>
                        <span class="timer-status-text" id="timerText">No expiry — key will never expire</span>
                    </div>
                </div>

                <button class="btn-primary" onclick="createKey()">
                    <svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm5 11h-4v4h-2v-4H7v-2h4V7h2v4h4v2z"/></svg>
                    Create API Key
                </button>
            </div>
            <?php endif; ?>

            <?php if ($page === 'history'): ?>
            <!-- ===== MANAGE KEYS ===== -->
            <div class="page-header">
                <h2>Manage Keys</h2>
                <p>View, edit, and manage all API keys</p>
            </div>

            <div class="search-wrap">
                <svg viewBox="0 0 24 24"><path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/></svg>
                <input type="text" id="searchInput" placeholder="Search by name or key..." oninput="filterKeys()">
            </div>

            <div class="keys-list" id="keysList">
                <div class="empty">
                    <div class="empty-icon-wrap">
                        <svg viewBox="0 0 24 24"><path d="M12 4V1L8 5l4 4V6c3.31 0 6 2.69 6 6 0 1.01-.25 1.97-.7 2.8l1.46 1.46C19.54 15.03 20 13.57 20 12c0-4.42-3.58-8-8-8zm0 14c-3.31 0-6-2.69-6-6 0-1.01.25-1.97.7-2.8L5.24 7.74C4.46 8.97 4 10.43 4 12c0 4.42 3.58 8 8 8v3l4-4-4-4v3z"/></svg>
                    </div>
                    <p>Loading keys...</p>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- ===== BOTTOM NAV ===== -->
        <nav class="bottom-nav">
            <div class="bottom-nav-inner">
                <a href="admin.php" class="nav-item <?php echo $page === 'home' ? 'active' : ''; ?>">
                    <svg viewBox="0 0 24 24"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg>
                    <span>Create</span>
                </a>
                <a href="admin.php?page=history" class="nav-item <?php echo $page === 'history' ? 'active' : ''; ?>">
                    <svg viewBox="0 0 24 24"><path d="M13 3c-4.97 0-9 4.03-9 9H1l3.89 3.89.07.14L9 12H6c0-3.87 3.13-7 7-7s7 3.13 7 7-3.13 7-7 7c-1.93 0-3.68-.79-4.94-2.06l-1.42 1.42C8.27 19.99 10.51 21 13 21c4.97 0 9-4.03 9-9s-4.03-9-9-9zm-1 5v5l4.28 2.54.72-1.21-3.5-2.08V8H12z"/></svg>
                    <span>Keys</span>
                </a>
            </div>
        </nav>
    </div>

    <!-- ===== EDIT MODAL ===== -->
    <div class="modal" id="editModal">
        <div class="modal-sheet">
            <div class="modal-grabber"></div>
            <div class="modal-head">
                <h3 class="modal-title">
                    <svg viewBox="0 0 24 24"><path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/></svg>
                    Edit API Key
                </h3>
                <button class="modal-close" onclick="closeEditModal()">
                    <svg viewBox="0 0 24 24"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>
                </button>
            </div>
            <input type="hidden" id="editId">
            <div class="field">
                <label class="field-label">
                    <svg viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                    Key Name
                </label>
                <input type="text" id="editName">
            </div>
            <div class="field">
                <label class="field-label">
                    <svg viewBox="0 0 24 24"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/></svg>
                    Daily Limit
                </label>
                <input type="number" id="editLimit" min="0">
                <div class="field-hint">0 = Unlimited</div>
            </div>
            <div class="field">
                <label class="field-label">
                    <svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
                    Status
                </label>
                <select id="editStatus">
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
            <div class="field">
                <label class="field-label">
                    <svg viewBox="0 0 24 24"><path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67z"/></svg>
                    Expiry Date
                </label>
                <input type="date" id="editExpiry">
            </div>
            <button class="btn-primary" onclick="saveEdit()">
                <svg viewBox="0 0 24 24"><path d="M17 3H5c-1.11 0-2 .9-2 2v14c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V7l-4-4zm-5 16c-1.66 0-3-1.34-3-3s1.34-3 3-3 3 1.34 3 3-1.34 3-3 3zm3-10H5V5h10v4z"/></svg>
                Save Changes
            </button>
        </div>
    </div>

    <!-- ===== TOAST ===== -->
    <div class="toast" id="toast"></div>

    <script>
        const PAGE = '<?php echo $page; ?>';

        /* ===== ICONS ===== */
        const ICONS = {
            success: '<svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>',
            error: '<svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>',
            active: '<svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>',
            expired: '<svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>',
            inactive: '<svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8z"/></svg>',
            infinity: '<svg viewBox="0 0 24 24"><path d="M18.6 6.62c-1.44 0-2.8.56-3.77 1.53L12 10.66 10.48 12h.01L7.8 14.39c-.64.64-1.49.99-2.4.99-1.87 0-3.39-1.51-3.39-3.38S3.53 8.62 5.4 8.62c.91 0 1.76.35 2.44 1.03l1.13 1 1.51-1.34L9.22 8.2C8.2 7.18 6.84 6.62 5.4 6.62 2.42 6.62 0 9.04 0 12s2.42 5.38 5.4 5.38c1.44 0 2.8-.56 3.77-1.53l2.83-2.5.01.01L14.2 12l2.72-2.38c.64-.64 1.49-.99 2.4-.99 1.87 0 3.39 1.51 3.39 3.38s-1.52 3.38-3.39 3.38c-.9 0-1.76-.35-2.44-1.03l-1.14-1.01-1.51 1.34 1.27 1.12c1.02 1.01 2.37 1.57 3.82 1.57 2.98 0 5.4-2.42 5.4-5.38s-2.42-5.38-5.4-5.38z"/></svg>',
            clock: '<svg viewBox="0 0 24 24"><path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67z"/></svg>'
        };

        /* ===== TOAST ===== */
        function toast(msg, type = 'success') {
            const t = document.getElementById('toast');
            t.innerHTML = (ICONS[type] || ICONS.success) + '<span>' + msg + '</span>';
            t.className = 'toast show ' + type;
            setTimeout(() => t.className = 'toast', 2800);
        }

        /* ===== GENERATE KEY ===== */
        async function generateKey() {
            const res = await fetch('admin.php?action=api_generate');
            const data = await res.json();
            if (data.success) {
                document.getElementById('keyValue').value = data.key;
                toast('Key generated successfully');
            }
        }

        /* ===== EXPIRY TIMER ===== */
        function setExpiry(days, btn) {
            // Reset all preset buttons
            document.querySelectorAll('.preset-btn').forEach(b => b.classList.remove('active'));
            if (btn) btn.classList.add('active');

            const input = document.getElementById('expiresAt');
            if (days === 0) {
                input.value = '';
            } else {
                const d = new Date();
                d.setDate(d.getDate() + days);
                input.value = d.toISOString().split('T')[0];
            }
            updateTimerStatus();
        }

        function updateTimerStatus() {
            const val = document.getElementById('expiresAt').value;
            const status = document.getElementById('timerStatus');
            const text = document.getElementById('timerText');
            const icon = status.querySelector('svg');

            status.className = 'timer-status';
            status.querySelector('svg').innerHTML = '';

            if (!val) {
                status.className = 'timer-status unlimited';
                status.querySelector('svg').innerHTML = ICONS.infinity.replace(/<\/?svg[^>]*>/g, '').replace('<path', '<path').replace('/></svg>', '/>');
                status.querySelector('svg').innerHTML = '<path d="M18.6 6.62c-1.44 0-2.8.56-3.77 1.53L12 10.66 10.48 12h.01L7.8 14.39c-.64.64-1.49.99-2.4.99-1.87 0-3.39-1.51-3.39-3.38S3.53 8.62 5.4 8.62c.91 0 1.76.35 2.44 1.03l1.13 1 1.51-1.34L9.22 8.2C8.2 7.18 6.84 6.62 5.4 6.62 2.42 6.62 0 9.04 0 12s2.42 5.38 5.4 5.38c1.44 0 2.8-.56 3.77-1.53l2.83-2.5.01.01L14.2 12l2.72-2.38c.64-.64 1.49-.99 2.4-.99 1.87 0 3.39 1.51 3.39 3.38s-1.52 3.38-3.39 3.38c-.9 0-1.76-.35-2.44-1.03l-1.14-1.01-1.51 1.34 1.27 1.12c1.02 1.01 2.37 1.57 3.82 1.57 2.98 0 5.4-2.42 5.4-5.38s-2.42-5.38-5.4-5.38z"/>';
                text.textContent = 'No expiry — key will never expire';
                return;
            }

            const diff = new Date(val) - new Date();
            if (diff <= 0) {
                status.className = 'timer-status expired';
                status.querySelector('svg').innerHTML = '<path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>';
                text.textContent = 'Expired — this key is no longer valid';
                return;
            }

            const days = Math.floor(diff / (1000*60*60*24));
            const hours = Math.floor((diff % (1000*60*60*24)) / (1000*60*60));
            status.querySelector('svg').innerHTML = '<path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67z"/>';
            text.textContent = days + 'd ' + hours + 'h remaining';
        }

        if (document.getElementById('expiresAt')) {
            document.getElementById('expiresAt').addEventListener('change', updateTimerStatus);
        }

        /* ===== CREATE KEY ===== */
        async function createKey() {
            const name = document.getElementById('keyName').value.trim();
            const key = document.getElementById('keyValue').value.trim();
            const limit = document.getElementById('dailyLimit').value;
            const expiry = document.getElementById('expiresAt').value;

            if (!name || !key) { toast('Name and Key are required', 'error'); return; }

            const res = await fetch('admin.php?action=api_create', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({name, key, daily_limit: limit, expires_at: expiry})
            });

            const data = await res.json();
            if (data.success) {
                toast('API Key created successfully');
                setTimeout(() => window.location.href = 'admin.php?page=history', 900);
            } else {
                toast(data.error || 'Failed', 'error');
            }
        }

        /* ===== LOAD KEYS ===== */
        async function loadKeys() {
            if (PAGE !== 'history') return;
            const res = await fetch('admin.php?action=api_keys');
            const data = await res.json();
            if (!data.success) return;

            const keys = data.keys || [];
            const list = document.getElementById('keysList');

            if (keys.length === 0) {
                list.innerHTML = `
                    <div class="empty">
                        <div class="empty-icon-wrap">
                            <svg viewBox="0 0 24 24"><path d="M20 6h-2.18c.11-.31.18-.65.18-1a2.996 2.996 0 0 0-5.5-1.65l-.5.67-.5-.68C10.96 2.54 10.05 2 9 2 7.34 2 6 3.34 6 5c0 .35.07.69.18 1H4c-1.11 0-2 .89-2 2v11c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V8c0-1.11-.89-2-2-2zm-5-2c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zM9 4c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zm11 15H4v-2h16v2zm0-5H4V8h5.08L7 10.83 8.62 12 11 8.76l1-1.36 1 1.36L15.38 12 17 10.83 14.92 8H20v6z"/></svg>
                        </div>
                        <h3>No API keys yet</h3>
                        <p>Create your first API key to get started</p>
                        <a href="admin.php" class="empty-btn">
                            <svg viewBox="0 0 24 24"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg>
                            Create Key
                        </a>
                    </div>`;
                return;
            }

            list.innerHTML = keys.map(k => {
                const isExpired = k.expires_at && new Date(k.expires_at) < new Date();
                const status = isExpired ? 'expired' : (k.status !== 'active' ? 'inactive' : 'active');
                const limit = k.daily_limit || 0;
                const today = k.today_usage || 0;
                const total = k.total_usage || 0;
                const initial = (k.name || '?').charAt(0).toUpperCase();

                let limitDisplay = limit === 0 
                    ? '<div class="stat-box-value infinity">♾</div>' 
                    : `<div class="stat-box-value">${today}/${limit}</div>`;

                let timerHtml = '';
                if (!k.expires_at) {
                    timerHtml = '<div class="stat-box-value infinity">♾</div>';
                } else if (isExpired) {
                    timerHtml = '<div class="stat-box-value timer expired">Expired</div>';
                } else {
                    const diff = new Date(k.expires_at) - new Date();
                    const days = Math.floor(diff / (1000*60*60*24));
                    const hours = Math.floor((diff % (1000*60*60*24)) / (1000*60*60));
                    timerHtml = `<div class="stat-box-value timer">${days}d ${hours}h</div>`;
                }

                const statusIcon = ICONS[status] || ICONS.active;

                return `
                    <div class="key-card ${status}" data-name="${escapeHtml(k.name.toLowerCase())}" data-key="${escapeHtml(k.key.toLowerCase())}">
                        <div class="key-head">
                            <div class="key-name-wrap">
                                <div class="key-avatar">${initial}</div>
                                <div class="key-name-text">
                                    <h3>${escapeHtml(k.name)}</h3>
                                    <p>${new Date(k.created_at).toLocaleDateString('en-US', {month:'short', day:'numeric', year:'numeric'})}</p>
                                </div>
                            </div>
                            <span class="status-chip ${status}">
                                ${statusIcon}
                                ${status}
                            </span>
                        </div>
                        <div class="key-code">
                            <div class="key-code-text">${escapeHtml(k.key)}</div>
                            <button class="key-copy" onclick="copyKey('${escapeHtml(k.key)}')" title="Copy">
                                <svg viewBox="0 0 24 24"><path d="M16 1H4c-1.1 0-2 .9-2 2v14h2V3h12V1zm3 4H8c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h11c1.1 0 2-.9 2-2V7c0-1.1-.9-2-2-2zm0 16H8V7h11v14z"/></svg>
                            </button>
                        </div>
                        <div class="stat-grid">
                            <div class="stat-box">
                                <div class="stat-box-label">Today</div>
                                ${limitDisplay}
                            </div>
                            <div class="stat-box">
                                <div class="stat-box-label">Total</div>
                                <div class="stat-box-value">${total}</div>
                            </div>
                            <div class="stat-box">
                                <div class="stat-box-label">Timer</div>
                                ${timerHtml}
                            </div>
                        </div>
                        <div class="key-actions">
                            <button class="action-btn edit" onclick='openEditModal(${JSON.stringify(k).replace(/'/g, "&apos;")})'>
                                <svg viewBox="0 0 24 24"><path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/></svg>
                                Edit
                            </button>
                            <button class="action-btn delete" onclick="deleteKey('${k.id}', '${escapeHtml(k.name)}')">
                                <svg viewBox="0 0 24 24"><path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/></svg>
                                Delete
                            </button>
                        </div>
                    </div>`;
            }).join('');
        }

        /* ===== HELPERS ===== */
        function escapeHtml(s) {
            return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#039;');
        }

        function copyKey(key) {
            navigator.clipboard.writeText(key);
            toast('Key copied to clipboard');
        }

        /* ===== EDIT MODAL ===== */
        function openEditModal(k) {
            document.getElementById('editId').value = k.id;
            document.getElementById('editName').value = k.name;
            document.getElementById('editLimit').value = k.daily_limit || 0;
            document.getElementById('editStatus').value = k.status || 'active';
            document.getElementById('editExpiry').value = k.expires_at || '';
            document.getElementById('editModal').classList.add('show');
            document.body.style.overflow = 'hidden';
        }

        function closeEditModal() {
            document.getElementById('editModal').classList.remove('show');
            document.body.style.overflow = '';
        }

        async function saveEdit() {
            const payload = {
                id: document.getElementById('editId').value,
                name: document.getElementById('editName').value.trim(),
                daily_limit: document.getElementById('editLimit').value,
                status: document.getElementById('editStatus').value,
                expires_at: document.getElementById('editExpiry').value
            };

            const res = await fetch('admin.php?action=api_update', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify(payload)
            });
            const data = await res.json();

            if (data.success) {
                toast('Key updated successfully');
                closeEditModal();
                loadKeys();
            } else {
                toast(data.error || 'Failed', 'error');
            }
        }

        /* ===== DELETE ===== */
        async function deleteKey(id, name) {
            if (!confirm(`Delete key "${name}"?\nThis action cannot be undone.`)) return;

            const res = await fetch('admin.php?action=api_delete', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({id})
            });
            const data = await res.json();

            if (data.success) {
                toast('Key deleted');
                loadKeys();
            } else {
                toast(data.error || 'Failed', 'error');
            }
        }

        /* ===== FILTER ===== */
        function filterKeys() {
            const q = document.getElementById('searchInput').value.toLowerCase();
            document.querySelectorAll('.key-card').forEach(card => {
                const name = card.getAttribute('data-name') || '';
                const key = card.getAttribute('data-key') || '';
                card.style.display = (name.includes(q) || key.includes(q)) ? '' : 'none';
            });
        }

        /* ===== INIT ===== */
        if (PAGE === 'history') loadKeys();
        if (document.getElementById('expiresAt')) updateTimerStatus();

        document.getElementById('editModal')?.addEventListener('click', function(e) {
            if (e.target === this) closeEditModal();
        });
    </script>
</body>
</html>
    <?php
}
?>
