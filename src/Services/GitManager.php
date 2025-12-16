<?php

namespace Ahmedhsieb\GitManager\Services;

use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\Process\Exception\ProcessFailedException;

class GitManager
{
    protected $repositoryPath;
    protected $username;
    protected $token;
    protected $timeout = 300;
    protected string $gitBinary;

    public function __construct($repositoryPath = null)
    {
        $this->repositoryPath = $repositoryPath ?? base_path();
        $this->username = config('app.git.username');
        $this->token = config('app.git.token');
        $this->gitBinary = $this->resolveGitBinary();

        // Auto-configure git user if not set
        $this->ensureGitUserConfigured();

        // Configure credential helper for HTTPS authentication
        $this->configureCredentialHelper();
    }

    /**
     * Configure Git credential helper for authentication
     */
    protected function configureCredentialHelper()
    {
        if ($this->username && $this->token) {
            // Set up credential helper to use our credentials
            $this->execute(['config', '--local', 'credential.helper', 'store']);

            // Create .git-credentials file with auth
            $remote = $this->execute(['remote', 'get-url', 'origin']);
            if ($remote['success']) {
                $url = trim($remote['output']);
                if (preg_match('/^https?:\/\/(.+)$/', $url, $matches)) {
                    $host = parse_url($url, PHP_URL_HOST);
                    $credentialUrl = "https://{$this->username}:{$this->token}@{$host}";

                    // Store credential
                    $credFile = $this->repositoryPath . '/.git/credentials';
                    file_put_contents($credFile, $credentialUrl . PHP_EOL);
                    chmod($credFile, 0600);
                }
            }
        }
    }

    /**
     * Resolve Git binary path
     */
    protected function resolveGitBinary(): string
    {
        if ($this->canRunCommand(['git', '--version'])) {
            return 'git';
        }

        if (PHP_OS_FAMILY === 'Windows') {
            $process = new Process(['where', 'git']);
            $process->run();

            if ($process->isSuccessful()) {
                $paths = explode(PHP_EOL, trim($process->getOutput()));
                return trim($paths[0]);
            }

            $commonPaths = [
                'C:\Program Files\Git\bin\git.exe',
                'C:\Program Files (x86)\Git\bin\git.exe',
                'C:\laragon\bin\git\bin\git.exe',
            ];

            foreach ($commonPaths as $path) {
                if (file_exists($path)) {
                    return $path;
                }
            }
        }

        $process = new Process(['which', 'git']);
        $process->run();

        if ($process->isSuccessful()) {
            return trim($process->getOutput());
        }

        $commonPaths = ['/usr/bin/git', '/usr/local/bin/git'];
        foreach ($commonPaths as $path) {
            if (file_exists($path)) {
                return $path;
            }
        }

        Log::warning('Could not resolve git binary path, using default "git"');
        return 'git';
    }

    /**
     * Check if a command can run successfully
     */
    protected function canRunCommand(array $command): bool
    {
        try {
            $process = new Process($command);
            $process->run();
            return $process->isSuccessful();
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * Ensure git user is configured
     */
    protected function ensureGitUserConfigured()
    {
        $emailResult = $this->execute(['config', 'user.email']);

        if (!$emailResult['success'] || empty(trim($emailResult['output']))) {
            $email = config('app.git.email', 'git@localhost');
            $this->execute(['config', '--local', 'user.email', $email]);
        }

        $nameResult = $this->execute(['config', 'user.name']);

        if (!$nameResult['success'] || empty(trim($nameResult['output']))) {
            $name = config('app.git.name', config('app.name', 'Git Manager'));
            $this->execute(['config', '--local', 'user.name', $name]);
        }
    }

    /**
     * Set Git credentials
     */
    public function setCredentials($username, $token)
    {
        $this->username = $username;
        $this->token = $token;
        $this->configureCredentialHelper();
        return $this;
    }

    /**
     * Execute a git command
     */
    protected function execute(array $command, $withAuth = false)
    {
        array_unshift($command, $this->gitBinary);

        $process = new Process($command, $this->repositoryPath);
        $process->setTimeout($this->timeout);

        // Set environment variables for authentication
        $env = [];

        if ($withAuth && $this->username && $this->token) {
            // Method 1: Environment variables
            $env['GIT_ASKPASS'] = '/bin/echo';
            $env['GIT_USERNAME'] = $this->username;
            $env['GIT_PASSWORD'] = $this->token;

            // Method 2: Set credential helper
            $env['GIT_TERMINAL_PROMPT'] = '0';
        }

        if (!empty($env)) {
            $process->setEnv($env);
        }

        try {
            $process->run();

            $output = $process->getOutput();
            $errorOutput = $process->getErrorOutput();

            return [
                'success' => $process->isSuccessful(),
                'output' => $output,
                'error' => $errorOutput,
                'exit_code' => $process->getExitCode(),
                'command' => implode(' ', $command)
            ];
        } catch (ProcessFailedException $e) {
            Log::error('Git command failed', [
                'command' => implode(' ', $command),
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'output' => '',
                'error' => $e->getMessage(),
                'exit_code' => $e->getProcess()->getExitCode(),
                'command' => implode(' ', $command)
            ];
        }
    }

    /**
     * Execute custom git command
     */
    public function executeCustom($commandString)
    {
        $commandString = preg_replace('/^git\s+/', '', trim($commandString));
        $command = $this->parseCommand($commandString);

        $dangerousCommands = ['rm', 'clean -fd', 'reset --hard HEAD~'];
        foreach ($dangerousCommands as $dangerous) {
            if (stripos($commandString, $dangerous) !== false) {
                return [
                    'success' => false,
                    'output' => '',
                    'error' => 'Dangerous command blocked for safety. Use specific methods instead.',
                    'exit_code' => 1,
                    'command' => $commandString
                ];
            }
        }

        return $this->execute($command, true);
    }

    /**
     * Parse command string into array
     */
    protected function parseCommand($commandString)
    {
        preg_match_all('/"(?:\\\\.|[^\\\\"])*"|\S+/', $commandString, $matches);
        return array_map(function ($arg) {
            return trim($arg, '"');
        }, $matches[0]);
    }

    /**
     * Execute Artisan command
     */
    public function executeArtisan($commandString)
    {
        try {
            Artisan::call($commandString);
            $output = Artisan::output();

            return [
                'success' => true,
                'output' => $output,
                'error' => '',
                'exit_code' => 0,
                'command' => $commandString
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'output' => '',
                'error' => $e->getMessage(),
                'exit_code' => 1,
                'command' => $commandString
            ];
        }
    }

    public function status()
    {
        return $this->execute(['status']);
    }

    public function currentBranch()
    {
        return $this->execute(['branch', '--show-current']);
    }

    public function branches()
    {
        return $this->execute(['branch', '-a']);
    }

    public function add($files = '.')
    {
        if (is_array($files)) {
            $command = array_merge(['add'], $files);
        } else {
            $command = ['add', $files];
        }

        return $this->execute($command);
    }

    public function commit($message)
    {
        return $this->execute(['commit', '-m', $message]);
    }

    public function push($remote = 'origin', $branch = null, $force = false)
    {
        if (!$branch) {
            $branchResult = $this->currentBranch();
            $branch = trim($branchResult['output']);
        }

        // First, set the remote URL with credentials
        if ($this->username && $this->token) {
            $remoteUrl = $this->getRemoteUrlWithAuth($remote);
            $this->execute(['remote', 'set-url', $remote, $remoteUrl]);
        }

        $command = ['push', $remote];

        if ($force) {
            $command[] = '--force';
        }

        $command[] = $branch;

        $result = $this->execute($command, true);

        // Reset remote URL to remove credentials from config
        if ($this->username && $this->token) {
            $originalUrl = $this->execute(['remote', 'get-url', $remote]);
            if ($originalUrl['success']) {
                $cleanUrl = preg_replace('/https?:\/\/[^@]+@/', 'https://', trim($originalUrl['output']));
                $this->execute(['remote', 'set-url', $remote, $cleanUrl]);
            }
        }

        return $result;
    }

    public function pull($remote = 'origin', $branch = null)
    {
        if (!$branch) {
            $branchResult = $this->currentBranch();
            $branch = trim($branchResult['output']);
        }

        // Set remote URL with credentials
        if ($this->username && $this->token) {
            $remoteUrl = $this->getRemoteUrlWithAuth($remote);
            $this->execute(['remote', 'set-url', $remote, $remoteUrl]);
        }

        $command = ['pull', $remote, $branch];

        $result = $this->execute($command, true);

        // Reset remote URL
        if ($this->username && $this->token) {
            $originalUrl = $this->execute(['remote', 'get-url', $remote]);
            if ($originalUrl['success']) {
                $cleanUrl = preg_replace('/https?:\/\/[^@]+@/', 'https://', trim($originalUrl['output']));
                $this->execute(['remote', 'set-url', $remote, $cleanUrl]);
            }
        }

        return $result;
    }

    public function fetch($remote = 'origin')
    {
        if ($this->username && $this->token) {
            $remoteUrl = $this->getRemoteUrlWithAuth($remote);
            $this->execute(['remote', 'set-url', $remote, $remoteUrl]);
        }

        $result = $this->execute(['fetch', $remote], true);

        if ($this->username && $this->token) {
            $originalUrl = $this->execute(['remote', 'get-url', $remote]);
            if ($originalUrl['success']) {
                $cleanUrl = preg_replace('/https?:\/\/[^@]+@/', 'https://', trim($originalUrl['output']));
                $this->execute(['remote', 'set-url', $remote, $cleanUrl]);
            }
        }

        return $result;
    }

    public function checkout($branch, $create = false)
    {
        $command = ['checkout'];

        if ($create) {
            $command[] = '-b';
        }

        $command[] = $branch;

        return $this->execute($command);
    }

    public function merge($branch, $message = null)
    {
        $command = ['merge', $branch];

        if ($message) {
            $command[] = '-m';
            $command[] = $message;
        }

        return $this->execute($command);
    }

    public function createBranch($branchName, $checkout = true)
    {
        if ($checkout) {
            return $this->checkout($branchName, true);
        }

        return $this->execute(['branch', $branchName]);
    }

    public function deleteBranch($branchName, $force = false)
    {
        $flag = $force ? '-D' : '-d';
        return $this->execute(['branch', $flag, $branchName]);
    }

    public function log($limit = 10)
    {
        return $this->execute(['log', "--pretty=format:%H|%an|%ae|%ad|%s", "-n", $limit]);
    }

    public function diff($file = null)
    {
        $command = ['diff'];

        if ($file) {
            $command[] = $file;
        }

        return $this->execute($command);
    }

    public function stash($message = null)
    {
        $command = ['stash'];

        if ($message) {
            $command[] = 'push';
            $command[] = '-m';
            $command[] = $message;
        }

        return $this->execute($command);
    }

    public function stashPop()
    {
        return $this->execute(['stash', 'pop']);
    }

    public function stashList()
    {
        return $this->execute(['stash', 'list']);
    }

    public function reset($mode = 'hard', $commit = 'HEAD')
    {
        return $this->execute(['reset', "--$mode", $commit]);
    }

    public function clone($repoUrl, $destination = null)
    {
        $urlWithAuth = $this->addAuthToUrl($repoUrl);

        $command = ['clone', $urlWithAuth];

        if ($destination) {
            $command[] = $destination;
        }

        return $this->execute($command, true);
    }

    protected function getRemoteUrlWithAuth($remote = 'origin')
    {
        $result = $this->execute(['remote', 'get-url', $remote]);

        if (!$result['success']) {
            return $remote;
        }

        $url = trim($result['output']);

        return $this->addAuthToUrl($url);
    }

    protected function addAuthToUrl($url)
    {
        if (!$this->username || !$this->token) {
            return $url;
        }

        // Remove existing credentials if any
        $url = preg_replace('/https?:\/\/[^@]+@/', 'https://', $url);

        if (preg_match('/^(https?:\/\/)(.+)$/', $url, $matches)) {
            $protocol = $matches[1];
            $rest = $matches[2];

            return $protocol . urlencode($this->username) . ':' . urlencode($this->token) . '@' . $rest;
        }

        return $url;
    }

    public function info()
    {
        $status = $this->status();
        $branch = $this->currentBranch();
        $remotes = $this->execute(['remote', '-v']);

        return [
            'status' => $status,
            'current_branch' => $branch,
            'remotes' => $remotes
        ];
    }

    public function hasChanges()
    {
        $result = $this->execute(['status', '--porcelain']);
        return !empty(trim($result['output']));
    }

    public function changedFiles()
    {
        return $this->execute(['status', '--porcelain']);
    }

    public function createTag($tagName, $message = null)
    {
        $command = ['tag'];

        if ($message) {
            $command[] = '-a';
            $command[] = $tagName;
            $command[] = '-m';
            $command[] = $message;
        } else {
            $command[] = $tagName;
        }

        return $this->execute($command);
    }

    public function listTags()
    {
        return $this->execute(['tag', '-l']);
    }

    public function deleteTag($tagName)
    {
        return $this->execute(['tag', '-d', $tagName]);
    }

    public function pushTags($remote = 'origin')
    {
        if ($this->username && $this->token) {
            $remoteUrl = $this->getRemoteUrlWithAuth($remote);
            $this->execute(['remote', 'set-url', $remote, $remoteUrl]);
        }

        $result = $this->execute(['push', $remote, '--tags'], true);

        if ($this->username && $this->token) {
            $originalUrl = $this->execute(['remote', 'get-url', $remote]);
            if ($originalUrl['success']) {
                $cleanUrl = preg_replace('/https?:\/\/[^@]+@/', 'https://', trim($originalUrl['output']));
                $this->execute(['remote', 'set-url', $remote, $cleanUrl]);
            }
        }

        return $result;
    }

    public function configUser($name, $email, $global = false)
    {
        $scope = $global ? '--global' : '--local';

        $nameResult = $this->execute(['config', $scope, 'user.name', $name]);
        $emailResult = $this->execute(['config', $scope, 'user.email', $email]);

        return [
            'success' => $nameResult['success'] && $emailResult['success'],
            'name_result' => $nameResult,
            'email_result' => $emailResult
        ];
    }

    public function getGitUser()
    {
        $name = $this->execute(['config', 'user.name']);
        $email = $this->execute(['config', 'user.email']);

        return [
            'name' => trim($name['output']),
            'email' => trim($email['output'])
        ];
    }
}
