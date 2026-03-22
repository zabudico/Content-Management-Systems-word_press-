<?php
/**
 * USM Theme — functions.php
 * Функции и определения темы.
 */

// Базовая настройка темы
function usm_theme_setup()
{

    // Переводы
    load_theme_textdomain('usm-theme', get_template_directory() . '/languages');

    // Поддержка тега <title> (автоматическая генерация)
    add_theme_support('title-tag');

    // Миниатюры записей
    add_theme_support('post-thumbnails');
    set_post_thumbnail_size(800, 450, true);

    // HTML5-разметка
    add_theme_support('html5', array(
        'comment-list',
        'comment-form',
        'search-form',
        'gallery',
        'caption',
        'navigation-widgets',
    ));

    // Автоматическое добавление RSS-ссылок в <head>
    add_theme_support('automatic-feed-links');

    // Регистрация меню навигации
    register_nav_menus(array(
        'primary' => __('Основное меню', 'usm-theme'),
        'footer' => __('Меню подвала', 'usm-theme'),
    ));
}
add_action('after_setup_theme', 'usm_theme_setup');


// Подключение стилей и скриптов
function usm_theme_scripts()
{

    // Основной стиль темы (style.css)
    wp_enqueue_style(
        'usm-theme-style',
        get_stylesheet_uri(),
        array(),
        wp_get_theme()->get('Version')
    );

    // Google Fonts (опционально)
    wp_enqueue_style(
        'usm-google-fonts',
        'https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap',
        array(),
        null
    );
}
add_action('wp_enqueue_scripts', 'usm_theme_scripts');


// Регистрация сайдбаров
function usm_theme_widgets_init()
{

    // Основной сайдбар
    register_sidebar(array(
        'name' => __('Боковая панель', 'usm-theme'),
        'id' => 'sidebar-1',
        'description' => __('Добавьте виджеты в боковую панель.', 'usm-theme'),
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h3 class="widget-title">',
        'after_title' => '</h3>',
    ));

    // Сайдбар подвала
    register_sidebar(array(
        'name' => __('Подвал', 'usm-theme'),
        'id' => 'footer-1',
        'description' => __('Виджеты в подвале.', 'usm-theme'),
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h3 class="widget-title">',
        'after_title' => '</h3>',
    ));
}
add_action('widgets_init', 'usm_theme_widgets_init');


// Длина автоматического excerpt
function usm_custom_excerpt_length($length)
{
    return 25;
}
add_filter('excerpt_length', 'usm_custom_excerpt_length', 999);


// Текст "читать далее" для excerpt
function usm_excerpt_more($more)
{
    return '…';
}
add_filter('excerpt_more', 'usm_excerpt_more');