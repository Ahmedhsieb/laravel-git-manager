<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Git Manager - Setup Required</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-3xl w-full">
        <div class="bg-white rounded-2xl shadow-2xl overflow-hidden">
            <!-- Header -->
            <div class="bg-gradient-to-r from-yellow-500 to-orange-500 p-6 text-white">
                <div class="flex items-center">
                    <div class="p-4 bg-white bg-opacity-20 rounded-full mr-4">
                        <i class="fas fa-exclamation-triangle text-4xl"></i>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold">Setup Required</h1>
                        <p class="text-yellow-100">Git Manager needs to be configured</p>
                    </div>
                </div>
            </div>

            <!-- Body -->
            <div class="p-8">
                <!-- Alert -->
                <div class="bg-yellow-50 border-l-4 border-yellow-500 p-6 mb-8 rounded-r-lg">
                    <div class="flex items-start">
                        <i class="fas fa-info-circle text-yellow-500 text-2xl mr-4 mt-1"></i>
                        <div>
                            <h3 class="font-bold text-yellow-800 text-lg mb-2">Configuration Not Found</h3>
                            <p class="text-yellow-700">
                                Git Manager has not been set up yet. Please run the setup wizard to configure the
                                package before you can use it.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Setup Command -->
                <div class="mb-8">
                    <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-terminal text-blue-600 mr-2"></i>
                        Run Setup Command
                    </h3>
                    <div class="bg-gray-900 text-green-400 p-6 rounded-lg font-mono text-lg shadow-inner">
                        <div class="flex items-center justify-between">
                            <span>php artisan git-manager:setup</span>
                            <button onclick="copyCommand()"
                                class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded text-sm transition-colors">
                                <i class="fas fa-copy mr-2"></i>Copy
                            </button>
                        </div>
                    </div>
                </div>

                <!-- What Will Be Configured -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-8">
                    <h3 class="text-lg font-bold text-blue-900 mb-4 flex items-center">
                        <i class="fas fa-cog text-blue-600 mr-2"></i>
                        The setup wizard will configure:
                    </h3>
                    <ul class="space-y-3">
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-green-600 mr-3 mt-1"></i>
                            <div>
                                <strong class="text-gray-800">Git Authentication</strong>
                                <p class="text-sm text-gray-600">Username and personal access token for push/pull
                                    operations</p>
                            </div>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-green-600 mr-3 mt-1"></i>
                            <div>
                                <strong class="text-gray-800">Git User Identity</strong>
                                <p class="text-sm text-gray-600">Name and email that will appear in your commits</p>
                            </div>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-green-600 mr-3 mt-1"></i>
                            <div>
                                <strong class="text-gray-800">Access Password</strong>
                                <p class="text-sm text-gray-600">Secure password to protect the Git Manager interface
                                </p>
                            </div>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-green-600 mr-3 mt-1"></i>
                            <div>
                                <strong class="text-gray-800">Global Git Configuration</strong>
                                <p class="text-sm text-gray-600">Optional: Configure Git globally on your server</p>
                            </div>
                        </li>
                    </ul>
                </div>

                <!-- Need Help? -->
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-6">
                    <h3 class="font-bold text-gray-800 mb-3 flex items-center">
                        <i class="fas fa-life-ring text-gray-600 mr-2"></i>
                        Need Help?
                    </h3>
                    <div class="space-y-2 text-sm text-gray-600">
                        <p>
                            <strong>GitHub Token:</strong> Generate at
                            <a href="https://github.com/settings/tokens" target="_blank"
                                class="text-blue-600 hover:underline">
                                GitHub Settings → Tokens
                            </a>
                        </p>
                        <p>
                            <strong>GitLab Token:</strong> Generate at
                            <a href="https://gitlab.com/-/profile/personal_access_tokens" target="_blank"
                                class="text-blue-600 hover:underline">
                                GitLab → Access Tokens
                            </a>
                        </p>
                        <p>
                            <strong>Documentation:</strong> Visit our
                            <a href="https://github.com/ahmedhsieb/laravel-git-manager" target="_blank"
                                class="text-blue-600 hover:underline">
                                GitHub Repository
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function copyCommand() {
            navigator.clipboard.writeText('php artisan git-manager:setup').then(() => {
                alert('Command copied to clipboard!');
            });
        }
    </script>
</body>

</html>