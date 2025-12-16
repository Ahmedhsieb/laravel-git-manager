<?php

namespace Ahmedhsieb\GitManager\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use Ahmedhsieb\GitManager\Services\GitManager;
use Ahmedhsieb\GitManager\Http\Middleware\GitManagerAuth;

class GitManagerController extends Controller
{
    protected $git;

    public function __construct()
    {
        $this->git = new GitManager();
    }

    /**
     * Show main dashboard
     */
    public function index()
    {
        return view('git-manager::layouts.manager');
    }

    /**
     * Show login page
     */
    public function showLogin()
    {
        return view('git-manager::auth.login');
    }

    /**
     * Authenticate user
     */
    public function authenticate(Request $request)
    {
        $password = $request->input('password');

        if (GitManagerAuth::authenticate($password)) {
            return redirect()->route('git-manager.index');
        }

        return back()->with('error', 'Invalid password. Please try again.');
    }

    /**
     * Logout user
     */
    public function logout()
    {
        GitManagerAuth::logoutUser();
        return redirect()->route('git-manager.login')->with('success', 'Logged out successfully');
    }

    /**
     * Get repository status and info
     */
    public function status()
    {
        $info = $this->git->info();
        $hasChanges = $this->git->hasChanges();
        $changedFiles = $this->git->changedFiles();
        $gitUser = $this->git->getGitUser();

        return response()->json([
            'success' => true,
            'has_changes' => $hasChanges,
            'info' => $info,
            'changed_files' => $changedFiles,
            'git_user' => $gitUser
        ]);
    }

    /**
     * Get current branch
     */
    public function currentBranch()
    {
        $result = $this->git->currentBranch();

        return response()->json([
            'success' => $result['success'],
            'branch' => trim($result['output']),
            'output' => $result['output'],
            'error' => $result['error']
        ]);
    }

    /**
     * List all branches
     */
    public function branches()
    {
        $result = $this->git->branches();

        return response()->json([
            'success' => $result['success'],
            'output' => $result['output'],
            'error' => $result['error']
        ]);
    }

    /**
     * Add files to staging
     */
    public function add(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'files' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $files = $request->input('files', '.');
        $result = $this->git->add($files);

        return response()->json([
            'success' => $result['success'],
            'output' => $result['output'],
            'error' => $result['error'],
            'message' => $result['success'] ? 'Files added to staging' : 'Failed to add files'
        ]);
    }

    /**
     * Commit changes
     */
    public function commit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'message' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $result = $this->git->commit($request->message);

        return response()->json([
            'success' => $result['success'],
            'output' => $result['output'],
            'error' => $result['error'],
            'message' => $result['success'] ? 'Changes committed successfully' : 'Failed to commit changes'
        ]);
    }

    /**
     * Push to remote
     */
    public function push(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'remote' => 'nullable|string',
            'branch' => 'nullable|string',
            'force' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $remote = $request->input('remote', 'origin');
        $branch = $request->input('branch');
        $force = $request->input('force', false);

        $result = $this->git->push($remote, $branch, $force);

        return response()->json([
            'success' => $result['success'],
            'output' => $result['output'],
            'error' => $result['error'],
            'message' => $result['success'] ? 'Pushed successfully' : 'Failed to push'
        ]);
    }

    /**
     * Pull from remote
     */
    public function pull(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'remote' => 'nullable|string',
            'branch' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $remote = $request->input('remote', 'origin');
        $branch = $request->input('branch');

        $result = $this->git->pull($remote, $branch);

        return response()->json([
            'success' => $result['success'],
            'output' => $result['output'],
            'error' => $result['error'],
            'message' => $result['success'] ? 'Pulled successfully' : 'Failed to pull'
        ]);
    }

    /**
     * Fetch from remote
     */
    public function fetch(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'remote' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $remote = $request->input('remote', 'origin');
        $result = $this->git->fetch($remote);

        return response()->json([
            'success' => $result['success'],
            'output' => $result['output'],
            'error' => $result['error'],
            'message' => $result['success'] ? 'Fetched successfully' : 'Failed to fetch'
        ]);
    }

    /**
     * Checkout branch
     */
    public function checkout(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'branch' => 'required|string',
            'create' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $create = $request->input('create', false);
        $result = $this->git->checkout($request->branch, $create);

        return response()->json([
            'success' => $result['success'],
            'output' => $result['output'],
            'error' => $result['error'],
            'message' => $result['success'] ? "Checked out to {$request->branch}" : 'Failed to checkout'
        ]);
    }

    /**
     * Merge branch
     */
    public function merge(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'branch' => 'required|string',
            'message' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $message = $request->input('message');
        $result = $this->git->merge($request->branch, $message);

        return response()->json([
            'success' => $result['success'],
            'output' => $result['output'],
            'error' => $result['error'],
            'message' => $result['success'] ? "Merged {$request->branch} successfully" : 'Failed to merge'
        ]);
    }

    /**
     * Create new branch
     */
    public function createBranch(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'checkout' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $checkout = $request->input('checkout', true);
        $result = $this->git->createBranch($request->name, $checkout);

        return response()->json([
            'success' => $result['success'],
            'output' => $result['output'],
            'error' => $result['error'],
            'message' => $result['success'] ? "Branch {$request->name} created" : 'Failed to create branch'
        ]);
    }

    /**
     * Delete branch
     */
    public function deleteBranch(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'force' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $force = $request->input('force', false);
        $result = $this->git->deleteBranch($request->name, $force);

        return response()->json([
            'success' => $result['success'],
            'output' => $result['output'],
            'error' => $result['error'],
            'message' => $result['success'] ? "Branch {$request->name} deleted" : 'Failed to delete branch'
        ]);
    }

    /**
     * Get commit log
     */
    public function log(Request $request)
    {
        $limit = $request->input('limit', 10);
        $result = $this->git->log($limit);

        return response()->json([
            'success' => $result['success'],
            'output' => $result['output'],
            'error' => $result['error']
        ]);
    }

    /**
     * Get diff
     */
    public function diff(Request $request)
    {
        $file = $request->input('file');
        $result = $this->git->diff($file);

        return response()->json([
            'success' => $result['success'],
            'output' => $result['output'],
            'error' => $result['error']
        ]);
    }

    /**
     * Stash changes
     */
    public function stash(Request $request)
    {
        $message = $request->input('message');
        $result = $this->git->stash($message);

        return response()->json([
            'success' => $result['success'],
            'output' => $result['output'],
            'error' => $result['error'],
            'message' => $result['success'] ? 'Changes stashed' : 'Failed to stash'
        ]);
    }

    /**
     * Apply stash
     */
    public function stashPop()
    {
        $result = $this->git->stashPop();

        return response()->json([
            'success' => $result['success'],
            'output' => $result['output'],
            'error' => $result['error'],
            'message' => $result['success'] ? 'Stash applied' : 'Failed to apply stash'
        ]);
    }

    /**
     * List stashes
     */
    public function stashList()
    {
        $result = $this->git->stashList();

        return response()->json([
            'success' => $result['success'],
            'output' => $result['output'],
            'error' => $result['error']
        ]);
    }

    /**
     * Reset changes
     */
    public function reset(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mode' => 'nullable|in:soft,mixed,hard',
            'commit' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $mode = $request->input('mode', 'hard');
        $commit = $request->input('commit', 'HEAD');

        $result = $this->git->reset($mode, $commit);

        return response()->json([
            'success' => $result['success'],
            'output' => $result['output'],
            'error' => $result['error'],
            'message' => $result['success'] ? 'Reset successfully' : 'Failed to reset'
        ]);
    }

    /**
     * Full workflow: Add, Commit, Push
     */
    public function fullPush(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'message' => 'required|string',
            'files' => 'nullable|string',
            'remote' => 'nullable|string',
            'branch' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $steps = [];

        $files = $request->input('files', '.');
        $addResult = $this->git->add($files);
        $steps[] = [
            'step' => 'add',
            'success' => $addResult['success'],
            'output' => $addResult['output'],
            'error' => $addResult['error']
        ];

        if (!$addResult['success']) {
            return response()->json([
                'success' => false,
                'steps' => $steps,
                'message' => 'Failed at add stage'
            ]);
        }

        $commitResult = $this->git->commit($request->message);
        $steps[] = [
            'step' => 'commit',
            'success' => $commitResult['success'],
            'output' => $commitResult['output'],
            'error' => $commitResult['error']
        ];

        if (!$commitResult['success']) {
            return response()->json([
                'success' => false,
                'steps' => $steps,
                'message' => 'Failed at commit stage'
            ]);
        }

        $remote = $request->input('remote', 'origin');
        $branch = $request->input('branch');
        $pushResult = $this->git->push($remote, $branch);
        $steps[] = [
            'step' => 'push',
            'success' => $pushResult['success'],
            'output' => $pushResult['output'],
            'error' => $pushResult['error']
        ];

        return response()->json([
            'success' => $pushResult['success'],
            'steps' => $steps,
            'message' => $pushResult['success'] ? 'All steps completed successfully' : 'Failed at push stage'
        ]);
    }

    /**
     * Configure git user
     */
    public function configUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|email',
            'global' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $global = $request->input('global', false);
        $result = $this->git->configUser($request->name, $request->email, $global);

        return response()->json([
            'success' => $result['success'],
            'message' => $result['success'] ? 'Git user configured successfully' : 'Failed to configure git user',
            'details' => $result
        ]);
    }

    /**
     * Get current git user
     */
    public function getGitUser()
    {
        $user = $this->git->getGitUser();

        return response()->json([
            'success' => true,
            'user' => $user
        ]);
    }

    /**
     * Execute custom git command
     */
    public function executeCustom(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'command' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $result = $this->git->executeCustom($request->command);

        return response()->json([
            'success' => $result['success'],
            'output' => $result['output'],
            'error' => $result['error'],
            'command' => $result['command'],
            'message' => $result['success'] ? 'Command executed successfully' : 'Command failed'
        ]);
    }

    /**
     * Execute artisan command
     */
    public function executeArtisan(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'command' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $result = $this->git->executeArtisan($request->command);

        return response()->json([
            'success' => $result['success'],
            'output' => $result['output'],
            'error' => $result['error'],
            'command' => $result['command'],
            'message' => $result['success'] ? 'Command executed successfully' : 'Command failed'
        ]);
    }
}
