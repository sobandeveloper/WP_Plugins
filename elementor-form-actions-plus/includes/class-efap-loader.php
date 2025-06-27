<?php
class EFAP_Loader {

    public static function init() {
        add_action('init', [__CLASS__, 'load_dependencies']);
        add_action('admin_menu', [__CLASS__, 'register_admin_menu']);
        add_action('elementor_pro/forms/new_record', [__CLASS__, 'handle_form_submission'], 10, 2);
        add_action('elementor/widgets/register', [__CLASS__, 'register_widgets']);
    }

    public static function load_dependencies() {
        require_once EFAP_PLUGIN_DIR . 'includes/class-efap-db-handler.php';
        require_once EFAP_PLUGIN_DIR . 'includes/class-efap-admin-page.php';
        require_once EFAP_PLUGIN_DIR . 'widgets/class-efap-table-widget.php';
        require_once EFAP_PLUGIN_DIR . 'shortcodes/class-efap-shortcode-table.php';
    }

    public static function register_admin_menu() {
        add_menu_page(
            'Form Entries',
            'Form Entries',
            'manage_options',
            'efap_entries',
            ['EFAP_Admin_Page', 'render'],
            'dashicons-feedback',
            26
        );
    }

    public static function handle_form_submission($record, $handler) {
        if ( ! $record || ! $handler ) return;

        //$form_name = $record->get_form_settings('efap_save_to_db');
        $form_name = $record->get_form_settings('efap_enable_save');
		$label = sanitize_text_field($record->get_form_settings('efap_label_name'));

        if ($form_name === 'yes' && ! empty($label)) {
            $fields = $record->get('fields');
            EFAP_DB_Handler::save_entry($label, $fields);
        }
    }

    public static function register_widgets($widgets_manager) {
        require_once EFAP_PLUGIN_DIR . 'widgets/class-efap-table-widget.php';
        $widgets_manager->register(new \EFAP_Table_Widget());
    }
}


add_action('elementor_pro/init', function() {
    require_once EFAP_PLUGIN_DIR . 'includes/class-efap-form-action.php';
    $forms_module = \ElementorPro\Plugin::instance()->modules_manager->get_modules('forms');
    if ( $forms_module ) {
        $forms_module->add_form_action('save_to_db', new EFAP_Form_Action());
    }
});
