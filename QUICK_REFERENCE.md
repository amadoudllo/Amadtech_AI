# QUICK REFERENCE - AMADTECH AI

## 🚀 ACCESS URLS

| Page | URL | Credentials |
|------|-----|-------------|
| **Chat** | http://127.0.0.1:8000/chat | - |
| **User Login** | http://127.0.0.1:8000/login | Test user account |
| **User Register** | http://127.0.0.1:8000/register | Sign up |
| **Admin Login** | http://127.0.0.1:8000/admin/login | admin@amadtech.com / Admin@2025 |
| **Admin Dashboard** | http://127.0.0.1:8000/admin | (auto-login) |
| **Manage Users** | http://127.0.0.1:8000/admin/users | (requires admin) |
| **Settings** | http://127.0.0.1:8000/admin/settings | (requires admin) |

---

## 🔑 TEST CREDENTIALS

### Admin Account
```
Email:    admin@amadtech.com
Password: Admin@2025
```

### Test User Registration
```
1. Visit: http://127.0.0.1:8000/register
2. Fill in: name, email, password
3. Check email for verification link
4. Click link to verify
5. Auto-login to chat
```

---

## 📝 COMMON TASKS

### View Server Logs
```bash
tail -f storage/logs/laravel.log
```

### Access Database
```bash
mysql -u root amadtech_ai
```

### Run Tests
```bash
php comprehensive-test.php
```

### Clear Cache
```bash
php artisan cache:clear
```

### List All Routes
```bash
php artisan route:list
```

---

## 🆘 TROUBLESHOOTING

### Server Won't Start
```bash
# Kill existing php processes
Get-Process php -ErrorAction SilentlyContinue | Stop-Process -Force
# Restart
php artisan serve
```

### Database Connection Error
```bash
# Verify XAMPP MySQL is running
# Default: localhost, user: root, password: (empty)
```

### Email Not Sending
```bash
# Check SMTP settings in .env:
# MAIL_MAILER=smtp
# MAIL_SCHEME=smtps
# MAIL_PORT=465
# MAIL_HOST=smtp-relay.brevo.com
# MAIL_USERNAME=contact@univ-vibe.com
# MAIL_PASSWORD=(see .env)
```

### Permission Denied
```powershell
# Grant IIS_IUSRS permissions
icacls "C:\xampp\htdocs\Amadtech_AI\storage" /grant "IIS_IUSRS:(OI)(CI)F" /T
icacls "C:\xampp\htdocs\Amadtech_AI\bootstrap\cache" /grant "IIS_IUSRS:(OI)(CI)F" /T
```

---

## 🔍 FILE LOCATIONS

```
📦 Project Root
 ├── 📁 app/
 │   ├── Http/Controllers/Admin/AdminDashboardController.php
 │   ├── Http/Controllers/Auth/*.php
 │   └── Models/User.php, AdminSetting.php, etc.
 ├── 📁 resources/views/
 │   ├── admin/dashboard.blade.php
 │   ├── admin/users/index.blade.php
 │   ├── admin/settings/index.blade.php
 │   └── auth/*.blade.php
 ├── 📁 database/
 │   ├── migrations/ (all table definitions)
 │   └── seeders/
 ├── 📁 storage/
 │   ├── logs/laravel.log
 │   └── framework/sessions/
 ├── 📁 routes/
 │   └── web.php (all routes)
 ├── .env (configuration)
 └── composer.json (dependencies)
```

---

## 💾 DATABASE TABLES

```sql
-- View all tables
SHOW TABLES;

-- User count
SELECT COUNT(*) FROM users;

-- Admin users
SELECT * FROM users WHERE role='admin';

-- Email verifications
SELECT * FROM email_verifications;

-- Settings
SELECT * FROM admin_settings;

-- Activity logs
SELECT * FROM activity_logs;
```

---

## 🔄 API TESTING (cURL)

### Login
```bash
curl -X POST http://127.0.0.1:8000/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@amadtech.com","password":"Admin@2025"}'
```

### Send Chat Message
```bash
curl -X POST http://127.0.0.1:8000/chat/send \
  -H "Content-Type: application/json" \
  -d '{"message":"Hello AI","conversation_id":null}'
```

### Get Conversations
```bash
curl http://127.0.0.1:8000/api/chat/conversations
```

---

## 📊 MONITORING

### System Health Check
```bash
php comprehensive-test.php
```

### View Recent Errors
```bash
# Last 50 lines of log
Get-Content storage/logs/laravel.log -Tail 50
```

### Database Connection Test
```bash
php -r "new PDO('mysql:host=127.0.0.1;dbname=amadtech_ai', 'root', ''); echo 'OK';"
```

---

## 🎯 FEATURE CHECKLIST

- ✅ User Registration with Email Verification
- ✅ Admin Dashboard
- ✅ User Management
- ✅ Settings Configuration
- ✅ Activity Logging
- ✅ User Blocking
- ✅ Email Notifications
- ✅ Session Management
- ✅ Role-Based Access Control
- ✅ Database Operations

---

## 📞 ADMIN FUNCTIONS

| Function | Route | Method |
|----------|-------|--------|
| View Dashboard | /admin | GET |
| List Users | /admin/users | GET |
| Block User | /admin/users/{id}/block | POST |
| Unblock User | /admin/users/{id}/unblock | POST |
| Delete User | /admin/users/{id}/delete | POST |
| View Settings | /admin/settings | GET |
| Update Settings | /admin/settings | POST |
| View Activity Logs | /admin/logs/activity | GET |

---

## 🌐 ENVIRONMENT

- **Server:** PHP 8.2+ (XAMPP)
- **Database:** MySQL 5.7+
- **Framework:** Laravel 11
- **URL:** http://127.0.0.1:8000
- **Port:** 8000 (default)
- **Environment:** Development (.env = local)
- **Debug Mode:** Enabled (APP_DEBUG=true)

---

*Last Updated: December 6, 2025*  
*Status: ✅ Fully Operational*
