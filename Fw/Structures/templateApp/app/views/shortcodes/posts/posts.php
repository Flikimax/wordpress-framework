<div class="wp-shortcode-content">
    <?php foreach ($posts as $post) : ?>
        <div class="wp-shortcode-post">
            <a href="<?=get_post_permalink($post->ID); ?>">
                <h2><?=$post->post_title; ?></h2>
            </a>
            <p class="excerpt"><?=$post->post_excerpt; ?></p>
            <p class="author"><strong>Author: </strong><?=get_the_author(); ?></p>
        </div>
    <?php endforeach; ?>
</div>