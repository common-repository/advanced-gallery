<?php
/**
 * Main Advanced_Gallery class
 *
 * @package Advanced_Gallery
 */
namespace Advanced_Gallery;

defined( 'ABSPATH' ) || exit;

/**
 * Main Advanced_Gallery Cass.
 *
 * @class Advanced_Gallery
 */
final class Init {
    /**
     * Advanced_Gallery verison.
     *
     * @var string
     */
    public $version = '1.0.2';

    /**
     * The single instance of the class.
     *
     * @var Advanced_Gallery
     * @since 1.0.0
     */
    protected static $_instance = null;

    /**
     * Main Advanced_Gallery Instance.
     *
     * Ensures only one instance of Advanced_Gallery is loaded or can be loaded.
     *
     * @since 1.0.0
     * @static
     * @return Advanced_Gallery - Main instance.
     */
    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * Advanced_Gallery/Init Constructor.
     */
    public function __construct() {

        if ( $this->meets_requirements() ) {
            
            /**
             * Define constants.
             */
            $this->_defineConstants();

            /**
             * Includes.
             */
            $this->includes();

            /**
             * Activation.
             */
            $this->activate();
    
            /**
             * Init hooks.
             */
            $this->init_hooks();

        } else {
            add_action( 'admin_notices', [ $this, 'maybe_disable_plugin' ] );
        }
    }

    /**
     * Activation hook.
     *
     * @return void
     */
    public function activate(){
    }

    /**
     * When WP has loaded all plugins, trigger the 'advanced_gallery_loaded; hook.
     *
     * This ensures 'advanced_gallery_loaded' is called only after all the other plugins
     * are loaded, to avoid issues caused by plugin directory naming changing
     * the load order.
     *
     * @since 1.0.0
     * @access public
     */
    public function onPluginLoaded(){
        do_action('advanced_gallery_loaded');
    }

    /**
     * Define Constants.
     *
     * @since 1.0.0
     * @access private
     */
    private function _defineConstants() {
        $this->define('ADVANCED_GALLERY_PLUGIN_NAME', 'advanced-gallery');
        $this->define('ADVANCED_GALLERY_ABSPATH', dirname(ADVANCED_GALLERY_PLUGIN_FILE) . '/');
        $this->define('ADVANCED_GALLERY_PLUGIN_BASENAME', plugin_basename(ADVANCED_GALLERY_PLUGIN_FILE) );
        $this->define('ADVANCED_GALLERY_VERSION', $this->version);
        $this->define('ADVANCED_GALLERY_PLUGIN_URL', $this->plugin_url());
    }

    /**
     * Define constant if not already set.
     *
     * @param string      $name       Constant name.
     * @param string|bool $value      Constant value.
     * @return void
     */
    private function define($name, $value)
    {
        if (!defined($name)) {
            define($name, $value);
        }
    }

    /**
     * Include required files.
     *
     * @return void
     */
    public function includes() {
        include_once ADVANCED_GALLERY_PLUGIN_PATH . 'includes/classes/class-widgets.php';
    }

    /**
	 * Initialize hooks.
	 *
	 * @since 1.0.0
	 */
	public function init_hooks() {
		add_action( 'elementor/frontend/after_register_scripts', array( $this, 'register_scripts' ), 9999 );
		add_action( 'elementor/frontend/after_register_styles', array( $this, 'register_styles' ), 9999 );
    }

    /**
	 * Register Styles.
	 */
	public function register_styles() {
        wp_register_style( 
            'adv-gal-widgets', 
            plugin_dir_url( ADVANCED_GALLERY_PLUGIN_FILE ) . 'includes/css/adv-gal-widgets.css', 
            array(), 
            ADVANCED_GALLERY_PLUGIN_VERSION 
        );

        wp_register_style( 
            'justifiedGallery-css', 
            plugin_dir_url( ADVANCED_GALLERY_PLUGIN_FILE ) . 'includes/css/justifiedGallery.min.css', 
            array(), 
            '3.8.0' 
        );

        wp_register_style( 
            'fancybox-css', 
            plugin_dir_url( ADVANCED_GALLERY_PLUGIN_FILE ) . 'includes/css/jquery.fancybox.min.css', 
            array(), 
            '3.5.7' 
        );
	}

    /**
	 * Register Scripts.
	 */
	public function register_scripts() {
        wp_register_script( 
            'adv-gal-widgets-js', 
            plugin_dir_url( ADVANCED_GALLERY_PLUGIN_FILE ) . 'includes/js/adv-gal-widgets.js', 
            array('jquery'),
            ADVANCED_GALLERY_PLUGIN_VERSION,
            true 
        );

        wp_register_script( 
            'justifiedGallery-js', 
            plugin_dir_url( ADVANCED_GALLERY_PLUGIN_FILE ) . 'includes/js/justifiedGallery.min.js', 
            array(), 
            '3.8.0',
            true 
        );

        wp_register_script( 
            'isotope', 
            plugin_dir_url( ADVANCED_GALLERY_PLUGIN_FILE ) . 'includes/js/isotope.pkgd.min.js', 
            array(), 
            '3.0.6',
            true 
        );

        wp_register_script( 
            'fancybox-js', 
            plugin_dir_url( ADVANCED_GALLERY_PLUGIN_FILE ) . 'includes/js/jquery.fancybox.min.js', 
            array(), 
            '3.5.7',
            true 
        );
	}

    /**
     * Init Advanced_Gallery when WordPress initializes.
     *
     * @since 1.0.0
     * @access public
     */
    public function init() {
        // Set up localization.
        $this->loadPluginTextdomain();
    }

    /**
     * Load the plugin text domain for translation.
     *
     * @since 1.0.0
     *
     * Note: the first-loaded translation file overrides any following ones -
     * - if the same translation is present.
     *
     * Locales found in:
     *      - WP_LANG_DIR/advanced-gallery/advanced-gallery-LOCALE.mo
     *      - WP_LANG_DIR/plugins/advanced-gallery-LOCALE.mo
     */
    public function loadPluginTextdomain()
    {
        if (function_exists('determine_locale')) {
            $locale = determine_locale();
        } else {
            $locale = is_admin() ? get_user_locale() : get_locale();
        }

        $locale = apply_filters( 'plugin_locale', $locale, 'advanced-gallery' );

        unload_textdomain( 'advanced-gallery' );
        load_textdomain( 'advanced-gallery', WP_LANG_DIR . '/advanced-gallery/advanced-gallery-' . $locale . '.mo' );
        load_plugin_textdomain(
            'advanced-gallery',
            false,
            dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
        );
    }

    /**
     * Get the plugin URL.
     *
     * @since 1.0.0
     * @access public
     *
     * @return string
     */
    public function plugin_url() {
        return untrailingslashit( plugins_url( '/', ADVANCED_GALLERY_PLUGIN_FILE ) );
    }

    /**
     * Get the plugin path.
     *
     * @since 1.0.0
     * @access public
     *
     * @return string
     */
    public function plugin_path() {
        return untrailingslashit( plugin_dir_path( ADVANCED_GALLERY_PLUGIN_FILE ) );
    }

    /**
     * Output error message and disable plugin if requirements are not met.
     *
     * This fires on admin_notices.
     *
     * @since 1.0.0
     */
    public function maybe_disable_plugin() {

        if ( ! $this->meets_requirements() ) {
			
            echo wp_kses_post(
                sprintf(
                    '<div class="error"><p>'
                    // translators: 1. Advanced Gallery Name 2. Link to Elementor Plugin.
                    . sprintf( __( '%1$s requires the %2$s plugin to work. Please install and activate the latest <strong>Elementor</strong> plugin first.', 'advanced-gallery' ), '<strong>Advanced Gallery - Addons for Elementor</strong>', '<a href="https://wordpress.org/plugins/elementor/" target="__blank">Elementor</a>' )
                    . '</p></div>'
                )
            );

            // Deactivate our plugin
			deactivate_plugins( ADVANCED_GALLERY_PLUGIN_FILE );

		}
    
    }

    /**
         * Admin notice for required php version
         *
         * @return void
         */
        public function required_php_version_missing_notice() {
            $notice = sprintf(
                /* translators: 1: Plugin name 2: PHP 3: Required PHP version */
                esc_html__( '"%1$s" requires "%2$s" version %3$s or greater.', 'advanced-gallery' ),
                '<strong>' . esc_html__( 'Advanced Gallery', 'advanced-gallery' ) . '</strong>',
                '<strong>' . esc_html__( 'PHP', 'advanced-gallery' ) . '</strong>',
                ADVANCED_GALLERY_MINIMUM_PHP_VERSION
            );

            printf( '<div class="notice notice-warning is-dismissible"><p style="padding: 13px 0">%1$s</p></div>', $notice );
        }

        /**
         * Admin notice for elementor if missing
         *
         * @return void
         */
        public function elementor_missing_notice() {
            $notice = wp_kses_post( sprintf(
                /* translators: 1: Plugin name 2: Elementor 3: Elementor installation link */
                __( '%1$s requires %2$s to be installed and activated to function properly. %3$s', 'advanced-gallery' ),
                '<strong>' . __( 'Advanced Gallery', 'advanced-gallery' ) . '</strong>',
                '<strong>' . __( 'Elementor', 'advanced-gallery' ) . '</strong>',
                '<a href="' . esc_url( admin_url( 'plugin-install.php?s=Elementor&tab=search&type=term' ) ) . '">' . __( 'Please click on this link and install Elementor', 'advanced-gallery' ) . '</a>'
            ) );

            printf( '<div class="notice notice-warning is-dismissible"><p style="padding: 13px 0">%1$s</p></div>', $notice );
        }

        /**
         * Admin notice for required elementor version
         *
         * @return void
         */
        public function required_elementor_version_missing_notice() {
            $notice = sprintf(
                /* translators: 1: Plugin name 2: Elementor 3: Required Elementor version */
                esc_html__( '"%1$s" requires "%2$s" version %3$s or greater.', 'advanced-gallery' ),
                '<strong>' . esc_html__( 'Advanced Gallery', 'advanced-gallery' ) . '</strong>',
                '<strong>' . esc_html__( 'Elementor', 'advanced-gallery' ) . '</strong>',
                \ADVANCED_GALLERY_ELEMENTOR_MINIMUM_ELEMENTOR_VERSION
            );

            printf( '<div class="notice notice-warning is-dismissible"><p style="padding: 13px 0">%1$s</p></div>', $notice );
        }

    /**
     * Check if all plugin requirements are met.
     *
     * @since 1.0.0
     *
     * @return bool True if requirements are met, otherwise false.
     */
    private function meets_requirements() {
        // Check if Elementor installed and activated
        // Check for required PHP version
        if ( version_compare( PHP_VERSION, ADVANCED_GALLERY_MINIMUM_PHP_VERSION, '<' ) ) {
            add_action( 'admin_notices', array( $this, 'required_php_version_missing_notice' ) );
            return false;
        }

        // Check if Elementor installed and activated
        if ( ! did_action( 'elementor/loaded' ) ) {
            add_action( 'admin_notices', array( $this, 'elementor_missing_notice' ) );
            return false;
        }

        // Check for required Elementor version
        if ( ! version_compare( ELEMENTOR_VERSION, ADVANCED_GALLERY_ELEMENTOR_MINIMUM_ELEMENTOR_VERSION, '>=' ) ) {
            add_action( 'admin_notices', array( $this, 'required_elementor_version_missing_notice' ) );
            return false;
        }

        return true;
    }

}
