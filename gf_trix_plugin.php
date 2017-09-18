<?php
/*
Plugin Name: Gravity Forms Trix Editor Add-On
Description: Use the Trix rich text editor in Gravity Forms
Plugin URI: https://github.com/garrettjohnson/GravityForms-Trix
Version: 1.0.0
Author: Garrett Johnson
Author URI: http://garrett.io
License: GPL2
Text Domain: gravity-forms-trix

------------------------------------------------------------------------
Copyright 2017 Garrett Johnson
Copyright 2015 Adrian Gordon

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA

*/

if ( ! defined( 'ABSPATH' ) ) {
	die();
}

load_plugin_textdomain( 'gravity-forms-trix', false, dirname( plugin_basename( __FILE__ ) ) . '/lang' );


add_action( 'admin_notices', array( 'GF_TRIX', 'admin_warnings' ), 20 );
load_plugin_textdomain( 'itsg_gf_wysiwyg_ckeditor', false, dirname( plugin_basename( __FILE__ ) ) . '/lang' );

require_once( plugin_dir_path( __FILE__ ).'gf_trix_settings.php' );

if ( !class_exists( 'GF_TRIX' ) ) {
    class GF_TRIX {
			private static $name = 'CKEditor WYSIWYG for Gravity Forms';
    	private static $slug = 'gravity-forms-trix';

      /*
       * Construct the plugin object
       */
      public function __construct() {
			// register plugin functions through 'gform_loaded' -
			// this delays the registration until Gravity Forms has loaded, ensuring it does not run before Gravity Forms is available.
            add_action( 'gform_loaded', array( $this, 'register_actions' ) );
		} // END __construct

		/*
         * Register plugin functions
         */
		function register_actions() {
			// register actions
      if ( self::is_gravityforms_installed() ) {
				$trix_settings = self::get_options();

				// addon framework
				require_once( plugin_dir_path( __FILE__ ).'gf-trix-addon.php' );

				//start plug in

				add_action( 'wp_ajax_gf_trix_upload', array( $this, 'gf_trix_upload' ) );
				add_action( 'wp_ajax_nopriv_gf_trix_upload', array( $this, 'gf_trix_upload' ) );

				add_filter( 'gform_save_field_value', array( $this, 'save_field_value' ), 10, 4 );
				add_action( 'gform_field_standard_settings', array( $this, 'trix_field_settings' ), 10, 2 );
				add_filter( 'gform_tooltips', array( $this, 'ckeditor_field_tooltips' ) );
				add_action( 'gform_field_css_class', array( $this, 'ckeditor_field_css_class' ), 10, 3 );
				add_filter( 'gform_field_content',  array( $this, 'ckeditor_field_content' ), 10, 5 );
				add_filter( 'gform_counter_script', array( $this, 'ckeditor_counter_script_js' ), 10, 4 );
				add_filter( 'gform_merge_tag_filter', array( $this, 'decode_wysiwyg_frontend_confirmation' ), 10, 5 );
				add_filter( 'gform_entry_field_value', array( $this, 'decode_wysiwyg_backend_and_gravitypdf' ), 10, 4 );
				add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), array( $this, 'plugin_action_links' ) );

				add_filter( 'gform_field_validation', array( $this, 'ckeditor_dont_count_spaces' ), 10, 4 );

				add_filter( 'gform_entries_column_filter', array( $this,  'entries_column_filter' ), 10, 5 );

				if ( self::is_minimum_php_version() ) {
					require_once( plugin_dir_path( __FILE__ ).'gravitypdf/gravitypdf.php' );
				}


				if ( 'gf_settings' == RGForms::get('page') ) {
					// add settings page
					RGForms::add_settings_page( 'Trix Editor', array( 'GF_TRIX_Settings_Page', 'settings_page' ), self::get_base_url() . '/images/user-registration-icon-32.png' );

					if ( ( 'Trix+Editor' == RGForms::get('subview') || 'Trix Editor' == RGForms::get('subview') ) && !self::is_minimum_php_version() ) {
						add_action( 'admin_notices', array( $this, 'admin_warnings_minimum_php_version'), 20 );
					}

				}

				if ( $trix_settings['enable_upload_image'] && self::is_minimum_php_version() ) {
					// handles the change upload path settings
					add_filter( 'gform_upload_path', array( $this, 'change_upload_path' ), 10, 2 );
				}
			}
		} // END register_actions

		public function ckeditor_dont_count_spaces( $result, $value, $form, $field ) {
			//if ( $this->is_trix_editor( $field ) || $field->useRichTextEditor ) {
				if ( ! is_numeric( $field['maxLength'] ) ) {
					return $result;
				}

				$value = strip_tags( $value );

				$trix_settings = GF_TRIX::get_options();

				if ( rgar( $trix_settings, 'enable_count_spaces' ) ) {
					$value = preg_replace( '/\r|\n/', '' , $value ); // remove line breaks for the purpose of the character count
				} else {
					$value = preg_replace( '/\r|\n|\s|&nbsp;/', '' , $value ); // remove line breaks and spaces for the purpose of the character count
				}

				// decode HTML entities so they are counted correctly
				if ( function_exists( 'mb_convert_encoding' ) ) {
					$value =  mb_convert_encoding( $value, 'UTF-8', 'HTML-ENTITIES' );
				} else {
					$value =  preg_replace( '/&.*?;/', 'x', $value ); // multi-byte characters converted to X
				}

				if ( GFCommon::safe_strlen( $value ) > $field['maxLength'] ) {
					$result['is_valid']  = false;
					$result['message'] = empty( $field['errorMessage'] ) ? esc_html_x( 'The text entered exceeds the maximum number of characters.', 'Same as Gravity Forms (slug: gravityforms) plugin','gravity-forms-trix' ) : $field['errorMessage'];
				} elseif ( ! ( $field['isRequired'] && GFCommon::safe_strlen( $value ) == 0 ) ) {
					$result['is_valid']  = true;
				}
			//}
			return $result;
		}


		function entries_column_filter( $value, $form_id, $field_id, $entry, $query_string ) {
			$form = GFAPI::get_form( $form_id );
			foreach ( $form['fields'] as $field ) {
				if ( $field->id == $field_id && $this->is_trix_editor( $field ) ) {
					return  substr( wp_kses_post( htmlspecialchars_decode( strip_tags( $value, '<strong><a><u><i>' ) ) ), 0, 140);
				}
			}
			return $value;
		}

		function gf_trix_upload() {
			$CKEditorFuncNum = isset( $_GET['CKEditorFuncNum'] ) ? $_GET['CKEditorFuncNum'] : null;
			if ( is_null( $CKEditorFuncNum ) ) {
				die( "<script>
				window.parent.CKEDITOR.tools.callFunction(
				'',
				'',
				'ERROR: Failed to pass CKEditorFuncNum');
				</script>" );
			}

			$form_id = isset( $_GET['form_id'] ) ? $_GET['form_id'] : null;

			if ( is_null( $form_id ) ) {
				die( "<script>
				window.parent.CKEDITOR.tools.callFunction('',
				'',
				'ERROR: Failed to get form_id');
				</script>" );
			}

			// get target path - also responsible for creating directories if path doesnt exist
			$target = GFFormsModel::get_file_upload_path( $form_id, null );
			$target_path = pathinfo( $target['path'] );
			$target_url = pathinfo( $target['url'] );

			// get Ajax Upload options
			$trix_settings = self::get_options();

			// calculate file size in KB from MB
			$file_size = $trix_settings['setting_upload_filesize'];
			$file_size_kb = $file_size * 1024 * 1024;

			// push options to upload handler
			$options = array(
				'paramName' => 'upload',
				'param_name' => 'upload',
				'CKEditorFuncNum' => $CKEditorFuncNum,
				'upload_dir' => $target_path['dirname'].'/',
				'upload_url' => $target_url['dirname'].'/',
				'image_versions' => array(
					'' => array(
					'max_width' => empty( $trix_settings['setting_upload_filewidth'] ) ? null : $trix_settings['setting_upload_filewidth'],
					'max_height' => empty( $trix_settings['setting_upload_fileheight'] ) ? null : $trix_settings['setting_upload_fileheight'],
					'jpeg_quality' => empty( $trix_settings['setting_upload_filejpegquality'] ) ? null : $trix_settings['setting_upload_filejpegquality']
					)
				),
				'accept_file_types' => empty( $trix_settings['setting_upload_filetype'] ) ? '/(\.|\/)(png|tif|jpeg|jpg|gif)$/i' : '/(\.|\/)('.$trix_settings['setting_upload_filetype'].')$/i',
				'max_file_size' => empty( $file_size_kb ) ? null : $file_size_kb
			);

			if ( class_exists( 'ITSG_GFCKEDITOR_UploadHandler' ) ) {
				// initialise the upload handler and pass the options
				$upload_handler = new ITSG_GFCKEDITOR_UploadHandler( $options );
			}

			// terminate the function
			die();
		} // END gf_trix_upload

		/*
		 *   Changes the upload path for Gravity Form uploads.
		 *   Changes made by this function will be seen when the Gravity Forms function  GFFormsModel::get_file_upload_path() is called.
		 *   The default upload path applied by this function matches the default for Gravity forms:
		 *   /gravity_forms/{form_id}-{hashed_form_id}/{month}/{year}/
		 */
		function change_upload_path( $path_info, $form_id ) {
			$trix_settings = self::get_options();
			$file_dir = $trix_settings['setting_upload_filedir'];

			if ( 0 != strlen( $file_dir ) ) {
				// Generate the yearly and monthly dirs
				$time            = current_time( 'mysql' );
				$y               = substr( $time, 0, 4 );
				$m               = substr( $time, 5, 2 );

				// removing leading forward slash, if present
				if( '/' == $file_dir[0] ) {
					$file_dir = ltrim( $file_dir, '/' );
				}

				// remove leading forward slash, if present
				if( '/' == substr( $file_dir, -1 ) ) {
					$file_dir = rtrim( $file_dir, '/' );
				}

				// if {form_id} keyword used, replace with current form id
				if ( false !== strpos( $file_dir, '{form_id}' ) ) {
					$file_dir = str_replace( '{form_id}', $form_id, $file_dir );
				}

				// if {hashed_form_id} keyword used, replace with hashed current form id
				if ( false !== strpos( $file_dir, '{hashed_form_id}' ) ) {
					$file_dir = str_replace( '{hashed_form_id}', wp_hash( $form_id), $file_dir );
				}

				// if {year} keyword used, replace with current year
				if ( false !== strpos($file_dir,'{year}') ) {
					$file_dir = str_replace( '{year}', $y, $file_dir );
				}

				// if {month} keyword used, replace with current month
				if ( false !== strpos( $file_dir, '{month}' ) ) {
					$file_dir = str_replace( '{month}', $m, $file_dir );
				}

				// if {user_id} keyword used, replace with current user id
				if ( false !== strpos( $file_dir, '{user_id}' ) ) {
					if ( isset( $_POST['entry_user_id'] ) ) {
						$entry_user_id = $_POST['entry_user_id'];
						$file_dir = str_replace( '{user_id}', $entry_user_id, $file_dir );
					} else {
						$user_id = get_current_user_id() ? get_current_user_id() : '0';
						$file_dir = str_replace( '{user_id}', $user_id, $file_dir );
					}
				}

				// if {hashed_user_id} keyword used, replace with hashed current user id
				if ( false !== strpos( $file_dir, '{hashed_user_id}' ) ) {
					if ( isset( $_POST['entry_user_id'] ) ) {
						$entry_user_id = $_POST['entry_user_id'];
						$hashed_entry_user_id = wp_hash( $entry_user_id );
						$file_dir = str_replace( '{hashed_user_id}', $hashed_entry_user_id, $file_dir );
					} else {
						$hashed_user_id = wp_hash( is_user_logged_in() ? get_current_user_id() : '0' );
						$file_dir = str_replace( '{hashed_user_id}', $hashed_user_id, $file_dir );
					}
				}

				$upload_dir = wp_upload_dir(); // get WordPress upload directory information - returns an array

				$path_info['path']	= $upload_dir['basedir'].'/'.$file_dir.'/';  // set the upload path
				$path_info['url']	= $upload_dir['baseurl'].'/'.$file_dir.'/';  // set the upload URL
			}
			return $path_info;
		} // END change_upload_path

		/*
		 *   Converts php.ini memory limit string to bytes.
		 *   For example, 2MB would convert to 2097152
		 */
		public static function return_bytes( $val ) {
			$val = trim( $val );
			$last = strtolower( $val[ strlen( $val ) -1 ] );

			switch( $last ) {
				case 'g':
					$val *= 1024;
				case 'm':
					$val *= 1024;
				case 'k':
					$val *= 1024;
			}
			return $val;
		} // END return_bytes

		/*
		 *   Determines the maximum upload file size.
		 *   Retrieves three values from php.ini and returns the smallest.
		 */
		public static function max_file_upload_in_bytes() {
			//select maximum upload size
			$max_upload = self::return_bytes( ini_get( 'upload_max_filesize' ) );
			//select post limit
			$max_post = self::return_bytes( ini_get( 'post_max_size' ) );
			//select memory limit
			$memory_limit = self::return_bytes( ini_get( 'memory_limit' ) );
			// return the smallest of them, this defines the real limit
			return min( $max_upload, $max_post, $memory_limit );
		} // END max_file_upload_in_bytes

		/*
         * Add 'Settings' link to plugin in WordPress installed plugins page
         */
		function plugin_action_links( $links ) {

			$action_links = array(
				'settings' => '<a href="' . admin_url( 'admin.php?page=gf_settings&subview=WYSIWYG+CKEditor' ) . '" >' . __( 'Settings', 'gravity-forms-trix' ) . '</a>',
			);

			return array_merge( $action_links, $links );
		} // END plugin_action_links

		/*
		 *   Handles the plugin options.
		 *   Default values are stored in an array.
		 */
		public static function get_options() {
			$defaults = array(
				'enable_in_form_editor' => 'on',
				'enable_bold' => 'on',
				'enable_italic' => 'on',
				'enable_underline' => 'on',
				'enable_pastetext' => 'on',
				'enable_pastefromword' => 'on',
				'enable_numberedlist' => 'on',
				'enable_bulletedlist' => 'on',
				'enable_outdent' => 'on',
				'enable_indent' => 'on',
				'enable_link' => 'on',
				'enable_unlink' => 'on',
				'enable_format' => 'on',
				'enable_font' => 'on',
				'enable_fontsize' => 'on',
				'setting_upload_filesize' => '2',
				'setting_upload_filetype' => 'png|tif|jpeg|jpg|gif',
				'setting_upload_filedir' => '/gravity_forms/{form_id}-{hashed_form_id}/{month}/{year}/',
				'setting_upload_filejpegquality' => '75',
				'setting_upload_filewidth' => '786',
				'setting_upload_fileheight' => '786',
				'enable_upload_image' => 'off',
				'setting_editor_height' => '200',
				'setting_scayt_language' => 'en_US',
				'setting_link_target' => 'current_window',
			);

			$options = wp_parse_args( get_option( 'gf_trix_settings' ), $defaults );

			return $options;
		} // END get_options

		/*
         * Customises 'Paragraph Text' field output to
		 *  1. apply 'gform_trix' class to ckeditor fields in the wp-admin
		 *  2. include character limit details and CSS class for admin area
         */
		public function ckeditor_field_content( $content, $field, $value, $lead_id, $form_id ){
			if ( $this->is_trix_editor( $field ) ) {
				if ( is_admin() ){
					$content = str_replace( "class='", "class='gform_trix ", $content );
				} else {
					$label = rgar( $field, 'label' );
					$limit = ( '' == rgar( $field, 'maxLength' ) ? 'unlimited' : rgar( $field, 'maxLength' ) );
					$input_id = "input_".rgar( $field, 'formId' )."_".rgar( $field, 'id' );

					$content = str_replace( "<div class='ginput_container ", "<div class='trix-contained-input ginput_container ".$limit."' ", $content);
					$content = str_replace( "<textarea ", "<input type='hidden' ", $content);
					$content = str_replace( "</textarea>","<trix-editor input=".$input_id."></trix-editor>", $content);
				}
			}
			return $content;
		} // END ckeditor_field_content

		/*
         * Applies CSS class to 'Paragraph text' fields when CKEditor is enabled
         */
		public function ckeditor_field_css_class( $classes, $field, $form ) {
			if ( $this->is_trix_editor( $field ) ) {
				 $classes .= ' gform_trix';
			}
            return $classes;
        } // END ckeditor_field_css_class

		/*
         * Applies 'Enable Trix Editor' option to 'Paragraph Text' field
         */
		public function trix_field_settings($position, $form_id) {
			if ( 25 == $position ) {
				?>
				<li class="wysiwyg_field_setting_wysiwyg_ckeditor field_setting" style="display:list-item;">
					<input type="checkbox" id="field_enable_wysiwyg_ckeditor"/>
					<label for="field_enable_wysiwyg_ckeditor" class="inline">
						<?php _e( 'Enable Trix Editor', 'gravity-forms-trix' ); ?>
					</label>
					<?php gform_tooltip( 'form_field_enable_trix' ) ?><br/>
				</li>
			<?php
			}
		} // END trix_field_settings

		/*
         * Tooltip for field in form editor
         */
		public function ckeditor_field_tooltips( $tooltips ){
			$tooltips['form_field_enable_trix'] = '<h6>'.__( 'Enable WYSIWYG', 'gravity-forms-trix' ).'</h6>'.__( 'Check this box to turn this field into a rich text editor, using Trix.', 'gravity-forms-trix' );
			return $tooltips;
		} // END ckeditor_field_tooltips

		/*
         * Checks if field is CKEditor enabled
         */
		public function is_trix_editor( $field ) {
			$field_type = self::get_type( $field );
			if ( 'post_content' == $field_type ||
				'textarea' == $field_type ||
				( 'post_custom_field' == $field_type && 'textarea' == $field['inputType'] ) ) {
				if ( 'true' == $field->enable_wysiwyg_ckeditor && true !== $field->useRichTextEditor ) {
					return true;
				}
			}
			return false;
		} // END is_trix_editor

		/*
         * Get field type
         */
		private static function get_type( $field ) {
			$type = '';
			if ( isset( $field['type'] ) ) {
				$type = $field['type'];
				if ( 'post_custom_field' == $type ) {
					if ( isset( $field['inputType'] ) ) {
						$type = $field['inputType'];
					}
				}
				return $type;
			}
		} // END get_type

		/*
         * Modifies the value before saved to the database - removes line spaces
         */
		public function save_field_value( $value, $lead, $field, $form ) {
			if ( $this->is_trix_editor( $field ) ) {
				$value = rgpost( "input_{$field['id']}" );
				$value = preg_replace( "/\r|\n/", "", $value );
			}
			return $value;
		} // END save_field_value

		/*
         * Warning message if Gravity Forms is installed and enabled
         */
		public static function admin_warnings() {
			if ( !self::is_gravityforms_installed() ) {
				printf(
					'<div class="error"><h3>%s</h3><p>%s</p><p>%s</p></div>',
						__( 'Warning', 'gravity-forms-trix' ),
						sprintf ( __( 'The plugin %s requires Gravity Forms to be installed.', 'gravity-forms-trix' ), '<strong>'.self::$name.'</strong>' ),
						sprintf ( esc_html__( 'Please %sdownload the latest version of Gravity Forms%s and try again.', 'gravity-forms-trix' ), '<a href="https://www.e-junkie.com/ecom/gb.php?cl=54585&c=ib&aff=299380" target="_blank" >', '</a>' )
				);
			}
		} // END admin_warnings

		/*
         * Warning message if Gravity Forms is installed and enabled
         */
		public static function admin_warnings_minimum_php_version() {
				printf(
					'<div class="error"><h3>%s</h3><p>%s</p><p>%s</p></div>',
						__( 'Warning', 'gravity-forms-trix' ),
						sprintf( __( 'The <strong>image upload</strong> feature requires a minimum of PHP version 5.4.', 'gravity-forms-trix' ) ),
						sprintf( __( 'You are running an PHP version %s. Contact your web hosting provider to update.', 'gravity-forms-trix' ), phpversion() )
				);
		} // END admin_warnings_minimum_php_version

		/*
         * Check if GF is installed
         */
        private static function is_gravityforms_installed() {
			if ( !function_exists( 'is_plugin_active' ) || !function_exists( 'is_plugin_active_for_network' ) ) {
				require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
			}
			if (is_multisite()) {
				return ( is_plugin_active_for_network('gravityforms/gravityforms.php' ) || is_plugin_active( 'gravityforms/gravityforms.php' ) );
			} else {
				return is_plugin_active( 'gravityforms/gravityforms.php' );
			}
        } // END is_gravityforms_installed

		/*
         * Check if PHP version is at least 5.4
         */
        private static function is_minimum_php_version() {
			return version_compare( phpversion(), '5.4', '>=' );
        } // END is_minimum_php_version

		/*
         * Check if Gravity Forms - Data Persistence Reloaded is installed
         */
        private function is_dpr_installed() {
            return function_exists( 'ri_gfdp_ajax' );
        } // END is_dpr_installed

		/*
         * Get plugin url
         */
		 private function get_base_url(){
			return plugins_url( null, __FILE__ );
		} // END get_base_url

		/*
         * decodes the value before being displayed in the front end confirmation - for gravity wiz better pre-confirmation
         */
		public function decode_wysiwyg_frontend_confirmation( $value, $merge_tag, $modifier, $field, $raw_value ) {
			if ( $this->is_trix_editor( $field ) ) {
				return wp_kses_post( $raw_value );
			}
			return $value;
		} // END decode_wysiwyg_frontend_confirmation

		/*
         * decodes the value before being displayed in the entry editor and Gravity PDF 3.x
         */
		public function decode_wysiwyg_backend_and_gravitypdf( $value, $field, $lead, $form ) {
			if ( $this->is_trix_editor( $field ) ) {
				return  wp_kses_post( htmlspecialchars_decode( $value ) );
			}
			return $value;
		} // END decode_wysiwyg_backend_and_gravitypdf

    }
    $GF_TRIX = new GF_TRIX();
}