<?php 

/*****************************************************************/
/* MULTISITE - SET OPTIONS FOR ALL NETWORK BLOGS */
/*****************************************************************/

// add multisite options update

if ( is_multisite() ) {

	if ( ! function_exists( 'wpat_update_blog' ) ) :

		function wpat_update_blog( $blog_id = null ) {
			
			if ( $blog_id ) {
				switch_to_blog( $blog_id );
			}

			// get options from main blog (ID = 1)
			$blog_id = 1;
			$options = get_blog_option( $blog_id, 'wpat_settings_options', array() );

			// manage options
			// options from all network sites = options from blog ID 1
            
            // get option fields
            $init_wp_options = new wpat_Options;
            $get_all_fields = $init_wp_options->option_fields;
        
            foreach( $get_all_fields as $key => $value ) {
                $options[$key] = $options[$key];
            }

			// update options
			update_option('wpat_settings_options', $options);

			if ( $blog_id ) {
				restore_current_blog();
			}
		}

	endif;
	
}

/*****************************************************************/
/* MULTISITE ADMIN PAGE */
/*****************************************************************/

// create multisite update admin page

if ( is_multisite() ) {

	if ( ! function_exists( 'wpat_update_admin_menu' ) ) :

		function wpat_update_admin_menu() {

			global $blog_id;
			$options = get_blog_option( $blog_id, 'wpat_settings_options' );

			// hide this page, if "disable theme options" is true
			// the page will be displayed for blog ID 1 only
			if( ! empty( $options['disable_theme_options'] ) && $blog_id == 1 || $blog_id == 1 ) {

				add_submenu_page(
					'tools.php',
					esc_html__( 'WPAT - Multisite Update', 'wpat' ),
					esc_html__( 'WPAT Multisite', 'wpat' ),
					'manage_network',
					'wpat-update-network',
					'wpat_update_page'
				);

			}

		}

		add_action('admin_menu', 'wpat_update_admin_menu');

	endif;
	
}

if ( is_multisite() ) {

	if ( ! function_exists( 'wpat_update_page' ) ) :

		function wpat_update_page() {

			global $wpdb, $message;

			// update this blog
			if ( ! empty( $_POST['update_current_blog'] ) ) {

				/*wpat_update_blog();
				$message = esc_html__( 'This blog has been updated.', 'wpat' );*/

			// update all network blogs
			} elseif ( ! empty( $_POST['update_all_blogs'] ) ) {

				$blogs = $wpdb->get_results("
					SELECT blog_id
					FROM {$wpdb->blogs}
					WHERE site_id = '{$wpdb->siteid}'
					AND archived = '0'
					AND spam = '0'
					AND deleted = '0'
				");

				foreach ( $blogs as $blog ) {
					wpat_update_blog( $blog->blog_id );
				}

				$message = esc_html__( 'All network sites has been updated.', 'wpat' );

			} 

			// get option values from blog ID 1			
			$blog_id = 1;
			$blog_name = get_blog_option( $blog_id, 'blogname' );
			$options = get_blog_option( $blog_id, 'wpat_settings_options' ); ?>

			<div class="wrap">
				<h1><?php echo esc_html__( 'WPAT - Multisite Update', 'wpat' ); ?></h1>

				<?php if ($message) { ?>
					<div class="updated"><p><strong><?php echo esc_html( $message ); ?></strong></p></div>
				<?php } ?>

				<p><strong><?php echo esc_html__( 'Update your WPAT options for all network websites together.', 'wpat' ); ?></strong></p>

				<h2><?php echo esc_html__( 'Share Options', 'wpat' ); ?></h2>

				<p><?php echo esc_html__( 'You will share the following options from Blog ID', 'wpat' ); ?>: <strong><?php echo esc_html( $blog_id ); ?></strong> / <?php echo esc_html__( 'Blog Name', 'wpat' ); ?>: <strong><?php echo esc_html( $blog_name ); ?></strong> <?php echo esc_html__( 'for all network blogs', 'wpat' ); ?>.</p>

				<table class="wp-list-table widefat fixed striped posts">
					<thead>
						<tr>
							<th style="width: 20%" class="manage-column"><?php echo esc_html__( 'Option', 'wpat' ); ?></th>
							<th class="manage-column"><?php echo esc_html__( 'Value', 'wpat' ); ?></th>
						</tr>
					</thead>
					<tbody>
					    <?php $is_visible = '<span class="sys-status enable"><span class="dashicons dashicons-yes"></span> ' . esc_html__( 'Visible', 'wpat' ) . '</span>';
						$is_hidden = '<span class="sys-status disable"><span class="dashicons dashicons-no"></span> ' . esc_html__( 'Hidden', 'wpat' ) . '</span>';
						$is_enabled = '<span class="sys-status enable"><span class="dashicons dashicons-yes"></span> ' . esc_html__( 'Enabled', 'wpat' ) . '</span>';
						$is_disabled = '<span class="sys-status disable"><span class="dashicons dashicons-no"></span> ' . esc_html__( 'Disabled', 'wpat' ) . '</span>';
						$is_none = '<span class="sys-status disable"><span class="dashicons dashicons-no"></span> ' . esc_html__( 'Not selected', 'wpat' ) . '</span>';
						$is_not_added = '<span class="sys-status disable"><span class="dashicons dashicons-no"></span> ' . esc_html__( 'Not added', 'wpat' ) . '</span>';
						$is_activate = '<span class="sys-status enable"><span class="dashicons dashicons-yes"></span> ' . esc_html__( 'Activated', 'wpat' ) . '</span>';
						$is_deactivate = '<span class="sys-status disable"><span class="dashicons dashicons-no"></span> ' . esc_html__( 'Deactivated', 'wpat' ) . '</span>';

                        // get all options
                        $options = get_option('wpat_settings_options'); // Get specific options data
            
                        // get all option labels
                        $init_wp_options = new wpat_Options;
                        $get_all_fields = $init_wp_options->option_fields;
                        $label = $get_all_fields;            
            
                        foreach( $options as $key => $value ) {                            
                            
                            if( $key == 'company_box_logo' ) {
                                
                                if( empty( $value ) ) $status = $is_not_added;
                                else $status = $options['company_box_logo'];
                                echo '<tr><td>' . $label[ $key ] . ':</td><td>' . $status . '</td></tr>';
                            
                            } elseif( $key == 'company_box_logo_size' ) {
                                
                                if( empty( $value ) ) $status = '140';
                                else $status = $options['company_box_logo_size'];
                                echo '<tr><td>' . $label[ $key ] . ':</td><td>' . $status . ' ' . esc_html__( 'Pixel', 'wpat' ) . '</td></tr>';
                                
                            } elseif( $key == 'meta_referrer_policy' ) {
                                
                                if( $value == 'none' ) $status = $is_disabled;
                                else $status = $options['meta_referrer_policy'];
                                echo '<tr><td>' . $label[ $key ] . ':</td><td>' . $status . '</td></tr>';
                                
                            } elseif( $key == 'spacing' ) {
                                
                                if( empty( $value ) ) $status = $is_enabled;
                                else $status = $is_disabled;
                                echo '<tr><td>' . $label[ $key ] . ':</td><td>' . $status . '</td></tr>';
                                
                            } elseif( $key == 'spacing_max_width' ) {
                                
                                if( empty( $value ) ) $status = '2000';
                                else $status = $options['spacing_max_width'];
                                echo '<tr><td>' . $label[ $key ] . ':</td><td>' . $status . ' ' . esc_html__( 'Pixel', 'wpat' ) . '</td></tr>';
                                
                            } elseif( $key == 'left_menu_expand' ) {
                                
                                if( empty( $value ) ) $status = $is_deactivate;
                                else $status = $is_activate;
                                echo '<tr><td>' . $label[ $key ] . ':</td><td>' . $status . '</td></tr>';
                                
                            } elseif( $key == 'google_webfont' ) {
                                
                                if( empty( $value ) ) $status = $is_not_added;
                                else $status = $options['google_webfont'];
                                echo '<tr><td>' . $label[ $key ] . ':</td><td>' . $status . '</td></tr>';
                                
                            } elseif( $key == 'google_webfont_weight' ) {
                                
                                if( empty( $value ) ) $status = $is_not_added;
                                else $status = $options['google_webfont_weight'];
                                echo '<tr><td>' . $label[ $key ] . ':</td><td>' . $status . '</td></tr>';
                                
                            } elseif( $key == 'toolbar_icon' ) {
                                
                                if( empty( $value ) ) $status = $is_not_added;
                                else $status = $options['toolbar_icon'];
                                echo '<tr><td>' . $label[ $key ] . ':</td><td>' . $status . '</td></tr>';
                                
                            } elseif( $key == 'theme_color' ) {
                                
                                if( empty( $value ) ) $status = '#4777CD';
                                else $status = $options['theme_color'];
                                echo '<tr><td>' . $label[ $key ] . ':</td><td>' . esc_html( $status ) . ' <span style="display:inline-block;width:10px;height:10px;background-color:' . esc_html( $status ) . '"></span></td></tr>'; 
                                
                            } elseif( $key == 'theme_background' ) {
                                
                                if( empty( $value ) ) $status = '#545c63';
                                else $status = $options['theme_background'];
                                echo '<tr><td>' . $label[ $key ] . ':</td><td>' . esc_html( $status ) . ' <span style="display:inline-block;width:10px;height:10px;background-color:' . esc_html( $status ) . '"></span></td></tr>'; 
                                
                            } elseif( $key == 'theme_background_end' ) {
                                
                                if( empty( $value ) ) $status = '#32373c';
                                else $status = $options['theme_background_end'];
                                echo '<tr><td>' . $label[ $key ] . ':</td><td>' . esc_html( $status ) . ' <span style="display:inline-block;width:10px;height:10px;background-color:' . esc_html( $status ) . '"></span></td></tr>'; 
                                
                            } elseif( $key == 'login_title' ) {
                                
                                if( empty( $value ) ) $status = esc_html__( 'Welcome Back.', 'wpat' );
                                else $status = $options['login_title'];
                                echo '<tr><td>' . $label[ $key ] . ':</td><td>' . $status . '</td></tr>'; 
                                
                            } elseif( $key == 'logo_upload' ) {
                                
                                if( empty( $value ) ) $status = $is_none;
                                else $status = $options['logo_upload'];
                                echo '<tr><td>' . $label[ $key ] . ':</td><td>' . $status . '</td></tr>'; 
                                
                            } elseif( $key == 'logo_size' ) {
                                
                                if( empty( $value ) ) $status = '200';
                                else $status = $options['logo_size'];
                                echo '<tr><td>' . $label[ $key ] . ':</td><td>' . $status . ' ' . esc_html__( 'Pixel', 'wpat' ) . '</td></tr>';
                                
                            } elseif( $key == 'login_bg' ) {
                                
                                if( empty( $value ) ) $status = $is_none;
                                else $status = $options['login_bg'];
                                echo '<tr><td>' . $label[ $key ] . ':</td><td>' . $status . '</td></tr>';
                                
                            } elseif( $key == 'css_admin' ) {
                                
                                if( empty( $value ) ) $status = $is_not_added;
                                else $status = $options['css_admin'];
                                $textarea_start = '<textarea class="option-textarea" readonly>';
						        $textarea_end = '</textarea>';
                                echo '<tr><td>' . $label[ $key ] . ':</td><td>' . $textarea_start . wp_kses( $status, array() ) . $textarea_end . '</td></tr>';
                                
                            } elseif( $key == 'css_login' ) {
                                
                                if( empty( $value ) ) $status = $is_not_added;
                                else $status = $options['css_login'];
                                $textarea_start = '<textarea class="option-textarea" readonly>';
						        $textarea_end = '</textarea>';
                                echo '<tr><td>' . $label[ $key ] . ':</td><td>' . $textarea_start . wp_kses( $status, array() ) . $textarea_end . '</td></tr>';
                                
                            } elseif( $key == 'wp_header_code' ) {
                                
                                if( empty( $value ) ) $status = esc_html__( 'Not added', 'wpat' );
                                else $status = $options['wp_header_code'];
                                $textarea_start = '<textarea class="option-textarea" readonly>';
						        $textarea_end = '</textarea>';
                                echo '<tr><td>' . $label[ $key ] . ':</td><td>' . $textarea_start . $status . $textarea_end . '</td></tr>';
                                
                            } elseif( $key == 'wp_footer_code' ) {
                                
                                if( empty( $value ) ) $status = esc_html__( 'Not added', 'wpat' );
                                else $status = $options['wp_footer_code'];
                                $textarea_start = '<textarea class="option-textarea" readonly>';
						        $textarea_end = '</textarea>';
                                echo '<tr><td>' . $label[ $key ] . ':</td><td>' . $textarea_start . $status . $textarea_end . '</td></tr>';
                                
                            } elseif( $key == 'login_disable' || $key == 'wp_svg' || $key == 'wp_ico' ) {
                                
                                if( empty( $value ) ) $status = $is_deactivate;
                                else $status = $is_activate;
                                echo '<tr><td>' . $label[ $key ] . ':</td><td>' . $status . '</td></tr>'; 
                                
                            } elseif( $key == 'disable_page_system' || $key == 'disable_page_export' || $key == 'disable_page_ms' || $key == 'disable_theme_options' || $key == 'wp_version_tag' || $key == 'wp_emoji' || $key == 'wp_feed_links' || $key == 'wp_rsd_link' || $key == 'wp_wlwmanifest' || $key == 'wp_shortlink' || $key == 'wp_rest_api' || $key == 'wp_oembed' || $key == 'wp_xml_rpc' || $key == 'wp_heartbeat' || $key == 'wp_rel_link' || $key == 'wp_self_pingback' || $key == 'mb_custom_fields' || $key == 'mb_commentstatus' || $key == 'mb_comments' || $key == 'mb_author' || $key == 'mb_category' || $key == 'mb_format' || $key == 'mb_pageparent' || $key == 'mb_postexcerpt' || $key == 'mb_postimage' || $key == 'mb_revisions' || $key == 'mb_slug' || $key == 'mb_tags' || $key == 'mb_trackbacks' || $key == 'dbw_quick_press' || $key == 'dbw_right_now' || $key == 'dbw_activity' || $key == 'dbw_primary' || $key == 'dbw_welcome' || $key == 'dbw_wpat_user_log' || $key == 'dbw_wpat_sys_info' || $key == 'dbw_wpat_count_post' || $key == 'dbw_wpat_count_page' || $key == 'dbw_wpat_count_comment' || $key == 'dbw_wpat_recent_post' || $key == 'dbw_wpat_recent_page' || $key == 'dbw_wpat_recent_comment' || $key == 'dbw_wpat_memory' || $key == 'wt_pages' || $key == 'wt_calendar' || $key == 'wt_archives' || $key == 'wt_meta' || $key == 'wt_search' || $key == 'wt_text' || $key == 'wt_categories' || $key == 'wt_recent_posts' || $key == 'wt_recent_comments' || $key == 'wt_rss' || $key == 'wt_tag_cloud' || $key == 'wt_nav' || $key == 'wt_image' || $key == 'wt_audio' || $key == 'wt_video' || $key == 'wt_gallery' || $key == 'wt_html' ) {
                                
                                if( empty( $value ) ) $status = $is_activate;
                                else $status = $is_deactivate;
                                echo '<tr><td>' . $label[ $key ] . ':</td><td>' . $status . '</td></tr>'; 
                                
                            } else {
                                
                                if( empty( $value ) ) $status = $is_visible;
                                else $status = $is_hidden;     
                                echo '<tr><td>' . $label[ $key ] . ':</td><td>' . $status . '</td></tr>';
                            }
                            
                        } ?>
					</tbody>
				</table>        

				<form action="" method="post">

					<p style="margin-top: 40px">
						<?php echo esc_html__( 'You will update the following network sites', 'wpat' ) . ':'; ?>
					</p>

					<p>
						<?php $subsites = get_sites();
						foreach( $subsites as $subsite ) {
							$subsite_id = get_object_vars( $subsite )['blog_id'];
							$subsite_name = get_blog_details( $subsite_id )->blogname;
							echo esc_html__( 'Blog Name', 'wpat' ) . ': <strong>'. $subsite_name . '</strong> (' . esc_html__( 'ID', 'wpat' ) . ': ' . $subsite_id . ')<br/>';
						} ?>
					</p>

					<p class="submit">      
						<?php /*<input type="submit" name="update_current_blog" class="button" value="<?php esc_attr__( 'Update This Blog', 'wpat' ); ?>" /> */ ?>
						<input type="submit" name="update_all_blogs" class="button-primary" value="<?php esc_attr_e( 'Update all network blogs', 'wpat' ); ?>" onclick="return confirm('<?php esc_html_e( 'Are you sure you want to run the update for all blogs?', 'wpat' ); ?>');" />
					</p>
				</form>

			</div>

		<?php }

	endif;

}