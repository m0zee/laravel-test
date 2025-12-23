<?php

namespace App\Http\Controllers;

use App\Services\GoogleWalletService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __construct(
        private GoogleWalletService $walletService
    ) {}

    /**
     * Display the Google Wallet page
     */
    public function index(): View
    {
        return view('wallet');
    }

    /**
     * Generate a Google Wallet pass and return JWT
     */
    public function generatePass(Request $request): JsonResponse
    {
        try {
            // Ensure pass class exists
            if (!$this->walletService->passClassExists()) {
                $this->walletService->createPassClass();
            }

            // Get custom data from request or use defaults
            $data = [
                'card_title' => $request->input('card_title', 'My Wallet Pass'),
                'header' => $request->input('header', 'Special Offer'),
                'subheader' => $request->input('subheader', 'Valid for 30 days'),
                'background_color' => $request->input('background_color', '#4285f4'),
                'barcode_value' => $request->input('barcode_value', 'PASS-' . uniqid()),
                'text_modules' => $request->input('text_modules', [
                    [
                        'header' => 'Customer Name',
                        'body' => $request->input('customer_name', 'John Doe'),
                        'id' => 'customer-name'
                    ],
                    [
                        'header' => 'Pass ID',
                        'body' => 'PASS-' . uniqid(),
                        'id' => 'pass-id'
                    ]
                ])
            ];

            // Only add logo if it's a valid URL
            if ($request->filled('logo') && filter_var($request->input('logo'), FILTER_VALIDATE_URL)) {
                $data['logo'] = $request->input('logo');
            }

            // Only add hero image if it's a valid URL
            if ($request->filled('hero_image') && filter_var($request->input('hero_image'), FILTER_VALIDATE_URL)) {
                $data['hero_image'] = $request->input('hero_image');
            }

            $jwt = $this->walletService->generatePass($data);

            return response()->json([
                'success' => true,
                'jwt' => $jwt,
                'message' => 'Pass generated successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate pass: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create or update the pass class
     */
    public function createClass(): JsonResponse
    {
        try {
            $this->walletService->createPassClass();

            return response()->json([
                'success' => true,
                'message' => 'Pass class created/updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create pass class: ' . $e->getMessage()
            ], 500);
        }
    }
}
