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
                <p class="text-gray-600">Generate and add passes to your Google Wallet</p>
            </div>

            <!-- Pass Configuration Form -->
            <div class="space-y-4 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Card Title</label>
                    <input type="text" id="card_title" value="My Wallet Pass"
                           class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Header</label>
                    <input type="text" id="header" value="Special Offer"
                           class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Subheader</label>
                    <input type="text" id="subheader" value="Valid for 30 days"
                           class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Customer Name</label>
                    <input type="text" id="customer_name" value="John Doe"
                           class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Logo URL (Optional)</label>
                    <input type="url" id="logo" value="" placeholder="https://example.com/logo.png"
                           class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <p class="text-xs text-gray-500 mt-1">Must be a publicly accessible URL (330x100px recommended)</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Background Color</label>
                    <input type="color" id="background_color" value="#4285f4"
                           class="w-full h-10 border border-gray-300 rounded-md cursor-pointer">
                </div>
            </div>

            <!-- Status Messages -->
            <div id="status-message" class="hidden mb-4 p-4 rounded-md"></div>

            <!-- Add to Google Wallet Button Container -->
            <div class="text-center">
                <button id="generate-pass-btn"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-8 rounded-md transition duration-200 mb-4">
                    Generate Pass
                </button>

                <div id="wallet-button-container" class="flex justify-center"></div>

                <p class="text-sm text-gray-500 mt-4">Click "Generate Pass" to create your Google Wallet pass</p>
            </div>

            <!-- Info Section -->
            <div class="mt-8 p-4 bg-blue-50 rounded-md">
                <h3 class="font-semibold text-blue-900 mb-2">How it works:</h3>
                <ol class="list-decimal list-inside text-sm text-blue-800 space-y-1">
                    <li>Customize your pass details above</li>
                    <li>Click "Generate Pass" to create a unique wallet pass</li>
                    <li>Click the "Add to Google Wallet" button that appears</li>
                    <li>Sign in with your Google account if prompted</li>
                    <li>The pass will be added to your Google Wallet app</li>
                </ol>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const generateBtn = document.getElementById('generate-pass-btn');
            const statusMessage = document.getElementById('status-message');
            const walletButtonContainer = document.getElementById('wallet-button-container');

            generateBtn.addEventListener('click', async function() {
                // Show loading state
                generateBtn.disabled = true;
                generateBtn.textContent = 'Generating...';
                showStatus('Generating your pass...', 'info');
                walletButtonContainer.innerHTML = '';

                try {
                    // Gather form data
                    const formData = {
                        card_title: document.getElementById('card_title').value,
                        header: document.getElementById('header').value,
                        subheader: document.getElementById('subheader').value,
                        customer_name: document.getElementById('customer_name').value,
                        background_color: document.getElementById('background_color').value,
                        barcode_value: 'PASS-' + Date.now()
                    };

                    // Only add logo if provided
                    const logoUrl = document.getElementById('logo').value.trim();
                    if (logoUrl) {
                        formData.logo = logoUrl;
                    }

                    // Call API to generate pass
                    const response = await fetch('{{ route('wallet.generate') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify(formData)
                    });

                    const data = await response.json();

                    if (data.success) {
                        showStatus('Pass generated successfully! Click the button below to add it to Google Wallet.', 'success');
                        createWalletButton(data.jwt);
                    } else {
                        showStatus('Error: ' + data.message, 'error');
                    }
                } catch (error) {
                    showStatus('Error: ' + error.message, 'error');
                } finally {
                    generateBtn.disabled = false;
                    generateBtn.textContent = 'Generate Pass';
                }
            });

            function showStatus(message, type) {
                statusMessage.className = 'mb-4 p-4 rounded-md ';

                if (type === 'success') {
                    statusMessage.className += 'bg-green-100 text-green-800 border border-green-200';
                } else if (type === 'error') {
                    statusMessage.className += 'bg-red-100 text-red-800 border border-red-200';
                } else {
                    statusMessage.className += 'bg-blue-100 text-blue-800 border border-blue-200';
                }

                statusMessage.textContent = message;
                statusMessage.classList.remove('hidden');
            }

            function createWalletButton(jwt) {
                // Create the Add to Google Wallet button
                const saveUrl = `https://pay.google.com/gp/v/save/${jwt}`;

                const link = document.createElement('a');
                link.href = saveUrl;
                link.target = '_blank';
                link.rel = 'noopener noreferrer';

                const img = document.createElement('img');
                img.src = 'https://pay.google.com/gp/p/generate_logo?origin={{ config('app.url') }}&type=save_to_wallet&locale=en';
                img.alt = 'Add to Google Wallet';
                img.style.height = '48px';

                link.appendChild(img);
                walletButtonContainer.innerHTML = '';
                walletButtonContainer.appendChild(link);
            }
        });
    </script>
</body>
</html>

