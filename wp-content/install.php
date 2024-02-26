
<?php 

// Add action to run after install
add_action( 'wp_install', 'gh_post_install');

/**
 * Set comment default to disabled, to be run after install.
 * @param $user User User object, not used but provided by hook
 */
function gh_post_install($user = null) {
  update_option( 'default_comment_status', 'closed'); 
}