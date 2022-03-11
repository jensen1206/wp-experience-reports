<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://wwdh.de
 * @since      1.0.0
 *
 * @package    Wp_Experience_Reports
 * @subpackage Wp_Experience_Reports/admin
 */

use Experience\Reports\Experience_Reports_Admin_Ajax;

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Wp_Experience_Reports
 * @subpackage Wp_Experience_Reports/admin
 * @author     Jens Wiecker <email@jenswiecker.de>
 */
class Wp_Experience_Reports_Admin {

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $basename    The ID of this plugin.
     */
    private string $basename;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private string $version;

    /**
     * Store plugin main class to allow public access.
     *
     * @since    1.0.0
     * @access   private
     * @var Wp_Experience_Reports $main The main class.
     */
    private  Wp_Experience_Reports $main;

    /**
     * License Config of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var  object $config License Config.
     */
    private object $config;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param      string    $plugin_name The name of this plugin.
	 * @param string $version    The version of this plugin.
	 *@since    1.0.0
	 */
    public function __construct( string $plugin_name, string $version, Wp_Experience_Reports $main, object $config) {

        $this->basename = $plugin_name;
        $this->version = $version;
        $this->main = $main;
        $this->config = $config;

	}

    public function register_experience_reports_menu() {
        $hook_suffix = add_menu_page(
            __( 'Experience Reports', 'wp-experience-reports' ),
            __( 'Experience', 'wp-experience-reports' ),
            get_option('experience_reports_user_role'),
            'wp-experience-reports-settings',
            array( $this, 'admin_experience_reports_settings_page' ),
            apply_filters($this->basename.'/er_svg_icons','cast',true,true), 30
        );

        add_action( 'load-' . $hook_suffix, array( $this, 'experience_reports_load_ajax_admin_options_script' ) );

        $hook_suffix = add_options_page(
            __( 'Experience Reports Settings', 'wp-experience-reports' ),
            __( 'Experience Reports', 'wp-experience-reports' ),
            get_option('experience_reports_user_role'),
            'wp-experience-reports-options',
            array( $this, 'experience_reports_options_page' )
        );

        add_action( 'load-' . $hook_suffix, array( $this, 'experience_reports_load_ajax_admin_options_script' ) );
    }

    /**
     * ============================================
     * =========== PLUGIN SETTINGS LINK ===========
     * ============================================
     */
    public static function experience_reports_plugin_add_action_link( $data ) {
        // check permission
        if ( ! current_user_can( get_option('experience_reports_user_role') ) ) {
            return $data;
        }
        return array_merge(
            $data,
            array(
                sprintf(
                    '<a href="%s">%s</a>',
                    add_query_arg(
                        array(
                            'page' => 'wp-experience-reports-options'
                        ),
                        admin_url( '/options-general.php' )
                    ),
                    __( "Settings", "wp-experience-reports" )
                )
            )
        );
    }


    public function admin_experience_reports_settings_page():void {

    }

    //Options Page
    public function experience_reports_options_page():void {

    }

    public function experience_reports_load_ajax_admin_options_script():void {
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
        $title_nonce = wp_create_nonce( 'experience_reports_admin_handle' );

        wp_register_script( 'experience-reports-admin-ajax-script', '', [], '', true );
        wp_enqueue_script( 'experience-reports-admin-ajax-script' );
        wp_localize_script( 'experience-reports-admin-ajax-script', 'report_ajax_obj', array(
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'nonce'    => $title_nonce,
            'data_table' => plugin_dir_url( __FILE__ ) . 'json/DataTablesGerman.json',
            'rest_url' => get_rest_url()
        ));
    }

    /**
     * Register Experience Reports AJAX ADMIN RESPONSE HANDLE
     *
     * @since    1.0.0
     */
    public function prefix_ajax_EReportHandle(): void {
        check_ajax_referer( 'experience_reports_admin_handle' );
        require_once 'ajax/class_experience_reports_admin_ajax.php';
        $adminAjaxHandle = new Experience_Reports_Admin_Ajax($this->basename, $this->version, $this->main);
        wp_send_json($adminAjaxHandle->experience_reports_admin_ajax_handle());
    }

    /**
     * Register the Update-Checker for the Plugin.
     *
     * @since    1.0.0
     */
    public function set_experience_reports_update_checker() {

        if(get_option("{$this->basename}_server_api") && get_option($this->basename.'_server_api')->update->update_aktiv) {
            $postSelectorUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
                get_option("{$this->basename}_server_api")->update->update_url_git,
                WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . $this->basename . DIRECTORY_SEPARATOR . $this->basename . '.php',
                $this->basename
            );

            if (get_option("{$this->basename}_server_api")->update->update_type == '1') {
                if (get_option("{$this->basename}_server_api")->update->update_branch == 'release') {
                    $postSelectorUpdateChecker->getVcsApi()->enableReleaseAssets();
                } else {
                    $postSelectorUpdateChecker->setBranch(get_option("{$this->basename}_server_api")->update->branch_name);
                }
            }
        }
    }

    public function experience_reports_show_upgrade_notification( $current_plugin_metadata, $new_plugin_metadata ) {

        /**
         * Check "upgrade_notice" in readme.txt.
         *
         * Eg.:
         * == Upgrade Notice ==
         * = 20180624 = <- new version
         * Notice		<- message
         *
         */
        if ( isset( $new_plugin_metadata->upgrade_notice ) && strlen( trim( $new_plugin_metadata->upgrade_notice ) ) > 0 ) {

            // Display "upgrade_notice".
            echo sprintf( '<span style="background-color:#d54e21;padding:10px;color:#f9f9f9;margin-top:10px;display:block;"><strong>%1$s: </strong>%2$s</span>', esc_attr( 'Important Upgrade Notice', 'post-selector' ), esc_html( rtrim( $new_plugin_metadata->upgrade_notice ) ) );

        }
    }

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Wp_Experience_Reports_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wp_Experience_Reports_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->basename, plugin_dir_url( __FILE__ ) . 'css/wp-experience-reports-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Wp_Experience_Reports_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wp_Experience_Reports_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->basename, plugin_dir_url( __FILE__ ) . 'js/wp-experience-reports-admin.js', array( 'jquery' ), $this->version, false );

	}

}
