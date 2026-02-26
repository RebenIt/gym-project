# 🚀 FitZone Installation Guide

## Complete Step-by-Step Setup Instructions

---

## 📋 Table of Contents
1. [System Requirements](#system-requirements)
2. [Installing XAMPP](#installing-xampp)
3. [Setting Up the Database](#database-setup)
4. [Configuring the System](#configuration)
5. [Testing the Installation](#testing)
6. [Default Login Credentials](#login-credentials)
7. [Troubleshooting](#troubleshooting)

---

## 💻 System Requirements

### Minimum Requirements:
- **Operating System:** Windows 7/8/10/11, macOS 10.13+, or Linux
- **RAM:** 2 GB (4 GB recommended)
- **Storage:** 500 MB free space
- **Browser:** Chrome 90+, Firefox 88+, Safari 14+, or Edge 90+

### Software Requirements:
- XAMPP 8.0+ (includes Apache, PHP 7.4+, MySQL 5.7+)
- Modern web browser
- Text editor (optional, for viewing code)

---

## 📥 Step 1: Installing XAMPP

### For Windows:

**1.1 Download XAMPP**
1. Go to: https://www.apachefriends.org/
2. Click "XAMPP for Windows"
3. Download the installer (approximately 150 MB)

**1.2 Install XAMPP**
1. Double-click the downloaded installer
2. Click "Next" through the welcome screens
3. **Important:** Make sure these components are selected:
   - ✅ Apache
   - ✅ MySQL
   - ✅ PHP
   - ✅ phpMyAdmin
4. Choose installation folder: `C:\xampp` (default)
5. Click "Next" and wait for installation (5-10 minutes)
6. When finished, click "Finish"

**1.3 Start XAMPP**
1. Open "XAMPP Control Panel" (should auto-start, or search in Start menu)
2. Click "Start" next to **Apache** (should turn green)
3. Click "Start" next to **MySQL** (should turn green)

![XAMPP Control Panel Screenshot]
- Apache: **Running** (green)
- MySQL: **Running** (green)

### For macOS:

**1.1 Download & Install**
1. Go to: https://www.apachefriends.org/
2. Download "XAMPP for OS X"
3. Open the `.dmg` file
4. Drag XAMPP to Applications folder
5. Open XAMPP from Applications

**1.2 Start Services**
1. Open XAMPP Manager
2. Click "Start" for Apache
3. Click "Start" for MySQL

### For Linux:

```bash
# Download
wget https://www.apachefriends.org/xampp-files/8.0.30/xampp-linux-x64-8.0.30-0-installer.run

# Make executable
chmod +x xampp-linux-x64-8.0.30-0-installer.run

# Run installer
sudo ./xampp-linux-x64-8.0.30-0-installer.run

# Start services
sudo /opt/lampp/lampp start
```

---

## 🗄️ Step 2: Setting Up the Database

### Method 1: Using phpMyAdmin (Easiest)

**2.1 Access phpMyAdmin**
1. Make sure Apache and MySQL are running in XAMPP
2. Open your browser
3. Go to: `http://localhost/phpmyadmin`
4. You should see phpMyAdmin interface

**2.2 Create Database**
1. Click "New" in the left sidebar
2. Database name: `gym_website`
3. Collation: `utf8mb4_unicode_ci`
4. Click "Create"

**2.3 Import Database Tables**
1. Click on `gym_website` database in left sidebar
2. Click "Import" tab at the top
3. Click "Choose File"
4. Select `setup.sql` from the project folder
5. Click "Go" at the bottom
6. Wait for success message: "Import has been successfully finished"

**2.4 Verify Tables**
- Click on `gym_website` database
- You should see 15 tables:
  - admins
  - users
  - games
  - trainers
  - tips
  - certificates
  - plans
  - services
  - pages
  - beginner_programs
  - settings
  - contact_messages
  - user_lists
  - user_list_games
  - user_notes

### Method 2: Using Command Line

```bash
# Navigate to project folder
cd c:\xampp\htdocs\gym-project

# Import database
c:\xampp\mysql\bin\mysql -u root -p gym_website < setup.sql

# (Press Enter when asked for password - default is no password)
```

---

## ⚙️ Step 3: Configuring the System

### 3.1 Copy Project to htdocs

**Windows:**
1. Navigate to `C:\xampp\htdocs\`
2. **Important:** The project should already be there as `gym-project`
3. If not, copy the entire `gym-project` folder there

**macOS:**
```bash
# Location: /Applications/XAMPP/htdocs/
cp -r ~/Downloads/gym-project /Applications/XAMPP/htdocs/
```

**Linux:**
```bash
# Location: /opt/lampp/htdocs/
sudo cp -r ~/Downloads/gym-project /opt/lampp/htdocs/
sudo chmod -R 755 /opt/lampp/htdocs/gym-project
```

### 3.2 Configure Database Connection

**Option A: Default Configuration (No Changes Needed)**

If you're using XAMPP with default settings:
- ✅ Username: `root`
- ✅ Password: (empty/blank)
- ✅ Host: `localhost`
- ✅ Database: `gym_website`

**No configuration needed!** The system is already set up with these defaults.

**Option B: Custom Database Settings**

If you changed MySQL password or settings:

1. Open file: `includes/config.php`
2. Find these lines (around line 8-11):
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', ''); // Your password here if you set one
define('DB_NAME', 'gym_website');
```

3. Update with your settings
4. Save the file

### 3.3 Set Upload Folder Permissions

**Windows:**
- Usually not needed (permissions okay by default)

**macOS/Linux:**
```bash
cd /path/to/gym-project
chmod -R 755 assets/uploads/
chmod -R 755 assets/
```

---

## ✅ Step 4: Testing the Installation

### 4.1 Test Homepage

1. Open browser
2. Go to: `http://localhost/gym-project/`
3. **Expected:** You should see the FitZone homepage with:
   - Navigation menu (Home, Exercises, Trainers, etc.)
   - Welcome section
   - Featured exercises (if sample data imported)

**If you see error:**
- Check XAMPP Apache is running (green)
- Check URL is correct: `http://localhost/gym-project/`
- Check project is in `htdocs` folder

### 4.2 Test Database Connection

1. Go to: `http://localhost/gym-project/games.php`
2. **Expected:** Page loads showing "Exercises" (may be empty if no data)

**If you see "Database connection error":**
- Check MySQL is running in XAMPP
- Check database name is `gym_website`
- Check `includes/config.php` settings

### 4.3 Test Admin Login

1. Go to: `http://localhost/gym-project/admin/`
2. **Login Credentials:**
   - Username: `admin`
   - Password: `admin123`
3. Click "Login"
4. **Expected:** You should see admin dashboard with statistics

**If login fails:**
- Make sure `admins` table has data
- Run the password reset script (see Troubleshooting)

### 4.4 Test User Registration

1. Go to: `http://localhost/gym-project/register.php`
2. Fill in:
   - Username: `testuser`
   - Email: `test@test.com`
   - Password: `Test123!`
3. Click "Register"
4. **Expected:** Success message, redirected to login

### 4.5 Test File Upload

1. Login to admin panel
2. Go to "Games / Exercises"
3. Click "Add New Exercise"
4. Fill form and upload an image
5. Click "Add Exercise"
6. **Expected:** Exercise added, image uploaded

**If upload fails:**
- Check `assets/uploads/` folder exists
- Check folder permissions (macOS/Linux)

---

## 🔑 Default Login Credentials

### Admin Account:
```
URL: http://localhost/gym-project/admin/
Username: admin
Password: admin123
```

### Test User Account:
```
URL: http://localhost/gym-project/user/
Username: (register your own)
Password: (set your own)
```

**⚠️ IMPORTANT SECURITY:**
After installation, immediately:
1. Login to admin panel
2. Go to your profile settings
3. Change the admin password!

---

## 🐛 Troubleshooting

### Problem 1: "Cannot connect to database"

**Symptoms:**
- Error message: "Could not connect to database"
- Pages won't load

**Solutions:**

**A. Check MySQL is Running**
1. Open XAMPP Control Panel
2. Make sure MySQL shows "Running" (green)
3. If not, click "Start"

**B. Check Database Name**
1. Go to `http://localhost/phpmyadmin`
2. Look for `gym_website` in left sidebar
3. If missing, create it (see Step 2)

**C. Check Database Settings**
1. Open `includes/config.php`
2. Verify:
   ```php
   define('DB_HOST', 'localhost'); // Correct
   define('DB_USER', 'root');      // Correct
   define('DB_PASS', '');          // Empty for XAMPP
   define('DB_NAME', 'gym_website'); // Must match database name
   ```

**D. Check MySQL Port**
1. XAMPP Control Panel → MySQL → Config → my.ini
2. Find line: `port=3306`
3. If different, update in `config.php`:
   ```php
   define('DB_HOST', 'localhost:3307'); // Your port
   ```

---

### Problem 2: "Admin login doesn't work"

**Symptoms:**
- "Invalid username or password" error
- Can't access admin panel

**Solution: Reset Admin Password**

**Method A: Using phpMyAdmin**
1. Go to `http://localhost/phpmyadmin`
2. Select `gym_website` database
3. Click `admins` table
4. Click "Browse" tab
5. Find the admin row
6. Click "Edit" (pencil icon)
7. In "password" field, select "Function" → MD5
8. Enter: `admin123`
9. Click "Go"
10. Now try logging in with:
    - Username: `admin`
    - Password: `admin123`

**Method B: Using SQL**
1. Go to `http://localhost/phpmyadmin`
2. Click `gym_website` database
3. Click "SQL" tab
4. Paste this:
   ```sql
   UPDATE admins
   SET password = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'
   WHERE username = 'admin';
   ```
5. Click "Go"
6. Now login with: `admin` / `admin123`

---

### Problem 3: "Page not found" or "404 Error"

**Symptoms:**
- Browser shows "404 Not Found"
- "The requested URL was not found"

**Solutions:**

**A. Check URL Format**
- ✅ Correct: `http://localhost/gym-project/index.php`
- ❌ Wrong: `http://localhost/index.php`
- ❌ Wrong: `C:\xampp\htdocs\gym-project\index.php`

**B. Check Project Location**
1. Open File Explorer / Finder
2. Navigate to `C:\xampp\htdocs\` (Windows)
3. You should see `gym-project` folder there
4. Inside `gym-project`, you should see:
   - `index.php`
   - `admin/` folder
   - `user/` folder
   - `includes/` folder
   - etc.

**C. Check Apache is Running**
1. XAMPP Control Panel
2. Apache should be green ("Running")
3. If red, click "Start"

---

### Problem 4: "Permission denied" when uploading files

**Symptoms:**
- "Failed to upload file"
- Images don't save

**Solutions:**

**Windows:**
1. Right-click `C:\xampp\htdocs\gym-project\assets\uploads`
2. Properties → Security tab
3. Click "Edit"
4. Select "Users"
5. Check "Full Control"
6. Click OK

**macOS/Linux:**
```bash
sudo chmod -R 777 /path/to/gym-project/assets/uploads/
```

**Or safer:**
```bash
sudo chown -R www-data:www-data /path/to/gym-project/assets/uploads/
sudo chmod -R 755 /path/to/gym-project/assets/uploads/
```

---

### Problem 5: "Headers already sent" error

**Symptoms:**
- Error: "Cannot modify header information - headers already sent"
- Usually after saving/editing

**Causes:**
- BOM (Byte Order Mark) in PHP files
- Space or text before `<?php`

**Solution:**
1. Open file in text editor
2. Make sure first line starts with `<?php` (no spaces before)
3. Save with UTF-8 **without BOM** encoding

**In VS Code:**
- Bottom right corner → "UTF-8" → "UTF-8 without BOM"

---

### Problem 6: "Session not working"

**Symptoms:**
- Can't stay logged in
- Redirects to login after every click

**Solutions:**

**A. Check PHP Session Settings**
1. Create file: `test-session.php` in project root
2. Add:
   ```php
   <?php
   session_start();
   $_SESSION['test'] = 'Hello';
   echo "Session ID: " . session_id() . "<br>";
   echo "Session data: ";
   print_r($_SESSION);
   ```
3. Visit: `http://localhost/gym-project/test-session.php`
4. Refresh page
5. If "Hello" doesn't persist, session not working

**B. Check Session Directory**

**Windows:**
1. Open `php.ini` file:
   - `C:\xampp\php\php.ini`
2. Find line: `session.save_path`
3. Make sure it's set to:
   ```
   session.save_path = "C:\xampp\tmp"
   ```
4. Check `C:\xampp\tmp` folder exists
5. Restart Apache

**macOS/Linux:**
```bash
# Check session directory
sudo ls -la /tmp

# Should show session files like:
# sess_abc123def456...
```

---

### Problem 7: "CSS/Design not loading"

**Symptoms:**
- Page loads but looks plain (no styling)
- Missing colors, layouts broken

**Solutions:**

**A. Check File Paths**
1. Right-click on page → "View Page Source"
2. Look for lines like:
   ```html
   <link rel="stylesheet" href="assets/css/style.css">
   ```
3. Click the link
4. Should load CSS file content
5. If 404 error, path is wrong

**B. Clear Browser Cache**
1. Press `Ctrl + Shift + Del` (Windows) or `Cmd + Shift + Del` (Mac)
2. Select "Cached images and files"
3. Click "Clear data"
4. Refresh page: `Ctrl + F5` or `Cmd + Shift + R`

**C. Check Bootstrap CDN**
1. Make sure you have internet connection
2. Bootstrap loads from CDN (needs internet)
3. If offline, download Bootstrap locally

---

### Problem 8: "Blank white page"

**Symptoms:**
- Page loads but completely blank
- No error message shown

**Solutions:**

**A. Enable Error Display**
1. Open `includes/config.php`
2. Add at the very top (line 2-3):
   ```php
   <?php
   error_reporting(E_ALL);
   ini_set('display_errors', 1);
   ```
3. Refresh page
4. Now you'll see the actual error

**B. Check Apache Error Log**
1. XAMPP Control Panel
2. Click "Logs" next to Apache
3. Look at last lines in error.log
4. Shows PHP errors and warnings

---

### Problem 9: "Image not displaying"

**Symptoms:**
- Image uploads but doesn't show
- Broken image icon

**Solutions:**

**A. Check File Path**
```php
// Make sure using correct path
<img src="assets/uploads/filename.jpg">  // ✅ Correct
<img src="/assets/uploads/filename.jpg"> // ❌ Might not work
<img src="C:\xampp\...">                  // ❌ Wrong (local path)
```

**B. Check File Actually Exists**
1. Navigate to: `C:\xampp\htdocs\gym-project\assets\uploads\`
2. Check if image file is there
3. Check filename matches exactly (case-sensitive)

**C. Check Image URL**
1. Right-click broken image → "Copy Image Address"
2. Paste in browser address bar
3. If 404, file missing or path wrong

---

## 📞 Getting Help

If you're still having issues:

**1. Check Error Messages**
- Read the exact error message
- Google the error (usually finds solution)

**2. Check XAMPP Logs**
- Apache error log: `C:\xampp\apache\logs\error.log`
- MySQL error log: `C:\xampp\mysql\data\mysql_error.log`

**3. Common Resources**
- XAMPP FAQ: https://www.apachefriends.org/faq.html
- PHP Manual: https://www.php.net/manual/
- Stack Overflow: https://stackoverflow.com/

**4. Verification Checklist**
- ✅ XAMPP installed correctly
- ✅ Apache running (green in XAMPP)
- ✅ MySQL running (green in XAMPP)
- ✅ Database `gym_website` created
- ✅ Tables imported (15 tables total)
- ✅ Project in `htdocs/gym-project/`
- ✅ Can access: `http://localhost/gym-project/`
- ✅ Admin login works

---

## 🎉 Installation Complete!

If all tests pass, congratulations! Your FitZone Gym Management System is ready to use.

### Next Steps:

1. **Change Admin Password**
   - Login to admin panel
   - Go to Settings/Profile
   - Change password from default

2. **Add Content**
   - Add exercises from Admin → Games
   - Add trainers from Admin → Trainers
   - Add fitness tips from Admin → Tips

3. **Customize Settings**
   - Admin → Settings
   - Update gym name, contact info
   - Add social media links

4. **Test Features**
   - Register a test user account
   - Create a workout list
   - Test all admin features

5. **Backup**
   - Export database from phpMyAdmin
   - Keep backup of `assets/uploads/` folder

---

## 🔒 Security Recommendations

### After Installation:

1. **Change Default Password**
   ```
   Admin → Profile → Change Password
   ```

2. **Add .htaccess Protection** (Optional)
   Create `admin/.htaccess`:
   ```apache
   # Protect admin folder
   AuthType Basic
   AuthName "Admin Area"
   AuthUserFile /path/to/.htpasswd
   Require valid-user
   ```

3. **Disable Directory Listing**
   Add to `assets/.htaccess`:
   ```apache
   Options -Indexes
   ```

4. **Regular Backups**
   - Weekly database export
   - Monthly full backup

---

## 📱 Mobile Access

To access from phone on same network:

1. Find computer's IP address:
   - Windows: Open Command Prompt → type `ipconfig`
   - Look for "IPv4 Address" (e.g., 192.168.1.5)

2. On phone browser, visit:
   ```
   http://192.168.1.5/gym-project/
   ```

3. Make sure computer firewall allows connections

---

**Installation guide complete! Good luck with your project!** 🚀
