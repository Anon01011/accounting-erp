@php
use Illuminate\Support\Facades\Route;
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --primary-color: #2563eb;
            --primary-dark: #1d4ed8;
            --primary-light: #dbeafe;
            --accent-color: #059669;
            --text-primary: #1e293b;
            --text-secondary: #64748b;
            --background: #f8fafc;
            --white: #ffffff;
            --gradient-start: #0f172a;
            --gradient-middle: #1e40af;
            --gradient-end: #3b82f6;
            --gradient-accent: #0ea5e9;
        }

        body {
            font-family: 'Figtree', sans-serif;
            background: linear-gradient(
                135deg,
                var(--gradient-start) 0%,
                var(--gradient-middle) 25%,
                var(--gradient-end) 50%,
                var(--gradient-accent) 75%,
                var(--gradient-start) 100%
            );
            background-size: 400% 400%;
            animation: gradientBG 20s ease infinite;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
            margin: 0;
            padding: 1rem;
        }

        @keyframes gradientBG {
            0% {
                background-position: 0% 50%;
            }
            50% {
                background-position: 100% 50%;
            }
            100% {
                background-position: 0% 50%;
            }
        }

        .animated-bg {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            opacity: 0.1;
            background: 
                radial-gradient(circle at 20% 20%, rgba(255, 255, 255, 0.8) 0%, transparent 50%),
                radial-gradient(circle at 80% 80%, rgba(255, 255, 255, 0.8) 0%, transparent 50%),
                radial-gradient(circle at 50% 50%, rgba(255, 255, 255, 0.4) 0%, transparent 70%);
            animation: pulseLight 10s ease-in-out infinite;
        }

        @keyframes pulseLight {
            0% {
                opacity: 0.1;
                transform: scale(1);
            }
            50% {
                opacity: 0.2;
                transform: scale(1.1);
            }
            100% {
                opacity: 0.1;
                transform: scale(1);
            }
        }

        .login-container {
            width: 100%;
            max-width: 380px;
            padding: 2rem;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(12px);
            border-radius: 1rem;
            box-shadow: 
                0 8px 32px rgba(0, 0, 0, 0.1),
                0 2px 8px rgba(0, 0, 0, 0.05),
                inset 0 1px 1px rgba(255, 255, 255, 0.5);
            transform: translateY(0);
            transition: all 0.3s ease;
            border: 1px solid rgba(255, 255, 255, 0.2);
            animation: containerFloat 6s ease-in-out infinite;
        }

        @keyframes containerFloat {
            0% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-5px);
            }
            100% {
                transform: translateY(0);
            }
        }

        .login-container:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
        }

        .login-header {
            text-align: center;
            margin-bottom: 1.5rem;
            position: relative;
        }

        .login-header h1 {
            color: var(--text-primary);
            font-size: 1.75rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .login-header p {
            color: var(--text-secondary);
            font-size: 0.875rem;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-label {
            display: block;
            color: var(--text-primary);
            font-size: 0.813rem;
            font-weight: 500;
            margin-bottom: 0.25rem;
        }

        .form-input {
            width: 100%;
            padding: 0.625rem 0.875rem;
            border: 1px solid #e2e8f0;
            border-radius: 0.5rem;
            background-color: rgba(255, 255, 255, 0.9);
            transition: all 0.2s;
            font-size: 0.875rem;
            height: 2.5rem;
        }

        .form-input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
            background-color: var(--white);
        }

        .remember-me {
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
        }

        .remember-me input[type="checkbox"] {
            width: 0.875rem;
            height: 0.875rem;
            margin-right: 0.375rem;
            border: 1px solid #e2e8f0;
            border-radius: 0.25rem;
            transition: all 0.2s;
        }

        .remember-me label {
            color: var(--text-secondary);
            font-size: 0.813rem;
            user-select: none;
        }

        .login-button {
            width: 100%;
            padding: 0.625rem;
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            color: var(--white);
            border: none;
            border-radius: 0.5rem;
            font-weight: 500;
            font-size: 0.875rem;
            cursor: pointer;
            transition: all 0.2s;
            height: 2.5rem;
            position: relative;
            overflow: hidden;
        }

        .login-button:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2);
        }

        .login-button:active {
            transform: translateY(0);
        }

        .test-credentials-btn {
            width: 100%;
            padding: 0.625rem;
            background: linear-gradient(135deg, var(--white), var(--primary-light));
            color: var(--text-primary);
            border: 1px solid rgba(37, 99, 235, 0.2);
            border-radius: 0.5rem;
            font-weight: 500;
            font-size: 0.875rem;
            cursor: pointer;
            transition: all 0.2s;
            margin-top: 1rem;
            height: 2.5rem;
        }

        .test-credentials-btn:hover {
            background: linear-gradient(135deg, var(--primary-light), var(--white));
            border-color: var(--primary-color);
            transform: translateY(-1px);
        }

        .test-credentials-btn i {
            margin-right: 0.5rem;
            color: var(--primary-color);
        }

        .forgot-password {
            display: block;
            text-align: center;
            color: var(--primary-color);
            font-size: 0.875rem;
            text-decoration: none;
            margin-top: 1rem;
            transition: all 0.2s;
            position: relative;
        }

        .forgot-password:hover {
            color: var(--primary-dark);
            transform: translateY(-1px);
        }

        .forgot-password::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 50%;
            width: 0;
            height: 1px;
            background: var(--primary-color);
            transition: all 0.2s;
            transform: translateX(-50%);
        }

        .forgot-password:hover::after {
            width: 100%;
        }

        .error-message {
            color: #dc2626;
            font-size: 0.875rem;
            margin-top: 0.5rem;
        }

        .divider {
            display: flex;
            align-items: center;
            text-align: center;
            margin: 1.5rem 0;
            color: var(--text-secondary);
            font-size: 0.875rem;
        }

        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid rgba(226, 232, 240, 0.8);
        }

        .divider::before {
            margin-right: 1rem;
        }

        .divider::after {
            margin-left: 1rem;
        }

        @media (max-width: 480px) {
            .login-container {
                padding: 2rem;
            }

            .login-header h1 {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="animated-bg"></div>
    
    <div class="login-container">
        <div class="login-header">
            <h1>Welcome Back</h1>
            <p>Please sign in to your account</p>
        </div>

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="form-group">
                <label for="email" class="form-label">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" class="form-input" placeholder="Enter your email">
                @error('email')
                    <p class="error-message">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <label for="password" class="form-label">Password</label>
                <input id="password" type="password" name="password" required autocomplete="current-password" class="form-input" placeholder="Enter your password">
                @error('password')
                    <p class="error-message">{{ $message }}</p>
                @enderror
            </div>

            <div class="remember-me">
                <input type="checkbox" id="remember_me" name="remember">
                <label for="remember_me">Remember me</label>
            </div>

            <button type="submit" class="login-button">
                Sign in
            </button>

            <div class="divider">or</div>

            <button type="button" class="test-credentials-btn" onclick="fillTestCredentials()">
                <i class="fas fa-key"></i> Use Test Credentials
            </button>

            @if (\Illuminate\Support\Facades\Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="forgot-password">
                    Forgot your password?
                </a>
            @endif
        </form>
    </div>

    <script>
        function fillTestCredentials() {
            document.getElementById('email').value = 'admin@accounting.com';
            document.getElementById('password').value = 'admin123';
            document.getElementById('remember_me').checked = true;
        }
    </script>
</body>
</html> 