<footer class="bg-white border-t border-gray-200 px-6 py-3">
    <div class="flex items-center justify-between text-sm text-gray-600">
        <div>
            <span class="font-semibold">Git Manager</span> v1.0.0
            <span class="mx-2">|</span>
            <a href="https://github.com/ahmedhsieb/laravel-git-manager" target="_blank" class="hover:text-blue-600">
                <i class="fab fa-github"></i> Documentation
            </a>
        </div>
        <div class="flex items-center space-x-4">
            <span>Session expires in: <span id="session-timer" class="font-mono">--:--</span></span>
            <span>Repository: <span class="font-mono text-blue-600">{{ basename(base_path()) }}</span></span>
        </div>
    </div>
</footer>

<script>
    // Session timer
    function updateSessionTimer() {
        const duration = {{ config('git-manager.session_duration', 120) }} * 60; // minutes to seconds
        const authenticatedAt = {{ Session::get('git_manager_authenticated_at', time()) }};
        const expiresAt = authenticatedAt + duration;
        const now = Math.floor(Date.now() / 1000);
        const remaining = expiresAt - now;

        if (remaining <= 0) {
            window.location.reload();
            return;
        }

        const minutes = Math.floor(remaining / 60);
        const seconds = remaining % 60;
        document.getElementById('session-timer').textContent =
            `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
    }

    setInterval(updateSessionTimer, 1000);
    updateSessionTimer();
</script>