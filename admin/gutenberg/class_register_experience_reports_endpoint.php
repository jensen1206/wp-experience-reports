<?php

namespace Experience\Reports;

use Wp_Experience_Reports;
use stdClass;
use WP_Error;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;

/**
 * ADMIN Experience Reports Gutenberg ENDPOINT
 *
 * @link       https://wwdh.de
 * @since      1.0.0
 *
 * @package    Post_Selector
 * @subpackage Experience_Reports/admin/gutenberg/
 */
defined( 'ABSPATH' ) or die();

class Register_Experience_Reports_Endpoint {
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
	 * Register the routes for the objects of the controller.
	 */
	public function register_experience_reports_routes()
	{

		$version = '2';
		$namespace = 'experience-reports-endpoint/v' . $version;
		$base = '/';

		register_rest_route(
			$namespace,
			$base . '(?P<method>[\S^]+)/(?P<radio_check>[^/]+)',

			array(
				'methods' => WP_REST_Server::READABLE,
				'callback' => array($this, 'experience_reports_rest_endpoint_get_response'),
				'permission_callback' => array($this, 'permissions_check')
			)
		);


		register_rest_route(
			$namespace,
			$base . '(?P<method>[\S^]+)',

			array(
				'methods' => WP_REST_Server::READABLE,
				'callback' => array($this, 'experience_reports_rest_endpoint_get_response'),
				'permission_callback' => array($this, 'permissions_check')
			)
		);
	}

	/**
	 * Get one item from the collection.
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 *
	 * @return WP_Error|WP_REST_Response
	 */
	public function experience_reports_rest_endpoint_get_response(WP_REST_Request $request) {

		$method = $request->get_param( 'method' );
		$radio_check = $request->get_param('radio_check');
		if (!$method) {
			return new WP_Error(404, ' Method failed');
		}
		$response = new stdClass();

		switch ( $method ) {
			case 'get-post-slider':

				$retSlid = [];

				$types = [
					'0' => [
						'id' => 1,
						'name' => 'Card Image rechts'
					],
					'1' => [
						'id' => 2,
						'name' => 'Card Image oben'
					],
					'2' => [
						'id' => 3,
						'name' => 'Card Image unten'
					],
					'3' => [
						'id' => 4,
						'name' => 'Image overlay'
					]
				];

				$response->slider  = $retSlid;
				$response->news = $types;
				$response->radio_check = (int) $radio_check;
				$response->galerie  = [];
				break;

		}
		return new WP_REST_Response( $response, 200 );

	}

	/**
	 * Check if a given request has access.
	 *
	 * @return bool
	 */
	public function permissions_check(): bool
	{
		return current_user_can('edit_posts');
	}
}