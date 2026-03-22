<?php get_header(); ?>

<main class="site-content">
    <div class="container">
        <div class="content-area">

            <?php while (have_posts()):
                the_post(); ?>

                <article id="post-<?php the_ID(); ?>" <?php post_class('single-post'); ?>>

                    <header class="entry-header">
                        <h1 class="entry-title">
                            <?php the_title(); ?>
                        </h1>
                        <div class="entry-meta">
                            Опубликовано:
                            <?php echo get_the_date(); ?>
                            автором <strong>
                                <?php the_author(); ?>
                            </strong>
                            | Категории:
                            <?php the_category(', '); ?>
                            | Теги:
                            <?php the_tags('', ', '); ?>
                        </div>
                    </header>

                    <?php if (has_post_thumbnail()): ?>
                        <div class="single-thumbnail">
                            <?php the_post_thumbnail('large'); ?>
                        </div>
                    <?php endif; ?>
                    <div class="entry-content">
                        <?php
                        the_content();
                        wp_link_pages(array(
                            'before' => '<div class="page-links">Страницы:',
                            'after' => '</div>',
                        ));
                        ?>
                    </div>

                    <footer class="entry-footer">
                        <div class="post-navigation">
                            <?php
                            the_post_navigation(array(
                                'prev_text' => '← %title',
                                'next_text' => '%title →',
                            ));
                            ?>
                        </div>
                    </footer>

                </article>

                <?php
                if (comments_open() || get_comments_number()) {
                    comments_template();
                }
                ?>

            <?php endwhile; ?>

        </div>
        <?php get_sidebar(); ?>
    </div>
</main>

<?php get_footer(); ?>