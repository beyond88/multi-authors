<?php
namespace MultiAuthors;

/**
 * Support language
 * 
 * @since    1.0.0
 */
class MultiAuthorsi18n {

	/**
	 * Call language method 
	 *
	 * @since	1.0.0
	 * @access	public
	 * @param	none
	 * @return	void
	 */
	public function __construct() {
		add_action('plugins_loaded', array($this, 'load_plugin_textdomain'));
	}

	/**
	 * Load language file from directory
	 *
	 * @since	1.0.0
	 * @access	public
	 * @param	none
	 * @return	void
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'multi-authors',
			false,
			dirname(dirname(MULTI_AUTHORS_BASENAME)) . '/languages/'
		);
	}
}
