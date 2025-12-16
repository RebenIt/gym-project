# FitZone Gym Management System

A modern, bilingual (English/Kurdish Sorani) gym website with admin panel.

## 🚀 Features

### Public Features
- **Bilingual Support** - English and Kurdish (Sorani) with RTL support
- **Homepage** - Hero, Services, About, Plans, Reviews, Contact sections
- **Games/Exercises** - Browse all exercises with videos and instructions
- **Trainers** - View trainer profiles
- **Tips & News** - Weekly fitness tips and nutrition advice
- **Beginner Program** - 2-month starter program for new players
- **Certificates** - Hall awards and certifications
- **Contact Form** - Messages saved to database

### User Features
- **Registration & Login** - User account system
- **Personal Dashboard** - View stats and activity
- **Workout Lists** - Create multiple personalized game lists
- **Daily Notes** - Track mood, weight, and workout status
- **Profile Management** - Update personal information

### Admin Features
- **Full Dashboard** - Statistics and overview
- **User Management** - View and manage users
- **Games Management** - Add/Edit/Delete exercises with:
  - Bilingual content (EN/KU)
  - Image upload
  - YouTube video links
  - Difficulty levels
  - Muscle groups
  - Instructions
- **Trainers Management** - Manage trainer profiles
- **Services Management** - Edit services
- **Plans Management** - Update pricing and features
- **Tips/News Management** - Post weekly tips
- **Beginner Program** - Manage starter exercises
- **Certificates** - Manage awards
- **Reviews** - Approve/manage reviews
- **Messages** - View contact form submissions
- **Settings** - Update all website content

## 📦 Installation

### Requirements
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache/Nginx web server
- XAMPP/WAMP/MAMP for local development

### Setup Steps

1. **Copy Files**
   - Extract files to your web server directory (e.g., `htdocs/gym-project`)

2. **Create Database**
   - Open phpMyAdmin: http://localhost/phpmyadmin
   - Click "New" → Create database: `gym_website`
   - Select collation: `utf8mb4_unicode_ci`
   - Click on the database → Import tab
   - Upload: `database/setup.sql`
   - Click "Go"

3. **Configure Database Connection**
   - Open `includes/config.php`
   - Update these values if needed:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'gym_website');
   define('DB_USER', 'root');
   define('DB_PASS', ''); // Your MySQL password
   define('SITE_URL', 'http://localhost/gym-project');
   ```

4. **Set Permissions**
   - Make `assets/uploads` writable:
   ```bash
   chmod 755 assets/uploads
   ```

5. **Access the Website**
   - Website: http://localhost/gym-project
   - Admin Panel: http://localhost/gym-project/admin

### Default Admin Credentials
- **Email:** admin@fitzone.com
- **Password:** admin123

⚠️ **IMPORTANT:** Change the admin password after first login!

## 📁 Folder Structure

```
gym-project/
├── admin/                 # Admin panel
│   ├── includes/          # Admin includes (sidebar)
│   ├── index.php          # Admin dashboard
│   ├── login.php          # Admin login
│   ├── games.php          # Games management
│   ├── trainers.php       # Trainers management
│   └── ...
├── assets/
│   ├── css/               # Stylesheets
│   ├── js/                # JavaScript files
│   ├── images/            # Static images
│   └── uploads/           # User uploads
├── database/
│   └── setup.sql          # Database setup file
├── includes/
│   ├── config.php         # Configuration
│   ├── functions.php      # Helper functions
│   └── auth.php           # Authentication
├── pages/                 # Public pages
├── user/                  # User dashboard area
│   ├── dashboard.php
│   └── ...
├── index.php              # Homepage
├── login.php              # User login
├── register.php           # User registration
└── README.md
```

## 🎨 Customization

### Change Colors
Edit `assets/css/style.css`:
```css
:root {
    --primary: #f97316;        /* Main orange color */
    --primary-dark: #ea580c;
    --dark: #0a0a0a;           /* Background */
}
```

### Change Site Name
1. Login to Admin Panel
2. Go to Settings
3. Update "Site Name" field

### Add New Language
The system is built for easy language expansion. Contact for multi-language support.

## 🔐 Security Notes

1. Change default admin password immediately
2. Use strong passwords for all accounts
3. Keep PHP and MySQL updated
4. Set proper file permissions
5. Use HTTPS in production

## 📝 API Endpoints

- `POST /submit-contact.php` - Contact form submission

## 🆘 Support

For issues or customization requests, please contact the developer.

## 📄 License

This project is proprietary. All rights reserved.

---

Built with ❤️ for FitZone Gym
