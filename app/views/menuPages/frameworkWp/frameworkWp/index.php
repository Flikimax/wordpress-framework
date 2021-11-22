<div class="title">
    <h1><?=get_admin_page_title(); ?></h1>
</div>

<div class="fw-form-create-plugin">
    <form method="POST">
        <?=wp_nonce_field('create-plugin-nonce', 'fwNonce'); ?>
        <div class="input-container">
            <label for="data[name]">Plugin Name</label>
            <input type="text" name="data[pluginName]" required>
        </div>
    
        <div class="input-container">
            <label for="data[description]">Description</label>
            <input type="text" name="data[description]" required>
        </div>
    
        <div class="input-container">
            <label for="data[pluginUri]">Plugin URI</label>
            <input type="url" name="data[pluginUri]" placeholder="https://example.com" pattern="https://.*" size="60">
        </div>
    
        <div class="input-container">
            <label for="data[author]">Author</label>
            <input type="text" name="data[author]" >
        </div>
    
        <div class="input-container">
            <label for="data[authorUri]">Author URI</label>
            <input type="url" name="data[authorUri]" placeholder="https://example.com" pattern="https://.*" size="60">
        </div>
    
        <div class="input-container">
            <label for="data[version]">Version</label>
            <input type="number" step="0.01" name="data[version]">
        </div>
    
        <div class="input-container">
            <button>Create base component</button>
        </div>

    </form>
</div>