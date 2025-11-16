# 🔐 Security Checklist & Best Practices

Panduan keamanan lengkap untuk aplikasi Keuangan Desktop.

---

## ✅ Authentication & Authorization

### User Authentication
- [x] Passwords hashed with bcrypt (PHP password_hash)
- [x] "Remember Me" token secure (unique, long random string)
- [x] Password reset flow uses time-limited tokens (1 hour)
- [x] Failed login attempts throttled (5 attempts/minute per IP)
- [x] Session timeout set to 120 minutes inactivity
- [x] HTTPS-only cookies in production

### Authorization Checks
- [x] Verify user owns resource before edit/delete
- [x] Route middleware checks authenticated user
- [x] Policy classes for Transaction, Budget access
- [x] No direct ID manipulation (use proper authorization)

**Implementation:**
```php
// In TransactionController
public function update(Request $request, Transaction $transaction)
{
    if ($transaction->user_id !== $request->user()->id) {
        abort(403, 'Unauthorized');
    }
    // ... proceed
}
```

---

## 🔒 Data Protection

### Input Validation
- [x] All inputs validated server-side (not just client-side)
- [x] Type checking (numeric, string, date, enum)
- [x] Length limits enforced (max 1000 chars for description)
- [x] File upload validation (MIME types, max 5MB)
- [x] Email validation with RFC 5322 standard

**Implementation:**
```php
$data = $request->validate([
    'amount' => 'required|numeric|min:0.01|max:999999999',
    'type' => 'required|in:income,expense',
    'date' => 'required|date',
    'description' => 'nullable|string|max:1000',
    'receipt' => 'nullable|file|mimes:jpg,png,pdf|max:5120'
]);
```

### Output Escaping
- [x] Blade templates auto-escape by default
- [x] AI responses sanitized before display
- [x] No unescaped JavaScript evaluation
- [x] User-generated content wrapped in `{{ }}` not `{!! !!}`

**Implementation:**
```blade
<!-- SAFE: Auto-escaped -->
<p>{{ $transaction->description }}</p>

<!-- DANGEROUS: Only for trusted HTML -->
{!! $adminGeneratedContent !!}
```

### SQL Injection Prevention
- [x] Use Eloquent ORM (parameterized queries)
- [x] Never concatenate user input in raw queries
- [x] Use parameter binding for raw DB queries

**Safe example:**
```php
// Safe - Eloquent
Transaction::where('user_id', $user->id)->get();

// Safe - Parameter binding
DB::select('SELECT * FROM transactions WHERE user_id = ?', [$user->id]);

// DANGEROUS - Never do this!
DB::select("SELECT * FROM transactions WHERE user_id = $user->id");
```

---

## 🛡️ API Security

### CSRF Protection
- [x] CSRF token on all POST/PUT/DELETE forms
- [x] Laravel middleware enabled by default
- [x] SameSite cookie policy (Lax mode)

**Implementation:**
```blade
<form method="POST" action="/transactions">
    @csrf
    <!-- form fields -->
</form>
```

### Rate Limiting
- [x] AI chat endpoint: 10 requests per minute per user
- [x] Login endpoint: 5 attempts per minute per IP
- [x] API endpoints: 60 requests per minute per user

**Implementation:**
```php
Route::post('/ai/chat', [AIChatController::class, 'ask'])->middleware('throttle:10,1');
```

### API Key Management
- [x] OpenAI key stored in `.env` (never committed)
- [x] Local LLM keys stored securely
- [x] Keys never logged or exposed in errors
- [x] Implement key rotation mechanism

**Implementation:**
```php
// .env
SERVICES_AI_KEY=sk-xxxxxxxxxxxx  # Never commit this

// config/services.php
'ai' => [
    'key' => env('SERVICES_AI_KEY'),
    'endpoint' => env('SERVICES_AI_ENDPOINT'),
]
```

---

## 🔑 Cryptography & Encryption

### Encryption at Rest
- [x] Sensitive data encrypted in database (if needed)
- [x] Encryption key in `.env` (APP_KEY)
- [x] Use `Hash::make()` for passwords (one-way)
- [x] Use `Crypt::encrypt()` for reversible data

**Implementation:**
```php
// One-way (passwords)
$user->password = Hash::make($request->password);

// Reversible (if storing sensitive data)
$encrypted = Crypt::encrypt($sensibleValue);
$decrypted = Crypt::decrypt($encrypted);
```

### File Encryption
- [x] Uploaded receipts stored outside web root
- [x] Files served through controller (not direct access)
- [x] Original filename sanitized

**Implementation:**
```php
// Storage in storage/app/private/
Storage::disk('private')->put("receipts/{$filename}", $file);

// Serve through controller with auth check
public function downloadReceipt(Attachment $attachment)
{
    $this->authorize('view', $attachment);
    return Storage::disk('private')->download($attachment->path);
}
```

---

## 🌐 Desktop Environment Security

### Electron/NativePHP Security
- [x] Disable Node.js integration in renderer process
- [x] Preload script for IPC (if using Electron)
- [x] Sandbox enabled
- [x] Content Security Policy headers

**Implementation (Electron):**
```javascript
const mainWindow = new BrowserWindow({
  webPreferences: {
    nodeIntegration: false,
    enableRemoteModule: false,
    preload: path.join(__dirname, 'preload.js'),
    sandbox: true
  }
});
```

### SQLite Database Protection
- [x] Database file readable only by app user
- [x] Database file permissions: `600` (Linux/macOS) or owner-only (Windows)
- [x] No direct shell access to database
- [x] Regular backups to safe location

**Implementation (Linux/macOS):**
```bash
chmod 600 database/database.sqlite
```

---

## 📝 Logging & Monitoring

### Logging Best Practices
- [x] Log authentication attempts (success/failure)
- [x] Log sensitive operations (transaction create/delete, budget changes)
- [x] Never log passwords or API keys
- [x] Log AI requests/responses (without full payload if sensitive)
- [x] Logs rotated daily (configured in `config/logging.php`)

**Implementation:**
```php
Log::info('User login successful', ['user_id' => $user->id, 'ip' => $request->ip()]);
Log::warning('Failed login attempt', ['email' => $email, 'attempts' => $attempts]);
Log::error('AI API error', ['error' => $error, 'model' => config('services.ai.model')]);
```

### Error Handling
- [x] Error pages don't expose internal paths or stack traces (production)
- [x] Debug mode OFF in production (`APP_DEBUG=false`)
- [x] Errors logged to storage/logs/ not displayed to users
- [x] Generic error messages shown to users

**Implementation (.env):**
```env
APP_ENV=production
APP_DEBUG=false
```

---

## 🔄 Backup & Disaster Recovery

### Database Backups
- [x] Daily automated backups of database
- [x] Backups stored securely (separate location from main DB)
- [x] Encryption of backup files
- [x] Test restore process monthly

**Implementation:**
```bash
# Create backup script (backup.sh)
#!/bin/bash
mysqldump -u root keuangan_desktop | gzip > backups/db_$(date +%Y%m%d_%H%M%S).sql.gz

# Or for SQLite
cp database/database.sqlite backups/database_$(date +%Y%m%d_%H%M%S).sqlite

# Add to crontab for daily 2 AM backup
0 2 * * * /path/to/backup.sh
```

### Document/Receipt Backups
- [x] User receipts backed up regularly
- [x] Backups encrypted
- [x] Cloud backup option (AWS S3) optional

---

## 🧪 Security Testing Checklist

Before deployment, verify:
- [ ] SQL injection tests (try `'; DROP TABLE--`)
- [ ] XSS tests (try `<script>alert('xss')</script>` in inputs)
- [ ] CSRF token validation (try POST without token)
- [ ] Authentication bypasses (try accessing /dashboard without login)
- [ ] Authorization (try accessing another user's transaction)
- [ ] Rate limiting (send 100+ requests to /ai/chat quickly)
- [ ] File upload validation (try `.exe`, `.php` files)
- [ ] Brute force (try 100 login attempts)
- [ ] Information disclosure (check error messages for leaks)

---

## 🚀 Production Deployment

### Pre-Deployment Checklist
- [ ] `.env` configured with production values
- [ ] `APP_DEBUG=false` set
- [ ] `APP_KEY` generated (unique per instance)
- [ ] Database migrated and seeded
- [ ] All tests passing: `php artisan test`
- [ ] No sensitive data in git history: `git log --all --full-history -- .env`
- [ ] Static assets built: `npm run build`
- [ ] Laravel config cached: `php artisan config:cache`
- [ ] Route cache built: `php artisan route:cache`
- [ ] Supervisor/systemd configured for background jobs (if needed)

### Security Headers (NativePHP/Electron)
Add to `config/app.php` middleware or use middleware:
```php
// app/Http/Middleware/SecurityHeaders.php
public function handle($request, $next)
{
    $response = $next($request);
    
    $response->header('X-Content-Type-Options', 'nosniff');
    $response->header('X-Frame-Options', 'SAMEORIGIN');
    $response->header('X-XSS-Protection', '1; mode=block');
    $response->header('Referrer-Policy', 'strict-origin-when-cross-origin');
    
    return $response;
}
```

---

## 📚 References

- [OWASP Top 10](https://owasp.org/www-project-top-ten/)
- [Laravel Security](https://laravel.com/docs/security)
- [PHP Security Guide](https://www.php.net/manual/en/security.php)
- [CWE/SANS Top 25](https://cwe.mitre.org/top25/)

---

**Keep your users' financial data safe! 🔒**
