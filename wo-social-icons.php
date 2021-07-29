<?php  
/*
Plugin Name: WO Social Icons
Plugin URI: https://github.com/WindelOira/wo-social-icons
Description: Flexible social media plugin for wordpress.
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
		protected $version;

		/**
		 * Icons.
		 *
		 * @var array
		 */
		protected $icons;

		/** 
		 * Icons set.
		 *
		 * @var array 
		 */
		protected $iconSet;

		/**
		 * Sets up the widgets name etc.
		 */
		public function __construct() {
			$widgetOps = [
				'classname' 	=> 'wo_social_icons',
				'description' 	=> 'Advanced social icons widget.',
			];
			parent::__construct( 'wo_social_icons', 'WO Social Icons', $widgetOps );

			$this->version = '1.0.0';

			$this->icons = apply_filters( 'wosi_default_icons', [
				0	=> 'behance',
				1 	=> 'dribbble',
				2 	=> 'email',
				3 	=> 'facebook',
				4 	=> 'flickr',
				5 	=> 'github',
				6 	=> 'google-plus',
				7 	=> 'instagram',
				8 	=> 'linkedin',
				9 	=> 'medium',
				10 	=> 'phone',
				11 	=> 'pinterest',
				12 	=> 'rss',
				13 	=> 'snapchat',
				14 	=> 'stumbleupon',
				15 	=> 'tumblr',
				16 	=> 'twitter',
				17 	=> 'vimeo',
				18 	=> 'wordpress',
				19 	=> 'xing',
				20	=> 'youtube'
			]);

			$this->iconSet = apply_filters( 'wosi_default_icons_set', [
				'behance'		=> [
					'behance', 'behance-square'
				],
				'dribbble' 		=> [
					'dribbble'
				],
				'email' 		=> [
					'envelope-o'
				],
				'facebook' 		=> [
					'facebook', 'facebook-official', 'facebook-square'
				],
				'flickr' 		=> [
					'flickr'
				],
				'github'		=> [
					'github', 'github-square'
				],
				'google-plus'	=> [
					'google-plus', 'google-plus-circle', 'google-plus-square'
				],
				'instagram' 	=> [
					'instagram'
				],
				'linkedin'		=> [
					'linkedin', 'linkedin-square'
				],
				'medium' 		=> [
					'medium'
				],
				'phone'			=> [
					'phone', 'phone-square'
				],
				'pinterest' 	=> [
					'pinterest', 'pinterest-p', 'pinterest-square'
				],
				'rss' 			=> [
					'rss-square'
				],
				'snapchat' 		=> [
					'snapchat', 'snapchat-ghost', 'snapchat-square'
				],
				'stumbleupon' 		=> [
					'stumbleupon', 'stumbleupon-circle'
				],
				'tumblr' 		=> [
					'tumblr', 'tumblr-square'
				],
				'twitter' 		=> [
					'twitter', 'twitter-square'
				],
				'vimeo' 		=> [
					'vimeo', 'vimeo-square'
				],
				'wordpress'		=> [
					'wordpress'
				],
				'xing' 			=> [
					'xing', 'xing-square'
				],
				'youtube' 		=> [
					'youtube', 'youtube-play', 'youtube-square'
				]
			]);

			add_action( 'admin_enqueue_scripts', [ $this, 'adminEnqueueStylesScripts' ] );
			add_action( 'wp_ajax_nopriv_ajaxReorderIcons', [ $this, 'ajaxReorderIcons' ] );
			add_action( 'wp_ajax_ajaxReorderIcons', [ $this, 'ajaxReorderIcons' ] );

			add_action( 'wp_enqueue_scripts', [ $this, 'enqueueStylesScripts' ] );
			add_filter( 'upload_mimes', [ $this, 'addMimeTypes' ] );
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
					if( ! empty( $i[$icon]['url'] ) ) :

					if( 
						$icon == 'email' &&
						is_email( $i[$icon]['url'] ) 
					) :
						$i[$icon]['url'] = 'mailto:' . $i[$icon]['url'];
					elseif( 
						$icon == 'phone' &&
						$this->validatePhone( $i[$icon]['url'] )
					) :
						$i[$icon]['url'] = 'tel:' . $i[$icon]['url'];
					endif;
				?>
					<li id="wosi-<?php echo $icon; ?>">
						<a href="<?php echo $i[$icon]['url']; ?>" <?php echo ! is_null( $i['link-type'] ) ? 'target="_blank"' : ''; ?>>
							<?php 
							if( ! empty( $i[$icon]['custom-icon'] ) ) : 
								$iconMetaData = get_post_meta( $i[$icon]['custom-icon'] );
								$iconMimeType = get_post_mime_type( $i[$icon]['custom-icon'] );
								$iconSize = $i['font-size'] * 2;

								if( $iconMimeType == 'image/png' ) :
									echo wp_get_attachment_image( $i[$icon]['custom-icon'], 'full', true, [ 'class' => 'wosi-icon-image' ] );
								else :
							?>
								<span>
									<object data="<?php echo wp_get_attachment_url( $i[$icon]['custom-icon'] ); ?>" 
											width="<?php echo $i['font-size']; ?>" 
											height="<?php echo $i['font-size']; ?>" data-color="<?php echo $i[$icon]['color']; ?>" 
											data-hcolor="<?php echo $i[$icon]['color-hover']; ?>" 
											type="image/svg+xml">
										<?php echo wp_get_attachment_image( $i[$icon]['custom-icon'], 'full', true, [ 'class' => 'wosi-icon-image' ] ); ?>
									</object>
								</span>
							<?php
								endif; 
							else : 
							?>
							<svg class="wosi-icon wosi-icon-<?php echo $icon; ?>">
								<use xlink:href="<?php echo ! empty( $i[$icon]['icon'] ) ? esc_url( plugin_dir_url( __FILE__ ) . 'assets/img/symbol-defs.svg#wosi-icon-' . $i[$icon]['icon'] ) : esc_url( plugin_dir_url( __FILE__ ) . 'assets/img/symbol-defs.svg#wosi-icon-' . $this->iconSet[$icon][0] ); ?>"></use>
							</svg>
							<?php endif; ?>
							<span class="wosi-icon-text"><?php echo ucwords(str_replace("-", " ", $icon)); ?></span>
						</a>
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
		 * @param array $i 	 	Widget options.
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

			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'font-size' ) ); ?>">
					<?php esc_attr_e( 'Font Size:', 'wo-social-icons' ); ?>
				</label>
				<input id="<?php echo esc_attr( $this->get_field_id( 'font-size' ) ); ?>" type="number" name="<?php echo esc_attr( $this->get_field_name( 'font-size' ) ); ?>" value="<?php echo $i['font-size'] > 0 ? $i['font-size'] : 8; ?>" class="widefat" min="8" step="0.01">
			</p>

			<p class="alignleft radius">
				<label for="<?php echo esc_attr( $this->get_field_id( 'radius' ) ); ?>">
					<?php esc_attr_e( 'Radius:', 'wo-social-icons' ); ?>
				</label>
				<input id="<?php echo esc_attr( $this->get_field_id( 'radius' ) ); ?>" type="number" name="<?php echo esc_attr( $this->get_field_name( 'radius' ) ); ?>" value="<?php echo $i['radius'] > 0 ? $i['radius'] : 0; ?>" class="widefat" min="0" step="0.01">
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
				<?php 
				foreach( $icons as $icon ) : 
					if( $icon == 'email' ) :
						$type = 'email';
					elseif( $icon == 'phone' ) :
						$type = 'tel';
					else :
						$type = 'url';
					endif;
				?>
				<li id="<?php echo $icon; ?>" data-number="<?php echo $this->number; ?>">
					<h3><?php echo ucwords( str_replace( '-', ' ', $icon ) ); ?></h3>
					<div>
						<div class="alignleft">
							<label><?php esc_attr_e( 'Select Icon :', 'wo-social-icons' ); ?></label>
							<?php if( is_array($this->iconSet[$icon]) && count($this->iconSet[$icon]) ) : ?>
							<ul class="icon-set">
								<?php foreach( $this->iconSet[$icon] as $ics ) : ?>
								<li>
									<input id="<?php echo esc_attr( $this->get_field_id( $ics ) ); ?>" type="radio" name="<?php echo esc_attr( $this->get_field_name( $icon ) ); ?>[icon]" value="<?php echo $ics; ?>" <?php empty( $i[$icon]['custom-icon'] ) ? is_array( $i[$icon] ) && array_key_exists( 'icon', $i[$icon] ) ? checked( $i[$icon]['icon'], $ics ) : checked( $this->iconSet[$icon][0], $ics ) : ''; ?>>
									<label for="<?php echo esc_attr( $this->get_field_id( $ics ) ); ?>">
										<svg class="wosi-icon wosi-icon-<?php echo $ics; ?>">
											<use xlink:href="<?php echo esc_url( plugin_dir_url( __FILE__ ) . 'assets/img/symbol-defs.svg#wosi-icon-' . $ics ); ?>"></use>
										</svg>
									</label>
								</li>
								<?php endforeach; ?>
							</ul>
							<?php endif; ?>
						</div>
						<div class="hide-if-no-js alignright wosi-media">
							<label for="<?php echo esc_attr( $this->get_field_id( $icon . '-custom-icon' ) ); ?>">
								<?php esc_attr_e( 'or Upload Icon ( png, svg ) :', 'wo-social-icons' ); ?>
							</label>
							<div class="wosi-icon-holder">
								<?php echo ! empty( $i[$icon]['custom-icon'] ) ? wp_get_attachment_image( $i[$icon]['custom-icon'], 'full', false, [] ) : ''; ?>
							</div>
							<input id="<?php echo esc_attr( $this->get_field_id( $icon ) ); ?>-custom-icon" type="hidden" name="<?php echo esc_attr( $this->get_field_name( $icon ) ); ?>[custom-icon]" value="<?php echo ! empty( $i[$icon]['custom-icon'] ) ? $i[$icon]['custom-icon'] : ''; ?>">
							<div class="wosi-media-buttons">
								<button class="button wosi-add-media <?php echo ! empty( $i[$icon]['custom-icon'] ) ? 'hidden' : ''; ?>" type="button">
									<?php esc_attr_e( 'Add Icon', 'wo-social-icons' ); ?>
								</button>
								<button class="button-link button-link-delete wosi-delete-media <?php echo empty( $i[$icon]['custom-icon'] ) ? 'hidden' : ''; ?>" type="button">
									<?php esc_attr_e( 'Remove', 'wo-social-icons' ); ?>
								</button>
							</div>
						</div>

						<p class="clear"></p>

						<p>
							<label for="<?php echo esc_attr( $this->get_field_id( $icon ) ); ?>-url">
								<?php esc_attr_e( 'URL:', 'wo-social-icons' ); ?>
							</label>
							<input id="<?php echo esc_attr( $this->get_field_id( $icon ) ); ?>-url" class="widefat" type="<?php echo $type; ?>" value="<?php echo ! empty( $i[$icon]['url'] ) ? $i[$icon]['url'] : ''; ?>" name="<?php echo esc_attr( $this->get_field_name( $icon ) ); ?>[url]">
						</p>
						<fieldset class="colors">
							<legend>Colors</legend>
							<p class="alignleft">
								<label for="<?php echo esc_attr( $this->get_field_id( $icon ) ); ?>-color">
									<?php esc_attr_e( 'Color:', 'wo-social-icons' ); ?>
								</label>
								<input id="<?php echo esc_attr( $this->get_field_id( $icon ) ); ?>-color" type="text" class="wosi-color-picker" name="<?php echo esc_attr( $this->get_field_name( $icon ) ); ?>[color]" value="<?php echo ! empty( $i[$icon]['color'] ) ? $i[$icon]['color'] : ''; ?>">
							</p>

							<p class="alignright">
								<label for="<?php echo esc_attr( $this->get_field_id( $icon ) ); ?>-color-hover">
									<?php esc_attr_e( 'Hover Color:', 'wo-social-icons' ); ?>
								</label>
								<input id="<?php echo esc_attr( $this->get_field_id( $icon ) ); ?>-color-hover" type="text" class="wosi-color-picker" name="<?php echo esc_attr( $this->get_field_name( $icon ) ); ?>[color-hover]" value="<?php echo ! empty( $i[$icon]['color-hover'] ) ? $i[$icon]['color-hover'] : ''; ?>">
							</p>

							<p class="alignleft">
								<label for="<?php echo esc_attr( $this->get_field_id( $icon ) ); ?>-bg">
									<?php esc_attr_e( 'Background:', 'wo-social-icons' ); ?>
								</label>
								<input id="<?php echo esc_attr( $this->get_field_id( $icon ) ); ?>-bg" type="text" class="wosi-color-picker" name="<?php echo esc_attr( $this->get_field_name( $icon ) ); ?>[bg]" value="<?php echo ! empty( $i[$icon]['bg'] ) ? $i[$icon]['bg'] : ''; ?>">
							</p>

							<p class="alignright">
								<label for="<?php echo esc_attr( $this->get_field_id( $icon ) ); ?>-bg-hover">
									<?php esc_attr_e( 'Hover Background:', 'wo-social-icons' ); ?>
								</label>
								<input id="<?php echo esc_attr( $this->get_field_id( $icon ) ); ?>-bg-hover" type="text" class="wosi-color-picker" name="<?php echo esc_attr( $this->get_field_name( $icon ) ); ?>[bg-hover]" value="<?php echo ! empty( $i[$icon]['bg-hover'] ) ? $i[$icon]['bg-hover'] : ''; ?>">
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

			wp_enqueue_media();
			wp_enqueue_style( 'wp-color-picker' );
			wp_enqueue_style( 'wo-social-icons', plugins_url( 'assets/css/style.css', __FILE__ ), '', $this->version );

			wp_register_script( 'wo-social-icons', plugins_url( 'assets/js/script.js', __FILE__ ), [ 'jquery', 'jquery-ui-accordion', 'jquery-ui-sortable', 'wp-color-picker' ], $this->version, true );
			wp_enqueue_script( 'wo-social-icons' );
			wp_enqueue_script( 'svgxuse', plugins_url( 'assets/js/svgxuse.js', __FILE__ ), [ 'jquery' ], $this->version, true );
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

			$allInstances = $this->get_settings();

			if( count( $allInstances ) > 0 ) :
				$styles = '';

				foreach( $allInstances as $iK => $iV ) :
					if( $iV['radius'] > 0 ) :
						$styles .= '#' . $this->id_base . '-' . $iK . ' ul.wo-social-icons > li a {';
							$styles .= 'border-radius: ' . $iV['radius'] . $iV['unit'] . ';';
						$styles .= '}'; 
					endif;

					$styles .= '#' . $this->id_base . '-' . $iK . ' ul.wo-social-icons > li a {';
						$styles .= 'font-size: ' . $iV['font-size'] . 'px;';
					$styles .= '}'; 

					foreach( $this->icons as $icon ) :
						if( 
							! empty( $iV[$icon]['color'] ) ||
							! empty( $iV[$icon]['bg'] )
						) :
							$styles .= '#' . $this->id_base . '-' . $iK . ' ul.wo-social-icons > li#wosi-' . $icon . ' a {';
								if( ! empty( $iV[$icon]['color'] ) ) :
									$styles .= 'color: ' . $iV[$icon]['color'] . ';';
								endif;

								if( ! empty( $iV[$icon]['bg'] ) ) :
									$styles .= 'background-color: ' . $iV[$icon]['bg'] . ';';
								endif;
							$styles .= '}'; 
						endif;

						if( 
							! empty( $iV[$icon]['color-hover'] ) ||
							! empty( $iV[$icon]['bg-hover'] )
						) :
							$styles .= '#' . $this->id_base . '-' . $iK . ' ul.wo-social-icons > li#wosi-' . $icon . ' a:hover {';
								if( ! empty( $iV[$icon]['color-hover'] ) ) :
									$styles .= 'color: ' . $iV[$icon]['color-hover'] . ';';
								endif;

								if( ! empty( $iV[$icon]['bg-hover'] ) ) :
									$styles .= 'background-color: ' . $iV[$icon]['bg-hover'] . ';';
								endif;
							$styles .= '}'; 
						endif;
					endforeach;
				endforeach;

				wp_add_inline_style( 'wo-socialicons', $styles );

				wp_register_script( 'svgxuse', plugins_url( 'assets/js/svgxuse.js', __FILE__ ), [ 'jquery' ], $this->version, true );
				wp_register_script( 'jquery-svg-es5', plugins_url( 'assets/js/jquery.svg.es5.min.js', __FILE__ ), ['jquery'], $this->version, true );
				wp_register_script( 'jquery-svg', plugins_url( 'assets/js/jquery.svg.js', __FILE__ ), [ 'jquery' ], $this->version, true );
				wp_enqueue_script( 'wo-social-icons', plugins_url( 'assets/js/wo-social-icons.js', __FILE__ ), [ 'jquery', 'svgxuse', 'jquery-svg-es5', 'jquery-svg' ], $this->version, true );
			endif;
		}

		/**
		 * Add mime types filter.
		 *
		 * @param $m 				Existing mime types.
		 *
		 * @return array
		 */
		public function addMimeTypes( $m ) {
			$m['svg'] = 'image/svg+xml';

			return $m;
		}

		/**
		 * AJAX Reorder icons.
		 */
		public function ajaxReorderIcons() {
			if( ! defined( DOING_AJAX ) && ! DOING_AJAX ) return;

			$allInstances = $this->get_settings();
			$allInstances[$_POST['number']]['icons'] = $_POST['icons'];

			if( update_option( 'widget_wo_social_icons', $allInstances ) ) :
				wp_send_json([
					'success' 	=> true
				]);
			endif;
		}

		/**
		 * Validate phone number,
		 *
		 * @param string | int $ph 	Phone number.
		 *
		 * @return bool
		 *
		 * @reference https://www.codespeedy.com/how-to-validate-phone-number-in-php/
		 */
		public function validatePhone( $ph ) {
		     $filteredPhone = filter_var($ph, FILTER_SANITIZE_NUMBER_INT);
		     $phoneCheck = str_replace("-", "", $filteredPhone);

		     return strlen($phone_to_check) < 10 || strlen($phoneCheck) > 14 ? false : true;
		}

		/**
		 * Processing widget options on save.
		 *
		 * @param array $ni 		New options.
		 * @param array $oi 		Previous options.
		 *
		 * @return array
		 */
		public function update( $ni, $oi ) {
			// Get all instance
			$allInstances = $this->get_settings();
			$icons = $allInstances[$this->number]['icons'];

			$ni['icons'] = $icons;

			foreach( $ni as $niK => $niV ) :
				// Validate border radius.
				if( 
					$niK == 'radius' && 
					( $niV == '' || ! is_numeric( $niV ) )
				) :
					$ni[$niK] = 0;
				endif;

				// Validate font size.
				if( 
					$niK == 'font-size' && 
					( $niV == '' || ! is_numeric( $niV ) )
				) :
					$ni[$niK] = 8;
				endif;
			endforeach;

			// Icons
			foreach( $this->icons as $icon ) :
				if( 
					! empty( $ni[$icon]['url'] ) &&
					( $icon != 'email' && $icon != 'phone' )
				) :
					$ni[$icon]['url'] = esc_url( $ni[$icon]['url'] );
				endif;

				// Validate hex colors.
				if( 
					! empty( $ni[$icon]['color'] ) && 
					preg_match( '/^#(([a-fA-F0-9]{3}$)|([a-fA-F0-9]{6}$))/', $ni[$icon]['color'] ) == 0 
				) :
					$ni[$icon]['color'] = $oi[$icon]['color'];
				endif;

				if( 
					! empty( $ni[$icon]['color-hover'] ) && 
					preg_match( '/^#(([a-fA-F0-9]{3}$)|([a-fA-F0-9]{6}$))/', $ni[$icon]['color-hover'] 
				) == 0 ) :
					$ni[$icon]['color-hover'] = $oi[$icon]['color-hover'];
				endif;

				if( 
					! empty( $ni[$icon]['bg'] ) && 
					preg_match( '/^#(([a-fA-F0-9]{3}$)|([a-fA-F0-9]{6}$))/', $ni[$icon]['bg'] ) == 0 
				) :
					$ni[$icon]['bg'] = $oi[$icon]['bg'];
				endif;

				if( 
					! empty( $ni[$icon]['bg-hover'] ) && 
					preg_match( '/^#(([a-fA-F0-9]{3}$)|([a-fA-F0-9]{6}$))/', $ni[$icon]['bg-hover'] ) == 0 
				) :
					$ni[$icon]['bg-hover'] = $oi[$icon]['bg-hover'];
				endif;
			endforeach;

			return $ni;
		}
	}

	// Register widget.
	add_action( 'widgets_init', function(){
		register_widget( 'WOSI\WO_SocialIcons' );
	});
endif;
?>
