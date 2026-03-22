<?php
if (post_password_required()) {
    return;
}
?>

<div id="comments" class="comments-area">

    <?php if (have_comments()): ?>

        <h2 class="comments-title">
            <?php
            $count = get_comments_number();
            if ('1' === $count) {
                printf('1 комментарий к «%s»', get_the_title());
            } else {
                printf('%1$s комментариев к «%2$s»', number_format_i18n($count), get_the_title());
            }
            ?>
        </h2>

        <ol class="comment-list">
            <?php
            wp_list_comments(array(
                'style' => 'ol',
                'short_ping' => true,
                'avatar_size' => 48,
            ));
            ?>
        </ol>

        <?php the_comments_pagination(); ?>

    <?php endif; ?>

    <?php if (!comments_open() && get_comments_number() && post_type_supports(get_post_type(), 'comments')): ?>
        <p class="no-comments">Комментарии закрыты.</p>
    <?php endif; ?>

    <?php
    comment_form(array(
        'title_reply' => 'Оставить комментарий',
        'title_reply_before' => '<h2 id="reply-title" class="comment-reply-title">',
        'title_reply_after' => '</h2>',
        'label_submit' => 'Отправить',
        'comment_notes_before' => '<p class="comment-notes">Email не будет опубликован.</p>',
    ));
    ?>

</div>