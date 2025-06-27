<?php
class EFAP_DB_Handler {

    private static $table_name;

    public static function init() {
        global $wpdb;
        self::$table_name = $wpdb->prefix . 'efap_entries';

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE IF NOT EXISTS " . self::$table_name . " (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            label VARCHAR(100) NOT NULL,
            form_id VARCHAR(100),
            data LONGTEXT NOT NULL,
            status VARCHAR(100) DEFAULT 'new',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        ) $charset_collate;";

        dbDelta($sql);
    }

    public static function save_entry($label, $fields) {
        global $wpdb;
        self::$table_name = $wpdb->prefix . 'efap_entries';

        $data_json = wp_json_encode($fields);

        $wpdb->insert(
            self::$table_name,
            [
                'label' => $label,
                'form_id' => $label, // Using label as form ID here, can be improved later
                'data' => $data_json,
                'status' => 'new',
                'created_at' => current_time('mysql'),
            ],
            ['%s', '%s', '%s', '%s', '%s']
        );
    }

    public static function get_entries_by_label($label) {
        global $wpdb;
        self::$table_name = $wpdb->prefix . 'efap_entries';

        $results = $wpdb->get_results(
            $wpdb->prepare("SELECT * FROM " . self::$table_name . " WHERE label = %s ORDER BY created_at DESC", $label),
            ARRAY_A
        );

        return $results;
    }

    public static function delete_entry($id) {
        global $wpdb;
        self::$table_name = $wpdb->prefix . 'efap_entries';

        $wpdb->delete(self::$table_name, ['id' => $id], ['%d']);
    }

    public static function update_status($id, $new_status) {
        global $wpdb;
        self::$table_name = $wpdb->prefix . 'efap_entries';

        $wpdb->update(
            self::$table_name,
            ['status' => $new_status],
            ['id' => $id],
            ['%s'],
            ['%d']
        );
    }

    public static function get_labels() {
        global $wpdb;
        self::$table_name = $wpdb->prefix . 'efap_entries';

        $labels = $wpdb->get_col("SELECT DISTINCT label FROM " . self::$table_name);
        return $labels;
    }
}
