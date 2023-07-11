<?php
/*
Plugin Name: Post View Counter
Description: track and display post view by ESI block
*/


defined('WPINC') || exit;

function check_lscwp_esi_status() {
    if (!defined('LSCWP_V') || ! apply_filters( 'litespeed_esi_status', false )  ) {
    return;
    }
    else {
        do_action( 'litespeed_debug2', 'LSCWP and ESI are enabled' );
        add_action('wp_head', 'lscwp_esi_counting');
        add_action( 'litespeed_esi_load-pv_couting_esi_block', 'pv_couting_esi_block_esi_load' );
        add_action( 'litespeed_esi_load-pv_display_esi_block', 'pv_display_esi_block_esi_load' );
        add_action('wp_head', 'lscwp_esi_display_count'); 
    }
}
add_action('init', 'check_lscwp_esi_status', 999);


function lscwp_esi_counting() {
echo apply_filters( 'litespeed_esi_url', 'pv_couting_esi_block', 'Post View Counting ESI block' );
}


function pv_couting_esi_block_esi_load(){
do_action( 'litespeed_control_set_nocache' );
    if ( ! is_single()) {
        return;
    }
    #$post_id = url_to_postid( $_SERVER[ 'ESI_REFERER' ] );
    do_action( 'litespeed_debug2', '[Post View] - post view count for URL ' . $_SERVER[ 'ESI_REFERER' ] );
    global $post;
    $post_id = $post->ID;
    do_action( 'litespeed_debug2', '[Post View] - extracted post ID: ' . $post_id );
    $count = get_post_meta($post_id, 'post_views_count', true);
    if ($count === '') {
        $count = 1;
        add_post_meta($post_id, 'post_views_count', $count, true);
        do_action( 'litespeed_debug2', '[Post View] - post view count for post ID ' . $post_id . 'was not set, initialize it to 1');
    } else {
        $count++;
        update_post_meta($post_id, 'post_views_count', $count);
        do_action( 'litespeed_debug2', '[Post View] - extracted post view count for post ID ' . $post_id . ': ' . $count . ' , increasing +1');
    }
    
}

function pv_display_esi_block_esi_load(){
    do_action( 'litespeed_control_set_nocache' );
    do_action( 'litespeed_debug2', '[Post View] - post view count for URL ' . $_SERVER[ 'ESI_REFERER' ] );
    global $post;
    $post_id = $post->ID;
    do_action( 'litespeed_debug2', '[Post View] - extracted post ID: ' . $post_id );
    $count = get_post_meta($post_id, 'post_views_count', true);
    if ($count === '') {
        $count = 0;
        add_post_meta($post_id, 'post_views_count', $count, true);
    }
    echo 'Views: ' . $count;
    do_action( 'litespeed_debug2', '[Post View] - extracted post view count for post ID ' . $post_id . ': ' . $count);
}

function lscwp_esi_display_count() {
echo apply_filters( 'litespeed_esi_url', 'pv_display_esi_block', 'Post View Display ESI block' );
}

