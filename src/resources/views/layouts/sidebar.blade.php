<aside class="w-64 bg-gray-800 text-white flex-shrink-0">
    <div class="p-6">
        <div class="flex items-center space-x-3">
            <i class="fas fa-code-branch text-3xl text-blue-400"></i>
            <div>
                <h1 class="text-xl font-bold">Git Manager</h1>
                <p class="text-xs text-gray-400">Version Control</p>
            </div>
        </div>
    </div>

    <nav class="mt-6">
        <a href="{{ route('git-manager.index') }}"
            class="sidebar-link {{ request()->routeIs('git-manager.index') ? 'active' : '' }}">
            <i class="fas fa-home w-5"></i>
            <span class="ml-3">Dashboard</span>
        </a>

        <div class="px-4 py-2 text-xs font-semibold text-gray-400 uppercase tracking-wider mt-6">
            Operations
        </div>

        <a href="#status" class="sidebar-link" onclick="loadStatus(); return false;">
            <i class="fas fa-info-circle w-5"></i>
            <span class="ml-3">Status</span>
        </a>

        <a href="#branches" class="sidebar-link" onclick="loadBranches(); return false;">
            <i class="fas fa-code-branch w-5"></i>
            <span class="ml-3">Branches</span>
        </a>

        <a href="#commits" class="sidebar-link" onclick="loadLog(); return false;">
            <i class="fas fa-history w-5"></i>
            <span class="ml-3">Commits</span>
        </a>

        <div class="px-4 py-2 text-xs font-semibold text-gray-400 uppercase tracking-wider mt-6">
            Settings
        </div>

        <a href="#config" class="sidebar-link" onclick="showGitUser(); return false;">
            <i class="fas fa-cog w-5"></i>
            <span class="ml-3">Git Config</span>
        </a>

        <form action="{{ route('git-manager.logout') }}" method="POST" class="mt-auto">
            @csrf
            <button type="submit" class="sidebar-link w-full text-left hover:bg-red-700">
                <i class="fas fa-sign-out-alt w-5"></i>
                <span class="ml-3">Logout</span>
            </button>
        </form>
    </nav>

    <!-- Repository Info -->
    <div class="absolute bottom-0 w-64 p-4 bg-gray-900 border-t border-gray-700">
        <div class="text-xs text-gray-400">
            <div class="flex items-center justify-between mb-1">
                <span>Branch:</span>
                <span id="sidebar-branch" class="text-blue-400 font-mono">-</span>
            </div>
            <div class="flex items-center justify-between">
                <span>Status:</span>
                <span id="sidebar-status" class="text-green-400">-</span>
            </div>
        </div>
    </div>
</aside>