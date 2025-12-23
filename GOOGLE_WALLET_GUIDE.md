# Google Wallet Integration - Setup & Usage Guide

## Overview
This Laravel application implements Google Wallet pass generation using the `chiiya/laravel-passes` package. Users can create and add custom passes to their Google Wallet.

## Configuration

### Environment Variables
The following variables are configured in your `.env` file:

```env
PASSES_GOOGLE_CREDENTIALS='cobalt-carver-482017-n4-c4b70c42d7b4.json'
GOOGLE_WALLET_ISSUER_ID=3388000000023061074
GOOGLE_WALLET_MERCHANT_ID=BCR2DN5TZDQ4VSZ7
```

### Service Account JSON
Location: `public/cobalt-carver-482017-n4-c4b70c42d7b4.json`

**Security Note:** For production, move this file to `storage/app/` and update the config to use `storage_path()` instead of `public_path()`.

## How It Works

### 1. Pass Class (Template)
A **Pass Class** is a reusable template that defines the structure and appearance of your passes. It needs to be created once.

- **Class ID Format:** `{issuerId}.{className}`
- **Example:** `3388000000023061074.generic-pass-class`

### 2. Pass Object (Individual Pass)
A **Pass Object** is an individual pass instance created from a class. Each pass has a unique ID.

- **Object ID Format:** `{issuerId}.{uuid}`
- **Example:** `3388000000023061074.a1b2c3d4-e5f6-7890-abcd-ef1234567890`

### 3. JWT Token
The JWT token contains the pass object data and is used to generate the "Add to Google Wallet" button link.

## Files Created

### 1. Service Layer
**File:** `app/Services/GoogleWalletService.php`

Main service handling:
- Pass class creation/update
- Pass object generation
- JWT signing

**Key Methods:**
- `createPassClass()` - Creates/updates the pass template
- `generatePass($data)` - Generates a pass with custom data
- `passClassExists()` - Checks if class is already created

### 2. Controller
**File:** `app/Http/Controllers/HomeController.php`

**Endpoints:**
- `GET /wallet` - Display the wallet pass generation page
- `POST /wallet/generate` - Generate a pass and return JWT
- `POST /wallet/create-class` - Manually create/update the pass class

### 3. View
**File:** `resources/views/wallet.blade.php`

Interactive web interface with:
- Form to customize pass details
- Pass generation button
- Google Wallet "Add to Wallet" button
- Real-time status messages

### 4. Routes
**File:** `routes/web.php`

```php
Route::get('/wallet', [HomeController::class, 'index'])->name('wallet.index');
Route::post('/wallet/generate', [HomeController::class, 'generatePass'])->name('wallet.generate');
Route::post('/wallet/create-class', [HomeController::class, 'createClass'])->name('wallet.create-class');
```

## Usage

### Step 1: Create the Pass Class
Before generating passes, create the pass class (template):

**Option A - Automatic (Recommended):**
The pass class is automatically created the first time you generate a pass.

**Option B - Manual:**
```bash
# Via API
curl -X POST http://localhost/wallet/create-class
```

### Step 2: Access the Web Interface
Navigate to: `http://localhost/wallet`

### Step 3: Generate a Pass
1. Customize the pass details in the form
2. Click "Generate Pass"
3. Click the "Add to Google Wallet" button that appears
4. Sign in to your Google account
5. The pass will be added to your Google Wallet

## API Usage

### Generate Pass via API

**Endpoint:** `POST /wallet/generate`

**Request Body:**
```json
{
  "card_title": "My Custom Pass",
  "header": "Special Offer",
  "subheader": "Limited Time",
  "customer_name": "John Doe",
  "background_color": "#4285f4",
  "barcode_value": "PASS-12345",
  "text_modules": [
    {
      "header": "Field Label",
      "body": "Field Value",
      "id": "field-id"
    }
  ]
}
```

**Response:**
```json
{
  "success": true,
  "jwt": "eyJhbGciOiJSUzI1NiIsInR5cCI6IkpXVCJ9...",
  "message": "Pass generated successfully"
}
```

### Use the JWT
The JWT can be used in two ways:

**1. Direct Link:**
```html
https://pay.google.com/gp/v/save/{JWT_TOKEN}
```

**2. Google Wallet Button:**
```html
<a href="https://pay.google.com/gp/v/save/{JWT_TOKEN}">
  <img src="https://pay.google.com/gp/p/generate_logo?origin=YOUR_DOMAIN&type=save_to_wallet" 
       alt="Add to Google Wallet">
</a>
```

## Customization

### Modify Pass Class Template
Edit `app/Services/GoogleWalletService.php` in the `createPassClass()` method:

```php
$class = new GenericClass(
    id: $this->getClassId(),
    imageModulesData: [...],
    textModulesData: [...],
    linksModuleData: ...,
    // Add more customization
);
```

### Change Pass Design
Modify the `generatePass()` method to customize:
- Card title, header, subheader
- Logo and hero images
- Background color
- Barcode type and value
- Text fields
- Validity period

## Testing

### Test with cURL

**Create Class:**
```bash
curl -X POST http://localhost/wallet/create-class
```

**Generate Pass:**
```bash
curl -X POST http://localhost/wallet/generate \
  -H "Content-Type: application/json" \
  -d '{
    "card_title": "Test Pass",
    "header": "Test Header",
    "customer_name": "Test User"
  }'
```

## Important Notes

### Do You Need to Create a Class?
**YES!** The pass class must be created before generating pass objects. The implementation handles this automatically, but you can also create it manually using the `/wallet/create-class` endpoint.

### Class vs Object
- **Class** = Template (created once, reusable)
- **Object** = Individual pass instance (created per user)

### Google Wallet API Requirements
1. ✅ Service account credentials JSON file
2. ✅ Issuer ID (3388000000023061074)
3. ✅ Google Wallet API enabled in Google Cloud Console
4. ✅ Valid origin domain configured

### Security Considerations
- Move service account JSON to `storage/app/` in production
- Add authentication to wallet endpoints
- Validate user input
- Use HTTPS in production
- Set proper CORS headers

## Troubleshooting

### "Class not found" Error
Run: `POST /wallet/create-class` to create the pass class.

### "Invalid credentials" Error
- Verify the JSON file path is correct
- Check that the Issuer ID matches your Google Cloud project
- Ensure Google Wallet API is enabled

### "Origin not allowed" Error
Update the `origins` in `config/passes.php` to match your domain.

### Button not appearing
Check browser console for JavaScript errors and verify the JWT was generated successfully.

## Google Wallet Console
Access your Google Wallet console:
- URL: https://pay.google.com/business/console
- Project: cobalt-carver-482017-n4
- View and manage your passes here

## Next Steps

1. **Test the implementation:**
   - Visit `/wallet`
   - Generate a test pass
   - Add it to Google Wallet

2. **Customize the design:**
   - Update pass class template
   - Add your logo and branding
   - Customize colors and fields

3. **Integrate with your app:**
   - Call the API from your frontend
   - Store pass IDs in database
   - Send passes via email

4. **Production setup:**
   - Move credentials to secure location
   - Add authentication
   - Configure proper domain origins
   - Enable HTTPS

## Support

Package Documentation:
- Laravel Passes: https://github.com/chiiya/laravel-passes
- Base Package: https://github.com/chiiya/passes
- Google Wallet API: https://developers.google.com/wallet

## License
MIT License - Same as Laravel framework

