<?php
/*
Plugin Name: WP Youtube Pretty Embeds
Plugin URI: http://americanprogress.org
Description: Adds pretty embed (https://github.com/mike-zarandona/PrettyEmbed.js) support to the WP YouTube oEmbed class.
Version: 0.1
Author: Seth Rubenstein
Author URI: http://sethrubenstein.info
*/

function pretty_embed_load() {
    // Check for jQuery
    // Check for fitVids
    wp_register_script( 'waitForImages', plugin_dir_url(__FILE__).'bower_components/waitForImages/dist/jquery.waitforimages.min.js' );
    wp_enqueue_script( 'waitForImages' );

    wp_register_script( 'pretty-embed', plugin_dir_url(__FILE__).'bower_components/pretty-embed/jquery.prettyembed.min.js' );
    wp_enqueue_script( 'pretty-embed' );
}
add_action( 'wp_enqueue_scripts', 'pretty_embed_load' );

function pretty_embed_init() {
    $script = '<script>jQuery(document).ready(function($){ $().prettyEmbed({ useFitVids: true }); });</script>';
    echo $script;
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
