<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Git Username
    |--------------------------------------------------------------------------
    |
    | Your Git username for authentication (GitHub, GitLab, Bitbucket, etc.)
    |
    */
    'username' => env('GIT_MANAGER_USERNAME', ''),

    /*
    |--------------------------------------------------------------------------
    | Git Personal Access Token
    |--------------------------------------------------------------------------
    |
    | Your personal access token for authentication
    | GitHub: Settings → Developer settings → Personal access tokens
    | GitLab: Settings → Access Tokens
    |
    */
    'token' => env('GIT_MANAGER_TOKEN', ''),

    /*
    |--------------------------------------------------------------------------
    | Git User Name (for commits)
    |--------------------------------------------------------------------------
    |
    | The name that will appear in git commits
    |
    */
    'user_name' => env('GIT_MANAGER_USER_NAME', ''),

    /*
    |--------------------------------------------------------------------------
    | Git User Email (for commits)
    |--------------------------------------------------------------------------
    |
    | The email that will appear in git commits
    |
    */
    'user_email' => env('GIT_MANAGER_USER_EMAIL', ''),

    /*
    |--------------------------------------------------------------------------
    | Access Password
    |--------------------------------------------------------------------------
    |
    | Password to access the Git Manager interface
    | This is hashed for security
    |
    */
    'access_password' => env('GIT_MANAGER_PASSWORD', ''),

    /*
    |--------------------------------------------------------------------------
    | Session Duration (minutes)
    |--------------------------------------------------------------------------
    |
    | How long the authentication session lasts before requiring password again
    |
    */
    'session_duration' => env('GIT_MANAGER_SESSION_DURATION', 120), // 2 hours

    /*
    |--------------------------------------------------------------------------
    | Default Remote
    |--------------------------------------------------------------------------
    |
    | The default remote repository name
    |
    */
    'default_remote' => env('GIT_MANAGER_DEFAULT_REMOTE', 'origin'),

    /*
    |--------------------------------------------------------------------------
    | Repository Path
    |--------------------------------------------------------------------------
    |
    | Default repository path (usually your Laravel root)
    |
    */
    'repository_path' => base_path(),

    /*
    |--------------------------------------------------------------------------
    | Command Timeout (seconds)
    |--------------------------------------------------------------------------
    |
    | Timeout for git commands in seconds
    |
    */
    'timeout' => env('GIT_MANAGER_TIMEOUT', 300),

    /*
    |--------------------------------------------------------------------------
    | Route Prefix
    |--------------------------------------------------------------------------
    |
    | The URL prefix for accessing the Git Manager
    | Example: 'git-manager' will be accessible at /git-manager
    |
    */
    'route_prefix' => env('GIT_MANAGER_ROUTE_PREFIX', 'git-manager'),

    /*
    |--------------------------------------------------------------------------
    | Middleware
    |--------------------------------------------------------------------------
    |
    | Additional middleware to apply to Git Manager routes
    | Example: ['web', 'auth'] to require Laravel authentication
    |
    */
    'middleware' => ['web'],

];
