<div class="wrap">
    <div class="icon32" id="icon-options-general"></div>
    <h2>Blog Settings</h2>

    <?php if (isset($_GET['settings-updated']) && $_GET['settings-updated'] == true) : ?>
    <div class="updated fade">
        <p>Changes saved.</p>
    </div>
    <?php endif; ?>

    <form action="options.php" method="post">

        <?php settings_fields('blog_settings'); ?>
        <?php do_settings_sections($_GET['page']); ?>
        <p class="submit">
            <input name="Submit" type="submit" class="button-primary" value="Save Settings" />
        </p>
    </form>

</div>