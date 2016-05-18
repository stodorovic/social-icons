<?php
/**
 * Social Icons Admin TinyMCE Class.
 *
 * @class    SI_Admin_TinyMCE
 * @version  1.0.0
 * @package  Social_Icons/Admin
 * @category Admin
 * @author   ThemeGrill
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * SI_Admin_TinyMCE Class
 */
class SI_Admin_TinyMCE {

	/**
	 * Hook in tabs.
	 */
	public function __construct() {
		add_action( 'admin_init', array( $this, 'add_shortcode_button' ) );
		add_filter( 'tiny_mce_version', array( $this, 'refresh_tiny_mce' ) );
		add_filter( 'mce_external_languages', array( $this, 'add_tinymce_locales' ), 20, 1 );
	}

	/**
	 * Returns whether or not shortcode is enabled.
	 * @return bool
	 */
	private function is_shortcode_enabled() {
		global $post, $post_type;

		return apply_filters( 'social_icons_is_shortcodes_enabled', is_a( $post, 'WP_Post' ) && 'social_icon' !== $post_type );
	}

	/**
	 * Add a button for shortcodes to the WP editor.
	 */
	public function add_shortcode_button() {
		if ( ! current_user_can( 'edit_posts' ) && ! current_user_can( 'edit_pages' ) ) {
			return;
		}

		if ( 'true' == get_user_option( 'rich_editing' ) ) {
			add_filter( 'mce_buttons', array( $this, 'register_shortcode_button' ) );
			add_filter( 'mce_external_plugins', array( $this, 'add_shortcode_tinymce_plugin' ) );
		}
	}

	/**
	 * Register the shortcode button.
	 * @param  array $buttons
	 * @return array $buttons
	 */
	public function register_shortcode_button( $buttons ) {
		if ( $this->is_shortcode_enabled() ) {
			array_push( $buttons, '|', 'social_icons_shortcodes' );
		}

		return $buttons;
	}

	/**
	 * Add the shortcode button to TinyMCE.
	 * @param  array $plugins TinyMCE plugins.
	 * @return array $plugins Social Icons TinyMCE plugin.
	 */
	public function add_shortcode_tinymce_plugin( $plugins ) {
		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		if ( $this->is_shortcode_enabled() ) {
			$plugins['social_icons_shortcodes'] = SI()->plugin_url() . '/assets/js/admin/editor' . $suffix . '.js';
		}

		return $plugins;
	}

	/**
	 *
	 * Force TinyMCE to refresh.
	 * @param  int $version
	 * @return int
	 */
	public function refresh_tiny_mce( $version ) {
		$version += 3;

		return $version;
	}

	/**
	 * TinyMCE locales function.
	 * @param  array $locales TinyMCE locales.
	 * @return array
	 */
	public function add_tinymce_locales( $locales ) {
		if ( $this->is_shortcode_enabled() ) {
			$locales['social_icons_shortcodes'] = SI()->plugin_path() . '/includes/admin/si-shortcodes-editor-i18n.php';
		}

		return $locales;
	}
}

new SI_Admin_TinyMCE();
