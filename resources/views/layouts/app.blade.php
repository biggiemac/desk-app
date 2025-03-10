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
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @if (isset($header))
                <header class="bg-white dark:bg-gray-800 shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <!-- Page Content -->
            <main>
                @yield('content')
            </main>

            <!-- Donate Button for authenticated users (not shown on home page) -->
            @auth
                @unless(request()->routeIs('home'))
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        <div class="py-8 text-center">
                            <p class="text-gray-600 mb-4">Find this useful? Consider supporting the project!</p>
                            <a href="https://paypal.me/biggiemac" 
                               target="_blank"
                               class="inline-flex items-center px-6 py-3 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition-colors">
                                <svg class="w-5 h-5 mr-2" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M20.067 8.478c.492.315.844.825.983 1.422.545 2.339-.881 4.803-3.13 5.42l-.04.007c-.29.055-.59.083-.89.083h-3.35l-.89 4.11c-.054.258-.282.442-.548.442h-2.76c-.242 0-.422-.224-.367-.456l.077-.363.84-3.89.078-.363c.056-.232.236-.456.478-.456h1.674c3.19 0 5.686-.886 6.412-3.45.56-1.97-.346-3.08-1.07-3.51"/>
                                    <path d="M7.926 8.478c.492.315.844.825.983 1.422.545 2.339-.881 4.803-3.13 5.42l-.04.007c-.29.055-.59.083-.89.083h-3.35l-.89 4.11c-.054.258-.282.442-.548.442h-2.76c-.242 0-.422-.224-.367-.456l.077-.363.84-3.89.078-.363c.056-.232.236-.456.478-.456h1.674c3.19 0 5.686-.886 6.412-3.45.56-1.97-.346-3.08-1.07-3.51"/>
                                </svg>
                                Make a Donation
                            </a>
                        </div>
                    </div>
                @endunless
            @endauth
        </div>
    </body>
</html>
