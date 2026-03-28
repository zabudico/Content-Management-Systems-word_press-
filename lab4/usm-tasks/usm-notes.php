<?php
/**
 * Plugin Name: USM Tasks
 * Plugin URI:  https://github.com/zabudico/university-labs
 * Description: Плагин для управления задачами с приоритетами и дедлайном.
 * Version:     1.0.0
 * Author:      zabudico
 * License:     GPL2
 * Text Domain: usm-tasks
 */

if (!defined('ABSPATH')) {
    exit; // Запрет прямого доступа к файлу
}

/* =============================================================
   ШАГ 3. РЕГИСТРАЦИЯ CUSTOM POST TYPE — «Задачи»
   ============================================================= */

function usm_register_tasks_cpt()
{
    $labels = [
        'name' => 'Задачи',
        'singular_name' => 'Задача',
        'add_new' => 'Добавить задачу',
        'add_new_item' => 'Новая задача',
        'edit_item' => 'Редактировать задачу',
        'new_item' => 'Задача',
        'view_item' => 'Просмотреть задачу',
        'search_items' => 'Найти задачу',
        'not_found' => 'Задачи не найдены',
        'not_found_in_trash' => 'В корзине задач нет',
        'all_items' => 'Все задачи',
        'menu_name' => 'Задачи',
    ];

    register_post_type('usm_task', [
        'labels' => $labels,
        'public' => true,
        'has_archive' => true,
        'supports' => ['title', 'editor', 'author', 'thumbnail'],
        'menu_icon' => 'dashicons-list-view',
        'show_in_rest' => true,
        'rewrite' => ['slug' => 'tasks'],
    ]);
}
add_action('init', 'usm_register_tasks_cpt');


/* =============================================================
   ШАГ 4. РЕГИСТРАЦИЯ ТАКСОНОМИИ — «Приоритет»
   ============================================================= */

function usm_register_priority_taxonomy()
{
    $labels = [
        'name' => 'Приоритеты',
        'singular_name' => 'Приоритет',
        'all_items' => 'Все приоритеты',
        'edit_item' => 'Редактировать приоритет',
        'update_item' => 'Обновить приоритет',
        'add_new_item' => 'Добавить приоритет',
        'new_item_name' => 'Название приоритета',
        'search_items' => 'Найти приоритет',
        'not_found' => 'Приоритеты не найдены',
        'parent_item' => 'Родительский приоритет',
        'parent_item_colon' => 'Родительский приоритет:',
    ];

    register_taxonomy('priority', 'usm_task', [
        'labels' => $labels,
        'hierarchical' => true,   // как категории
        'public' => true,
        'show_in_rest' => true,
        'rewrite' => ['slug' => 'priority'],
    ]);
}
add_action('init', 'usm_register_priority_taxonomy');


/* =============================================================
   ШАГ 5. МЕТАБОКС — «Дедлайн»
   ============================================================= */

// 5.1 — Регистрация метабокса
function usm_add_deadline_metabox()
{
    add_meta_box(
        'usm_deadline',          // ID метабокса
        'Дедлайн задачи',        // Заголовок
        'usm_deadline_callback', // Callback
        'usm_task',              // CPT
        'side',                  // Позиция
        'high'                   // Приоритет
    );
}
add_action('add_meta_boxes', 'usm_add_deadline_metabox');


// 5.2 — HTML-форма метабокса
function usm_deadline_callback($post)
{
    // Генерируем nonce для защиты от CSRF
    wp_nonce_field('usm_save_deadline', 'usm_deadline_nonce');

    $value = get_post_meta($post->ID, '_usm_deadline', true);
    $today = date('Y-m-d');

    echo '<label for="usm_deadline"><strong>Выберите дату дедлайна:</strong></label>';
    echo '<br><br>';
    echo '<input
            type="date"
            id="usm_deadline"
            name="usm_deadline"
            value="' . esc_attr($value) . '"
            min="' . esc_attr($today) . '"
            required
            style="width:100%; padding:5px; border:1px solid #ccc; border-radius:4px;"
          >';
    echo '<p style="color:#888; font-size:0.82em; margin-top:6px;">
            ⚠ Дата не может быть в прошлом.
          </p>';
}


// 5.3 — Сохранение с валидацией и nonce
function usm_save_deadline($post_id)
{

    // Пропускаем автосохранение
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
        return;

    // Проверяем наличие nonce
    if (!isset($_POST['usm_deadline_nonce']))
        return;

    // Верифицируем nonce
    if (!wp_verify_nonce($_POST['usm_deadline_nonce'], 'usm_save_deadline'))
        return;

    // Проверяем права пользователя
    if (!current_user_can('edit_post', $post_id))
        return;

    // Проверяем наличие поля
    if (!isset($_POST['usm_deadline']))
        return;

    $date = sanitize_text_field($_POST['usm_deadline']);

    // Валидация: дата не должна быть в прошлом
    if (!empty($date) && $date < date('Y-m-d')) {
        set_transient(
            'usm_deadline_error_' . $post_id,
            '⛔ Ошибка: дедлайн не может быть в прошлом! Изменения не сохранены.',
            45
        );
        return;
    }

    // Сохраняем дату
    if (!empty($date)) {
        update_post_meta($post_id, '_usm_deadline', $date);
    } else {
        delete_post_meta($post_id, '_usm_deadline');
    }
}
add_action('save_post', 'usm_save_deadline');


// 5.4 — Вывод сообщения об ошибке в админке
function usm_show_deadline_error()
{
    global $post;
    if (!$post)
        return;

    $error = get_transient('usm_deadline_error_' . $post->ID);
    if ($error) {
        echo '<div class="notice notice-error is-dismissible">';
        echo '<p><strong>' . esc_html($error) . '</strong></p>';
        echo '</div>';
        delete_transient('usm_deadline_error_' . $post->ID);
    }
}
add_action('admin_notices', 'usm_show_deadline_error');


// 5.5 — Колонка «Дедлайн» в списке задач в админке
function usm_add_deadline_column($columns)
{
    $columns['deadline'] = '📅 Дедлайн';
    return $columns;
}
add_filter('manage_usm_task_posts_columns', 'usm_add_deadline_column');

function usm_show_deadline_column($column, $post_id)
{
    if ($column === 'deadline') {
        $date = get_post_meta($post_id, '_usm_deadline', true);
        if ($date) {
            $past = $date < date('Y-m-d');
            $color = $past ? 'color:#e74c3c;' : 'color:#27ae60;';
            echo '<span style="' . $color . 'font-weight:600;">'
                . esc_html($date) . '</span>';
            if ($past)
                echo ' <span style="color:#e74c3c;">(просрочено)</span>';
        } else {
            echo '<span style="color:#aaa;">—</span>';
        }
    }
}
add_action('manage_usm_task_posts_custom_column', 'usm_show_deadline_column', 10, 2);


/* =============================================================
   ШАГ 6. ШОРТКОД [usm_tasks]
   ============================================================= */

function usm_tasks_shortcode($atts)
{
    $atts = shortcode_atts([
        'priority' => '',
        'before_date' => '',
    ], $atts);

    $args = [
        'post_type' => 'usm_task',
        'posts_per_page' => -1,
        'post_status' => 'publish',
        'orderby' => 'meta_value',
        'meta_key' => '_usm_deadline',
        'order' => 'ASC',
    ];

    // Фильтр по таксономии «Приоритет»
    if (!empty($atts['priority'])) {
        $args['tax_query'] = [
            [
                'taxonomy' => 'priority',
                'field' => 'slug',
                'terms' => sanitize_text_field($atts['priority']),
            ]
        ];
    }

    // Фильтр по дедлайну
    if (!empty($atts['before_date'])) {
        $args['meta_query'] = [
            [
                'key' => '_usm_deadline',
                'value' => sanitize_text_field($atts['before_date']),
                'compare' => '<=',
                'type' => 'DATE',
            ]
        ];
    }

    $tasks = new WP_Query($args);

    if (!$tasks->have_posts()) {
        return '<p class="usm-empty">Нет задач с заданными параметрами</p>';
    }

    $output = '<ul class="usm-tasks-list">';

    while ($tasks->have_posts()) {
        $tasks->the_post();

        $deadline = get_post_meta(get_the_ID(), '_usm_deadline', true);
        $terms = get_the_terms(get_the_ID(), 'priority');
        $prio_name = (!empty($terms) && !is_wp_error($terms))
            ? $terms[0]->name : '—';
        $prio_slug = (!empty($terms) && !is_wp_error($terms))
            ? strtolower($terms[0]->slug) : 'none';

        $is_overdue = $deadline && $deadline < date('Y-m-d');

        $output .= '<li class="usm-task usm-priority-' . esc_attr($prio_slug)
            . ($is_overdue ? ' usm-overdue' : '') . '">';

        $output .= '<div class="usm-task-header">';
        $output .= '<strong class="usm-task-title">' . get_the_title() . '</strong>';
        $output .= '<span class="usm-badge usm-badge-' . esc_attr($prio_slug) . '">'
            . esc_html($prio_name) . '</span>';
        $output .= '</div>';

        $output .= '<div class="usm-task-meta">';
        $output .= '<span class="usm-deadline">';
        $output .= '📅 Дедлайн: <strong>' . esc_html($deadline ?: '—') . '</strong>';
        if ($is_overdue) {
            $output .= ' <span class="usm-overdue-label">просрочено</span>';
        }
        $output .= '</span>';
        $output .= '</div>';

        $content = get_the_excerpt() ?: wp_trim_words(get_the_content(), 18, '...');
        if ($content) {
            $output .= '<div class="usm-task-content">' . esc_html($content) . '</div>';
        }

        $output .= '</li>';
    }

    $output .= '</ul>';
    wp_reset_postdata();

    return $output;
}
add_shortcode('usm_tasks', 'usm_tasks_shortcode');


// Подключение CSS на фронтенде
function usm_enqueue_styles()
{
    wp_enqueue_style(
        'usm-tasks-style',
        plugin_dir_url(__FILE__) . 'style.css',
        [],
        '1.0.0'
    );
}
add_action('wp_enqueue_scripts', 'usm_enqueue_styles');