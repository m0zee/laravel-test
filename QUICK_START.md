# Google Wallet Implementation - Quick Start

## ✅ Implementation Complete!

Your Google Wallet integration has been successfully implemented and configured.

## 📋 What Was Done

### 1. Configuration Updated
- ✅ Added `GOOGLE_WALLET_ISSUER_ID=3388000000023061074` to `.env`
- ✅ Added `GOOGLE_WALLET_MERCHANT_ID=BCR2DN5TZDQ4VSZ7` to `.env`
- ✅ Updated `config/passes.php` with issuer_id and merchant_id
- ✅ Service credentials configured: `public/cobalt-carver-482017-n4-c4b70c42d7b4.json`

### 2. Files Created
- ✅ `app/Services/GoogleWalletService.php` - Main service for pass generation
- ✅ `app/Http/Controllers/HomeController.php` - Controller with wallet endpoints
- ✅ `resources/views/wallet.blade.php` - Web interface for pass generation
- ✅ `routes/web.php` - Routes updated with wallet endpoints
- ✅ `GOOGLE_WALLET_GUIDE.md` - Complete documentation
- ✅ `test-google-wallet-config.php` - Configuration test script

### 3. Routes Available
```
GET  /wallet                    - Web interface for pass generation
POST /wallet/generate           - API endpoint to generate passes
POST /wallet/create-class       - API endpoint to create/update pass class
```

## 🚀 Quick Start

### Option 1: Web Interface (Easiest)
1. Start your Laravel server:
   ```bash
   php artisan serve
   ```

2. Visit: http://localhost:8000/wallet

3. Fill in the form and click "Generate Pass"

4. Click the "Add to Google Wallet" button

### Option 2: API Usage

**Step 1: Create the Pass Class (One-time)**
```bash
curl -X POST http://localhost:8000/wallet/create-class \
  -H "Content-Type: application/json"
```

**Step 2: Generate a Pass**
```bash
curl -X POST http://localhost:8000/wallet/generate \
  -H "Content-Type: application/json" \
  -d '{
    "card_title": "My First Pass",
    "header": "Welcome",
    "subheader": "Thank you for joining",
    "customer_name": "John Doe",
    "background_color": "#4285f4"
  }'
```

**Response:**
```json
{
  "success": true,
  "jwt": "eyJhbGciOiJSUzI1NiIs...",
  "message": "Pass generated successfully"
}
```

**Step 3: Use the JWT**
```html
<a href="https://pay.google.com/gp/v/save/{JWT_TOKEN}">
  Add to Google Wallet
</a>
```

## ❓ Do You Need to Create a Class?

**YES!** You need to create a pass CLASS before generating pass objects.

**What's the difference?**
- **Class** = Template (like a blueprint) - Created ONCE
- **Object** = Individual pass instance - Created for EACH user

**Good news:** The implementation automatically creates the class the first time you generate a pass. But you can also create it manually:

```bash
curl -X POST http://localhost:8000/wallet/create-class
```

## 🔑 Your Configuration

```
Issuer ID:  3388000000023061074
Merchant ID: BCR2DN5TZDQ4VSZ7
Class ID:    3388000000023061074.generic-pass-class
Project:     cobalt-carver-482017-n4
```

## 📱 Testing Your Pass

1. **Generate a pass** using either method above
2. **Click the "Add to Google Wallet" link**
3. **Sign in** with your Google account
4. **View your pass** in Google Wallet app or at https://pay.google.com

## 🔧 Customization

### Change Pass Design
Edit `app/Services/GoogleWalletService.php`:

```php
// In createPassClass() method - customize the template
$class = new GenericClass(
    id: $this->getClassId(),
    imageModulesData: [...],  // Your images
    textModulesData: [...],   // Your default text
    // Add more customization
);

// In generatePass() method - customize individual passes
$object = new GenericObject(
    cardTitle: LocalizedString::make('en', 'Your Title'),
    header: LocalizedString::make('en', 'Your Header'),
    logo: Image::make('https://yoursite.com/logo.png'),
    hexBackgroundColor: '#FF5733',
    // Add more fields
);
```

### Add More Fields
```php
textModulesData: [
    new TextModuleData(
        header: 'Points Balance',
        body: '1,234 pts',
        id: 'points'
    ),
    new TextModuleData(
        header: 'Member Since',
        body: '2024',
        id: 'member-since'
    )
]
```

## 📚 Documentation

- **Complete Guide:** `GOOGLE_WALLET_GUIDE.md`
- **Package Docs:** https://github.com/chiiya/laravel-passes
- **Google Wallet API:** https://developers.google.com/wallet

## ✅ Verified & Working

All tests pass:
```
✓ Issuer ID configured
✓ Merchant ID configured
✓ Service credentials file valid
✓ Origins configured
✓ Class ID format valid
✓ GoogleWalletService instantiable
✓ Routes registered
```

## 🎯 Next Steps

1. **Test the implementation:**
   - Visit `/wallet` and generate a test pass
   - Add it to your Google Wallet

2. **Customize the design:**
   - Update pass class with your branding
   - Add your logo and colors
   - Customize text fields

3. **Integrate with your app:**
   - Call the API from your frontend
   - Store pass IDs in your database
   - Send passes via email to users

4. **Production setup:**
   - Move JSON credentials to `storage/app/`
   - Add authentication to wallet endpoints
   - Update origins to your production domain
   - Enable HTTPS

## 🔒 Security Notes

⚠️ **Important for Production:**
1. Move `cobalt-carver-482017-n4-c4b70c42d7b4.json` from `public/` to `storage/app/`
2. Update config to use `storage_path()` instead of `public_path()`
3. Add authentication middleware to wallet routes
4. Use HTTPS in production
5. Validate all user input

## 💬 Support

If you encounter any issues:
1. Check the configuration with: `php test-google-wallet-config.php`
2. Review `GOOGLE_WALLET_GUIDE.md` for troubleshooting
3. Check Laravel logs: `storage/logs/laravel.log`
4. Verify Google Wallet API is enabled in Google Cloud Console

## 🎉 You're All Set!

Your Google Wallet integration is ready to use. Start generating passes! 🚀

