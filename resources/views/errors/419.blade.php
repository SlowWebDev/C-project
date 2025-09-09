<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>419 - Session Expired</title>
    @vite(['resources/css/app.css'])
    <style>
        .zoom-419 {
            font-size: 15rem;
            line-height: 1;
        }
    </style>
</head>
<body class="min-h-screen bg-gray-900">
    <div class="min-h-screen flex items-center justify-center">
        <div class="text-center">
            <div class="zoom-419 font-bold text-gray-200/20 mb-8">
                419
            </div>
            <h1 class="mb-4 text-3xl font-bold text-white">
                Session Expired
            </h1>
            <button id="goHomeBtn"
               class="inline-flex items-center px-6 py-3 text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-all">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to Home
            </button>
            </div>
        </div>
    </div>
    
    <script>
        document.getElementById('goHomeBtn').onclick = function() {
            this.innerHTML = '<svg class="w-5 h-5 mr-2 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" class="opacity-25"></circle><path fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" class="opacity-75"></path></svg>Clearing session...';
            this.disabled = true;
            
            document.cookie.split(";").forEach(cookie => {
                const name = cookie.split("=")[0].trim();
                document.cookie = name + "=;expires=Thu, 01 Jan 1970 00:00:00 GMT;path=/";
                document.cookie = name + "=;expires=Thu, 01 Jan 1970 00:00:00 GMT;path=/;domain=" + window.location.hostname;
            });
            
            try { localStorage.clear(); sessionStorage.clear(); } catch(e) {}
            
            setTimeout(() => window.location.href = '/', 1000);
        };
    </script>
</body>
</html>
