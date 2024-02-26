<?php
// Enqueue parent and child theme stylesheets
function enqueue_theme_styles() {
    // Enqueue parent theme stylesheet
    wp_enqueue_style('parent-style', get_template_directory_uri() . '/style.css');

    // Enqueue child theme stylesheet
    wp_enqueue_style('child-style', get_stylesheet_uri());
}
add_action('wp_enqueue_scripts', 'enqueue_theme_styles');


function custom_post_list_shortcode($atts) {
	$cats = [];
	$categories = get_field('category');
	$tags = get_field('tag');
	$order = get_field('order');
	$author_list_ids = get_field('author');

	
	$cat_list_ids = [];

	foreach ($categories as $category) {
		$cat_list_ids[] = $category->term_id;
	}
	
	foreach ($tags as $tag) {
		$tag_list_ids[] = $tag->term_id;
	}	
		
    // Query arguments
    $args = array(
        'post_type'      => 'post',
        'posts_per_page' => -1, // Ensure the number is an integer
        'order'          => ($order == 'news-mode') ? 'DESC' : 'ASC',
		'category__in' => $cat_list_ids,
		'author__in' => $author_ids,
		'tag__in' => $tag_list_ids,
    );
	
	// Query for posts
    $query = new WP_Query($args);

    // Output post content
    $output = '<div class="custom-post-list">';

    while ($query->have_posts()) {
        $query->the_post();
		$output .= '';
		
		// Author information
       
		$author_name = get_the_author_meta('nickname');
		$user_slogan = get_the_author_meta('first_name');
        $author_avatar = get_avatar(get_the_author_meta('ID'), 64);

        // Date information
        $post_date = get_the_date();
		$post_time = get_the_time();
		$timezone =  get_option('timezone_string');
		
        // Thumbnail
        $thumbnail = get_the_post_thumbnail(get_the_ID(), 'medium_large');

        // Title
        $title = get_the_title() ? '<'.get_field( "title_heading").'>'. $author_list_ids . get_the_title() . '</'. get_field( "title_heading") .'>' : '';

        // description
        $excerpt = '<div>' . apply_filters('the_content', get_the_content()) . '</div>';
		
		$image_url = get_field('profile_image_link');
		
		$image = get_field('profile_image');
		$size = 'large';
		$imageUrl = $image ? wp_get_attachment_image( $image, $size ) : ''  ;
		
        // Output for each post
        $output .= '<div class="post-item"><div class="post-item-header"><div class="left_content"><a href="' . $image_url .'" class="img_block"><img src="' . $image . '" alt="" /></a><div><span class=name>' . get_field( "name") . '</span><span class=caption>' . get_field( "caption") . '</span></div></div><div class="right_content"><span class=name>' . str_ireplace('/','.',$post_date) . '</span><span class=caption>' . $post_time .' ' . get_field( "location") .'</span></div></div>' . $thumbnail . $title . $excerpt . '</div>';
    }

    $output .= '</div>';

    // Restore original post data
    wp_reset_postdata();

    return $output;
}
add_shortcode('post_list', 'custom_post_list_shortcode');


function thewpx_new_avatar( $avatar_defaults ) {
  $new_avatar = 'http://ypsilon.berlin/wp-content/uploads/2023/11/Profile-pic-Ypsilon.png';
  $avatar_defaults[ $new_avatar ] = 'My Avatar';
 
  return $avatar_defaults;
}
add_filter( 'avatar_defaults', 'thewpx_new_avatar' );

remove_action( 'wp_head', 'wp_site_icon', 99);

function insert_html_in_header() {
	
	global $post;
	$fav_image = get_field('page_favicon', $post->ID);
    if ($fav_image) {
		
        echo '<link rel="icon" href="' . esc_url($fav_image) . '" sizes="32x32" />';
        echo '<link rel="icon" href="' . esc_url($fav_image) . '" sizes="192x192" />';
        echo '<link rel="apple-touch-icon" href="' . esc_url($fav_image) . '" sizes="192x192" />';
    }
}

add_action('wp_head', 'insert_html_in_header', 9999);