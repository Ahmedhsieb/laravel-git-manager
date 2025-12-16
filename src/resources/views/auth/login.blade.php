<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Git Manager - Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body
    class="bg-gradient-to-br from-gray-900 via-blue-900 to-gray-900 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full">
        <div class="bg-white rounded-2xl shadow-2xl p-8 transform hover:scale-105 transition-transform duration-300">
            <!-- Logo & Title -->
            <div class="text-center mb-8">
                <div class="inline-block p-4 bg-blue-100 rounded-full mb-4">
                    <i class="fas fa-code-branch text-5xl text-blue-600"></i>
                </div>
                <h1 class="text-3xl font-bold text-gray-800 mb-2">Git Manager</h1>
                <p class="text-gray-600">Secure repository management</p>
            </div>

            <!-- Error Message -->
            @if(session('error'))
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded mb-6 flex items-start">
                    <i class="fas fa-exclamation-circle text-red-500 mr-3 mt-1"></i>
                    <div>
                        <p class="font-semibold">Authentication Failed</p>
                        <p class="text-sm">{{ session('error') }}</p>
                    </div>
                </div>
            @endif

            <!-- Success Message -->
            @if(session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded mb-6 flex items-start">
                    <i class="fas fa-check-circle text-green-500 mr-3 mt-1"></i>
                    <div>
                        <p class="font-semibold">Success</p>
                        <p class="text-sm">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            <!-- Login Form -->
            <form action="{{ route('git-manager.authenticate') }}" method="POST" class="space-y-6">
                @csrf

                <div>
                    <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-lock mr-2"></i>Access Password
                    </label>
                    <input type="password" id="password" name="password" required autofocus
                        class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all"
                        placeholder="Enter your password">
                </div>

                <button type="submit"
                    class="w-full bg-gradient-to-r from-blue-600 to-blue-700 text-white py-3 rounded-lg hover:from-blue-700 hover:to-blue-800 transition-all font-semibold shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                    <i class="fas fa-sign-in-alt mr-2"></i>
                    Access Git Manager
                </button>
            </form>

            <!-- Help Text -->
            <div class="mt-8 p-4 bg-gray-50 rounded-lg border border-gray-200">
                <p class="text-sm text-gray-600 mb-2">
                    <i class="fas fa-question-circle text-blue-600 mr-2"></i>
                    <strong>Forgot your password?</strong>
                </p>
                <p class="text-xs text-gray-500 mb-2">Run this command to update your password:</p>
                <code class="block bg-gray-800 text-green-400 px-3 py-2 rounded font-mono text-xs">
                    php artisan git-manager:config password
                </code>
            </div>
        </div>

        <!-- Footer -->
        <div class="text-center mt-6 text-gray-400 text-sm">
            <p>
                <i class="fas fa-shield-alt mr-1"></i>
                Protected by password authentication
            </p>
            <p class="mt-2">
                Git Manager v1.0.0 |
                <a href="https://github.com/ahmedhsieb/laravel-git-manager" target="_blank"
                    class="text-blue-400 hover:text-blue-300">
                    Documentation
                </a>
            </p>
        </div>
    </div>
</body>

</html>