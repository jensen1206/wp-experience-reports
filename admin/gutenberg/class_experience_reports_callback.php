<?php
namespace Experience\Reports;

use Wp_Experience_Reports;
use WP_Query;

/**
 * ADMIN Post-Selector Gutenberg Callback
 *
 * @since      1.0.0
 * @package    Wp_Experience_Reports
 * @subpackage Wp_Experience_Reports/includes
 * @author     Jens Wiecker <email@jenswiecker.de>
 */
defined('ABSPATH') or die();

class Experience_Reports_Callback {

	/**
	 * The plugin Slug Path.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string $plugin_dir plugin Slug Path.
	 */
	protected string $plugin_dir;

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $basename The ID of this plugin.
	 */
	private string $basename;

	/**
	 * The Version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $version The current Version of this plugin.
	 */
	private string $version;

	/**
	 * Store plugin main class to allow public access.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var Wp_Experience_Reports $main The main class.
	 */
	private Wp_Experience_Reports $main;

	/**
	 * Store plugin main class to allow public access.
	 *
	 * @param string $basename
	 * @param string $version
	 *
	 * @since    1.0.0
	 * @access   private
	 *
	 * @var Wp_Experience_Reports $main
	 */

	public function __construct( string $basename, string $version,  Wp_Experience_Reports $main ) {

		$this->basename   = $basename;
		$this->version    = $version;
		$this->main       = $main;

	}

	/**
	 * @param $attributes
	 *
	 * @return mixed|void
	 */
	public function callback_post_selector_two_block($attributes) {
		$selected_posts = $attributes['selectedPosts'] ?? false;
		$total = 0;
		$pagination = '';

			$posts = new WP_Query([
				'post__in' => $selected_posts,
				'post_type' => get_post_types(),
				//'order_by' => 'posts__in'
			]);


		wp_reset_query();
		return apply_filters('gutenberg_block_post_selector_two_render', $posts, $attributes, $pagination);

	}
}