<?php
namespace Advanced_Gallery;

use \Elementor\Plugin;
use \Advanced_Gallery\Widget;
use \Advanced_Gallery;
use \Elementor\Controls_Manager;
use \Elementor\Repeater;

/**
 * Widgets.
 */
defined( 'ABSPATH' ) || exit;

/**
 * Class Widgets.
 *
 * @since 1.0.0
 */
class Widgets_Controller {

    protected static $instance = null;

	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

    /**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'elementor/widgets/register', array( $this, 'register_elementor_widgets' ), 99 );
		add_action( 'elementor/elements/categories_registered', array( $this, 'add_elementor_categories' ) );
		add_action( 'elementor/common/after_register_scripts', array( $this, 'load_adv_gal_icons' ), 99999 );
		add_action( 'init', array( $this, 'set_core_widgets' ), 20 );
	}

	public function load_adv_gal_icons() {
		wp_enqueue_style( 'adv-icons', plugin_dir_url( ADVANCED_GALLERY_PLUGIN_FILE ) . 'includes/css/adv-gal-icons.css' );
	}

    public function add_elementor_categories( $elements_manager ) {
		$elements_manager->add_category(
			'advgallery',
			array(
				'title' => __( 'Advanced Gallery', 'advanced-gallery' )
			)
		);
	}

    /**
	 * Settings for widgets
	 */
	public function set_core_widgets() {

		$repeater = new Repeater();

		$repeater->add_control(
			'filter',
			[
				'label' => __( 'Filter Name', 'advanced-gallery' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __( 'Type gallery filter name', 'advanced-gallery' ),
				'default' => __( 'Filter Name', 'advanced-gallery' ),
			]
		);

		$repeater->add_control(
			'is_default_filter',
			[
				'label' => __( 'Make this default filter?', 'advanced-gallery' ),
				'type' => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'description' => __( 'Set this as default active filter. Make sure filter menu is active and visible. Last active will get priority.', 'advanced-gallery' ),
				'style_transfer' => true,
			]
		);

		$repeater->add_control(
			'images',
			[
				'type' => Controls_Manager::GALLERY,
				'dynamic' => [
					'active' => true,
				]
			]
		);

        // General Terms Controls.
        $general_terms_controls = array(
			'image_settings'      => array(
				'type'        => 'control_section',
				'label'       => __( 'Image Settings', 'advanced-gallery' ),
				'tab'		  => 'TAB_CONTENT',
				'subcontrols' => array(
                    'advImage'    => array(
						'label'   => __( 'Upload Image', 'advanced-gallery' ),
						'type'    => 'GALLERY'
					),
					'advFilterGal' => array(
						'label'   => __( 'Add Gallery with Filter', 'advanced-gallery' ),
						'type'    => 'REPEATER',
						'title_field' => sprintf( __( 'Filter Group: %1$s', 'advanced-gallery' ), '{{filter}}' ),
						'default' => [
							[
								'filter' => __( 'Filter Name', 'advanced-gallery' ),
							]
						],
						'fields' => $repeater->get_controls(),
					),
                    'advImgSize'   => array(
						'type'          => 'group_control_image_size',
						'default'       => 'medium_large',
					),
					'advImageFit'    => array(
						'label'   => __( 'Image Fit', 'advanced-gallery' ),
						'type'    => 'SWITCHER',
						'default' => 'no',
					),
					'advRowHeight'   => array(
						'label'   => __( 'Row Height', 'advanced-gallery' ),
						'type'    => 'SLIDER',
						'default' => [
							'unit' => 'px',
							'size' => 250,
						],
						'range' => [
							'px' => [
								'min' => 0,
								'max' => 1000,
								'step' => 5,
							],
						],
						'frontend_available' => true,
					),
					'advImgSpacing'    => array(
						'label'   => __( 'Image Spacing', 'advanced-gallery' ),
						'type'    => 'SLIDER',
						'default' => [
							'unit' => 'px',
							'size' => 20,
						],
						'size_units' => [ 'px' ],
						'selectors' => [
							'{{WRAPPER}} .adv-gal-image-grid__wrap:not(.adv-masonry-gallery):not(.adv-filterable-gallery) '  => 'grid-gap: {{SIZE}}{{UNIT}};',
							'{{WRAPPER}} .adv-gal-image-grid__wrap.adv-masonry-gallery,
							{{WRAPPER}} .adv-gal-image-grid__wrap.adv-filterable-gallery' => '--adv-image-gutter: {{SIZE}}{{UNIT}}'
							
						],
						'frontend_available' => true,
					),
					'advImgMargin'    => array(
						'label'   => __( 'Image Spacing', 'advanced-gallery' ),
						'type'    => 'SLIDER',
						'default' => [
							'unit' => 'px',
							'size' => 20,
						],
						'range' => [
							'px' => [
								'min' => 0,
								'max' => 100,
							],
						],
						'size_units' => [ 'px' ],
						'selectors' => [
							'{{WRAPPER}} .adv-justified-gallery'  => '--justified-grid-pull: {{SIZE}}{{UNIT}};',
						],
						'frontend_available' => true,
					),
					'advImgCol'    => array(
						'label'   => __( 'Number of Columns', 'advanced-gallery' ),
						'type'    => 'NUMBER',
						'min'	  => 1,
						'default' => 4,
						'max'     => 6,
						'selectors' => [
							'{{WRAPPER}} .adv-gal-image-grid__wrap' => '--adv-image-col: {{VALUE}}'				
						],
						'frontend_available' => true,
					),
					'enableLightbox'    => array(
						'label'   => __( 'Enable Lightbox', 'advanced-gallery' ),
						'type'    => 'SWITCHER',
						'default' => 'yes',
						'condition' => [ 'advCapLayout' => 'card'],
					)
				),
			),
			'caption_settings'=> array(
				'type'        => 'control_section',
				'label'       => __( 'Caption Settings', 'advanced-gallery' ),
				'tab'		  => 'TAB_CONTENT',
				'subcontrols' => array(
                    'advCaption'    => array(
						'label'   => __( 'Show Caption', 'advanced-gallery' ),
						'type'    => 'SWITCHER',
						'default' => 'no',
						'frontend_available' => true
					),
					'advTitle'    => array(
						'label'   => __( 'Caption Type', 'advanced-gallery' ),
						'type'    => 'SELECT',
						'default' => 'title',
						'options'   => array(
							'title'       => __( 'Title', 'advanced-gallery' ),
							'description' => __( 'Description', 'advanced-gallery' ),
							'alt'         => __( 'Alt', 'advanced-gallery' ),
							'caption'     => __( 'Caption', 'advanced-gallery' )
						),
						'condition' => [ 'advCaption' => 'yes']
					),
					'advAlignment'    => array(
						'label'   => __( 'Caption Alignment', 'advanced-gallery' ),
						'type'    => 'CHOOSE',
						'default' => 'left',
						'options'   => array(
							'left' => [
								'title' => __('Left', 'advanced-gallery'),
								'icon' => 'eicon-text-align-left',
							],
							'center' => [
								'title' => __('Center', 'advanced-gallery'),
								'icon' => 'eicon-text-align-center',
							],
							'right' => [
								'title' => __('Right', 'advanced-gallery'),
								'icon' => 'eicon-text-align-right',
							],
						),
						'condition' => [ 'advCaption' => 'yes']
					),
					'advCapLayout'    => array(
						'label'   => __( 'Caption Layout', 'advanced-gallery' ),
						'type'    => 'SELECT',
						'default' => 'card',
						'options'   => array(			
							'overlay' => __( 'Overlay', 'advanced-gallery' ),
							'card'    => __( 'Card', 'advanced-gallery' )
						),
						'condition' => [ 'advCaption' => 'yes']
					),
				)
			),
			'image_styling'      => array(
				'type'        => 'control_section',
				'label'       => __( 'Image Styling', 'advanced-gallery' ),
				'tab'		  => 'TAB_STYLE',
				'subcontrols' => array(
					'advImgPadding'    => array(
						'label'   => __( 'Padding', 'advanced-gallery' ),
						'type'    => 'DIMENSIONS',
						'size_units' => [ 'px', 'em', '%', 'rem' ],
						'selectors' => [
							'{{WRAPPER}} .adv-gal-image-grid__wrap:not(.justified-gallery) .adv-img-cap-wrap' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
							'{{WRAPPER}} .adv-gal-image-grid__wrap.justified-gallery .adv-img-cap-wrap img' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
							'{{WRAPPER}} .adv-gal-image-grid__wrap.justified-gallery'  =>' --image-padding-top: {{TOP}}{{UNIT}}; --image-padding-left: {{LEFT}}{{UNIT}}; --image-padding-right: {{RIGHT}}{{UNIT}};--image-padding-bottom: {{BOTTOM}}{{UNIT}};',
						
						],
					),
					'advImgRadius'    => array(
						'label'   => __( 'Border Radius', 'advanced-gallery' ),
						'type'    => 'DIMENSIONS',
						'size_units' => [ 'px', '%' ],
						'selectors' => [
							'{{WRAPPER}} .adv-gal-image-grid__wrap .adv-img-cap-wrap img,
							{{WRAPPER}} .adv-gal-image-grid__wrap .adv-img-cap-wrap .adv-img-wrap,
							{{WRAPPER}} .adv-gal-image-grid__wrap .adv-img-cap-wrap,
							{{WRAPPER}} .adv-gal-image-grid__wrap .adv-img-cap-wrap.overlay,
							{{WRAPPER}} .adv-gal-image-grid__wrap .adv-img-cap-wrap.card, 
							{{WRAPPER}} .adv-img-cap-wrap.overlay .adv-gal-cap-wrap ' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
							'{{WRAPPER}} .adv-gal-image-grid__wrap .adv-img-cap-wrap.card img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} 0 0;',
							'{{WRAPPER}} .adv-gal-image-grid__wrap .adv-img-cap-wrap.card .adv-gal-cap-wrap' => 'border-radius: 0 0 {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
						],
					),
					'advCaptionShadow'    => array(
						'label'     => __( 'Box Shadow', 'advanced-gallery' ),
						'type'      => 'group_control_box_shadow',
						'exclude' => [
							'box_shadow_position',
						],
						'selector'  => '{{WRAPPER}} .adv-gal-image-grid__wrap:not(.adv-justified-gallery) .adv-img-cap-wrap,
										{{WRAPPER}} .adv-gal-image-grid__wrap.adv-justified-gallery .adv-img-cap-wrap img',
					),
					'advEdHover'    => array(
						'label'   => __( 'Enable Hover Animation', 'advanced-gallery' ),
						'type'    => 'SWITCHER',
						'default' => 'no',
						'condition' => [ 'advCapLayout' => 'card'],
					),
					'advImgHover'    => array(
						'label'   => __( 'Hover Animation', 'advanced-gallery' ),
						'type' => 'HOVER_ANIMATION',
						'default' => 'grow',
						'label_block' => true,
						'condition' => [ 'advCapLayout' => 'card', 'advEdHover' => 'yes'],
					),
					'advHoverAni'    => array(
						'label'   => __( 'Animation Delay', 'advanced-gallery' ),
						'type' => 'NUMBER',
						'min'	  => 0,
						'step'     => 100,
						'selectors' => [
							'{{WRAPPER}} .adv-gal-image-grid__wrap .adv-img-wrap img' => '--adv-animation-delay: {{VALUE}}'		
						],
						'condition' => [ 'advCapLayout' => 'card', 'advEdHover' => 'yes'],
					),
					'advBgOverlayColor'    => array(
						'label'   => __( 'Overlay Background Color', 'advanced-gallery' ),
						'type'    => 'COLOR',
						'default' => 'rgba(255,255,255,0.8)',
						'selectors' => [
							'{{WRAPPER}} .adv-gal-image-grid__wrap .adv-img-cap-wrap.overlay:hover .adv-gal-cap-wrap' => 'background-color: {{VALUE}};',
						],
						'condition' => [ 'advCapLayout' => 'overlay'],
					)					
				),
			),
			'caption_styling'      => array(
				'type'        => 'control_section',
				'label'       => __( 'Caption Styling', 'advanced-gallery' ),
				'tab'		  => 'TAB_STYLE',
				'subcontrols' => array(
					'advCaptionTypo'    => array(
						'label'   => __( 'Typography', 'advanced-gallery' ),
						'type'    => 'group_control_typography',
						'selector' => '{{WRAPPER}} .adv-gal-cap-wrap p',
					),
					'advTextColor'    => array(
						'label'   => __( 'Text Color', 'advanced-gallery' ),
						'type'    => 'COLOR',
						'default' => '#000000',
						'selectors' => [
							'{{WRAPPER}} .adv-gal-cap-wrap p' => 'color: {{VALUE}};',
						],
					),
					'advBgColor'    => array(
						'label'   => __( 'Background Color', 'advanced-gallery' ),
						'type'    => 'COLOR',
						'default' => '#FFFFFF',
						'condition' => [ 'advCapLayout' => 'card'],
						'selectors' => [
							'{{WRAPPER}} .adv-gal-cap-wrap' => 'background-color: {{VALUE}};',
						],
					),
					'advTextPadding'    => array(
						'label'   => __( 'Padding', 'advanced-gallery' ),
						'type'    => 'DIMENSIONS',
						'size_units' => [ 'px', 'em', '%', 'rem' ],
						'default'	=> [
							'top' => 12,
							'right' => 12,
							'bottom' => 12,
							'left' => 12,
							'unit' => 'px',
							'isLinked' => true,
						],
						'selectors' => [
							'{{WRAPPER}} .adv-img-cap-wrap .adv-gal-cap-wrap .adv-cap-inner-wrap' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
					),
				)
			)
        );

		// Filter Controls
		$general_terms_controls['filter_settings'] = array(
			'type'        => 'control_section',
			'label'       => __( 'Filter Settings', 'advanced-gallery' ),
			'tab'		  => 'TAB_CONTENT',
			'subcontrols' => array(
				
				'advShowFilter'    => array(
					'label'   => __( 'Show Filter', 'advanced-gallery' ),
					'type'    => 'SWITCHER',
					'default' => 'yes',
				),
				'advShowAllText'    => array(
					'label'   => __( 'Show "All" Text', 'advanced-gallery' ),
					'type'    => 'SWITCHER',
					'default' => 'yes',
					'condition' => [ 'advShowFilter' => 'yes']
				),
				'advAllText'    => array(
					'label'   => __( 'Rename "All" Text', 'advanced-gallery' ),
					'type'    => 'TEXT',						
					'default' => 'All',
					'condition' => [ 'advShowFilter' => 'yes', 'advShowAllText' => 'yes' ]
				),
				'advFilAlignment'    => array(
					'label'   => __( 'Filter Alignment', 'advanced-gallery' ),
					'type'    => 'CHOOSE',
					'default' => 'center',
					'options'   => array(
						'left' => [
							'title' => __('Left', 'advanced-gallery'),
							'icon' => 'eicon-text-align-left',
						],
						'center' => [
							'title' => __('Center', 'advanced-gallery'),
							'icon' => 'eicon-text-align-center',
						],
						'right' => [
							'title' => __('Right', 'advanced-gallery'),
							'icon' => 'eicon-text-align-right',
						],
					),
					'condition' => [ 'advShowFilter' => 'yes']
				),
			),
		);

		$general_terms_controls['filter_styling'] = array(
			'type'        => 'control_section',
			'label'       => __( 'Filter Styling', 'advanced-gallery' ),
			'tab'		  => 'TAB_STYLE',
			'subcontrols' => array(
				'advFilterTypo'    => array(
					'label'   => __( 'Filter Typography', 'advanced-gallery' ),
					'type'    => 'group_control_typography',
					'selector' => '{{WRAPPER}} .adv-gal-filter button',
				),
				'advFilterSpacing'    => array(
					'label'   => __( 'Item Spacing', 'advanced-gallery' ),
					'type'    => 'SLIDER',
					'default' => [
						'unit' => 'px',
						'size' => 10,
					],
					'size_units' => [ 'px' ],
					'selectors' => [
						'{{WRAPPER}} .adv-gal-filter button'  => 'margin: 0 {{SIZE}}{{UNIT}} {{SIZE}}{{UNIT}} {{SIZE}}{{UNIT}};',
						'{{WRAPPER}} .adv-gal-filter button:first-child'  => 'margin: 0 {{SIZE}}{{UNIT}} {{SIZE}}{{UNIT}} 0;',
					],
				),
				'advFilterborder'    => array(
					'label'    => __( 'Filter Border', 'advanced-gallery' ),
					'type'     => 'group_control_border',
					'selector' => '{{WRAPPER}} .adv-gal-filter button',
				),
				'advTextColorNormalTab'  => array(
					'type'    => 'sub_control_section',
					'mark'	  => 'start',
					'label'   => __( 'Normal', 'advanced-gallery' ),
					'subsubcontrols' => array(
						'advFilTextColor' => array(
							'label'   => __( 'Filter Text Color', 'advanced-gallery' ),
							'type'    => 'COLOR',
							'default' => '#000000',
							'selectors' => [
								'{{WRAPPER}} .adv-gal-filter button.adv-gal-filter__item' => 'color: {{VALUE}};',
							],
						),
						'advFilBgColor'    => array(
							'label'   => __( 'Filter Background Color', 'advanced-gallery' ),
							'type'    => 'COLOR',
							'default' => '#FFFFFF',
							'selectors' => [
								'{{WRAPPER}} .adv-gal-filter button.adv-gal-filter__item' => 'background-color: {{VALUE}};',
							],
						),
					)
				),
				'advTextColorActiveTab'  => array(
					'type'    => 'sub_control_section',
					'label'   => __( 'Active', 'advanced-gallery' ),
					'mark'	  => 'end',
					'subsubcontrols' => array(
						'advFilTextColorActive'    => array(
							'label'   => __( 'Filter Text Color', 'advanced-gallery' ),
							'type'    => 'COLOR',
							'default' => '#FFFFFF',
							'selectors' => [
								'{{WRAPPER}} .adv-gal-filter button.adv-gal-filter__item--active' => 'color: {{VALUE}};',
							],
						),
						'advFilBgColorActive'    => array(
							'label'   => __( 'Filter Background Color', 'advanced-gallery' ),
							'type'    => 'COLOR',
							'default' => '#c9c9c9',
							'selectors' => [
								'{{WRAPPER}} .adv-gal-filter button.adv-gal-filter__item--active' => 'background-color: {{VALUE}};',
							],
						),
					)
				),
			)
		);

        $default_taxonomies = array(
			'gallery-grid' => array(
				'title'    => __( 'Gallery Grid', 'advanced-gallery' ),
				'icon'     => 'adv-gallery-grid',
				'styleDependency' => [ 'adv-gal-widgets', 'fancybox-css' ],
				'jsDependency' => [ 'fancybox-js' ]
			),
			'justified-gallery' => array(
				'title'    => __( 'Justified Gallery', 'advanced-gallery' ),
				'icon'     => 'adv-justified-gallery',
				'styleDependency' => [ 'adv-gal-widgets', 'justifiedGallery-css', 'fancybox-css' ],
				'jsDependency' => [ 'adv-gal-widgets-js', 'justifiedGallery-js', 'fancybox-js' ]
			),
			'masonry-gallery' => array(
				'title'    => __( 'Masonry Gallery', 'advanced-gallery' ),
				'icon'     => 'adv-masonry-gallery',
				'styleDependency' => [ 'adv-gal-widgets', 'fancybox-css' ],
				'jsDependency' => [ 'adv-gal-widgets-js', 'isotope', 'fancybox-js' ]
			),
			'filterable-gallery' => array(
				'title'    => __( 'Filterable Gallery', 'advanced-gallery' ),
				'icon'     => 'adv-filterable-gallery',
				'styleDependency' => [ 'adv-gal-widgets', 'fancybox-css' ],
				'jsDependency' => [ 'adv-gal-widgets-js', 'isotope', 'fancybox-js' ]
			)
		);

        foreach ( $default_taxonomies as $widget_name => $widget_args ) {
			$widget_args['categories']          = 'advgallery';
			$widget_args['style_dependencies']  = array();
			$widget_args['script_dependencies'] = array();
			
			$image_settings   = $general_terms_controls['image_settings'];
			$caption_settings = $general_terms_controls['caption_settings'];
			$image_styling    = $general_terms_controls['image_styling'];
			$caption_styling  = $general_terms_controls['caption_styling'];

            $widget_args['controls'] = array(
				"{$widget_name}_image_settings"   => $image_settings,
				"{$widget_name}_caption_settings" => $caption_settings,
				"{$widget_name}_image_styling"    => $image_styling,
				"{$widget_name}_caption_styling"  => $caption_styling
			);

			if ('filterable-gallery' === $widget_name) {
				$filter_settings = $general_terms_controls['filter_settings'];
				$filter_styling  = $general_terms_controls['filter_styling'];
				$widget_args['controls']["{$widget_name}_filter_settings"] = $filter_settings;
				$widget_args['controls']["{$widget_name}_filter_styling"]  = $filter_styling;
			}

			$widgets[ "advgallery-{$widget_name}" ] = $widget_args;

            $core_widget_collection                = apply_filters(
                'adv_gal_elementor_widgets',
                $widgets
            );
    
            $this->core_widgets_collection = $core_widget_collection;
		}
    }

    public function get_core_widget_setting( $name, $key = null ) {
		$settings = array();
		if ( isset( $this->core_widgets_collection[ $name ] ) ) {
			$settings = $this->core_widgets_collection[ $name ];
		} elseif ( isset( $this->core_widgets_collection[ "advgallery-{$name}" ] ) ) {
			$settings = $this->core_widgets_collection[ "advgallery-{$name}" ];
		}
		if ( $key && isset( $settings[ $key ] ) ) {
			return $settings[ $key ];
		}
		return $settings;
	}

    /**
	 * Registers Elementor Widgets.
	 */
	public function register_elementor_widgets() {

		include_once ADVANCED_GALLERY_PLUGIN_PATH . 'includes/classes/class-widget.php';

		$core_widgets = $this->core_widgets_collection;

		if ( is_array( $core_widgets ) ) {
			$core_widgets = array_keys( $core_widgets );
		}

		$core_widgets = apply_filters( 'adv_gal_elementor_widgets_file_names', $core_widgets );

		foreach ( $core_widgets as $core_widget ) {
			$core_widget = str_replace( 'advgallery-', '', $core_widget );
			if ( file_exists( ADVANCED_GALLERY_PLUGIN_PATH . "includes/widgets/{$core_widget}/{$core_widget}.php" ) ) {
				include_once ADVANCED_GALLERY_PLUGIN_PATH . "includes/widgets/{$core_widget}/{$core_widget}.php";
				$class_name = str_replace( '-', '_', $core_widget );
				$class_name = __NAMESPACE__ . "\Widget_{$class_name}";
				if ( method_exists( Plugin::instance()->widgets_manager, 'register' ) ) {
					Plugin::instance()->widgets_manager->register( new $class_name() );
				} else {
					Plugin::instance()->widgets_manager->register_widget_type( new $class_name() );
				}
			}
		}
	}
}

Widgets_Controller::instance();