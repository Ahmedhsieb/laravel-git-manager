<?php

namespace Ahmedhsieb\GitManager\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class GitManagerAuth
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if password is configured
        $configuredPassword = config('git-manager.access_password');

        if (empty($configuredPassword)) {
            return response()->view('git-manager::auth.setup-required');
        }

        // Allow login route
        if ($request->is('*/login') || $request->is('*/authenticate')) {
            return $next($request);
        }

        // Check if authenticated
        if ($this->isAuthenticated()) {
            return $next($request);
        }

        // Redirect to login
        return redirect()->route('git-manager.login');
    }

    /**
     * Check if user is authenticated
     */
    protected function isAuthenticated()
    {
        $authenticated = Session::get('git_manager_authenticated', false);
        $authenticatedAt = Session::get('git_manager_authenticated_at');

        if (!$authenticated || !$authenticatedAt) {
            return false;
        }

        // Check if session expired
        $sessionDuration = config('git-manager.session_duration', 120); // minutes
        $expiresAt = $authenticatedAt + ($sessionDuration * 60);

        if (time() > $expiresAt) {
            $this->logout();
            return false;
        }

        return true;
    }

    /**
     * Logout user
     */
    protected function logout()
    {
        Session::forget('git_manager_authenticated');
        Session::forget('git_manager_authenticated_at');
    }

    /**
     * Authenticate user
     */
    public static function authenticate($password)
    {
        $configuredPassword = config('git-manager.access_password');

        if (Hash::check($password, $configuredPassword)) {
            Session::put('git_manager_authenticated', true);
            Session::put('git_manager_authenticated_at', time());
            return true;
        }

        return false;
    }

    /**
     * Logout user (static method)
     */
    public static function logoutUser()
    {
        Session::forget('git_manager_authenticated');
        Session::forget('git_manager_authenticated_at');
    }
}
