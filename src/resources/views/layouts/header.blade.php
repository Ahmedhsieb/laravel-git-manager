<header class="bg-white shadow-sm">
    <div class="flex items-center justify-between px-6 py-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">
                @yield('page-title', 'Dashboard')
            </h2>
            <p class="text-sm text-gray-600">
                @yield('page-description', 'Manage your repository')
            </p>
        </div>

        <div class="flex items-center space-x-4">
            <!-- Refresh Button -->
            <button onclick="loadStatus()" class="btn btn-primary flex items-center">
                <i class="fas fa-sync-alt mr-2"></i>
                Refresh
            </button>

            <!-- User Info -->
            <div class="flex items-center space-x-3 border-l pl-4">
                <div class="text-right">
                    <div class="text-sm font-medium text-gray-800" id="header-user-name">
                        {{ config('git-manager.user_name', 'Git User') }}
                    </div>
                    <div class="text-xs text-gray-500" id="header-user-email">
                        {{ config('git-manager.user_email', '') }}
                    </div>
                </div>
                <div class="w-10 h-10 rounded-full bg-blue-600 flex items-center justify-center text-white font-bold">
                    {{ strtoupper(substr(config('git-manager.user_name', 'G'), 0, 1)) }}
                </div>
            </div>

            <!-- Logout -->
            <form action="{{ route('git-manager.logout') }}" method="POST">
                @csrf
                <button type="submit" class="text-gray-600 hover:text-red-600 transition-colors" title="Logout">
                    <i class="fas fa-sign-out-alt text-lg"></i>
                </button>
            </form>
        </div>
    </div>
</header>