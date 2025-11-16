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

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }

            body {
                font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 20px;
            }

            .auth-container {
                width: 100%;
                max-width: 420px;
                background: white;
                padding: 40px;
                border-radius: 8px;
                box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            }

            h1 {
                font-size: 24px;
                font-weight: 600;
                margin-bottom: 30px;
                text-align: center;
                color: #333;
            }

            label {
                display: block;
                margin-bottom: 8px;
                font-weight: 500;
                color: #333;
            }

            input[type="text"],
            input[type="email"],
            input[type="password"] {
                width: 100%;
                padding: 12px;
                border: 1px solid #ddd;
                border-radius: 6px;
                font-size: 14px;
                margin-bottom: 15px;
                transition: border-color 0.3s;
            }

            input[type="text"]:focus,
            input[type="email"]:focus,
            input[type="password"]:focus {
                outline: none;
                border-color: #667eea;
                box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
            }

            button[type="submit"] {
                width: 100%;
                padding: 12px;
                background: #667eea;
                color: white;
                border: none;
                border-radius: 6px;
                font-size: 16px;
                font-weight: 500;
                cursor: pointer;
                transition: background 0.3s;
            }

            button[type="submit"]:hover {
                background: #5568d3;
            }

            a {
                color: #667eea;
                text-decoration: none;
            }

            a:hover {
                text-decoration: underline;
            }

            .error {
                color: #dc2626;
                font-size: 12px;
                margin-top: -12px;
                margin-bottom: 15px;
            }

            .text-center {
                text-align: center;
            }

            .checkbox {
                display: flex;
                align-items: center;
                margin: 15px 0;
            }

            .checkbox input[type="checkbox"] {
                width: auto;
                margin: 0 8px 0 0;
            }
        </style>
    </head>
    <body>
        <div class="auth-container">
            {{ $slot }}
        </div>
    </body>
</html>
