# 🏗️ NativePHP Desktop Build Guide

Panduan lengkap untuk membuat Keuangan Desktop sebagai aplikasi desktop native menggunakan **NativePHP**.

## 📋 Requirements

- PHP 8.2+
- Composer
- NativePHP CLI installed
- Laravel 10+
- Windows/macOS/Linux (depending on target platform)

---

## 🚀 Quick Start

### 1. Install NativePHP Package

```bash
cd "D:\Project Mario\keuangan desktop"

# Install NativePHP
composer require native-php/laravel
php artisan native:install
```

### 2. Serve as Desktop App (Development)

```bash
php artisan native:serve
```

This will:
- Start the Laravel development server
- Open a native window showing your app
- Allow native file system access
- Enable offline-first functionality

### 3. Build for Distribution

#### Windows (EXE)
```bash
php artisan native:build --platform=windows --output=dist/windows

# Result: dist/windows/Keuangan.exe (portable executable)
#         dist/windows/Keuangan Installer.exe (MSI installer)
```

#### macOS (App Bundle)
```bash
php artisan native:build --platform=macos --output=dist/macos

# Result: dist/macos/Keuangan.app (macOS application)
```

#### Linux (AppImage)
```bash
php artisan native:build --platform=linux --output=dist/linux

# Result: dist/linux/Keuangan.AppImage (portable Linux app)
#         dist/linux/keuangan_x.y.z_amd64.deb (Debian package)
```

---

## ⚙️ NativePHP Configuration

Edit `nativephp.json` (root directory) to customize build:

```json
{
  "app_name": "Keuangan",
  "app_version": "1.0.0",
  "app_id": "com.keuangan.finance",
  "window": {
    "width": 1200,
    "height": 800,
    "min_width": 800,
    "min_height": 600,
    "frame": true,
    "transparent": false,
    "resizable": true
  },
  "php": {
    "version": "8.2",
    "extensions": ["sqlite3", "pdo_mysql", "gd", "zip"]
  },
  "menu": [
    {
      "label": "File",
      "submenu": [
        {
          "label": "Exit",
          "accelerator": "Ctrl+Q",
          "action": "quit"
        }
      ]
    },
    {
      "label": "Help",
      "submenu": [
        {
          "label": "About",
          "action": "about"
        }
      ]
    }
  ]
}
```

---

## 📦 Database Setup for Desktop (Offline-First)

### Use SQLite for Offline Mode

Edit `.env`:
```env
DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite
```

Then:
```bash
# Create empty SQLite file
touch database/database.sqlite

# Run migrations
php artisan migrate

# Seed demo data
php artisan db:seed
```

### Hybrid Mode (MySQL Development + SQLite Production)

In `config/database.php`, add logic to auto-switch:

```php
'default' => env('DB_CONNECTION', app()->environment('production') ? 'sqlite' : 'mysql'),
```

---

## 🔐 Application Signing (Production)

### Windows (Code Signing Certificate)

1. Obtain a code signing certificate (e.g., Sectigo, DigiCert, GoDaddy)
2. Configure NativePHP:

```json
{
  "windows": {
    "signing_certificate": "path/to/certificate.pfx",
    "signing_password": "YOUR_CERT_PASSWORD",
    "signing_timestamp_server": "http://timestamp.comodoca.com/authenticode"
  }
}
```

3. Build:
```bash
php artisan native:build --platform=windows --sign --output=dist/windows
```

### macOS (Developer Certificate + Notarization)

1. Register for Apple Developer Program
2. Create a Developer ID Application certificate
3. Configure NativePHP:

```json
{
  "macos": {
    "signing_identity": "Developer ID Application: Your Name (TEAM_ID)",
    "team_id": "TEAM_ID",
    "notarize": true,
    "notarize_username": "apple@email.com",
    "notarize_password": "app-specific-password"
  }
}
```

4. Build:
```bash
php artisan native:build --platform=macos --sign --output=dist/macos
```

### Linux (GPG Signing - Optional)

```bash
gpg --sign-key dist/linux/keuangan_x.y.z_amd64.deb
```

---

## 📱 Installer Creation

### Windows MSI Installer

NativePHP automatically creates an MSI installer. Customize in `nativephp.json`:

```json
{
  "windows": {
    "installer": {
      "manufacturer": "Keuangan Inc",
      "license_path": "LICENSE",
      "icon": "resources/icon.ico",
      "banner": "resources/installer-banner.bmp",
      "welcome_image": "resources/welcome.bmp"
    }
  }
}
```

### macOS DMG Installer

```json
{
  "macos": {
    "installer": {
      "icon": "resources/icon.icns",
      "background": "resources/dmg-background.png",
      "window_width": 540,
      "window_height": 360
    }
  }
}
```

---

## 🔄 Updates & Auto-Update

Configure auto-update in `nativephp.json`:

```json
{
  "updates": {
    "enabled": true,
    "channel": "stable",
    "check_interval": 86400,
    "update_server": "https://updates.keuangan.com/api/updates",
    "public_key": "your-ed25519-public-key"
  }
}
```

Then create an update server that returns:

```json
{
  "version": "1.1.0",
  "url": "https://releases.keuangan.com/keuangan-1.1.0.exe",
  "notes": "Performance improvements and bug fixes",
  "pub_date": "2025-12-01T10:00:00Z",
  "signature": "ed25519-signature-here"
}
```

---

## 🎨 Branding & Icons

### Required Assets

```
resources/
├── icon.ico              # Windows icon (256x256)
├── icon.icns             # macOS icon (512x512)
├── icon.png              # Linux icon (512x512)
├── installer-banner.bmp  # Windows installer banner (493×58)
├── welcome.bmp           # Windows welcome screen (463×462)
├── dmg-background.png    # macOS DMG background (540x360)
└── splash.png            # Splash screen on startup (400x300)
```

Generate icons:
```bash
# Using ImageMagick
convert icon-1024.png -define icon:auto-resize=256,128,96,64,48,32,16 icon.ico
convert icon-1024.png -define icon:auto-resize=512 icon.icns
```

---

## 🧪 Testing the Desktop Build

### Before Building
```bash
# Test development mode
php artisan native:serve

# Test built version locally (Windows)
dist\windows\Keuangan.exe

# Test built version locally (macOS)
open dist/macos/Keuangan.app

# Test built version locally (Linux)
./dist/linux/Keuangan.AppImage
```

### Validation Checklist
- [ ] App launches without errors
- [ ] Database initialized correctly
- [ ] AI chat connects to API
- [ ] Transactions create/edit/delete work
- [ ] Charts render properly
- [ ] All navigation links work
- [ ] Offline mode works (SQLite)
- [ ] File uploads work
- [ ] Logout/login cycle works

---

## 📊 File Size & Performance

Typical build sizes:
- **Windows EXE:** 200-300 MB (includes PHP runtime)
- **macOS APP:** 250-350 MB
- **Linux AppImage:** 220-320 MB

**To reduce size:**
1. Remove unused PHP extensions in `nativephp.json`
2. Minify frontend assets: `npm run build`
3. Enable compression in deployment

---

## 🔄 Alternative: Electron Wrapper (if NativePHP doesn't work)

If NativePHP has issues, use **Electron** instead:

```bash
npm install --save-dev electron electron-builder

# Create main.js
touch electron/main.js
```

In `electron/main.js`:
```javascript
const { app, BrowserWindow } = require('electron');
const path = require('path');

let mainWindow;

app.on('ready', () => {
  mainWindow = new BrowserWindow({
    width: 1200,
    height: 800,
    webPreferences: {
      nodeIntegration: false,
      preload: path.join(__dirname, 'preload.js')
    }
  });

  // Point to running Laravel server
  mainWindow.loadURL('http://localhost:8000');
  
  mainWindow.webContents.openDevTools(); // Remove in production
});
```

Build:
```bash
electron-builder --win
electron-builder --mac
electron-builder --linux
```

---

## 🚀 Distribution

### GitHub Releases
```bash
# Tag your release
git tag -a v1.0.0 -m "Release version 1.0.0"
git push origin v1.0.0

# Upload binaries to GitHub release page
# https://github.com/your-repo/releases
```

### Official Website
Create a download page with checksums:

```
Keuangan v1.0.0 - Download

Windows (exe):     dist/windows/Keuangan.exe (SHA256: abc123...)
Windows (installer): dist/windows/Keuangan Installer.exe (SHA256: def456...)
macOS:            dist/macos/Keuangan.dmg (SHA256: ghi789...)
Linux (AppImage): dist/linux/Keuangan.AppImage (SHA256: jkl012...)
Linux (deb):      dist/linux/keuangan_1.0.0_amd64.deb (SHA256: mno345...)
```

---

## 📝 Troubleshooting

### App won't start
- Check PHP version: `php -v`
- Verify Laravel cache: `php artisan cache:clear`
- Check `.env` configuration
- Enable debug mode temporarily: `APP_DEBUG=true`

### AI chat not working
- Verify API key in `.env`
- Check internet connection
- Test API manually: `curl -X POST https://api.openai.com/v1/chat/completions ...`

### Database errors
- For MySQL: Ensure MySQL is running
- For SQLite: Check file permissions on `database/database.sqlite`
- Run migrations: `php artisan migrate`

### File upload issues
- Ensure `storage/` folder is writable
- Check file size limits in `php.ini`
- Verify MIME type validation

---

## 📚 References

- [NativePHP Documentation](https://nativephp.com)
- [Laravel Documentation](https://laravel.com/docs)
- [Electron Documentation](https://www.electronjs.org/docs)

---

**Ready to ship your financial management app! 🚀**
