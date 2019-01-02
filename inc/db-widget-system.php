<?php 

/*****************************************************************/
/* WP SYSTEM DASHBOARD WIDGET */
/*****************************************************************/

// WP SYSTEM WIDGET --> CALLABLE WIDGET CONTENT

if( ! function_exists('wp_admin_system_dashboard_widget_content') ) :

    function wp_admin_system_dashboard_widget_content() { 

        $common = new WPAT_Sys_info(); ?>

        <style>
            .sys-info ul {margin:0px -15px -15px -15px}
            .sys-info li:nth-child(even) {background:#f8f9fb}
            .sys-info li {margin:0px;padding:10px 15px;border-bottom:1px solid #eee}
            .sys-info li:first-child {padding-top:0px}
            .sys-info li:last-child {border:0px}
            .sys-info .status-progressbar {width:100%;margin-top:5px}
        </style>

        <div class="sys-info">         
            <div class="table listing">
                <ul>
                    <li>
                        <?php esc_html_e( 'WP Version', 'wpat' ); ?>:
                        <strong><?php bloginfo('version'); ?></strong>
                    </li>
                    <li>
                        <?php esc_html_e('PHP Version', 'wpat'); ?>:
                        <strong><?php echo $common->getPhpVersionLite(); ?></strong>
                    </li>
                    <li>
                        <?php esc_html_e( 'MySQL Version', 'wpat' ); ?>:
                        <strong><?php echo $common->getMySQLVersionLite(); ?></strong>
                    </li>
                    <li>
                        <?php esc_html_e( 'PHP Memory Server-Limit', 'wpat' ); ?>: 
                        <?php echo '<strong>' . $common->server_memory_usage()['MemLimitFormat'] . '</strong>'; ?> 
                    </li>
                    <li>
                        <?php esc_html_e( 'PHP Memory Server-Usage', 'wpat' ); ?>: 
                        <?php if( $common->server_memory_usage()['MemLimitGet'] == '-1' ) { ?>
                            <strong><?php echo $common->server_memory_usage()['MemUsageFormat'] . ' ' . esc_html__( 'of', 'wpat' ) . ' ' . esc_html__( 'Unlimited', 'wpat' ) . ' (-1)'; ?></strong>
                        <?php } else { ?>
                            <strong><?php echo $common->server_memory_usage()['MemUsageFormat'] . ' ' . esc_html__( 'of', 'wpat' ) . ' ' . $common->server_memory_usage()['MemLimitFormat']; ?></strong>
                            <br>
                            <div class="status-progressbar"><span><?php echo $common->server_memory_usage()['MemUsageCalc'] . '% '; ?></span><div style="width: <?php echo $common->server_memory_usage()['MemUsageCalc']; ?>%"></div></div>
                        <?php } ?>
                    </li>
                    <li>
                        <?php esc_html_e( 'PHP Memory WP-Limit', 'wpat' ); ?>: 
                        <?php echo '<strong>' . $common->wp_memory_usage()['MemLimitFormat'] . '</strong>'; ?> 
                    </li>
                    <li>
                        <?php esc_html_e( 'PHP Memory WP-Usage', 'wpat' ); ?>: 
                        <?php if( $common->wp_memory_usage()['MemLimitGet'] == '-1' ) { ?>
                            <strong><?php echo $common->wp_memory_usage()['MemUsageFormat'] . ' ' . esc_html__( 'of', 'wpat' ) . ' ' . esc_html__( 'Unlimited', 'wpat' ) . ' (-1)'; ?></strong>
                        <?php } else { ?>
                            <strong><?php echo $common->wp_memory_usage()['MemUsageFormat'] . ' ' . esc_html__( 'of', 'wpat' ) . ' ' . $common->wp_memory_usage()['MemLimitFormat']; ?></strong>
                            <br>
                            <div class="status-progressbar"><span><?php echo $common->wp_memory_usage()['MemUsageCalc'] . '% '; ?></span><div style="width: <?php echo $common->wp_memory_usage()['MemUsageCalc']; ?>%"></div></div>
                        <?php } ?>
                    </li>
                    <li>
                        <?php esc_html_e( 'PHP Max Upload Size (WP)', 'wpat' ); ?>: <strong><?php echo (int)ini_get('upload_max_filesize') . ' MB (' . size_format( wp_max_upload_size() ) . ')'; ?></strong>
                    </li>
                    <li>
                        <a href="<?php echo admin_url('tools.php?page=wpat_sys_info'); ?>"><span class="dashicons dashicons-dashboard"></span> <?php esc_html_e('Full System Information', 'wpat'); ?></a>
                    </li>
                </ul>            
            </div>																 
        </div>

    <?php }

endif;

// WP SYSTEM WIDGET --> ADD DASHBOARD WIDGET

if( ! function_exists('wp_admin_system_dashboard_widget') ) :

	function wp_admin_system_dashboard_widget() {
        
		wp_add_dashboard_widget('system_info_db_widget', esc_html__( 'System Info', 'wpat' ), 'wp_admin_system_dashboard_widget_content');

	}

endif;

add_action('wp_dashboard_setup', 'wp_admin_system_dashboard_widget');