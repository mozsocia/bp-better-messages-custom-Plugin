<?php
/*
Plugin Name: bp better messages custom Plugin
Description: Simple Plugin shows the  form submission
Version: 1.0.0
Author: Jhon Doe
 */

//
// ========== some default defination ====================
define("MOZ_PLUGIN_DIR_PATH", plugin_dir_path(__FILE__));

/**
 * logging functions
 */
if (!function_exists('moz_debug_fn')) {
    function moz_debug_fn($data)
    {
        // to theme directory
        // $file = get_stylesheet_directory() .'/coutom_log.txt';
        // to this plugin dir
        $file = plugin_dir_path(__FILE__) . '/custom_log.txt';
        file_put_contents($file, current_time('mysql') . " :: " . print_r($data, true) . "\n\n", FILE_APPEND);
    };
}
;

if (!function_exists('moz_plugin_log')) {
    // to use in production site
    function moz_plugin_log($entry, $mode = 'a', $file = 'moz_plugin_log')
    {
        // Get WordPress uploads directory.
        $upload_dir = wp_upload_dir();
        $upload_dir = $upload_dir['basedir'];
        // the entry to json_encode.
        $entry = json_encode($entry);
        // Write the log file.
        $file = $upload_dir . '/' . $file . '.log';
        $file = fopen($file, $mode);
        $bytes = fwrite($file, current_time('mysql') . "::" . print_r($entry, true) . "\n\n");
        fclose($file);
        return $bytes;
    }
}

//
// ======================= main code start ===========================

//
// ====== setting admin_menu =============
// function moz_menus_development()
// {
//     add_menu_page("Moz Plugin", "Test Plugin", "manage_options", "wp-moz-plugin", "moz_wp_list_call");

// }

// add_action("admin_menu", "moz_menus_development");

// function moz_wp_list_call()
// {
//     include_once MOZ_PLUGIN_DIR_PATH . '/views/list-some.php';
// }

//
// ========== ajax call code must be in main plugin file

// add_action('wp_ajax_my_ajax_form_action', 'my_ajax_form_handler');
// add_action('wp_ajax_nopriv_my_ajax_form_action', 'my_ajax_form_handler');

// function my_ajax_form_handler()
// {

//     // if (!isset($_POST['_moz_nonce']) || !wp_verify_nonce($_POST['_moz_nonce'], 'moz_nonce_secret')) {
//     //     wp_send_json('invalid request ');
//     //     return;
//     // }

//     // Verify the nonce
//     check_ajax_referer('moz_nonce_secret', '_moz_nonce');

//     moz_debug_fn([$_POST]);

//     // Do something with the data (e.g. save to database, send email, etc.)

//     // Return a response
//     wp_send_json(['msg' => 'Form submitted successfully!']);
// }

add_action('better_messages_message_sent', 'on_message_sent', 10, 1);

function on_message_sent($message)
{

    // moz_debug_fn($message->recipients);

    foreach ($message->recipients as $item) {
        moz_debug_fn(["item", $item]);
        $author_obj = get_user_by('id', $item->user_id);

        $username = $author_obj->user_login;

        $current_url = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

        moz_debug_fn($current_url);

// Set up the data to be sent in the request body
        $data = array(
            'contents' => array(
                'en' => 'message',
                'es' => 'messaggio',
            ),
            'name' => 'INTERNAL_CAMPAIGN_NAME',
            'app_id' => 'e7cc6309-8fea-4bf6-b986-c6e874e0119b',
            'include_external_user_ids' => [$username],
            'url' => $current_url,
        );

        // Set up the cURL request
        $ch = curl_init('https://onesignal.com/api/v1/notifications');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        // Set the request headers
        $headers = array(
            'Accept: application/json',
            'Authorization: Basic MjMzZjdmNGMtYjk5My00MDc1LTljMjYtODNhYWZhODVkM2E0',
            'Content-Type: application/json',
        );
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        // Execute the request and get the response
        $response = curl_exec($ch);

        // Check for errors
        if (curl_errno($ch)) {
            // There was an error executing the cURL request
            echo 'Error: ' . curl_error($ch);
        } else {
            // The request was successful
            echo $response;
        }

        // Close the cURL request
        curl_close($ch);

        moz_debug_fn(["author", $author_obj]);

    }
}
