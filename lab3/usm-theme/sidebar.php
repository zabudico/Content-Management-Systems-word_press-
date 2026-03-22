<?php if (is_active_sidebar('sidebar-1')): ?>

    <aside class="widget-area" aria-label="Боковая панель">
        <?php dynamic_sidebar('sidebar-1'); ?>
    </aside>

    <?php
else:
    // Если виджетов нет, показываем дефолтный блок
    ?>
    <aside class="widget-area">
        <div class="widget">
            <h3 class="widget-title">О сайте</h3>
            <p>
                <?php bloginfo('description'); ?>
            </p>
        </div>
        <div class="widget">
            <h3 class="widget-title">Последние записи</h3>
            <?php
            $recent = new WP_Query(array('posts_per_page' => 5));
            if ($recent->have_posts()):
                ?>
                <ul>
                    <?php while ($recent->have_posts()):
                        $recent->the_post(); ?>
                        <li><a href="<?php the_permalink(); ?>">
                                <?php the_title(); ?>
                            </a></li>
                    <?php endwhile;
                    wp_reset_postdata(); ?>
                </ul>
            <?php endif; ?>
        </div>
    </aside>
<?php endif; ?>