{{--
    404 Error Page - Page Not Found
    
    Displays user-friendly 404 error with navigation back to home.
    Includes custom styling and responsive design.
    
    Author: SlowWebDev
--}}

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Page Not Found</title>
    @vite(['resources/css/app.css'])
    <style>
        .zoom-404 {
            font-size: 15rem;
            line-height: 1;
        }
    </style>
</head>
<body class="min-h-screen bg-gray-900">
    <div class="min-h-screen flex items-center justify-center">
        <div class="text-center">
            <div class="zoom-404 font-bold text-gray-200/20 mb-8">
                404
            </div>
            <h1 class="mb-4 text-3xl font-bold text-white">
                Page Not Found
            </h1>
            <a href="{{ route('home') }}" 
               class="inline-flex items-center px-6 py-3 text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-all">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to Home
            </a>
            </div>
        </div>
    </div>
</body>
</html>
