<?php get_header(); ?>

<main class="site-content">
    <div class="container">
        <div class="content-area">

            <?php if (have_posts()): ?>

                <?php while (have_posts()):
                    the_post(); ?>

                    <article id="post-<?php the_ID(); ?>" <?php post_class('post-card'); ?>>

                        <?php if (has_post_thumbnail()): ?>
                            <div class="post-thumbnail">
                                <a href="<?php the_permalink(); ?>">
                                    <?php the_post_thumbnail('medium_large'); ?>
                                </a>
                            </div>
                        <?php endif; ?>

                        <div class="post-body">
                            <h2 class="entry-title">
                                <a href="<?php the_permalink(); ?>">
                                    <?php the_title(); ?>
                                </a>
                            </h2>

                            <div class="entry-meta">
                                <span class="meta-date">
                                    <?php echo get_the_date(); ?>
                                </span>
                                <span class="meta-sep">&bull;</span>
                                <span class="meta-author">
                                    <?php the_author(); ?>
                                </span>
                                <span class="meta-sep">&bull;</span>
                                <span class="meta-cats">
                                    <?php the_category(', '); ?>
                                </span>
                            </div>

                            <div class="entry-summary">
                                <?php the_excerpt(); ?>
                            </div>

                            <a href="<?php the_permalink(); ?>" class="read-more">
                                Читать далее &rarr;
                            </a>
                        </div>

                    </article>

                <?php endwhile; ?>

                <div class="pagination">
                    <?php the_posts_pagination(); ?>
                </div>

            <?php else: ?>

                <p class="no-posts">Записи не найдены.</p>

            <?php endif; ?>

        </div>

        <?php get_sidebar(); ?>

    </div>
</main>

<?php get_footer(); ?>