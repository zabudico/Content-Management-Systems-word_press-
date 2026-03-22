<?php get_header(); ?>

<main class="site-content">
    <div class="container">
        <div class="content-area">

            <?php while (have_posts()):
                the_post(); ?>

                <article id="page-<?php the_ID(); ?>" <?php post_class('static-page'); ?>>

                    <header class="entry-header">
                        <h1 class="entry-title">
                            <?php the_title(); ?>
                        </h1>
                    </header>

                    <?php if (has_post_thumbnail()): ?>
                        <div class="page-thumbnail">
                            <?php the_post_thumbnail('large'); ?>
                        </div>
                    <?php endif; ?>

                    <div class="entry-content">
                        <?php the_content(); ?>
                    </div>

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