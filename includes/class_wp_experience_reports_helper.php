<?php
namespace Experience\Reports;
use Exception;
use Wp_Experience_Reports;

/**
 * Default Plugin Settings
 *
 * @link       https://wwdh.de
 * @since      1.0.0
 *
 * @package    Wp_Experience_Reports
 * @subpackage Wp_Experience_Reports/includes
 */
defined('ABSPATH') or die();
/**
 * @since      1.0.0
 * @package    Wp_Experience_Reports
 * @subpackage Wp_Experience_Reports/includes
 * @author     Jens Wiecker <email@jenswiecker.de>
 */

class WP_Experience_Reports_Helper
{
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
     * TRAIT of Default Settings.
     *
     * @since    1.0.0
     */
    use Trait_Extension_Defaults;

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
     * The Default Settings.
     *
     * @since    1.0.0
     * @access   private
     * @var      array|object $default The current version of the database Version.
     */
    private $default;

    /**
     * Store plugin helper class.
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
        $this->default = $this->get_theme_default_settings('');

    }

    /**
     * @throws Exception
     */
    public function getERRandomString(): string
    {
        if (function_exists('random_bytes')) {
            $bytes = random_bytes(16);
            $str = bin2hex($bytes);
        } elseif (function_exists('openssl_random_pseudo_bytes')) {
            $bytes = openssl_random_pseudo_bytes(16);
            $str = bin2hex($bytes);
        } else {
            $str = md5(uniqid('post_selector_rand', true));
        }

        return $str;
    }

    /**
     * @param int $passwordlength
     * @param int $numNonAlpha
     * @param int $numNumberChars
     * @param bool $useCapitalLetter
     * @return string
     */
    public function getERGenerateRandomId(int $passwordlength = 12, int $numNonAlpha = 1, int $numNumberChars = 4, bool $useCapitalLetter = true): string
    {
        $numberChars = '123456789';
        //$specialChars = '!$&?*-:.,+@_';
        $specialChars = '!$%&=?*-;.,+~@_';
        $secureChars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghjkmnpqrstuvwxyz';
        $stack = $secureChars;
        if ($useCapitalLetter == true) {
            $stack .= strtoupper($secureChars);
        }
        $count = $passwordlength - $numNonAlpha - $numNumberChars;
        $temp = str_shuffle($stack);
        $stack = substr($temp, 0, $count);
        if ($numNonAlpha > 0) {
            $temp = str_shuffle($specialChars);
            $stack .= substr($temp, 0, $numNonAlpha);
        }
        if ($numNumberChars > 0) {
            $temp = str_shuffle($numberChars);
            $stack .= substr($temp, 0, $numNumberChars);
        }

        return str_shuffle($stack);
    }

    /**
     * @param $name
     * @param bool $base64
     * @param bool $data
     *
     * @return string
     */
    public function er_svg_icons($name, bool $base64 = true, bool $data = true): string {
        $icon = '';
        switch ($name){
            case 'layer':
                $icon = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="black" class="ps2-icon" viewBox="0 0 16 16">
  						  <path d="M8.235 1.559a.5.5 0 0 0-.47 0l-7.5 4a.5.5 0 0 0 0 .882L3.188 8 .264 9.559a.5.5 0 0 0 0 .882l7.5 4a.5.5 0 0 0 .47 0l7.5-4a.5.5 0 0 0 0-.882L12.813 8l2.922-1.559a.5.5 0 0 0 0-.882l-7.5-4zM8 9.433 1.562 6 8 2.567 14.438 6 8 9.433z"/>
						 </svg>';
                break;
            case'cast':
                $icon = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="black" class="er-cast" viewBox="0 0 16 16">
                         <path d="m7.646 9.354-3.792 3.792a.5.5 0 0 0 .353.854h7.586a.5.5 0 0 0 .354-.854L8.354 9.354a.5.5 0 0 0-.708 0z"/>
                         <path d="M11.414 11H14.5a.5.5 0 0 0 .5-.5v-7a.5.5 0 0 0-.5-.5h-13a.5.5 0 0 0-.5.5v7a.5.5 0 0 0 .5.5h3.086l-1 1H1.5A1.5 1.5 0 0 1 0 10.5v-7A1.5 1.5 0 0 1 1.5 2h13A1.5 1.5 0 0 1 16 3.5v7a1.5 1.5 0 0 1-1.5 1.5h-2.086l-1-1z"/>
                         </svg>';
                break;
            case'square':
                $icon = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="black" class="er-chat-square-text" viewBox="0 0 16 16">
                         <path d="M14 1a1 1 0 0 1 1 1v8a1 1 0 0 1-1 1h-2.5a2 2 0 0 0-1.6.8L8 14.333 6.1 11.8a2 2 0 0 0-1.6-.8H2a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h12zM2 0a2 2 0 0 0-2 2v8a2 2 0 0 0 2 2h2.5a1 1 0 0 1 .8.4l1.9 2.533a1 1 0 0 0 1.6 0l1.9-2.533a1 1 0 0 1 .8-.4H14a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H2z"/>
                         <path d="M3 3.5a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5zM3 6a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9A.5.5 0 0 1 3 6zm0 2.5a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5z"/>
                          </svg>';
                break;
            case 'cast2':
                $icon = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="black" class="er-cast" viewBox="0 0 16 16">
                          <path d="m7.646 9.354-3.792 3.792a.5.5 0 0 0 .353.854h7.586a.5.5 0 0 0 .354-.854L8.354 9.354a.5.5 0 0 0-.708 0z"/>
                          <path d="M11.414 11H14.5a.5.5 0 0 0 .5-.5v-7a.5.5 0 0 0-.5-.5h-13a.5.5 0 0 0-.5.5v7a.5.5 0 0 0 .5.5h3.086l-1 1H1.5A1.5 1.5 0 0 1 0 10.5v-7A1.5 1.5 0 0 1 1.5 2h13A1.5 1.5 0 0 1 16 3.5v7a1.5 1.5 0 0 1-1.5 1.5h-2.086l-1-1z"/>
                          </svg>';
                break;
        }
        if($base64){
            if ($data){
                return 'data:image/svg+xml;base64,'. base64_encode($icon);
            }
            return base64_encode($icon);
        }
        return $icon;
    }

    /**
     * @param $array
     * @return object
     */
    public function ERArrayToObject($array):object {
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $array[$key] = self::ERArrayToObject($value);
            }
        }

        return (object)$array;
    }

    /**
     * Load the plugin Wp_Experience_Reports Default Options.
     *
     * @since    1.0.0
     */
    public function experience_reports_set_default_options()
    {
        // JOB API Options
        $apiDef = $this->ERArrayToObject($this->default['api_settings']);
        $apiOptions = get_option($this->basename . '-api-options');
        $apiDefaults = [
            'api_url' => $apiDef->api_url,
            'public_api_token_url' => $apiDef->public_api_token_url,
            'public_api_support_url' => $apiDef->public_api_support_url,
            'public_api_resource_url' => $apiDef->public_api_resource_url,
            'public_api_preview_url' => $apiDef->public_api_preview_url,
            //Extension
            'extension_api_activate_url' => $apiDef->extension_api_activate_url,
            // Token URL
            'extension_api_id_rsa_token' => $apiDef->extension_api_id_rsa_token,
            //Resource
            'extension_api_resource_url' => $apiDef->extension_api_resource_url,
            //Download
            'extension_api_extension_download' => $apiDef->extension_api_extension_download
        ];
        $apiOptions = wp_parse_args($apiOptions, $apiDefaults);
        update_option($this->basename . '-api-options', $apiOptions);

        if(get_option($this->basename.'/wwdh_extension_check')){
            $ref = current_time('timestamp') - get_option($this->basename.'/wwdh_extension_check');
            if($ref >= WP_EXPERIENCE_REPORTS_UPDATE_EXTENSION_TIME){
                do_action('check_extension_preview_updates');
                update_option($this->basename.'/wwdh_extension_check', current_time('timestamp'));
            }
        }
    }

    /**
     * @param float $bytes
     * @return string
     * @access final public
     */
    final public function ExperienceReportsFileSizeConvert(float $bytes): string
    {
        $result = '';
        $arBytes = array(
            0 => array("UNIT" => "TB", "VALUE" => pow(1024, 4)),
            1 => array("UNIT" => "GB", "VALUE" => pow(1024, 3)),
            2 => array("UNIT" => "MB", "VALUE" => pow(1024, 2)),
            3 => array("UNIT" => "KB", "VALUE" => 1024),
            4 => array("UNIT" => "B", "VALUE" => 1),
        );

        foreach ($arBytes as $arItem) {
            if ($bytes >= $arItem["VALUE"]) {
                $result = $bytes / $arItem["VALUE"];
                $result = str_replace(".", ",", strval(round($result, 2))) . " " . $arItem["UNIT"];
                break;
            }
        }
        return $result;
    }
    /**
     * @param $dir
     * @return bool
     */
    public function wwdhDestroyDir($dir): bool
    {
        if (!is_dir($dir) || is_link($dir))
            return unlink($dir);

        foreach (scandir($dir) as $file) {
            if ($file == "." || $file == "..")
                continue;
            if (!$this->wwdhDestroyDir($dir . "/" . $file)) {
                chmod($dir . "/" . $file, 0777);
                if (!$this->wwdhDestroyDir($dir . "/" . $file)) return false;
            }
        }
        return rmdir($dir);
    }

    function experience_reports_user_roles_select():array {
        return [
            'read'           => esc_html__( 'Subscriber', 'wp-experience-reports' ),
            'edit_posts'     => esc_html__( 'Contributor', 'wp-experience-reports' ),
            'publish_posts'  => esc_html__( 'Author', 'wp-experience-reports' ),
            'publish_pages'  => esc_html__( 'Editor', 'wp-experience-reports' ),
            'manage_options' => esc_html__( 'Administrator', 'wp-experience-reports' )
        ];
    }
}