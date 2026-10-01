<?php
// ------------------------------------------------------------
// Number Info API - Landing Page
// Shubham Hacker Edition
// ------------------------------------------------------------
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="theme-color" content="#05070f">
    <title>Number Info API · Shubham Hacker</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; -webkit-tap-highlight-color: transparent; }
        html { scroll-behavior: smooth; }
        body {
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
                radial-gradient(ellipse at 15% 8%, rgba(99,102,241,0.18) 0%, transparent 45%),
                radial-gradient(ellipse at 85% 92%, rgba(168,85,247,0.18) 0%, transparent 45%),
                radial-gradient(ellipse at 50% 50%, rgba(236,72,153,0.06) 0%, transparent 60%);
            pointer-events: none;
            z-index: 0;
        }

        /* ===== HEADER ===== */
        .header {
            position: sticky;
            top: 0;
            z-index: 100;
            background: rgba(5,7,15,0.85);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border-bottom: 1px solid rgba(99,102,241,0.12);
            padding: 14px 0;
        }
        .header-inner {
            max-width: 1100px;
            margin: 0 auto;
            padding: 0 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
        }
        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            color: inherit;
        }
        .brand-icon {
            width: 44px;
            height: 44px;
            background: linear-gradient(135deg, #6366f1, #a855f7);
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 10px 25px rgba(99,102,241,0.4);
            position: relative;
            flex-shrink: 0;
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
            font-size: 16px;
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
        .header-cta {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 11px 20px;
            background: linear-gradient(135deg, #6366f1, #a855f7);
            border-radius: 12px;
            color: #fff;
            font-size: 13px;
            font-weight: 700;
            text-decoration: none;
            box-shadow: 0 10px 25px rgba(99,102,241,0.4);
            letter-spacing: 0.2px;
            white-space: nowrap;
        }
        .header-cta svg { width: 15px; height: 15px; fill: #fff; }

        /* ===== MAIN ===== */
        .main {
            position: relative;
            z-index: 1;
            max-width: 1100px;
            margin: 0 auto;
            padding: 0 24px;
        }

        /* ===== HERO ===== */
        .hero {
            text-align: center;
            padding: 60px 0 40px;
        }
        .hero-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 7px 16px;
            background: rgba(99,102,241,0.1);
            border: 1px solid rgba(99,102,241,0.25);
            border-radius: 30px;
            color: #a5b4fc;
            font-size: 12px;
            font-weight: 600;
            margin-bottom: 26px;
            letter-spacing: 0.3px;
        }
        .hero-pill svg { width: 13px; height: 13px; fill: #a5b4fc; }
        .hero h1 {
            font-size: 54px;
            font-weight: 900;
            line-height: 1.05;
            letter-spacing: -2px;
            margin-bottom: 20px;
            color: #fff;
        }
        .hero h1 .grad {
            background: linear-gradient(135deg, #6366f1, #a855f7, #ec4899);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            display: inline-block;
        }
        .hero-sub {
            font-size: 18px;
            color: #94a3b8;
            font-weight: 500;
            max-width: 640px;
            margin: 0 auto 18px;
            line-height: 1.6;
        }
        .hero-desc {
            font-size: 15px;
            color: #64748b;
            font-weight: 500;
            max-width: 640px;
            margin: 0 auto 32px;
            line-height: 1.7;
        }

        /* ===== CTA ROW ===== */
        .cta-row {
            display: flex;
            justify-content: center;
            gap: 12px;
            flex-wrap: wrap;
            margin-bottom: 16px;
        }
        .btn-hero {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 17px 32px;
            border-radius: 16px;
            font-size: 15px;
            font-weight: 700;
            text-decoration: none;
            letter-spacing: 0.2px;
            transition: all 0.3s;
            border: none;
            cursor: pointer;
            font-family: inherit;
        }
        .btn-hero.primary {
            background: linear-gradient(135deg, #6366f1, #a855f7);
            color: #fff;
            box-shadow: 0 15px 40px rgba(99,102,241,0.5);
        }
        .btn-hero.secondary {
            background: rgba(30,41,59,0.7);
            color: #e2e8f0;
            border: 1px solid rgba(99,102,241,0.25);
        }
        .btn-hero svg { width: 18px; height: 18px; fill: currentColor; }
        .btn-hero:active { transform: scale(0.97); }

        /* ===== TRUST BADGES ===== */
        .trust-row {
            display: flex;
            justify-content: center;
            gap: 24px;
            flex-wrap: wrap;
            padding: 20px 24px;
            margin: 24px auto 0;
            max-width: 720px;
            background: rgba(15,20,35,0.5);
            border: 1px solid rgba(99,102,241,0.12);
            border-radius: 20px;
            backdrop-filter: blur(10px);
        }
        .trust-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            color: #94a3b8;
            font-weight: 600;
        }
        .trust-item svg { width: 16px; height: 16px; fill: #10b981; flex-shrink: 0; }

        /* ===== TERMINAL SHOWCASE ===== */
        .terminal-wrap {
            margin: 50px auto 40px;
            max-width: 780px;
            border-radius: 20px;
            overflow: hidden;
            border: 1px solid rgba(99,102,241,0.25);
            background: rgba(10,15,28,0.95);
            box-shadow: 0 30px 80px rgba(99,102,241,0.15);
        }
        .terminal-bar {
            background: rgba(15,20,35,0.95);
            padding: 13px 18px;
            display: flex;
            align-items: center;
            gap: 8px;
            border-bottom: 1px solid rgba(99,102,241,0.15);
        }
        .terminal-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
        }
        .dot-red { background: #ef4444; }
        .dot-yellow { background: #f59e0b; }
        .dot-green { background: #10b981; }
        .terminal-title {
            margin-left: 12px;
            font-family: 'JetBrains Mono', monospace;
            font-size: 12px;
            color: #64748b;
            font-weight: 500;
        }
        .terminal-body {
            padding: 26px 24px;
            font-family: 'JetBrains Mono', monospace;
            font-size: 13px;
            line-height: 1.9;
            color: #94a3b8;
        }
        .term-line { margin-bottom: 8px; }
        .term-prompt { color: #a5b4fc; font-weight: 600; }
        .term-cmd { color: #e2e8f0; }
        .term-resp { color: #10b981; }
        .term-key { color: #f59e0b; }
        .term-cursor {
            display: inline-block;
            width: 8px;
            height: 16px;
            background: #a5b4fc;
            animation: blink 1s infinite;
            vertical-align: middle;
            margin-left: 4px;
        }
        @keyframes blink {
            0%, 50% { opacity: 1; }
            51%, 100% { opacity: 0; }
        }

        /* ===== SECTION HEADER ===== */
        .section-head {
            text-align: center;
            padding: 60px 0 30px;
        }
        .section-tag {
            display: inline-block;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 3px;
            color: #a5b4fc;
            text-transform: uppercase;
            margin-bottom: 14px;
        }
        .section-title {
            font-size: 36px;
            font-weight: 900;
            color: #fff;
            letter-spacing: -1px;
            margin-bottom: 14px;
            line-height: 1.15;
        }
        .section-desc {
            font-size: 16px;
            color: #94a3b8;
            font-weight: 500;
            max-width: 620px;
            margin: 0 auto;
            line-height: 1.65;
        }

        /* ===== REAL API TESTER ===== */
        .tester {
            max-width: 720px;
            margin: 0 auto;
            background: rgba(15,20,35,0.75);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(99,102,241,0.2);
            border-radius: 26px;
            padding: 32px 28px;
            box-shadow: 0 30px 80px rgba(99,102,241,0.12);
        }
        .tester-title {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 24px;
        }
        .tester-icon {
            width: 44px;
            height: 44px;
            background: linear-gradient(135deg, #6366f1, #a855f7);
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 10px 25px rgba(99,102,241,0.35);
            flex-shrink: 0;
        }
        .tester-icon svg { width: 22px; height: 22px; fill: #fff; }
        .tester-title h3 {
            font-size: 18px;
            font-weight: 800;
            color: #fff;
            letter-spacing: -0.3px;
        }
        .tester-title p {
            font-size: 12px;
            color: #94a3b8;
            font-weight: 500;
            margin-top: 2px;
        }

        .field {
            margin-bottom: 18px;
        }
        .field-label {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 12px;
            font-weight: 700;
            color: #cbd5e1;
            letter-spacing: 0.3px;
            margin-bottom: 9px;
            padding-left: 2px;
            text-transform: uppercase;
        }
        .field-label svg { width: 14px; height: 14px; fill: #a78bfa; flex-shrink: 0; }
        .field-input {
            width: 100%;
            padding: 16px 18px;
            background: rgba(5,10,20,0.8);
            border: 1.5px solid #1e2d48;
            border-radius: 14px;
            color: #e2e8f0;
            font-size: 14px;
            font-family: inherit;
            outline: none;
            transition: all 0.25s;
        }
        .field-input:focus {
            border-color: #6366f1;
            background: rgba(5,10,20,0.95);
            box-shadow: 0 0 0 4px rgba(99,102,241,0.12);
        }
        .field-input::placeholder { color: #475569; }
        .field-input.mono { font-family: 'JetBrains Mono', monospace; font-size: 13px; letter-spacing: 0.3px; }

        .btn-info {
            width: 100%;
            padding: 18px;
            background: linear-gradient(135deg, #6366f1, #a855f7);
            border: none;
            border-radius: 16px;
            color: #fff;
            font-size: 15px;
            font-weight: 800;
            cursor: pointer;
            font-family: inherit;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            transition: all 0.3s;
            box-shadow: 0 15px 40px rgba(99,102,241,0.45);
            letter-spacing: 0.3px;
            margin-top: 6px;
        }
        .btn-info svg { width: 18px; height: 18px; fill: #fff; }
        .btn-info:active { transform: scale(0.98); }
        .btn-info:disabled { opacity: 0.6; cursor: not-allowed; }

        .tester-loader {
            display: none;
            text-align: center;
            padding: 32px 0;
        }
        .tester-loader.show { display: block; }
        .spinner {
            width: 44px;
            height: 44px;
            border: 3px solid rgba(99,102,241,0.15);
            border-top-color: #6366f1;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
            margin: 0 auto 14px;
        }
        @keyframes spin { to { transform: rotate(360deg); } }
        .tester-loader p { color: #94a3b8; font-size: 13px; font-weight: 600; }

        .result-box {
            display: none;
            margin-top: 22px;
            background: rgba(5,10,20,0.9);
            border: 1px solid #1e2d48;
            border-radius: 16px;
            overflow: hidden;
        }
        .result-box.show { display: block; animation: fadeUp 0.35s ease-out; }
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .result-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 18px;
            background: rgba(15,20,35,0.8);
            border-bottom: 1px solid #1e2d48;
        }
        .result-status {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 12px;
            font-weight: 700;
            color: #10b981;
            letter-spacing: 0.3px;
        }
        .result-status.error { color: #f87171; }
        .result-status svg { width: 14px; height: 14px; fill: currentColor; }
        .result-copy {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            background: rgba(51,65,85,0.5);
            border: 1px solid #2a3a5a;
            border-radius: 8px;
            color: #cbd5e1;
            font-size: 11px;
            font-weight: 700;
            cursor: pointer;
            font-family: inherit;
            transition: all 0.25s;
        }
        .result-copy svg { width: 12px; height: 12px; fill: currentColor; }
        .result-copy:active { transform: scale(0.94); background: #6366f1; border-color: #6366f1; color: #fff; }
        .result-content {
            padding: 20px;
            font-family: 'JetBrains Mono', monospace;
            font-size: 12px;
            line-height: 1.8;
            color: #10b981;
            white-space: pre-wrap;
            word-break: break-all;
            max-height: 500px;
            overflow-y: auto;
        }
        .result-content::-webkit-scrollbar { width: 6px; }
        .result-content::-webkit-scrollbar-track { background: transparent; }
        .result-content::-webkit-scrollbar-thumb { background: rgba(99,102,241,0.4); border-radius: 3px; }

        /* ===== FEATURE GRID ===== */
        .feature-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 18px;
            margin: 40px 0;
        }
        .feature-card {
            background: rgba(15,20,35,0.6);
            border: 1px solid rgba(99,102,241,0.15);
            border-radius: 20px;
            padding: 26px 22px;
            transition: all 0.35s;
        }
        .feature-card:hover {
            border-color: rgba(99,102,241,0.4);
            transform: translateY(-4px);
            box-shadow: 0 20px 50px rgba(99,102,241,0.15);
        }
        .feature-icon {
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, rgba(99,102,241,0.15), rgba(168,85,247,0.15));
            border: 1px solid rgba(99,102,241,0.25);
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 18px;
        }
        .feature-icon svg { width: 22px; height: 22px; fill: #a5b4fc; }
        .feature-card h4 {
            font-size: 16px;
            font-weight: 800;
            color: #fff;
            margin-bottom: 8px;
            letter-spacing: -0.2px;
        }
        .feature-card p {
            font-size: 13px;
            color: #94a3b8;
            line-height: 1.6;
            font-weight: 500;
        }

        /* ===== DOC SECTION ===== */
        .docs {
            background: rgba(15,20,35,0.6);
            border: 1px solid rgba(99,102,241,0.15);
            border-radius: 24px;
            padding: 36px 30px;
            margin: 40px 0;
        }
        .endpoint-box {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 18px 20px;
            background: rgba(5,10,20,0.8);
            border: 1px solid #1e2d48;
            border-radius: 14px;
            margin-bottom: 24px;
            flex-wrap: wrap;
        }
        .endpoint-method {
            padding: 5px 12px;
            background: linear-gradient(135deg, #10b981, #059669);
            color: #fff;
            border-radius: 8px;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 0.8px;
            flex-shrink: 0;
        }
        .endpoint-url {
            flex: 1;
            font-family: 'JetBrains Mono', monospace;
            font-size: 13px;
            color: #a5b4fc;
            word-break: break-all;
            font-weight: 500;
            min-width: 200px;
        }
        .doc-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 26px;
        }
        .doc-table th {
            text-align: left;
            padding: 12px 16px;
            font-size: 11px;
            font-weight: 800;
            color: #a5b4fc;
            text-transform: uppercase;
            letter-spacing: 1px;
            border-bottom: 1px solid rgba(99,102,241,0.15);
        }
        .doc-table td {
            padding: 14px 16px;
            font-size: 13px;
            color: #cbd5e1;
            border-bottom: 1px solid rgba(99,102,241,0.08);
            font-weight: 500;
        }
        .doc-table td code {
            font-family: 'JetBrains Mono', monospace;
            background: rgba(5,10,20,0.8);
            padding: 3px 8px;
            border-radius: 6px;
            color: #f59e0b;
            font-size: 12px;
        }
        .doc-subhead {
            font-size: 15px;
            font-weight: 800;
            color: #fff;
            margin: 28px 0 14px;
            display: flex;
            align-items: center;
            gap: 10px;
            letter-spacing: -0.2px;
        }
        .doc-subhead svg { width: 18px; height: 18px; fill: #a78bfa; }
        .code-snippet {
            background: rgba(5,10,20,0.9);
            border: 1px solid #1e2d48;
            border-radius: 14px;
            padding: 20px;
            font-family: 'JetBrains Mono', monospace;
            font-size: 12px;
            line-height: 1.8;
            color: #10b981;
            overflow-x: auto;
            white-space: pre;
            font-weight: 500;
        }

        /* ===== PRICING / CTA ===== */
        .cta-block {
            background: linear-gradient(135deg, rgba(99,102,241,0.15), rgba(168,85,247,0.1));
            border: 1px solid rgba(99,102,241,0.3);
            border-radius: 28px;
            padding: 50px 30px;
            text-align: center;
            margin: 60px 0;
            position: relative;
            overflow: hidden;
        }
        .cta-block::before {
            content: '';
            position: absolute;
            top: -50%; right: -20%;
            width: 400px; height: 400px;
            background: radial-gradient(circle, rgba(99,102,241,0.3) 0%, transparent 70%);
            border-radius: 50%;
            pointer-events: none;
        }
        .cta-block h2 {
            font-size: 34px;
            font-weight: 900;
            color: #fff;
            margin-bottom: 14px;
            letter-spacing: -1px;
            position: relative;
        }
        .cta-block p {
            font-size: 16px;
            color: #94a3b8;
            margin-bottom: 28px;
            position: relative;
            font-weight: 500;
        }
        .cta-block .btn-hero { position: relative; }

        /* ===== FOOTER ===== */
        .footer {
            text-align: center;
            padding: 40px 24px;
            border-top: 1px solid rgba(99,102,241,0.12);
            margin-top: 40px;
            color: #64748b;
            font-size: 13px;
            position: relative;
            z-index: 1;
        }
        .footer-credit {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 12px 24px;
            background: rgba(15,20,35,0.6);
            border: 1px solid rgba(99,102,241,0.15);
            border-radius: 30px;
            margin-bottom: 16px;
        }
        .footer-credit svg { width: 16px; height: 16px; fill: #a78bfa; }
        .footer-credit span { color: #a5b4fc; font-weight: 700; }
        .footer p:last-child { font-size: 11px; color: #475569; }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 768px) {
            .hero h1 { font-size: 38px; letter-spacing: -1.5px; }
            .hero-sub { font-size: 16px; }
            .hero-desc { font-size: 14px; }
            .section-title { font-size: 28px; }
            .section-desc { font-size: 14px; }
            .tester { padding: 26px 20px; border-radius: 22px; }
            .docs { padding: 28px 20px; }
            .cta-block { padding: 40px 22px; }
            .cta-block h2 { font-size: 26px; }
            .header-inner { padding: 0 16px; }
            .main { padding: 0 16px; }
            .brand-text h1 { font-size: 14px; }
            .brand-icon { width: 40px; height: 40px; border-radius: 12px; }
            .brand-icon svg { width: 20px; height: 20px; }
            .header-cta { padding: 9px 14px; font-size: 12px; }
            .header-cta svg { width: 13px; height: 13px; }
            .trust-row { padding: 16px; gap: 16px; }
            .trust-item { font-size: 12px; }
            .terminal-body { padding: 20px 18px; font-size: 11px; }
            .endpoint-box { padding: 14px 16px; }
            .endpoint-url { font-size: 11px; }
            .doc-table th, .doc-table td { padding: 10px 12px; font-size: 12px; }
        }
        @media (max-width: 480px) {
            .hero h1 { font-size: 32px; letter-spacing: -1px; }
            .hero { padding: 40px 0 30px; }
            .section-title { font-size: 24px; }
            .section-head { padding: 40px 0 24px; }
            .btn-hero { padding: 15px 24px; font-size: 14px; }
            .cta-row { flex-direction: column; }
            .btn-hero { width: 100%; }
            .feature-grid { grid-template-columns: 1fr; }
            .brand-text p { display: none; }
            .terminal-title { display: none; }
        }
    </style>
</head>
<body>
    <!-- ===== HEADER ===== -->
    <header class="header">
        <div class="header-inner">
            <a href="/" class="brand">
                <div class="brand-icon">
                    <svg viewBox="0 0 24 24"><path d="M7 2v11h3v9l7-12h-4l4-8z"/></svg>
                </div>
                <div class="brand-text">
                    <h1>SHUBHAM HACKER</h1>
                    <p>Number Intelligence</p>
                </div>
            </a>
            <a href="admin.php" class="header-cta">
                <svg viewBox="0 0 24 24"><path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm0 10.99h7c-.53 4.12-3.28 7.79-7 8.94V12H5V6.3l7-3.11v8.8z"/></svg>
                Admin
            </a>
        </div>
    </header>

    <main class="main">
        <!-- ===== HERO ===== -->
        <section class="hero">
            <div class="hero-pill">
                <svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
                Live · Phone · Aadhaar · Identity
            </div>
            <h1>The API that runs<br><span class="grad">your intelligence.</span></h1>
            <p class="hero-sub">Query. Reveal. Verify.</p>
            <p class="hero-desc">Shubham Hacker turns a single phone number into a rich, actionable identity — name, address, telecom circle, alternate numbers, and more. Built for investigators, security teams, and analysts.</p>

            <div class="cta-row">
                <a href="#test" class="btn-hero primary">
                    <svg viewBox="0 0 24 24"><path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/></svg>
                    Get Info
                </a>
                <a href="#docs" class="btn-hero secondary">
                    <svg viewBox="0 0 24 24"><path d="M14 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/></svg>
                    Documentation
                </a>
            </div>

            <div class="trust-row">
                <div class="trust-item">
                    <svg viewBox="0 0 24 24"><path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4z"/></svg>
                    Secure API
                </div>
                <div class="trust-item">
                    <svg viewBox="0 0 24 24"><path d="M13 3c-4.97 0-9 4.03-9 9H1l3.89 3.89.07.14L9 12H6c0-3.87 3.13-7 7-7s7 3.13 7 7-3.13 7-7 7c-1.93 0-3.68-.79-4.94-2.06l-1.42 1.42C8.27 19.99 10.51 21 13 21c4.97 0 9-4.03 9-9s-4.03-9-9-9z"/></svg>
                    Real-time Data
                </div>
                <div class="trust-item">
                    <svg viewBox="0 0 24 24"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 3c1.93 0 3.5 1.57 3.5 3.5S13.93 13 12 13s-3.5-1.57-3.5-3.5S10.07 6 12 6zm7 13H5v-.23c0-.62.28-1.2.76-1.58C7.47 15.82 9.64 15 12 15s4.53.82 6.24 2.19c.48.38.76.97.76 1.58V19z"/></svg>
                    Full Identity
                </div>
            </div>
        </section>

        <!-- ===== TERMINAL SHOWCASE ===== -->
        <div class="terminal-wrap">
            <div class="terminal-bar">
                <div class="terminal-dot dot-red"></div>
                <div class="terminal-dot dot-yellow"></div>
                <div class="terminal-dot dot-green"></div>
                <span class="terminal-title">shubham · api · v1</span>
            </div>
            <div class="terminal-body">
                <div class="term-line"><span class="term-prompt">$</span> <span class="term-cmd">curl "https://api.shubham.dev/num?num=8800952843&key=YOUR_KEY"</span></div>
                <div class="term-line"><span class="term-resp">→ status: 200 OK</span></div>
                <div class="term-line">{</div>
                <div class="term-line">&nbsp;&nbsp;<span class="term-key">"success"</span>: <span class="term-resp">true</span>,</div>
                <div class="term-line">&nbsp;&nbsp;<span class="term-key">"name"</span>: <span class="term-resp">"Prem Kumar"</span>,</div>
                <div class="term-line">&nbsp;&nbsp;<span class="term-key">"father_name"</span>: <span class="term-resp">"Jwala Prasad"</span>,</div>
                <div class="term-line">&nbsp;&nbsp;<span class="term-key">"address"</span>: <span class="term-resp">"a49 om vihar phase 5 delhi 110059"</span>,</div>
                <div class="term-line">&nbsp;&nbsp;<span class="term-key">"circle"</span>: <span class="term-resp">"AIRTEL DELHI"</span></div>
                <div class="term-line">}<span class="term-cursor"></span></div>
            </div>
        </div>

        <!-- ===== FEATURES ===== -->
        <div class="section-head">
            <span class="section-tag">Capabilities</span>
            <h2 class="section-title">One query, every insight</h2>
            <p class="section-desc">Every request returns clean, structured intelligence — ready for your workflows.</p>
        </div>

        <div class="feature-grid">
            <div class="feature-card">
                <div class="feature-icon">
                    <svg viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                </div>
                <h4>Identity Reveal</h4>
                <p>Full name, father's name, and unique identity ID linked to the number.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <svg viewBox="0 0 24 24"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>
                </div>
                <h4>Address Intelligence</h4>
                <p>Complete postal address with district, state, and pincode breakdown.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <svg viewBox="0 0 24 24"><path d="M1 9l2 2c4.97-4.97 13.03-4.97 18 0l2-2C16.93 2.93 7.08 2.93 1 9zm8 8l3 3 3-3c-1.65-1.66-4.34-1.66-6 0zm-4-4l2 2c2.76-2.76 7.24-2.76 10 0l2-2C15.14 9.14 8.87 9.14 5 13z"/></svg>
                </div>
                <h4>Telecom Circle</h4>
                <p>Carrier and geographic circle — Airtel, Jio, Vi, BSNL with region.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <svg viewBox="0 0 24 24"><path d="M20 15.5c-1.25 0-2.45-.2-3.57-.57-.35-.11-.74-.03-1.02.24l-2.2 2.2c-2.83-1.44-5.15-3.75-6.59-6.59l2.2-2.2c.28-.28.36-.67.25-1.02C8.7 6.45 8.5 5.25 8.5 4c0-.55-.45-1-1-1H4c-.55 0-1 .45-1 1 0 9.39 7.61 17 17 17 .55 0 1-.45 1-1v-3.5c0-.55-.45-1-1-1z"/></svg>
                </div>
                <h4>Alternate Numbers</h4>
                <p>Discover linked and alternate phone numbers tied to the same identity.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <svg viewBox="0 0 24 24"><path d="M19 3h-4.18C14.4 1.84 13.3 1 12 1c-1.3 0-2.4.84-2.82 2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 0c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zm2 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/></svg>
                </div>
                <h4>Clean JSON Output</h4>
                <p>Structured, predictable JSON responses — perfect for automation.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <svg viewBox="0 0 24 24"><path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm0 10.99h7c-.53 4.12-3.28 7.79-7 8.94V12H5V6.3l7-3.11v8.8z"/></svg>
                </div>
                <h4>Key-Based Access</h4>
                <p>Every request authenticated with your private API key and daily limits.</p>
            </div>
        </div>

        <!-- ===== REAL API TESTER ===== -->
        <div class="section-head" id="test">
            <span class="section-tag">Try it live</span>
            <h2 class="section-title">Get real intelligence now</h2>
            <p class="section-desc">Enter your API key and a phone number — the response below is real data.</p>
        </div>

        <div class="tester">
            <div class="tester-title">
                <div class="tester-icon">
                    <svg viewBox="0 0 24 24"><path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/></svg>
                </div>
                <div>
                    <h3>Live Number Lookup</h3>
                    <p>Enter your API key and number to fetch real info</p>
                </div>
            </div>

            <div class="field">
                <label class="field-label">
                    <svg viewBox="0 0 24 24"><path d="M12.65 10C11.83 7.67 9.61 6 7 6c-3.31 0-6 2.69-6 6s2.69 6 6 6c2.61 0 4.83-1.67 5.65-4H17v4h4v-4h2v-4H12.65zM7 14c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2z"/></svg>
                    API Key
                </label>
                <input type="text" class="field-input mono" id="apiKey" placeholder="NXX_XXXXXXXXXXXXXXXXXXXXXXXX">
            </div>

            <div class="field">
                <label class="field-label">
                    <svg viewBox="0 0 24 24"><path d="M20 15.5c-1.25 0-2.45-.2-3.57-.57-.35-.11-.74-.03-1.02.24l-2.2 2.2c-2.83-1.44-5.15-3.75-6.59-6.59l2.2-2.2c.28-.28.36-.67.25-1.02C8.7 6.45 8.5 5.25 8.5 4c0-.55-.45-1-1-1H4c-.55 0-1 .45-1 1 0 9.39 7.61 17 17 17 .55 0 1-.45 1-1v-3.5c0-.55-.45-1-1-1z"/></svg>
                    Phone Number
                </label>
                <input type="tel" class="field-input mono" id="phoneNum" placeholder="8800952843" maxlength="10" inputmode="numeric">
            </div>

            <button class="btn-info" id="getInfoBtn" onclick="getInfo()">
                <svg viewBox="0 0 24 24"><path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/></svg>
                Get Info
            </button>

            <div class="tester-loader" id="loader">
                <div class="spinner"></div>
                <p>Fetching intelligence...</p>
            </div>

            <div class="result-box" id="resultBox">
                <div class="result-bar">
                    <div class="result-status" id="resultStatus">
                        <svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
                        Success
                    </div>
                    <button class="result-copy" onclick="copyResult()">
                        <svg viewBox="0 0 24 24"><path d="M16 1H4c-1.1 0-2 .9-2 2v14h2V3h12V1zm3 4H8c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h11c1.1 0 2-.9 2-2V7c0-1.1-.9-2-2-2zm0 16H8V7h11v14z"/></svg>
                        Copy
                    </button>
                </div>
                <div class="result-content" id="resultContent"></div>
            </div>
        </div>

        <!-- ===== DOCUMENTATION ===== -->
        <div class="section-head" id="docs">
            <span class="section-tag">API Reference</span>
            <h2 class="section-title">Documentation</h2>
            <p class="section-desc">Everything you need to integrate the Shubham Number Info API.</p>
        </div>

        <div class="docs">
            <div class="doc-subhead">
                <svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
                Endpoint
            </div>
            <div class="endpoint-box">
                <span class="endpoint-method">GET</span>
                <span class="endpoint-url">/api.php?num=PHONE_NUMBER&key=YOUR_API_KEY</span>
            </div>

            <div class="doc-subhead">
                <svg viewBox="0 0 24 24"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/></svg>
                Parameters
            </div>
            <table class="doc-table">
                <thead>
                    <tr>
                        <th>Parameter</th>
                        <th>Type</th>
                        <th>Required</th>
                        <th>Description</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><code>num</code></td>
                        <td>String</td>
                        <td>Yes</td>
                        <td>10-digit Indian phone number to lookup</td>
                    </tr>
                    <tr>
                        <td><code>key</code></td>
                        <td>String</td>
                        <td>Yes</td>
                        <td>Your private API access key</td>
                    </tr>
                </tbody>
            </table>

            <div class="doc-subhead">
                <svg viewBox="0 0 24 24"><path d="M9.4 16.6L4.8 12l4.6-4.6L8 6l-6 6 6 6 1.4-1.4zm5.2 0l4.6-4.6-4.6-4.6L16 6l6 6-6 6-1.4-1.4z"/></svg>
                Example Request
            </div>
            <div class="code-snippet">curl "https://yourdomain.com/api.php?num=8800952843&key=NXX_XXXXXXXXXXXXXXXXXXXXXXXX"</div>

            <div class="doc-subhead" style="margin-top: 30px;">
                <svg viewBox="0 0 24 24"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/></svg>
                Sample Response
            </div>
            <div class="code-snippet">{
  "success": true,
  "number": "8800952843",
  "data": {
    "name": "Prem Kumar",
    "father_name": "Jwala Prasad",
    "mobile": "8800952843",
    "alternate_number": "8923794921",
    "address": "a49 om vihar phase 5 delhi uttam nagar 110059",
    "circle": "AIRTEL DELHI",
    "id": "377548772766",
    "email": null
  },
  "checked_at": "2026-09-26 10:30:45 UTC",
  "api_info": {
    "developed_by": "Creator Shyamchand & Ayan - CEO & Founder Of - Shubham Hacker",
    "organization": "Shubham Hackers",
    "purpose": "For Educational Purposes Only"
  }
}</div>

            <div class="doc-subhead" style="margin-top: 30px;">
                <svg viewBox="0 0 24 24"><path d="M1 21h22L12 2 1 21zm12-3h-2v-2h2v2zm0-4h-2v-4h2v4z"/></svg>
                Error Codes
            </div>
            <table class="doc-table">
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Meaning</th>
                    </tr>
                </thead>
                <tbody>
                    <tr><td><code>401</code></td><td>API key missing</td></tr>
                    <tr><td><code>403</code></td><td>Invalid / expired / limit exceeded</td></tr>
                    <tr><td><code>400</code></td><td>Invalid phone number format</td></tr>
                    <tr><td><code>404</code></td><td>No record found for number</td></tr>
                    <tr><td><code>502</code></td><td>Backend unavailable</td></tr>
                </tbody>
            </table>
        </div>

        <!-- ===== CTA ===== -->
        <div class="cta-block">
            <h2>Start your investigation today</h2>
            <p>Contact the admin to receive your personal API key with custom limits.</p>
            <a href="admin.php" class="btn-hero primary">
                <svg viewBox="0 0 24 24"><path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4z"/></svg>
                Get Access
            </a>
        </div>
    </main>

    <!-- ===== FOOTER ===== -->
    <footer class="footer">
        <div class="footer-credit">
            <svg viewBox="0 0 24 24"><path d="M7 2v11h3v9l7-12h-4l4-8z"/></svg>
            <span>Shubham Hacker</span>
        </div>
        <p>Creator Shyamchand & Ayan · CEO & Founder Of Shubham Hacker</p>
        <p style="margin-top: 8px;">For Educational Purposes Only · © <?php echo date('Y'); ?></p>
    </footer>

    <script>
        /* ===== PHONE INPUT SANITIZE ===== */
        document.getElementById('phoneNum').addEventListener('input', function(e) {
            this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10);
        });

        /* ===== GET INFO — REAL API ===== */
        async function getInfo() {
            const key = document.getElementById('apiKey').value.trim();
            const num = document.getElementById('phoneNum').value.trim();
            const btn = document.getElementById('getInfoBtn');
            const loader = document.getElementById('loader');
            const box = document.getElementById('resultBox');
            const content = document.getElementById('resultContent');
            const status = document.getElementById('resultStatus');

            // Validation
            if (!key) { alert('Please enter your API key'); return; }
            if (!num || num.length !== 10) { alert('Please enter a valid 10-digit phone number'); return; }

            btn.disabled = true;
            loader.classList.add('show');
            box.classList.remove('show');

            try {
                const response = await fetch(`api.php?num=${encodeURIComponent(num)}&key=${encodeURIComponent(key)}`);
                const data = await response.json();

                loader.classList.remove('show');
                btn.disabled = false;

                // Format content
                const jsonStr = JSON.stringify(data, null, 2);
                content.textContent = jsonStr;

                // Status
                if (data.success) {
                    status.className = 'result-status';
                    status.innerHTML = '<svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>Success';
                } else {
                    status.className = 'result-status error';
                    status.innerHTML = '<svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>Error';
                }

                box.classList.add('show');
            } catch (error) {
                loader.classList.remove('show');
                btn.disabled = false;
                content.textContent = 'Request failed: ' + error.message;
                status.className = 'result-status error';
                status.innerHTML = '<svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>Network Error';
                box.classList.add('show');
            }
        }

        /* ===== COPY RESULT ===== */
        function copyResult() {
            const content = document.getElementById('resultContent').textContent;
            navigator.clipboard.writeText(content);
            const btn = event.currentTarget;
            const original = btn.innerHTML;
            btn.innerHTML = '<svg viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>Copied';
            setTimeout(() => { btn.innerHTML = original; }, 1800);
        }

        /* ===== ENTER KEY SUBMIT ===== */
        document.getElementById('phoneNum').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') getInfo();
        });
        document.getElementById('apiKey').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') getInfo();
        });
    </script>
</body>
</html>
