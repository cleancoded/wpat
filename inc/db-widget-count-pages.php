<?php 

/*****************************************************************/
/* WP COUNT PAGES DASHBOARD WIDGET */
/*****************************************************************/

// WP COUNT PAGES WIDGET --> CALLABLE WIDGET CONTENT

if( ! function_exists('wp_admin_count_pages_widget_content') ) :

    function wp_admin_count_pages_widget_content() { 

        $count_pages = wp_count_posts('page');
        $published = $count_pages->publish;
        $draft = $count_pages->draft . ' ' . esc_html__( 'Draft', 'wpat' );
        $pending = $count_pages->pending . ' ' . esc_html__( 'Pending', 'wpat' );
        $private = $count_pages->private . ' ' . esc_html__( 'Private', 'wpat' );
        $future = $count_pages->future . ' ' . esc_html__( 'Future', 'wpat' ); ?>

        <style>
            .wpat-post-count {margin:0px -15px -15px -15px;text-align:center;}
            .wpat-post-count-focus {line-height:normal;color:#82878c;font-size:40px;border-bottom:1px solid #eee;padding-bottom:20px}
            .wpat-post-count-focus .wpat-post-count-num {display:inline-block}
            .wpat-post-count-focus .wpat-post-count-num ~ div {font-size:16px;font-weight:100;width:100%}
            .wpat-post-count-detail {background:#f8f9fb;padding:12px}
        </style>

        <div class="wpat-post-count">
            <div class="wpat-post-count-focus">
                <div class="wpat-post-count-num">
                    <?php echo esc_html( $published ); ?>
                </div>
                <div><?php esc_html_e( 'Published Pages', 'wpat' ); ?></div>
            </div>
            <div class="wpat-post-count-detail">
                <?php echo esc_html( $draft ) . ' | ' .  esc_html( $pending ) . ' | ' .  esc_html( $private ) . ' | ' .  esc_html( $future ); ?>
            </div>
            
        </div>

    <?php }

endif;

// WP COUNT PAGES WIDGET --> ADD DASHBOARD WIDGET

if( ! function_exists('wp_admin_count_pages_widget') ) :

	function wp_admin_count_pages_widget() {

		wp_add_dashboard_widget('wp_count_pages_db_widget', esc_html__( 'Pages', 'wpat' ), 'wp_admin_count_pages_widget_content');

	}

endif;

add_action('wp_dashboard_setup', 'wp_admin_count_pages_widget');