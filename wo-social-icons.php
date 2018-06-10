<?php  
/*
Plugin Name: WO Social Icons
Plugin URI: https://github.com/WindelOira/wo-social-icons
Description: A simple WordPress plugin template
Version: 1.0
Author: Windel Kien L. Oira
Author URI: https://github.com/WindelOira
License: GPL2
*/
/*
Copyright 2012  Windel Kien L. Oira  ( email : windeloira07@gmail.com )

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as 
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

namespace WOSI;

if( ! class_exists( 'WO_SocialIcons' ) ) :
	class WO_SocialIcons extends \WP_Widget {
		/**
		 * Plugin version.
		 *
		 * @var string
		 */
		protected $version = '1.0.0';

		/**
		 * Icons.
		 *
		 * @var array
		 */
		protected $icons;

		/**
		 * Sets up the widgets name etc.
		 */
		public function __construct() {
			$widgetOps = [
				'classname' 	=> 'wo_social_icons',
				'description' 	=> 'Advanced social icons widget.',
			];
			parent::__construct( 'wo_social_icons', 'WO Social Icons', $widgetOps );

			$this->icons = apply_filters( 'wosi_default_icons', [
				0 	=> 'facebook',
				1 	=> 'google-plus',
				2 	=> 'instagram',
				3 	=> 'linkedin',
				4 	=> 'twitter'
			]);

			add_action( 'admin_enqueue_scripts', [ $this, 'adminEnqueueStylesScripts' ] );
			add_action( 'wp_ajax_nopriv_ajaxReorderIcons', [ $this, 'ajaxReorderIcons' ] );
			add_action( 'wp_ajax_ajaxReorderIcons', [ $this, 'ajaxReorderIcons' ] );

			add_action( 'wp_enqueue_scripts', [ $this, 'enqueueStylesScripts' ] );
		}

		/**
		 * Outputs the content of the widget.
		 *
		 * @param array $a
		 * @param array $i
		 */
		public function widget( $a, $i ) {
			extract( $a );

			echo $before_widget;
				if ( ! empty( $i['title'] ) ) :
					echo $before_title . apply_filters( 'widget_title', $i['title'] ) . $after_title;
				endif;

				$icons = is_array( $i['icons'] ) ? $i['icons'] : $this->icons;
		?>
				<ul class="wo-social-icons">
				<?php 
				foreach( $icons as $icon ) : 
					if( ! empty( $i[ $icon ]['url'] ) ) :
				?>
					<li id="wosi-<?php echo $icon; ?>">
						<a href="<?php echo $i[ $icon ]['url']; ?>" <?php echo ! is_null( $i['link-type'] ) ? 'target="_blank"' : ''; ?>><?php echo $icon; ?></a>
					</li>
				<?php 
					endif;
				endforeach; 
				?>
				</ul>
		<?php
			echo $after_widget;
		}

		/**
		 * Outputs the options form on admin.
		 *
		 * @param array $i 	 Widget options.
		 */
		public function form( $i ) {
			$title = ! empty( $i['title'] ) ? $i['title'] : '';
			$icons = is_array( $i['icons'] ) ? $i['icons'] : $this->icons;
		?>
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>">
					<?php esc_attr_e( 'Title:', 'wo-social-icons' ); ?>	
				</label> 
				<input id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" class="widefat" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
			</p>

			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'link-type' ) ); ?>">
					<?php esc_attr_e( 'Open links in a new tab ?', 'wo-social-icons' ); ?>
				</label>
				<input id="<?php echo esc_attr( $this->get_field_id( 'link-type' ) ); ?>" type="checkbox" name="<?php echo esc_attr( $this->get_field_name( 'link-type' ) ); ?>" <?php checked( $i['link-type'], 'on' ); ?>>
			</p>

			<p class="alignleft radius">
				<label for="<?php echo esc_attr( $this->get_field_id( 'radius' ) ); ?>">
					<?php esc_attr_e( 'Radius:', 'wo-social-icons' ); ?>
				</label>
				<input id="<?php echo esc_attr( $this->get_field_id( 'radius' ) ); ?>" type="number" name="<?php echo esc_attr( $this->get_field_name( 'radius' ) ); ?>" value="<?php echo $i['radius'] > 0 ? $i['radius'] : 0; ?>" class="widefat" min="0">
			</p>

			<p class="alignright unit">
				<label for="<?php echo esc_attr( $this->get_field_id( 'unit' ) ); ?>">
					<?php esc_attr_e( 'Unit:', 'wo-social-icons' ); ?>
				</label>
				<select name="<?php echo esc_attr( $this->get_field_name( 'unit' ) ) ?>" id="<?php echo esc_attr( $this->get_field_id( 'unit' ) ); ?>" class="widefat">
					<option value="px" <?php selected( $i['unit'], 'px' ); ?>>
						<?php esc_attr_e( 'px', 'wo-social-icons' ); ?>
					</option>
					<option value="%" <?php selected( $i['unit'], '%' ); ?>>
						<?php esc_attr_e( '%', 'wo-social-icons' ); ?>
					</option>
					<option value="em" <?php selected( $i['unit'], 'em' ); ?>>
						<?php esc_attr_e( 'em', 'wo-social-icons' ); ?>
					</option>
					<option value="rem" <?php selected( $i['unit'], 'rem' ); ?>>
						<?php esc_attr_e( 'rem', 'wo-social-icons' ); ?>
					</option>
				</select>
			</p>

			<ul id="<?php echo $this->id; ?>-sortable" class="<?php echo $this->widget_options['classname']; ?>-sortable">
				<?php foreach( $icons as $icon ) : ?>
				<li id="<?php echo $icon; ?>">
					<h3><?php echo ucwords( str_replace( '-', ' ', $icon ) ); ?></h3>
					<div>
						<p>
							<label for="<?php echo esc_attr( $this->get_field_id( $icon ) ); ?>-url">
								<?php esc_attr_e( 'URL:', 'wo-social-icons' ); ?>
							</label>
							<input id="<?php echo esc_attr( $this->get_field_id( $icon ) ); ?>-url" class="widefat" type="url" value="<?php echo ! empty( $i[ $icon ]['url'] ) ? $i[ $icon ]['url'] : ''; ?>" name="<?php echo esc_attr( $this->get_field_name( $icon ) ); ?>[url]">
						</p>
						<fieldset class="colors">
							<legend>Colors</legend>
							<p class="alignleft">
								<label for="<?php echo esc_attr( $this->get_field_id( $icon ) ); ?>-color">
									<?php esc_attr_e( 'Color:', 'wo-social-icons' ); ?>
								</label>
								<input id="<?php echo esc_attr( $this->get_field_id( $icon ) ); ?>-color" type="text" class="wosi-color-picker" name="<?php echo esc_attr( $this->get_field_name( $icon ) ); ?>[color]" value="<?php echo ! empty( $i[ $icon ]['color'] ) ? $i[ $icon ]['color'] : ''; ?>">
							</p>

							<p class="alignright">
								<label for="<?php echo esc_attr( $this->get_field_id( $icon ) ); ?>-color-hover">
									<?php esc_attr_e( 'Hover Color:', 'wo-social-icons' ); ?>
								</label>
								<input id="<?php echo esc_attr( $this->get_field_id( $icon ) ); ?>-color-hover" type="text" class="wosi-color-picker" name="<?php echo esc_attr( $this->get_field_name( $icon ) ); ?>[color-hover]" value="<?php echo ! empty( $i[ $icon ]['color-hover'] ) ? $i[ $icon ]['color-hover'] : ''; ?>">
							</p>

							<p class="alignleft">
								<label for="<?php echo esc_attr( $this->get_field_id( $icon ) ); ?>-bg">
									<?php esc_attr_e( 'Background:', 'wo-social-icons' ); ?>
								</label>
								<input id="<?php echo esc_attr( $this->get_field_id( $icon ) ); ?>-bg" type="text" class="wosi-color-picker" name="<?php echo esc_attr( $this->get_field_name( $icon ) ); ?>[bg]" value="<?php echo ! empty( $i[ $icon ]['bg'] ) ? $i[ $icon ]['bg'] : ''; ?>">
							</p>

							<p class="alignright">
								<label for="<?php echo esc_attr( $this->get_field_id( $icon ) ); ?>-bg-hover">
									<?php esc_attr_e( 'Hover Background:', 'wo-social-icons' ); ?>
								</label>
								<input id="<?php echo esc_attr( $this->get_field_id( $icon ) ); ?>-bg-hover" type="text" class="wosi-color-picker" name="<?php echo esc_attr( $this->get_field_name( $icon ) ); ?>[bg-hover]" value="<?php echo ! empty( $i[ $icon ]['bg-hover'] ) ? $i[ $icon ]['bg-hover'] : ''; ?>">
							</p>
							<div class="clear"></div>
						</fieldset>
					</div>
				</li>
				<?php endforeach; ?>
			</ul>
		<?php
		}

		/**
		 * Enqueue admin scripts & styles.
		 */
		public function adminEnqueueStylesScripts( $h ) {
			if( $h != 'widgets.php' ) return;

			wp_enqueue_style( 'wp-color-picker' );
			wp_enqueue_style( 'wo-social-icons', plugins_url( 'assets/css/style.css', __FILE__ ), '', $this->version );

			wp_register_script( 'wo-social-icons', plugins_url( 'assets/js/script.js', __FILE__ ), [ 'jquery', 'jquery-ui-accordion', 'jquery-ui-sortable', 'wp-color-picker' ], $this->version, true );
			wp_enqueue_script( 'wo-social-icons' );
			wp_localize_script(
				'wo-social-icons',
				'wosi',
				[
					'id' 				=> $this->id,
					'widget_options' 	=> $this->widget_options,
					'ajax' 				=> [
						'url' 				=> admin_url( 'admin-ajax.php' ),
						'actions' 			=> [
							'reorder' 			=> 'ajaxReorderIcons'
						]
					]
				]
			);
		}

		/**
		 * Enqueue scripts & styles.
		 */
		public function enqueueStylesScripts() {
			wp_enqueue_style( 'wo-socialicons', plugins_url( 'assets/css/wo-social-icons.css', __FILE__ ), '', $this->version );


			$settings = $this->get_settings()[ $this->number ];
			$styles = '';

			if( $settings['radius'] > 0 ) :
				$styles .= '#' . $this->id . ' ul.wo-social-icons > li a {';
					$styles .= 'border-radius: ' . $settings['radius'] . $settings['unit'] . ';';
				$styles .= '}'; 
			endif;

			foreach( $this->icons as $icon ) :
				if( 
					! empty( $settings[ $icon ]['color-hover'] ) ||
					! empty( $settings[ $icon ]['bg-hover'] )
				) :
					$styles .= '#' . $this->id . ' ul.wo-social-icons > li#wosi-' . $icon . ' a {';
						if( ! empty( $settings[ $icon ]['color'] ) ) :
							$styles .= 'color: ' . $settings[ $icon ]['color'] . ';';
						endif;

						if( ! empty( $settings[ $icon ]['bg'] ) ) :
							$styles .= 'background-color: ' . $settings[ $icon ]['bg'] . ';';
						endif;
					$styles .= '}'; 
				endif;

				if( 
					! empty( $settings[ $icon ]['color-hover'] ) ||
					! empty( $settings[ $icon ]['bg-hover'] )
				) :
					$styles .= '#' . $this->id . ' ul.wo-social-icons > li#wosi-' . $icon . ' a:hover {';
						if( ! empty( $settings[ $icon ]['color-hover'] ) ) :
							$styles .= 'color: ' . $settings[ $icon ]['color-hover'] . ';';
						endif;

						if( ! empty( $settings[ $icon ]['bg-hover'] ) ) :
							$styles .= 'background-color: ' . $settings[ $icon ]['bg-hover'] . ';';
						endif;
					$styles .= '}'; 
				endif;
			endforeach;

			wp_add_inline_style( 'wo-socialicons', $styles );
		}

		/**
		 * AJAX Reorder icons.
		 */
		public function ajaxReorderIcons() {
			if( ! defined( DOING_AJAX ) && ! DOING_AJAX ) return;

			$allInstances = $this->get_settings();
			$instances = $allInstances[ $this->number ];
			$instances['icons'] = $_POST['icons'];

			$newInstances = wp_parse_args( $allInstances, $instances );

			if( update_option( 'widget_wo_social_icons', $newInstances ) ) :
				wp_send_json([
					'success' 	=> true
				]);
			endif;
		}

		/**
		 * Processing widget options on save.
		 *
		 * @param array $ni 	New options.
		 * @param array $oi 	Previous options.
		 *
		 * @return array
		 */
		public function update( $ni, $oi ) {
			$i = [];
			$i['title'] = ! empty( $ni['title'] ) ? $ni['title'] : '';
			$i['link-type'] = $ni['link-type'];
			$i['radius'] = $ni['radius'];
			$i['unit'] = $ni['unit'];
			foreach( $this->icons as $icon ) :
				$i[ $icon ]['url'] = ! empty( $ni[ $icon ]['url'] ) ? $ni[ $icon ]['url'] : '';
				$i[ $icon ]['color'] = ! empty( $ni[ $icon ]['color'] ) ? $ni[ $icon ]['color'] : '';
				$i[ $icon ]['color-hover'] = ! empty( $ni[ $icon ]['color-hover'] ) ? $ni[ $icon ]['color-hover'] : '';
				$i[ $icon ]['bg'] = ! empty( $ni[ $icon ]['bg'] ) ? $ni[ $icon ]['bg'] : '';
				$i[ $icon ]['bg-hover'] = ! empty( $ni[ $icon ]['bg-hover'] ) ? $ni[ $icon ]['bg-hover'] : '';
			endforeach;

			return $i;
		}
	}

	// Register widget.
	add_action( 'widgets_init', function(){
		register_widget( 'WOSI\WO_SocialIcons' );
	});
endif;
?>