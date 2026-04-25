#!/usr/bin/env python3
"""Create User Flow / Use Case Diagram showing what users can do after login."""

import matplotlib
matplotlib.use('Agg')
import matplotlib.pyplot as plt
from matplotlib.patches import FancyBboxPatch, Circle, FancyArrowPatch
from matplotlib.patches import Ellipse, Polygon
import numpy as np

def create_user_flow_diagram():
    fig, ax = plt.subplots(1, 1, figsize=(20, 14))
    ax.set_xlim(0, 20)
    ax.set_ylim(0, 14)
    ax.axis('off')
    fig.patch.set_facecolor('white')

    # Title
    ax.text(10, 13.5, 'FitZone User Flow & Login Coverage Diagram',
            fontsize=22, fontweight='bold', ha='center', va='center', color='#1a1a2e')
    ax.text(10, 13.0, 'Complete user journey from visit to logged-in experience',
            fontsize=12, ha='center', va='center', color='#666', style='italic')

    # Colors
    visitor_color = '#2196F3'
    user_color = '#4CAF50'
    admin_color = '#9C27B0'
    public_bg = '#e3f2fd'
    user_bg = '#e8f5e9'
    admin_bg = '#f3e5f5'
    decision_bg = '#FFF9C4'
    decision_border = '#F9A825'

    def draw_box(x, y, w, h, text, bg, border, fontsize=10, bold=True):
        rect = FancyBboxPatch((x - w/2, y - h/2), w, h,
                              boxstyle="round,pad=0.08",
                              facecolor=bg, edgecolor=border, linewidth=2)
        ax.add_patch(rect)
        ax.text(x, y, text, fontsize=fontsize, ha='center', va='center',
                fontweight='bold' if bold else 'normal', color='#1a1a2e')

    def draw_diamond(x, y, w, h, text, bg, border, fontsize=10):
        diamond = Polygon([(x, y+h/2), (x+w/2, y), (x, y-h/2), (x-w/2, y)],
                          facecolor=bg, edgecolor=border, linewidth=2.5)
        ax.add_patch(diamond)
        ax.text(x, y, text, fontsize=fontsize, ha='center', va='center', fontweight='bold')

    def draw_actor(x, y, label, color):
        """Draw a stick figure actor"""
        # Head
        circle = Circle((x, y + 0.4), 0.18, facecolor=color, edgecolor='black', linewidth=1.5)
        ax.add_patch(circle)
        # Body
        ax.plot([x, x], [y + 0.22, y - 0.2], color='black', linewidth=2)
        # Arms
        ax.plot([x - 0.3, x + 0.3], [y, y], color='black', linewidth=2)
        # Legs
        ax.plot([x, x - 0.2], [y - 0.2, y - 0.55], color='black', linewidth=2)
        ax.plot([x, x + 0.2], [y - 0.2, y - 0.55], color='black', linewidth=2)
        # Label
        ax.text(x, y - 0.85, label, fontsize=11, ha='center', va='center',
                fontweight='bold', color=color)

    def arrow(x1, y1, x2, y2, label='', color='#555', style='->'):
        ax.annotate('', xy=(x2, y2), xytext=(x1, y1),
                    arrowprops=dict(arrowstyle=style, color=color, lw=1.8))
        if label:
            mx, my = (x1+x2)/2, (y1+y2)/2
            ax.text(mx + 0.1, my + 0.15, label, fontsize=8,
                    color=color, style='italic',
                    bbox=dict(boxstyle='round,pad=0.15', facecolor='white',
                             edgecolor='none', alpha=0.85))

    # ============================
    # ACTORS (left side)
    # ============================
    draw_actor(1.0, 11.5, 'Visitor', visitor_color)
    draw_actor(1.0, 7.0, 'Member\n(User)', user_color)
    draw_actor(1.0, 2.5, 'Admin', admin_color)

    # ============================
    # STEP 1: VISITOR ENTERS WEBSITE
    # ============================
    draw_box(4, 12.0, 2.5, 0.7, 'Visit FitZone\n(index.php)', public_bg, visitor_color, fontsize=10)
    arrow(1.5, 11.5, 2.85, 12.0, color=visitor_color)

    # PUBLIC PAGES BOX
    rect = FancyBboxPatch((5.8, 10.3), 13.8, 2.0, boxstyle="round,pad=0.1",
                          facecolor='#f8fbff', edgecolor=visitor_color,
                          linewidth=2, linestyle='--')
    ax.add_patch(rect)
    ax.text(12.7, 12.05, 'PUBLIC PAGES (No Login Required)', fontsize=12,
            fontweight='bold', ha='center', va='center', color=visitor_color)

    public_pages = [
        (6.6, 11.2, 'Home\nindex.php'),
        (8.1, 11.2, 'Exercises\ngames.php'),
        (9.6, 11.2, 'Trainers\ntrainers.php'),
        (11.1, 11.2, 'Plans\nplans.php'),
        (12.6, 11.2, 'Tips\ntips.php'),
        (14.1, 11.2, 'Contact\ncontact.php'),
        (15.6, 11.2, 'Services\nservices'),
        (17.1, 11.2, 'About\nabout.php'),
        (18.6, 11.2, 'Lang\nEN/KU'),
    ]
    for x, y, txt in public_pages:
        draw_box(x, y, 1.35, 0.7, txt, public_bg, visitor_color, fontsize=7)

    # Login/Register row
    draw_box(8, 10.6, 1.6, 0.45, 'Login', '#FFE082', '#FB8C00', fontsize=8)
    draw_box(10, 10.6, 1.6, 0.45, 'Register', '#FFE082', '#FB8C00', fontsize=8)

    # Arrow from visit to public
    arrow(5.25, 12.0, 5.85, 11.5, color=visitor_color)

    # ============================
    # STEP 2: LOGIN DECISION
    # ============================
    draw_diamond(10, 9.4, 2.5, 0.9, 'Logged In?\n(Authentication)', decision_bg, decision_border, fontsize=9)
    arrow(9, 10.4, 9.5, 9.85, color=decision_border)

    # Branch labels
    ax.text(6.5, 9.4, 'Regular User', fontsize=10, color=user_color, fontweight='bold', ha='center')
    ax.text(13.5, 9.4, 'Admin', fontsize=10, color=admin_color, fontweight='bold', ha='center')
    ax.text(10, 8.5, 'No → Stay Public', fontsize=8, color='#999', style='italic', ha='center')

    # Branch arrows
    arrow(8.75, 9.4, 5.5, 8.7, color=user_color, style='->')
    arrow(11.25, 9.4, 14.5, 8.7, color=admin_color, style='->')

    # ============================
    # USER PATH (left side after login)
    # ============================
    rect = FancyBboxPatch((0.5, 4.5), 9.4, 4.2, boxstyle="round,pad=0.1",
                          facecolor='#f1f8e9', edgecolor=user_color,
                          linewidth=2, linestyle='--')
    ax.add_patch(rect)
    ax.text(5.2, 8.4, 'USER LOGGED IN  -  Member Dashboard', fontsize=13,
            fontweight='bold', ha='center', va='center', color=user_color)

    # User Dashboard
    draw_box(5.2, 7.6, 4.5, 0.7, 'USER DASHBOARD (user/index.php)\nWelcome message + Stats',
             '#c8e6c9', user_color, fontsize=9)

    # User features
    user_features = [
        (1.8, 6.4, 'Workout Lists\n(my-lists.php)'),
        (4, 6.4, 'Daily Notes\n(notes.php)'),
        (6.2, 6.4, 'My Profile\n(profile.php)'),
        (8.4, 6.4, 'Change\nPassword'),
    ]
    for x, y, txt in user_features:
        draw_box(x, y, 1.85, 0.85, txt, user_bg, user_color, fontsize=8)

    # User sub-features (what each does)
    user_subs = [
        (1.8, 5.4, 'Create Lists\nAdd Exercises\nSets/Reps'),
        (4, 5.4, 'Mood Tracking\nWeight Log\nWorkout Done'),
        (6.2, 5.4, 'Edit Info (EN/KU)\nUpload Avatar\nUpdate Bio'),
        (8.4, 5.4, 'Logout\nGo to Public\nSwitch Lang'),
    ]
    for x, y, txt in user_subs:
        rect = FancyBboxPatch((x - 0.92, y - 0.4), 1.85, 0.8,
                              boxstyle="round,pad=0.05",
                              facecolor='#dcedc8', edgecolor=user_color, linewidth=1)
        ax.add_patch(rect)
        ax.text(x, y, txt, fontsize=7, ha='center', va='center')

    # Arrows from dashboard to features
    for fx, _, _ in user_features:
        arrow(5.2, 7.25, fx, 6.85, color=user_color)
    for i, ((fx, _, _), (sx, _, _)) in enumerate(zip(user_features, user_subs)):
        arrow(fx, 5.95, sx, 5.85, color=user_color)

    # ============================
    # ADMIN PATH (right side after login)
    # ============================
    rect = FancyBboxPatch((10.1, 4.5), 9.4, 4.2, boxstyle="round,pad=0.1",
                          facecolor='#faf0ff', edgecolor=admin_color,
                          linewidth=2, linestyle='--')
    ax.add_patch(rect)
    ax.text(14.8, 8.4, 'ADMIN LOGGED IN  -  Control Panel', fontsize=13,
            fontweight='bold', ha='center', va='center', color=admin_color)

    # Admin Dashboard
    draw_box(14.8, 7.6, 4.5, 0.7, 'ADMIN DASHBOARD (admin/index.php)\nStats + Recent Activity',
             '#e1bee7', admin_color, fontsize=9)

    # Admin features - Row 1
    admin_features1 = [
        (11.0, 6.5, 'Games\nManagement'),
        (12.5, 6.5, 'Trainers\nManagement'),
        (14.0, 6.5, 'Plans\nManagement'),
        (15.5, 6.5, 'Tips\nManagement'),
        (17.0, 6.5, 'Certificates'),
        (18.5, 6.5, 'Services'),
    ]
    for x, y, txt in admin_features1:
        draw_box(x, y, 1.4, 0.7, txt, admin_bg, admin_color, fontsize=7)
        arrow(14.8, 7.25, x, 6.9, color=admin_color)

    # Admin features - Row 2
    admin_features2 = [
        (11.0, 5.4, 'Messages'),
        (12.5, 5.4, 'Users\nManagement'),
        (14.0, 5.4, 'Settings'),
        (15.5, 5.4, 'Colors'),
        (17.0, 5.4, 'Pages'),
        (18.5, 5.4, 'Reviews'),
    ]
    for x, y, txt in admin_features2:
        draw_box(x, y, 1.4, 0.7, txt, admin_bg, admin_color, fontsize=7)

    # Admin actions: CRUD note
    rect = FancyBboxPatch((10.5, 4.6), 8.6, 0.5, boxstyle="round,pad=0.05",
                          facecolor='#f8bbd0', edgecolor=admin_color, linewidth=1.5)
    ax.add_patch(rect)
    ax.text(14.8, 4.85, 'Each section supports: CREATE  -  READ  -  UPDATE  -  DELETE  -  Bilingual (EN/KU)',
            fontsize=9, ha='center', va='center', color=admin_color, fontweight='bold')

    # ============================
    # ACTOR ARROWS to login decision
    # ============================
    arrow(1.5, 7.0, 9.5, 9.0, label='Login as User', color=user_color)
    arrow(1.5, 2.5, 10.5, 9.0, label='Login as Admin', color=admin_color)

    # ============================
    # SECURITY LAYER (bottom)
    # ============================
    rect = FancyBboxPatch((1, 2.5), 18, 1.6, boxstyle="round,pad=0.1",
                          facecolor='#fff3e0', edgecolor='#E65100',
                          linewidth=2, linestyle='-')
    ax.add_patch(rect)
    ax.text(10, 3.85, 'SECURITY & DATABASE LAYER', fontsize=13,
            fontweight='bold', ha='center', va='center', color='#E65100')

    sec_items = [
        (2.5, 3.1, 'CSRF\nTokens'),
        (4.5, 3.1, 'BCRYPT\nPasswords'),
        (6.5, 3.1, 'PDO\nPrepared'),
        (8.5, 3.1, 'XSS\nPrevention'),
        (10.5, 3.1, 'Session\nManagement'),
        (12.5, 3.1, 'Input\nValidation'),
        (14.5, 3.1, 'Bilingual\nEN / KU'),
        (17.0, 3.1, 'MySQL Database\n(18 Tables)'),
    ]
    for x, y, txt in sec_items:
        rect = FancyBboxPatch((x - 0.85, y - 0.3), 1.7, 0.6,
                              boxstyle="round,pad=0.05",
                              facecolor='#ffe0b2', edgecolor='#E65100', linewidth=1)
        ax.add_patch(rect)
        ax.text(x, y, txt, fontsize=7, ha='center', va='center', fontweight='bold')

    # Arrows from User and Admin sections down to security
    arrow(5.2, 4.5, 5.2, 4.1, color='#999')
    arrow(14.8, 4.5, 14.8, 4.1, color='#999')

    # ============================
    # LEGEND
    # ============================
    rect = FancyBboxPatch((1, 0.5), 18, 1.4, boxstyle="round,pad=0.1",
                          facecolor='#f5f5f5', edgecolor='#888', linewidth=1)
    ax.add_patch(rect)
    ax.text(2, 1.65, 'Legend:', fontsize=11, fontweight='bold', color='#333')

    legend_items = [
        (2.5, 1.2, public_bg, visitor_color, 'Public Access'),
        (5, 1.2, user_bg, user_color, 'User Features'),
        (7.5, 1.2, admin_bg, admin_color, 'Admin Features'),
        (10, 1.2, decision_bg, decision_border, 'Decision Point'),
        (12.5, 1.2, '#fff3e0', '#E65100', 'Security Layer'),
    ]
    for x, y, bg, border, label in legend_items:
        rect = FancyBboxPatch((x - 0.25, y - 0.2), 0.5, 0.4,
                              boxstyle="round,pad=0.03",
                              facecolor=bg, edgecolor=border, linewidth=2)
        ax.add_patch(rect)
        ax.text(x + 0.5, y, label, fontsize=8, va='center', fontweight='bold', color=border)

    ax.text(2, 0.75, 'Note:', fontsize=10, fontweight='bold', color='#333')
    ax.text(3, 0.75, 'After login, users access role-specific features. All actions are bilingual (English/Kurdish) and secured.',
            fontsize=9, va='center', color='#555', style='italic')

    plt.tight_layout()
    plt.savefig('assets/images/user_flow_diagram.png', dpi=180, bbox_inches='tight',
                facecolor='white', edgecolor='none')
    print("User Flow Diagram saved!")
    plt.close()


if __name__ == '__main__':
    create_user_flow_diagram()
