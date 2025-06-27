<?php
class EFAP_Admin_Page {

    public static function render() {
        if (!current_user_can('manage_options')) return;

        // Handle deletion
        if (isset($_GET['efap_delete'])) {
            EFAP_DB_Handler::delete_entry(intval($_GET['efap_delete']));
        }

        // Handle status update
        if (isset($_POST['efap_update_status'])) {
            $entry_id = intval($_POST['entry_id']);
            $new_status = sanitize_text_field($_POST['new_status']);
            EFAP_DB_Handler::update_status($entry_id, $new_status);
        }

        $labels = EFAP_DB_Handler::get_labels();
        ?>
        <div class="wrap">
            <h1>Form Entries</h1>
            <h2 class="nav-tab-wrapper">
                <?php foreach ($labels as $index => $label): ?>
                    <a href="?page=efap_entries&label=<?php echo esc_attr($label); ?>" class="nav-tab <?php echo (!isset($_GET['label']) && $index === 0) || (isset($_GET['label']) && $_GET['label'] === $label) ? 'nav-tab-active' : ''; ?>">
                        <?php echo esc_html(ucfirst($label)); ?>
                    </a>
                <?php endforeach; ?>
            </h2>

            <?php
            $current_label = $_GET['label'] ?? ($labels[0] ?? '');
            if ($current_label):
                $entries = EFAP_DB_Handler::get_entries_by_label($current_label);
            ?>
                <table class="widefat fixed striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Data</th>
                            <th>Status</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($entries as $entry): ?>
                            <tr>
                                <td><?php echo esc_html($entry['id']); ?></td>
                                <td>
                                    <?php
                                    $data = json_decode($entry['data'], true);
                                    foreach ($data as $key => $val) {
                                        echo "<strong>" . esc_html($key) . ":</strong> " . esc_html($val['value']) . "<br>";
                                    }
                                    ?>
                                </td>
                                <td>
                                    <form method="post" style="margin:0;">
                                        <input type="hidden" name="entry_id" value="<?php echo esc_attr($entry['id']); ?>">
                                        <select name="new_status">
                                            <?php
                                            $statuses = ['new', 'pending', 'approved', 'rejected'];
                                            foreach ($statuses as $status) {
                                                echo '<option value="' . esc_attr($status) . '" ' . selected($entry['status'], $status, false) . '>' . esc_html($status) . '</option>';
                                            }
                                            ?>
                                        </select>
                                        <button type="submit" name="efap_update_status" class="button">Update</button>
                                    </form>
                                </td>
                                <td><?php echo esc_html($entry['created_at']); ?></td>
                                <td>
                                    <a href="?page=efap_entries&label=<?php echo esc_attr($current_label); ?>&efap_delete=<?php echo esc_attr($entry['id']); ?>" onclick="return confirm('Delete this entry?');" class="button-link-delete">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
        <?php
    }
}
