<?php
/*  ----------------------------------------------------------------------------
    Newspaper V6.3+ Child theme - Please do not use this child theme with older versions of Newspaper Theme

    What can be overwritten via the child theme:
     - everything from /parts folder
     - all the loops (loop.php loop-single-1.php) etc
	 - please read the child theme documentation: http://forum.tagdiv.com/the-child-theme-support-tutorial/


     - the rest of the theme has to be modified via the theme api:
       http://forum.tagdiv.com/the-theme-api/

 */




/*  ----------------------------------------------------------------------------
    add the parent style + style.css from this folder
 */
add_action( 'wp_enqueue_scripts', 'theme_enqueue_styles', 1001);
function theme_enqueue_styles() {
    wp_enqueue_style('td-theme', get_template_directory_uri() . '/style.css', '', TD_THEME_VERSION, 'all' );
    wp_enqueue_style('td-theme-child', get_stylesheet_directory_uri() . '/style.css', array('td-theme'), TD_THEME_VERSION . 'c', 'all' );

}

if ( ! class_exists( 'WPAlchemy_MetaBox' ) ){
    include_once get_template_directory()  . '/includes/wp_booster/wp-admin/external/wpalchemy/MetaBox.php';
}

add_action('init', 'vm_register_post_metaboxes', 10000);

function vm_register_post_metaboxes() {
    $td_template_settings_path = get_template_directory() . '/includes/wp_booster/wp-admin/content-metaboxes/';

    /**
     * single posts, Custom Post Types and WooCommerce products all use the same metadata keys!
     * we just switch here the views
     */


    /**
     * 'post' post type / single
     */
    if (current_user_can('publish_posts')) {
        new WPAlchemy_MetaBox(array(
            'id' => 'td_post_theme_settings',
            'title' => 'Post Settings',
            'types' => array('movie'),
            'priority' => 'high',
            'template' => get_template_directory() . '/includes/wp_booster/wp-admin/content-metaboxes/td_set_post_settings.php',
        ));
    }


}

function vm_remove_slug( $post_link, $post, $leavename ) {

    if ( !in_array($post->post_type, array('movie', 'celebrity')) || 'publish' != $post->post_status ) {
        return $post_link;
    }

    $post_link = str_replace( '/' . $post->post_type . '/', '/', $post_link );

    return $post_link;
}
add_filter( 'post_type_link', 'vm_remove_slug', 10, 3 );

function vm_parse_request( $query ) {

    if ( ! $query->is_main_query() || 2 != count( $query->query ) || ! isset( $query->query['page'] ) ) {
        return;
    }

    if ( ! empty( $query->query['name'] ) ) {
        $query->set( 'post_type', array( 'post', 'page', 'movie', 'celebrity' ) );
    }
}
add_action( 'pre_get_posts', 'vm_parse_request' );

remove_filter( 'sanitize_title', 'sanitize_title_with_dashes' );
add_filter( 'sanitize_title', 'vm_sanitize_title' );

function vm_sanitize_title( $title, $raw_title = '', $context = 'display' ) {
  if (substr( $title, 0, 1 ) === "@") {
    return sanitize_title_with_underscores($title, $raw_title, $context);
  } else {
    return sanitize_title_with_dashes($title, $raw_title, $context);
  }
}

/**
 * Sanitizes a title, replacing whitespace and a few other characters with underscores.
 *
 * Limits the output to alphanumeric characters, underscore (_) and dash (-).
 * Whitespace becomes a dash.
 *
 * @since 1.2.0
 *
 * @param string $title     The title to be sanitized.
 * @param string $raw_title Optional. Not used.
 * @param string $context   Optional. The operation for which the string is sanitized.
 * @return string The sanitized title.
 */
function sanitize_title_with_underscores( $title, $raw_title = '', $context = 'display' ) {
	$title = strip_tags($title);
	// Preserve escaped octets.
	$title = preg_replace('|%([a-fA-F0-9][a-fA-F0-9])|', '---$1---', $title);
	// Remove percent signs that are not part of an octet.
	$title = str_replace('%', '', $title);
	// Restore octets.
	$title = preg_replace('|---([a-fA-F0-9][a-fA-F0-9])---|', '%$1', $title);
	if (seems_utf8($title)) {
		/*if (function_exists('mb_strtolower')) {
			$title = mb_strtolower($title, 'UTF-8');
		}*/
		$title = utf8_uri_encode($title, 200);
	}
	/// $title = strtolower($title);
	if ( 'save' == $context ) {
		// Convert nbsp, ndash and mdash to hyphens
		$title = str_replace( array( '%c2%a0', '%e2%80%93', '%e2%80%94' ), '', $title );
		// Convert nbsp, ndash and mdash HTML entities to hyphens
		$title = str_replace( array( '&nbsp;', '&#160;', '&ndash;', '&#8211;', '&mdash;', '&#8212;' ), '', $title );
		// Strip these characters entirely
		$title = str_replace( array(
			// iexcl and iquest
			'%c2%a1', '%c2%bf',
			// angle quotes
			'%c2%ab', '%c2%bb', '%e2%80%b9', '%e2%80%ba',
			// curly quotes
			'%e2%80%98', '%e2%80%99', '%e2%80%9c', '%e2%80%9d',
			'%e2%80%9a', '%e2%80%9b', '%e2%80%9e', '%e2%80%9f',
			// copy, reg, deg, hellip and trade
			'%c2%a9', '%c2%ae', '%c2%b0', '%e2%80%a6', '%e2%84%a2',
			// acute accents
			'%c2%b4', '%cb%8a', '%cc%81', '%cd%81',
			// grave accent, macron, caron
			'%cc%80', '%cc%84', '%cc%8c',
		), '', $title );
		// Convert times to x
		$title = str_replace( '%c3%97', 'x', $title );
	}
	$title = preg_replace('/&.+?;/', '', $title); // kill entities
	/// $title = str_replace('.', '-', $title);
	$title = preg_replace('/[^%a-zA-Z0-9 _-]/', '', $title);
	$title = preg_replace('/\s+/', '_', $title);
	$title = preg_replace('|-+|', '_', $title);
	$title = trim($title, '_');
	return $title;
}
