<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') - {{ config('app.name', 'ITAM Enterprise') }}</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    
    <style>
        :root {
            --color-bg: #090d16;
            --color-card: #0f172a;
            --color-text-main: #f8fafc;
            --color-text-muted: #94a3b8;
            --color-accent: #4f46e5;
            --color-accent-hover: #4338ca;
            --color-border: #1e293b;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
            background-color: var(--color-bg);
            color: var(--color-text-main);
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            overflow: hidden;
            position: relative;
        }

        /* Abstract Background Elements */
        .bg-glow {
            position: absolute;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(79, 70, 229, 0.15) 0%, rgba(0, 0, 0, 0) 70%);
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 0;
            pointer-events: none;
            filter: blur(40px);
        }

        .bg-glow-2 {
            position: absolute;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(239, 68, 68, 0.1) 0%, rgba(0, 0, 0, 0) 70%);
            top: 20%;
            right: 10%;
            z-index: 0;
            pointer-events: none;
            filter: blur(40px);
        }

        .error-container {
            position: relative;
            z-index: 10;
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 24px;
            padding: 60px 40px;
            text-align: center;
            max-width: 600px;
            width: 90%;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5), inset 0 1px 0 rgba(255, 255, 255, 0.1);
            animation: floatUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            opacity: 0;
            transform: translateY(30px);
        }

        @keyframes floatUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .error-code {
            font-family: 'JetBrains Mono', monospace;
            font-size: 8rem;
            font-weight: 800;
            line-height: 1;
            background: linear-gradient(135deg, #f8fafc 0%, #94a3b8 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 20px;
            text-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            position: relative;
        }

        .error-title {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 16px;
            letter-spacing: -0.02em;
        }

        .error-message {
            font-size: 1.125rem;
            color: var(--color-text-muted);
            line-height: 1.6;
            margin-bottom: 40px;
        }

        .action-buttons {
            display: flex;
            gap: 16px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 14px 28px;
            border-radius: 999px;
            font-size: 1rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s ease;
            cursor: pointer;
        }

        .btn-primary {
            background-color: var(--color-accent);
            color: white;
            border: 1px solid var(--color-accent);
            box-shadow: 0 4px 15px rgba(79, 70, 229, 0.3);
        }

        .btn-primary:hover {
            background-color: var(--color-accent-hover);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(79, 70, 229, 0.4);
        }

        .btn-secondary {
            background-color: rgba(255, 255, 255, 0.05);
            color: var(--color-text-main);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .btn-secondary:hover {
            background-color: rgba(255, 255, 255, 0.1);
            transform: translateY(-2px);
        }

        .icon-container {
            margin-bottom: 20px;
            font-size: 4rem;
            color: var(--color-accent);
            opacity: 0.8;
            filter: drop-shadow(0 0 20px rgba(79, 70, 229, 0.4));
        }

        @media (max-width: 640px) {
            .error-code { font-size: 6rem; }
            .error-title { font-size: 1.5rem; }
            .error-message { font-size: 1rem; }
            .error-container { padding: 40px 20px; }
            .btn { width: 100%; }
        }
    </style>
</head>
<body>
    <div class="bg-glow"></div>
    <div class="bg-glow-2"></div>
    
    <div class="error-container">
        @yield('content')
    </div>
</body>
</html>
