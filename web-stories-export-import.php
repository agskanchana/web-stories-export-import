<?php
/**
 * Plugin Name: Web Stories Content Transfer (Exact Export/Import)
 * Description: Exports and imports post_content_filtered exactly as it is in the database.
 * Version: 1.1.3
 * Author: Ekwa Marketing
 */

if (!defined('ABSPATH')) exit;

require 'includes/plugin-update-checker/plugin-update-checker.php';
use YahnisElsts\PluginUpdateChecker\v5\PucFactory;

$myUpdateChecker = PucFactory::buildUpdateChecker(
	'https://github.com/agskanchana/web-stories-export-import/',
	__FILE__,
	'web-stories-export-import'
);

/**
 * Export Web Stories post_content_filtered as CSV without escaping.
 */
function web_stories_export_csv() {
    if (!current_user_can('manage_options')) {
        return;
    }

    global $wpdb;

    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename=web-stories-content.csv');
    $output = fopen('php://output', 'w');

    fputcsv($output, ['post_id', 'post_content_filtered']); // CSV Headers

    $stories = $wpdb->get_results("SELECT ID, post_content_filtered FROM {$wpdb->posts} WHERE post_type = 'web-story'");

    foreach ($stories as $story) {
        // Output the raw content without escaping
        fputcsv($output, [$story->ID, $story->post_content_filtered]);
    }

    fclose($output);
    exit;
}
add_action('admin_post_web_stories_export_csv', 'web_stories_export_csv');

/**
 * Add Export Button to Admin Menu.
 */
function web_stories_export_menu() {
    add_submenu_page(
        'tools.php',
        'Export Web Stories Content',
        'Export Web Stories Content',
        'manage_options',
        'web-stories-export',
        function () {
            echo '<div class="wrap"><h1>Export Web Stories Content</h1>';
            echo '<a href="' . admin_url('admin-post.php?action=web_stories_export_csv') . '" class="button button-primary">Download CSV</a>';
            echo '</div>';
        }
    );
}
add_action('admin_menu', 'web_stories_export_menu');

/**
 * Import Web Stories post_content_filtered from CSV without altering data.
 */
function web_stories_import_csv() {
    if (!current_user_can('manage_options')) {
        wp_die('You do not have permission to import.');
    }

    if (empty($_FILES['web_stories_csv']['tmp_name'])) {
        wp_die('No file uploaded.');
    }

    global $wpdb;

    $file = fopen($_FILES['web_stories_csv']['tmp_name'], 'r');
    fgetcsv($file); // Skip the header row

    $old_site_url = ''; // Placeholder for detecting old URLs
    $new_site_url = get_site_url(); // Get the current site's URL

    while ($row = fgetcsv($file)) {
        $post_id = intval($row[0]);
        $post_content_filtered = $row[1];

        if ($post_id && !empty($post_content_filtered)) {
            // Extract old site URL if not already set
            if (!$old_site_url) {
                // Convert escaped slashes to regular slashes for easier processing
                $temp_content = str_replace('\/', '/', $post_content_filtered);

                // Find URL pattern that ends with /wp-content/
                if (preg_match('/(https?:\/\/[^\/]+(?:\/[^\/]+)*?)\/wp-content\//', $temp_content, $matches)) {
                    $old_site_url = $matches[1];
                }
            }

            // Replace old site URL with new site URL while maintaining escaped format
            if ($old_site_url) {
                // Convert escaped slashes to regular slashes for replacement
                $temp_content = str_replace('\/', '/', $post_content_filtered);

                // Do the URL replacement
                $temp_content = str_replace($old_site_url, $new_site_url, $temp_content);

                // Convert back to escaped format
                $post_content_filtered = str_replace('/', '\/', $temp_content);
            }

            // Update the database with the modified content
            $wpdb->update(
                $wpdb->posts,
                ['post_content_filtered' => $post_content_filtered],
                ['ID' => $post_id],
                ['%s'],
                ['%d']
            );
        }
    }

    fclose($file);
    wp_redirect(admin_url('tools.php?page=web-stories-import&success=1'));
    exit;
}
add_action('admin_post_web_stories_import_csv', 'web_stories_import_csv');

/**
 * Add Import Button to Admin Menu.
 */
function web_stories_import_menu() {
    add_submenu_page(
        'tools.php',
        'Import Web Stories Content',
        'Import Web Stories Content',
        'manage_options',
        'web-stories-import',
        function () {
            echo '<div class="wrap"><h1>Import Web Stories Content</h1>';
            if (!empty($_GET['success'])) {
                echo '<div class="updated notice"><p>Import successful!</p></div>';
            }
            echo '<form method="post" enctype="multipart/form-data" action="' . admin_url('admin-post.php?action=web_stories_import_csv') . '">';
            echo '<input type="file" name="web_stories_csv" accept=".csv" required>';
            echo '<button type="submit" class="button button-primary">Upload & Import</button>';
            echo '</form></div>';
        }
    );
}
add_action('admin_menu', 'web_stories_import_menu');
