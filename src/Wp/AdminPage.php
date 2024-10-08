<?php

namespace wpSfv\Wp;

class AdminPage
{

    public function __construct()
    {
        add_action('admin_menu', [ $this, 'add_adminMenu' ]);
        add_action('admin_init', [ $this, 'register_settings' ]);
        add_action('update_option_wpSfv_saver_input', [ $this, 'update_config' ], 10, 2);
    }

    public function add_adminMenu()
    {
        add_menu_page(
            'WP SFV Admin',
            'WP SFV',
            'manage_options',
            'wp-sfv-admin',
            [$this, 'renderAdminPage'],
        );
    }

    public function register_settings()
    {
        register_setting('wpSfv_saver_group', 'wpSfv_saver_input');
    }

    function renderAdminPage()
    {
        ?>
          <div class="wrap">
               <h1>JSON Saver</h1>
               <form method="post" action="options.php">
                   <?php
                   settings_fields( 'wpSfv_saver_group' );
                   do_settings_sections( 'wp-sfv-admin' );
                   ?>
                   <textarea name="wpSfv_saver_input" rows="10" cols="50"><?php echo esc_textarea( get_option( 'wpSfv_saver_input', '' ) ); ?></textarea>
                   <?php submit_button( 'Speichern' ); ?>
               </form>
           </div>
        <?php
    }

    public function update_config($oldValue, $value)
    {
        $pluginDir = dirname(plugin_dir_path( __FILE__ ), 2);
        $file = $pluginDir.'/config/config.json';
        file_put_contents($file, $value);
    }
}