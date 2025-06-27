<?php
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

class EFAP_Table_Widget extends Widget_Base {

    public function get_name() {
        return 'efap_table_widget';
    }

    public function get_title() {
        return __('Form Entries Table', 'efap');
    }

    public function get_icon() {
        return 'eicon-table';
    }

    public function get_categories() {
        return ['general'];
    }

    protected function register_controls() {
        // --- Content Section ---
        $this->start_controls_section('content_section', [
            'label' => __('Content', 'efap'),
            'tab' => Controls_Manager::TAB_CONTENT,
        ]);

        $labels = EFAP_DB_Handler::get_labels();
        $options = [];
        foreach ($labels as $label) {
            $options[$label] = ucwords($label);
        }

        $this->add_control('label_name', [
            'label' => __('Select Entry Label', 'efap'),
            'type' => Controls_Manager::SELECT,
            'options' => $options,
        ]);

        $this->add_control('show_status', [
            'label' => __('Show Status Column?', 'efap'),
            'type' => Controls_Manager::SWITCHER,
            'default' => 'yes',
        ]);

        $this->add_control('filter_status', [
            'label' => __('Filter by Status (optional)', 'efap'),
            'type' => Controls_Manager::TEXT,
            'description' => 'Comma-separated values, e.g., new,approved',
        ]);

        $this->end_controls_section();

        // --- Header Style ---
        $this->start_controls_section('header_style', [
            'label' => __('Header Style', 'efap'),
            'tab' => Controls_Manager::TAB_STYLE,
        ]);

        $this->add_control('header_bg_color', [
            'label' => __('Background Color', 'efap'),
            'type' => Controls_Manager::COLOR,
            'default' => '#f5f5f5',
        ]);

        $this->add_control('heading_color', [
            'label' => __('Text Color', 'efap'),
            'type' => Controls_Manager::COLOR,
            'default' => '#000',
        ]);

        $this->add_control('header_border_color', [
            'label' => __('Border Color', 'efap'),
            'type' => Controls_Manager::COLOR,
            'default' => '#ccc',
        ]);

        $this->add_control('header_border_width', [
            'label' => __('Border Width (px)', 'efap'),
            'type' => Controls_Manager::NUMBER,
            'default' => 1,
        ]);

        $this->add_control('header_align', [
            'label' => __('Text Alignment', 'efap'),
            'type' => Controls_Manager::SELECT,
            'options' => ['left' => 'Left', 'center' => 'Center', 'right' => 'Right'],
            'default' => 'center',
        ]);

        $this->add_group_control(Group_Control_Typography::get_type(), [
            'name' => 'header_typography',
            'label' => __('Typography', 'efap'),
            'selector' => '{{WRAPPER}} th',
        ]);

        $this->end_controls_section();

        // --- Row Style ---
        $this->start_controls_section('row_style', [
            'label' => __('Row Style', 'efap'),
            'tab' => Controls_Manager::TAB_STYLE,
        ]);

        $this->add_control('row_bg_color', [
            'label' => __('Row Background Color', 'efap'),
            'type' => Controls_Manager::COLOR,
            'default' => '#fff',
        ]);

        $this->add_control('row_striped_color', [
            'label' => __('Striped Row Color (odd rows)', 'efap'),
            'type' => Controls_Manager::COLOR,
            'default' => '#f9f9f9',
        ]);

        $this->add_control('cell_color', [
            'label' => __('Text Color', 'efap'),
            'type' => Controls_Manager::COLOR,
            'default' => '#333',
        ]);

        $this->add_control('cell_border_color', [
            'label' => __('Border Color', 'efap'),
            'type' => Controls_Manager::COLOR,
            'default' => '#ddd',
        ]);

        $this->add_control('cell_border_width', [
            'label' => __('Border Width (px)', 'efap'),
            'type' => Controls_Manager::NUMBER,
            'default' => 1,
        ]);

        $this->add_control('cell_align', [
            'label' => __('Text Alignment', 'efap'),
            'type' => Controls_Manager::SELECT,
            'options' => ['left' => 'Left', 'center' => 'Center', 'right' => 'Right'],
            'default' => 'left',
        ]);

        $this->add_group_control(Group_Control_Typography::get_type(), [
            'name' => 'cell_typography',
            'label' => __('Typography', 'efap'),
            'selector' => '{{WRAPPER}} td',
        ]);

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $label = strtolower(trim($settings['label_name']));
        $entries = EFAP_DB_Handler::get_entries_by_label($label);
        $show_status = $settings['show_status'] === 'yes';
        $filters = array_filter(array_map('trim', explode(',', $settings['filter_status'])));

        $header_styles = sprintf(
            'background:%s; color:%s; border:%dpx solid %s; text-align:%s;',
            esc_attr($settings['header_bg_color']),
            esc_attr($settings['heading_color']),
            $settings['header_border_width'],
            esc_attr($settings['header_border_color']),
            esc_attr($settings['header_align'])
        );

        $cell_styles = sprintf(
            'color:%s; border:%dpx solid %s; text-align:%s;',
            esc_attr($settings['cell_color']),
            $settings['cell_border_width'],
            esc_attr($settings['cell_border_color']),
            esc_attr($settings['cell_align'])
        );

        echo '<table style="width:100%; border-collapse:collapse;">';
        echo '<thead><tr>';
        echo '<th style="' . $header_styles . '">ID</th>';

        if (!empty($entries)) {
            $first_entry = json_decode($entries[0]['data'], true);
            foreach ($first_entry as $key => $val) {
                echo '<th style="' . $header_styles . '">' . esc_html($key) . '</th>';
            }
        }

        if ($show_status) {
            echo '<th style="' . $header_styles . '">Status</th>';
        }

        echo '<th style="' . $header_styles . '">Date</th>';
        echo '</tr></thead><tbody>';

        if (empty($entries)) {
            echo '<tr><td colspan="100%" style="text-align:center;">No entries found for label: ' . esc_html($label) . '</td></tr>';
        }

        $row_index = 0;
        foreach ($entries as $entry) {
            if (!empty($filters) && !in_array($entry['status'], $filters)) {
                continue;
            }

            $row_color = ($row_index % 2 === 1) ? $settings['row_striped_color'] : $settings['row_bg_color'];
            echo '<tr style="background:' . esc_attr($row_color) . ';">';
            echo '<td style="' . $cell_styles . '">' . esc_html($entry['id']) . '</td>';

            $data = json_decode($entry['data'], true);
            foreach ($data as $key => $val) {
                $display = is_array($val) ? ($val['value'] ?? '') : $val;
                echo '<td style="' . $cell_styles . '">' . esc_html($display) . '</td>';
            }

            if ($show_status) {
                echo '<td style="' . $cell_styles . '">' . esc_html($entry['status']) . '</td>';
            }

            echo '<td style="' . $cell_styles . '">' . esc_html($entry['created_at']) . '</td>';
            echo '</tr>';

            $row_index++;
        }

        echo '</tbody></table>';
    }
}
