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

add_action('admin_menu', 'add_amazon_link_plugin_menu');
function add_amazon_link_plugin_menu(){
    add_menu_page( 'Amazon link', 'Amazon link', 'administrator', 'amazon-link', 'amazon_link', 'dashicons-amazon' );
}

function amazon_link(){
    //start buffering html
    ob_start();
    ?>
    <label for="amazon_link"><h2>Amazon link: </h2></label>
    <input type="text" id="amazon_link" name="amazon_link" title="Valid amazon url"><br><br>
    <input type="submit" id="submit" name="submit" value="SUBMIT">
    <div id="my_amazon_div" style="margin-top:25px; width: 1200px;"></div>
    <?php

    //echo and stop buffering hmtl
    echo ob_get_clean();
};


function add_amazon_script() {
    wp_enqueue_script('amazon_get_links', plugins_url('amazon_get_links.js', __FILE__), array('jquery'),false, true);
    wp_localize_script( 'amazon_get_links', 'ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
};
//use admin_enqueue_scripts to enque to the admin only
add_action( 'admin_enqueue_scripts', 'add_amazon_script' );  
add_action( 'wp_ajax_amazon_get_links', 'amazon_get_links' );

function amazon_get_links() {
    //sanitze the url link
    $sanitized_link =  sanitize_url(($_POST['amazon_link']));

    //gets the data from the sanitized link
    $amazon_data = wp_remote_retrieve_body( wp_remote_get( $sanitized_link ) ) ;
    
    wp_send_json_success($amazon_data);
    
};
?>
