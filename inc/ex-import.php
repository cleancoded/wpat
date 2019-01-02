<?php 

/*****************************************************************/
/* IMPORT / EXPORT ADMIN PAGE */
/*****************************************************************/

if ( ! function_exists( 'wpat_export_admin_menu' ) ) :

	function wpat_export_admin_menu() {
			
		add_submenu_page(
			'tools.php',
			esc_html__( 'WPAT - Import & Export', 'wpat' ),
			esc_html__( 'WPAT Import/Export', 'wpat' ),
			'manage_options',
			'wpat-export',
			'wpat_export_page'
		);
		
	}

	add_action( 'admin_menu', 'wpat_export_admin_menu' );

	function wpat_export_page() { 
		if( ! isset($_POST['export']) ) { 
			global $message; ?>
	
			<div class="wrap">
				<h1><?php echo esc_html__( 'WPAT - Import & Export', 'wpat' ); ?></h1>

				<?php if ($message) { ?>
					<div class="updated"><p><strong><?php echo esc_html( $message ); ?></strong></p></div>
				<?php } ?>
			
				<h2><?php esc_html_e( 'Export', 'wpat' ); ?></h2>
				
				<p><?php esc_html_e( 'When you click the Export button, the system will generate a JSON file for you to save to your local computer.', 'wpat' ); ?></p>
				<p><?php esc_html_e( 'This backup file contains all WPAT configution and setting options from this WordPress installation.', 'wpat' ); ?></p>
				<p><?php esc_html_e( 'After exporting, you can either use the JSON file to restore your settings on this site again or another WordPress site.', 'wpat' ); ?></p>
				
				<form method="post">
					<?php wp_nonce_field('wpat-export'); ?>
					<input class="button" type="submit" name="export" value="<?php esc_html_e( 'Export WPAT options', 'wpat' ); ?>">
				</form>
					
				<h2><?php esc_html_e( 'Import', 'wpat' ); ?></h2>
				<?php
					if( isset( $_FILES['import'] ) && check_admin_referer('wpat-import') ) {
						if( $_FILES['import']['error'] > 0 ) {
							
							echo '<div class="error"><p><strong>' . esc_html__( 'An error occurred.', 'wpat' ) . '</strong></p></div>';
							wp_die();
							
						} else {
							
							$file_name = $_FILES['import']['name']; // Get the name of file
							$file_ext = strtolower(end(explode(".", $file_name))); // Get extension of file
							$file_size = $_FILES['import']['size']; // Get size of file
							
							// Ensure uploaded file is JSON file type and the size not over 500000 bytes
							if( ($file_ext == "json") && ($file_size < 500000) ) {
								$encode_options = file_get_contents($_FILES['import']['tmp_name']);
								$options = json_decode($encode_options, true);
								foreach ($options as $key => $value) {
									update_option($key, $value);
								}
								$message = esc_html__( 'All options have been restored successfully.', 'wpat' );
							} else {
								$message = esc_html__( 'Invalid file or file size too big.', 'wpat' );
							}
							
						}
					}
				?>
				
				<p><?php esc_html_e( 'Click the Browse button and choose a JSON file.', 'wpat' ); ?></p>
				<p><?php esc_html_e( 'Press the Import button to restore all saved options.', 'wpat' ); ?></p>
				
				<form method="post" enctype="multipart/form-data">
					<p class="submit">
						<?php wp_nonce_field('wpat-import'); ?>
						<input type="file" name="import" />
						<input class="button button-primary" type="submit" name="submit" value="<?php esc_html_e( 'Import options', 'wpat' ); ?>">
					</p>
				</form>		

			</div>
		
		<?php } elseif( check_admin_referer('wpat-export') ) {
			
			$blogname = str_replace(" ", "", get_option('blogname'));
			$date = date("m-d-Y");
			$json_name = $blogname."-".$date; // Namming the filename will be generated.

			//$options = get_alloptions(); // Get all options data, return array
			$options = array( 'wpat_settings_options' => get_option('wpat_settings_options') ); // Get specific options data
			//$options = array( 'test' => get_option('test'), 'test2' => get_option('test2') ); // Get specific options data
			
			foreach ($options as $key => $value) {
				$value = maybe_unserialize($value);
				$need_options[$key] = $value;
			}

			$json_file = json_encode( $need_options ); // Encode data for json data

			ob_clean();
			echo $json_file;
			header( "Content-Type: text/json; charset=" . get_option('blog_charset') );
			header( "Content-Disposition: attachment; filename=$json_name.json" );
			exit();
			
		}
			
	}

endif;