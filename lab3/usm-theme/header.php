<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>
        <?php
        if (is_front_page()) {
            bloginfo('name');
            echo ' — ';
            bloginfo('description');
        } else {
            wp_title('|', true, 'right');
            bloginfo('name');
        }
        ?>
    </title>
    <link rel="profile" href="https://gmpg.org/xfn/11">
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
    <?php wp_body_open(); ?>

    <header class="site-header">
        <div class="header-inner">

            <div class="site-branding">
                <h1 class="site-title">
                    <a href="<?php echo esc_url(home_url('/')); ?>" rel="home">
                        <?php bloginfo('name'); ?>
                    </a>
                </h1>
                <?php
                $description = get_bloginfo('description', 'display');
                if ($description):
                    ?>
                    <p class="site-description">
                        <?php echo $description; ?>
                    </p>
                <?php endif; ?>
            </div>

            <nav class="main-navigation" aria-label="Основное меню">
                <?php
                wp_nav_menu(array(
                    'theme_location' => 'primary',
                    'menu_id' => 'primary-menu',
                    'menu_class' => 'nav-menu',
                    'fallback_cb' => false,
                ));
                ?>
            </nav>

        </div>
    </header>