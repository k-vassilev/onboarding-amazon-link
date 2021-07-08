<?php
/**
 * Plugin Name: Amazon link
 * Description: Displays amazon link results
 * Author: Kristian Vassilev
 * Version: 1.0.0
 */

if(!defined('ABSPATH')) {
    exit;
}

//Adds the plugin menu to admin menu
add_action('admin_menu', 'add_amazon_link_plugin_menu');
function add_amazon_link_plugin_menu(){
    add_menu_page( 'Amazon link', 'Amazon link', 'administrator', 'amazon-link', 'amazon_link', 'dashicons-amazon' );
}

//User input and visualization form
function amazon_link(){
    //start buffering html
    ob_start();
    ?>
    <label for="amazon_link"><h2>Amazon link: </h2></label>
    <input type="text" id="amazon_link" name="amazon_link" title="Valid amazon url"><br><br>

    <label for="cache_duration">Cache duration:</label>
    <select name="cache_duration" id="cache_duration">
        <option value="60">1 min</option>
        <option value="1800">30 min</option>
        <option value="3600">1 hour</option>
        <option value="86400">1 day</option>
    </select>

    <input type="submit" id="submit" name="submit" value="SUBMIT">
    <div id="my_amazon_div" style="display: flex;"><?php echo get_transient( 'amazon_cached_data' ); ?></div>
    <?php

    //echo and stop buffering hmtl
    echo ob_get_clean();
};

//Add the amazon_get_link.js script
function add_amazon_script() {
    wp_enqueue_script('amazon_get_links', plugins_url('amazon_get_links.js', __FILE__), array('jquery'),false, true);
    wp_localize_script( 'amazon_get_links', 'ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
};

//use admin_enqueue_scripts to enque to the admin only
add_action( 'admin_enqueue_scripts', 'add_amazon_script' );  
add_action( 'wp_ajax_amazon_get_links', 'amazon_get_links' );

//Get the ajax request from the user input to the js file and sets the transient data
function amazon_get_links() {
    //sanitze the url link
    $sanitized_link =  sanitize_url(($_POST['amazon_link']));

    //cache duration logic
    $sanitized_cache_duration = sanitize_text_field(($_POST['cache_duration_option']));
        if ($sanitized_cache_duration =='60') {
            $cache_duration = 60;
        }else if($sanitized_cache_duration =='1800') {
            $cache_duration = 1800;
        }else if ($sanitized_cache_duration =='3600') {
            $cache_duration = 3600;
        }else if($sanitized_cache_duration =='86400') {
            $cache_duration = 86400;
        }

    
    //gets the data from the sanitized link
    $amazon_data = wp_remote_retrieve_body( wp_remote_get( $sanitized_link ) );
    
    //sets the cache
    $cached_data = set_transient( 'amazon_cached_data', $amazon_data, $cache_duration );
    
    //sends the data back to js to append it (the html)
    wp_send_json_success($amazon_data);
}



?>