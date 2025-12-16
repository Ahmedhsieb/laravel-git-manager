<?php

namespace Ahmedhsieb\GitManager\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;

class GitManagerConfig extends Command
{
    protected $signature = 'git-manager:config {option?}';
    protected $description = 'Update Git Manager configuration';

    protected $configOptions = [
        'username' => 'Git Username',
        'token' => 'Personal Access Token',
        'user_name' => 'Git User Name (for commits)',
        'user_email' => 'Git User Email (for commits)',
        'password' => 'Access Password',
        'all' => 'Update All Settings',
    ];

    public function handle()
    {
        $this->info('🔧 Git Manager Configuration');
        $this->line('');

        // Verify current password first
        if (!$this->verifyPassword()) {
            $this->error('❌ Invalid password!');
            return 1;
        }

        $option = $this->argument('option');

        if (!$option) {
            $option = $this->choice(
                'What would you like to update?',
                array_values($this->configOptions),
                0
            );

            // Convert display name back to key
            $option = array_search($option, $this->configOptions);
        }

        $this->line('');

        switch ($option) {
            case 'username':
                $this->updateUsername();
                break;
            case 'token':
                $this->updateToken();
                break;
            case 'user_name':
                $this->updateUserName();
                break;
            case 'user_email':
                $this->updateUserEmail();
                break;
            case 'password':
                $this->updatePassword();
                break;
            case 'all':
                $this->updateAll();
                break;
            default:
                $this->error('Invalid option!');
                return 1;
        }

        $this->call('config:clear');

        $this->line('');
        $this->info('✅ Configuration updated successfully!');

        return 0;
    }

    protected function verifyPassword()
    {
        $currentPassword = config('git-manager.access_password');

        if (empty($currentPassword)) {
            $this->warn('⚠️  No password set. Please run: php artisan git-manager:setup');
            return false;
        }

        $password = $this->secret('Enter current access password');

        return Hash::check($password, $currentPassword);
    }

    // protected function updateUsername()
    // {
    //     $current = config('git-manager.username');
    //     $this->line("Current username: {$current}");

    //     $username = $this->ask('Enter new Git username', $current);

    //     $this->updateEnv('GIT_MANAGER_USERNAME', $username);
    //     $this->info('✓ Username updated');
    // }

    protected function updateToken()
    {
        $this->warn('⚠️  Generate a new token at:');
        $this->line('   GitHub: https://github.com/settings/tokens');
        $this->line('   GitLab: https://gitlab.com/-/profile/personal_access_tokens');
        $this->line('');

        $token = $this->secret('Enter new Personal Access Token');

        if (empty($token)) {
            $this->error('Token cannot be empty!');
            return;
        }

        $this->updateEnv('GIT_MANAGER_TOKEN', $token);
        $this->info('✓ Token updated');
    }

    protected function updateUserName()
    {
        $current = config('git-manager.user_name');
        $this->line("Current name: {$current}");

        $userName = $this->ask('Enter new Git user name', $current);

        $this->updateEnv('GIT_MANAGER_USER_NAME', $userName);

        if ($this->confirm('Update Git globally?', false)) {
            exec("git config --global user.name " . escapeshellarg($userName));
            $this->info('✓ Git configured globally');
        }

        $this->info('✓ User name updated');
    }

    protected function updateUserEmail()
    {
        $current = config('git-manager.user_email');
        $this->line("Current email: {$current}");

        $userEmail = $this->ask('Enter new Git user email', $current);

        if (!filter_var($userEmail, FILTER_VALIDATE_EMAIL)) {
            $this->error('Invalid email address!');
            return;
        }

        $this->updateEnv('GIT_MANAGER_USER_EMAIL', $userEmail);

        if ($this->confirm('Update Git globally?', false)) {
            exec("git config --global user.email " . escapeshellarg($userEmail));
            $this->info('✓ Git configured globally');
        }

        $this->info('✓ User email updated');
    }

    protected function updatePassword()
    {
        $password = $this->secret('Enter new password (min 6 characters)');

        if (strlen($password) < 6) {
            $this->error('Password must be at least 6 characters!');
            return;
        }

        $passwordConfirm = $this->secret('Confirm new password');

        if ($password !== $passwordConfirm) {
            $this->error('Passwords do not match!');
            return;
        }

        $hashedPassword = bcrypt($password);
        $this->updateEnv('GIT_MANAGER_PASSWORD', $hashedPassword);
        $this->info('✓ Password updated');
    }

    protected function updateAll()
    {
        $this->updateUsername();
        $this->line('');
        $this->updateToken();
        $this->line('');
        $this->updateUserName();
        $this->line('');
        $this->updateUserEmail();
        $this->line('');

        if ($this->confirm('Do you want to change the access password?', false)) {
            $this->updatePassword();
        }
    }

    protected function updateEnv($key, $value)
    {
        $envPath = base_path('.env');

        if (!File::exists($envPath)) {
            $this->error('.env file not found!');
            return;
        }

        $envContent = File::get($envPath);

        // Escape special characters
        $value = str_replace('"', '\"', $value);
        $value = str_replace('$', '\$', $value);

        if (preg_match("/^{$key}=/m", $envContent)) {
            $envContent = preg_replace(
                "/^{$key}=.*/m",
                "{$key}=\"{$value}\"",
                $envContent
            );
        } else {
            $envContent .= "\n{$key}=\"{$value}\"";
        }

        File::put($envPath, $envContent);
    }
}
