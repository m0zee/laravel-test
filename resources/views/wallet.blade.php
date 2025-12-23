<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Google Wallet Integration - {{ config('app.name') }}</title>
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center p-4">
        <div class="max-w-2xl w-full bg-white rounded-lg shadow-lg p-8">
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-gray-800 mb-2">Google Wallet Pass Generator</h1>
                <p class="text-gray-600">One-click pass generation and wallet integration</p>
            </div>

            @if(session('error'))
            <div class="mb-4 p-4 bg-red-100 text-red-800 border border-red-200 rounded-md">
                {{ session('error') }}
            </div>
            @endif

            <!-- Pass Configuration Form -->
            <form action="{{ route('wallet.add') }}" method="POST" class="space-y-4 mb-6">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Card Title</label>
                    <input type="text" name="card_title" value="My Wallet Pass"
                           class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Header</label>
                    <input type="text" name="header" value="Special Offer"
                           class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Subheader</label>
                    <input type="text" name="subheader" value="Valid for 30 days"
                           class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Customer Name</label>
                    <input type="text" name="customer_name" value="John Doe"
                           class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Logo URL (Optional)</label>
                    <input type="url" name="logo" value="" placeholder="https://example.com/logo.png"
                           class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <p class="text-xs text-gray-500 mt-1">Must be a publicly accessible URL (330x100px recommended)</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Background Color</label>
                    <input type="color" name="background_color" value="#4285f4"
                           class="w-full h-10 border border-gray-300 rounded-md cursor-pointer">
                </div>

                <!-- Hidden field for barcode -->
                <input type="hidden" name="barcode_value" value="PASS-{{ time() }}">

                <!-- One-Click Add to Google Wallet Button -->
                <div class="text-center pt-4">
                    <button type="submit"
                            class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-8 rounded-md transition duration-200 shadow-md hover:shadow-lg">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M21,18V19A2,2 0 0,1 19,21H5C3.89,21 3,20.1 3,19V5A2,2 0 0,1 5,3H19A2,2 0 0,1 21,5V6H12C10.89,6 10,6.9 10,8V16A2,2 0 0,0 12,18M12,16H22V8H12M16,13.5A1.5,1.5 0 0,1 14.5,12A1.5,1.5 0 0,1 16,10.5A1.5,1.5 0 0,1 17.5,12A1.5,1.5 0 0,1 16,13.5Z" />
                        </svg>
                        Add to Google Wallet
                    </button>
                    <p class="text-sm text-gray-500 mt-4">Click to generate pass and add to Google Wallet instantly</p>
                </div>
            </form>

            <!-- Info Section -->
            <div class="mt-8 p-4 bg-blue-50 rounded-md">
                <h3 class="font-semibold text-blue-900 mb-2">How it works:</h3>
                <ol class="list-decimal list-inside text-sm text-blue-800 space-y-1">
                    <li>Customize your pass details above (or use defaults)</li>
                    <li>Click "Add to Google Wallet" button</li>
                    <li>Pass is generated instantly and you're redirected to Google Wallet</li>
                    <li>Sign in with your Google account if prompted</li>
                    <li>The pass will be added to your Google Wallet app</li>
                </ol>
            </div>

            <!-- Advanced Section (Collapsible) -->
            <div class="mt-6">
                <button onclick="toggleAdvanced()" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                    ▼ Advanced: API Integration
                </button>
                <div id="advanced-section" class="hidden mt-4 p-4 bg-gray-50 rounded-md">
                    <h4 class="font-semibold text-gray-900 mb-2">API Endpoints:</h4>
                    <div class="text-sm text-gray-700 space-y-2 font-mono">
                        <div>
                            <span class="font-bold">POST</span> /wallet/add
                            <p class="text-xs text-gray-600 ml-4">→ Generate & redirect to Google Wallet (one-click)</p>
                        </div>
                        <div>
                            <span class="font-bold">POST</span> /wallet/generate
                            <p class="text-xs text-gray-600 ml-4">→ Generate pass and return JWT (API only)</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleAdvanced() {
            const section = document.getElementById('advanced-section');
            section.classList.toggle('hidden');
        }
    </script>
</body>
</html>

