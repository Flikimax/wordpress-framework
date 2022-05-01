<div class="wp-shortcode-content">
    <?php foreach ($categories as $category) : ?>
        <div class="wp-shortcode-post">
            <a href="<?=site_url( "{$category->taxonomy}/{$category->slug}" ); ?>">
                <h2><?=$category->name; ?></h2>
            </a>
            <p class="excerpt"><?=$category->category_description; ?></p>
        </div>
    <?php endforeach; ?>
</div>

