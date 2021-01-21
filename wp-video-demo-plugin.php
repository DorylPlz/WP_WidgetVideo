<?php  
/*
/*
Plugin Name: wp-video-demo-plugin
Plugin URI: https://github.com/DorylPlz
Description: Mostrar video de demostración como widget
Version: 1.0
Author: Daryl Olivares
Author URI: https://github.com/DorylPlz
License: GPL2
*/

add_action('add_meta_boxes', 'video_demo_metabox');
add_action('save_post', 'demovid_save_metabox');
add_action('widgets_init', 'demovid_widget_init');

function video_demo_metabox(){
    add_meta_box('demovid_youtube', 'Link', 'demovid_youtube_handler', 'post');
}

function demovid_youtube_handler(){
    $values = get_post_custom($post->ID);
    $demovid_link = esc_attr($values['demovid_youtube'][0]);
    echo '<label for="demovid_youtube">Video demostrativo: </label><input type="text" id="demovid_youtube" name="demovid_youtube" value="'.$demovid_link.'"/>';
}

function demovid_save_metabox($post_id){
    if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE){
        return;
    }
    if(!current_user_can('edit_post')){
        return;
    }
    if(isset($_POST['demovid_youtube'])){
        update_post_meta($post_id, 'demovid_youtube', esc_url($_POST['demovid_youtube']));
    }
}

function demovid_widget_init() {
    register_widget('demovid_Widget');
}

class demovid_Widget extends WP_Widget {
    function demovid_Widget() {
        $widget_options = array(
            'classname' => 'widget_class', //CSS
            'description' => 'Muestra un video registrado en el metadata del post'
        );
        
        $this->WP_Widget('demovid_id', 'Video Demo', $widget_options); //demovid_id es para css
    }
    
    /**
     * show widget form in Appearence / Widgets
     */
    function form($instance) {
        $defaults = array('title' => 'Video');
        $instance = wp_parse_args( (array) $instance, $defaults);
        
        $title = esc_attr($instance['title']);
        
        echo '<p>Title <input type="text" class="widefat" name="'.$this->get_field_name('title').'" value="'.$title.'" /></p>';
    }
    
    /**
     * save widget form
     */
    function update($new_instance, $old_instance) {
        
        $instance = $old_instance;        
        $instance['title'] = strip_tags($new_instance['title']);        
        return $instance;
    }
    
    /**
     * show widget in post / page
     */
    function widget($args, $instance) {
        extract( $args );        
        $title = apply_filters('widget_title', $instance['title']);
        
        //show only if single post
        if(is_single()) {
            echo $before_widget;
            echo $before_title.$title.$after_title;
            
            //get post metadata
            $demovid_youtube = esc_url(get_post_meta(get_the_ID(), 'demovid_youtube', true));
            
            //print widget content
            echo '<iframe width="100%" height="100%" frameborder="0" allowfullscreen src="http://www.youtube.com/embed/'.get_yt_videoid($demovid_youtube).'"></iframe>';       
            
            echo $after_widget;
        }
    }
}
function get_yt_videoid($url) {
    parse_str( parse_url( $url, PHP_URL_QUERY ), $my_array_of_vars );
    return $my_array_of_vars['v']; 
}
 ?>