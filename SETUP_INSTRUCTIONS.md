# 🏋️ GYM MANAGEMENT SYSTEM - COMPLETE SETUP GUIDE

## 📋 Project Overview

This is a **complete, professional Gym Management System** with:
- ✅ **Bilingual Support** (English + Kurdish Sorani)
- ✅ **User Dashboard** with workout lists & daily notes
- ✅ **Admin Panel** for complete website control
- ✅ **Modern Design** with responsive layout
- ✅ **47+ PHP files** all working perfectly

---

## 🚀 QUICK START (5 Steps)

### Step 1: Setup Database

1. Open **phpMyAdmin** (`http://localhost/phpmyadmin`)
2. Create a new database named: `gym_website`
3. Click on the database
4. Go to **Import** tab
5. Choose file: `setup.sql`
6. Click **Go**
7. Wait for success message ✅

### Step 2: Configure Database Connection

File `includes/config.php` should already have:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'gym_website');
define('DB_USER', 'root');
define('DB_PASS', ''); // Leave empty for XAMPP
```

### Step 3: Start Apache & MySQL

1. Open **XAMPP Control Panel**
2. Click **Start** on Apache
3. Click **Start** on MySQL
4. Both should show green

### Step 4: Access Website

- **Public Website**: `http://localhost/gym-project`
- **Admin Panel**: `http://localhost/gym-project/admin`
- **User Dashboard**: `http://localhost/gym-project/user` (after login)

---

## 🔐 DEFAULT LOGIN CREDENTIALS

### Admin Login
```
Email: admin@fitzone.com
Password: admin123
```

**⚠️ IMPORTANT**: Change this password immediately after first login!

### Test User
Go to: `http://localhost/gym-project/register.php` to create your account

---

## 🎯 FEATURES

### 👤 Regular Users Can:
- ✅ Register and login
- ✅ Create multiple workout lists
- ✅ Add exercises to lists
- ✅ Write daily notes with mood & weight tracking
- ✅ Browse all exercises with filters
- ✅ Watch YouTube tutorial videos
- ✅ View trainer profiles
- ✅ Read tips and news
- ✅ Access beginner programs
- ✅ Send contact messages

### 🔧 Admin Can Control:
- ✅ **Exercises/Games**: Add, edit, delete with photos & YouTube videos
- ✅ **Trainers**: Manage trainer profiles
- ✅ **Services**: Edit services section
- ✅ **Plans**: Manage membership plans & pricing
- ✅ **Tips**: Post weekly tips, nutrition, news
- ✅ **Certificates**: Add awards & achievements
- ✅ **Users**: View & manage user accounts
- ✅ **Messages**: View all contact form submissions
- ✅ **Beginner Program**: Customize 8-week program
- ✅ **Settings**: Edit site name, contact info, social links

---

## 🌍 LANGUAGE SWITCHING

Click the language switcher in the navigation:
- **English** (EN)
- **کوردی** (KU)

All content is bilingual!

---

## 🐛 TROUBLESHOOTING

### Database Connection Error
1. Check XAMPP MySQL is running
2. Verify `includes/config.php` credentials
3. Make sure database `gym_website` exists

### Images Not Uploading
1. Check folder permissions
2. Verify `assets/images/uploads/` exists
3. Check `php.ini`: `upload_max_filesize = 10M`

---

## 🎉 YOU'RE READY!

**Access Now**: `http://localhost/gym-project`

**Enjoy your new gym website! 🏋️‍♂️💪**
