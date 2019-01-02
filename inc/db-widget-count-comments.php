<?php 

/*****************************************************************/
/* WP COUNT COMMENTS DASHBOARD WIDGET */
/*****************************************************************/

// WP COUNT COMMENTS WIDGET --> CALLABLE WIDGET CONTENT

if( ! function_exists('wp_admin_count_comments_widget_content') ) :

    function wp_admin_count_comments_widget_content() { 

        $count_comments = wp_count_comments();
        $all = $count_comments->all;
        $approved = $count_comments->approved . ' ' . esc_html__( 'Approved', 'wpat' );
        $moderated = $count_comments->moderated . ' ' . esc_html__( 'Pending', 'wpat' );
        $spam = $count_comments->spam . ' ' . esc_html__( 'Spam', 'wpat' );
        $trash = $count_comments->trash . ' ' . esc_html__( 'Trash', 'wpat' ); ?>

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
                    <?php echo esc_html( $all ); ?>
                </div>
                <div><?php esc_html_e( 'Comments', 'wpat' ); ?></div>
            </div>
            <div class="wpat-post-count-detail">
                <?php echo esc_html( $approved ) . ' | ' .  esc_html( $moderated ) . ' | ' .  esc_html( $spam ) . ' | ' .  esc_html( $trash ); ?>
            </div>
            
        </div>

    <?php }

endif;

// WP COUNT COMMENTS WIDGET --> ADD DASHBOARD WIDGET

if( ! function_exists('wp_admin_count_comments_widget') ) :

	function wp_admin_count_comments_widget() {

		wp_add_dashboard_widget('wp_count_comments_db_widget', esc_html__( 'Comments', 'wpat' ), 'wp_admin_count_comments_widget_content');

	}

endif;

add_action('wp_dashboard_setup', 'wp_admin_count_comments_widget');