<?php get_header(); ?>

<main class="site-content">
    <div class="container">
        <div class="content-area">

            <header class="archive-header">
                <h1 class="archive-title">
                    <?php
                    if (is_category()) {
                        echo 'Категория: ';
                        single_cat_title();
                    } elseif (is_tag()) {
                        echo 'Тег: ';
                        single_tag_title();
                    } elseif (is_author()) {
                        echo 'Автор: ';
                        the_author();
                    } elseif (is_year()) {
                        echo 'Год: ' . get_the_date('Y');
                    } elseif (is_month()) {
                        echo 'Месяц: ' . get_the_date('F Y');
                    } elseif (is_day()) {
                        echo 'День: ' . get_the_date();
                    }
                    ?>
                </h1>

                <?php
                $archive_description = get_the_archive_description();
                if ($archive_description): ?>
                    <div class="archive-description">
                        <?php echo $archive_description; ?>
                    </div>
                <?php endif; ?>
            </header>

            <?php if (have_posts()): ?>

                <?php while (have_posts()):
                    the_post(); ?>

                    <article <?php post_class('archive-entry post-card'); ?>>
                        <h2 class="entry-title">
                            <a href="<?php the_permalink(); ?>">
                                <?php the_title(); ?>
                            </a>
                        </h2>
                        <div class="entry-meta">
                            <?php echo get_the_date(); ?>
                        </div>
                        <div class="entry-summary">
                            <?php the_excerpt(); ?>
                        </div>
                    </article>

                <?php endwhile; ?>

                <div class="pagination">
                    <?php the_posts_pagination(); ?>
                </div>
            <?php else: ?>
                <p>Записи не найдены.</p>
            <?php endif; ?>

        </div>
        <?php get_sidebar(); ?>
    </div>
</main>

<?php get_footer(); ?>