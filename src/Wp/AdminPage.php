<?php

namespace wpSfv\Wp;

class AdminPage
{

    public function __construct()
    {
        add_action('admin_menu', [ $this, 'add_adminMenu' ]);
        add_action('admin_init', [ $this, 'register_settings' ]);
        add_action('update_option_wpSfv_saver_input', [ $this, 'update_config' ], 10, 3);
        add_action('admin_notices', [ $this, 'admin_notice' ]);
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

    public function admin_notice()
    {
        $msg = get_option('wpSfv_config_update_notice');
        if ($msg) {
            echo '<div class="notice notice-success is-dismissible"><p>'.esc_html($msg).'</p></div>';
            delete_option('wpSfv_config_update_notice');
        }
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
        $pluginDir = dirname(plugin_dir_path(__FILE__), 2);
        $file = $pluginDir . '/config/config.json';

        // Verzeichnis sicherstellen
        if (!is_dir(dirname($file))) {
            wp_mkdir_p(dirname($file));
        }

        // Schreibbarkeit prüfen
        if (!is_writable(dirname($file))) {
            error_log('config dir not writable: '.dirname($file));
            update_option('wpSfv_config_update_notice', 'Fehler: Zielordner nicht beschreibbar: '.dirname($file));
            return;
        }

        // Optional: JSON validieren/formatieren
        $decoded = json_decode($value, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            error_log('invalid JSON: '.json_last_error_msg());
            update_option('wpSfv_config_update_notice', 'Fehler: Ungültiges JSON – '.json_last_error_msg());
            return;
        }
        $pretty = json_encode($decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

        $bytes = file_put_contents($file, $pretty);
        if ($bytes === false) {
            error_log('failed to write config.json');
            update_option('wpSfv_config_update_notice', 'Fehler: Schreiben der config.json fehlgeschlagen.');
            return;
        }

        // Erfolgsmeldung setzen
        update_option('wpSfv_config_update_notice', 'Config aktualisiert und gespeichert: '.basename($file));

    }
}