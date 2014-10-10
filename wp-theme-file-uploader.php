<?php /*
Plugin Name: WP Theme File Uploader
Plugin URI: http://gabrielcastillo.net/wp-theme-uploader
Description: Upload theme files and assets directly to you active wordpress theme. This plugin was built for quickly added updated theme files with out having to ftp into the server. Supported files are: '.php', '.png', '.css', '.min.css', '.js', '.min.js', '.jpg', '.html', 'htm', '.xml', '.pdf'. These file will be added or overwrite to the active theme within your wordpress site.
Version: 1.2
Author: Gabriel Castillo
Author URI: http://gabrielcastillo.net/
*/

/**
 * Copyright (c) `date "+%Y"` Your Name. All rights reserved.
 *
 * Released under the GPL license
 * http://www.opensource.org/licenses/gpl-license.php
 *
 * This is an add-on for WordPress
 * http://wordpress.org/
 *
 * **********************************************************************
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * **********************************************************************
 */

	
	global $wp_version;

	if( !version_compare($wp_version, '3.1', '>=')){
		die('Wordpress Version 3.1 or greater is required for this plugin to word');
	}

	function add_admin_styles()
	{
		wp_enqueue_style('wp_theme_file_uploader_css', plugins_url('css/admin_style.css',__FILE__), array(), false);
	}

	add_action('admin_init', 'add_admin_styles');

	add_action('admin_menu', 'wp_theme_file_upload');

	function wp_theme_file_upload()
	{
		add_options_page('Theme File Uploader', 'File Uploader', 'manage_options', 'theme_file_uploader', 'wp_theme_file_upload_options');
	}


	function select_file_notice()
	{
		$notice = '<div class="error"><p>Please select file to upload!</p></div>';
		return $notice;
	}

	add_action('admin_notices', 'select_file_notice');


	function select_file_type_notice()
	{
		$notice = '<div class="error"><p>This file type is not allowed!</p></div>';
		return $notice;
	}

	add_action('admin_notices', 'select_file_type_notice');


	function select_file_fail_notice()
	{
		$notice = '<div class="error"><p>Upload Failed. Please Check Your File Type!</p></div>';
		return $notice;
	}

	add_action('admin_notices', 'select_file_fail_notice');


	function select_file_success_notice($file)
	{
		$notice = '<div class="updated"><p>'.$file.' has been uploaded!</p></div>';
		return $notice;
	}

	add_action('admin_notices', 'select_file_success_notice');


	function wp_theme_file_upload_options()
	{	
		if(isset($_FILES['file'])){
			$file = $_FILES['file']['name'];
			$upload_path =  get_stylesheet_directory() . '/';
			
			$allowed_filetypes = array('.php', '.png', '.css', '.min.css', '.js', '.min.js', '.jpg', '.html', '.htm', '.xml', '.pdf');
			$ext = substr($file, strpos($file,'.'), strlen($file)-1);
			if(!in_array($ext,$allowed_filetypes)){
				echo select_file_type_notice();
			}else{
				$file = str_replace(' ', '_', $file);
				$file = strtolower($file);
				if($_POST['folder'] != '/'){
					$folder_path = $_POST['folder'] . '/';
					if(move_uploaded_file($_FILES['file']['tmp_name'],$upload_path.$folder_path.$file)){
						echo select_file_success_notice($file);
					}else{
						echo select_file_fail_notice();
					}
				}else{
					if(move_uploaded_file($_FILES['file']['tmp_name'],$upload_path.$file)){
						echo select_file_success_notice($file);
					}else{
						echo select_file_fail_notice();
					}
				}
				
			}
		}

		$directory = get_stylesheet_directory() . '/';
		$files = glob($directory . "*");

		global $select_options; if ( ! isset( $_REQUEST['settings-updated'] ) ) $_REQUEST['settings-updated'] = false;


		?>
		<div class="wrap">
			<?php screen_icon(); echo "<h2>". __( 'WP Theme File Uploader', 'wp_theme_file_uploader' ) . "</h2>"; ?>
			<?php if ( false !== $_REQUEST['settings-updated'] ) : ?>
			<p><strong><?php _e( 'Options saved', 'wp_theme_file_uploader' ); ?></strong></p>
			<?php endif; ?> 
			<form method="post" action="<?php $_SERVER['PHP_SELF']; ?>" enctype="multipart/form-data">
				<?php settings_fields( 'wp_theme_file_uploader_options' ); ?>  
				<?php $options = get_option( 'wp_theme_file_uploader_options' ); ?>
				<div id="wp-theme-file-uploader">
					<p>
						Upload files to your active theme folder or theme subfolders. This plugin will allow you to quickly add template files, images, css files with out having to use FTP or any other uploading program.
					</p>
					<p>
					<label for="folders">Select Folder:</label>
					<select name="folder">
						<option value="/"><?php echo wp_get_theme(); ?></option>
						<?php foreach($files as $file): ?>
							<?php if(is_dir($file)): ?>
								<option value="<?php echo basename($file); ?>"><?php echo basename($file); ?></option>
							<?php endif; ?>
						<?php endforeach; ?>
					</select>
					<span class="wp_theme_file_uploader_helper">" / " is the active theme directory</span>
					</p>
					<p>
						<label for="file">Select File:</label>
						<input class="upload" type="file" name="file" id="file"><br>
						<span class="wp_theme_file_uploader_helper">This will overwrite any files.</span>
					</p>
					<input class="button-primary" type="submit" name="submit" value="Upload">
				</div>
				<span>Developed By: <a href="http://gabrielcastillo.net/" title="Gabriel Castillo" target="_blank">Gabriel Castillo</a></span>
			</form>
		</div>
		<p>If you like this plugin, write a review or some feed back. <a href="https://wordpress.org/support/view/plugin-reviews/wp-theme-file-uploader">here</a>.</p>
			<p style="margin-top:100px;">
				<p>Buy me a coffee or support this plugin.</p>
				<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
					<input type="hidden" name="cmd" value="_s-xclick">
					<input type="hidden" name="hosted_button_id" value="QWQR38AG9MX9Q">
					<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
					<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
				</form>

			</p>
	<?php
	}
		
 ?>