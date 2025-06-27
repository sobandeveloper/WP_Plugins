<?php
class EFAP_Shortcode_Table {

    public static function init() {
        add_shortcode('efap_table', [__CLASS__, 'render_shortcode']);
    }

    public static function render_shortcode($atts) {
        $atts = shortcode_atts([
            'label' => '',
            'show_status' => 'yes',
            'status_filter' => ''
        ], $atts, 'efap_table');

        if (empty($atts['label'])) return '<p>No label provided.</p>';

        $entries = EFAP_DB_Handler::get_entries_by_label($atts['label']);
        $show_status = $atts['show_status'] === 'yes';
        $filters = array_map('trim', explode(',', $atts['status_filter']));

        ob_start();
        echo '<table style="width:100%; border-collapse:collapse; border:1px solid #ccc;">';
        echo '<thead><tr>';
        echo '<th>ID</th>';

        if (!empty($entries)) {
            $first_entry = json_decode($entries[0]['data'], true);
            foreach ($first_entry as $key => $val) {
                echo '<th>' . esc_html($key) . '</th>';
            }
        }

        if ($show_status) {
            echo '<th>Status</th>';
        }
        echo '<th>Date</th>';
        echo '</tr></thead><tbody>';

        foreach ($entries as $entry) {
            if (!empty($filters) && !in_array($entry['status'], $filters)) continue;

            echo '<tr>';
            echo '<td>' . esc_html($entry['id']) . '</td>';

            $data = json_decode($entry['data'], true);
            foreach ($data as $key => $val) {
                echo '<td>' . esc_html($val['value']) . '</td>';
            }

            if ($show_status) {
                echo '<td>' . esc_html($entry['status']) . '</td>';
            }

            echo '<td>' . esc_html($entry['created_at']) . '</td>';
            echo '</tr>';
        }

        echo '</tbody></table>';
        return ob_get_clean();
    }
}

EFAP_Shortcode_Table::init();
