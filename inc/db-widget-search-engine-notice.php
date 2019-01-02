<?php 

/*****************************************************************/
/* WP SEACH ENGINE NOTICE DASHBOARD WIDGET */
/*****************************************************************/

// WP SEACH ENGINE NOTICE WIDGET --> CALLABLE WIDGET CONTENT

if( ! function_exists('wp_admin_search_engine_notice_widget_content') ) :

    function wp_admin_search_engine_notice_widget_content() { ?>

        <style>
            .wpat-seo-vis {background:#fdfaf1;margin:-15px -15px -15px -15px;text-align:center;}
            .wpat-seo-vis-focus {line-height:normal;color:#82878c;font-size:40px;border-bottom:1px solid #eee;padding:23px 20px 28px 20px}
            .wpat-seo-vis-focus .wpat-seo-vis-num {display:inline-block;color:#ffb900}
            .wpat-seo-vis-focus .wpat-seo-vis-num span {width:auto;height:auto;font-size:40px}
            .wpat-seo-vis-focus .wpat-seo-vis-num ~ div {font-size:16px;font-weight:100;line-height:1.4em;width:100%}
        </style>

        <div class="wpat-seo-vis">
            <div class="wpat-seo-vis-focus">
                <div class="wpat-seo-vis-num">
                    <span class="dashicons dashicons-warning"></span>
                </div>
                <div><?php esc_html_e( 'Your website is currently not visible to search engines!', 'wpat' ); ?></div>
            </div>
        </div>

    <?php }

endif;

// WP SEACH ENGINE NOTICE WIDGET --> ADD DASHBOARD WIDGET

if( ! function_exists('wp_admin_search_engine_notice_widget') ) :

	function wp_admin_search_engine_notice_widget() {

		wp_add_dashboard_widget('wp_search_engine_notice_db_widget', esc_html__( 'Search Engine Visibility', 'wpat' ), 'wp_admin_search_engine_notice_widget_content');

	}

endif;

add_action('wp_dashboard_setup', 'wp_admin_search_engine_notice_widget');