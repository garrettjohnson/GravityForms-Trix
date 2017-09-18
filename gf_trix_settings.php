<?php

if ( ! defined( 'ABSPATH' ) ) {
	die();
}

if ( !class_exists( 'GF_TRIX_Settings_Page' ) ) {
	class GF_TRIX_Settings_Page {

	/*
    * Settings page
    */
	 public static function settings_page(){

		   $trix_settings = GF_TRIX::get_options();
		
			$is_submit = rgpost( 'itsg_gf_wysiwyg_ckeditor_settings_submit' );

			if( $is_submit ){
			/* ENABLE IN FORM EDITOR */
				$trix_settings['enable_in_form_editor'] = rgpost( 'in_form_editor' );
				$trix_settings['enable_remove_elementspath'] = rgpost( 'remove_elementspath' );
			/* BASIC STYLES */
				$trix_settings['enable_bold'] = rgpost( 'bold' );
				$trix_settings['enable_italic'] = rgpost( 'italic' );
				$trix_settings['enable_underline'] = rgpost( 'underline' );
				$trix_settings['enable_strike'] = rgpost( 'strike' );
				$trix_settings['enable_subscript'] = rgpost( 'subscript' );
				$trix_settings['enable_superscript'] = rgpost( 'superscript' );
				$trix_settings['enable_removeformat'] = rgpost( 'removeformat' );
			/* CLIPBOARD */
				$trix_settings['enable_cut'] = rgpost( 'cut' );
				$trix_settings['enable_copy'] = rgpost( 'copy' );
				$trix_settings['enable_paste'] = rgpost( 'paste' );
				$trix_settings['enable_pastetext'] = rgpost( 'pastetext' );
				$trix_settings['enable_pastefromword'] = rgpost( 'pastefromword' );
				$trix_settings['enable_undo'] = rgpost( 'undo' );
				$trix_settings['enable_redo'] = rgpost( 'redo' );
			/* PARAGRAPH */
				$trix_settings['enable_numberedlist'] = rgpost( 'numberedlist' );
				$trix_settings['enable_bulletedlist'] = rgpost( 'bulletedlist' );
				$trix_settings['enable_outdent'] = rgpost( 'outdent' );
				$trix_settings['enable_indent'] = rgpost( 'indent' );
				$trix_settings['enable_blockquote'] = rgpost( 'blockquote' );
				$trix_settings['enable_creatediv'] = rgpost( 'creatediv' );
				$trix_settings['enable_justifyleft'] = rgpost( 'justifyleft' );
				$trix_settings['enable_justifycenter'] = rgpost( 'justifycenter' );
				$trix_settings['enable_justifyright'] = rgpost( 'justifyright' );
				$trix_settings['enable_justifyblock'] = rgpost( 'justifyblock' );
				$trix_settings['enable_bidiltr'] = rgpost( 'bidiltr' );
				$trix_settings['enable_bidirtl'] = rgpost( 'bidirtl' );
				$trix_settings['enable_language'] = rgpost( 'language' );
			/* DOCUMENT */
				$trix_settings['enable_preview'] = rgpost( 'preview' );
				$trix_settings['enable_print'] = rgpost( 'print' );
			/* EDITING */
				$trix_settings['enable_find'] = rgpost( 'find' );
				$trix_settings['enable_replace'] = rgpost( 'replace' );
				$trix_settings['enable_selectall'] = rgpost( 'selectall' );
				$trix_settings['enable_scayt'] = rgpost( 'scayt' );
				$trix_settings['setting_scayt_language'] = rgpost( 'scayt_language' );
			/* INSERT */
				$trix_settings['enable_image'] = rgpost( 'image' );
			/* IMAGE ULOAD */
				$trix_settings['enable_upload_image'] = rgpost( 'upload_image' );
				$trix_settings['setting_upload_filesize'] = rgpost( 'upload_filesize' );
				$trix_settings['setting_upload_filetype'] = rgpost( 'upload_filetype' );
				$trix_settings['setting_upload_filedir'] = rgpost( 'upload_filedir' );
				$trix_settings['setting_upload_filejpegquality'] = rgpost( 'upload_filejpegquality' );
				$trix_settings['setting_upload_filewidth'] = rgpost( 'upload_filewidth' );
				$trix_settings['setting_upload_fileheight'] = rgpost( 'upload_fileheight' );
				$trix_settings['enable_flash'] = rgpost( 'flash' );
				$trix_settings['enable_table'] = rgpost( 'table' );
				$trix_settings['enable_horizontalrule'] = rgpost( 'horizontalrule' );
				$trix_settings['enable_smiley'] = rgpost( 'smiley' );
				$trix_settings['enable_specialchar'] = rgpost( 'specialchar' );
				$trix_settings['enable_pagebreak'] = rgpost( 'pagebreak' );
				$trix_settings['enable_iframe'] = rgpost( 'iframe' );
			/* STYLES */
				$trix_settings['enable_styles'] = rgpost( 'styles' );
				$trix_settings['enable_format'] = rgpost( 'format' );
				$trix_settings['enable_font'] = rgpost( 'font' );
				$trix_settings['enable_fontsize'] = rgpost( 'fontsize' );
			/* COLOURS */
				$trix_settings['enable_textcolor'] = rgpost( 'textcolor' );
				$trix_settings['enable_bgcolor'] = rgpost( 'bgcolor' );
			/* ABOUT */
				$trix_settings['enable_about'] = rgpost( 'about' );
			/* LAYOUT */
				$trix_settings['setting_editor_height'] = rgpost( 'editor_height' );
				$trix_settings['enable_count_spaces'] = rgpost( 'count_spaces' );

				update_option( 'itsg_gf_wysiwyg_ckeditor_settings', $trix_settings );
			}

			?>

			<form method="post">
				<?php wp_nonce_field( 'update', 'itsg_gf_wysiwyg_ckeditor_update' ) ?>
				<input type="hidden" value="1" name="itsg_gf_wysiwyg_ckeditor_settings_submit" />
				<h3><?php _e( 'Trix Editor settings', 'gravity-forms-trix' ) ?></h3>
				<h4><?php _e( 'Form editor settings', 'gravity-forms-trix' ) ?></h4>
				<ul>
					<li>
						<input type="checkbox" id="in_form_editor" name="in_form_editor" <?php echo rgar( $trix_settings, 'enable_in_form_editor' ) ? "checked='checked'" : "" ?>  >
						<label for="in_form_editor"><?php _e( 'Enable in form editor', 'gravity-forms-trix' ) ?></label>
					</li>
					<li>
						<input type="checkbox" id="remove_elementspath" name="remove_elementspath" <?php echo rgar( $trix_settings, 'enable_remove_elementspath' ) ? "checked='checked'" : "" ?>  >
						<label for="remove_elementspath"><?php _e( 'Remove elements path (body p)', 'gravity-forms-trix' ) ?></label>
					</li>
				</ul>
				<h4><?php _e( 'Toolbar settings', 'gravity-forms-trix' ) ?></h4>
				<fieldset>
				<legend><?php _e( 'Basic styles', 'gravity-forms-trix' ) ?></legend>
					 <div>
						<ul>
						   <li>
							<input type="checkbox" id="bold" name="bold" <?php echo rgar( $trix_settings, 'enable_bold' ) ? "checked='checked'" : "" ?> >
							<label for="bold"><?php _e( 'Bold', 'gravity-forms-trix' ) ?></label>
						   </li>
						   <li>
							<input type="checkbox" id="italic" name="italic" <?php echo rgar( $trix_settings, 'enable_italic' ) ? "checked='checked'" : "" ?> >
							<label for="italic"><?php _e( 'Italic', 'gravity-forms-trix' ) ?></label>
						   </li>
						   <li>
							<input type="checkbox" id="underline" name="underline" <?php echo rgar( $trix_settings, 'enable_underline' ) ? "checked='checked'" : "" ?> >
							<label for="underline"><?php _e( 'Underline', 'gravity-forms-trix' ) ?></label>
						   </li>
						   <li>
							<input type="checkbox" id="strike" name="strike" <?php echo rgar( $trix_settings, 'enable_strike' ) ? "checked='checked'" : "" ?> >
							<label for="strike"><?php _e( 'Strike', 'gravity-forms-trix' ) ?></label>
						   </li>
						   <li>
							<input type="checkbox" id="subscript" name="subscript" <?php echo rgar( $trix_settings, 'enable_subscript' ) ? "checked='checked'" : "" ?> >
							<label for="subscript"><?php _e( 'Subscript', 'gravity-forms-trix' ) ?></label>
						   </li>
						   <li>
							<input type="checkbox" id="superscript" name="superscript" <?php echo rgar( $trix_settings, 'enable_superscript' ) ? "checked='checked'" : "" ?> >
							<label for="superscript"><?php _e( 'Superscript', 'gravity-forms-trix' ) ?></label>
						   </li>
						   <li>
							<input type="checkbox" id="removeformat" name="removeformat" <?php echo rgar( $trix_settings, 'enable_removeformat' ) ? "checked='checked'" : "" ?> >
							<label for="removeformat"><?php _e( 'Remove format', 'gravity-forms-trix' ) ?></label>
						   </li>
						</ul>
					 </div>
				</fieldset>
				<fieldset>
				<legend><?php _e( 'Clipboard', 'gravity-forms-trix' ) ?></legend>
					 <div>
						<ul>
						   <li>
							<input type="checkbox" id="cut" name="cut" <?php echo rgar( $trix_settings, 'enable_cut' ) ? "checked='checked'" : "" ?> >
							<label for="cut"><?php _e( 'Cut', 'gravity-forms-trix' ) ?></label>
						   </li>
						   <li>
							<input type="checkbox" id="copy" name="copy" <?php echo rgar( $trix_settings, 'enable_copy' ) ? "checked='checked'" : "" ?> >
							<label for="copy"><?php _e( 'Copy', 'gravity-forms-trix' ) ?></label>
						   </li>
						   <li>
							<input type="checkbox" id="paste" name="paste" <?php echo rgar( $trix_settings, 'enable_paste' ) ? "checked='checked'" : "" ?> >
							<label for="paste"><?php _e( 'Paste', 'gravity-forms-trix' ) ?></label>
						   </li>
						   <li>
							<input type="checkbox" id="pastetext" name="pastetext" <?php echo rgar( $trix_settings, 'enable_pastetext' ) ? "checked='checked'" : "" ?> >
							<label for="pastetext"><?php _e( 'Paste as text', 'gravity-forms-trix' ) ?></label>
						   </li>
						   <li>
							<input type="checkbox" id="pastefromword" name="pastefromword" <?php echo rgar( $trix_settings, 'enable_pastefromword' ) ? "checked='checked'" : "" ?> >
							<label for="pastefromword"><?php _e( 'Paste from word', 'gravity-forms-trix' ) ?></label>
						   </li>
						   <li>
							<input type="checkbox" id="undo" name="undo" <?php echo rgar( $trix_settings, 'enable_undo' ) ? "checked='checked'" : "" ?> >
							<label for="undo"><?php _e( 'Undo', 'gravity-forms-trix' ) ?></label>
						   </li>
						   <li>
							<input type="checkbox" id="redo" name="redo" <?php echo rgar( $trix_settings, 'enable_redo' ) ? "checked='checked'" : "" ?> >
							<label for="redo"><?php _e( 'Redo', 'gravity-forms-trix' ) ?></label>
						   </li>
						</ul>
					 </div>
				</fieldset>
				<fieldset>
				<legend><?php _e( 'Paragraph', 'gravity-forms-trix' ) ?></legend>
					 <div>
						<ul>
						   <li>
							<input type="checkbox" id="numberedlist" name="numberedlist" <?php echo rgar( $trix_settings, 'enable_numberedlist' ) ? "checked='checked'" : "" ?> >
							<label for="numberedlist"><?php _e( 'Numbered list', 'gravity-forms-trix' ) ?></label>
						   </li>
						   <li>
							<input type="checkbox" id="bulletedlist" name="bulletedlist" <?php echo rgar( $trix_settings, 'enable_bulletedlist' ) ? "checked='checked'" : "" ?> >
							<label for="bulletedlist"><?php _e( 'Bulleted list', 'gravity-forms-trix' ) ?></label>
						   </li>
						   <li>
							<input type="checkbox" id="outdent" name="outdent" <?php echo rgar( $trix_settings, 'enable_outdent' ) ? "checked='checked'" : "" ?> >
							<label for="outdent"><?php _e( 'Outdent', 'gravity-forms-trix' ) ?></label>
						   </li>
						   <li>
							<input type="checkbox" id="indent" name="indent" <?php echo rgar( $trix_settings, 'enable_indent' ) ? "checked='checked'" : "" ?> >
							<label for="indent"><?php _e( 'Indent', 'gravity-forms-trix' ) ?></label>
						   </li>
						   <li>
							<input type="checkbox" id="blockquote" name="blockquote" <?php echo rgar( $trix_settings, 'enable_blockquote' ) ? "checked='checked'" : "" ?> >
							<label for="blockquote"><?php _e( 'Block quote', 'gravity-forms-trix' ) ?></label>
						   </li>
						   <li>
							<input type="checkbox" id="creatediv" name="creatediv" <?php echo rgar( $trix_settings, 'enable_creatediv' ) ? "checked='checked'" : "" ?> >
							<label for="creatediv"><?php _e( 'Create div', 'gravity-forms-trix' ) ?></label>
						   </li>
						   <li>
							<input type="checkbox" id="justifyleft" name="justifyleft" <?php echo rgar( $trix_settings, 'enable_justifyleft' ) ? "checked='checked'" : "" ?> >
							<label for="justifyleft"><?php _e( 'Justify left', 'gravity-forms-trix' ) ?></label>
						   </li>
						   <li>
							<input type="checkbox" id="justifycenter" name="justifycenter" <?php echo rgar( $trix_settings, 'enable_justifycenter' ) ? "checked='checked'" : "" ?> >
							<label for="justifycenter"><?php _e( 'Justify center', 'gravity-forms-trix' ) ?></label>
						   </li>
						   <li>
							<input type="checkbox" id="justifyright" name="justifyright" <?php echo rgar( $trix_settings, 'enable_justifyright' ) ? "checked='checked'" : "" ?> >
							<label for="justifyright"><?php _e( 'Justify right', 'gravity-forms-trix' ) ?></label>
						   </li>
						   <li>
							<input type="checkbox" id="justifyblock" name="justifyblock" <?php echo rgar( $trix_settings, 'enable_justifyblock' ) ? "checked='checked'" : "" ?> >
							<label for="justifyblock"><?php _e( 'Justify block', 'gravity-forms-trix' ) ?></label>
						   </li>
						   <li>
							<input type="checkbox" id="bidiltr" name="bidiltr" <?php echo rgar( $trix_settings, 'enable_bidiltr' ) ? "checked='checked'" : "" ?> >
							<label for="bidiltr"><?php _e( 'Bidirectional - left to right', 'gravity-forms-trix' ) ?></label>
						   </li>
						   <li>
							<input type="checkbox" id="bidirtl" name="bidirtl" <?php echo rgar( $trix_settings, 'enable_bidirtl' ) ? "checked='checked'" : "" ?> >
							<label for="bidirtl"><?php _e( 'Bidirectional - right to left', 'gravity-forms-trix' ) ?></label>
						   </li>
						   <li>
							<input type="checkbox" id="language" name="language" <?php echo rgar( $trix_settings, 'enable_language' ) ? "checked='checked'" : "" ?> >
							<label for="language"><?php _e( 'Language', 'gravity-forms-trix' ) ?></label>
						   </li>
						</ul>
					 </div>
				</fieldset>
				<fieldset>
				<legend><?php _e( 'Document', 'gravity-forms-trix' ) ?></legend>
					 <div>
						<ul>
						   <li>
								<input type="checkbox" id="preview" name="preview" <?php echo rgar( $trix_settings, 'enable_preview' ) ? "checked='checked'" : "" ?> >
								<label for="preview"><?php _e( 'Preview', 'gravity-forms-trix' ) ?></label>
						   </li>
						   <li>
								<input type="checkbox" id="print" name="print" <?php echo rgar( $trix_settings, 'enable_print' ) ? "checked='checked'" : "" ?> >
								<label for="print"><?php _e( 'Print', 'gravity-forms-trix' ) ?></label>
							</li>
						</ul>
					 </div>
				</fieldset>
				<fieldset>
				<legend><?php _e( 'Insert', 'gravity-forms-trix' ) ?></legend>
					 <div>
						<ul>
						   <li>
								<input type="checkbox" id="image" name="image" <?php echo rgar( $trix_settings, 'enable_image' ) ? "checked='checked'" : "" ?> >
								<label for="image"><?php _e( 'Image', 'gravity-forms-trix' ) ?></label>
						   </li>
						   <li>
								<input type="checkbox" id="upload_image" name="upload_image" <?php echo rgar( $trix_settings, 'enable_upload_image' ) ? "checked='checked'" : "" ?> >
								<label for="upload_image"><?php _e( 'Image upload', 'gravity-forms-trix' ) ?></label>
						   </li>
						    <li>
							<div id="upload_image_settings">
								<p><strong><?php _e( 'Image upload settings', 'gravity-forms-trix') ?></strong></p>
								<label for="upload_filesize" style="display: block; width: 200px;font-weight: 800;"><?php _e( 'Maximum file size (MB)', 'gravity-forms-trix' ) ?></label>
								<?php
								$server_upload_limit_bytes = GF_TRIX::max_file_upload_in_bytes();
								$server_upload_limit_megabytes = $server_upload_limit_bytes / 1024 / 1024;
								?>
								<input type="number" min="0" max="<?php echo $server_upload_limit_megabytes; ?>" id="upload_filesize" name="upload_filesize" value="<?php echo rgar( $trix_settings, 'setting_upload_filesize' ) ?>" >
								<p class="instructions"><?php _e( 'This is the maximum file size that can be uploaded in megabytes (MB). Note that this cannot be larger than the maximum as defined in your servers configuration.', 'gravity-forms-trix' ) ?></p>
								<p><?php printf( __( 'Your servers maximum upload file size is currently configured as %s MB.', 'gravity-forms-trix' ),  $server_upload_limit_megabytes ) ?> </p>
								<br>
								<label for="upload_filetype" style="display: block; width: 200px;font-weight: 800;"><?php _e( "Accepted file types", 'gravity-forms-trix' ) ?></label>
								<input type="text" id="upload_filetype" name="upload_filetype" value="<?php echo rgar( $trix_settings, 'setting_upload_filetype' ) ?>" >
								<p class="instructions"><?php _e( "Specify the file types that can be uploaded. These need to be the file extension separated with the vertical bar character '|'.", 'gravity-forms-trix' ) ?></p>
								<br>
								<label for="upload_filedir" style="display: block; width: 200px;font-weight: 800;"><?php _e( 'Upload file dir', 'gravity-forms-trix' ) ?></label>
								<input style="min-width: 500px;" type="text" id="upload_filedir" name="upload_filedir" value="<?php echo rgar( $trix_settings, 'setting_upload_filedir' ) ?>" >
								<p class="instructions"><?php _e( 'This setting allows you to control where uploaded images are saved to on your server.', 'gravity-forms-trix' ) ?></p>
								<?php
								// Generate the yearly and monthly dirs
								$time            = current_time( 'mysql' );
								$y               = substr( $time, 0, 4 );
								$m               = substr( $time, 5, 2 );

								$wp_upload_dir = wp_upload_dir();
								$base_dir = $wp_upload_dir['basedir'];

								?>
								 <div>
									<p><?php _e( 'Keywords supported are:', 'gravity-forms-trix' ) ?>
										<br>{form_id} - <?php _e( 'for example', 'gravity-forms-trix' ) ?> '1'
										<br>{hashed_form_id} - <?php _e( 'for example', 'gravity-forms-trix' ) ?>  '<?php echo wp_hash(1);?>'
										<br>{user_id} - <?php _e( 'for example', 'gravity-forms-trix' ) ?>  '<?php echo get_current_user_id();?>' (<?php _e("note - if no user is logged in, this will be '0'", 'gravity-forms-trix' ) ?>)
										<br>{hashed_user_id} - <?php _e( 'for example', 'gravity-forms-trix' ) ?>  '<?php echo wp_hash(get_current_user_id() );?>' (<?php _e( 'note - if no user is logged in, this will be', 'gravity-forms-trix' ) ?> '<?php echo wp_hash(0);?>')
										<br>{year} - <?php _e( 'for example', 'gravity-forms-trix' ) ?>  '<?php echo $y;?>'
										<br>{month}	- <?php _e( 'for example', 'gravity-forms-trix' ) ?>  '<?php echo $m;?>'
									</p>
									<p><?php _e( 'If you set this field to', 'gravity-forms-trix' ) ?>
										<br><strong>/gravity_forms/{form_id}-{hashed_form_id}/{year}/{month}/</strong>
										<br><?php _e( 'Files will be uploaded to', 'gravity-forms-trix' ) ?>
										<br><strong><?php echo $base_dir . '/gravity_forms/1-' . wp_hash(1) .'/' . $y . '/' . $m ; ?></strong>
									</p>
								</div>
								<br>
								<label for="upload_filejpegquality" style="display: block; width: 200px;font-weight: 800;"><?php _e( 'Image JPEG quality', 'gravity-forms-trix' ) ?></label>
								<input type="text" id="upload_filejpegquality" name="upload_filejpegquality" value="<?php echo rgar( $trix_settings, 'setting_upload_filejpegquality' ) ?>" >
								<p class="instructions"><?php _e( 'Uploaded images are processed before being saved. The JPEG quality controls the amount of compression applied.', 'gravity-forms-trix' ) ?></p>
								<br>
								<label for="upload_filewidth" style="display: block; width: 200px;font-weight: 800;"><?php _e( "Image file width", 'gravity-forms-trix' ) ?></label>
								<input type="text" id="upload_filewidth" name="upload_filewidth" value="<?php echo rgar( $trix_settings, 'setting_upload_filewidth' ) ?>" >
								<p class="instructions"><?php _e( 'Uploaded images can be reduced in size before being saved. This setting allows you to specify the MAXIMUM width for images. If the image will only be changed if it is larger.', 'gravity-forms-trix' ) ?></p>
								<br>
								<label for="upload_fileheight" style="display: block; width: 200px;font-weight: 800;"><?php _e( 'Image file height', 'gravity-forms-trix' ) ?></label>
								<input type="text" id="upload_fileheight" name="upload_fileheight" value="<?php echo rgar( $trix_settings, 'setting_upload_fileheight' ) ?>" >
								<p class="instructions"><?php _e( 'Uploaded images can be reduced in size before being saved. This setting allows you to specify the MAXIMUM height for images. If the image will only be changed if it is larger.', 'gravity-forms-trix' ) ?></p>
							</div>
						   </li>
						   <li>
							  <input type="checkbox" id="flash" name="flash" <?php echo rgar( $trix_settings, 'enable_flash' ) ? "checked='checked'" : "" ?> >
							  <label for="flash"><?php _e( 'Flash', 'gravity-forms-trix' ) ?></label>
							</li>
							<li>
							<input type="checkbox" id="table" name="table" <?php echo rgar( $trix_settings, 'enable_table' ) ? "checked='checked'" : "" ?> >
							<label for="table"><?php _e( 'Table', 'gravity-forms-trix' ) ?></label>
						   </li>
						   <li>
							  <input type="checkbox" id="horizontalrule" name="horizontalrule" <?php echo rgar( $trix_settings, 'enable_horizontalrule' ) ? "checked='checked'" : "" ?> >
							  <label for="horizontalrule"><?php _e( 'Horizontal rule', 'gravity-forms-trix' ) ?></label>
							</li>
							<li>
							<input type="checkbox" id="smiley" name="smiley" <?php echo rgar( $trix_settings, 'enable_smiley' ) ? "checked='checked'" : "" ?> >
							<label for="smiley"><?php _e( 'Smiley', 'gravity-forms-trix' ) ?></label>
						   </li>
						   <li>
							  <input type="checkbox" id="specialchar" name="specialchar" <?php echo rgar( $trix_settings, 'enable_specialchar' ) ? "checked='checked'" : "" ?> >
							  <label for="specialchar"><?php _e( 'Special character', 'gravity-forms-trix' ) ?></label>
							</li>
							<li>
							<input type="checkbox" id="pagebreak" name="pagebreak" <?php echo rgar( $trix_settings, 'enable_pagebreak' ) ? "checked='checked'" : "" ?> >
							<label for="pagebreak"><?php _e( 'Page break', 'gravity-forms-trix' ) ?></label>
						   </li>
						   <li>
							<input type="checkbox" id="iframe" name="iframe" <?php echo rgar( $trix_settings, 'enable_iframe' ) ? "checked='checked'" : "" ?> >
							<label for="iframe"><?php _e( 'iframe', 'gravity-forms-trix' ) ?></label>
						   </li>
						</ul>
					 </div>
				</fieldset>



				<h4><?php _e( 'Form editor settings', 'gravity-forms-trix' ) ?></h4>
					<div>
						<ul>
							<li>
								<label for="editor_height" style="display: block; width: 200px;font-weight: 800;"><?php _e( 'Editor height', 'gravity-forms-trix' ) ?></label>
								<input type="text" id="editor_height" name="editor_height" value="<?php echo rgar( $trix_settings, 'setting_editor_height' ) ?>" >
								<p class="instructions"><?php _e( 'Default editor height in pixels (px). Editor can be resized by the user.', 'gravity-forms-trix' ) ?></p>
							</li>
							<li>
								<input type="checkbox" id="count_spaces" name="count_spaces" <?php echo rgar( $trix_settings, 'enable_count_spaces' ) ? "checked='checked'" : "" ?> >
								<label for="count_spaces"><?php _e( 'Count spaces as characters', 'gravity-forms-trix' ) ?></label>
						   </li>
						</ul>

					 </div>

				<input type="submit" name="save settings" value="<?php _e( 'Save Settings', 'gravity-forms-trix' ) ?>" class="button-primary" style="margin-top:40px;" />
			</form>

		   <?php

		}
	}
}