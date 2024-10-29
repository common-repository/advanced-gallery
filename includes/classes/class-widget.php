<?php
namespace Advanced_Gallery;

use \Elementor\Widget_Base;
use \Elementor\Controls_Manager;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Border;
use Elementor\Plugin; 

/**
 * Widgets.
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class Widgets.
 *
 * @since 1.0.0
 */
class Widget extends Widget_Base {

	/**
	 * Default filter is the global filter
	 * and can be overriden from settings
	 *
	 * @var string
	 */
	protected $_default_filter = '*';

    /**
	 * Widget Name.
	 */
	public function get_name() {
		return $this->widget_name;
	}

	/**
	 * Widget Title.
	 */
	public function get_title() {
		return Widgets_Controller::instance()->get_core_widget_setting( $this->widget_name, 'title' );
	}

	/**
	 * Widget Icon.
	 */
	public function get_icon() {
		return Widgets_Controller::instance()->get_core_widget_setting( $this->widget_name, 'icon' );
	}

	/**
	 * Style dependencies.
	 */
	public function get_style_depends() {
		return Widgets_Controller::instance()->get_core_widget_setting( $this->widget_name, 'styleDependency' );
	}

	/**
	 * Javascripts dependencies.
	 */
	public function get_script_depends() {
		return Widgets_Controller::instance()->get_core_widget_setting( $this->widget_name, 'jsDependency' );
	}

	/**
	 * Widget categories.
	 */
	public function get_categories() {
		return array( 'advgallery' );
	}

    /**
	 * Adds controls from
	 */
    private function adv_gal_add_controls( $controls ) {

        foreach ( $controls as $id => $args ) {
            $args  = (object) $args;

            if ( ! isset( $args->type ) ) {
				continue;
			}

            if ( 'control_section' === $args->type ) {
				if ( isset( $args->subcontrols ) ) {

					$tab = 'TAB_CONTENT' === $args->tab ? Controls_Manager::TAB_CONTENT : Controls_Manager::TAB_STYLE;

					$this->start_controls_section(
						$id,
						array(
							'label' => $args->label,
							'tab'   => $tab
						)
					);
					$this->adv_gal_add_controls( $args->subcontrols );
					$this->end_controls_section();
				}
				continue;
			}

			if ( 'sub_control_section' === $args->type ) {
			
				if ( isset( $args->subsubcontrols ) ) {
					if ( 'start' === $args->mark ){
						$this->start_controls_tabs( $id . '_tab_start' );
					}
					$this->start_controls_tab(
						$id,
						array(
							'label' => $args->label,
						)
					);
					$this->adv_gal_add_controls( $args->subsubcontrols );
					$this->end_controls_tab();

					if ( 'end' === $args->mark ){
						$this->end_controls_tabs();
					}			
				}
				
				continue;
			}

			if ( defined( "\Elementor\Controls_Manager::{$args->type}" ) ) {
				$args->type = \constant( "\Elementor\Controls_Manager::{$args->type}" );
			}
				
			if ( 'group_control_image_size' === $args->type ) {
				$args->name = $id;
				$this->add_group_control(
					Group_Control_Image_Size::get_type(),
					(array) $args
				);
			} elseif ( 'group_control_typography' === $args->type ) {
				$args->name = $id;
				$this->add_group_control(
					Group_Control_Typography::get_type(),
					(array) $args
				);
			} elseif ( 'group_control_box_shadow' === $args->type ) {
				$args->name = $id;
				$this->add_group_control(
					Group_Control_Box_Shadow::get_type(),
					(array) $args
				);
			}  elseif ( 'group_control_border' === $args->type ) {
				$args->name = $id;
				$this->add_group_control(
					Group_Control_Border::get_type(),
					(array) $args
				);
			} elseif( Controls_Manager::DIMENSIONS === $args->type || Controls_Manager::NUMBER === $args->type || Controls_Manager::SLIDER === $args->type ) {
				$this->add_responsive_control(
					$id,
					(array) $args
				);
			} else {
				$this->add_control(
					$id,
					(array) $args
				);
			}
        }
    }

    /**
	 * Widget Settigs.
	 */
    protected function register_controls() { // phpcs:ignore
		
		$settings = Widgets_Controller::instance()->get_core_widget_setting( $this->widget_name, 'controls' );
		$controls = isset( $settings['controls'] ) && is_array( $settings['controls'] ) ? $settings['controls'] : array();

		if ('advgallery-filterable-gallery' === $this->widget_name) {			
			unset($settings['filterable-gallery_image_settings']['subcontrols']['advImage']);
			unset($settings['filterable-gallery_image_settings']['subcontrols']['advImgMargin']);
			unset($settings['filterable-gallery_image_settings']['subcontrols']['advRowHeight']);
		}
		if ('advgallery-gallery-grid' === $this->widget_name ) {			
			unset($settings['gallery-grid_image_settings']['subcontrols']['advFilterGal']);
			unset($settings['gallery-grid_image_settings']['subcontrols']['advImgMargin']);
			unset($settings['gallery-grid_image_settings']['subcontrols']['advRowHeight']);
		}
		if ( 'advgallery-masonry-gallery' === $this->widget_name ) {			
			unset($settings['masonry-gallery_image_settings']['subcontrols']['advFilterGal']);
			unset($settings['masonry-gallery_image_settings']['subcontrols']['advImgMargin']);
			unset($settings['masonry-gallery_image_settings']['subcontrols']['advRowHeight']);
		}
		if ( 'advgallery-justified-gallery' === $this->widget_name ) {			
			unset($settings['justified-gallery_image_settings']['subcontrols']['advFilterGal']);	
			unset($settings['justified-gallery_image_settings']['subcontrols']['advImgSize']);
			unset($settings['justified-gallery_image_settings']['subcontrols']['advImgSpacing']);
			unset($settings['justified-gallery_image_settings']['subcontrols']['advImgCol']);
			unset($settings['justified-gallery_image_settings']['subcontrols']['advImageFit']);
			unset($settings['justified-gallery_image_settings']['subcontrols']['advCaptionShadow']);
		}
		$this->adv_gal_add_controls( $settings );
	}

	/**
	 * Caption Settigs.
	 */
	protected function get_caption_data( $setting, $img ) { 

		$image_alt_text        = get_post_meta($img, '_wp_attachment_image_alt', true);
		$image_caption         = get_post_field('post_excerpt', $img);
		$image_title           = get_post_field('post_title', $img);
		$image_content         = get_post_field('post_content', $img);

		switch ( $setting ) {
			case 'title':
				$caption_text = $image_title;
				break;

			case 'description':
				$caption_text = $image_content;
				break;

			case 'alt':
				$caption_text = $image_alt_text;
				break;

			case 'caption':
				$caption_text = $image_caption;
				break;

			default:
				$caption_text = $image_title;
				break;
		}

		return $caption_text;
	}

	/**
	 * Render Caption Information.
	 */
	protected function render_caption_data( $attributes, $adv_title ) {
		if ( 'yes' === $attributes['advCaption'] && !empty($adv_title) ){ ?>
			<div <?php $this->print_render_attribute_string( 'caption_wrap' ); ?>>
				<?php 	
					echo '<div class="adv-cap-inner-wrap"><p>' . esc_html( $adv_title ) . '</p></div>';	
				?>
			</div>
		<?php } 
	}
	
	/**
	 * Get Gallery Data.
	 */
	protected function get_gallery_data( $attributes ) {
		$gallery = [];

		if( isset($attributes['advImage']) && !empty($attributes['advImage']) ){
			$gallery = $attributes['advImage'];
		} elseif( isset($attributes['advFilterGal']) && !empty($attributes['advFilterGal']) ) {
			
			if ( ! is_array( $attributes['advFilterGal'] ) || empty( $attributes['advFilterGal'] ) ) {
				return [];
			}

			$menu = [];
			$items = [];

			foreach( $attributes['advFilterGal'] as $key => $item ){		
				if ( empty( $item['images'] ) ) {
					continue;
				}
	
				$images = $item['images'];
				$filter = '__fltr-' . ( $key + 1 );
	
				if ( ! empty( $item['is_default_filter'] ) ) {
					$this->_default_filter = '.' . $filter;
				}
	
				if ( $filter && ! isset( $data[ $filter ] ) ) {
					$menu[ $filter ] = $item['filter'];
				}
	
				foreach ( $images as $image ) {
					if ( ! isset( $items[ $image['id'] ] ) ) {
						$items[ $image['id'] ] = $filter;
					} else {
						$items[ $image['id'] ] .= ' ' . $filter;
					}
				}	
			}

			$gallery = compact( 'menu', 'items' );
		}

		return $gallery;
	}
	
	/**
	 * Notice when gallery is empty
	 */
	protected function image_missing_alert() {
		if( Plugin::instance()->editor->is_edit_mode() ){
			printf(
				'<div %s>%s</div>',
				'style="margin: 1rem;padding: 1rem 1.25rem;border-top: 5px solid #0583f254;color: #292929;background-color: #0583f229;"',
				__( 'Please add images to the gallery', 'advanced-gallery' )
			);
		}
	}

	/**
	 * Run JS script in the editor preview
	 */
	protected function render_editor_script(){
		echo '<script type="text/javascript">
				jQuery(".adv-filterable-gallery").isotope({
					itemSelector: ".filter-item"
				});
				jQuery(".adv-masonry-gallery").isotope({
					itemSelector: ".filter-item"
				});
			</script>';		
	}

	/**
	 * Final output.
	 */
	protected function render() {
		$attributes   = $this->get_settings_for_display();
		$gallery_data = $this->get_gallery_data($attributes);
		$settings     = Widgets_Controller::instance()->get_core_widget_setting( $this->widget_name );
		$gallery_class = '';

		$col_no           = $settings['title'] === "Justified Gallery" ? 4 : $attributes['advImgCol'];

		if( $settings['title'] === "Justified Gallery" ){
			$gallery_class = ' adv-justified-gallery';
		}else if( $settings['title'] === "Masonry Gallery" ){
			$gallery_class = ' adv-masonry-gallery';
		}else if( $settings['title'] === "Filterable Gallery" ){
			$gallery_class = ' adv-filterable-gallery';
		}else if( $settings['title'] === "Gallery Grid" ){
			$gallery_class = ' adv-gallery-grid';
		}
	
		$image_wrap_class = 'adv-gal-image-grid__wrap img-column-' . $col_no . $gallery_class;
		$this->add_render_attribute( 'grid_wrap', 'class', $image_wrap_class );

		$image_fit = "Justified Gallery" !== $settings['title'] && 'yes' === $attributes['advImageFit'] ? ' adv-img-fit-enabled' : '';
		$img_and_cap_wrap_class = 'adv-img-cap-wrap ' . $attributes['advCapLayout'] . $image_fit;
		$this->add_render_attribute( 'img_cap_wrap', 'class', $img_and_cap_wrap_class ); 

		$cap_wrap_class = 'adv-gal-cap-wrap align-' . $attributes['advAlignment']; 
		$this->add_render_attribute( 'caption_wrap', 'class', $cap_wrap_class ); 
		
		$elementor_animation = $attributes['advImgHover'] ? 'elementor-animation-' . $attributes['advImgHover'] : '';

		//Lighbox settings
		$item_html_tag = $attributes['enableLightbox'] ? 'a' : 'div';
		$tag_for_justified = $attributes['enableLightbox'] && $settings['title'] === "Justified Gallery" ? 'a' : 'div';

		if( $settings['title'] === "Justified Gallery" ){
			$image_size = 'full';
		} else { 
			if ('custom' === $attributes['advImgSize_size']){
				$width = $attributes['advImgSize_custom_dimension']['width'];
				$height = $attributes['advImgSize_custom_dimension']['height'];
				$image_size = [$width, $height];
			} else {
				$image_size = $attributes['advImgSize_size'];
			}
		}

		if( $settings['title'] !== "Filterable Gallery" ){
			if( empty( $gallery_data ) ) {
				$this->image_missing_alert();
				return;
			}
		}

		if( $settings['title'] === "Filterable Gallery" ){
			if( empty( $gallery_data['items'] ) ) {
				$this->image_missing_alert();
				return;
			}
		}

		if( $settings['title'] === "Filterable Gallery" && isset($attributes['advFilterGal']) && isset($attributes['advShowFilter']) ){
			if ( $attributes['advShowFilter'] === 'yes' ) : ?>
				<div class="adv-gal-filter adv-gal-js-filter align-<?php echo esc_attr($attributes['advFilAlignment']);?>" data-default-filter="<?php echo esc_attr($this->_default_filter); ?>" role="navigation" aria-label="<?php echo esc_attr_x( 'Gallery filter', 'Gallery filter aria label', 'advanced-gallery' ); ?>">
					<?php if ( $attributes['advShowAllText'] ) : ?>
						<button class="adv-gal-filter__item" type="button" data-filter="*"><?php echo esc_html( $attributes['advAllText'] ); ?></button></li>
					<?php endif; ?>
					<?php foreach ( $gallery_data['menu'] as $key => $val ) : ?>
						<button class="adv-gal-filter__item" type="button" data-filter=".<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $val ); ?></button></li>
					<?php endforeach; ?>
				</div>
			<?php endif;
		} ?>

		<div <?php $this->print_render_attribute_string( 'grid_wrap' ); ?>>

			<?php if( $settings['title'] !== "Filterable Gallery" ) {
				
				if( !empty($gallery_data) ){
				
					foreach ( $gallery_data as $img ) :					
						$adv_title  = $this->get_caption_data( $attributes['advTitle'], $img['id'] );
						$popup = $attributes['enableLightbox'] ? sprintf( 'href="%s" data-fancybox="adv-gallery"', esc_url( wp_get_attachment_image_url( $img['id'], 'full' ) ) ) : '';
						$popup_justified = $attributes['enableLightbox'] && $settings['title'] === "Justified Gallery" ? sprintf( 'href="%s" data-fancybox="adv-gallery"', esc_url( wp_get_attachment_image_url( $img['id'], 'full' ) ) ) : '';

						if( $settings['title'] === "Masonry Gallery" ) echo '<div class="filter-item">'; ?>
							<<?php echo wp_kses_post( $tag_for_justified ); ?> <?php echo $popup_justified; ?> <?php $this->print_render_attribute_string( 'img_cap_wrap' ); ?>>
								<?php										
									if( $settings['title'] !== "Justified Gallery" ) { ?>
										<<?php echo wp_kses_post( $item_html_tag ); ?> <?php echo $popup; ?> class="adv-img-wrap">
									<?php }

									echo wp_get_attachment_image( $img['id'], $image_size, false, [ 'class' => esc_attr( $elementor_animation ) ] ); 

									if( $settings['title'] !== "Justified Gallery" ) { ?>
										</<?php echo wp_kses_post( $item_html_tag ); ?>>
									<?php }

									$this->render_caption_data($attributes, $adv_title );				
								?>
							</<?php echo wp_kses_post( $tag_for_justified ); ?>>
						<?php if( $settings['title'] === "Masonry Gallery" ) echo '</div>';
					endforeach;
				}
			} else {
				
				foreach ( $gallery_data['items'] as $img => $filter_str ) :
				
				$popup = $attributes['enableLightbox'] ? sprintf( 'href="%s" data-fancybox="adv-gallery"', esc_url( wp_get_attachment_image_url( $img, 'full' ) ) ) : '';
					
				$adv_title = $this->get_caption_data( $attributes['advTitle'], $img ); ?>
				<div class="<?php echo 'filter-item ' . esc_attr( $filter_str ); ?>">
					<div class="adv-img-cap-wrap <?php echo esc_attr( $image_fit ); ?> <?php echo esc_attr($attributes['advCapLayout']); ?>">
						<<?php echo wp_kses_post( $item_html_tag ); ?> <?php echo $popup; ?> class="adv-img-wrap">
							<?php echo wp_get_attachment_image( $img, $image_size, false, [ 'class' => esc_attr( $elementor_animation ) ] ); ?>
						</<?php echo wp_kses_post( $item_html_tag ); ?>> 
						<?php $this->render_caption_data($attributes, $adv_title ); ?>			
					</div>
				</div>

				<?php endforeach; 
			} ?>
		</div>
		<?php

		if( Plugin::instance()->editor->is_edit_mode() ){
			$this->render_editor_script();
		}
		
	}

}