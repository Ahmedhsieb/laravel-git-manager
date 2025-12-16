<?php

namespace Ahmedhsieb\GitManager\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class GitManagerSetup extends Command
{
    protected $signature = 'git-manager:setup';
    protected $description = 'Setup Git Manager configuration';

    public function handle()
    {
        $this->info('🚀 Git Manager Setup Wizard');
        $this->line('');

        // Check if already configured
        if ($this->isConfigured()) {
            if (!$this->confirm('Git Manager is already configured. Do you want to reconfigure?', false)) {
                $this->info('Setup cancelled.');
                return 0;
            }
        }

        // Step 1: Git Username
        $this->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->info('Step 1: Git Authentication');
        $this->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');

        $username = $this->ask('Enter your Git username (GitHub/GitLab/Bitbucket)', config('git-manager.username'));

        // Step 2: Personal Access Token
        $this->line('');
        $this->warn('⚠️  Generate a Personal Access Token:');
        $this->line('   GitHub: https://github.com/settings/tokens');
        $this->line('   GitLab: https://gitlab.com/-/profile/personal_access_tokens');
        $this->line('');

        $token = $this->secret('Enter your Personal Access Token');

        if (empty($token)) {
            $this->error('Token is required!');
            return 1;
        }

        // Step 3: Git User Identity
        $this->line('');
        $this->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->info('Step 2: Git User Identity (for commits)');
        $this->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');

        $userName = $this->ask('Enter your full name', config('git-manager.user_name'));
        $userEmail = $this->ask('Enter your email', config('git-manager.user_email'));

        // Validate email
        if (!filter_var($userEmail, FILTER_VALIDATE_EMAIL)) {
            $this->error('Invalid email address!');
            return 1;
        }

        // Step 4: Access Password
        $this->line('');
        $this->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->info('Step 3: Interface Security');
        $this->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->warn('Set a password to protect the Git Manager interface');

        $password = $this->secret('Enter access password (min 6 characters)');

        if (strlen($password) < 6) {
            $this->error('Password must be at least 6 characters!');
            return 1;
        }

        $passwordConfirm = $this->secret('Confirm password');

        if ($password !== $passwordConfirm) {
            $this->error('Passwords do not match!');
            return 1;
        }

        // Hash password
        $hashedPassword = bcrypt($password);

        // Step 5: Configure globally
        $this->line('');
        $configureGlobally = $this->confirm('Do you want to configure Git globally on this server?', true);

        if ($configureGlobally) {
            $this->configureGitGlobally($userName, $userEmail);
        }

        // Save to .env
        $this->line('');
        $this->info('💾 Saving configuration...');

        $this->updateEnv([
            'GIT_MANAGER_USERNAME' => $username,
            'GIT_MANAGER_TOKEN' => $token,
            'GIT_MANAGER_USER_NAME' => $userName,
            'GIT_MANAGER_USER_EMAIL' => $userEmail,
            'GIT_MANAGER_PASSWORD' => $hashedPassword,
        ]);

        // Clear cache
        $this->call('config:clear');

        // Summary
        $this->line('');
        $this->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->info('✅ Setup Complete!');
        $this->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->line('');
        $this->line('Configuration saved:');
        $this->line("  📧 Email: {$userEmail}");
        $this->line("  👤 Name: {$userName}");
        $this->line("  🔐 Password: ********");
        $this->line('');
        $this->info('Access Git Manager at: ' . url(config('git-manager.route_prefix', 'git-manager')));
        $this->line('');

        return 0;
    }

    protected function isConfigured()
    {
        return !empty(config('git-manager.username'))
            && !empty(config('git-manager.token'))
            && !empty(config('git-manager.access_password'));
    }

    protected function configureGitGlobally($name, $email)
    {
        $this->info('⚙️  Configuring Git globally...');

        try {
            exec("git config --global user.name " . escapeshellarg($name), $output, $return);
            exec("git config --global user.email " . escapeshellarg($email), $output, $return);

            if ($return === 0) {
                $this->info('✅ Git configured globally');
            } else {
                $this->warn('⚠️  Could not configure Git globally. You may need to do this manually.');
            }
        } catch (\Exception $e) {
            $this->warn('⚠️  Could not configure Git globally: ' . $e->getMessage());
        }
    }

    protected function updateEnv(array $data)
    {
        $envPath = base_path('.env');

        if (!File::exists($envPath)) {
            $this->error('.env file not found!');
            return;
        }

        $envContent = File::get($envPath);

        foreach ($data as $key => $value) {
            // Escape special characters in value
            $value = str_replace('"', '\"', $value);
            $value = str_replace('$', '\$', $value);

            // Check if key exists
            if (preg_match("/^{$key}=/m", $envContent)) {
                // Update existing
                $envContent = preg_replace(
                    "/^{$key}=.*/m",
                    "{$key}=\"{$value}\"",
                    $envContent
                );
            } else {
                // Add new
                $envContent .= "\n{$key}=\"{$value}\"";
            }
        }

        File::put($envPath, $envContent);
    }
}
