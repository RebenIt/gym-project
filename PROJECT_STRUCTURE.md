# 📁 FitZone Gym Website - Project Structure

## 📋 Overview
This document explains the complete file structure of the FitZone Gym Management System, making it easy to understand where everything is located and what each file does.

---

## 🗂️ Main Folder Structure

```
gym-project/
├── admin/              → Admin Dashboard (Management Panel)
├── user/               → User Dashboard (Member Area)
├── assets/             → CSS, JavaScript, Images
├── includes/           → Shared PHP Files (Database, Functions)
├── *.php              → Public Pages (Homepage, About, etc.)
└── Documentation/      → Project Documentation
```

---

## 📂 Detailed File Breakdown

### 1️⃣ **ROOT DIRECTORY** (Main Folder)
These are the public-facing pages that visitors see:

| File | Purpose | What it does |
|------|---------|--------------|
| `index.php` | Homepage | First page users see - shows featured exercises, plans, trainers |
| `about.php` | About Us Page | Information about the gym |
| `games.php` | Exercises List | Shows all available exercises/workouts |
| `game-detail.php` | Exercise Details | Detailed view of a single exercise with video |
| `trainers.php` | Trainers List | Shows all gym trainers |
| `trainer-detail.php` | Trainer Profile | Detailed view of a single trainer |
| `tips.php` | Fitness Tips | Blog/news articles about fitness |
| `tip-detail.php` | Tip Details | Full article view |
| `certificates.php` | Certificates & Awards | Shows gym achievements |
| `beginners.php` | Beginner Programs | Special programs for new members |
| `contact.php` | Contact Form | Users can send messages |
| `logout.php` | Logout | Logs out users |

---

### 2️⃣ **ADMIN FOLDER** (`/admin/`)
Complete management system for administrators:

| File | Purpose | Features |
|------|---------|----------|
| `index.php` | Admin Dashboard | Statistics, charts, overview |
| `games.php` | Manage Exercises | Add/Edit/Delete exercises |
| `trainers.php` | Manage Trainers | Add/Edit/Delete trainers |
| `tips.php` | Manage Tips | Add/Edit/Delete fitness articles |
| `certificates.php` | Manage Certificates | Add/Edit/Delete awards |
| `plans.php` | Manage Plans | Add/Edit/Delete membership plans |
| `users.php` | Manage Users | View/Edit/Delete members |
| `beginners.php` | Manage Beginner Programs | Add/Edit programs |
| `pages.php` | Manage Pages | Edit About, Privacy pages |
| `services.php` | Manage Services | Add/Edit gym services |
| `settings.php` | Site Settings | Contact info, social media |
| `messages.php` | Contact Messages | View messages from contact form |
| `logout.php` | Admin Logout | Logs out administrator |

**Admin Subfolders:**
- `admin/includes/` → Shared admin files
  - `header.php` → Top navigation
  - `sidebar.php` → Left menu
  - `footer.php` → Bottom section
  - `topbar.php` → Top bar with notifications

---

### 3️⃣ **USER FOLDER** (`/user/`)
Member dashboard where users manage their workouts:

| File | Purpose | Features |
|------|---------|----------|
| `dashboard.php` | User Dashboard | Overview, stats, quick actions |
| `profile.php` | Profile Settings | Edit personal information |
| `my-lists.php` | My Workout Lists | View all saved workout lists |
| `create-list.php` | Create List | Create new workout list |
| `edit-list.php` | Edit List | Modify existing workout list |
| `view-list.php` | View List | See exercises in a list |
| `notes.php` | Exercise Notes | Personal notes about exercises |
| `save-note.php` | Save Note | Backend for saving notes |
| `logout.php` | User Logout | Logs out user |

**User Subfolders:**
- `user/includes/` → Shared user files
  - `header.php` → Navigation
  - `sidebar.php` → Left menu
  - `footer.php` → Footer

---

### 4️⃣ **INCLUDES FOLDER** (`/includes/`)
Core system files used throughout the website:

| File | Purpose | Critical Functions |
|------|---------|-------------------|
| `config.php` | Database Connection | Connects to MySQL database |
| `auth.php` | Authentication | Login, registration, session management |
| `functions.php` | Helper Functions | Commonly used functions |
| `database.php` | Database Functions | Query helpers |
| `dynamic-colors.php` | Theme Colors | Color customization |
| `header.php` | Public Header | Navigation for public pages |
| `navbar.php` | Navigation Bar | Main menu |
| `footer.php` | Public Footer | Bottom section |

---

### 5️⃣ **ASSETS FOLDER** (`/assets/`)
All design and media files:

```
assets/
├── css/                    → Stylesheets
│   ├── admin.css          → Admin panel styles
│   ├── admin-modern.css   → Modern admin design
│   ├── admin-components.css → Reusable components
│   ├── admin-animations.css → Animations
│   ├── variables.css      → Color variables
│   └── style.css          → Public site styles
│
├── js/                     → JavaScript files
│   ├── admin-modern.js    → Admin interactions
│   └── main.js            → Public site scripts
│
├── uploads/                → User uploaded files
│   ├── exercises/         → Exercise images
│   ├── trainers/          → Trainer photos
│   ├── certificates/      → Certificate images
│   └── tips/              → Article images
│
└── images/                 → Site graphics
    └── logo.png           → Gym logo
```

---

## 🎨 **Design System**

### Color Schemes by Section:
Each admin page has unique gradient colors:

| Page | Colors | Gradient |
|------|--------|----------|
| Dashboard | Blue/Cyan | #0ea5e9 → #06b6d4 |
| Exercises | Indigo/Pink | #6366f1 → #ec4899 |
| Trainers | Purple/Pink | #8b5cf6 → #ec4899 |
| Tips | Orange/Red | #f59e0b → #dc2626 |
| Plans | Purple/Pink | #8b5cf6 → #ec4899 |
| Certificates | Indigo/Pink | #6366f1 → #ec4899 |
| Settings | Blue/Indigo | #0ea5e9 → #6366f1 |
| Beginners | Green/Emerald | #10b981 → #059669 |
| Pages | Purple/Indigo | #6366f1 → #8b5cf6 |

---

## 🗄️ **Database Structure**

### Main Tables:

1. **users** → Member accounts
2. **admins** → Administrator accounts
3. **games** → Exercises/workouts
4. **trainers** → Gym trainers
5. **tips** → Fitness articles/blog
6. **certificates** → Awards and achievements
7. **plans** → Membership pricing plans
8. **services** → Gym services
9. **pages** → CMS pages (About, Privacy, etc.)
10. **beginner_programs** → Programs for beginners
11. **settings** → Site configuration
12. **contact_messages** → Messages from contact form
13. **user_lists** → User workout lists
14. **user_list_games** → Exercises in lists (many-to-many)
15. **user_notes** → Personal notes on exercises

---

## 🔐 **User Types**

### 1. **Public Visitors** (Not logged in)
- Can view: exercises, trainers, tips, certificates
- Cannot: save workouts, access dashboard

### 2. **Registered Users** (Logged in members)
- Can: create workout lists, save notes, manage profile
- Access: User dashboard (`/user/`)

### 3. **Administrators** (Logged in admins)
- Can: manage all content, view analytics, edit settings
- Access: Admin dashboard (`/admin/`)

---

## 🌐 **Bilingual Support**

The system supports **English** and **Kurdish**:

- Most content has two fields: `title` and `title_ku`
- Kurdish text displays right-to-left (RTL)
- Users can see content in their preferred language
- All forms have English and Kurdish input fields

---

## 📱 **Responsive Design**

- ✅ Desktop (1920px+)
- ✅ Laptop (1366px)
- ✅ Tablet (768px)
- ✅ Mobile (375px+)

Modern CSS Grid and Flexbox used throughout.

---

## 🚀 **Key Features**

### Public Features:
1. Browse exercises with difficulty levels
2. View trainer profiles
3. Read fitness tips and articles
4. See gym certificates and awards
5. Beginner-friendly workout programs
6. Contact form

### User Features:
1. Personal dashboard
2. Create custom workout lists
3. Save exercise notes
4. Track workout history
5. Profile management

### Admin Features:
1. Complete content management
2. Statistics and analytics
3. User management
4. Message inbox
5. Site settings control
6. Bulk operations

---

## 🔧 **Technology Stack**

| Technology | Version | Purpose |
|------------|---------|---------|
| PHP | 7.4+ | Backend logic |
| MySQL | 5.7+ | Database |
| HTML5 | - | Structure |
| CSS3 | - | Styling |
| JavaScript | ES6 | Interactivity |
| Bootstrap | 5.3 | UI framework |
| Chart.js | 3.9 | Dashboard charts |

---

## 📝 **Important Notes**

### For Beginners:
1. **Never edit files in `/includes/config.php`** without backup
2. **Database credentials** are in `config.php`
3. **Uploads** go to `/assets/uploads/`
4. **Test changes** on localhost first

### Security Features:
- ✅ CSRF Protection (all forms)
- ✅ SQL Injection Prevention (prepared statements)
- ✅ XSS Protection (sanitized outputs)
- ✅ Password Hashing (bcrypt)
- ✅ Session Management
- ✅ File Upload Validation

---

## 🎯 **Quick Navigation**

**Need to...**
- Add exercise? → `/admin/games.php?action=add`
- Add trainer? → `/admin/trainers.php?action=add`
- Edit homepage? → `/index.php`
- Change contact info? → `/admin/settings.php`
- View messages? → `/admin/messages.php`
- Manage users? → `/admin/users.php`

---

**Last Updated:** January 2026
**Project:** FitZone Gym Management System
**Version:** 1.0
