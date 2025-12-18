<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>Pojok IMS - Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <!-- Tailwind CDN for quick test, ganti dengan build Tailwind kalau di proyek beneran -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-indigo-600 to-purple-700 min-h-screen flex items-center justify-center font-sans">

    <div class="bg-white rounded-xl shadow-xl p-10 max-w-md w-full animate-fadeInUp">
        <h3 class="text-3xl font-bold text-gray-900 mb-8 text-center">
            <span class="text-red-500">POJOK</span> Inventory Management System
        </h3>

        <h2 class="text-2xl font-semibold text-green-600 mb-6 text-center">Log In</h2>

        <!-- Alert Messages -->
        @if(session('success'))
            <div class="bg-green-100 text-green-800 rounded-md p-4 mb-4 flex items-center space-x-2">
                <i class="fa fa-check-circle"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 text-red-800 rounded-md p-4 mb-4 flex items-center space-x-2">
                <i class="fa fa-exclamation-circle"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-100 text-red-800 rounded-md p-4 mb-4">
                <i class="fa fa-exclamation-triangle"></i>
                <ul class="list-disc list-inside mt-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="space-y-6" novalidate>
            @csrf
            <div>
                <input 
                    type="email" 
                    name="email" 
                    value="{{ old('email') }}" 
                    placeholder="Email"
                    required
                    class="w-full border-b-2 border-gray-300 focus:border-green-600 outline-none py-2 text-gray-900 placeholder-gray-400"
                />
            </div>

            <div>
                <input 
                    type="password" 
                    name="password" 
                    placeholder="Password" 
                    required
                    class="w-full border-b-2 border-gray-300 focus:border-green-600 outline-none py-2 text-gray-900 placeholder-gray-400"
                />
            </div>

            <div class="flex items-center justify-between">
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-6 rounded-md transition">
                    Login
                </button>

                <label class="inline-flex items-center space-x-2 text-gray-700 text-sm">
                    <input type="checkbox" name="remember" class="form-checkbox text-green-600" />
                    <span>Remember me</span>
                </label>
            </div>
        </form>

        <hr class="border-t-4 border-green-600 my-8">

        <div class="bg-gray-100 rounded-md p-4 text-gray-700 text-sm leading-relaxed">
            <strong>Demo Accounts:</strong><br>
            Admin: admin@pojokims.com / admin123<br>
            Petugas: petugas@pojokims.com / petugas123
        </div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
    <script>
        if (!window.jQuery) {
            var script = document.createElement('script');
            script.src = "{{ asset('assets/js/vendor/jquery/jquery-1.11.2.min.js') }}";
            document.head.appendChild(script);
        }
    </script>

    <script src="{{ asset('assets/js/vendor/bootstrap/bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/js/main.js') }}"></script>

    <script>
        $(window).on('load', function(){
            setTimeout(() => $('.alert').fadeOut('slow'), 5000);
            $('input[name="email"]').focus();
        });
    </script>

    <style>
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translate3d(0, 30px, 0);
            }
            to {
                opacity: 1;
                transform: none;
            }
        }
        .animate-fadeInUp {
            animation: fadeInUp 0.6s ease forwards;
        }
    </style>
</body>
</html>
