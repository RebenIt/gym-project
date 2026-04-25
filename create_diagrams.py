#!/usr/bin/env python3
"""
Create System Architecture Diagram and Website Flowchart for FitZone project.
"""

import matplotlib
matplotlib.use('Agg')
import matplotlib.pyplot as plt
import matplotlib.patches as mpatches
from matplotlib.patches import FancyBboxPatch, FancyArrowPatch
import numpy as np

# ============================================================
# DIAGRAM 1: SYSTEM ARCHITECTURE
# ============================================================
def create_architecture_diagram():
    fig, ax = plt.subplots(1, 1, figsize=(14, 10))
    ax.set_xlim(0, 14)
    ax.set_ylim(0, 10)
    ax.axis('off')
    fig.patch.set_facecolor('white')

    # Title
    ax.text(7, 9.6, 'FitZone System Architecture', fontsize=22, fontweight='bold',
            ha='center', va='center', color='#1a1a2e')

    # Colors
    client_color = '#e8f4fd'
    client_border = '#2196F3'
    server_color = '#fff3e0'
    server_border = '#FF9800'
    db_color = '#e8f5e9'
    db_border = '#4CAF50'
    box_color = '#f5f5f5'
    arrow_color = '#555555'

    # ============================
    # CLIENT TIER (Top)
    # ============================
    # Main box
    rect = FancyBboxPatch((0.5, 6.8), 13, 2.4, boxstyle="round,pad=0.15",
                          facecolor=client_color, edgecolor=client_border, linewidth=2.5)
    ax.add_patch(rect)
    ax.text(7, 9.0, 'CLIENT TIER (Frontend)', fontsize=14, fontweight='bold',
            ha='center', va='center', color=client_border)

    # Browser box
    items_client = [
        (2.5, 7.6, 'Web Browser\n(Chrome / Firefox / Edge)', '#bbdefb'),
        (7, 7.6, 'Bootstrap 5.3\nHTML5 / CSS3', '#bbdefb'),
        (11.5, 7.6, 'Vanilla JavaScript\n(ES6+)', '#bbdefb'),
    ]
    for x, y, txt, clr in items_client:
        rect = FancyBboxPatch((x-1.5, y-0.45), 3, 0.9, boxstyle="round,pad=0.1",
                              facecolor=clr, edgecolor=client_border, linewidth=1.5)
        ax.add_patch(rect)
        ax.text(x, y, txt, fontsize=9, ha='center', va='center', fontweight='bold')

    # Arrow: Client -> Server
    ax.annotate('', xy=(7, 6.3), xytext=(7, 6.8),
                arrowprops=dict(arrowstyle='->', color=arrow_color, lw=2.5))
    ax.text(8.2, 6.55, 'HTTP Requests / Responses', fontsize=8, ha='center',
            va='center', color=arrow_color, style='italic')

    # ============================
    # SERVER TIER (Middle)
    # ============================
    rect = FancyBboxPatch((0.5, 3.2), 13, 3.0, boxstyle="round,pad=0.15",
                          facecolor=server_color, edgecolor=server_border, linewidth=2.5)
    ax.add_patch(rect)
    ax.text(7, 6.0, 'SERVER TIER (Backend - Apache / PHP 8.2)', fontsize=14,
            fontweight='bold', ha='center', va='center', color=server_border)

    # Server components
    server_items = [
        (2.5, 5.2, 'Public Pages\nindex.php, games.php\ntrainers.php, tips.php\ncontact.php, plans.php', '#ffe0b2'),
        (7, 5.2, 'User Dashboard\nuser/dashboard.php\nuser/my-lists.php\nuser/notes.php\nuser/profile.php', '#ffe0b2'),
        (11.5, 5.2, 'Admin Panel\nadmin/dashboard.php\nadmin/games.php\nadmin/trainers.php\nadmin/settings.php', '#ffe0b2'),
    ]
    for x, y, txt, clr in server_items:
        rect = FancyBboxPatch((x-1.8, y-0.7), 3.6, 1.4, boxstyle="round,pad=0.1",
                              facecolor=clr, edgecolor=server_border, linewidth=1.5)
        ax.add_patch(rect)
        ax.text(x, y, txt, fontsize=8, ha='center', va='center', fontweight='bold')

    # Core modules
    core_items = [
        (3.5, 3.8, 'config.php\n(Database)', '#ffcc80'),
        (7, 3.8, 'functions.php\n(30+ helpers)', '#ffcc80'),
        (10.5, 3.8, 'auth.php\n(Login/Register)', '#ffcc80'),
    ]
    for x, y, txt, clr in core_items:
        rect = FancyBboxPatch((x-1.3, y-0.35), 2.6, 0.7, boxstyle="round,pad=0.1",
                              facecolor=clr, edgecolor=server_border, linewidth=1.2)
        ax.add_patch(rect)
        ax.text(x, y, txt, fontsize=8, ha='center', va='center', fontweight='bold')

    # Arrow: Server -> Database
    ax.annotate('', xy=(7, 2.6), xytext=(7, 3.2),
                arrowprops=dict(arrowstyle='->', color=arrow_color, lw=2.5))
    ax.text(8.5, 2.9, 'PDO Prepared Statements', fontsize=8, ha='center',
            va='center', color=arrow_color, style='italic')

    # ============================
    # DATABASE TIER (Bottom)
    # ============================
    rect = FancyBboxPatch((0.5, 0.3), 13, 2.2, boxstyle="round,pad=0.15",
                          facecolor=db_color, edgecolor=db_border, linewidth=2.5)
    ax.add_patch(rect)
    ax.text(7, 2.3, 'DATABASE TIER (MySQL 8.0 / MariaDB)', fontsize=14,
            fontweight='bold', ha='center', va='center', color=db_border)

    db_items = [
        (2.2, 1.3, 'Users & Auth\nusers, admins\nsessions', '#c8e6c9'),
        (5.2, 1.3, 'Content\ngames, trainers\ntips, plans', '#c8e6c9'),
        (8.2, 1.3, 'User Data\nuser_game_lists\nuser_notes', '#c8e6c9'),
        (11.2, 1.3, 'Settings\nsettings, pages\ncontact_messages', '#c8e6c9'),
    ]
    for x, y, txt, clr in db_items:
        rect = FancyBboxPatch((x-1.2, y-0.55), 2.4, 1.1, boxstyle="round,pad=0.1",
                              facecolor=clr, edgecolor=db_border, linewidth=1.2)
        ax.add_patch(rect)
        ax.text(x, y, txt, fontsize=8, ha='center', va='center', fontweight='bold')

    # Bottom note
    ax.text(7, 0.05, '17 Database Tables  |  Bilingual (EN/KU)  |  CSRF + BCRYPT + PDO Security  |  Responsive Design',
            fontsize=9, ha='center', va='center', color='#666666', style='italic')

    plt.tight_layout()
    plt.savefig('assets/images/system_architecture.png', dpi=200, bbox_inches='tight',
                facecolor='white', edgecolor='none')
    print(" System Architecture Diagram saved!")
    plt.close()


# ============================================================
# DIAGRAM 2: WEBSITE FLOWCHART
# ============================================================
def create_flowchart():
    fig, ax = plt.subplots(1, 1, figsize=(16, 12))
    ax.set_xlim(0, 16)
    ax.set_ylim(0, 12)
    ax.axis('off')
    fig.patch.set_facecolor('white')

    # Title
    ax.text(8, 11.6, 'FitZone Website Flowchart', fontsize=22, fontweight='bold',
            ha='center', va='center', color='#1a1a2e')

    # Colors
    start_color = '#ff6b35'
    public_color = '#2196F3'
    user_color = '#4CAF50'
    admin_color = '#9C27B0'
    page_bg_public = '#e3f2fd'
    page_bg_user = '#e8f5e9'
    page_bg_admin = '#f3e5f5'
    decision_color = '#FFC107'

    def draw_box(x, y, w, h, text, bg_color, border_color, fontsize=9, bold=True):
        rect = FancyBboxPatch((x - w/2, y - h/2), w, h, boxstyle="round,pad=0.08",
                              facecolor=bg_color, edgecolor=border_color, linewidth=2)
        ax.add_patch(rect)
        ax.text(x, y, text, fontsize=fontsize, ha='center', va='center',
                fontweight='bold' if bold else 'normal', color='#1a1a2e')

    def draw_diamond(x, y, w, h, text, bg_color, border_color):
        diamond = plt.Polygon([(x, y+h/2), (x+w/2, y), (x, y-h/2), (x-w/2, y)],
                             facecolor=bg_color, edgecolor=border_color, linewidth=2)
        ax.add_patch(diamond)
        ax.text(x, y, text, fontsize=8, ha='center', va='center', fontweight='bold')

    def arrow(x1, y1, x2, y2, label='', color='#555'):
        ax.annotate('', xy=(x2, y2), xytext=(x1, y1),
                    arrowprops=dict(arrowstyle='->', color=color, lw=1.8))
        if label:
            mx, my = (x1+x2)/2, (y1+y2)/2
            ax.text(mx + 0.15, my + 0.1, label, fontsize=7, color=color, style='italic')

    # ============================
    # START - Visit Website
    # ============================
    draw_box(8, 11.0, 3.5, 0.6, 'User Visits FitZone Website', '#ffccbc', start_color, fontsize=11)

    # Arrow down to public pages
    arrow(8, 10.7, 8, 10.2)

    # ============================
    # PUBLIC PAGES SECTION
    # ============================
    # Public section header
    rect = FancyBboxPatch((0.3, 7.3), 15.4, 2.8, boxstyle="round,pad=0.1",
                          facecolor='#f8fbff', edgecolor=public_color, linewidth=2, linestyle='--')
    ax.add_patch(rect)
    ax.text(8, 9.9, 'PUBLIC PAGES (No Login Required)', fontsize=13,
            fontweight='bold', ha='center', va='center', color=public_color)

    public_pages = [
        (1.8, 8.8, 'Home Page\nindex.php'),
        (4.3, 8.8, 'Exercises\ngames.php'),
        (6.8, 8.8, 'Trainers\ntrainers.php'),
        (9.3, 8.8, 'Plans\nplans.php'),
        (11.8, 8.8, 'Tips\ntips.php'),
        (14.3, 8.8, 'Contact\ncontact.php'),
    ]
    for x, y, txt in public_pages:
        draw_box(x, y, 2.1, 0.8, txt, page_bg_public, public_color, fontsize=8)

    # Language & Auth row
    draw_box(3, 7.8, 2.5, 0.55, 'EN / KU Switch', '#fff9c4', '#F9A825', fontsize=8)
    draw_box(6.5, 7.8, 2, 0.55, 'Login\nlogin.php', '#ffecb3', '#FF9800', fontsize=8)
    draw_box(9.5, 7.8, 2, 0.55, 'Register\nregister.php', '#ffecb3', '#FF9800', fontsize=8)

    # ============================
    # DECISION: Login?
    # ============================
    draw_diamond(8, 6.8, 2.4, 0.8, 'Logged In?', '#FFF9C4', '#F9A825')

    arrow(8, 7.3, 8, 7.2)

    # Yes -> splits to User and Admin
    ax.text(5.0, 6.8, 'User', fontsize=9, color=user_color, fontweight='bold')
    ax.text(10.8, 6.8, 'Admin', fontsize=9, color=admin_color, fontweight='bold')

    arrow(6.8, 6.8, 4.5, 6.3, '', user_color)
    arrow(9.2, 6.8, 11.5, 6.3, '', admin_color)

    # No arrow back
    ax.text(8.3, 7.25, 'No → Stay\non Public', fontsize=7, color='#999', style='italic')

    # ============================
    # USER DASHBOARD SECTION
    # ============================
    rect = FancyBboxPatch((0.3, 2.5), 7, 3.7, boxstyle="round,pad=0.1",
                          facecolor='#f1f8e9', edgecolor=user_color, linewidth=2, linestyle='--')
    ax.add_patch(rect)
    ax.text(3.8, 6.0, 'USER DASHBOARD', fontsize=13,
            fontweight='bold', ha='center', va='center', color=user_color)

    draw_box(3.8, 5.3, 3, 0.6, 'Dashboard (user/)', page_bg_user, user_color, fontsize=9)

    user_pages = [
        (1.8, 4.3, 'Workout\nLists\nmy-lists.php'),
        (3.8, 4.3, 'Daily\nNotes\nnotes.php'),
        (5.8, 4.3, 'Profile\n\nprofile.php'),
    ]
    for x, y, txt in user_pages:
        draw_box(x, y, 1.7, 1.0, txt, page_bg_user, user_color, fontsize=7)

    # Arrows from dashboard to user pages
    arrow(2.8, 5.0, 1.8, 4.85, '', user_color)
    arrow(3.8, 5.0, 3.8, 4.85, '', user_color)
    arrow(4.8, 5.0, 5.8, 4.85, '', user_color)

    # User sub-features
    user_features = [
        (1.2, 3.2, 'Create Lists'),
        (2.5, 3.2, 'Add Exercises'),
        (3.8, 3.2, 'Mood & Weight'),
        (5.1, 3.2, 'Edit Info'),
        (6.3, 3.2, 'Change Password'),
    ]
    for x, y, txt in user_features:
        rect = FancyBboxPatch((x-0.55, y-0.2), 1.1, 0.4, boxstyle="round,pad=0.05",
                              facecolor='#c8e6c9', edgecolor=user_color, linewidth=1)
        ax.add_patch(rect)
        ax.text(x, y, txt, fontsize=6, ha='center', va='center')

    # ============================
    # ADMIN PANEL SECTION
    # ============================
    rect = FancyBboxPatch((8.3, 2.5), 7.4, 3.7, boxstyle="round,pad=0.1",
                          facecolor='#faf0ff', edgecolor=admin_color, linewidth=2, linestyle='--')
    ax.add_patch(rect)
    ax.text(12, 6.0, 'ADMIN PANEL', fontsize=13,
            fontweight='bold', ha='center', va='center', color=admin_color)

    draw_box(12, 5.3, 3, 0.6, 'Admin Dashboard (admin/)', page_bg_admin, admin_color, fontsize=9)

    admin_pages = [
        (9.3, 4.2, 'Games\nManagement'),
        (10.8, 4.2, 'Trainers\nManagement'),
        (12.3, 4.2, 'Plans\nManagement'),
        (13.8, 4.2, 'Tips\nManagement'),
    ]
    for x, y, txt in admin_pages:
        draw_box(x, y, 1.3, 0.8, txt, page_bg_admin, admin_color, fontsize=7)

    admin_pages2 = [
        (9.3, 3.1, 'Messages'),
        (10.8, 3.1, 'Colors\nSettings'),
        (12.3, 3.1, 'Users\nManagement'),
        (13.8, 3.1, 'Pages\nCertificates'),
    ]
    for x, y, txt in admin_pages2:
        draw_box(x, y, 1.3, 0.7, txt, page_bg_admin, admin_color, fontsize=7)

    # Arrows from admin dashboard
    for x in [9.3, 10.8, 12.3, 13.8]:
        arrow(12, 5.0, x, 4.65, '', admin_color)

    # ============================
    # DATABASE at bottom
    # ============================
    draw_box(8, 1.5, 6, 0.8, 'MySQL Database (17 Tables)\nPDO + Prepared Statements + BCRYPT',
             '#e8f5e9', '#2E7D32', fontsize=10)

    # Arrows to database
    arrow(3.8, 2.5, 6, 1.95, 'CRUD', '#666')
    arrow(12, 2.5, 10, 1.95, 'CRUD', '#666')

    # ============================
    # SECURITY badge
    # ============================
    draw_box(8, 0.5, 8, 0.5, 'Security: CSRF Tokens  |  BCRYPT Hashing  |  PDO Prepared Statements  |  XSS Prevention  |  Session Management',
             '#fff3e0', '#E65100', fontsize=8)

    # Legend
    legend_items = [
        (1, 1.3, page_bg_public, public_color, 'Public Pages'),
        (1, 0.9, page_bg_user, user_color, 'User Dashboard'),
        (1, 0.5, page_bg_admin, admin_color, 'Admin Panel'),
    ]
    for x, y, bg, border, label in legend_items:
        rect = FancyBboxPatch((x-0.3, y-0.15), 0.6, 0.3, boxstyle="round,pad=0.03",
                              facecolor=bg, edgecolor=border, linewidth=1.5)
        ax.add_patch(rect)
        ax.text(x+0.6, y, label, fontsize=8, va='center', fontweight='bold', color=border)

    plt.tight_layout()
    plt.savefig('assets/images/website_flowchart.png', dpi=200, bbox_inches='tight',
                facecolor='white', edgecolor='none')
    print(" Website Flowchart saved!")
    plt.close()


# Run both
if __name__ == '__main__':
    import os
    os.makedirs('assets/images', exist_ok=True)
    create_architecture_diagram()
    create_flowchart()
    print("\n Both diagrams created successfully!")
    print("  - assets/images/system_architecture.png")
    print("  - assets/images/website_flowchart.png")
