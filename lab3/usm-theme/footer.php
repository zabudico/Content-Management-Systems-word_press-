<footer class="site-footer">
    <div class="footer-inner">

        <div class="footer-widgets">
            <?php if (is_active_sidebar('footer-1')): ?>
                <div class="footer-widget-area">
                    <?php dynamic_sidebar('footer-1'); ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="footer-bottom">
            <p class="copyright">
                &copy;
                <?php echo date('Y'); ?>
                <a href="<?php echo esc_url(home_url('/')); ?>">
                    <?php bloginfo('name'); ?>
                </a>.
                Лабораторная работа — USM, Факультет МИ.
            </p>
        </div>

    </div>
</footer>

<?php wp_footer(); ?>
</body>

</html>