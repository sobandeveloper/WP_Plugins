<?php
use ElementorPro\Modules\Forms\Classes\Action_Base;

if ( ! defined( 'ABSPATH' ) ) exit;

class EFAP_Form_Action extends Action_Base {

    public function get_name() {
        return 'save_to_db';
    }

    public function get_label() {
        return __('Save to DB', 'efap');
    }

    public function register_settings_section($widget) {
        $widget->start_controls_section(
            'section_save_to_db',
            [
                'label' => __('Save to DB Settings', 'efap'),
                'condition' => [
                    'submit_actions' => 'save_to_db',
                ],
            ]
        );

        $widget->add_control(
            'efap_enable_save',
            [
                'label' => __('Enable DB Save', 'efap'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'efap'),
                'label_off' => __('No', 'efap'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $widget->add_control(
            'efap_label_name',
            [
                'label' => __('Label Name (e.g. bookings)', 'efap'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'placeholder' => 'bookings',
                'condition' => [
                    'efap_enable_save' => 'yes',
                ],
            ]
        );

        $widget->end_controls_section();
    }

    public function run($record, $ajax_handler) {
    $settings = $record->get('settings');

    if (empty($settings['efap_enable_save']) || $settings['efap_enable_save'] !== 'yes') {
        return;
    }

    $label = sanitize_text_field($settings['efap_label_name']);
    if (empty($label)) return;

    $fields = $record->get('fields');
    EFAP_DB_Handler::save_entry($label, $fields);
}


    public function on_export($element) {
        return $element;
    }
}
