<?php
/*
 *   Setup the settings page for configuring the options
 */
if ( class_exists( 'GFForms' ) ) {
	GFForms::include_addon_framework();
	class GFTrixEditor extends GFAddOn {
		protected $_version = '1.0.0';
		protected $_min_gravityforms_version = '2';
		protected $_slug = 'gravity-forms-trix';
		protected $_full_path = __FILE__;
		protected $_title = 'Trix for Gravity Forms';
		protected $_short_title = 'Trix';

		public function scripts() {
			//$min = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG || isset( $_GET['gform_debug'] ) ? '' : '.min';
			$min = '';
			$version = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG || isset( $_GET['gform_debug'] ) ? mt_rand() : $this->_version;

			$scripts = array(
				array(
					'handle'    => 'trix_js',
					'src'       => $this->get_base_url() . "/trix/trix.js",
					'version'   => $version,
					'deps'      => array( ),
					'enqueue'   => array( array( $this, 'requires_scripts' ) ),
					'in_footer' => false,
					// Don't localize the script for now
					//'callback'  => array( $this, 'localize_scripts' ),
				),
				array(
					'handle'    => 'gf_trix_admin_js',
					'src'       => $this->get_base_url() . "/js/gf_trix_admin_js{$min}.js",
					'version'   => $version,
					'deps'      => array( 'jquery' ),
					'enqueue'   => array( GFCommon::is_form_editor() ),
					'in_footer' => true
				)
			);

			 return array_merge( parent::scripts(), $scripts );
		} // END scripts

		public function styles() {
			$min = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG || isset( $_GET['gform_debug'] ) ? '' : '.min';
			$version = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG || isset( $_GET['gform_debug'] ) ? mt_rand() : $this->_version;

			$styles = array(
				array(
					'handle'  => 'gf_trix_admin_css',
					'src'     => $this->get_base_url() . "/css/gf_trix_admin_css{$min}.css",
					'version' => $version,
					'media'   => 'screen',
					'enqueue' => array( GFCommon::is_form_editor() ),
				),
				array(
					'handle'  => 'trix_css',
					'src'     => $this->get_base_url() . "/trix/trix.css",
					'version' => $version,
					'media'   => 'screen',
					'enqueue' => array( array( $this, 'requires_scripts' ) ),
				),
			);

			return array_merge( parent::styles(), $styles );
		} // END styles

		public function localize_scripts( $form, $is_ajax ) {
			// Localize the script with new data
			$form_id = $form['id'];
			$is_entry_detail = GFCommon::is_entry_detail();
			$is_form_editor = GFCommon::is_form_editor();
			$admin_url = admin_url( 'admin-ajax.php' );
			$trix_settings = GF_TRIX::get_options();

			$extra_plugins = '';
			if ( ( ! $is_form_editor || ! $is_entry_detail ) || rgar( $trix_settings, 'enable_oembed' ) ) {
				if ( ! $is_form_editor && ! $is_entry_detail ) {
					$extra_plugins .=  'wordcount,notification';
					if ( rgar( $trix_settings, 'enable_oembed' ) ) {
						$extra_plugins .= ',oembed,widget,widgetselection,dialog';
					}
				}
			}

			$remove_plugins = '';
			if ( rgar( $trix_settings, 'enable_remove_elementspath' ) ) {
				$remove_plugins .=  'elementspath';
			}

			$ckeditor_fields = array();

			$toolbar_settings = array(
				'basicstyles' => array(
					! rgar( $trix_settings, 'enable_bold' ) ?: 'Bold',
					! rgar( $trix_settings, 'enable_italic' ) ?: 'Italic',
					! rgar( $trix_settings, 'enable_underline' ) ?: 'Underline',
					! rgar( $trix_settings, 'enable_strike' ) ?: 'Strike',
				),
				'clipboard' => array(
					! rgar( $trix_settings, 'enable_undo' ) ?: 'Undo',
					! rgar( $trix_settings, 'enable_redo' ) ?: 'Redo',
				),
				'paragraph' => array(
					! rgar( $trix_settings, 'enable_numberedlist' ) ?: 'NumberedList',
					! rgar( $trix_settings, 'enable_bulletedlist' ) ?: 'BulletedList',
					! rgar( $trix_settings, 'enable_outdent' ) ?: 'Outdent',
					! rgar( $trix_settings, 'enable_indent' ) ?: 'Indent',
					! rgar( $trix_settings, 'enable_blockquote' ) ?: 'Blockquote',
				),
				'links' => array(
					! rgar( $trix_settings, 'enable_link' ) ?: 'Link',
					! rgar( $trix_settings, 'enable_unlink' ) ?: 'Unlink',
					! rgar( $trix_settings, 'enable_anchor' ) ?: 'Anchor',
				),
				'styles' => array(
					! rgar( $trix_settings, 'enable_styles' ) ?: 'Styles',
					! rgar( $trix_settings, 'enable_format' ) ?: 'Format',
					! rgar( $trix_settings, 'enable_font' ) ?: 'Font',
					! rgar( $trix_settings, 'enable_fontsize' ) ?: 'FontSize',
				),
				'colors' => array(
					! rgar( $trix_settings, 'enable_textcolor' ) ?: 'TextColor',
					! rgar( $trix_settings, 'enable_bgcolor' ) ?: 'BGColor',
				),
				'tools' => array(
					! rgar( $trix_settings, 'enable_maximize' ) ?: 'Maximize',
					! rgar( $trix_settings, 'enable_showblocks' ) ?: 'ShowBlocks',
				),
				'about' => array(
					! rgar( $trix_settings, 'enable_about' ) ?: 'About',
				)
			);

			// setup CKEditor in the Form Editor (uses default settings)
			if ( $is_form_editor ) {
				$ckeditor_fields[0][ 'source' ] = $toolbar_settings[ 'source' ];
				$ckeditor_fields[0][ 'basicstyles' ] =  $toolbar_settings[ 'basicstyles' ];
				$ckeditor_fields[0][ 'clipboard' ] =  $toolbar_settings[ 'clipboard' ];
				$ckeditor_fields[0][ 'paragraph' ] =  $toolbar_settings[ 'paragraph' ];
				$ckeditor_fields[0][ 'links' ] =  $toolbar_settings[ 'links' ];
				$ckeditor_fields[0][ 'document' ] =  $toolbar_settings[ 'document' ];
				$ckeditor_fields[0][ 'insert' ] =  $toolbar_settings[ 'insert' ];
				$ckeditor_fields[0][ 'styles' ] =  $toolbar_settings[ 'styles' ];
				$ckeditor_fields[0][ 'colors' ] =  $toolbar_settings[ 'colors' ];
				$ckeditor_fields[0][ 'tools' ] =  $toolbar_settings[ 'tools' ];
				$ckeditor_fields[0][ 'about' ] =  $toolbar_settings[ 'about' ];
			} else {
				if ( is_array( $form['fields'] ) ) {
					foreach ( $form['fields'] as $field ) {
						if ( $this->is_trix_editor( $field ) ) {
							$field_id = $field['id'];
							$ckeditor_fields[ $field_id ][ 'source' ] = $toolbar_settings[ 'source' ] ;
							$ckeditor_fields[ $field_id ][ 'basicstyles' ] =  $toolbar_settings[ 'basicstyles' ];
							$ckeditor_fields[ $field_id ][ 'clipboard' ] =  $toolbar_settings[ 'clipboard' ];
							$ckeditor_fields[ $field_id ][ 'paragraph' ] =  $toolbar_settings[ 'paragraph' ];
							$ckeditor_fields[ $field_id ][ 'links' ] =  $toolbar_settings[ 'links' ];
							$ckeditor_fields[ $field_id ][ 'document' ] =  $toolbar_settings[ 'document' ];
							$ckeditor_fields[ $field_id ][ 'editing' ] =  $toolbar_settings[ 'editing' ];
							$ckeditor_fields[ $field_id ][ 'insert' ] =  $toolbar_settings[ 'insert' ];
							$ckeditor_fields[ $field_id ][ 'styles' ] =  $toolbar_settings[ 'styles' ];
							$ckeditor_fields[ $field_id ][ 'colors' ] =  $toolbar_settings[ 'colors' ];
							$ckeditor_fields[ $field_id ][ 'tools' ] =  $toolbar_settings[ 'tools' ];
						}
					}
				}
			}

			// filter to modify global settings on a per form basis
			//$ckeditor_fields = apply_filters( 'itsg_gf_ckeditor_fields', $ckeditor_fields, $form_id );

			$settings_array = array(
				'form_id' => GFCommon::is_entry_detail() ? $_GET['id'] : $form_id,
				'is_entry_detail' => $is_entry_detail ? $is_entry_detail : 0,
				'is_form_editor' => $is_form_editor ? $is_form_editor : 0,
				'is_admin' => is_admin() ? 1 : 0,
				'is_dpr_installed' => $this->is_dpr_installed() ? 1 : 0,
				'enable_upload_image' => rgar( $trix_settings, 'enable_upload_image') ? 1 : 0,
				'is_minimum_php_version' => $this->is_minimum_php_version() ? 1 : 0,
				'ckeditor_fields' => $ckeditor_fields,
				'extra_plugins' => $extra_plugins,
				'remove_plugins' => $remove_plugins,
				'admin_url' => $admin_url,
				'editor_height' => ! rgar( $trix_settings, 'setting_editor_height' ) || $is_form_editor ? '200' : intval( rgar( $trix_settings, 'setting_editor_height' ) ),
				'link_target' => ! rgar( $trix_settings, 'setting_link_target' ) || 'current_window' == rgar( $trix_settings, 'setting_link_target' ) ? '' : '_blank',
			);
			
			wp_localize_script( 'trix_js', 'gf_trix_settings', $settings_array );

		} // END localize_scripts

		public function requires_scripts( $form, $is_ajax ) {
			$trix_settings = GF_TRIX::get_options();

			if ( GFCommon::is_form_editor() ) {
				if ( 'on' == $trix_settings['enable_in_form_editor'] ) {
					return true;
				} else {
					return false;
				}
			} else {
				if ( is_array( $form ) ) {
					foreach ( $form['fields'] as $field ) {
						if ( $this->is_trix_editor( $field ) ) {
							return true;
						}
					}
				}
			}

			return false;
		} // END requires_scripts

		/*
         * Checks if field is CKEditor enabled
         */
		public function is_trix_editor( $field ) {
			$field_type = $field->type;
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
     * Check if Gravity Forms - Data Persistence Reloaded is installed
     */
    private function is_dpr_installed() {
        return function_exists( 'ri_gfdp_ajax' );
    } // END is_dpr_installed

		/*
     * Check if PHP version is at least 5.4
     */
    private static function is_minimum_php_version() {
			return version_compare( phpversion(), '5.4', '>=' );
    } // END is_minimum_php_version
  }
  new GFTrixEditor();
}