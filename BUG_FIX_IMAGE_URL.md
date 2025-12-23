# 🔧 Bug Fix: Image URL Error

## Issue
Google Wallet API returned 400 error:
```
Image cannot be loaded. Invalid image URL: https://via.placeholder.com/...
```

## Root Cause
Google Wallet API requires **publicly accessible image URLs** that it can fetch and validate. The placeholder URLs from `via.placeholder.com` are either:
1. Blocked by Google's servers
2. Not accessible from Google's API endpoints
3. Considered invalid by Google's validation

## Solution Applied

### ✅ Changes Made:

1. **Made images optional in Pass Class** (`app/Services/GoogleWalletService.php`)
   - Removed placeholder images from class definition
   - Class now creates without any images (images can be added per pass object)

2. **Made logo optional in Pass Objects** (`app/Services/GoogleWalletService.php`)
   - Logo only added if valid URL is provided
   - Added URL validation using `filter_var($url, FILTER_VALIDATE_URL)`
   - Hero image already was optional

3. **Updated Controller** (`app/Http/Controllers/HomeController.php`)
   - Removed default placeholder logo URL
   - Only includes logo/hero_image if valid URL provided in request
   - Validates URLs before sending to service

4. **Updated View** (`resources/views/wallet.blade.php`)
   - Added optional "Logo URL" input field
   - Field starts empty (no placeholder)
   - Added helper text explaining requirements
   - JavaScript only includes logo in request if field is filled

## How to Use Now

### Option 1: No Logo (Works Immediately)
Just fill in the text fields and generate the pass. No logo will be displayed.

```json
{
  "card_title": "My Pass",
  "header": "Special Offer",
  "subheader": "Valid for 30 days",
  "customer_name": "John Doe",
  "background_color": "#4285f4"
}
```

### Option 2: With Your Own Logo
Provide a publicly accessible image URL:

```json
{
  "card_title": "My Pass",
  "header": "Special Offer",
  "logo": "https://yourdomain.com/logo.png",
  "background_color": "#4285f4"
}
```

## Image Requirements

If you want to add images, they MUST be:

### ✓ **Valid Image URLs:**
- Publicly accessible (no authentication required)
- Must use HTTPS (recommended)
- Must return proper image content-type headers
- Google servers must be able to fetch them

### ✓ **Recommended Sizes:**
- **Logo:** 330x100 pixels
- **Hero Image:** 1032x336 pixels
- **Main Image:** 1032x336 pixels

### ✓ **Supported Formats:**
- PNG (recommended)
- JPG/JPEG
- SVG (sometimes)

### ✗ **Will NOT Work:**
- Placeholder services (via.placeholder.com, placeholder.com, etc.)
- localhost URLs
- Private/internal network URLs
- URLs behind authentication
- Broken or invalid URLs

## Where to Host Images

### Good Options:
1. **Your own domain:** `https://yourdomain.com/images/logo.png`
2. **Cloud storage:** AWS S3, Google Cloud Storage, Azure Blob (with public access)
3. **CDN:** Cloudflare, Cloudinary, imgix
4. **GitHub:** Raw content URLs from public repos

### Example Valid URLs:
```
✓ https://example.com/logo.png
✓ https://your-bucket.s3.amazonaws.com/logo.png
✓ https://cdn.yourdomain.com/images/logo.png
✓ https://raw.githubusercontent.com/user/repo/main/logo.png
```

### Invalid URLs:
```
✗ https://via.placeholder.com/330x100
✗ http://localhost/logo.png
✗ https://192.168.1.1/logo.png
✗ https://private-site.com/protected/logo.png
```

## Testing

### Test Without Logo (Quick Test):
1. Visit: http://laravel-test.test/wallet
2. Fill in text fields only
3. Leave "Logo URL" field **empty**
4. Click "Generate Pass"
5. Should work! ✅

### Test With Logo:
1. Upload your logo to a public URL
2. Enter the URL in "Logo URL" field
3. Click "Generate Pass"
4. Should work with your logo! ✅

## Quick Test Command

```bash
# Test without logo (should work now)
curl -X POST http://laravel-test.test/wallet/generate \
  -H "Content-Type: application/json" \
  -H "X-CSRF-TOKEN: your-token" \
  -d '{
    "card_title": "Test Pass",
    "header": "No Logo Test",
    "subheader": "This should work",
    "customer_name": "Test User",
    "background_color": "#4285f4"
  }'
```

## Status
✅ **FIXED** - You can now generate passes without images immediately!

---

**Try it now:** http://laravel-test.test/wallet

