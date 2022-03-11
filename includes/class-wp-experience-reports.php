<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://wwdh.de
 * @since      1.0.0
 *
 * @package    Wp_Experience_Reports
 * @subpackage Wp_Experience_Reports/includes
 */

use Experience\Reports\Experience_Reports_Callback;
use Experience\Reports\Register_Experience_Reports_Endpoint;
use Experience\Reports\WP_Experience_Reports_Helper;
use Hupa\License\Register_Product_License;

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Wp_Experience_Reports
 * @subpackage Wp_Experience_Reports/includes
 * @author     Jens Wiecker <email@jenswiecker.de>
 */
class Wp_Experience_Reports {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Wp_Experience_Reports_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected Wp_Experience_Reports_Loader $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected string $plugin_name;

    /**
     * The current version of the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string $version The current version of the plugin.
     */
    protected string $version = '';

    /**
     * The current database version of the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string $db_version The current database version of the plugin.
     */
    protected string $db_version;

    /**
     * Store plugin main class to allow public access.
     *
     * @since    1.0.0
     * @var object The main class.
     */
    public object $main;

    /**
     * The plugin Slug Path.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $plugin_slug plugin Slug Path.
     */
    private string $plugin_slug;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

        $this->plugin_name = WP_EXPERIENCE_REPORTS_BASENAME;
        $this->plugin_slug = WP_EXPERIENCE_REPORTS_SLUG_PATH;
        $this->main        = $this;

        $plugin = get_file_data(plugin_dir_path( dirname( __FILE__ ) ) . $this->plugin_name . '.php', array('Version' => 'Version'), false);
        if(!$this->version){
            $this->version = $plugin['Version'];
        }

        if ( defined( 'WP_EXPERIENCE_REPORTS_DB_VERSION' ) ) {
            $this->db_version = WP_EXPERIENCE_REPORTS_DB_VERSION;
        } else {
            $this->db_version = '1.0.0';
        }

		$this->plugin_name = 'wp-experience-reports';

        $this->check_dependencies();
		$this->load_dependencies();
		$this->set_locale();
        $this->define_product_license_class();
        $this->register_helper_class();
        $this->register_experience_reports_endpoint();
        $this->register_experience_reports_render_callback();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Wp_Experience_Reports_Loader. Orchestrates the hooks of the plugin.
	 * - Wp_Experience_Reports_i18n. Defines internationalization functionality.
	 * - Wp_Experience_Reports_Admin. Defines all hooks for the admin area.
	 * - Wp_Experience_Reports_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wp-experience-reports-loader.php';

        /**
         * The class responsible for defining WP REST API Routes
         * side of the site.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/gutenberg/class_register_experience_reports_endpoint.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wp-experience-reports-i18n.php';

        /**
         * The trait for the default settings
         * of the plugin.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/trait_wp_experience_reports_defaults.php';

        /**
         * The class Helper
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class_wp_experience_reports_helper.php';

        /**
         * Update-Checker-Autoload
         * Git Update for Theme|Plugin.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/update-checker/autoload.php';

        /**
         * // JOB The class responsible for defining all actions that occur in the license area.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/license/class_register_product_license.php';


		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
        if ( is_file( plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-wp-experience-reports-admin.php' ) ) {
            require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/gutenberg/class_experience_reports_callback.php';
            require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-wp-experience-reports-admin.php';
        }

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-wp-experience-reports-public.php';

		$this->loader = new Wp_Experience_Reports_Loader();

	}


    /**
     * Check PHP and WordPress Version
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function check_dependencies(): void {
        global $wp_version;
        if ( version_compare( PHP_VERSION, WP_EXPERIENCE_REPORTS_PHP_VERSION, '<' ) || $wp_version < WP_EXPERIENCE_REPORTS_WP_VERSION ) {
            $this->maybe_self_deactivate();
        }
    }

    /**
     * Self-Deactivate
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function maybe_self_deactivate(): void {
        require_once ABSPATH . 'wp-admin/includes/plugin.php';
        deactivate_plugins( $this->plugin_slug );
        add_action( 'admin_notices', array( $this, 'self_deactivate_notice' ) );
    }

    /**
     * Self-Deactivate Admin Notiz
     * of the plugin.
     *
     * @since    1.0.0
     * @access   public
     */
    public function self_deactivate_notice(): void {
        echo sprintf( '<div class="notice notice-error is-dismissible" style="margin-top:5rem"><p>' . __( 'This plugin has been disabled because it requires a PHP version greater than %s and a WordPress version greater than %s. Your PHP version can be updated by your hosting provider.', 'wp-experience-reports' ) . '</p></div>', WP_EXPERIENCE_REPORTS_PHP_VERSION, WP_EXPERIENCE_REPORTS_WP_VERSION );
        exit();
    }

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Wp_Experience_Reports_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Wp_Experience_Reports_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

    /**
     * Register all the hooks related to the admin area functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_product_license_class() {

        if(!get_option('hupa_server_url')){
            update_option('hupa_server_url', $this->get_license_config()->api_server_url);
        }
        global $product_license;
        $product_license = new Register_Product_License( $this->get_plugin_name(), $this->get_version(), $this->get_license_config(), $this->main );
        $this->loader->add_action( 'init', $product_license, 'license_site_trigger_check' );
        $this->loader->add_action( 'template_redirect', $product_license, 'license_callback_site_trigger_check' );
    }

    /**
     * Register all the hooks related to the admin area functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function register_helper_class() {
        global $plugin_helper;
        $plugin_helper = new WP_Experience_Reports_Helper( $this->get_plugin_name(), $this->get_version(), $this->main );
        $this->loader->add_action( $this->plugin_name.'/get_random_string', $plugin_helper, 'getERRandomString' );
        $this->loader->add_action( $this->plugin_name.'/generate_random_id', $plugin_helper, 'getERGenerateRandomId' );
        $this->loader->add_action( $this->plugin_name.'/array_to_object', $plugin_helper, 'ERArrayToObject' );
        $this->loader->add_action( $this->plugin_name.'/er_svg_icons', $plugin_helper, 'er_svg_icons',10,3 );
    }

	/**
	 * Register all the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

        if(!get_option('experience_reports_user_role')){
            update_option('experience_reports_user_role', 'manage_options');
        }

        if ( is_file( plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-wp-experience-reports-admin.php' ) && get_option( "{$this->plugin_name}_product_install_authorize" ) ) {
            $plugin_admin = new Wp_Experience_Reports_Admin($this->get_plugin_name(), $this->get_version(), $this->main, $this->get_license_config());

            $this->loader->add_action('init', $plugin_admin, 'set_experience_reports_update_checker');
            $this->loader->add_action('in_plugin_update_message-' . $this->plugin_name . '/' . $this->plugin_name . '.php', $plugin_admin, 'experience_reports_show_upgrade_notification', 10, 2);

            //JOB WARNING ADD Plugin Settings Link
            $this->loader->add_filter('plugin_action_links_' . $this->plugin_name . '/' . $this->plugin_name . '.php', $plugin_admin, 'experience_reports_plugin_add_action_link');

            //Admin Menu | AJAX
            $this->loader->add_action('admin_menu', $plugin_admin, 'register_experience_reports_menu');
            $this->loader->add_action('wp_ajax_EReportHandle', $plugin_admin, 'prefix_ajax_EReportHandle');
        }
	}

    /**
     * Register all the hooks related to the Gutenberg Plugins functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function register_experience_reports_render_callback() {
        global $post_selector_callback;
        if ( is_file( plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-wp-experience-reports-admin.php' ) && get_option( "{$this->plugin_name}_product_install_authorize" ) ) {
            $post_selector_callback = new Experience_Reports_Callback( $this->get_plugin_name(), $this->get_version(), $this->main );
        }
    }

    /**
     * Register all the hooks related to the Gutenberg Plugins functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function register_experience_reports_endpoint() {
        global $register_experience_endpoint;
        $register_experience_endpoint = new Register_Experience_Reports_Endpoint( $this->get_plugin_name(), $this->get_version(), $this->main );
        $this->loader->add_action('rest_api_init', $register_experience_endpoint, 'register_experience_reports_routes');
    }

	/**
	 * Register all the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Wp_Experience_Reports_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

	}

	/**
	 * Run the loader to execute all the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name(): string
    {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Wp_Experience_Reports_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader(): Wp_Experience_Reports_Loader
    {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version(): string
    {
		return $this->version;
	}

    /**
     * License Config for the plugin.
     *
     * @return    object License Config.
     * @since     1.0.0
     */
    public function get_license_config():object {
        $config_file = plugin_dir_path( dirname( __FILE__ ) ) . 'includes/license/config.json';

        return json_decode(file_get_contents($config_file));
    }

}
