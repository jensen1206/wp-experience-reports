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

class Experience_Reports_Block_Callback {

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
     * @return false|string|void
     */
    public function callback_experience_report_block($attributes) {
        if ($attributes) {
            ob_start();
            //add_filter('render_block', array(Render_Callback_Templates::class, 'render_core_team_members_callback'), 0, 2);
            //apply_filters(HUPA_TEAMS_BASENAME.'/render_callback_template', $attributes);
            return ob_get_clean();
        }

    }
}