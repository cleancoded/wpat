<?php 

/*****************************************************************/
/* WP OPTIMIZATION TIPS ADMIN PAGE */
/*****************************************************************/

if ( ! function_exists( 'wpat_wp_optimize_tips_admin_menu' ) ) :

	function wpat_wp_optimize_tips_admin_menu() {
			
		add_submenu_page(
			'tools.php',
			esc_html__( 'WPAT - Optimization Tips', 'wpat' ),
			esc_html__( 'WPAT Optimization', 'wpat' ),
			'manage_options',
			'wpat_optimize_tips',
			'wpat_optimize_tips_page'
		);
		
	}

	add_action( 'admin_menu', 'wpat_wp_optimize_tips_admin_menu' );

	function wpat_optimize_tips_page() { 
        global $wpdb; 
        $common = new WPAT_Sys_info(); 

        $help = '<span class="dashicons dashicons-editor-help"></span>';
        $solved = '<span class="sys-status enable"><span class="dashicons dashicons-yes"></span> ' . esc_html__( 'Solved', 'wpat' ) . '</span>';
        $unsolved = '<span class="sys-status disable"><span class="dashicons dashicons-no"></span> ' . esc_html__( 'Unsolved', 'wpat' ) . '</span>';
        $yes = '<span class="sys-status enable"><span class="dashicons dashicons-yes"></span> ' . esc_html__( 'Yes', 'wpat' ) . '</span>';
        $no = '<span class="sys-status disable"><span class="dashicons dashicons-no"></span> ' . esc_html__( 'No', 'wpat' ) . '</span>';
        $entered = '<span class="sys-status enable"><span class="dashicons dashicons-yes"></span> ' . esc_html__( 'Defined', 'wpat' ) . '</span>';
        $not_entered = '<span class="sys-status disable"><span class="dashicons dashicons-no"></span> ' . esc_html__( 'Not defined', 'wpat' ) . '</span>';
        $sec_key = '<span class="error"><span class="dashicons dashicons-warning"></span> ' . esc_html__( 'Please enter this security key in the wp-confiq.php file', 'wpat' ) . '!</span>'; ?>
	
        <div class="wrap">
            <h1><?php echo esc_html__( 'WPAT - WP Optimization Tips', 'wpat' ); ?></h1>

            <h2><?php echo esc_html__( 'Speed up your WordPress Admin Area', 'wpat' ); ?></h2>
            
            <p><?php echo __( 'Follow these optimization tips to get your WP admin screens loading faster. Sometimes page load times are very slow.', 'wpat' ); ?>.</p>
            
            <table class="wp-list-table widefat fixed striped posts">
                <thead>
                    <tr>
                        <th width="20%" class="manage-column"><?php esc_html_e( 'Info', 'wpat' ); ?></th>
                        <th width="10%" class="manage-column"><?php esc_html_e( 'Status', 'wpat' ); ?></th>
                        <th class="manage-column"><?php esc_html_e( 'Tipp', 'wpat' ); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><?php esc_html_e( 'WP Version', 'wpat' ); ?>:</td>
                        <td>
                            <?php if( $test ) {
                                echo $unsolved;
                            } else {
                                echo $solved;
                            } ?>
                        </td>
                        <td><strong><?php bloginfo('version'); ?></strong></td>
                    </tr>
                    <tr>
                        <td><?php esc_html_e('PHP Version', 'wpat'); ?>:</td>
                        <td>
                            <?php if( $test ) {
                                echo $unsolved;
                            } else {
                                echo $solved;
                            } ?>
                        </td>
                        <td><?php echo $common->getPhpVersion(); ?></td>
                    </tr>
                    <tr>
                        <td><?php esc_html_e( 'MySQL Version', 'wpat' ); ?>:</td>
                        <td>
                            <?php if( $test ) {
                                echo $unsolved;
                            } else {
                                echo $solved;
                            } ?>
                        </td>
                        <td><?php echo $common->getMySQLVersion(); ?></td>
                    </tr>
                    <tr>
                        <td><?php esc_html_e( 'PHP Memory WP-Limit', 'wpat' ); ?>:</td>
                        <td>
                            <?php if( $test ) {
                                echo $unsolved;
                            } else {
                                echo $solved;
                            } ?>
                        </td>
                        <td><?php
                            $memory = $common->memory_size_convert( WP_MEMORY_LIMIT );

                            if ($memory < 67108864) {
                                echo '<span class="warning"><span class="dashicons dashicons-warning"></span> ' . sprintf(__('%s - For better performance, we recommend setting memory to at least 64MB. See: %s', 'wpat'), size_format($memory), '<a href="https://codex.wordpress.org/Editing_wp-config.php#Increasing_memory_allocated_to_PHP" target="_blank">' . __('Increasing memory allocated to PHP', 'wpat') . '</a>') . '</span>';
                            } else {
                                echo '<strong>' . size_format($memory) . '</strong>';
                            }
                            ?> 
                        </td>
                    </tr>
                    <tr>
                        <td><?php esc_html_e( 'PHP Memory Server-Limit', 'wpat' ); ?>:</td>
                        <td>
                            <?php if( $test ) {
                                echo $unsolved;
                            } else {
                                echo $solved;
                            } ?>
                        </td>
                        <td>
                            <?php
                            if (function_exists('memory_get_usage')) {
                                $system_memory = $common->memory_size_convert(@ini_get('memory_limit'));
                                $memory = max($memory, $system_memory);
                            }

                            if ($memory < 67108864) {
                                echo '<span class="warning"><span class="dashicons dashicons-warning"></span> ' . sprintf(__('%s - For better performance, we recommend setting memory to at least 64MB. See: %s', 'wpat'), size_format($memory), '<a href="https://codex.wordpress.org/Editing_wp-config.php#Increasing_memory_allocated_to_PHP" target="_blank">' . __('Increasing memory allocated to PHP', 'wpat') . '</a>') . '</span>';
                            } else {
                                echo '<strong>' . size_format($memory) . '</strong>';
                            }
                            ?> 
                        </td>
                    </tr>
                    <tr>
                        <td><?php esc_html_e( 'PHP Memory WP-Usage', 'wpat' ); ?>:</td>
                        <td>
                            <?php if( $test ) {
                                echo $unsolved;
                            } else {
                                echo $solved;
                            } ?>
                        </td>
                        <td>
                            <?php if( $common->wp_memory_usage() == '-1' ) {
                                echo $common->wp_memory_usage()['MemUsed'] . ' MB of -1 / ' . esc_html__( 'Unlimited', 'wpat' );
                            } else { ?>
                                <div class="status-progressbar"><span><?php echo $common->wp_memory_usage()['MemUsage'] . '% '; ?></span><div style="width: <?php echo $common->wp_memory_usage()['MemUsage']; ?>%"></div></div>
                                <?php echo ' ' . $common->wp_memory_usage()['MemUsed'] . ' MB of ' . (int)WP_MEMORY_LIMIT . ' MB'; ?>
                            <?php } ?>
                        </td>
                    </tr>
                    <tr>
                        <td>Post Revisions</td>
                        <td>
                            <?php if( $test ) {
                                echo $unsolved;
                            } else {
                                echo $solved;
                            } ?>
                        </td>
                        <td>slow</td>
                    </tr>
                    <tr>
                        <td>Spam / Trash Comments</td>
                        <td>
                            <?php if( $test ) {
                                echo $unsolved;
                            } else {
                                echo $solved;
                            } ?>
                        </td>
                        <td>slow</td>
                    </tr>
                    <tr>
                        <td>Debug Modes</td>
                        <td>
                            <?php if( $test ) {
                                echo $unsolved;
                            } else {
                                echo $solved;
                            } ?>
                        </td>
                        <td>WordPress Debug Mode is activate. This is slow up your backend. If you don't need it, while developing on your website, you can disable WordPress Debug Mode in the wp-confiq.php file ...</td>
                    </tr>
                    <tr>
                        <td>Admin Speed (Compress)</td>
                        <td>
                            <?php if( $test ) {
                                echo $unsolved;
                            } else {
                                echo $solved;
                            } ?>
                        </td>
                        <td>slow</td>
                    </tr>
                    <tr>
                        <td>Emoji</td>
                        <td>
                            <?php if( $test ) {
                                echo $unsolved;
                            } else {
                                echo $solved;
                            } ?>
                        </td>
                        <td>WordPress is loading the Emoji scripts. This is slow up your backend. If you don't need it, you can disable WordPress Emojis at ...</td>
                    </tr>
                </tbody>
            </table>
            
            <br><br>
            
        </div>

    <?php }

endif;