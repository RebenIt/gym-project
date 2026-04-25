#!/usr/bin/env python3
"""Create Database ER Diagram for FitZone project."""

import matplotlib
matplotlib.use('Agg')
import matplotlib.pyplot as plt
import matplotlib.patches as mpatches
from matplotlib.patches import FancyBboxPatch
import numpy as np

def create_er_diagram():
    fig, ax = plt.subplots(1, 1, figsize=(24, 18))
    ax.set_xlim(0, 24)
    ax.set_ylim(0, 18)
    ax.axis('off')
    fig.patch.set_facecolor('white')

    # Title
    ax.text(12, 17.5, 'FitZone Database - Entity Relationship Diagram',
            fontsize=24, fontweight='bold', ha='center', va='center', color='#1a1a2e')
    ax.text(12, 17.1, '18 Tables  |  MySQL / MariaDB  |  InnoDB Engine',
            fontsize=12, ha='center', va='center', color='#666', style='italic')

    # Colors by category
    colors = {
        'auth': ('#e3f2fd', '#1565C0'),       # Blue - Auth tables
        'content': ('#fff3e0', '#E65100'),     # Orange - Content tables
        'user_data': ('#e8f5e9', '#2E7D32'),   # Green - User data
        'system': ('#f3e5f5', '#6A1B9A'),      # Purple - System tables
        'beginner': ('#fce4ec', '#C62828'),     # Red - Beginner tables
    }

    def draw_table(x, y, w, h, name, columns, category, pk_count=1):
        bg, border = colors[category]

        # Table header
        header_h = 0.45
        header = FancyBboxPatch((x, y + h - header_h), w, header_h,
                                boxstyle="round,pad=0.05",
                                facecolor=border, edgecolor=border, linewidth=2)
        ax.add_patch(header)
        ax.text(x + w/2, y + h - header_h/2, name, fontsize=9,
                fontweight='bold', ha='center', va='center', color='white')

        # Table body
        body = FancyBboxPatch((x, y), w, h - header_h,
                              boxstyle="round,pad=0.05",
                              facecolor=bg, edgecolor=border, linewidth=1.5)
        ax.add_patch(body)

        # Columns
        col_y = y + h - header_h - 0.22
        for i, col in enumerate(columns):
            if i >= 8:  # Max 8 columns shown
                ax.text(x + 0.1, col_y, '...', fontsize=6.5, va='center', color='#999')
                break
            prefix = 'PK ' if i < pk_count else 'FK ' if col.startswith('*') else '   '
            col_clean = col.lstrip('*')
            if i < pk_count:
                ax.text(x + 0.1, col_y, prefix + col_clean, fontsize=6.5,
                        va='center', fontweight='bold', color='#333')
            elif col.startswith('*'):
                ax.text(x + 0.1, col_y, prefix + col_clean, fontsize=6.5,
                        va='center', color=border, fontweight='bold')
            else:
                ax.text(x + 0.1, col_y, '    ' + col_clean, fontsize=6.5,
                        va='center', color='#555')
            col_y -= 0.22

        return (x, y, w, h)

    def draw_relation(x1, y1, x2, y2, label='', style='->', color='#888'):
        ax.annotate('', xy=(x2, y2), xytext=(x1, y1),
                    arrowprops=dict(arrowstyle=style, color=color, lw=1.5,
                                   connectionstyle='arc3,rad=0.1'))
        if label:
            mx, my = (x1+x2)/2, (y1+y2)/2
            ax.text(mx, my + 0.15, label, fontsize=6, ha='center', color=color,
                    style='italic', bbox=dict(boxstyle='round,pad=0.1',
                    facecolor='white', edgecolor='none', alpha=0.8))

    # ================================================================
    # ROW 1: Auth & System tables (top)
    # ================================================================

    # admins
    draw_table(0.5, 14.5, 3.2, 2.5, 'admins', [
        'id INT (PK)',
        'username VARCHAR(50)',
        'email VARCHAR(100)',
        'password VARCHAR(255)',
        'full_name VARCHAR(100)',
        'role ENUM',
        'avatar VARCHAR(255)',
        'is_active TINYINT',
    ], 'auth')

    # users
    draw_table(4.5, 14.5, 3.2, 2.5, 'users', [
        'id INT (PK)',
        'username VARCHAR(50)',
        'email VARCHAR(100)',
        'password VARCHAR(255)',
        'first_name VARCHAR(50)',
        'first_name_ku VARCHAR(50)',
        'gender ENUM',
        'status ENUM',
    ], 'auth')

    # settings
    draw_table(8.5, 14.8, 3.2, 2.2, 'settings', [
        'id INT (PK)',
        'setting_key VARCHAR(100)',
        'setting_value TEXT',
        'setting_type ENUM',
        'category VARCHAR(50)',
        'label VARCHAR(100)',
        'sort_order INT',
    ], 'system')

    # pages_content
    draw_table(12.5, 14.8, 3.2, 2.2, 'pages_content', [
        'id INT (PK)',
        'page_key VARCHAR(100)',
        'section_key VARCHAR(100)',
        'title / title_ku',
        'subtitle / subtitle_ku',
        'content / content_ku',
        'image VARCHAR(255)',
    ], 'system')

    # pages
    draw_table(16.5, 14.8, 3.2, 2.2, 'pages', [
        'id INT (PK)',
        'title / title_ku',
        'slug VARCHAR(200)',
        'content / content_ku',
        'featured_image',
        'is_active TINYINT',
        'created_at TIMESTAMP',
    ], 'system')

    # contact_messages
    draw_table(20.3, 14.5, 3.2, 2.5, 'contact_messages', [
        'id INT (PK)',
        '*replied_by INT (FK)',
        'name VARCHAR(100)',
        'email VARCHAR(100)',
        'subject VARCHAR(255)',
        'message TEXT',
        'is_read TINYINT',
        'created_at TIMESTAMP',
    ], 'system')

    # ================================================================
    # ROW 2: Content tables (middle-top)
    # ================================================================

    # games
    draw_table(0.5, 10.5, 3.5, 3.2, 'games', [
        'id INT (PK)',
        'name / name_ku',
        'description / desc_ku',
        'image VARCHAR(255)',
        'youtube_url VARCHAR(255)',
        'difficulty ENUM',
        'muscle_group',
        'equipment_needed',
    ], 'content')

    # trainers
    draw_table(4.8, 10.8, 3.2, 2.8, 'trainers', [
        'id INT (PK)',
        'first_name / _ku',
        'last_name / _ku',
        'specialization / _ku',
        'bio / bio_ku TEXT',
        'experience_years INT',
        'avatar VARCHAR(255)',
        'is_active TINYINT',
    ], 'content')

    # plans
    draw_table(8.8, 10.8, 3.2, 2.8, 'plans', [
        'id INT (PK)',
        'name / name_ku',
        'description / desc_ku',
        'duration_days INT',
        'price DECIMAL(10,2)',
        'features JSON',
        'is_popular TINYINT',
        'is_active TINYINT',
    ], 'content')

    # tips
    draw_table(12.8, 10.5, 3.2, 3.0, 'tips', [
        'id INT (PK)',
        '*author_id INT (FK)',
        'title / title_ku',
        'content / content_ku',
        'category ENUM',
        'image VARCHAR(255)',
        'is_featured TINYINT',
        'is_published TINYINT',
    ], 'content')

    # services
    draw_table(16.8, 10.8, 3.2, 2.8, 'services', [
        'id INT (PK)',
        'name / name_ku',
        'description / desc_ku',
        'icon VARCHAR(50)',
        'image VARCHAR(255)',
        'is_featured TINYINT',
        'sort_order INT',
        'is_active TINYINT',
    ], 'content')

    # certificates
    draw_table(20.5, 10.8, 3.0, 2.8, 'certificates', [
        'id INT (PK)',
        'title / title_ku',
        'description / desc_ku',
        'image VARCHAR(255)',
        'year_received INT',
        'issuing_organization',
        'certificate_type ENUM',
        'is_active TINYINT',
    ], 'content')

    # ================================================================
    # ROW 3: User data tables (middle-bottom)
    # ================================================================

    # user_game_lists
    draw_table(1, 6.5, 3.2, 2.8, 'user_game_lists', [
        'id INT (PK)',
        '*user_id INT (FK)',
        'name VARCHAR(100)',
        'name_ku VARCHAR(100)',
        'description TEXT',
        'is_default TINYINT',
        'created_at TIMESTAMP',
    ], 'user_data')

    # user_game_list_items
    draw_table(5, 6.5, 3.2, 2.8, 'user_game_list_items', [
        'id INT (PK)',
        '*list_id INT (FK)',
        '*game_id INT (FK)',
        'sets INT',
        'reps VARCHAR(50)',
        'notes TEXT',
        'is_completed TINYINT',
        'sort_order INT',
    ], 'user_data')

    # user_notes
    draw_table(9, 6.5, 3.2, 2.8, 'user_notes', [
        'id INT (PK)',
        '*user_id INT (FK)',
        'note_date DATE',
        'title VARCHAR(150)',
        'content TEXT',
        'mood ENUM',
        'weight_kg DECIMAL',
        'workout_done TINYINT',
    ], 'user_data')

    # reviews
    draw_table(13, 6.5, 3.2, 2.8, 'reviews', [
        'id INT (PK)',
        '*user_id INT (FK)',
        'reviewer_name / _ku',
        'rating TINYINT',
        'review_text / _ku',
        'is_featured TINYINT',
        'is_approved TINYINT',
        'created_at TIMESTAMP',
    ], 'user_data')

    # ================================================================
    # ROW 4: Beginner tables (bottom)
    # ================================================================

    # beginner_programs
    draw_table(17, 6.5, 3.2, 2.8, 'beginner_programs', [
        'id INT (PK)',
        'title / title_ku',
        'description / desc_ku',
        'duration_weeks INT',
        'days_per_week INT',
        'level ENUM',
        'goal / goal_ku',
        'is_active TINYINT',
    ], 'beginner')

    # beginner_games
    draw_table(21, 6.5, 2.5, 2.8, 'beginner_games', [
        'id INT (PK)',
        '*game_id INT (FK)',
        'week_number INT',
        'day_of_week ENUM',
        'sets INT',
        'reps VARCHAR(50)',
        'rest_seconds INT',
        'is_active TINYINT',
    ], 'beginner')

    # ================================================================
    # RELATIONSHIPS (Arrows)
    # ================================================================

    # users -> user_game_lists (1:N)
    draw_relation(5.5, 14.5, 2.6, 9.3, '1:N', color='#2E7D32')

    # users -> user_notes (1:N)
    draw_relation(6.0, 14.5, 10.5, 9.3, '1:N', color='#2E7D32')

    # users -> reviews (1:N)
    draw_relation(6.5, 14.5, 14.5, 9.3, '1:N', color='#2E7D32')

    # user_game_lists -> user_game_list_items (1:N)
    draw_relation(4.2, 7.9, 5.0, 7.9, '1:N', color='#2E7D32')

    # games -> user_game_list_items (1:N)
    draw_relation(2.2, 10.5, 6.5, 9.3, '1:N', color='#E65100')

    # games -> beginner_games (1:N)
    draw_relation(3.0, 10.5, 22.0, 9.3, '1:N', color='#C62828')

    # admins -> tips (1:N) author
    draw_relation(2.5, 14.5, 14.4, 13.5, '1:N author', color='#1565C0')

    # admins -> contact_messages (1:N) replied_by
    draw_relation(3.0, 15.5, 20.3, 15.5, '1:N replied_by', color='#1565C0')

    # ================================================================
    # LEGEND
    # ================================================================
    legend_y = 5.5
    ax.text(1, legend_y, 'Legend:', fontsize=12, fontweight='bold', color='#333')

    legend_items = [
        (1, legend_y - 0.5, colors['auth'][0], colors['auth'][1], 'Authentication (admins, users)'),
        (1, legend_y - 0.9, colors['content'][0], colors['content'][1], 'Content (games, trainers, plans, tips, services, certificates)'),
        (1, legend_y - 1.3, colors['user_data'][0], colors['user_data'][1], 'User Data (lists, items, notes, reviews)'),
        (1, legend_y - 1.7, colors['beginner'][0], colors['beginner'][1], 'Beginner Programs (programs, beginner_games)'),
        (1, legend_y - 2.1, colors['system'][0], colors['system'][1], 'System (settings, pages, contact_messages)'),
    ]
    for x, y, bg, border, label in legend_items:
        rect = FancyBboxPatch((x - 0.3, y - 0.15), 0.5, 0.3,
                              boxstyle="round,pad=0.03",
                              facecolor=bg, edgecolor=border, linewidth=2)
        ax.add_patch(rect)
        ax.text(x + 0.5, y, label, fontsize=9, va='center', color='#333')

    # Key notation
    ax.text(12, legend_y, 'Notation:', fontsize=12, fontweight='bold', color='#333')
    notations = [
        (12, legend_y - 0.5, 'PK = Primary Key'),
        (12, legend_y - 0.9, 'FK = Foreign Key (marked with *)'),
        (12, legend_y - 1.3, '1:N = One-to-Many Relationship'),
        (12, legend_y - 1.7, '_ku = Kurdish language column'),
        (12, legend_y - 2.1, 'All tables have created_at / updated_at timestamps'),
    ]
    for x, y, text in notations:
        ax.text(x, y, text, fontsize=9, va='center', color='#555')

    # Stats
    ax.text(20, legend_y, 'Statistics:', fontsize=12, fontweight='bold', color='#333')
    stats = [
        (20, legend_y - 0.5, '18 Tables Total'),
        (20, legend_y - 0.9, '7 Foreign Key Relationships'),
        (20, legend_y - 1.3, 'Bilingual: EN + KU columns'),
        (20, legend_y - 1.7, 'InnoDB Engine, UTF-8 Charset'),
        (20, legend_y - 2.1, 'CASCADE / SET NULL on Delete'),
    ]
    for x, y, text in stats:
        ax.text(x, y, text, fontsize=9, va='center', color='#555')

    plt.tight_layout()
    plt.savefig('assets/images/er_diagram.png', dpi=180, bbox_inches='tight',
                facecolor='white', edgecolor='none')
    print("ER Diagram saved: assets/images/er_diagram.png")
    plt.close()


if __name__ == '__main__':
    create_er_diagram()
