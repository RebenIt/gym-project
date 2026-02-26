# 💻 Code Explanation Guide - Simple Version

## 📚 Table of Contents
1. [How PHP Works (Basics)](#how-php-works)
2. [Database Concepts](#database-concepts)
3. [How the Admin Panel Works](#admin-panel)
4. [How User Dashboard Works](#user-dashboard)
5. [Code Examples Explained](#code-examples)
6. [Common Functions Explained](#common-functions)

---

## 🎯 How PHP Works (Basics)

### What is PHP?
PHP is a programming language that runs on the server (your computer/hosting) and creates HTML pages dynamically.

### Simple Example:

```php
<?php
// This is PHP code - it runs BEFORE the page is sent to browser
$name = "FitZone Gym";
echo "Welcome to " . $name;
?>
```

**Output in browser:** Welcome to FitZone Gym

### How Our Website Works:

```
User visits → Server runs PHP → PHP talks to Database →
PHP creates HTML → Browser shows webpage
```

---

## 🗄️ Database Concepts

### What is a Database?
Think of it like an Excel file with multiple sheets (tables).

### Our Main Tables:

#### 1. **users** table (like a contact list)
```
| id | username | email | password |
|----|----------|-------|----------|
| 1  | john     | j@... | ****     |
| 2  | sarah    | s@... | ****     |
```

#### 2. **games** table (exercises)
```
| id | name | difficulty | muscle_group |
|----|------|------------|--------------|
| 1  | Push-ups | beginner | Chest |
| 2  | Squats   | intermediate | Legs |
```

### How We Get Data:

```php
// This asks database: "Give me all exercises"
$exercises = fetchAll("SELECT * FROM games");

// This asks: "Give me exercise with id=1"
$exercise = fetchOne("SELECT * FROM games WHERE id = 1");
```

---

## 🔧 Admin Panel - How It Works

### File: `admin/games.php` (Manage Exercises)

Let's break down this page step by step:

```php
<?php
// STEP 1: Include security check
require_once '../includes/auth.php';
requireAdminLogin(); // Make sure user is admin

// STEP 2: Get action from URL
// URL: games.php?action=add
$action = $_GET['action'] ?? 'list'; // Default is 'list'

// STEP 3: Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // User clicked submit button
    $name = sanitize($_POST['name']); // Get exercise name
    $difficulty = sanitize($_POST['difficulty']); // Get difficulty

    // Save to database
    query("INSERT INTO games (name, difficulty) VALUES (?, ?)",
          [$name, $difficulty]);

    // Show success message
    setFlash('success', 'Exercise added!');
    redirect('games.php'); // Go back to list
}

// STEP 4: Show the page
if ($action === 'list') {
    // Show all exercises
    $games = fetchAll("SELECT * FROM games");
    // ... display table HTML
} elseif ($action === 'add') {
    // Show add form
    // ... display form HTML
}
?>
```

### What Happens When Admin Adds Exercise:

1. **Admin clicks "Add Exercise" button**
   - URL changes to: `games.php?action=add`
   - PHP shows add form

2. **Admin fills form and clicks Submit**
   - Form data sent via POST
   - PHP validates data (checks if empty)
   - PHP saves to database
   - PHP shows success message
   - PHP redirects to list page

3. **Admin sees new exercise in list**
   - PHP loads all exercises from database
   - PHP creates HTML table
   - Browser displays table

---

## 👤 User Dashboard - How It Works

### File: `user/create-list.php` (Create Workout List)

```php
<?php
// STEP 1: Make sure user is logged in
require_once '../includes/auth.php';
requireUserLogin();

// STEP 2: Get current user info
$user = getCurrentUser(); // Gets user from session

// STEP 3: Get all available exercises
$exercises = fetchAll("SELECT * FROM games WHERE is_active = 1");

// STEP 4: When user submits form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $listTitle = sanitize($_POST['title']);
    $selectedExercises = $_POST['exercises']; // Array of exercise IDs

    // Create the list
    query("INSERT INTO user_lists (user_id, title) VALUES (?, ?)",
          [$user['id'], $listTitle]);

    // Get the new list ID
    $listId = db()->lastInsertId();

    // Add exercises to the list
    foreach ($selectedExercises as $exerciseId) {
        query("INSERT INTO user_list_games (list_id, game_id) VALUES (?, ?)",
              [$listId, $exerciseId]);
    }

    // Success!
    redirect('my-lists.php');
}
?>
```

### What Happens When User Creates Workout List:

1. **User clicks "Create New List"**
   - PHP checks if user is logged in
   - PHP loads all exercises from database
   - PHP shows form with checkboxes

2. **User selects exercises and clicks Save**
   - PHP creates new list in `user_lists` table
   - PHP adds each selected exercise to `user_list_games` table
   - PHP redirects to "My Lists" page

3. **User sees their new list**
   - PHP loads all lists for this user
   - PHP displays them in cards

---

## 📝 Code Examples Explained

### Example 1: Login System

```php
// File: includes/auth.php

function login($username, $password) {
    // STEP 1: Find user in database
    $user = fetchOne("SELECT * FROM users WHERE username = ?", [$username]);

    // STEP 2: Check if user exists
    if (!$user) {
        return ['success' => false, 'message' => 'User not found'];
    }

    // STEP 3: Check if password is correct
    if (!password_verify($password, $user['password'])) {
        return ['success' => false, 'message' => 'Wrong password'];
    }

    // STEP 4: Login successful - save to session
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];

    return ['success' => true];
}
```

**How it works:**
1. User enters username "john" and password "12345"
2. PHP searches database for username "john"
3. PHP compares password with stored hash
4. If correct, PHP saves user info in session
5. User is now logged in!

---

### Example 2: Upload Image

```php
// File: includes/functions.php

function uploadFile($file) {
    // STEP 1: Check if file was uploaded
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'message' => 'Upload failed'];
    }

    // STEP 2: Check file type (only images)
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    if (!in_array($file['type'], $allowedTypes)) {
        return ['success' => false, 'message' => 'Only images allowed'];
    }

    // STEP 3: Check file size (max 5MB)
    if ($file['size'] > 5 * 1024 * 1024) {
        return ['success' => false, 'message' => 'File too large'];
    }

    // STEP 4: Generate unique filename
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid() . '.' . $extension; // e.g., "65a1b2c3d4e5.jpg"

    // STEP 5: Move file to uploads folder
    $uploadPath = __DIR__ . '/../assets/uploads/' . $filename;
    move_uploaded_file($file['tmp_name'], $uploadPath);

    // STEP 6: Return filename to save in database
    return ['success' => true, 'filename' => $filename];
}
```

**How it works:**
1. User selects image file
2. PHP checks if upload was successful
3. PHP validates file type (must be image)
4. PHP checks file size (not too big)
5. PHP creates unique name (prevents duplicates)
6. PHP moves file to `/assets/uploads/` folder
7. PHP returns filename to save in database

---

### Example 3: CSRF Protection (Security)

```php
// What is CSRF?
// It's when a hacker tricks your browser into submitting forms

// Generate token when showing form:
function csrfField() {
    $token = bin2hex(random_bytes(32)); // Random string
    $_SESSION['csrf_token'] = $token; // Save in session
    return '<input type="hidden" name="csrf_token" value="' . $token . '">';
}

// Check token when form is submitted:
function verifyCSRFToken($token) {
    // Compare submitted token with saved token
    return isset($_SESSION['csrf_token']) &&
           hash_equals($_SESSION['csrf_token'], $token);
}
```

**How it works:**
1. When admin opens "Add Exercise" form, PHP creates random token
2. Token is hidden in form
3. Token is also saved in session
4. When form is submitted, PHP checks if tokens match
5. If they don't match, request is rejected (hacker blocked!)

---

## 🔧 Common Functions Explained

### 1. Database Functions

```php
// Get multiple rows
$users = fetchAll("SELECT * FROM users");
// Returns: [['id'=>1, 'name'=>'John'], ['id'=>2, 'name'=>'Sarah']]

// Get single row
$user = fetchOne("SELECT * FROM users WHERE id = ?", [1]);
// Returns: ['id'=>1, 'name'=>'John', 'email'=>'j@...']

// Insert/Update/Delete
query("INSERT INTO users (name, email) VALUES (?, ?)", ['John', 'j@gmail.com']);
query("UPDATE users SET name = ? WHERE id = ?", ['John Doe', 1]);
query("DELETE FROM users WHERE id = ?", [1]);
```

### 2. Security Functions

```php
// Clean user input (prevent XSS attacks)
$clean = sanitize($_POST['name']);
// Input: "<script>alert('hack')</script>"
// Output: "&lt;script&gt;alert('hack')&lt;/script&gt;"

// Escape for display (prevent HTML injection)
echo e($userInput);
// Converts < > to &lt; &gt;

// Hash password (one-way encryption)
$hashed = password_hash('12345', PASSWORD_BCRYPT);
// Output: "$2y$10$abcdef..." (cannot be reversed!)

// Verify password
password_verify('12345', $hashed); // Returns true
password_verify('wrong', $hashed); // Returns false
```

### 3. Session Functions

```php
// Save data in session (stays between pages)
$_SESSION['user_id'] = 1;
$_SESSION['username'] = 'john';

// Get data from session
$userId = $_SESSION['user_id'] ?? null;

// Check if logged in
if (isset($_SESSION['user_id'])) {
    echo "You are logged in!";
}

// Logout (destroy session)
session_destroy();
```

### 4. Flash Messages

```php
// Set message (saved in session)
setFlash('success', 'Exercise added successfully!');
setFlash('error', 'Something went wrong!');

// Display and remove message (shown once)
displayFlash();
// Shows green box for success, red box for error
```

### 5. File Upload Functions

```php
// Upload file
$result = uploadFile($_FILES['image']);
if ($result['success']) {
    $filename = $result['filename']; // Save this in database
}

// Get full URL to uploaded file
$url = uploadUrl('65a1b2c3d4e5.jpg');
// Returns: "/assets/uploads/65a1b2c3d4e5.jpg"
```

---

## 🎨 How the Design Works

### Modern Gradient Cards

```php
<!-- This creates a beautiful gradient card -->
<div style="background: linear-gradient(135deg, #6366f1 0%, #ec4899 100%);
            color: white;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(99, 102, 241, 0.3);">
    <h1>Exercises</h1>
    <p>Manage all gym exercises</p>
</div>
```

**Breakdown:**
- `linear-gradient(135deg, ...)` → Creates smooth color transition
- `#6366f1` (Indigo) → First color
- `#ec4899` (Pink) → Second color
- `border-radius: 12px` → Rounded corners
- `box-shadow` → Soft shadow effect

### Responsive Grid Layout

```php
<!-- This creates a responsive grid (automatic columns) -->
<div style="display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;">
    <!-- Each card automatically fits -->
    <div>Card 1</div>
    <div>Card 2</div>
    <div>Card 3</div>
</div>
```

**What this means:**
- Desktop (wide screen): Shows 4 cards per row
- Tablet: Shows 2 cards per row
- Mobile: Shows 1 card per row
- All automatic! No media queries needed

---

## 🔍 How Forms Work

### Step-by-Step Form Process:

```php
<!-- 1. THE FORM -->
<form method="POST" enctype="multipart/form-data">
    <?php echo csrfField(); ?> <!-- Security token -->

    <input type="text" name="exercise_name" required>
    <input type="file" name="image">

    <button type="submit">Save Exercise</button>
</form>

<!-- 2. THE PHP HANDLER -->
<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $name = sanitize($_POST['exercise_name']);

    // Handle file upload
    if (!empty($_FILES['image']['name'])) {
        $upload = uploadFile($_FILES['image']);
        $imageName = $upload['filename'];
    }

    // Save to database
    query("INSERT INTO games (name, image) VALUES (?, ?)",
          [$name, $imageName]);

    // Success message
    setFlash('success', 'Exercise added!');
    redirect('games.php');
}
?>
```

**The Flow:**
1. User fills form
2. User clicks Submit
3. Browser sends data to server (POST request)
4. PHP receives data in `$_POST` array
5. PHP validates and sanitizes data
6. PHP saves to database
7. PHP shows success message
8. PHP redirects to list page

---

## 🌐 How Bilingual System Works

### Database Storage:

```
| id | name | name_ku | description | description_ku |
|----|------|---------|-------------|----------------|
| 1  | Push-ups | پاش ئەپ | English text | کوردی دەق |
```

### Display Logic:

```php
<?php
// Get user's language preference
$lang = $_SESSION['language'] ?? 'en'; // Default: English

// Function to display bilingual text
function __($text, $textKu) {
    global $lang;
    if ($lang === 'ku' && !empty($textKu)) {
        return $textKu; // Show Kurdish
    }
    return $text; // Show English
}

// Usage:
echo __($exercise['name'], $exercise['name_ku']);
// If English: Shows "Push-ups"
// If Kurdish: Shows "پاش ئەپ"
?>
```

---

## 📊 How Dashboard Statistics Work

```php
<?php
// Count total exercises
$totalExercises = count($exercises);

// Count only active exercises
$activeExercises = count(array_filter($exercises, function($ex) {
    return $ex['is_active'] == 1;
}));

// Count featured exercises
$featuredExercises = count(array_filter($exercises, function($ex) {
    return $ex['is_featured'] == 1;
}));

// Calculate average
$totalDuration = array_sum(array_column($exercises, 'duration_minutes'));
$avgDuration = $totalExercises > 0 ? round($totalDuration / $totalExercises) : 0;
?>

<!-- Display in cards -->
<div>Total: <?php echo $totalExercises; ?></div>
<div>Active: <?php echo $activeExercises; ?></div>
<div>Featured: <?php echo $featuredExercises; ?></div>
<div>Avg Duration: <?php echo $avgDuration; ?> min</div>
```

---

## 🎓 Key Concepts Summary

### 1. **MVC Pattern** (Sort of)
- **Model** (Database) → `includes/database.php`
- **View** (HTML) → The `.php` pages
- **Controller** (Logic) → Form handlers in pages

### 2. **CRUD Operations**
- **Create** → INSERT query (Add new)
- **Read** → SELECT query (View existing)
- **Update** → UPDATE query (Modify existing)
- **Delete** → DELETE query (Remove)

### 3. **Security Principles**
- ✅ Never trust user input → Always sanitize
- ✅ Use prepared statements → Prevent SQL injection
- ✅ Hash passwords → Never store plain text
- ✅ CSRF tokens → Prevent cross-site attacks
- ✅ Validate file uploads → Check type and size

### 4. **Session Management**
- Session stores user info between pages
- Session data saved on server (secure)
- Session ID stored in browser cookie
- Session destroyed on logout

---

**Remember:**
- PHP runs on server (backend)
- JavaScript runs in browser (frontend)
- Database stores all data permanently
- Sessions store temporary user data

**This is simplified for easy understanding!**
The actual code has more error handling and features.
