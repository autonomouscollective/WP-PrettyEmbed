<?php
/*
Plugin Name: WP Pretty Embeds (YouTube)
Plugin URI: https://github.com/sethrubenstein/WP-PrettyEmbed/
Description: Adds pretty embed (https://github.com/mike-zarandona/PrettyEmbed.js) support to the WP YouTube oEmbed class.
Version: 1.0
Author: Seth Rubenstein
Author URI: http://sethrubenstein.info
*/

function pretty_embed_load() {
    // Check for jQuery
    if ( !wp_script_is( 'jquery', 'enqueued' ) ) {
        wp_enqueue_script( 'jquery' );
    }

    // Check for fitVids
    if ( !wp_script_is( 'fitvids', 'enqueued' ) ) {
        wp_register_script( 'fitvids', plugin_dir_url(__FILE__).'bower_components/fitvids/jquery.fitvids.js');
        wp_enqueue_script( 'fitvids', array('jquery') );
    }

    wp_register_script( 'waitForImages', plugin_dir_url(__FILE__).'bower_components/waitForImages/dist/jquery.waitforimages.min.js' );
    wp_enqueue_script( 'waitForImages', array('jquery') );

    wp_register_script( 'pretty-embed', plugin_dir_url(__FILE__).'bower_components/pretty-embed/jquery.prettyembed.min.js' );
    wp_enqueue_script( 'pretty-embed', array('jquery', 'waitForImages', 'fitvids') );
}
add_action( 'wp_enqueue_scripts', 'pretty_embed_load' );

function pretty_embed_init() {
    if(is_singular()) {
        $script = '<script>jQuery(document).ready(function(){ jQuery().prettyEmbed({ useFitVids: true }); });</script>';
        echo $script;
    }
}
add_action('wp_footer', 'pretty_embed_init');

function remove_youtube_controls($code){
    if (!is_admin()) {
        if(strpos($code, 'youtu.be') !== false || strpos($code, 'youtube.com') !== false){
            if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $code, $match)) {
                $video_id = $match[1];
                $code = '<div class="pretty-embed" data-pe-videoid="'.$video_id.'" data-pe-previewsize="hd" data-pe-fitvids="true"></div>';
            }
        }
    }
    return $code;
}
add_filter('embed_handler_html', 'remove_youtube_controls');
add_filter('embed_oembed_html', 'remove_youtube_controls');
add_filter('oembed_result', 'remove_youtube_controls', 10, 3);
