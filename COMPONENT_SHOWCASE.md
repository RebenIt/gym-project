# MODERN ADMIN PANEL - COMPONENT SHOWCASE

## Quick Reference Guide for All UI Components

---

## 1. STAT CARDS

### Usage
```html
<div class="stat-card">
    <div class="stat-card-icon primary">
        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
            <!-- Your SVG icon -->
        </svg>
    </div>
    <div class="stat-card-value">1,234</div>
    <div class="stat-card-label">Total Users</div>
    <span class="stat-card-trend up">
        <svg><!-- Arrow icon --></svg>
        +12.5%
    </span>
</div>
```

### Icon Colors Available
- `primary` - Indigo/Pink gradient
- `success` - Green/Teal gradient
- `warning` - Orange/Red gradient
- `info` - Blue gradient
- `teal` - Teal/Cyan gradient
- `purple` - Purple/Indigo gradient

### Trend Directions
- `up` - Green with up arrow
- `down` - Red with down arrow

---

## 2. BUTTONS

### Primary Styles
```html
<button class="btn btn-primary">Primary Action</button>
<button class="btn btn-secondary">Secondary</button>
<button class="btn btn-success">Success</button>
<button class="btn btn-danger">Danger</button>
<button class="btn btn-info">Info</button>
```

### Sizes
```html
<button class="btn btn-primary btn-sm">Small</button>
<button class="btn btn-primary">Default</button>
<button class="btn btn-primary btn-lg">Large</button>
```

### Icon Buttons
```html
<button class="btn-icon btn-primary">
    <svg width="20" height="20">...</svg>
</button>
```

### With Icons
```html
<button class="btn btn-primary">
    <svg width="16" height="16">...</svg>
    Button Text
</button>
```

---

## 3. CARDS

### Basic Card
```html
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Card Title</h3>
    </div>
    <div class="card-body">
        Card content goes here
    </div>
    <div class="card-footer">
        Footer content
    </div>
</div>
```

### Card with Header Actions
```html
<div class="card">
    <div class="card-header" style="display: flex; justify-content: space-between;">
        <h3 class="card-title">Title</h3>
        <a href="#" class="btn btn-sm btn-secondary">View All</a>
    </div>
    <div class="card-body">
        Content
    </div>
</div>
```

---

## 4. DATA TABLES

### Modern Table
```html
<div class="data-table-container">
    <table class="data-table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>John Doe</td>
                <td>john@example.com</td>
                <td><span class="badge badge-success">Active</span></td>
                <td>
                    <div class="table-actions">
                        <button class="table-action-btn edit">
                            <svg width="16" height="16">...</svg>
                        </button>
                        <button class="table-action-btn delete">
                            <svg width="16" height="16">...</svg>
                        </button>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>
</div>
```

### Sortable Table
```html
<table data-sortable>
    <thead>
        <tr>
            <th data-sort>Name</th>
            <th data-sort>Email</th>
        </tr>
    </thead>
    <tbody>...</tbody>
</table>
```

---

## 5. FORMS

### Standard Form
```html
<div class="form-group">
    <label class="form-label required">Name</label>
    <input type="text" class="form-control" placeholder="Enter name">
    <small class="form-text">Helper text</small>
</div>
```

### Floating Label Form
```html
<div class="form-floating">
    <input type="text" id="name" placeholder=" ">
    <label for="name">Name</label>
</div>
```

### Validation States
```html
<input type="text" class="form-control is-valid">
<div class="valid-feedback">Looks good!</div>

<input type="text" class="form-control is-invalid">
<div class="invalid-feedback">Please fix this.</div>
```

### File Upload
```html
<div class="file-upload">
    <div class="file-upload-icon">
        <svg width="40" height="40">...</svg>
    </div>
    <div class="file-upload-text">Click to upload</div>
    <div class="file-upload-hint">PNG, JPG up to 10MB</div>
    <input type="file" hidden>
</div>
```

---

## 6. BADGES

### Badge Styles
```html
<span class="badge badge-primary">Primary</span>
<span class="badge badge-success">Success</span>
<span class="badge badge-warning">Warning</span>
<span class="badge badge-danger">Danger</span>
<span class="badge badge-info">Info</span>
<span class="badge badge-secondary">Secondary</span>
```

### Pulsing Badge (for notifications)
```html
<span class="badge badge-danger badge-pulse">5</span>
```

---

## 7. ALERTS

### Alert Types
```html
<div class="alert alert-success">
    <div class="alert-icon">
        <svg width="24" height="24">...</svg>
    </div>
    <div class="alert-content">
        <div class="alert-title">Success!</div>
        <div class="alert-message">Your changes have been saved.</div>
    </div>
</div>
```

Available types: `alert-success`, `alert-warning`, `alert-danger`, `alert-info`

### Auto-dismiss Alert
```html
<div class="alert alert-info" data-dismiss-after="5000">
    Will auto-close after 5 seconds
</div>
```

---

## 8. MODALS

### JavaScript Modal
```javascript
showModal({
    title: 'Delete Item',
    content: '<p>Are you sure you want to delete this item?</p>',
    confirmText: 'Delete',
    cancelText: 'Cancel',
    showCancel: true,
    onConfirm: function() {
        // Handle confirm
    },
    onCancel: function() {
        // Handle cancel
    }
});
```

### Confirm Dialog (Shorthand)
```javascript
confirmDialog('Are you sure?', function() {
    // User confirmed
    console.log('Confirmed!');
});
```

---

## 9. TOAST NOTIFICATIONS

### Show Toast
```javascript
// Success
showToast('Operation successful!', 'success');

// Error
showToast('Something went wrong', 'error');

// Info
showToast('Information message', 'info');

// Warning
showToast('Warning message', 'warning');

// Custom duration (5 seconds)
showToast('Message', 'success', 5000);
```

---

## 10. TABS

### HTML Structure
```html
<div class="tabs">
    <button class="tab active" data-tab="tab1">Tab 1</button>
    <button class="tab" data-tab="tab2">Tab 2</button>
    <button class="tab" data-tab="tab3">Tab 3</button>
</div>

<div class="tab-content active" id="tab1">
    Content for tab 1
</div>
<div class="tab-content" id="tab2">
    Content for tab 2
</div>
<div class="tab-content" id="tab3">
    Content for tab 3
</div>
```

---

## 11. PAGINATION

### Pagination Bar
```html
<div class="pagination">
    <button class="pagination-btn">
        <svg width="16" height="16"><!-- Left arrow --></svg>
    </button>
    <button class="pagination-btn">1</button>
    <button class="pagination-btn active">2</button>
    <button class="pagination-btn">3</button>
    <button class="pagination-btn">
        <svg width="16" height="16"><!-- Right arrow --></svg>
    </button>
</div>
```

---

## 12. EMPTY STATES

### Empty State
```html
<div class="empty-state">
    <div class="empty-state-icon">
        <svg width="48" height="48">...</svg>
    </div>
    <div class="empty-state-title">No items found</div>
    <div class="empty-state-description">
        Get started by creating your first item.
    </div>
    <button class="btn btn-primary">Create Item</button>
</div>
```

---

## 13. LOADING STATES

### Spinner
```html
<div class="spinner"></div>
<div class="spinner spinner-sm"></div>
<div class="spinner spinner-lg"></div>
```

### Loading Dots
```html
<div class="loading-dots">
    <span></span>
    <span></span>
    <span></span>
</div>
```

### Loading Bars
```html
<div class="loading-bars">
    <span></span>
    <span></span>
    <span></span>
    <span></span>
</div>
```

### Skeleton Loader
```html
<div class="skeleton skeleton-text"></div>
<div class="skeleton skeleton-title"></div>
<div class="skeleton skeleton-card"></div>
```

---

## 14. DROPDOWNS

### Dropdown Menu
```html
<div class="dropdown">
    <button class="btn btn-secondary" data-bs-toggle="dropdown">
        Options
        <svg width="14" height="14"><!-- Down arrow --></svg>
    </button>
    <ul class="dropdown-menu">
        <li><a class="dropdown-item" href="#">Action</a></li>
        <li><a class="dropdown-item" href="#">Another action</a></li>
        <li><hr class="dropdown-divider"></li>
        <li><a class="dropdown-item" href="#">Separated link</a></li>
    </ul>
</div>
```

---

## 15. PROGRESS BARS

### Progress Bar
```html
<div class="progress-bar">
    <div class="progress-bar-fill" style="width: 60%;"></div>
</div>
```

---

## 16. GRIDS

### Stats Grid (Auto-fit)
```html
<div class="stats-grid">
    <div class="stat-card">...</div>
    <div class="stat-card">...</div>
    <div class="stat-card">...</div>
</div>
```

### Two Column Grid
```html
<div class="two-column-grid">
    <div class="card">...</div>
    <div class="card">...</div>
</div>
```

### Three Column Grid
```html
<div class="three-column-grid">
    <div class="card">...</div>
    <div class="card">...</div>
    <div class="card">...</div>
</div>
```

### Content Grid (Auto-fill)
```html
<div class="content-grid">
    <div class="card">...</div>
    <div class="card">...</div>
    <div class="card">...</div>
</div>
```

---

## 17. ANIMATIONS

### Animation Classes
```html
<div class="animate-fade-in">Fades in</div>
<div class="animate-slide-in-up">Slides up</div>
<div class="animate-bounce-in">Bounces in</div>
<div class="animate-scale-in">Scales in</div>
<div class="animate-pulse">Pulses</div>
<div class="animate-float">Floats up and down</div>
```

### Staggered Animations
```html
<div class="stagger-fade-in">
    <div>Item 1 (fades in first)</div>
    <div>Item 2 (fades in second)</div>
    <div>Item 3 (fades in third)</div>
</div>

<div class="stagger-slide-up">
    <div>Item 1</div>
    <div>Item 2</div>
    <div>Item 3</div>
</div>
```

### Hover Effects
```html
<div class="hover-lift">Lifts on hover</div>
<div class="hover-scale">Scales on hover</div>
<div class="hover-glow">Glows on hover</div>
```

---

## 18. FLOATING ACTION BUTTON

### FAB
```html
<button class="fab">
    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <line x1="12" y1="5" x2="12" y2="19"></line>
        <line x1="5" y1="12" x2="19" y2="12"></line>
    </svg>
</button>
```

---

## 19. UTILITY FEATURES

### Confirm Delete
```html
<a href="delete.php?id=123" data-confirm-delete="Are you sure?">Delete</a>
```

### Copy to Clipboard
```javascript
copyToClipboard('Text to copy');
// Shows success toast automatically
```

### Tooltip
```html
<button data-tooltip="This is a tooltip">Hover me</button>
```

---

## 20. KEYBOARD SHORTCUTS

Built-in shortcuts:
- `Ctrl/Cmd + K` - Focus search bar
- `Ctrl/Cmd + B` - Toggle sidebar
- `ESC` - Close modals

---

## CSS VARIABLES QUICK REFERENCE

### Colors
```css
var(--primary)
var(--secondary)
var(--success)
var(--warning)
var(--error)
var(--info)
```

### Gradients
```css
var(--gradient-primary)
var(--gradient-success)
var(--gradient-warning)
var(--gradient-dark)
```

### Spacing
```css
var(--spacing-xs)   /* 4px */
var(--spacing-sm)   /* 8px */
var(--spacing-md)   /* 16px */
var(--spacing-lg)   /* 24px */
var(--spacing-xl)   /* 32px */
```

### Shadows
```css
var(--shadow-xs)
var(--shadow-sm)
var(--shadow-md)
var(--shadow-lg)
var(--shadow-xl)
```

### Border Radius
```css
var(--border-radius)      /* 8px */
var(--border-radius-lg)   /* 12px */
var(--border-radius-xl)   /* 16px */
var(--border-radius-full) /* 9999px */
```

### Transitions
```css
var(--transition-fast)  /* 150ms */
var(--transition-base)  /* 250ms */
var(--transition-slow)  /* 350ms */
```

---

## RESPONSIVE HELPERS

### Hide on Mobile
```html
<div class="d-none d-md-block">Hidden on mobile</div>
```

### Show only on Mobile
```html
<div class="d-md-none">Only on mobile</div>
```

---

## QUICK TIPS

1. **Always use `var(--color)` instead of hex codes** for consistency
2. **Add `data-animate="fade-in"` to elements** for scroll animations
3. **Use `showToast()` instead of `alert()`** for better UX
4. **Add `data-confirm-delete` to delete links** for confirmation
5. **Use `.card` for all content containers** for consistency
6. **Apply `.hover-lift` to cards** for nice hover effect
7. **Use `.stagger-slide-up` on parent** for list animations
8. **Always include icon with buttons** for better UX
9. **Use `.empty-state` when no data** instead of plain text
10. **Apply `.animate-fade-in` to modals** for smooth entry

---

**That's it! You have all the components you need to build any admin page!**
