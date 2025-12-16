@extends('git-manager::layouts.app')

@section('page-title', 'Git Manager Dashboard')
@section('page-description', 'Manage your repository with ease')

@section('content')
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column - Main Actions -->
        <div class="lg:col-span-2 space-y-6">

            <!-- Status Card -->
            <div class="card">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold text-gray-800 flex items-center">
                        <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                        Repository Status
                    </h3>
                    <button onclick="loadStatus()" class="text-blue-600 hover:text-blue-800">
                        <i class="fas fa-sync-alt"></i>
                    </button>
                </div>

                <div class="grid grid-cols-3 gap-4 text-sm">
                    <div>
                        <span class="text-gray-600">Current Branch:</span>
                        <div id="current-branch" class="font-mono text-blue-600 font-bold mt-1">--</div>
                    </div>
                    <div>
                        <span class="text-gray-600">Status:</span>
                        <div id="repo-status" class="font-bold mt-1">--</div>
                    </div>
                    <div>
                        <span class="text-gray-600">Git User:</span>
                        <div id="git-user" class="font-mono text-sm mt-1">--</div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card">
                <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-bolt text-yellow-600 mr-2"></i>
                    Quick Actions
                </h3>

                <div class="space-y-4">
                    <!-- Add & Stash -->
                    <div class="flex gap-2">
                        <button onclick="gitAdd()" class="btn btn-primary flex-1 flex items-center justify-center">
                            <i class="fas fa-plus mr-2"></i> Add All Files
                        </button>
                        <button onclick="gitStash()" class="btn btn-warning flex items-center">
                            <i class="fas fa-box mr-2"></i> Stash
                        </button>
                        <button onclick="gitStashPop()" class="btn btn-warning flex items-center">
                            <i class="fas fa-box-open mr-2"></i> Pop
                        </button>
                    </div>

                    <!-- Commit -->
                    <div class="flex gap-2">
                        <input type="text" id="commit-message"
                            class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500"
                            placeholder="Commit message..." onkeypress="if(event.key==='Enter') gitCommit()">
                        <button onclick="gitCommit()" class="btn btn-success flex items-center">
                            <i class="fas fa-check mr-2"></i> Commit
                        </button>
                    </div>

                    <!-- Push/Pull -->
                    <div class="grid grid-cols-3 gap-2">
                        <button onclick="gitPull()" class="btn btn-primary flex items-center justify-center">
                            <i class="fas fa-download mr-2"></i> Pull
                        </button>
                        <button onclick="gitPush()" class="btn btn-danger flex items-center justify-center">
                            <i class="fas fa-upload mr-2"></i> Push
                        </button>
                        <button onclick="gitFetch()" class="btn btn-dark flex items-center justify-center">
                            <i class="fas fa-cloud-download-alt mr-2"></i> Fetch
                        </button>
                    </div>

                    <!-- Full Push -->
                    <button onclick="gitFullPush()"
                        class="w-full btn bg-gradient-to-r from-green-600 to-blue-600 text-white hover:from-green-700 hover:to-blue-700 flex items-center justify-center">
                        <i class="fas fa-rocket mr-2"></i> Add + Commit + Push
                    </button>
                </div>
            </div>

            <!-- Custom Commands -->
            <div class="card">
                <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-terminal text-green-600 mr-2"></i>
                    Custom Commands
                </h3>

                <div class="space-y-4">
                    <!-- Git Command -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Execute Git Command</label>
                        <div class="flex gap-2">
                            <span class="px-3 py-2 bg-gray-800 text-white rounded-l-lg font-mono text-sm">git</span>
                            <input type="text" id="custom-git-command"
                                class="flex-1 px-4 py-2 border border-gray-300 focus:outline-none focus:border-blue-500 font-mono text-sm"
                                placeholder="status -s" onkeypress="if(event.key==='Enter') executeCustomGit()">
                            <button onclick="executeCustomGit()" class="btn btn-primary">
                                <i class="fas fa-play"></i> Run
                            </button>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Examples: <code>status -s</code>,
                            <code>log --oneline -5</code>, <code>branch -a</code></p>
                    </div>

                    <!-- Artisan Command -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Execute Artisan Command</label>
                        <div class="flex gap-2">
                            <span class="px-3 py-2 bg-green-600 text-white rounded-l-lg font-mono text-sm">artisan</span>
                            <input type="text" id="custom-artisan-command"
                                class="flex-1 px-4 py-2 border border-gray-300 focus:outline-none focus:border-green-500 font-mono text-sm"
                                placeholder="cache:clear" onkeypress="if(event.key==='Enter') executeCustomArtisan()">
                            <button onclick="executeCustomArtisan()" class="btn btn-success">
                                <i class="fas fa-play"></i> Run
                            </button>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Examples: <code>cache:clear</code>,
                            <code>migrate:status</code>, <code>config:clear</code></p>
                    </div>
                </div>
            </div>

            <!-- Branch Management -->
            <div class="card">
                <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-code-branch text-purple-600 mr-2"></i>
                    Branch Management
                </h3>

                <div class="space-y-4">
                    <!-- Create Branch -->
                    <div class="flex gap-2">
                        <input type="text" id="new-branch-name"
                            class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500"
                            placeholder="feature/new-feature">
                        <button onclick="createBranch()" class="btn btn-primary flex items-center">
                            <i class="fas fa-plus mr-2"></i> Create & Checkout
                        </button>
                    </div>

                    <!-- Merge Branch -->
                    <div class="flex gap-2">
                        <input type="text" id="merge-branch-name"
                            class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-orange-500"
                            placeholder="Branch to merge">
                        <button onclick="mergeBranch()" class="btn btn-warning flex items-center">
                            <i class="fas fa-code-branch mr-2"></i> Merge
                        </button>
                    </div>

                    <!-- Danger Zone -->
                    <div class="border-t pt-4">
                        <h4 class="text-sm font-semibold text-red-600 mb-2">Danger Zone</h4>
                        <button onclick="resetHard()" class="btn btn-danger flex items-center">
                            <i class="fas fa-exclamation-triangle mr-2"></i> Reset Hard (Discard All Changes)
                        </button>
                    </div>
                </div>
            </div>

        </div>

        <!-- Right Column - Branches & Output -->
        <div class="space-y-6">

            <!-- Branches List -->
            <div class="card">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold text-gray-800 flex items-center">
                        <i class="fas fa-code-branch text-blue-600 mr-2"></i>
                        Branches
                    </h3>
                    <button onclick="loadBranches()" class="text-blue-600 hover:text-blue-800">
                        <i class="fas fa-sync-alt"></i>
                    </button>
                </div>
                <div id="branches-list" class="space-y-2 max-h-64 overflow-y-auto">
                    <div class="text-center text-gray-400 py-4">
                        <i class="fas fa-spinner fa-spin"></i> Loading...
                    </div>
                </div>
            </div>

            <!-- Output Console -->
            <div class="card">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold text-gray-800 flex items-center">
                        <i class="fas fa-terminal text-green-600 mr-2"></i>
                        Console Output
                    </h3>
                    <button onclick="clearOutput()" class="text-red-600 hover:text-red-800">
                        <i class="fas fa-trash"></i> Clear
                    </button>
                </div>
                <div id="git-output" class="console h-96 overflow-y-auto">
                    <div class="text-gray-500 text-center py-20">
                        <i class="fas fa-terminal text-4xl mb-4 opacity-50"></i>
                        <p>No output yet. Run a command to see results.</p>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const outputBox = document.getElementById('git-output');

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function () {
            loadStatus();
            loadBranches();
        });

        function logOutput(message, type = 'info') {
            const colors = {
                success: 'text-green-400',
                error: 'text-red-400',
                info: 'text-blue-400',
                warning: 'text-yellow-400',
                output: 'text-gray-300'
            };

            const icons = {
                success: '✓',
                error: '✗',
                info: 'ℹ',
                warning: '⚠',
                output: '→'
            };

            // Clear placeholder on first log
            if (outputBox.querySelector('.text-center')) {
                outputBox.innerHTML = '';
            }

            const time = new Date().toLocaleTimeString();
            outputBox.innerHTML += `
            <div class="${colors[type] || 'text-gray-300'} mb-1">
                <span class="text-gray-600">[${time}]</span> ${icons[type] || ''} ${escapeHtml(message)}
            </div>
        `;

            outputBox.scrollTop = outputBox.scrollHeight;
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        function clearOutput() {
            outputBox.innerHTML = '<div class="text-gray-500">Console cleared.</div>';
        }

        async function apiCall(endpoint, method = 'GET', data = null) {
            try {
                logOutput(`Executing ${method} ${endpoint}...`, 'info');

                const options = {
                    method,
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                };

                if (data && method !== 'GET') {
                    options.body = JSON.stringify(data);
                }

                const response = await fetch(endpoint, options);
                const result = await response.json();

                if (result.success) {
                    logOutput(result.message || 'Success', 'success');
                    if (result.output && result.output.trim()) {
                        result.output.split('\n').forEach(line => {
                            if (line.trim()) logOutput(line, 'output');
                        });
                    }
                } else {
                    logOutput(result.message || 'Failed', 'error');
                    if (result.error && result.error.trim()) {
                        result.error.split('\n').forEach(line => {
                            if (line.trim()) logOutput(line, 'error');
                        });
                    }
                }

                return result;
            } catch (error) {
                logOutput(`Error: ${error.message}`, 'error');
                return { success: false, error: error.message };
            }
        }

        async function loadStatus() {
            const result = await apiCall('{{ route("git-manager.status") }}', 'GET');
            if (result.success) {
                document.getElementById('current-branch').textContent = result.info.current_branch.output.trim() || 'Unknown';
                document.getElementById('sidebar-branch').textContent = result.info.current_branch.output.trim() || 'Unknown';

                const statusEl = document.getElementById('repo-status');
                const sidebarStatusEl = document.getElementById('sidebar-status');

                if (result.has_changes) {
                    statusEl.textContent = 'Modified';
                    statusEl.className = 'font-bold mt-1 text-yellow-600';
                    sidebarStatusEl.textContent = 'Modified';
                    sidebarStatusEl.className = 'text-yellow-400';
                } else {
                    statusEl.textContent = 'Clean';
                    statusEl.className = 'font-bold mt-1 text-green-600';
                    sidebarStatusEl.textContent = 'Clean';
                    sidebarStatusEl.className = 'text-green-400';
                }

                if (result.git_user) {
                    document.getElementById('git-user').textContent = result.git_user.name || 'Not configured';
                }
            }
        }

        async function loadBranches() {
            const result = await apiCall('{{ route("git-manager.branches") }}', 'GET');
            const branchesList = document.getElementById('branches-list');

            if (result.success && result.output) {
                const branches = result.output.split('\n').filter(b => b.trim());
                branchesList.innerHTML = '';

                branches.forEach(branch => {
                    const isCurrent = branch.includes('*');
                    const cleanBranch = branch.replace('*', '').trim();

                    const branchEl = document.createElement('button');
                    branchEl.className = isCurrent
                        ? 'w-full text-left px-3 py-2 bg-blue-600 text-white rounded-lg font-mono text-sm'
                        : 'w-full text-left px-3 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg font-mono text-sm transition-colors';
                    branchEl.textContent = cleanBranch;

                    if (!isCurrent) {
                        branchEl.onclick = () => checkoutBranch(cleanBranch);
                    }

                    branchesList.appendChild(branchEl);
                });
            }
        }

        async function checkoutBranch(branch) {
            if (confirm(`Switch to branch: ${branch}?`)) {
                const result = await apiCall('{{ route("git-manager.checkout") }}', 'POST', { branch });
                if (result.success) {
                    loadStatus();
                    loadBranches();
                }
            }
        }

        function gitAdd() {
            apiCall('{{ route("git-manager.add") }}', 'POST', { files: '.' }).then(() => loadStatus());
        }

        function gitCommit() {
            const msg = document.getElementById('commit-message').value;
            if (!msg) {
                logOutput('Commit message is required', 'error');
                return;
            }
            apiCall('{{ route("git-manager.commit") }}', 'POST', { message: msg }).then((result) => {
                if (result.success) {
                    document.getElementById('commit-message').value = '';
                    loadStatus();
                }
            });
        }

        function gitPull() {
            apiCall('{{ route("git-manager.pull") }}', 'POST').then(() => loadStatus());
        }

        function gitPush() {
            apiCall('{{ route("git-manager.push") }}', 'POST').then(() => loadStatus());
        }

        function gitFetch() {
            apiCall('{{ route("git-manager.fetch") }}', 'POST');
        }

        function gitStash() {
            apiCall('{{ route("git-manager.stash") }}', 'POST').then(() => loadStatus());
        }

        function gitStashPop() {
            apiCall('{{ route("git-manager.stash.pop") }}', 'POST').then(() => loadStatus());
        }

        function gitFullPush() {
            const msg = document.getElementById('commit-message').value;
            if (!msg) {
                logOutput('Commit message is required', 'error');
                return;
            }
            apiCall('{{ route("git-manager.full-push") }}', 'POST', { message: msg }).then((result) => {
                if (result.success) {
                    document.getElementById('commit-message').value = '';
                    loadStatus();
                }

                if (result.steps) {
                    result.steps.forEach(step => {
                        const icon = step.success ? '✓' : '✗';
                        const type = step.success ? 'success' : 'error';
                        logOutput(`${icon} ${step.step.toUpperCase()}`, type);
                    });
                }
            });
        }

        function createBranch() {
            const name = document.getElementById('new-branch-name').value;
            if (!name) {
                logOutput('Branch name is required', 'error');
                return;
            }
            apiCall('{{ route("git-manager.branch.create") }}', 'POST', { name, checkout: true }).then((result) => {
                if (result.success) {
                    document.getElementById('new-branch-name').value = '';
                    loadStatus();
                    loadBranches();
                }
            });
        }

        function mergeBranch() {
            const branch = document.getElementById('merge-branch-name').value;
            if (!branch) {
                logOutput('Branch name is required', 'error');
                return;
            }
            if (confirm(`Merge branch "${branch}" into current branch?`)) {
                apiCall('{{ route("git-manager.merge") }}', 'POST', { branch }).then((result) => {
                    if (result.success) {
                        document.getElementById('merge-branch-name').value = '';
                        loadStatus();
                    }
                });
            }
        }

        function resetHard() {
            if (confirm('⚠️ WARNING: This will discard ALL local changes! Are you absolutely sure?')) {
                if (confirm('Last chance! This action CANNOT be undone. Continue?')) {
                    apiCall('{{ route("git-manager.reset") }}', 'POST', { mode: 'hard' }).then(() => loadStatus());
                }
            }
        }

        function executeCustomGit() {
            const command = document.getElementById('custom-git-command').value;
            if (!command) {
                logOutput('Command is required', 'error');
                return;
            }

            logOutput(`$ git ${command}`, 'info');
            apiCall('{{ route("git-manager.custom") }}', 'POST', { command }).then(() => {
                loadStatus();
            });
        }

        function executeCustomArtisan() {
            const command = document.getElementById('custom-artisan-command').value;
            if (!command) {
                logOutput('Command is required', 'error');
                return;
            }

            logOutput(`$ php artisan ${command}`, 'info');
            apiCall('{{ route("git-manager.artisan") }}', 'POST', { command });
        }

        // Keyboard shortcuts
        document.addEventListener('keydown', function (e) {
            // Ctrl/Cmd + Enter to commit
            if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') {
                const commitInput = document.getElementById('commit-message');
                if (document.activeElement === commitInput) {
                    gitCommit();
                }
            }
        });
    </script>
@endpush