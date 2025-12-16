<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Git Manager')</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        [x-cloak] { display: none !important; }
        
        .sidebar-link {
            @apply flex items-center px-4 py-3 text-gray-300 hover:bg-gray-700 hover:text-white transition-colors duration-200;
        }
        
        .sidebar-link.active {
            @apply bg-gray-700 text-white border-l-4 border-blue-500;
        }
        
        .card {
            @apply bg-white rounded-lg shadow-md p-6;
        }
        
        .btn {
            @apply px-4 py-2 rounded-lg font-medium transition-colors duration-200;
        }
        
        .btn-primary {
            @apply bg-blue-600 text-white hover:bg-blue-700;
        }
        
        .btn-success {
            @apply bg-green-600 text-white hover:bg-green-700;
        }
        
        .btn-danger {
            @apply bg-red-600 text-white hover:bg-red-700;
        }
        
        .btn-warning {
            @apply bg-yellow-600 text-white hover:bg-yellow-700;
        }
        
        .btn-dark {
            @apply bg-gray-800 text-white hover:bg-gray-900;
        }
        
        .console {
            @apply bg-gray-900 text-green-400 font-mono text-sm p-4 rounded-lg overflow-auto;
        }
    </style>
    
    @stack('styles')
</head>
<body class="bg-gray-100">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        @include('git-manager::layouts.sidebar')
        
        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Header -->
            @include('git-manager::layouts.header')
            
            <!-- Page Content -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 p-6">
                @yield('content')
            </main>
            
            <!-- Footer -->
            @include('git-manager::layouts.footer')
        </div>
    </div>
    
    @stack('scripts')
</body>
</html>