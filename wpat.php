<?php /*
Plugin Name: WPAT
Plugin URI: https://cleancoded.com/wpat
Description: A clean coded WordPress admin theme
Version: 1.9
Author: CLEANCODED
Author URI: https://cleancoded.com
Text Domain: wpat
Domain Path: /languages
*/

/*****************************************************************/
/* CREATE PLUGIN PATHS */
/*****************************************************************/

/* NOTE: By adding a custom WP filter, this plugin can be called from the theme folder without installing it manually */

if ( ! function_exists( 'wpat_path' ) ) :

    function wpat_path( $path ) {
        
        if( has_filter( 'wpat_path' ) ) return apply_filters( 'wpat_path', $path ); // get custom filter path
        else return plugins_url( $path , __FILE__ ); // get plugin path
        
    }

endif;


if ( ! function_exists( 'wpat_dir' ) ) :

    function wpat_dir( $path ) {

        if( has_filter( 'wpat_dir' ) ) return apply_filters( 'wpat_dir', $path ); // get custom filter dir path
        else return plugin_dir_path( __FILE__ ) . $path; // get plugin dir path
        
    }

endif;


/*****************************************************************/
/* CREATE THE WPAT PLUGIN */
/*****************************************************************/

if( ! class_exists('WPAT_Options') ) :

class WPAT_Options {
 
	/*****************************************************************/
    /* ATTRIBUTES */
    /*****************************************************************/
 
    // Refers to a single instance of this class.
    private static $instance = null;
 
    // Saved options
    public $options;
    
 	
	/*****************************************************************/
    /* CONSTRUCTOR */
    /*****************************************************************/
 
    // Creates or returns an instance of this class.
    public static function get_instance() {
 
        if ( null == self::$instance ) {
            self::$instance = new self;
        }
 
        return self::$instance;
 
    } // end get_instance;
 
    
    /*****************************************************************/
    /* INITIALIZES THE WPAT PLUGIN */
    /*****************************************************************/
    
	public function __construct() {
 
        if( is_admin() ) {
			
			require_once( ABSPATH . 'wp-admin/includes/screen.php' );
			
            // Add the page to the admin menu
            add_action( 'admin_menu', array( &$this, 'wpat_add_page' ) );
        
            // Register settings options
            add_action( 'admin_init', array( &$this, 'wpat_register_settings') );

            // Register page options
            add_action( 'admin_init', array( &$this, 'wpat_register_page_options') );

            // Register plugin page scripts
            add_action( 'admin_enqueue_scripts', array( $this, 'wpat_load_plugin_page_specific_scripts') ); 

            // Register global javascript and stylesheets
            add_action( 'admin_enqueue_scripts', array( $this, 'wpat_enqueue_admin_js' ) );

            // Register generate js / css files
            add_action( 'admin_init', array( $this, 'wpat_generate_custom_css_js' ) );
            
            // Check for undefined option keys
            add_action( 'admin_init', array( $this, 'wpat_check_for_undefined_options' ) );
        }
        
		// Get registered options
        if( is_multisite() ) {
            $this->options = get_blog_option( get_current_blog_id(), 'wpat_settings_options', array() );
        } else {
            $this->options = get_option( 'wpat_settings_options' );
        }
            
        // Set all option field names
        $this->option_fields = array(
            'user_box' => esc_html__( 'User Box', 'wpat' ),
            'company_box' => esc_html__( 'Company Box', 'wpat' ),
            'company_box_logo' => esc_html__( 'Company Box Logo', 'wpat' ),
            'company_box_logo_size' => esc_html__( 'Company Box Logo Size', 'wpat' ),
            'thumbnail' => esc_html__( 'Thumbnails', 'wpat' ),
            'post_page_id' => esc_html__( 'Post/Page IDs', 'wpat' ),
            'hide_help' => esc_html__( 'Contextual Help', 'wpat' ),
            'hide_screen_option' => esc_html__( 'Screen Options', 'wpat' ),
            'left_menu_width' => esc_html__( 'Left Menu Width', 'wpat' ),
            'left_menu_expand' => esc_html__( 'Left Menu Expandable', 'wpat' ),
            'spacing' => esc_html__( 'Spacing', 'wpat' ),
            'spacing_max_width' => esc_html__( 'Spacing Max Width', 'wpat' ),
            'credits' => esc_html__( 'Credits', 'wpat' ),
            'google_webfont' => esc_html__( 'Custom Web Font', 'wpat' ),
            'google_webfont_weight' => esc_html__( 'Custom Web Font Weight', 'wpat' ),
            'toolbar' => esc_html__( 'Toolbar', 'wpat' ),
            'hide_adminbar_comments' => esc_html__( 'Toolbar Comments Menu', 'wpat' ),
            'hide_adminbar_new' => esc_html__( 'Toolbar New Content Menu', 'wpat' ),
            'hide_adminbar_customize' => esc_html__( 'Toolbar Customize Link', 'wpat' ),
            'hide_adminbar_search' => esc_html__( 'Toolbar Search', 'wpat' ),
            'toolbar_wp_icon' => esc_html__( 'Toolbar WP Icon', 'wpat' ),            
            'toolbar_icon' => esc_html__( 'Custom Toolbar Icon', 'wpat' ),
            'theme_color' => esc_html__( 'Theme Color', 'wpat' ),
            'theme_background' => esc_html__( 'Background Gradient Start Color', 'wpat' ),
            'theme_background_end' => esc_html__( 'Background Gradient End Color', 'wpat' ),
            'login_disable' => esc_html__( 'Customized Login Page', 'wpat' ),
            'login_title' => esc_html__( 'Login Title', 'wpat' ),
            'logo_upload' => esc_html__( 'Login Logo', 'wpat' ),
            'logo_size' => esc_html__( 'Login Logo Size', 'wpat' ),
            'login_bg' => esc_html__( 'Login Background Image', 'wpat' ),
            'memory_usage' => esc_html__( 'Memory Usage', 'wpat' ),
            'memory_limit' => esc_html__( 'WP Memory Limit', 'wpat' ),
            'memory_available' => esc_html__( 'Memory Available', 'wpat' ),
            'php_version' => esc_html__( 'PHP Version', 'wpat' ),
            'ip_address' => esc_html__( 'IP Address', 'wpat' ),
            'wp_version' => esc_html__( 'WP Version', 'wpat' ),
            'css_admin' => esc_html__( 'WP Admin CSS', 'wpat' ),
            'css_login' => esc_html__( 'WP Login CSS', 'wpat' ),
            'wp_svg' => esc_html__( 'SVG Support', 'wpat' ),
            'wp_ico' => esc_html__( 'ICO Support', 'wpat' ),
            'disable_page_system' => esc_html__( 'WPAT System Info Page', 'wpat' ),
            'disable_page_export' => esc_html__( 'WPAT Im- / Export Page', 'wpat' ),
            'disable_page_ms' => esc_html__( 'WPAT Multisite Sync Page', 'wpat' ),
            'disable_theme_options' => esc_html__( 'Network Theme Options', 'wpat' ),
            'wp_version_tag' => esc_html__( 'WP Version Meta-Tag', 'wpat' ),
            'wp_emoji' => esc_html__( 'WP Emoji', 'wpat' ),
            'wp_feed_links' => esc_html__( 'WP RSS Feed', 'wpat' ),
            'wp_rsd_link' => esc_html__( 'WP RSD', 'wpat' ),
            'wp_wlwmanifest' => esc_html__( 'WP Wlwmanifest', 'wpat' ),
            'wp_shortlink' => esc_html__( 'WP Shortlink', 'wpat' ),
            'wp_rest_api' => esc_html__( 'WP REST API', 'wpat' ),
            'wp_oembed' => esc_html__( 'WP oEmbed', 'wpat' ),
            'wp_xml_rpc' => esc_html__( 'WP XML-RPC / X-Pingback', 'wpat' ),
            'wp_heartbeat' => esc_html__( 'WP Heartbeat', 'wpat' ),
            'wp_rel_link' => esc_html__( 'WP Rel Links', 'wpat' ),
            'wp_self_pingback' => esc_html__( 'WP Self Pingbacks', 'wpat' ),
            'mb_custom_fields' => esc_html__( 'Custom Fields Meta Box', 'wpat' ),
            'mb_commentstatus' => esc_html__( 'Comments Status Meta Box', 'wpat' ),
            'mb_comments' => esc_html__( 'Comments Meta Box', 'wpat' ),
            'mb_author' => esc_html__( 'Author Meta Box', 'wpat' ),
            'mb_category' => esc_html__( 'Categories Meta Box', 'wpat' ),
            'mb_format' => esc_html__( 'Post Format Meta Box', 'wpat' ),
            'mb_pageparent' => esc_html__( 'Page Parent Meta Box', 'wpat' ),
            'mb_postexcerpt' => esc_html__( 'Post Excerpt Meta Box', 'wpat' ),
            'mb_postimage' => esc_html__( 'Post Image Meta Box', 'wpat' ),
            'mb_revisions' => esc_html__( 'Revisions Meta Box', 'wpat' ),
            'mb_slug' => esc_html__( 'Slug Meta Box', 'wpat' ),
            'mb_tags' => esc_html__( 'Tags Meta Box', 'wpat' ),
            'mb_trackbacks' => esc_html__( 'Trackbacks Meta Box', 'wpat' ),
            'dbw_quick_press' => esc_html__( 'Quick Draft Widget', 'wpat' ),
            'dbw_right_now' => esc_html__( 'At the Glance Widget', 'wpat' ),
            'dbw_activity' => esc_html__( 'Activity Widget', 'wpat' ),
            'dbw_primary' => esc_html__( 'WP Events & News Widget', 'wpat' ),
            'dbw_welcome' => esc_html__( 'Welcome Widget', 'wpat' ),
            'dbw_wpat_user_log' => esc_html__( 'WPAT User Activities Widget', 'wpat' ),
            'dbw_wpat_sys_info' => esc_html__( 'WPAT System Info Widget', 'wpat' ),
            'dbw_wpat_count_post' => esc_html__( 'WPAT Post Count Widget', 'wpat' ),
            'dbw_wpat_count_page' => esc_html__( 'WPAT Page Count Widget', 'wpat' ),
            'dbw_wpat_count_comment' => esc_html__( 'WPAT Comment Count Widget', 'wpat' ),
            'dbw_wpat_recent_post' => esc_html__( 'WPAT Recent Posts Widget', 'wpat' ),
            'dbw_wpat_recent_page' => esc_html__( 'WPAT Recent Pages Widget', 'wpat' ),
            'dbw_wpat_recent_comment' => esc_html__( 'WPAT Recent Comments Widget', 'wpat' ),
            'dbw_wpat_memory' => esc_html__( 'WPAT Memory Usage Widget', 'wpat' ),
            'wt_pages' => esc_html__( 'Pages Widget', 'wpat' ),
            'wt_calendar' => esc_html__( 'Calendar Widget', 'wpat' ),
            'wt_archives' => esc_html__( 'Archives Widget', 'wpat' ),
            'wt_meta' => esc_html__( 'Meta Widget', 'wpat' ),
            'wt_search' => esc_html__( 'Search Widget', 'wpat' ),
            'wt_text' => esc_html__( 'Text Widget', 'wpat' ),
            'wt_categories' => esc_html__( 'Categories Widget', 'wpat' ),
            'wt_recent_posts' => esc_html__( 'Recent Posts Widget', 'wpat' ),
            'wt_recent_comments' => esc_html__( 'Recent Comments Widget', 'wpat' ),
            'wt_rss' => esc_html__( 'RSS Widget', 'wpat' ),
            'wt_tag_cloud' => esc_html__( 'Tag Cloud Widget', 'wpat' ),
            'wt_nav' => esc_html__( 'Navigation Menu Widget', 'wpat' ),
            'wt_image' => esc_html__( 'Image Widget', 'wpat' ),
            'wt_audio' => esc_html__( 'Audio Widget', 'wpat' ),
            'wt_video' => esc_html__( 'Video Widget', 'wpat' ),
            'wt_gallery' => esc_html__( 'Gallery Widget', 'wpat' ),
            'wt_html' => esc_html__( 'Custom HTML Widget', 'wpat' ),
            'wp_header_code' => esc_html__( 'Header Code', 'wpat' ),
            'wp_footer_code' => esc_html__( 'Footer Code', 'wpat' ),
            'meta_referrer_policy' => esc_html__( 'Meta Referrer Policy', 'wpat' ),
        );
        
        // Exception fields are not restorable
        $css_admin = isset( $this->options['css_admin'] ) ? $this->options['css_admin'] : null;
        $css_login = isset( $this->options['css_login'] ) ? $this->options['css_login'] : null;
        $wp_header_code = isset( $this->options['wp_header_code'] ) ? $this->options['wp_header_code'] : null;
        $wp_footer_code = isset( $this->options['wp_footer_code'] ) ? $this->options['wp_footer_code'] : null;
        
        // Define pre-option values (used for restore options)
        $this->pre_options = array(
            'user_box' => false,
            'company_box' => false,
            'company_box_logo' => false,
            'company_box_logo_size' => '140',
            'thumbnail' => false,
            'post_page_id' => false,
            'hide_help' => false,
            'hide_screen_option' => false,
            'left_menu_width' => '200',
            'left_menu_expand' => false,
            'spacing' => false,
            'spacing_max_width' => '2000',
            'credits' => false,
            'google_webfont' => false,
            'google_webfont_weight' => false,
            'toolbar' => false,
            'hide_adminbar_comments' => false,
            'hide_adminbar_new' => false,
            'hide_adminbar_customize' => false,
            'hide_adminbar_search' => false,
            'toolbar_wp_icon' => false,
            'toolbar_icon' => false,
            'theme_color' => false,
            'theme_background' => false,
            'theme_background_end' => false,
            'login_disable' => false,
            'login_title' => esc_html__( 'Welcome Back.', 'wpat' ),
            'logo_upload' => false,
            'logo_size' => '200',
            'login_bg' => false,
            'memory_usage' => false,
            'memory_limit' => false,
            'memory_available' => false,
            'php_version' => false,
            'ip_address' => false,
            'wp_version' => false,
            'css_admin' => esc_html( $css_admin ),
            'css_login' => esc_html( $css_login ),
            'wp_svg' => false,
            'wp_ico' => false,
            'disable_page_system' => false,
            'disable_page_export' => false,
            'disable_page_ms' => false,
            'disable_theme_options' => false,
            'wp_version_tag' => false,
            'wp_emoji' => false,
            'wp_feed_links' => false,
            'wp_rsd_link' => false,
            'wp_wlwmanifest' => false,
            'wp_shortlink' => false,
            'wp_rest_api' => false,
            'wp_oembed' => false,
            'wp_xml_rpc' => false,
            'wp_heartbeat' => false,
            'wp_rel_link' => false,
            'wp_self_pingback' => false,
            'mb_custom_fields' => false,
            'mb_commentstatus' => false,
            'mb_comments' => false,
            'mb_author' => false,
            'mb_category' => false,
            'mb_format' => false,
            'mb_pageparent' => false,
            'mb_postexcerpt' => false,
            'mb_postimage' => false,
            'mb_revisions' => false,
            'mb_slug' => false,
            'mb_tags' => false,
            'mb_trackbacks' => false,
            'dbw_quick_press' => false,
            'dbw_right_now' => false,
            'dbw_activity' => false,
            'dbw_primary' => false,
            'dbw_welcome' => false,
            'dbw_wpat_user_log' => false,
            'dbw_wpat_sys_info' => false,
            'dbw_wpat_count_post' => false,
            'dbw_wpat_count_page' => false,
            'dbw_wpat_count_comment' => false,
            'dbw_wpat_recent_post' => false,
            'dbw_wpat_recent_page' => false,
            'dbw_wpat_recent_comment' => false,
            'dbw_wpat_memory' => false,
            'wt_pages' => false,
            'wt_calendar' => false,
            'wt_archives' => false,
            'wt_meta' => false,
            'wt_search' => false,
            'wt_text' => false,
            'wt_categories' => false,
            'wt_recent_posts' => false,
            'wt_recent_comments' => false,
            'wt_rss' => false,
            'wt_tag_cloud' => false,
            'wt_nav' => false,
            'wt_image' => false,
            'wt_audio' => false,
            'wt_video' => false,
            'wt_gallery' => false,
            'wt_html' => false,
            'wp_header_code' => esc_html( $wp_header_code ),
            'wp_footer_code' => esc_html( $wp_footer_code ),
            'meta_referrer_policy' => 'none',
        );
        
        $this->plugin_pages_option_fields = array(
            'disable_page_system',
            'disable_page_export',
            'disable_page_ms',      
        );
        
        $this->optimization_option_fields = array(
            array(
                'wp_version_tag',
                esc_html__( 'Remove the WordPress Version Meta-Tag from wp head.', 'wpat' ),
                esc_html__( 'Show the version number of your currently installed WordPress in the source code.', 'wpat' ),
            ),
            array(
                'wp_emoji',
                esc_html__( 'Remove the WordPress Emoticons from your source code.', 'wpat' ),
                esc_html__( 'Display a textual portrayals like ";-)" as a emoticon icon.', 'wpat' ),
            ),
            array(
                'wp_feed_links',
                esc_html__( 'Disable the RSS feed functionality and remove the WordPress page and comments RSS feed links from wp head.', 'wpat' ),
                esc_html__( 'RSS (Really Simple Syndication) is a type of web feed which allows users to access updates to online content in a standardized, computer-readable format.', 'wpat' ),
            ),
            array(
                'wp_rsd_link',
                esc_html__( 'Remove the RSD link from wp head.', 'wpat' ),
                esc_html__( 'Really Simple Discovery (RSD) is an XML format and a publishing convention for making services exposed by a blog, or other web software, discoverable by client software.', 'wpat' ),
            ),
            array(
                'wp_wlwmanifest',
                esc_html__( 'Remove the Wlwmanifest link from wp head.', 'wpat' ),
                esc_html__( 'Needed to enable tagging support for Windows Live Writer.', 'wpat' ),
            ),
            array(
                'wp_shortlink',
                esc_html__( 'Remove the shortlink link from wp head.', 'wpat' ),
                esc_html__( 'Shortlink is a shorten version of a web page’s URL.', 'wpat' ),
            ),
            array(
                'wp_rest_api',
                esc_html__( 'Disable the REST API and remove the wp json link from wp head.', 'wpat' ),
                esc_html__( 'The API makes it super easy to retrieve data using GET requests, which is useful for those building apps with WordPress.', 'wpat' ),
            ),
            array(
                'wp_oembed',
                esc_html__( 'Disable wp embed and remove the oEmbed links from wp head.', 'wpat' ),
                esc_html__( 'oEmbed feature which allows others to embed your WordPress posts into their own site by adding the post URL.', 'wpat' ),
            ),
            array(
                'wp_xml_rpc',
                esc_html__( 'Disable remote access.', 'wpat' ),
                esc_html__( 'XML-RPC is a remote procedure call which uses XML to encode its calls and HTTP as a transport mechanism. If you want to access and publish to your blog remotely, then you need XML-RPC enabled. XML-RPC protocol is used by WordPress as API for Pingbacks and third-party applications, such as mobile apps, inter-blog communication and popular plugins like JetPack.', 'wpat' ),
            ),
            array(
                'wp_heartbeat',
                esc_html__( 'Stop the heartbeat updates.', 'wpat' ),
                esc_html__( 'The Heartbeat API is a simple server polling API built in to WordPress, allowing near-real-time frontend updates. The heartbeat API allows for regular communication between the users browser and the server. One of the original motivations was to allow for locking posts and warning users when more than one user is attempting to edit a post, or warning the user when their log-in has expired.', 'wpat' ),
            ),
            array(
                'wp_rel_link',
                esc_html__( 'Remove the post rel index / start / parent / prev / next links from wp head.', 'wpat' ),
                esc_html__( 'This feature display the URL of the index, start, parent, previous and next post in the source code.', 'wpat' ),
            ),    
            array(
                'wp_self_pingback',
                esc_html__( 'Disable WordPress self pingbacks / trackbacks.', 'wpat' ),
                esc_html__( 'This will allow you to disable self-pingbacks (messages and comments), which are linking back to your own blog.', 'wpat' ),
            ),     
        );
        
        $this->meta_box_option_fields = array(
            array(
                'mb_custom_fields',
                esc_html__( 'Remove the Custom Fields Box for posts and pages.', 'wpat' ),
                '',
            ),
            array(
                'mb_commentstatus',
                esc_html__( 'Remove the Discussion Box for posts and pages.', 'wpat' ),
                '',
            ),
            array(
                'mb_comments',
                esc_html__( 'Remove the Comments Box for posts and pages.', 'wpat' ),
                '',
            ),
            array(
                'mb_author',
                esc_html__( 'Remove the Author Box for posts and pages.', 'wpat' ),
                '',
            ),
            array(
                'mb_category',
                esc_html__( 'Remove the Category Box for posts.', 'wpat' ),
                '',
            ),
            array(
                'mb_format',
                esc_html__( 'Remove the Post Format Box for posts.', 'wpat' ),
                '',
            ),
            array(
                'mb_pageparent',
                esc_html__( 'Remove the Page Attributes Box for pages.', 'wpat' ),
                '',
            ),
            array(
                'mb_postexcerpt',
                esc_html__( 'Remove the Excerpt Box for posts.', 'wpat' ),
                '',
            ),
            array(
                'mb_postimage',
                esc_html__( 'Remove the Featured Image Box for posts and pages.', 'wpat' ),
                '',
            ),
            array(
                'mb_revisions',
                esc_html__( 'Remove the Revisions Box for posts and pages.', 'wpat' ),
                '',
            ),
            array(
                'mb_slug',
                esc_html__( 'Remove the Slug Box for posts and pages.', 'wpat' ),
                esc_html__( 'Caution: Disabling the slug box does not allow you to customize the post or page URL.', 'wpat' ),
            ),
            array(
                'mb_tags',
                esc_html__( 'Remove the Tags Box for posts.', 'wpat' ),
                '',
            ),
            array(
                'mb_trackbacks',
                esc_html__( 'Remove the Send Trackbacks Box for posts and pages.', 'wpat' ),
                '',
            ),            
        );
        
        $this->db_widget_option_fields = array(
            'dbw_quick_press',
            'dbw_right_now',
            'dbw_activity',
            'dbw_primary',
            'dbw_welcome', 
            'dbw_wpat_user_log', 
            'dbw_wpat_sys_info',
            'dbw_wpat_count_post',
            'dbw_wpat_count_page',
            'dbw_wpat_count_comment',
            'dbw_wpat_recent_post',
            'dbw_wpat_recent_page',
            'dbw_wpat_recent_comment',
            'dbw_wpat_memory',
        );
        
        $this->widget_option_fields = array(
            'wt_pages',
            'wt_calendar',
            'wt_archives',
            'wt_meta',
            'wt_search',
            'wt_text',
            'wt_categories',
            'wt_recent_posts',
            'wt_recent_comments',
            'wt_rss',
            'wt_tag_cloud',
            'wt_nav',
            'wt_image',
            'wt_audio',
            'wt_video',
            'wt_gallery',
            'wt_html',
        );
        
        $this->frontend_option_fields = array(
            array(
                'wp_header_code',
                esc_html__( 'Add custom code to the frontend header.', 'wpat' ),
                esc_html__( 'Will be inserted into the wp_head hook.', 'wpat' ),
            ),
            array(
                'wp_footer_code',
                esc_html__( 'Add custom code to the frontend footer.', 'wpat' ),
                esc_html__( 'Will be inserted into the wp_footer hook.', 'wpat' ),
            ),
            array(
                'meta_referrer_policy',
                esc_html__( 'Add the meta referrer tag and select your value.', 'wpat' ),
                esc_html__( 'If you use SSL for your website, analytics tools like Google Analytics can not see the referrer by default. For example, if you select "Origin", your referrer will be visible again.', 'wpat' ),
            ),        
        );
        
        $this->option_heads = array(
            'head_theme' => esc_html__( 'Theme Options', 'wpat' ),
            'head_toolbar' => esc_html__( 'Toolbar', 'wpat' ),
            'head_color' => esc_html__( 'Colors', 'wpat' ),
            'head_login' => esc_html__( 'Login Page', 'wpat' ),
            'head_footer' => esc_html__( 'Footer', 'wpat' ),
            'head_css' => esc_html__( 'Custom CSS', 'wpat' ),
            'head_media' => esc_html__( 'Media', 'wpat' ),
            'head_pages' => esc_html__( 'Pages', 'wpat' ),
            'head_ms' => esc_html__( 'Multisite', 'wpat' ),
            'head_optimize' => esc_html__( 'Optimization & Security', 'wpat' ),
            'head_metabox' => esc_html__( 'Meta Boxes', 'wpat' ),
            'head_dashboard' => esc_html__( 'Dashboard Widgets', 'wpat' ),
            'head_widget' => esc_html__( 'Widgets', 'wpat' ),
            'head_frontend' => esc_html__( 'Frontend', 'wpat' ),
        );
		
    	// Load textdomain for i18n
        load_plugin_textdomain( 'wpat', null, basename(dirname( __FILE__ )) . '/languages/' );
        
	}
    
    
    /*****************************************************************/
    /* ADD A WPAT PLUGIN OPTIONS PAGE */
    /*****************************************************************/
    
    public function wpat_add_page() {
 
		// $page_title, $menu_title, $capability, $menu_slug, $callback_function
		add_submenu_page( 'tools.php', esc_html__( 'WPAT', 'wpat' ), esc_html__( 'WPAT Options', 'wpat' ), 'manage_options', 'wpat', array( $this, 'wpat_display_page' ) );
		
	}
    
    
    /*****************************************************************/
    /* REGISTER THE WPAT PLUGIN SETTINGS/OPTIONS */
    /*****************************************************************/
    
    public function wpat_register_settings() {
        
        // option group, option name, sanitize
		register_setting( '__FILE__', 'wpat_settings_options', array( $this, 'wpat_validate_options' ) );
		
	}
    
    
    /*****************************************************************/
    /* DISPLAY THE WPAT PLUGIN OPTIONS PAGE */
    /*****************************************************************/
 
    public function wpat_display_page() { ?>
    
		<div class="wrap wpat">

			<h1><?php echo esc_html__( 'WPAT - Options', 'wpat' ); ?><?php if ( is_multisite() ) { ?><span style="color:#8b959e"> <?php echo ' | ' . esc_html__( 'Current Blog ID', 'wpat' ) . ': '. get_current_blog_id(); ?></span><?php } ?></h1>           
            
            <p><?php esc_html_e( 'Speed up and modify your WordPress backend like a charm. This plugin is the central place to take WordPress design to the next level.', 'wpat' ); ?></p>
            
            <div class="wpat-page-menu">
                <ul>
                    <li><a href="#index_theme"><?php echo $this->option_heads['head_theme']; ?></a></li>
                    <li><a href="#index_toolbar"><?php echo $this->option_heads['head_toolbar']; ?></a></li>
                    <li><a href="#index_color"><?php echo $this->option_heads['head_color']; ?></a></li>
                    <li><a href="#index_login"><?php echo $this->option_heads['head_login']; ?></a></li>
                    <li><a href="#index_footer"><?php echo $this->option_heads['head_footer']; ?></a></li>
                    <li><a href="#index_css"><?php echo $this->option_heads['head_css']; ?></a></li>
                    <li><a href="#index_media"><?php echo $this->option_heads['head_media']; ?></a></li>
                    <li><a href="#index_page"><?php echo $this->option_heads['head_pages']; ?></a></li>
                    <li><a href="#index_ms"><?php echo $this->option_heads['head_ms']; ?></a></li>
                    <li><a href="#index_optimize"><?php echo $this->option_heads['head_optimize']; ?></a></li>
                    <li><a href="#index_metabox"><?php echo $this->option_heads['head_metabox']; ?></a></li>
                    <li><a href="#index_dashboard"><?php echo $this->option_heads['head_dashboard']; ?></a></li>
                    <li><a href="#index_widget"><?php echo $this->option_heads['head_widget']; ?></a></li>
                    <li><a href="#index_frontend"><?php echo $this->option_heads['head_frontend']; ?></a></li>
                </ul>
            </div>
            
			<form action="options.php" method="post" enctype="multipart/form-data">
				<?php if( is_multisite() ) {
                    $main_blog_id = 1;
                    $options = get_blog_option( $main_blog_id, 'wpat_settings_options', array() );
                } else {
                    $options = get_option( 'wpat_settings_options' );
                }
                                                      
                /*
                // print options check
                echo '<pre>';
                    print_r($this->options);
                echo '</pre>';
                */                         
                                                      
                // error message output			
                settings_errors('wpat_settings_options');

                // fields output				
                settings_fields('__FILE__');
                do_settings_sections('__FILE__');

                echo '<table class="form-table"><tbody><tr><th scope="row"></th><td><p class="description">';

                // manage save button visibility			
                if( $options['disable_theme_options'] == false || $options['disable_theme_options'] == true && get_current_blog_id() == 1 ) {
                    submit_button( esc_html__( 'Save Changes', 'wpat' ), 'button button-primary', 'save', false );
                } else {
                    echo '<button class="button" disabled value="">' . esc_html__( 'You have no permissions to change this options!', 'wpat' ) . '</button>';
                }
                                    
                // manage restore button visibility
                if( $options['disable_theme_options'] == false || $options['disable_theme_options'] == true && get_current_blog_id() == 1 ) {
                    submit_button( esc_html__( 'Restore all', 'wpat' ), 'button restore', 'reset', false ); 
                }

                echo '</p></td></tr></tbody></table>'; ?>
			</form>
			
		</div>
		
	<?php }
 
    
    /*****************************************************************/
    /* REGISTER THE WPAT PLUGIN ADMIN PAGE OPTIONS */
    /*****************************************************************/
    
    public function wpat_register_page_options() {
 
		// Add Section for option fields
		add_settings_section( 'admin_theme_section', '<span id="index_theme" class="wpat-page-index"></span>' . $this->option_heads['head_theme'], array( $this, 'wpat_display_section' ), '__FILE__' ); // Theme Options
        
            add_settings_field( 'admin_theme_spacing',                  $this->option_fields['spacing'],                        array( $this, 'admin_theme_spacing_settings' ),                 '__FILE__', 'admin_theme_section' ); // Add Spacing Option
            add_settings_field( 'admin_theme_user_box',                 $this->option_fields['user_box'],                       array( $this, 'admin_theme_user_box_settings' ),                '__FILE__', 'admin_theme_section' ); // Add User Box Option
            add_settings_field( 'admin_theme_company_box',              $this->option_fields['company_box'],                    array( $this, 'admin_theme_company_box_settings' ),             '__FILE__', 'admin_theme_section' ); // Add Company Box Option
            add_settings_field( 'admin_theme_thumbnail',                $this->option_fields['thumbnail'],                      array( $this, 'admin_theme_thumbnail_settings' ),               '__FILE__', 'admin_theme_section' ); // Add Thumbnail Option
            add_settings_field( 'admin_theme_post_page_id',             $this->option_fields['post_page_id'],                   array( $this, 'admin_theme_post_page_id_settings' ),            '__FILE__', 'admin_theme_section' ); // Add Post/Page ID Option
            add_settings_field( 'admin_theme_hide_help',                $this->option_fields['hide_help'],                      array( $this, 'admin_theme_hide_help_settings' ),               '__FILE__', 'admin_theme_section' ); // Add Hide the Contextual Help Option
            add_settings_field( 'admin_theme_hide_screen_option',       $this->option_fields['hide_screen_option'],             array( $this, 'admin_theme_hide_screen_option_settings' ),      '__FILE__', 'admin_theme_section' ); // Add Hide the Screen Options
            add_settings_field( 'admin_theme_left_menu_width',       	$this->option_fields['left_menu_width'],             	array( $this, 'admin_theme_left_menu_width_settings' ),      	'__FILE__', 'admin_theme_section' ); // Add Left Menu Width Option
            add_settings_field( 'admin_theme_left_menu_expand',       	$this->option_fields['left_menu_expand'],             	array( $this, 'admin_theme_left_menu_expand_settings' ),      	'__FILE__', 'admin_theme_section' ); // Add Left expandable Menu Option
            add_settings_field( 'admin_theme_google_webfont',           $this->option_fields['google_webfont'],                 array( $this, 'admin_theme_google_webfont_settings' ),          '__FILE__', 'admin_theme_section' ); // Add Google Webfont Option
		
		// Add Section for Toolbar
		add_settings_section( 'admin_theme_section_toolbar', '<span id="index_toolbar" class="wpat-page-index"></span>' . $this->option_heads['head_toolbar'], array( $this, 'wpat_display_section_toolbar' ), '__FILE__' );		
		
            add_settings_field( 'admin_theme_toolbar',                  $this->option_fields['toolbar'],                        array( $this, 'admin_theme_toolbar_settings' ),                 '__FILE__', 'admin_theme_section_toolbar' ); // Add Hide Toolbar Option            
            add_settings_field( 'admin_theme_hide_adminbar_comments',   $this->option_fields['hide_adminbar_comments'],         array( $this, 'admin_theme_hide_adminbar_comments_settings' ),  '__FILE__', 'admin_theme_section_toolbar' ); // Add Hide Toolbar Comments Menu            
            add_settings_field( 'admin_theme_hide_adminbar_new',        $this->option_fields['hide_adminbar_new'],              array( $this, 'admin_theme_hide_adminbar_new_settings' ),       '__FILE__', 'admin_theme_section_toolbar' ); // Add Hide Toolbar New Content Menu         
            add_settings_field( 'admin_theme_hide_adminbar_customize',  $this->option_fields['hide_adminbar_customize'],        array( $this, 'admin_theme_hide_adminbar_customize_settings' ), '__FILE__', 'admin_theme_section_toolbar' ); // Add Hide Toolbar Customize Link          
            add_settings_field( 'admin_theme_hide_adminbar_search',     $this->option_fields['hide_adminbar_search'],           array( $this, 'admin_theme_hide_adminbar_search_settings' ),    '__FILE__', 'admin_theme_section_toolbar' ); // Add Hide Toolbar Search   
            add_settings_field( 'admin_theme_toolbar_wp_icon',          $this->option_fields['toolbar_wp_icon'],                array( $this, 'admin_theme_toolbar_wp_icon_settings' ),         '__FILE__', 'admin_theme_section_toolbar' ); // Add Hide Toolbar WP Icon          
            add_settings_field( 'admin_theme_toolbar_icon',             $this->option_fields['toolbar_icon'],                   array( $this, 'admin_theme_toolbar_icon_settings' ),            '__FILE__', 'admin_theme_section_toolbar' ); // Add custom Toolbar Icon
		
		// Add Section for Colors Option
		add_settings_section( 'admin_theme_section_color', '<span id="index_color" class="wpat-page-index"></span>' . $this->option_heads['head_color'], array( $this, 'wpat_display_section_colors' ), '__FILE__' );		
            
            add_settings_field( 'admin_theme_color',                    $this->option_fields['theme_color'],                    array( $this, 'admin_theme_color_settings' ),                   '__FILE__', 'admin_theme_section_color' ); // Add custom Theme Color Field            
            add_settings_field( 'admin_theme_background',               esc_html__( 'Background Gradient Color', 'wpat' ), array( $this, 'admin_theme_background_settings' ),      '__FILE__', 'admin_theme_section_color' ); // Add custom Theme Background Gradient Color Field
		
		// Add Section for Login Option
		add_settings_section( 'admin_theme_section_login', '<span id="index_login" class="wpat-page-index"></span>' . $this->option_heads['head_login'], array( $this, 'wpat_display_section_login' ), '__FILE__' );
		
            add_settings_field( 'admin_theme_login_disable',            $this->option_fields['login_disable'],                  array( $this, 'admin_theme_login_disable_settings' ),           '__FILE__', 'admin_theme_section_login' ); // Add Login Disable Option            
            add_settings_field( 'admin_theme_login_title',              $this->option_fields['login_title'],                    array( $this, 'admin_theme_login_title_settings' ),             '__FILE__', 'admin_theme_section_login' ); // Add Title Field            
            add_settings_field( 'admin_theme_logo_upload',              $this->option_fields['logo_upload'],                    array( $this, 'admin_theme_logo_upload_settings' ),             '__FILE__', 'admin_theme_section_login' ); // Add Logo Option            
            add_settings_field( 'admin_theme_login_bg',                 $this->option_fields['login_bg'],                       array( $this, 'admin_theme_login_bg_settings' ),                '__FILE__', 'admin_theme_section_login' ); // Add Login BG Image Option
		
		// Add Section for Footer Information Option
		add_settings_section( 'admin_theme_section_footer', '<span id="index_footer" class="wpat-page-index"></span>' . $this->option_heads['head_footer'], array( $this, 'wpat_display_section_footer' ), '__FILE__' );		
            
            add_settings_field( 'admin_theme_credits',                  $this->option_fields['credits'],                        array( $this, 'admin_theme_credits_settings' ),                 '__FILE__', 'admin_theme_section_footer' ); // Add Credits Option
            add_settings_field( 'admin_theme_memory_usage',             $this->option_fields['memory_usage'],                   array( $this, 'admin_theme_memory_usage_settings' ),            '__FILE__', 'admin_theme_section_footer' ); // Add Memory Usage Option            
            add_settings_field( 'admin_theme_memory_limit',             $this->option_fields['memory_limit'],                   array( $this, 'admin_theme_memory_limit_settings' ),            '__FILE__', 'admin_theme_section_footer' ); // Add WP Memory Limit Option         
            add_settings_field( 'admin_theme_memory_available',         $this->option_fields['memory_available'],               array( $this, 'admin_theme_memory_available_settings' ),        '__FILE__', 'admin_theme_section_footer' ); // Add Memory Available Option             
            add_settings_field( 'admin_theme_php_version',              $this->option_fields['php_version'],                    array( $this, 'admin_theme_php_version_settings' ),             '__FILE__', 'admin_theme_section_footer' ); // Add PHP Version Option            
            add_settings_field( 'admin_theme_ip_address',               $this->option_fields['ip_address'],                     array( $this, 'admin_theme_ip_address_settings' ),              '__FILE__', 'admin_theme_section_footer' ); // Add IP Address Option            
            add_settings_field( 'admin_theme_wp_version',               $this->option_fields['wp_version'],                     array( $this, 'admin_theme_wp_version_settings' ),              '__FILE__', 'admin_theme_section_footer' ); // Add WP Version Option
		
		// Add Section for Custom CSS
		add_settings_section( 'admin_theme_section_css', '<span id="index_css" class="wpat-page-index"></span>' . $this->option_heads['head_css'], array( $this, 'wpat_display_section_css' ), '__FILE__' );		
            
            add_settings_field( 'admin_theme_css_admin',                $this->option_fields['css_admin'],                      array( $this, 'admin_theme_css_admin_settings' ),               '__FILE__', 'admin_theme_section_css' ); // Add Custom CSS for WPAT            
            add_settings_field( 'admin_theme_css_login',                $this->option_fields['css_login'],                      array( $this, 'admin_theme_css_login_settings' ),               '__FILE__', 'admin_theme_section_css' ); // Add Custom CSS for WP Login
		
		// Add Section for Media Support
		add_settings_section( 'admin_theme_section_media', '<span id="index_media" class="wpat-page-index"></span>' . $this->option_heads['head_media'], array( $this, 'wpat_display_section_media' ), '__FILE__' );		
            
            add_settings_field( 'admin_theme_wp_svg',                   $this->option_fields['wp_svg'],                         array( $this, 'admin_theme_wp_svg_settings' ),                  '__FILE__', 'admin_theme_section_media' ); // Add SVG Support            
            add_settings_field( 'admin_theme_wp_ico',                   $this->option_fields['wp_ico'],                         array( $this, 'admin_theme_wp_ico_settings' ),                  '__FILE__', 'admin_theme_section_media' ); // Add ICO Support
		
        // Add Section for Plugin Pages
		add_settings_section( 'admin_theme_section_plugin_pages', '<span id="index_page" class="wpat-page-index"></span>' . $this->option_heads['head_pages'], array( $this, 'wpat_display_section_plugin_pages' ), '__FILE__' );		
        
            add_settings_field( 'admin_theme_disable_page_system',      $this->option_fields['disable_page_system'],            array( $this, 'admin_theme_disable_plugin_pages_settings' ),    '__FILE__', 'admin_theme_section_plugin_pages' ); // Add Disable Plugin System Page
            add_settings_field( 'admin_theme_disable_page_export',      $this->option_fields['disable_page_export'],            array( $this, 'admin_theme_disable_plugin_pages_settings' ),    '__FILE__', 'admin_theme_section_plugin_pages' ); // Add Disable Plugin Import/Export Page
            add_settings_field( 'admin_theme_disable_page_ms',          $this->option_fields['disable_page_ms'],                array( $this, 'admin_theme_disable_plugin_pages_settings' ),    '__FILE__', 'admin_theme_section_plugin_pages' ); // Add Disable Plugin Multisite Sync Page
        
		// Add Section for Multisite Support
		add_settings_section( 'admin_theme_section_multisite', '<span id="index_ms" class="wpat-page-index"></span>' . $this->option_heads['head_ms'], array( $this, 'wpat_display_section_multisite' ), '__FILE__' );		
            
            add_settings_field( 'admin_theme_disable_theme_options',    $this->option_fields['disable_theme_options'],          array( $this, 'admin_theme_disable_theme_options_settings' ),   '__FILE__', 'admin_theme_section_multisite' ); // Add Disable Theme Options

		// Add Section for Optimization
		add_settings_section( 'admin_theme_section_optimization', '<span id="index_optimize" class="wpat-page-index"></span>' . $this->option_heads['head_optimize'], array( $this, 'wpat_display_section_optimization' ), '__FILE__' );		
            
            add_settings_field( 'admin_theme_wp_version_tag',           $this->option_fields['wp_version_tag'],                 array( $this, 'admin_theme_wp_optimization_settings' ),         '__FILE__', 'admin_theme_section_optimization' ); // Add Remove WP Version Tag            
            add_settings_field( 'admin_theme_wp_emoji',                 $this->option_fields['wp_emoji'],                       array( $this, 'admin_theme_wp_optimization_settings' ),         '__FILE__', 'admin_theme_section_optimization' ); // Add Remove WP Emoticons            
            add_settings_field( 'admin_theme_wp_feed_links',            $this->option_fields['wp_feed_links'],                  array( $this, 'admin_theme_wp_optimization_settings' ),         '__FILE__', 'admin_theme_section_optimization' ); // Add Remove WP Feed Links            
            add_settings_field( 'admin_theme_wp_rsd_link',              $this->option_fields['wp_rsd_link'],                    array( $this, 'admin_theme_wp_optimization_settings' ),         '__FILE__', 'admin_theme_section_optimization' ); // Add Remove WP RSD Link            
            add_settings_field( 'admin_theme_wp_wlwmanifest',           $this->option_fields['wp_wlwmanifest'],                 array( $this, 'admin_theme_wp_optimization_settings' ),         '__FILE__', 'admin_theme_section_optimization' ); // Add Remove WP Wlwmanifest            
            add_settings_field( 'admin_theme_wp_shortlink',             $this->option_fields['wp_shortlink'],                   array( $this, 'admin_theme_wp_optimization_settings' ),         '__FILE__', 'admin_theme_section_optimization' ); // Add Remove WP Shortlink            
            add_settings_field( 'admin_theme_wp_rest_api',              $this->option_fields['wp_rest_api'],                    array( $this, 'admin_theme_wp_optimization_settings' ),         '__FILE__', 'admin_theme_section_optimization' ); // Add Remove WP Rest API            
            add_settings_field( 'admin_theme_wp_oembed',                $this->option_fields['wp_oembed'],                      array( $this, 'admin_theme_wp_optimization_settings' ),         '__FILE__', 'admin_theme_section_optimization' ); // Add Remove WP oEmbed            
            add_settings_field( 'admin_theme_wp_xml_rpc',               $this->option_fields['wp_xml_rpc'],                     array( $this, 'admin_theme_wp_optimization_settings' ),         '__FILE__', 'admin_theme_section_optimization' ); // Add Remove WP XML RPC            
            add_settings_field( 'admin_theme_wp_heartbeat',             $this->option_fields['wp_heartbeat'],                   array( $this, 'admin_theme_wp_optimization_settings' ),         '__FILE__', 'admin_theme_section_optimization' ); // Add Remove WP Heartbeat            
            add_settings_field( 'admin_theme_wp_rel_link',              $this->option_fields['wp_rel_link'],                    array( $this, 'admin_theme_wp_optimization_settings' ),         '__FILE__', 'admin_theme_section_optimization' ); // Add Remove WP Rel Link          
            add_settings_field( 'admin_theme_wp_self_pingback',         $this->option_fields['wp_self_pingback'],               array( $this, 'admin_theme_wp_optimization_settings' ),         '__FILE__', 'admin_theme_section_optimization' ); // Add Disable Self Pingbacks Link

		// Add Section for Meta Boxes
		add_settings_section( 'admin_theme_section_meta_boxes', '<span id="index_metabox" class="wpat-page-index"></span>' . $this->option_heads['head_metabox'], array( $this, 'wpat_display_section_meta_boxes' ), '__FILE__' );		
		
            add_settings_field( 'admin_theme_mb_custom_fields',         $this->option_fields['mb_custom_fields'],               array( $this, 'admin_theme_meta_box_settings' ),                '__FILE__', 'admin_theme_section_meta_boxes' ); // Add Remove Custom Field Meta Box
            add_settings_field( 'admin_theme_mb_commentstatus',         $this->option_fields['mb_commentstatus'],               array( $this, 'admin_theme_meta_box_settings' ),                '__FILE__', 'admin_theme_section_meta_boxes' ); // Add Remove Comments Status Meta Box
            add_settings_field( 'admin_theme_mb_comments',              $this->option_fields['mb_comments'],                    array( $this, 'admin_theme_meta_box_settings' ),                '__FILE__', 'admin_theme_section_meta_boxes' ); // Add Remove Comments Meta Box
            add_settings_field( 'admin_theme_mb_author',                $this->option_fields['mb_author'],                      array( $this, 'admin_theme_meta_box_settings' ),                '__FILE__', 'admin_theme_section_meta_boxes' ); // Add Remove Author Meta Box
            add_settings_field( 'admin_theme_mb_category',              $this->option_fields['mb_category'],                    array( $this, 'admin_theme_meta_box_settings' ),                '__FILE__', 'admin_theme_section_meta_boxes' ); // Add Remove Category Meta Box
            add_settings_field( 'admin_theme_mb_format',                $this->option_fields['mb_format'],                      array( $this, 'admin_theme_meta_box_settings' ),                '__FILE__', 'admin_theme_section_meta_boxes' ); // Add Remove Post Format Meta Box
            add_settings_field( 'admin_theme_mb_pageparent',            $this->option_fields['mb_pageparent'],                  array( $this, 'admin_theme_meta_box_settings' ),                '__FILE__', 'admin_theme_section_meta_boxes' ); // Add Remove Page Parent Meta Box
            add_settings_field( 'admin_theme_mb_postexcerpt',           $this->option_fields['mb_postexcerpt'],                 array( $this, 'admin_theme_meta_box_settings' ),                '__FILE__', 'admin_theme_section_meta_boxes' ); // Add Remove Post Excerpt Meta Box
            add_settings_field( 'admin_theme_mb_postimage',             $this->option_fields['mb_postimage'],                   array( $this, 'admin_theme_meta_box_settings' ),                '__FILE__', 'admin_theme_section_meta_boxes' ); // Add Remove Post Image Meta Box
            add_settings_field( 'admin_theme_mb_revisions',             $this->option_fields['mb_revisions'],                   array( $this, 'admin_theme_meta_box_settings' ),                '__FILE__', 'admin_theme_section_meta_boxes' ); // Add Remove Revisions Meta Box
            add_settings_field( 'admin_theme_mb_slug',                  $this->option_fields['mb_slug'],                        array( $this, 'admin_theme_meta_box_settings' ),                '__FILE__', 'admin_theme_section_meta_boxes' ); // Add Remove Slug Meta Box
            add_settings_field( 'admin_theme_mb_tags',                  $this->option_fields['mb_tags'],                        array( $this, 'admin_theme_meta_box_settings' ),                '__FILE__', 'admin_theme_section_meta_boxes' ); // Add Remove Tags Meta Box
            add_settings_field( 'admin_theme_mb_trackbacks',            $this->option_fields['mb_trackbacks'],                  array( $this, 'admin_theme_meta_box_settings' ),                '__FILE__', 'admin_theme_section_meta_boxes' ); // Add Remove Trackbacks Meta Box

		// Add Section for Dashboard Widgets
		add_settings_section( 'admin_theme_section_db_widgets', '<span id="index_dashboard" class="wpat-page-index"></span>' . $this->option_heads['head_dashboard'], array( $this, 'wpat_display_section_db_widgets' ), '__FILE__' );	
		
            add_settings_field( 'admin_theme_dbw_quick_press',          $this->option_fields['dbw_quick_press'],                array( $this, 'admin_theme_db_widgets_settings' ),              '__FILE__', 'admin_theme_section_db_widgets' ); // Add Remove Quick Draft Widget
            add_settings_field( 'admin_theme_dbw_right_now',            $this->option_fields['dbw_right_now'],                  array( $this, 'admin_theme_db_widgets_settings' ),              '__FILE__', 'admin_theme_section_db_widgets' ); // Add Remove At the Glance Widget
            add_settings_field( 'admin_theme_dbw_activity',             $this->option_fields['dbw_activity'],                   array( $this, 'admin_theme_db_widgets_settings' ),              '__FILE__', 'admin_theme_section_db_widgets' ); // Add Remove Activity Widget
            add_settings_field( 'admin_theme_dbw_primary',              $this->option_fields['dbw_primary'],                    array( $this, 'admin_theme_db_widgets_settings' ),              '__FILE__', 'admin_theme_section_db_widgets' ); // Add Remove WP Events & News Widget
            add_settings_field( 'admin_theme_dbw_welcome',              $this->option_fields['dbw_welcome'],                    array( $this, 'admin_theme_db_widgets_settings' ),              '__FILE__', 'admin_theme_section_db_widgets' ); // Add Remove Welcome Widget
            add_settings_field( 'admin_theme_dbw_wpat_user_log',        $this->option_fields['dbw_wpat_user_log'],              array( $this, 'admin_theme_db_widgets_settings' ),              '__FILE__', 'admin_theme_section_db_widgets' ); // Add Remove WPAT User Activities Widget
            add_settings_field( 'admin_theme_dbw_wpat_sys_info',        $this->option_fields['dbw_wpat_sys_info'],              array( $this, 'admin_theme_db_widgets_settings' ),              '__FILE__', 'admin_theme_section_db_widgets' ); // Add Remove WPAT System info Widget
            add_settings_field( 'admin_theme_dbw_wpat_count_post',      $this->option_fields['dbw_wpat_count_post'],            array( $this, 'admin_theme_db_widgets_settings' ),              '__FILE__', 'admin_theme_section_db_widgets' ); // Add Remove WPAT Posts Count Widget
            add_settings_field( 'admin_theme_dbw_wpat_count_page',      $this->option_fields['dbw_wpat_count_page'],            array( $this, 'admin_theme_db_widgets_settings' ),              '__FILE__', 'admin_theme_section_db_widgets' ); // Add Remove WPAT Pages Count Widget
            add_settings_field( 'admin_theme_dbw_wpat_count_comment',   $this->option_fields['dbw_wpat_count_comment'],         array( $this, 'admin_theme_db_widgets_settings' ),              '__FILE__', 'admin_theme_section_db_widgets' ); // Add Remove WPAT Comments Count Widget
            add_settings_field( 'admin_theme_dbw_wpat_recent_post',     $this->option_fields['dbw_wpat_recent_post'],           array( $this, 'admin_theme_db_widgets_settings' ),              '__FILE__', 'admin_theme_section_db_widgets' ); // Add Remove WPAT Recent Posts Widget
            add_settings_field( 'admin_theme_dbw_wpat_recent_page',     $this->option_fields['dbw_wpat_recent_page'],           array( $this, 'admin_theme_db_widgets_settings' ),              '__FILE__', 'admin_theme_section_db_widgets' ); // Add Remove WPAT Recent Pages Widget
            add_settings_field( 'admin_theme_dbw_wpat_recent_comment',  $this->option_fields['dbw_wpat_recent_comment'],        array( $this, 'admin_theme_db_widgets_settings' ),              '__FILE__', 'admin_theme_section_db_widgets' ); // Add Remove WPAT Recent Comments Widget
            add_settings_field( 'admin_theme_dbw_wpat_memory',          $this->option_fields['dbw_wpat_memory'],                array( $this, 'admin_theme_db_widgets_settings' ),              '__FILE__', 'admin_theme_section_db_widgets' ); // Add Remove WPAT Memory Usage Widget

		// Add Section for Widgets
		add_settings_section( 'admin_theme_section_widgets', '<span id="index_widget" class="wpat-page-index"></span>' . $this->option_heads['head_widget'], array( $this, 'wpat_display_section_widgets' ), '__FILE__' );	
		
            add_settings_field( 'admin_theme_wt_pages',                 $this->option_fields['wt_pages'],                       array( $this, 'admin_theme_widgets_settings' ),                 '__FILE__', 'admin_theme_section_widgets' ); // Add Remove Pages Widget
            add_settings_field( 'admin_theme_wt_archives',              $this->option_fields['wt_archives'],                    array( $this, 'admin_theme_widgets_settings' ),                 '__FILE__', 'admin_theme_section_widgets' ); // Add Remove Calendar Widget
            add_settings_field( 'admin_theme_wt_calendar',              $this->option_fields['wt_calendar'],                    array( $this, 'admin_theme_widgets_settings' ),                 '__FILE__', 'admin_theme_section_widgets' ); // Add Remove Archives Widget
            add_settings_field( 'admin_theme_wt_meta',                  $this->option_fields['wt_meta'],                        array( $this, 'admin_theme_widgets_settings' ),                 '__FILE__', 'admin_theme_section_widgets' ); // Add Remove Meta Widget
            add_settings_field( 'admin_theme_wt_search',                $this->option_fields['wt_search'],                      array( $this, 'admin_theme_widgets_settings' ),                 '__FILE__', 'admin_theme_section_widgets' ); // Add Remove Search Widget
            add_settings_field( 'admin_theme_wt_text',                  $this->option_fields['wt_text'],                        array( $this, 'admin_theme_widgets_settings' ),                 '__FILE__', 'admin_theme_section_widgets' ); // Add Remove Text Widget
            add_settings_field( 'admin_theme_wt_categories',            $this->option_fields['wt_categories'],                  array( $this, 'admin_theme_widgets_settings' ),                 '__FILE__', 'admin_theme_section_widgets' ); // Add Remove Categories Widget
            add_settings_field( 'admin_theme_wt_recent_posts',          $this->option_fields['wt_recent_posts'],                array( $this, 'admin_theme_widgets_settings' ),                 '__FILE__', 'admin_theme_section_widgets' ); // Add Remove Recent Posts Widget
            add_settings_field( 'admin_theme_wt_recent_comments',       $this->option_fields['wt_recent_comments'],             array( $this, 'admin_theme_widgets_settings' ),                 '__FILE__', 'admin_theme_section_widgets' ); // Add Remove Recent Comments Widget
            add_settings_field( 'admin_theme_wt_rss',                   $this->option_fields['wt_rss'],                         array( $this, 'admin_theme_widgets_settings' ),                 '__FILE__', 'admin_theme_section_widgets' ); // Add Remove RSS Widget
            add_settings_field( 'admin_theme_wt_tag_cloud',             $this->option_fields['wt_tag_cloud'],                   array( $this, 'admin_theme_widgets_settings' ),                 '__FILE__', 'admin_theme_section_widgets' ); // Add Remove Tag Cloud Widget
            add_settings_field( 'admin_theme_wt_nav',                   $this->option_fields['wt_nav'],                         array( $this, 'admin_theme_widgets_settings' ),                 '__FILE__', 'admin_theme_section_widgets' ); // Add Remove Navigation Menu Widget
            add_settings_field( 'admin_theme_wt_image',                 $this->option_fields['wt_image'],                       array( $this, 'admin_theme_widgets_settings' ),                 '__FILE__', 'admin_theme_section_widgets' ); // Add Remove Image Widget
            add_settings_field( 'admin_theme_wt_audio',                 $this->option_fields['wt_audio'],                       array( $this, 'admin_theme_widgets_settings' ),                 '__FILE__', 'admin_theme_section_widgets' ); // Add Remove Audio Widget
            add_settings_field( 'admin_theme_wt_video',                 $this->option_fields['wt_video'],                       array( $this, 'admin_theme_widgets_settings' ),                 '__FILE__', 'admin_theme_section_widgets' ); // Add Remove Video Widget
            add_settings_field( 'admin_theme_wt_gallery',               $this->option_fields['wt_gallery'],                     array( $this, 'admin_theme_widgets_settings' ),                 '__FILE__', 'admin_theme_section_widgets' ); // Add Remove Gallery Widget
            add_settings_field( 'admin_theme_wt_html',                  $this->option_fields['wt_html'],                        array( $this, 'admin_theme_widgets_settings' ),                 '__FILE__', 'admin_theme_section_widgets' ); // Add Remove Custom HTML Widget
        
		// Add Section for Frontend
		add_settings_section( 'admin_theme_section_frontend', '<span id="index_frontend" class="wpat-page-index"></span>' . $this->option_heads['head_frontend'], array( $this, 'wpat_display_section_frontend' ), '__FILE__' );	
        
            add_settings_field( 'admin_theme_wp_header_code',           $this->option_fields['wp_header_code'],                 array( $this, 'admin_theme_frontend_settings' ),                '__FILE__', 'admin_theme_section_frontend' ); // Add Header Code
            add_settings_field( 'admin_theme_wp_footer_code',           $this->option_fields['wp_footer_code'],                 array( $this, 'admin_theme_frontend_settings' ),                '__FILE__', 'admin_theme_section_frontend' ); // Add Footer Code
            add_settings_field( 'admin_theme_meta_referrer_policy',     $this->option_fields['meta_referrer_policy'],           array( $this, 'admin_theme_frontend_settings' ),                '__FILE__', 'admin_theme_section_frontend' ); // Add Meta Policy
        
	}
    
 
    /*****************************************************************/
    /* ADD JS FOR WPAT PLUGIN OPTIONS PAGE */
    /*****************************************************************/
    
	function wpat_load_plugin_page_specific_scripts( $hook ) {
		
		// method to get the page hook
		// wp_die($hook);

		// Load only on admin_toplevel_page?page=mypluginname
		if( $hook != 'tools_page_wpat' ) {
			return;
		}
		
		// Add color picker css
		wp_enqueue_style( 'wp-color-picker' );
		
		// Add media upload js
		wp_enqueue_media();
		
		// Add plugin js		
		wp_enqueue_script( 'wp_admin_script_plugin', wpat_path( 'js/jquery.plugin.js' ), array( 'jquery', 'wp-color-picker' ), null, true );

	}
    
	
    /*****************************************************************/
    /* ADD GLOBAL JS / CSS */
    /*****************************************************************/
    
    public function wpat_enqueue_admin_js() {
		
		// Add admin style css
		wp_enqueue_style( 'wp_admin_style_custom', wpat_path( 'style.css' ), array(), filemtime( wpat_dir( 'style.css' ) ), 'all' );
		
        // Add admin rtl style css
		if( is_rtl() ) {			
			wp_enqueue_style( 'wp_admin_style_rtl', wpat_path( 'css/rtl-style.css' ), array(), filemtime( wpat_dir( 'css/rtl-style.css' ) ), 'all' );		
		}
			
		// Add admin colors css
        wp_enqueue_style( 'wp_admin_style_color', wpat_path( 'css/colors.css' ), array(), filemtime( wpat_dir( 'css/colors.css' ) ), 'all' );
		
		// Add admin js		
		wp_enqueue_script( 'wp_admin_script_custom', wpat_path( 'js/jquery.custom.js' ), array( 'jquery' ), null, true );
		
		// Avoiding flickering to reorder the first menu item (User Box) for left toolbar
		$custom_css = "#adminmenu li:first-child { display:none }";
        wp_add_inline_style( 'wp_admin_style_custom', $custom_css );
		
	}
 
    
    /*****************************************************************/
    /* GENERATE WPAT PLUGIN CUSTOM CSS / JS FILE */
    /*****************************************************************/
    
    public function wpat_generate_custom_css_js() {
		   
		global $wp_filesystem;
		WP_Filesystem(); // Initial WP file system

		ob_start();
		require_once( wpat_dir('css/colors.php') );
		$css = ob_get_clean();
		$wp_filesystem->put_contents( wpat_dir('css/colors.css'), $css, 0644 );
		
		ob_start();
		require_once( wpat_dir('css/login.php') );
		$css = ob_get_clean();
		$wp_filesystem->put_contents( wpat_dir('css/login.css'), $css, 0644 );
		
		ob_start();
		require_once( wpat_dir('css/frontend.php') );
		$css = ob_get_clean();
		$wp_filesystem->put_contents( wpat_dir('css/frontend.css'), $css, 0644 );
        
    }
    
    
    /*****************************************************************/
    /* VALIDATE ALL WPAT PLUGIN OPTION FIELDS */
    /*****************************************************************/
    
    public function wpat_validate_options( $fields ) {
        
        $valid_fields = array();
       
        // validate the following fields        
        $get_all_fields = $this->option_fields;        
        
        foreach( $get_all_fields as $key => $value ) {
               
            $field_type = trim( $fields[ $key ] );            
            
            // extra check for color fields
            if( $key == 'theme_color' || $key == 'theme_background' || $key == 'theme_background_end' ) {
            
                // check color is empty (or cleared by user)
                if( $field_type == false ) {
                    // empty value
                    $valid_fields[ $key ] = '';
                    
                // check if is a valid hex color    
                } elseif( false == $this->wpat_check_color( $field_type ) ) {                    
                    
                    if( $key == 'theme_color' ) {
                        $valid_fields[ $key ] = $this->options['theme_color'];
                    } elseif( $key == 'theme_background' ) {
                        $valid_fields[ $key ] = $this->options['theme_background'];   
                    } else {
                        $valid_fields[ $key ] = $this->options['theme_background_end'];   
                    }
                    
                    // Invalid color notice
                    if( ! empty( $field_type ) ) {
                        add_settings_error('wpat_settings_options', 'save_updated', esc_html__( 'Invalid Color for', 'wpat' ) . ' ' . $value . '! ' . esc_html__( 'Old values has been restored,', 'wpat' ), 'error' );
                    } 
                    
                // get validated new hex code
                } else {
                    
                    $valid_fields[ $key ] = $field_type;
                    
                }
                
            } else {
                
                // validate all other fields
                if( $key == 'wp_header_code' || $key == 'wp_footer_code' ) {
                    $valid_fields[ $key ] = $field_type;
                } else {
                    $valid_fields[ $key ] = strip_tags( stripslashes( $field_type ) );    
                }                
                
            }
            
            // get specific update notice
            if( $valid_fields[ $key ] == $this->options[ $key ] ) {
                // specific field has been not updated (new value == old value)
                //add_settings_error('wpat_settings_options', 'save_updated', esc_html__( 'nichts geändert', 'wpat' ), 'error' );
            } else {
                // specific field has been updated
                if( $field_type == $valid_fields[ $key ] ) {
                    add_settings_error('wpat_settings_options', 'save_updated', $value . ' ' . esc_html__( 'has been updated.', 'wpat' ), 'updated' );
                }
            }
            
        }
        
        // Reset all fields to default theme options
		if( isset( $_POST['reset'] ) ) {
            
        	add_settings_error('wpat_settings_options', 'reset_error', esc_html__( 'All fields has been restored.', 'wpat' ), 'updated' );
			
            // Restore all options to pre defined values
            return $this->pre_options;
            
    	}
		        
		add_settings_error('wpat_settings_options', 'save_updated', esc_html__('Settings saved.', 'wpat'), 'updated' );
		
		// Validate all
		return apply_filters( 'wpat_validate_options', $valid_fields, $fields);
				
	}
    
    
    /*****************************************************************/
    /* VALIDATE HEX CODE */
    /*****************************************************************/
 
    // Function that will check if value is a valid HEX color.
    public function wpat_check_color( $value ) {
 
		if( preg_match( '/^#[a-f0-9]{6}$/i', $value ) ) { // if user insert a HEX color with #
			return true;
		}

		return false;
	}
    
    
    /*****************************************************************/
    /* PRE-DEFINE OF UNDEFINED INDEX */
    /*****************************************************************/
 
    public function wpat_check_for_undefined_options() {
        
		// get pre and indexed option field names                                   
        $pre_defined_fields = $this->pre_options; 
        $wp_indexed_fields = $this->options;

        // check indexed option is array
        if( ! is_array( $wp_indexed_fields ) ) $wp_indexed_fields = array();
        else $wp_indexed_fields = $wp_indexed_fields;
        
        // get undefined fields (not in index)
        $diff_result = array_diff_key( $pre_defined_fields, $wp_indexed_fields );
        
        if( is_multisite() ) {
            $get_option = get_blog_option( get_current_blog_id(), 'wpat_settings_options', array() );
        } else {
            $get_option = get_option( 'wpat_settings_options' );
        }
        
        foreach( $diff_result as $key => $value ) {
            
            // Add undefined option key
            $get_option[$key] = '';

            // Update options
            if( is_multisite() ) {
                update_blog_option( get_current_blog_id(), 'wpat_settings_options', $get_option);
            } else {
                update_option('wpat_settings_options', $get_option);
            }
            
        }
      
	}
    

    public function wpat_display_section() {
        /* Leave blank */
	}
	
	public function wpat_display_section_toolbar() { 
		/* Leave blank */ 
	}
	
	public function wpat_display_section_colors() {
		/* Leave blank */ 
	}
	
	public function wpat_display_section_login() { 
		/* Leave blank */ 
	}
	
	public function wpat_display_section_footer() { 
		/* Leave blank */ 
	}
	
	public function wpat_display_section_css() { 
		/* Leave blank */ 
	}
	
	public function wpat_display_section_media() { 
		/* Leave blank */ 
	}
	
	public function wpat_display_section_plugin_pages() { 
		/* Leave blank */ 
	}
	
	public function wpat_display_section_multisite() { 
		/* Leave blank */ 
	}
	
	public function wpat_display_section_optimization() { 
		/* Leave blank */ 
	}
	
	public function wpat_display_section_meta_boxes() { 
		/* Leave blank */ 
	}
	
	public function wpat_display_section_db_widgets() { 
		/* Leave blank */ 
	}
	
	public function wpat_display_section_widgets() { 
		/* Leave blank */ 
	}
	
	public function wpat_display_section_frontend() { 
		/* Leave blank */ 
	}
    
	
    /*****************************************************************/
    /* DISPLAY THE WPAT PLUGIN OPTION PAGES SETTINGS FIELDS */
    /*****************************************************************/
    	
	// user box
	
	public function admin_theme_user_box_settings() {
        
		if( $this->options['user_box'] ) $checked = ' checked="checked" ';  
		else $checked = '';
        
		global $user_box_is_hidden;
        $user_box_is_hidden = $this->options['user_box'];
        
		if( ! $this->options['user_box'] ) { 
			$field_status = '<span class="field-status visible">' . esc_html__( 'Visible', 'wpat' ) . '</span>';
 		} else {
			$field_status = '<span class="field-status hidden">' . esc_html__( 'Hidden', 'wpat' ) . '</span>';
		}
		
		echo '<input type="checkbox" ' . $checked . ' id="user_box" name="wpat_settings_options[user_box]" />';
		
		echo '<label for="user_box">' . esc_html__( 'Hide', 'wpat' ) . $field_status . '</label>';
		
		echo '<p class="description">' . esc_html__( 'Display the user avatar and name before the left wordpress admin menu', 'wpat' ) . '.</p>';
		
	}
    	
	// company box
	
	public function admin_theme_company_box_settings() {
		
		if( $this->options['company_box'] ) $checked = ' checked="checked" ';  
		else $checked = '';
        
		global $user_box_is_hidden;
        
        if( $user_box_is_hidden ) { 
            echo '<div class="wpat-inactive-option">';
        }
        
            if( ! $this->options['company_box']) { 
                $field_status = '<span class="field-status hidden">' . esc_html__( 'Disabled', 'wpat' ) . '</span>';
            } else {
                $field_status = '<span class="field-status visible">' . esc_html__( 'Enabled', 'wpat' ) . '</span>';
            }

            echo '<input type="checkbox" ' . $checked . ' id="company_box" name="wpat_settings_options[company_box]" />';

            echo '<label for="company_box">' . esc_html__( 'Enable', 'wpat' ) . $field_status . '</label>';

            echo '<p class="description">' . esc_html__( 'Show a company box with your logo instead of the user box. The user box must be visible', 'wpat' ) . '.</p>';

            /*******/
            echo '<br>';

            $val = ( isset( $this->options['company_box_logo'] ) ) ? $this->options['company_box_logo'] : '';
            $val2 = ( isset( $this->options['company_box_logo_size'] ) ) ? $this->options['company_box_logo_size'] : '140';

            echo '<label for="company_box_logo">' . esc_html__( 'Company Logo', 'wpat' ) . ' </label>';
            echo '<input type="text" id="company_box_logo" name="wpat_settings_options[company_box_logo]" value="' . $val . '" />'; 
            echo '<input id="company_box_logo_upload_button" class="button uploader" type="button" value="' . esc_html__( 'Upload Image', 'wpat' ) . '" /> ';

            echo '<label class="wpat-nextto-input" for="company_box_logo_size" style="margin-left: 30px">' . esc_html__( 'Logo Size', 'wpat' ) . ' </label>';
			echo '<input class="wpat-range-value"  type="range" id="company_box_logo_size" name="wpat_settings_options[company_box_logo_size]" value="' . $val2 . '" min="100" max="300" />';
			echo '<span class="wpat-input-range"><span>140</span></span>';
            echo '<label for="company_box_logo_size"> ' . esc_html__( 'Pixel', 'wpat' ) . '</label>';

            if( $this->options['company_box_logo'] ) {
                $bg_image = $this->options['company_box_logo'];
            } else {
                $bg_image = wpat_path('img/no-thumb.jpg');
            }

            echo '<div class="img-upload-container" style="background-image:url(' . $bg_image . ')"></div>';
		
        if( $user_box_is_hidden ) { 
            echo '</div>';
        }
        
	}
	
	// thumbnails
	
	public function admin_theme_thumbnail_settings() {
		
		if( $this->options['thumbnail'] ) $checked = ' checked="checked" '; 
		else $checked = '';
		
		if( ! $this->options['thumbnail']) { 
			$field_status = '<span class="field-status visible">' . esc_html__( 'Visible', 'wpat' ) . '</span>';
 		} else {
			$field_status = '<span class="field-status hidden">' . esc_html__( 'Hidden', 'wpat' ) . '</span>';
		}
		
		echo '<input type="checkbox" ' . $checked . ' id="thumbnail" name="wpat_settings_options[thumbnail]" />';
		
		echo '<label for="thumbnail">' . esc_html__( 'Hide', 'wpat' ) . $field_status . '</label>';
		
		echo '<p class="description">' . esc_html__( 'Display a thumbnail column before the title for post and page table lists', 'wpat' ) . '.</p>';
		
	}
	
	// post/page ids
	
	public function admin_theme_post_page_id_settings() {
		
		if( $this->options['post_page_id'] ) $checked = ' checked="checked" '; 
		else $checked = '';
		
		if( ! $this->options['post_page_id']) { 
			$field_status = '<span class="field-status visible">' . esc_html__( 'Visible', 'wpat' ) . '</span>';
 		} else {
			$field_status = '<span class="field-status hidden">' . esc_html__( 'Hidden', 'wpat' ) . '</span>';
		}
		
		echo '<input type="checkbox" ' . $checked . ' id="post_page_id" name="wpat_settings_options[post_page_id]" />';
		
		echo '<label for="post_page_id">' . esc_html__( 'Hide', 'wpat' ) . $field_status . '</label>';
		
		echo '<p class="description">' . esc_html__( 'Display a IDs column for post and page table lists', 'wpat' ) . '.</p>';
		
	}
	
	// hide contextual help
	
	public function admin_theme_hide_help_settings() {
		
		if( $this->options['hide_help'] ) $checked = ' checked="checked" '; 
		else $checked = '';
		
		if( ! $this->options['hide_help']) { 
			$field_status = '<span class="field-status visible">' . esc_html__( 'Visible', 'wpat' ) . '</span>';
 		} else {
			$field_status = '<span class="field-status hidden">' . esc_html__( 'Hidden', 'wpat' ) . '</span>';
		}
		
		echo '<input type="checkbox" ' . $checked . ' id="hide_help" name="wpat_settings_options[hide_help]" />';
		
		echo '<label for="hide_help">' . esc_html__( 'Hide', 'wpat' ) . $field_status . '</label>';
		
		echo '<p class="description">' . esc_html__( 'Hide the contextual help at the top right side', 'wpat' ) . '.</p>';
		
	}
	
	// hide screen options
	
	public function admin_theme_hide_screen_option_settings() {
		
		if( $this->options['hide_screen_option'] ) $checked = ' checked="checked" '; 
		else $checked = '';
		
		if( ! $this->options['hide_screen_option']) { 
			$field_status = '<span class="field-status visible">' . esc_html__( 'Visible', 'wpat' ) . '</span>';
 		} else {
			$field_status = '<span class="field-status hidden">' . esc_html__( 'Hidden', 'wpat' ) . '</span>';
		}
		
		echo '<input type="checkbox" ' . $checked . ' id="hide_screen_option" name="wpat_settings_options[hide_screen_option]" />';
		
		echo '<label for="hide_screen_option">' . esc_html__( 'Hide', 'wpat' ) . $field_status . '</label>';
		
		echo '<p class="description">' . esc_html__( 'Hide the screen options at the top right side', 'wpat' ) . '.</p>';
		
	}
	
	// left menu width
	
	public function admin_theme_left_menu_width_settings() {
		
		$val = ( isset( $this->options['left_menu_width'] ) ) ? $this->options['left_menu_width'] : '200';
		
		echo '<input class="wpat-range-value" type="range" id="left_menu_width" name="wpat_settings_options[left_menu_width]" value="' . $val . '" min="200" max="400" />';
		echo '<span class="wpat-input-range"><span>200</span></span>';
		echo '<label for="left_menu_width"> ' . esc_html__( 'Pixel', 'wpat' ) . '</label>';
		
		echo '<p class="description">' . esc_html__( 'Increase the left admin menu width up to 400px', 'wpat' ) . '.</p>';
		
	}
	
	// left menu expandable options
	
	public function admin_theme_left_menu_expand_settings() {
		
		if( $this->options['left_menu_expand'] ) $checked = ' checked="checked" '; 
		else $checked = '';
		
		if( ! $this->options['left_menu_expand']) { 
			$field_status = '<span class="field-status hidden">' . esc_html__( 'Disabled', 'wpat' ) . '</span>';
 		} else {
			$field_status = '<span class="field-status visible">' . esc_html__( 'Enabled', 'wpat' ) . '</span>';
		}
		
		echo '<input type="checkbox" ' . $checked . ' id="left_menu_expand" name="wpat_settings_options[left_menu_expand]" />';
		
		echo '<label for="left_menu_expand">' . esc_html__( 'Enable', 'wpat' ) . $field_status . '</label>';
		
		echo '<p class="description">' . esc_html__( 'Display submenus of the left admin menu only after clicking as an expandable menu', 'wpat' ) . '.</p>';
		
	}
	
	// spacing
	
	public function admin_theme_spacing_settings() {
		
		if( $this->options['spacing'] ) $checked = ' checked="checked" '; 
		else $checked = '';        
        
        $val = ( isset( $this->options['spacing_max_width'] ) ) ? $this->options['spacing_max_width'] : '2000';
		
        global $spacing_is_disabled;
        $spacing_is_disabled = $this->options['spacing'];
        
		if( ! $this->options['spacing']) { 
			$field_status = '<span class="field-status visible">' . esc_html__( 'Enabled', 'wpat' ) . '</span>';
 		} else {
			$field_status = '<span class="field-status hidden">' . esc_html__( 'Disabled', 'wpat' ) . '</span>';
		}
		
		echo '<input type="checkbox" ' . $checked . ' id="spacing" name="wpat_settings_options[spacing]" />';
		
		echo '<label for="spacing">' . esc_html__( 'Disable', 'wpat' ) . $field_status . '</label>';
		
		echo '<p class="description">' . esc_html__( 'Remove the spacing around the backend block', 'wpat' ) . '.</p>';

        if( $spacing_is_disabled ) { 
            echo '<div class="wpat-inactive-option">';
        }
        
            /*******/
            echo '<br>';

            echo '<label class="wpat-nextto-input" for="spacing_max_width">' . esc_html__( 'Max Width', 'wpat' ) . ' </label>';
            echo '<input class="wpat-range-value"  type="range" id="spacing_max_width" name="wpat_settings_options[spacing_max_width]" value="' . $val . '" min="1000" max="2600" />';
			echo '<span class="wpat-input-range"><span>2000</span></span>';
            echo '<label for="spacing_max_width"> ' . esc_html__( 'Pixel', 'wpat' ) . '</label>';
        
        if( $spacing_is_disabled ) { 
            echo '</div>';
        }
		
	}
	
	// credits
	
	public function admin_theme_credits_settings() {
		
		if( $this->options['credits'] ) $checked = ' checked="checked" '; 
		else $checked = '';
		
		if( ! $this->options['credits']) { 
			$field_status = '<span class="field-status visible">' . esc_html__( 'Visible', 'wpat' ) . '</span>';
 		} else {
			$field_status = '<span class="field-status hidden">' . esc_html__( 'Hidden', 'wpat' ) . '</span>';
		}
		
		echo '<input type="checkbox" ' . $checked . ' id="credits" name="wpat_settings_options[credits]" />';
		
		echo '<label for="credits">' . esc_html__( 'Hide', 'wpat' ) . $field_status . '</label>';
		
		echo '<p class="description">' . esc_html__( 'Remove the credits note from the footer', 'wpat' ) . '.</p>';
		
	}
	
	// google webfont
	
	public function admin_theme_google_webfont_settings() {

		$val = ( isset( $this->options['google_webfont'] ) ) ? $this->options['google_webfont'] : '';
		$val2 = ( isset( $this->options['google_webfont_weight'] ) ) ? $this->options['google_webfont_weight'] : '';
		
		echo '<p><input type="text" id="google_webfont" name="wpat_settings_options[google_webfont]" value="' . $val . '" size="60" placeholder="Open+Sans" />';
		
		echo '&nbsp;&nbsp;<label for="google_webfont">' . esc_html__( 'Font-Family', 'wpat' ) . '</label></p>';
		
		echo '<p><input type="text" id="google_webfont_weight" name="wpat_settings_options[google_webfont_weight]" value="' . $val2 . '" size="60" placeholder="300,400,400i,700" />';
		
		echo '&nbsp;&nbsp;<label for="google_webfont_weight">' . esc_html__( 'Font-Weight', 'wpat' ) . '</label></p>';
		
		echo '<p class="description">' . wp_kses( __( 'Embed a custom <a target="_blank" href="https://fonts.google.com/">Google Webfont</a> to your WordPress Admin', 'wpat' ), 
			array(  
				'a' => array( 
					'href' => array(),
					'target' => array(),
				) 
			)
		) . '.</p>';
		
		echo '<small class="wpat-info">' . esc_html__( 'Please separate in Font-Name and Font-Weight like this example: [Font-Family = "Roboto"] and [Font-Weight = "400,400i,700"]', 'wpat' ) . '</small>';
		
	}
	
	// toolbar
	
	public function admin_theme_toolbar_settings() {
		
		if( $this->options['toolbar'] ) $checked = ' checked="checked" '; 
		else $checked = '';
		
        global $toolbar_is_hidden;
        $toolbar_is_hidden = $this->options['toolbar'];
        
		if( ! $this->options['toolbar']) { 
			$field_status = '<span class="field-status visible">' . esc_html__( 'Visible', 'wpat' ) . '</span>';
 		} else {
			$field_status = '<span class="field-status hidden">' . esc_html__( 'Hidden', 'wpat' ) . '</span>';
		}
		
		echo '<input type="checkbox" ' . $checked . ' id="toolbar" name="wpat_settings_options[toolbar]" />';
		
		echo '<label for="toolbar">' . esc_html__( 'Hide', 'wpat' ) . $field_status . '</label>';
		
		echo '<p class="description">' . esc_html__( 'Remove the upper toolbar', 'wpat' ) . '.</p>';
		
	}
	
	// toolbar comments menu
	
	public function admin_theme_hide_adminbar_comments_settings() {

        if( $this->options['hide_adminbar_comments'] ) $checked = ' checked="checked" '; 
        else $checked = '';
		
        global $toolbar_is_hidden;
        
        if( $toolbar_is_hidden ) { 
            echo '<div class="wpat-inactive-option">';
        }

            if( ! $this->options['hide_adminbar_comments']) { 
                $field_status = '<span class="field-status visible">' . esc_html__( 'Visible', 'wpat' ) . '</span>';
            } else {
                $field_status = '<span class="field-status hidden">' . esc_html__( 'Hidden', 'wpat' ) . '</span>';
            }

            echo '<input type="checkbox" ' . $checked . ' id="hide_adminbar_comments" name="wpat_settings_options[hide_adminbar_comments]" />';

            echo '<label for="hide_adminbar_comments">' . esc_html__( 'Hide', 'wpat' ) . $field_status . '</label>';

            echo '<p class="description">' . esc_html__( 'Remove the WordPress Comments Menu from the upper toolbar', 'wpat' ) . '.</p>';
		
        if( $toolbar_is_hidden ) { 
            echo '</div>';
        }
        
	}
	
	// toolbar new content menu
	
	public function admin_theme_hide_adminbar_new_settings() {

        if( $this->options['hide_adminbar_new'] ) $checked = ' checked="checked" '; 
        else $checked = '';
		
        global $toolbar_is_hidden;
        
        if( $toolbar_is_hidden ) { 
            echo '<div class="wpat-inactive-option">';
        }

            if( ! $this->options['hide_adminbar_new']) { 
                $field_status = '<span class="field-status visible">' . esc_html__( 'Visible', 'wpat' ) . '</span>';
            } else {
                $field_status = '<span class="field-status hidden">' . esc_html__( 'Hidden', 'wpat' ) . '</span>';
            }

            echo '<input type="checkbox" ' . $checked . ' id="hide_adminbar_new" name="wpat_settings_options[hide_adminbar_new]" />';

            echo '<label for="hide_adminbar_new">' . esc_html__( 'Hide', 'wpat' ) . $field_status . '</label>';

            echo '<p class="description">' . esc_html__( 'Remove the WordPress New Content Menu from the upper toolbar', 'wpat' ) . '.</p>';
		
        if( $toolbar_is_hidden ) { 
            echo '</div>';
        }
        
	}
	
	// toolbar customize link
	
	public function admin_theme_hide_adminbar_customize_settings() {

        if( $this->options['hide_adminbar_customize'] ) $checked = ' checked="checked" '; 
        else $checked = '';
		
        global $toolbar_is_hidden;
        
        if( $toolbar_is_hidden ) { 
            echo '<div class="wpat-inactive-option">';
        }

            if( ! $this->options['hide_adminbar_customize']) { 
                $field_status = '<span class="field-status visible">' . esc_html__( 'Visible', 'wpat' ) . '</span>';
            } else {
                $field_status = '<span class="field-status hidden">' . esc_html__( 'Hidden', 'wpat' ) . '</span>';
            }

            echo '<input type="checkbox" ' . $checked . ' id="hide_adminbar_customize" name="wpat_settings_options[hide_adminbar_customize]" />';

            echo '<label for="hide_adminbar_customize">' . esc_html__( 'Hide', 'wpat' ) . $field_status . '</label>';

            echo '<p class="description">' . esc_html__( 'Remove the WordPress Customize Link from the upper frontend toolbar', 'wpat' ) . '.</p>';
		
        if( $toolbar_is_hidden ) { 
            echo '</div>';
        }
        
	}
	
	// toolbar search
	
	public function admin_theme_hide_adminbar_search_settings() {

        if( $this->options['hide_adminbar_search'] ) $checked = ' checked="checked" '; 
        else $checked = '';
		
        global $toolbar_is_hidden;
        
        if( $toolbar_is_hidden ) { 
            echo '<div class="wpat-inactive-option">';
        }

            if( ! $this->options['hide_adminbar_search']) { 
                $field_status = '<span class="field-status visible">' . esc_html__( 'Visible', 'wpat' ) . '</span>';
            } else {
                $field_status = '<span class="field-status hidden">' . esc_html__( 'Hidden', 'wpat' ) . '</span>';
            }

            echo '<input type="checkbox" ' . $checked . ' id="hide_adminbar_search" name="wpat_settings_options[hide_adminbar_search]" />';

            echo '<label for="hide_adminbar_search">' . esc_html__( 'Hide', 'wpat' ) . $field_status . '</label>';

            echo '<p class="description">' . esc_html__( 'Remove the WordPress Search from the upper frontend toolbar', 'wpat' ) . '.</p>';
		
        if( $toolbar_is_hidden ) { 
            echo '</div>';
        }
        
	}
	
	// toolbar wp icon
	
	public function admin_theme_toolbar_wp_icon_settings() {

        if( $this->options['toolbar_wp_icon'] ) $checked = ' checked="checked" '; 
        else $checked = '';
		
        global $toolbar_is_hidden, $toolbar_wp_icon_is_hidden;
        $toolbar_wp_icon_is_hidden = $this->options['toolbar_wp_icon'];
        
        if( $toolbar_is_hidden ) { 
            echo '<div class="wpat-inactive-option">';
        }

            if( ! $this->options['toolbar_wp_icon']) { 
                $field_status = '<span class="field-status visible">' . esc_html__( 'Visible', 'wpat' ) . '</span>';
            } else {
                $field_status = '<span class="field-status hidden">' . esc_html__( 'Hidden', 'wpat' ) . '</span>';
            }

            echo '<input type="checkbox" ' . $checked . ' id="toolbar_wp_icon" name="wpat_settings_options[toolbar_wp_icon]" />';

            echo '<label for="toolbar_wp_icon">' . esc_html__( 'Hide', 'wpat' ) . $field_status . '</label>';

            echo '<p class="description">' . esc_html__( 'Remove the WordPress Menu and Icon from the upper toolbar', 'wpat' ) . '.</p>';
		
        if( $toolbar_is_hidden ) { 
            echo '</div>';
        }
        
	}
	
	// toolbar custom icon
	
	public function admin_theme_toolbar_icon_settings() {
		
		$val = ( isset( $this->options['toolbar_icon'] ) ) ? $this->options['toolbar_icon'] : '';
		
        global $toolbar_is_hidden, $toolbar_wp_icon_is_hidden;
        
        if( $toolbar_is_hidden || $toolbar_wp_icon_is_hidden ) { 
            echo '<div class="wpat-inactive-option">';
        }
		
            echo '<input type="text" id="toolbar_icon" name="wpat_settings_options[toolbar_icon]" value="' . $val . '" />'; 
            echo '<input id="toolbar_icon_upload_button" class="button uploader" type="button" value="' . esc_html__( 'Upload Image', 'wpat' ) . '" /> ';

            if( $this->options['toolbar_icon'] ) {
                $bg_image = $this->options['toolbar_icon'];
            } else {
                $bg_image = wpat_path('img/no-thumb.jpg');
            }

            echo '<div class="img-upload-container" style="background-image:url(' . $bg_image . ')"></div>';
            echo '<p class="description">' . esc_html__( 'Upload a custom icon instead of the WordPress icon', 'wpat' ) . '.</p>';

            echo '<small class="wpat-info">' . esc_html__( 'Recommended image size is 26 x 26px.', 'wpat' ) . '</small>';
		
        if( $toolbar_is_hidden || $toolbar_wp_icon_is_hidden ) { 
            echo '</div>';
        }
		
	}
 
	// theme color
	
    public function admin_theme_color_settings() {

		$val = ( isset( $this->options['theme_color'] ) ) ? $this->options['theme_color'] : '#4777CD';
		echo '<input type="text" name="wpat_settings_options[theme_color]" value="' . $val . '" class="cpa-color-picker" >';
		echo '<p class="description">' . esc_html__( 'Select your custom WPAT color. Default value is #4777CD', 'wpat' ) . '.</p>';
		
	}
	
	// theme gradient start + end color
	
    public function admin_theme_background_settings() {

		$val = ( isset( $this->options['theme_background'] ) ) ? $this->options['theme_background'] : '#545c63';
		echo '<input type="text" name="wpat_settings_options[theme_background]" value="' . $val . '" class="cpa-color-picker" >';
		echo '<label for="theme_background" class="color-picker">' . esc_html__( 'Start Color', 'wpat' ) . '</label>';
		
		$val2 = ( isset( $this->options['theme_background_end'] ) ) ? $this->options['theme_background_end'] : '#32373c';
		echo '<input type="text" name="wpat_settings_options[theme_background_end]" value="' . $val2 . '" class="cpa-color-picker" >';
		echo '<label for="theme_background_end" class="color-picker">' . esc_html__( 'End Color', 'wpat' ) . '</label>';
		
		echo '<p class="description">' . esc_html__( 'Select your custom WPAT background gradient color. Default start value is #545c63 and end value is #32373c', 'wpat' ) . '.</p>';
		
	}
	
	// login disable
	
	public function admin_theme_login_disable_settings() {
		
		if( $this->options['login_disable'] ) $checked = ' checked="checked" '; 
		else $checked = '';
		
        global $login_is_disabled;
        $login_is_disabled = $this->options['login_disable'];
		
		if( ! $this->options['login_disable']) { 
			$field_status = '<span class="field-status visible">' . esc_html__( 'Enabled', 'wpat' ) . '</span>';
 		} else {
			$field_status = '<span class="field-status hidden">' . esc_html__( 'Disabled', 'wpat' ) . '</span>';
		}
		
		echo '<input type="checkbox" ' . $checked . ' id="login_disable" name="wpat_settings_options[login_disable]" />';
		
		echo '<label for="login_disable">' . esc_html__( 'Disable', 'wpat' ) . $field_status . '</label>';
		
		echo '<p class="description">' . esc_html__( 'It is useful if you have an other login plugin installed. This is preventing conflicts with other plugins', 'wpat' ) . '.</p>';
		
	}
	
	// login title
	
	public function admin_theme_login_title_settings() {
		
        $val = ( isset( $this->options['login_title'] ) ) ? $this->options['login_title'] : 'Welcome back.';
        
        global $login_is_disabled;
        
        if( $login_is_disabled ) { 
            echo '<div class="wpat-inactive-option">';
        }
        
            echo '<input type="text" name="wpat_settings_options[login_title]" value="' . $val . '" size="60" />';
		
        if( $login_is_disabled ) { 
            echo '</div>';
        }
        
	}
 
	// login logo and size
	
    public function admin_theme_logo_upload_settings() {
		
		$val = ( isset( $this->options['logo_upload'] ) ) ? $this->options['logo_upload'] : '';
		$val2 = ( isset( $this->options['logo_size'] ) ) ? $this->options['logo_size'] : '200';
		
        global $login_is_disabled;
        
        if( $login_is_disabled ) { 
            echo '<div class="wpat-inactive-option">';
        }
        
            echo '<input type="text" id="logo_upload" name="wpat_settings_options[logo_upload]" value="' . $val . '" />'; 
            echo '<input id="logo_upload_button" class="button uploader" type="button" value="' . esc_html__( 'Upload Image', 'wpat' ) . '" /> ';
        
            echo '<label class="wpat-nextto-input" for="logo_size" style="margin-left: 30px">' . esc_html__( 'Logo Size', 'wpat' ) . ' </label>';
			echo '<input class="wpat-range-value"  type="range" id="logo_size" name="wpat_settings_options[logo_size]" value="' . $val2 . '" min="100" max="400" />';
			echo '<span class="wpat-input-range"><span>200</span></span>';
            echo '<label for="logo_size" class="logo-size"> ' . esc_html__( 'Pixel', 'wpat' ) . '</label>';

            if( $this->options['logo_upload'] ) {
                $logo_image = $this->options['logo_upload'];
            } else {
                $logo_image = wpat_path('img/no-thumb.jpg');
            }

            echo '<div class="img-upload-container" style="background-image:url(' . $logo_image . ')"></div>';
            echo '<p class="description">' . esc_html__( 'Upload an image for your WordPress login page', 'wpat' ) . '.</p>';
		
        if( $login_is_disabled ) { 
            echo '</div>';
        }
		
	}
 
	// login background image
	
    public function admin_theme_login_bg_settings() {
		
		$val = ( isset( $this->options['login_bg'] ) ) ? $this->options['login_bg'] : '';
		
        global $login_is_disabled;
        
        if( $login_is_disabled ) { 
            echo '<div class="wpat-inactive-option">';
        }
		
            echo '<input type="text" id="login_bg" name="wpat_settings_options[login_bg]" value="' . $val . '" />'; 
            echo '<input id="login_bg_upload_button" class="button uploader" type="button" value="' . esc_html__( 'Upload Image', 'wpat' ) . '" /> ';

            if( $this->options['login_bg'] ) {
                $bg_image = $this->options['login_bg'];
            } else {
                $bg_image = wpat_path('img/no-thumb.jpg');
            }

            echo '<div class="img-upload-container" style="background-image:url(' . $bg_image . ')"></div>';
            echo '<p class="description">' . esc_html__( 'Upload a background image for your WordPress login page', 'wpat' ) . '.</p>';
		
        if( $login_is_disabled ) { 
            echo '</div>';
        }
		
	}
	
	// memory usage
	
	public function admin_theme_memory_usage_settings() {
		
		if( $this->options['memory_usage'] ) $checked = ' checked="checked" '; 
		else $checked = '';
		
		if( ! $this->options['memory_usage']) { 
			$field_status = '<span class="field-status visible">' . esc_html__( 'Visible', 'wpat' ) . '</span>';
 		} else {
			$field_status = '<span class="field-status hidden">' . esc_html__( 'Hidden', 'wpat' ) . '</span>';
		}
		
		echo '<input type="checkbox" ' . $checked . ' id="memory_usage" name="wpat_settings_options[memory_usage]" />';
		
		echo '<label for="memory_usage">' . esc_html__( 'Hide', 'wpat' ) . $field_status . '</label>';
		
		echo '<p class="description">' . esc_html__( 'Display the currently memory usage of your WordPress installation', 'wpat' ) . '.</p>';
		
	}
	
	// memory limit
	
	public function admin_theme_memory_limit_settings() {
		
		if( $this->options['memory_limit'] ) $checked = ' checked="checked" '; 
		else $checked = '';
		
		if( ! $this->options['memory_limit']) { 
			$field_status = '<span class="field-status visible">' . esc_html__( 'Visible', 'wpat' ) . '</span>';
 		} else {
			$field_status = '<span class="field-status hidden">' . esc_html__( 'Hidden', 'wpat' ) . '</span>';
		}
		
		echo '<input type="checkbox" ' . $checked . ' id="memory_limit" name="wpat_settings_options[memory_limit]" />';
		
		echo '<label for="memory_limit">' . esc_html__( 'Hide', 'wpat' ) . $field_status . '</label>';
		
		echo '<p class="description">' . esc_html__( 'Display the memory limit of your WordPress installation', 'wpat' ) . '.</p>';
		
	}
	
	// memory available
	
	public function admin_theme_memory_available_settings() {
		
		if( $this->options['memory_available'] ) $checked = ' checked="checked" '; 
		else $checked = '';
		
		if( ! $this->options['memory_available']) { 
			$field_status = '<span class="field-status visible">' . esc_html__( 'Visible', 'wpat' ) . '</span>';
 		} else {
			$field_status = '<span class="field-status hidden">' . esc_html__( 'Hidden', 'wpat' ) . '</span>';
		}
		
		echo '<input type="checkbox" ' . $checked . ' id="memory_available" name="wpat_settings_options[memory_available]" />';
		
		echo '<label for="memory_available">' . esc_html__( 'Hide', 'wpat' ) . $field_status . '</label>';
		
		echo '<p class="description">' . esc_html__( 'Display the available server memory for your WordPress installation', 'wpat' ) . '.</p>';
		
	}
	
	// php version
	
	public function admin_theme_php_version_settings() {
		
		if( $this->options['php_version'] ) $checked = ' checked="checked" '; 
		else $checked = '';
		
		if( ! $this->options['php_version']) { 
			$field_status = '<span class="field-status visible">' . esc_html__( 'Visible', 'wpat' ) . '</span>';
 		} else {
			$field_status = '<span class="field-status hidden">' . esc_html__( 'Hidden', 'wpat' ) . '</span>';
		}
		
		echo '<input type="checkbox" ' . $checked . ' id="php_version" name="wpat_settings_options[php_version]" />';
		
		echo '<label for="php_version">' . esc_html__( 'Hide', 'wpat' ) . $field_status . '</label>';
		
		echo '<p class="description">' . esc_html__( 'Display the PHP version of your server', 'wpat' ) . '.</p>';
		
	}
	
	// IP address
	
	public function admin_theme_ip_address_settings() {
		
		if( $this->options['ip_address'] ) $checked = ' checked="checked" '; 
		else $checked = '';
		
		if( ! $this->options['ip_address']) { 
			$field_status = '<span class="field-status visible">' . esc_html__( 'Visible', 'wpat' ) . '</span>';
 		} else {
			$field_status = '<span class="field-status hidden">' . esc_html__( 'Hidden', 'wpat' ) . '</span>';
		}
		
		echo '<input type="checkbox" ' . $checked . ' id="ip_address" name="wpat_settings_options[ip_address]" />';
		
		echo '<label for="ip_address">' . esc_html__( 'Hide', 'wpat' ) . $field_status . '</label>';
		
		echo '<p class="description">' . esc_html__( 'Display the IP address of your server', 'wpat' ) . '.</p>';
		
	}
	
	// wp version
	
	public function admin_theme_wp_version_settings() {
		
		if( $this->options['wp_version'] ) $checked = ' checked="checked" '; 
		else $checked = '';
		
		if( ! $this->options['wp_version']) { 
			$field_status = '<span class="field-status visible">' . esc_html__( 'Visible', 'wpat' ) . '</span>';
 		} else {
			$field_status = '<span class="field-status hidden">' . esc_html__( 'Hidden', 'wpat' ) . '</span>';
		}
		
		echo '<input type="checkbox" ' . $checked . ' id="wp_version" name="wpat_settings_options[wp_version]" />';
		
		echo '<label for="wp_version">' . esc_html__( 'Hide', 'wpat' ) . $field_status . '</label>';
		
		echo '<p class="description">' . esc_html__( 'Display the installed WordPress version', 'wpat' ) . '.</p>';
		
	}
	
	// theme css
	
	public function admin_theme_css_admin_settings() {

		$val = ( isset( $this->options['css_admin'] ) ) ? $this->options['css_admin'] : '';
		echo '<textarea class="option-textarea" type="text" name="wpat_settings_options[css_admin]" placeholder=".your-class { color: blue }" />' . $val . '</textarea>';
		
		echo '<p class="description">' . esc_html__( 'Add custom CSS for the Wordpress admin theme. To overwrite some classes, use "!important". Like this example "border-right: 3px!important"', 'wpat' ) . '.</p>';
		
	}
		
	// login css
	
	public function admin_theme_css_login_settings() {

		$val = ( isset( $this->options['css_login'] ) ) ? $this->options['css_login'] : '';
		echo '<textarea class="option-textarea" type="text" name="wpat_settings_options[css_login]" placeholder=".your-class { color: blue }" />' . $val . '</textarea>';
		
		echo '<p class="description">' . esc_html__( 'Add custom CSS for the Wordpress login page. To overwrite some classes, use "!important". Like this example "border-right: 3px!important"', 'wpat' ) . '.</p>';
		
	}
	
	// wp svg
	
	public function admin_theme_wp_svg_settings() {
		
		if( $this->options['wp_svg'] ) $checked = ' checked="checked" '; 
		else $checked = '';
		
		if( ! $this->options['wp_svg'] ) { 
			$field_status = '<span class="field-status hidden">' . esc_html__( 'Deactivated', 'wpat' ) . '</span>';
 		} else {
			$field_status = '<span class="field-status visible">' . esc_html__( 'Activated', 'wpat' ) . '</span>';
		}
		
		echo '<input type="checkbox" ' . $checked . ' id="wp_svg" name="wpat_settings_options[wp_svg]" />';
		
		echo '<label for="wp_svg">' . esc_html__( 'Enable', 'wpat' ) . $field_status . '</label>';
		
		echo '<p class="description">' . esc_html__( 'Allow the upload of SVG files', 'wpat' ) . '.</p>';
		
	}
	
	// wp ico
	
	public function admin_theme_wp_ico_settings() {
		
		if( $this->options['wp_ico'] ) $checked = ' checked="checked" '; 
		else $checked = '';
		
		if( ! $this->options['wp_ico'] ) { 
			$field_status = '<span class="field-status hidden">' . esc_html__( 'Deactivated', 'wpat' ) . '</span>';
 		} else {
			$field_status = '<span class="field-status visible">' . esc_html__( 'Activated', 'wpat' ) . '</span>';
		}
		
		echo '<input type="checkbox" ' . $checked . ' id="wp_ico" name="wpat_settings_options[wp_ico]" />';
		
		echo '<label for="wp_ico">' . esc_html__( 'Enable', 'wpat' ) . $field_status . '</label>';
		
		echo '<p class="description">' . esc_html__( 'Allow the upload of ICO files', 'wpat' ) . '.</p>';
		
	}
    
    // remove WPAT plugin pages
    /*****************************************************************/
    
    function admin_theme_disable_plugin_pages_settings() {
        
        // get all meta box settings fields
        $field = array_shift( $this->plugin_pages_option_fields );

        // Multisite sync page can not be visible, because WordPress multisite is not activated
        if( $field == 'disable_page_ms' ) {
            if( ! is_multisite() ) { 
                echo '<div class="wpat-inactive-option">';
            }
        }
        
            if( $this->options[$field] ) $checked = ' checked="checked" '; 
            else $checked = '';

            if( $field == 'disable_page_ms' && ! is_multisite() ) {
                $field_status = '<span class="field-status hidden">' . esc_html__( 'Removed', 'wpat' ) . '</span>';
            } elseif( ! $this->options[$field] ) { 
                $field_status = '<span class="field-status visible">' . esc_html__( 'Activated', 'wpat' ) . '</span>';
            } else {
                $field_status = '<span class="field-status hidden">' . esc_html__( 'Removed', 'wpat' ) . '</span>';
            }

            echo '<input type="checkbox" ' . $checked . ' id="' . $field . '" name="wpat_settings_options[' . $field . ']" />';

            echo '<label for="' . $field . '">' . esc_html__( 'Disable', 'wpat' ) . $field_status . '</label>';
             
        if( $field == 'disable_page_ms' ) {            
            if( ! is_multisite() ) { 
                echo '</div>';            
            
                echo '<small class="wpat-info">' . esc_html__( 'Activate multisite support for WordPress to use this option', 'wpat' ) . '.</small>';
            }
        }
        
        
	} 
	
	// disable theme options (multisite)
	
	public function admin_theme_disable_theme_options_settings() {
		
		if( $this->options['disable_theme_options'] ) $checked = ' checked="checked" '; 
		else $checked = '';
		
		if( ! $this->options['disable_theme_options']) { 
			$field_status = '<span class="field-status hidden">' . esc_html__( 'Deactivated', 'wpat' ) . '</span>';
 		} else {
			$field_status = '<span class="field-status visible">' . esc_html__( 'Activated', 'wpat' ) . '</span>';
		}
		
		global $blog_id;
		
		if ( is_multisite() && $blog_id == 1 ) {
			echo '<input type="checkbox" ' . $checked . ' id="disable_theme_options" name="wpat_settings_options[disable_theme_options]" />';
		} else {
			echo '<input type="checkbox" ' . $checked . ' id="#" name="#" disabled="disabled" />';
		}
		
		echo '<label for="disable_theme_options">' . esc_html__( 'Disable', 'wpat' ) . $field_status . '</label>';
		
		echo '<p class="description">' . esc_html__( 'Disable the permissions to change WPAT options for all other network sites', 'wpat' ) . '.</p>';
		
		if ( ! is_multisite() ) {
			echo '<small class="wpat-info">' . esc_html__( 'Activate multisite support for WordPress to use this option', 'wpat' ) . '.</small>';
		} 
		
	}
	
	// WP optimization
    /*****************************************************************/
	
	public function admin_theme_wp_optimization_settings() {
		
        // get all optimization settings fields
        $field = array_shift( $this->optimization_option_fields );
		
        if( $this->options[$field[0]] ) $checked = ' checked="checked" '; 
        else $checked = '';

        if( ! $this->options[$field[0]] ) { 
            $field_status = '<span class="field-status visible">' . esc_html__( 'Activated', 'wpat' ) . '</span>';
        } else {
            $field_status = '<span class="field-status hidden">' . esc_html__( 'Removed', 'wpat' ) . '</span>';
        }          

        echo '<input type="checkbox" ' . $checked . ' id="' . $field[0] . '" name="wpat_settings_options[' . $field[0] . ']" />';

        echo '<label for="' . $field[0] . '">' . esc_html__( 'Disable', 'wpat' ) . $field_status . '</label>';
        
		echo '<p class="description">' . $field[1] . '</p>';
		
		echo '<small class="wpat-info">' . $field[2] . '</small>';
		
	}
    
	// remove WP meta boxes
    /*****************************************************************/
    
    function admin_theme_meta_box_settings() {
        
        // get all meta box settings fields
        $field = array_shift( $this->meta_box_option_fields );

        if( $this->options[$field[0]] ) $checked = ' checked="checked" '; 
        else $checked = '';

        if( ! $this->options[$field[0]] ) { 
            $field_status = '<span class="field-status visible">' . esc_html__( 'Activated', 'wpat' ) . '</span>';
        } else {
            $field_status = '<span class="field-status hidden">' . esc_html__( 'Removed', 'wpat' ) . '</span>';
        }

        echo '<input type="checkbox" ' . $checked . ' id="' . $field[0] . '" name="wpat_settings_options[' . $field[0] . ']" />';

        echo '<label for="' . $field[0] . '">' . esc_html__( 'Disable', 'wpat' ) . $field_status . '</label>';

        echo '<p class="description">' . $field[1] . '</p>';
        
		if( $field[2] ) {
            echo '<small class="wpat-info">' . $field[2] . '</small>';
        }
        
	} 
    
	// remove WP dashboard widgets
    /*****************************************************************/
    
    function admin_theme_db_widgets_settings() {
        
        // get all meta box settings fields
        $field = array_shift( $this->db_widget_option_fields );        
        
        // System info dashboad widget can not be activated, if the plugin system info page is deactivated
        $plugin_system_page_is_disabled = $this->options['disable_page_system'];
        
        if( $field == 'dbw_wpat_sys_info' ) {
            if( $plugin_system_page_is_disabled ) { 
                echo '<div class="wpat-inactive-option">';
            }
        }
        
            if( $this->options[$field] ) $checked = ' checked="checked" '; 
            else $checked = '';

            if( $field == 'dbw_wpat_sys_info' && $plugin_system_page_is_disabled ) {
                $field_status = '<span class="field-status hidden">' . esc_html__( 'Removed', 'wpat' ) . '</span>';
            } elseif( ! $this->options[$field] ) { 
                $field_status = '<span class="field-status visible">' . esc_html__( 'Activated', 'wpat' ) . '</span>';
            } else {
                $field_status = '<span class="field-status hidden">' . esc_html__( 'Removed', 'wpat' ) . '</span>';
            }

            echo '<input type="checkbox" ' . $checked . ' id="' . $field . '" name="wpat_settings_options[' . $field . ']" />';

            echo '<label for="' . $field . '">' . esc_html__( 'Disable', 'wpat' ) . $field_status . '</label>';
        
        
        if( $field == 'dbw_wpat_sys_info' ) {
            
            if( $plugin_system_page_is_disabled ) { 
                echo '</div>';            
            
                echo '<small class="wpat-info">' . esc_html__( 'System info dashboad widget can not be activated, if the plugin system info page is deactivated', 'wpat' ) . '.</small>';
            }
        }
        
                
	} 
    
	// remove WP widgets
    /*****************************************************************/
    
    function admin_theme_widgets_settings() {
        
        // get all meta box settings fields
        $field = array_shift( $this->widget_option_fields );

        if( $this->options[$field] ) $checked = ' checked="checked" '; 
        else $checked = '';

        if( ! $this->options[$field] ) { 
            $field_status = '<span class="field-status visible">' . esc_html__( 'Activated', 'wpat' ) . '</span>';
        } else {
            $field_status = '<span class="field-status hidden">' . esc_html__( 'Removed', 'wpat' ) . '</span>';
        }

        echo '<input type="checkbox" ' . $checked . ' id="' . $field . '" name="wpat_settings_options[' . $field . ']" />';

        echo '<label for="' . $field . '">' . esc_html__( 'Disable', 'wpat' ) . $field_status . '</label>';
                
	} 
    
    // frontend
    /*****************************************************************/
	
	public function admin_theme_frontend_settings() {
		
        // get all frontend settings fields
        $field = array_shift( $this->frontend_option_fields );
        
		// Meta Referrer Policy Field
        if( $field[0] == 'meta_referrer_policy' ) {
            
            $items = array(
                'none' => esc_html__( 'Disabled', 'wpat' ),
                'no-referrer' => 'No Referrer',
                'no-referrer-when-downgrade' => 'No Referrer When Downgrade',
                'same-origin' => 'Same Origin',
                'origin' => 'Origin',
                'strict-origin' => 'Strict Origin',
                'origin-when-crossorigin' => 'Origin When Crossorigin',
                'strict-origin-when-crossorigin' => 'Strict Origin When Crossorigin',
                'unsafe-url' => 'Unsafe URL',
            );
            
            echo '<select id="meta_referrer_policy" name="wpat_settings_options[meta_referrer_policy]">';
            
                foreach( $items as $key => $item ) {

                    $selected = ( $this->options['meta_referrer_policy'] == $key ) ? 'selected="selected"' : '';

                    echo '<option value="' . esc_html( $key ) . '" ' . esc_html( $selected ) . '>' . esc_html( $item ) . '</option>';
                }
            
            echo '</select>';
            
        // Header + Footer Code
        } elseif( $field[0] == 'wp_header_code' || $field[0] == 'wp_footer_code' ) {
            
            $val = ( isset( $this->options[$field[0]] ) ) ? $this->options[$field[0]] : '';
            echo '<textarea class="option-textarea" type="text" name="wpat_settings_options[' . $field[0] . ']" placeholder="<script>alert(\'My custom script\');</script> or <style>.my-class {color: red}</style>" />' . $val . '</textarea>';
        
        // Other Fields
        } else {  
		
            if( $this->options[$field[0]] ) $checked = ' checked="checked" '; 
            else $checked = '';
		
            if( ! $this->options[$field[0]] ) { 
                $field_status = '<span class="field-status visible">' . esc_html__( 'Activated', 'wpat' ) . '</span>';
            } else {
                $field_status = '<span class="field-status hidden">' . esc_html__( 'Removed', 'wpat' ) . '</span>';
            }          
        
            echo '<input type="checkbox" ' . $checked . ' id="' . $field[0] . '" name="wpat_settings_options[' . $field[0] . ']" />';

            echo '<label for="' . $field[0] . '">' . esc_html__( 'Disable', 'wpat' ) . $field_status . '</label>';
            
		}
        
		echo '<p class="description">' . $field[1] . '</p>';
		
		echo '<small class="wpat-info">' . $field[2] . '</small>';
		
	}
 
} // end class
 
WPAT_Options::get_instance(); 


/*****************************************************************/
/* DEFINE CALLABLE OPTION VARIABLES */
/*****************************************************************/

if ( ! function_exists( 'wpat_define_wpat_options' ) ) :

	function wpat_define_wpat_options() {

		// get pre defined option fields
		$pre_options = WPAT_Options::get_instance()->pre_options;

		// get currently indexed option fields
		if( is_multisite() ) {
			$curr_options = get_blog_option( get_current_blog_id(), 'wpat_settings_options', array() );
		} else {
			$curr_options = get_option( 'wpat_settings_options' );
		}

		// check indexed option is array
		if( ! is_array( $curr_options ) ) {
			$curr_options = array();
		} else {
			$curr_options = $curr_options;
		}

		// get options, which are undefined
		$diff_result = array_diff_key( $pre_options, $curr_options );

		// define callable option variables
		$WPAT = $curr_options;

		// check for indexed option fields
		foreach( $diff_result as $key ) {

			// undefined index, because option field is not indexed
			if( empty( $key ) ) {
				$WPAT = isset( $curr_options[$key] ) ? $curr_options[$key] : null;

			// option field is indexed
			} else {
				$WPAT = $curr_options;
			}

		}

		// return option variables
		return $WPAT;

	}

endif;

$WPAT = wpat_define_wpat_options();


/*****************************************************************/
/* INCLUDE WPAT PLUGIN PARTS */
/*****************************************************************/

// (BETA) Plugin Optimization tips Page
//include_once( wpat_dir( 'inc/optimization.php' ) );

// Plugin System Info Page
if( $WPAT['disable_page_system'] != true ) { 
    include_once( wpat_dir( 'inc/system-info.php' ) );
}

// Plugin Im- / Export Page
if( $WPAT['disable_page_export'] != true ) { 
    include_once( wpat_dir( 'inc/ex-import.php' ) );
}

// Plugin Multisite Sync Page
if( $WPAT['disable_page_ms'] != true ) { 
    include_once( wpat_dir( 'inc/multisite-sync.php' ) );
}

// Plugin User Activities Dashboard Widget
if( $WPAT['dbw_wpat_user_log'] != true ) { 
    include_once( wpat_dir( 'inc/db-widget-user.php' ) );
}

// Plugin System Info Dashboard Widget
if( $WPAT['disable_page_system'] != true && $WPAT['dbw_wpat_sys_info'] != true ) { 
    include_once( wpat_dir( 'inc/db-widget-system.php' ) );
}

// Plugin Recent Posts Dashboard Widget
if( $WPAT['dbw_wpat_recent_post'] != true ) {
    include_once( wpat_dir( 'inc/db-widget-recent-posts.php' ) );
}

// Plugin Recent Pages Dashboard Widget
if( $WPAT['dbw_wpat_recent_page'] != true ) {
    include_once( wpat_dir( 'inc/db-widget-recent-pages.php' ) );
}
    
// Plugin Recent Comments Dashboard Widget
if( $WPAT['dbw_wpat_recent_comment'] != true ) {
    include_once( wpat_dir( 'inc/db-widget-recent-comments.php' ) );
}

// Plugin Post Count Dashboard Widget
if( $WPAT['dbw_wpat_count_post'] != true ) {
    include_once( wpat_dir( 'inc/db-widget-count-posts.php' ) );
}
    
// Plugin Page Count Dashboard Widget
if( $WPAT['dbw_wpat_count_page'] != true ) {
    include_once( wpat_dir( 'inc/db-widget-count-pages.php' ) );
}
    
// Plugin Comment Count Dashboard Widget
if( $WPAT['dbw_wpat_count_comment'] != true ) {
    include_once( wpat_dir( 'inc/db-widget-count-comments.php' ) );
}
    
// Plugin Memory Usage Dashboard Widget
if( $WPAT['dbw_wpat_memory'] != true ) {
    include_once( wpat_dir( 'inc/db-widget-memory.php' ) );
}


/*****************************************************************/
/* ADDITIONAL CONTENT FOR RECIPE POST TYPE */
/*****************************************************************/

if ( ! function_exists( 'wpat_post_type_recipe' ) ) :

	function wpat_post_type_recipe() {

		if( post_type_exists('recipe') ) {
			// Plugin Recent Recipes Dashboard Widget
			include_once( wpat_dir( 'inc/db-widget-recent-recipes.php' ) );
			
			// Plugin Recipe Count Dashboard Widget
			include_once( wpat_dir( 'inc/db-widget-count-recipes.php' ) );
		} else {
			return false;
		}

	}

endif;

add_action( 'admin_init', 'wpat_post_type_recipe', 30 );


/*****************************************************************/
/* CHECK SEARCH ENGINE VISIBILITY */
/*****************************************************************/

if( is_multisite() ) {
    $visibility = get_blog_option( get_current_blog_id(), 'blog_public', array() );
} else {
    $visibility = get_option( 'blog_public' );
}

if( 0 == $visibility ) {
    include_once( wpat_dir( 'inc/db-widget-search-engine-notice.php' ) );
}


/*****************************************************************/
/* ADD FRONTEND CSS */
/*****************************************************************/

if ( ! function_exists( 'wpat_frontend_css' ) ) :

	function wpat_frontend_css() {

		require_once(ABSPATH . '/wp-includes/pluggable.php');
		
		if( is_user_logged_in() ) {
			wp_register_style( 'wpat-style', wpat_path( 'css/frontend.css' ), array(), null, 'all' );
			wp_enqueue_style ( 'wpat-style' );
		}
	}

endif;

add_action( 'wp_enqueue_scripts', 'wpat_frontend_css', 30 );


/*****************************************************************/
/* LOADING GOOGLE WEB FONTS */
/*****************************************************************/

if( $WPAT['google_webfont'] ) {

	if ( ! function_exists( 'wpat_webfonts_url' ) ) :

		function wpat_webfonts_url( $font_style = '' ) {

			global $WPAT;
			
			$selected_fonts = '';
			
			// get custom font name
			$selected_fonts .= $WPAT['google_webfont'];
			
			// check if custom font weight exist
			if( ! empty( $WPAT['google_webfont_weight'] ) ) {													
				$selected_fonts .= ':' . $WPAT['google_webfont_weight'];
			}
			
			$font_style = add_query_arg( 'family', esc_html( $selected_fonts ), "//fonts.googleapis.com/css" );

			return $font_style;
		}

	endif;


	if ( ! function_exists( 'wpat_webfonts_output' ) ) :

		function wpat_webfonts_output() {

			wp_enqueue_style( 'wpat_webfonts', wpat_webfonts_url(), array(), null, 'all' );

		}

	endif;

	add_action( 'admin_enqueue_scripts', 'wpat_webfonts_output', 30 );

}


/*****************************************************************/
/* ADD BODY CLASSES */
/*****************************************************************/

if ( ! function_exists( 'wpat_body_class' ) ) :

	function wpat_body_class( $classes ) {

        global $WPAT;
        
		if( ! $WPAT['spacing'] ) { 
			$wp_admin_spacing = 'wp-admin-spacing ';
		} else {
            $wp_admin_spacing = false;
        }
        
        if( $WPAT['toolbar'] ) { 
			$wpat_admin_toolbar = 'wp-admin-toolbar-hide ';
		} else {
            $wpat_admin_toolbar = false;
        }
		
		if( $WPAT['left_menu_expand'] ) { 
			$wpat_admin_menu_expand = 'wp-admin-left-menu-expand ';
		} else {
            $wpat_admin_menu_expand = false;
        }

		return $classes . $wp_admin_spacing . $wpat_admin_toolbar . $wpat_admin_menu_expand;

	}

endif;

add_filter( 'admin_body_class', 'wpat_body_class' );


/*****************************************************************/
/* REMOVE USER THEME OPTIONS */
/*****************************************************************/

if ( ! function_exists( 'wpat_remove_theme_option' ) ) :

	function wpat_remove_theme_option() {
        
		global $_wp_admin_css_colors;

		/* Get fresh color data */
		$fresh_color_data = $_wp_admin_css_colors['fresh'];

		/* Remove everything else */
		$_wp_admin_css_colors = array( 'fresh' => $fresh_color_data );
	}

endif;

add_action( 'admin_init', 'wpat_remove_theme_option', 1 );


/*****************************************************************/
/* SET ALL USER ADMIN THEME OPTIONS TO DEFAULT */
/*****************************************************************/
 
if ( ! function_exists( 'wpat_set_default_theme' ) ) :

	function wpat__set_default_theme( $color ){
		return 'fresh';
	}

endif; 

add_filter( 'get_user_option_admin_color', 'wpat__set_default_theme' );


/*****************************************************************/
/* CREATE LOGOUT BUTTON */
/*****************************************************************/

if( $WPAT['toolbar'] ) {

	if ( ! function_exists( 'wpat_logout' ) ) :

		function wpat_logout() {
			echo '<div class="wpat-logout"><div class="wpat-logout-button"></div><div class="wpat-logout-content"><a target="_blank" class="btn home-btn" href="' . home_url() . '">' . esc_html__( 'Home', 'wpat' ) . '</a>';
            if( is_multisite() ) {
                echo '<a class="btn multisite-btn" href="' . network_admin_url() . '">' . esc_html__( 'My Sites', 'wpat' ) . '</a>';
            }
            echo '<a class="btn logout-btn" href="' . wp_logout_url() . '">' . esc_html__( 'Logout', 'wpat' ) . '</a></div></div>';
		}

	endif;

    add_action('admin_head', 'wpat_logout');
	
}


/*****************************************************************/
/* ADD LEFT FOOTER NOTICE */
/*****************************************************************/

if ( ! function_exists( 'wpat_footer_notice' ) ) :

    global $WPAT;

	if( ! $WPAT['credits'] ) {
		function wpat_footer_notice( $text ) {
			$text = 'WordPress admin designed by <a target="_blank" href="https://cleancoded.com">CLEANCODED</a>';
			return $text;
		}
	} else {
		function wpat_footer_notice( $text ) {
			return;
		}
	}	

endif;

add_filter('admin_footer_text', 'wpat_footer_notice');


/*****************************************************************/
/* WRAP THE WP ADMIN CONTENT */
/*****************************************************************/

if( ! $WPAT['spacing'] ) {

	if ( ! function_exists( 'wpat_wrap_content' ) ) :

		function wpat_wrap_content() {
			ob_start( 'wpat_replace_content' );
		}

	endif;

	if ( ! function_exists( 'wpat_replace_content' ) ) :

        function wpat_replace_content( $output ) {

            $find = array('/<div id="wpwrap">/', '#</body>#');
            $replace = array('<div class="body-spacer"><div id="wpwrap">', '</div></body>');
            $result = preg_replace( $find, $replace, $output );

            return $result;
        }

	endif;

	add_action( 'init', 'wpat_wrap_content', 0, 0 );

}


/*****************************************************************/
/* CUSTOM LOGIN PAGE */
/*****************************************************************/

if( ! $WPAT['login_disable'] ) {

	/*****************************************************************/
	/* ADD LOGIN STYLE */
	/*****************************************************************/

	if ( ! function_exists( 'wpat_login_style' ) ) :

		function wpat_login_style() {

			wp_enqueue_style( 'custom-login', wpat_path('css/login.css'), array(), filemtime( plugin_dir_path( __FILE__ ) . '/css/login.css' ), 'all' );
			//wp_enqueue_script( 'custom-login', wpat_path('js/login.js') );

		}

	endif;

    add_action('login_enqueue_scripts', 'wpat_login_style');
	
	
	/*****************************************************************/
	/* CHANGE LOGIN LOGO URL */
	/*****************************************************************/

	if ( ! function_exists( 'wpat_logo_url' ) ) :

		function wpat_logo_url() {
			return home_url();
		}

	endif;

    add_filter( 'login_headerurl', 'wpat_logo_url' );


	/*****************************************************************/
	/* ADD LOGIN MESSAGE */
	/*****************************************************************/

	global $WPAT;
    
	if( $WPAT['login_title'] ) {

		if ( ! function_exists( 'wpat_login_message' ) ) :

			function wpat_login_message( $message ) {

				global $WPAT;

				if ( empty( $message ) ){
					return '<div class="login-message">' . esc_html( $WPAT['login_title'] ) . '</div>';
				} else {
					return $message;
				}
			}

		endif;

        add_filter( 'login_message', 'wpat_login_message' );

	}

}


/*****************************************************************/
/* ADD USER BOX TO LEFT ADMIN MENU */
/*****************************************************************/
	
if( ! $WPAT['user_box'] && ! $WPAT['company_box'] ) {

	if ( ! function_exists( 'wpat_userbox' ) ) :

		function wpat_userbox() {

			global $menu, $user_id, $scheme;

			// get user name and avatar
			$current_user = wp_get_current_user();
			$user_name = $current_user->display_name ;
			$user_avatar = get_avatar( $current_user->user_email, 74 );

			// get user profile link
			if ( is_user_admin() ) {
				$url = user_admin_url( 'profile.php', $scheme );
			} elseif ( is_network_admin() ) {
				$url = network_admin_url( 'profile.php', $scheme );
			} else {
				$url = get_dashboard_url( $user_id, 'profile.php', $scheme );
			}    

			if( is_rtl() ) {
				$html = '<div class="adminmenu-avatar">' . $user_avatar . '<div class="adminmenu-user-edit">' . esc_html__( 'Edit', 'wpat' ) . '</div></div><div class="adminmenu-user-name"><span>' . esc_html__( $user_name ) . ', ' . esc_html__('Howdy', 'wpat') . '</span></div>';
			} else {
				$html = '<div class="adminmenu-avatar">' . $user_avatar . '<div class="adminmenu-user-edit">' . esc_html__( 'Edit', 'wpat' ) . '</div></div><div class="adminmenu-user-name"><span>' . esc_html__('Howdy', 'wpat') . ', ' . esc_html__( $user_name ) . '</span></div>';
			}

			$menu[0] = array( $html, 'read', $url, 'user-box', 'adminmenu-container');

		}

	endif;

	add_action('admin_menu', 'wpat_userbox');
	
}
	

/*****************************************************************/
/* ADD COMPANY BOX TO THE LEFT ADMIN MENU */
/*****************************************************************/

if( ! $WPAT['user_box'] && $WPAT['company_box'] ) {
	
	if ( ! function_exists( 'wpat_companybox' ) ) :

		function wpat_companybox() {

			global $WPAT, $menu, $user_id, $scheme;

			$blog_name = get_bloginfo( 'name' );
			$site_url = get_bloginfo( 'wpurl' ) . '/';

			if( ! empty( $WPAT['company_box_logo'] ) ){
				$company_logo_output = '<img style="width:' . esc_html( $WPAT['company_box_logo_size'] ) . 'px" class="company-box-logo" src="' . esc_url( $WPAT['company_box_logo'] ) . '" alt="' . esc_attr( $blog_name ) . '">';
			} else {
				$company_logo_output = esc_html__( 'No image selected.', 'wpat' );
			}

			$html = '<div class="adminmenu-avatar">' . $company_logo_output . '<div class="adminmenu-user-edit">' . esc_html__( 'Home', 'wpat' ) . '</div></div><div class="adminmenu-user-name"><span>' . esc_html( $blog_name ) . '</span></div>';

			$menu[0] = array( $html, 'read', $site_url, 'user-box', 'adminmenu-container');

		}

	endif;

	add_action('admin_menu', 'wpat_companybox');
	
}


/*****************************************************************/
/* WP ADMIN POST AND PAGE LIST IMAGE COLUMN */
/*****************************************************************/

if( ! $WPAT['thumbnail'] ) {

	if( ! function_exists('wpat_post_img_col') ) :
	
		function wpat_post_img_col() {

        	$currentScreen = get_current_screen();
			
			if( $currentScreen->post_type === 'post' || $currentScreen->post_type === 'page' || $currentScreen->post_type === 'recipe' ) {

				/*****************************************************************/
				/* ADD IMAGE COLUMN TO WP ADMIN POSTS AND PAGES */
				/*****************************************************************/

				if ( ! function_exists( 'wpat_featured_image' ) ) :

					// get the image
					function wpat_featured_image( $post_ID ) {
						$post_thumbnail_id = get_post_thumbnail_id( $post_ID );
						if ( $post_thumbnail_id ) {
							$post_thumbnail_img = wp_get_attachment_image_src( $post_thumbnail_id, array(32,32) );
							return $post_thumbnail_img[0];
						}
					}

				endif;


				if ( ! function_exists( 'wpat_columns_head' ) ) :

					// add new col
					function wpat_columns_head( $defaults ) {
						$defaults['featured_image'] = esc_html__( 'Image', 'wpat' );
						return $defaults;
					}

				endif;	

				add_filter('manage_posts_columns', 'wpat_columns_head');
				add_filter('manage_pages_columns', 'wpat_columns_head');


				if ( ! function_exists( 'wpat_columns_content' ) ) :

					// output the image
					function wpat_columns_content( $column_name, $post_ID ) {
						if ( $column_name == 'featured_image' ) {
							$post_featured_image = wpat_featured_image( $post_ID );
							if ( $post_featured_image ) {
								echo '<img src="' . esc_url( $post_featured_image ) . '" />';
							} else {
								echo '<img style="width:55px;height:55px" src="' . wpat_path( 'img/no-thumb.jpg' ) . '" alt="' . esc_attr__( 'No Thumbnail', 'wpat' ) . '"/>';
							}
						}
					}

				endif;	

				add_action('manage_posts_custom_column', 'wpat_columns_content', 3, 2);
				add_action('manage_pages_custom_column', 'wpat_columns_content', 3, 2);


				/*****************************************************************/
				/* MOVE IMAGE COLUMN TO FIRST POSITION BEFORE TITLE */
				/*****************************************************************/

				if ( ! function_exists( 'wpat_thumbnail_column' ) ) :

					function wpat_thumbnail_column($columns) {
						$new = array();
						foreach($columns as $key => $title) {
							if ($key=='title')
								$new['featured_image'] = 'Image';
							$new[$key] = $title;
						}
						return $new;
					}

				endif;

				add_filter('manage_posts_columns', 'wpat_thumbnail_column');
				add_filter('manage_pages_columns', 'wpat_thumbnail_column');

			}

		}

	endif;

	add_action( 'current_screen', 'wpat_post_img_col' );

}


/*****************************************************************/
/* ADD ID COLUMN TO WP ADMIN PAGES AND POSTS */
/*****************************************************************/

if( ! $WPAT['post_page_id'] ) {

	if( ! function_exists('wpat_posts_columns_id') ) :

		function wpat_posts_columns_id($defaults){
			$defaults['wps_post_id'] = esc_html__('ID', 'wpat');
			return $defaults;
		}

	endif;

    add_filter('manage_posts_columns', 'wpat_posts_columns_id', 99);
    add_filter('manage_pages_columns', 'wpat_posts_columns_id', 99);


	if( ! function_exists('wpat_posts_custom_id_columns') ) :

		function wpat_posts_custom_id_columns($column_name, $id){
			if($column_name === 'wps_post_id'){
				echo esc_html( $id );
			}
		}

	endif;

    add_action('manage_posts_custom_column', 'wpat_posts_custom_id_columns', 99, 2);
    add_action('manage_pages_custom_column', 'wpat_posts_custom_id_columns', 99, 2);

}


/*****************************************************************/
/* ADD RIGHT FOOTER MEMORY NOTICE */
/*****************************************************************/

if( ! $WPAT['memory_usage'] || ! $WPAT['memory_limit'] || ! $WPAT['ip_address'] || ! $WPAT['php_version'] || ! $WPAT['wp_version'] ) {

	if ( ! function_exists( 'wpat_memory_notice' ) ) :

		function wpat_memory_notice( $text ) {
			$text = wp_memory_data();
			return $text;
		}

	endif;

	add_filter('update_footer', 'wpat_memory_notice', 11);

}


/*****************************************************************/
/* ADD FOOTER INFORMATION */
/*****************************************************************/

if( ! $WPAT['memory_usage'] ) {
	
	// get wp memory usage

	if ( ! function_exists( 'wp_memory_usage' ) ) : 

		function wp_memory_usage() {

			global $memory_limit, $memory_usage;
            
            if( ini_get( 'memory_limit' ) == '-1' ) {
                $memory_limit = '-1';
            } else { 
                $memory_limit = //(int)ini_get( 'memory_limit' ); 
                $memory_limit = (int)WP_MEMORY_LIMIT; 
            }
            
			$memory_usage = function_exists( 'memory_get_peak_usage' ) ? round( memory_get_peak_usage(true) / 1024 / 1024 ) : 0;
			

			if( $memory_usage != false && $memory_limit != false ) {

				global $memory_percent;

                if( ini_get( 'memory_limit' ) == '-1' ) {
                    $memory_percent = esc_html__( 'Unlimited', 'wpat' );
                } else {
				    $memory_percent = round( $memory_usage / $memory_limit * 100, 0 );
                }

			}

		}

	endif;
	
}

if( ! $WPAT['memory_limit'] ) {

	// get WP memory limit

	if ( ! function_exists( 'wp_memory_limit' ) ) : 

		function wp_memory_limit( $size ) {

			global $wp_limit;

			$value  = substr( $size, -1 );
			$wp_limit = substr( $size, 0, -1 );

			$wp_limit = (int)$wp_limit;

			switch ( strtoupper( $value ) ) {
				case 'P' :
					$wp_limit*= 1024;
				case 'T' :
					$wp_limit*= 1024;
				case 'G' :
					$wp_limit*= 1024;
				case 'M' :
					$wp_limit*= 1024;
				case 'K' :
					$wp_limit*= 1024;
			}

			return $wp_limit;
		}  

	endif;
	
	// check WP memory limit
	
	if ( ! function_exists( 'wp_check_memory_limit' ) ) : 

		function wp_check_memory_limit() {

			global $check_memory;

			$check_memory = wp_memory_limit( WP_MEMORY_LIMIT );
			$check_memory = size_format( $check_memory );

			return ($check_memory) ? $check_memory : esc_html__( 'N/A', 'wpat' );

		}

	endif;
	
}

// output WP memory data

if ( ! function_exists( 'wp_memory_data' ) ) : 

	function wp_memory_data() {

		global $WPAT, $memory_limit, $memory_usage, $memory_percent, $check_memory, $wp_version;
        
        echo '<span class="wpat-footer-info">';
        
        // ip address
		if( ! $WPAT['ip_address'] ) {
			
			// get ip address
			$server_ip_address = ( ! empty( $_SERVER[ 'SERVER_ADDR' ] ) ? $_SERVER[ 'SERVER_ADDR' ] : '' );
			if( $server_ip_address == '' || $server_ip_address == false ) { 
				$server_ip_address = ( ! empty( $_SERVER[ 'LOCAL_ADDR' ] ) ? $_SERVER[ 'LOCAL_ADDR' ] : '' );
			}
			
            echo '<span class="wpat-footer-info-sep">';
			if( is_rtl() ) {
				echo $server_ip_address . ' :' . esc_html__( 'IP', 'wpat' );
			} else {
				echo esc_html__( 'IP', 'wpat' ) . ' ' . $server_ip_address;
			}
            echo '</span>';
			
		}

		// php version
		if( ! $WPAT['php_version'] ) {
			
            echo '<span class="wpat-footer-info-sep">';
			if( is_rtl() ) {
				echo PHP_VERSION . ' :' . esc_html__( 'PHP', 'wpat' );
			} else {
				echo esc_html__( 'PHP', 'wpat' ) . ' ' . PHP_VERSION;
			}
            echo '</span>';
			
			
		}

		// WP version
		if( ! $WPAT['wp_version'] ) {
			
            echo '<span class="wpat-footer-info-sep">';
			if( is_rtl() ) {
				echo $wp_version . ' :' . esc_html__( 'WP', 'wpat' );
			} else {
				echo esc_html__( 'WP', 'wpat' ) . ' ' . $wp_version;
			}
            echo '</span>';
			
		}

		echo '</span><br><span class="wpat-footer-info">';
		
		// memory usage
		if( ! $WPAT['memory_usage'] ) {
			
			wp_memory_usage();

			if ( $memory_percent <= 65 ) $memory_status = '#20bf6b';
            if ( $memory_percent > 65 ) $memory_status = '#f7b731';
            if ( $memory_percent > 85 ) $memory_status = '#eb3b5a';
            
            if ( $memory_percent == 'Unlimited' ) {
                $memory_unit = '';
            } else {
                $memory_unit = '%';
            }

            echo '<span class="wpat-footer-info-sep">';
			if( is_rtl() ) {
				echo '<span class="memory-status" style="background:' . $memory_status . '"><strong>' . $memory_unit . $memory_percent . '</strong></span>';
				echo ' MB ' . $memory_limit . esc_html__( ' of ', 'wpat' );
				echo $memory_usage . ': ' . esc_html__( 'Memory Usage', 'wpat' );
			} else {
				echo esc_html__( 'WP Memory Usage', 'wpat' ) . ': ' . $memory_usage;
				echo esc_html__( ' of', 'wpat' ) . ' ' . $memory_limit . ' MB';
				echo '<span class="memory-status" style="background:' . $memory_status . '"><strong>' . $memory_percent . $memory_unit . '</strong></span>';
			}
            echo '</span>';

		}
		
		// WP memory limit
		if( ! $WPAT['memory_limit'] ) {
			
			wp_check_memory_limit();
			
            echo '<span class="wpat-footer-info-sep">';
			if( is_rtl() ) {
				echo $check_memory . ' :' . esc_html__( 'WP Memory Limit', 'wpat' );
			} else {
				echo esc_html__( 'WP Memory Limit', 'wpat' ) . ': ' . $check_memory;
			}
			echo '</span>';
            
		}
		
		// memory available
		if( ! $WPAT['memory_available'] ) {
			
            echo '<span class="wpat-footer-info-sep">';
			if( is_rtl() ) {
				echo 'MB ' . (int)@ini_get( 'memory_limit' ) . ' :' . esc_html__( 'Memory Available', 'wpat' );
			} else {
				echo esc_html__( 'Memory Available', 'wpat' ) . ': ' . (int)@ini_get( 'memory_limit' ) . ' MB';
			}
			echo '</span>';
            
		}
        
        echo '</span>';

	}

endif;


/*****************************************************************/
/* SVG SUPPORT */
/*****************************************************************/

if( $WPAT['wp_svg'] ) {
	
	if ( ! function_exists( 'wpat_svg_support' ) ) : 
	
		function wpat_svg_support( $svg_mime ) {
			$svg_mime['svg'] = 'image/svg+xml';		
			return $svg_mime;
		}
	
	endif;

	add_filter('upload_mimes', 'wpat_svg_support', 10, 4);
	
}


/*****************************************************************/
/* ICO SUPPORT */
/*****************************************************************/

if( $WPAT['wp_ico'] ) {
	
	if ( ! function_exists( 'wpat_ico_support' ) ) : 
	
		function wpat_ico_support( $ico_mime ) {
			$ico_mime['ico'] = 'image/x-icon';
			return $ico_mime;
		}
	
	endif;

	add_filter('upload_mimes', 'wpat_ico_support', 10, 5);

}


/*****************************************************************/
/* REMOVE WP VERSION META TAG */
/*****************************************************************/

if( $WPAT['wp_version_tag'] ) {

	remove_action('wp_head', 'wp_generator');

}


/*****************************************************************/
/* REMOVE WP EMOTICONS */
/*****************************************************************/

if( $WPAT['wp_emoji'] ) {

	if ( ! function_exists( 'remove_emoji' ) ) : 
	
		function remove_emoji() {
			remove_action('wp_head', 'print_emoji_detection_script', 7);
			remove_action('admin_print_scripts', 'print_emoji_detection_script');
			remove_action('admin_print_styles', 'print_emoji_styles');
			remove_action('wp_print_styles', 'print_emoji_styles');
			remove_filter('the_content_feed', 'wp_staticize_emoji');
			remove_filter('comment_text_rss', 'wp_staticize_emoji');
			remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
			add_filter('tiny_mce_plugins', 'remove_tinymce_emoji');
		}
	
	endif;

	add_action('init', 'remove_emoji');

	if ( ! function_exists( 'remove_tinymce_emoji' ) ) : 
	
		function remove_tinymce_emoji( $plugins ) {
			if (!is_array( $plugins )) {
				return array();
			}
			return array_diff( $plugins, array( 'wpemoji' ));
		}
	
	endif;

}


/*****************************************************************/
/* REMOVE RSS FEED LINKS */
/*****************************************************************/

if( $WPAT['wp_feed_links'] ) {
	
	remove_action('wp_head', 'feed_links', 2);
	remove_action('wp_head', 'feed_links_extra', 3);
	
	if ( ! function_exists( 'wpat_disable_rss' ) ) : 
	
		function wpat_disable_rss() {
			wp_die( 
				esc_html__( 'No feed available, please visit our', 'wpat' ) . ' <a href="'. esc_url( home_url( '/' ) ) .'">' . esc_html__( 'homepage', 'wpat' ) . '</a>!'
			);
		}
	
	endif;

	add_action('do_feed', 'wpat_disable_rss', 1);
	add_action('do_feed_rdf', 'wpat_disable_rss', 1);
	add_action('do_feed_rss', 'wpat_disable_rss', 1);
	add_action('do_feed_rss2', 'wpat_disable_rss', 1);
	add_action('do_feed_atom', 'wpat_disable_rss', 1);
	add_action('do_feed_rss2_comments', 'wpat_disable_rss', 1);
	add_action('do_feed_atom_comments', 'wpat_disable_rss', 1);
		
}


/*****************************************************************/
/* REMOVE RSD LINK */
/*****************************************************************/

if( $WPAT['wp_rsd_link'] ) {
	
	remove_action('wp_head', 'rsd_link');
	
}


/*****************************************************************/
/* REMOVE WLWMANIFEST LINK */
/*****************************************************************/

if( $WPAT['wp_wlwmanifest'] ) {
	
	remove_action('wp_head', 'wlwmanifest_link');
	
}


/*****************************************************************/
/* REMOVE SHORTLINK */
/*****************************************************************/

if( $WPAT['wp_shortlink'] ) {
	
	remove_action('wp_head', 'wp_shortlink_wp_head', 10, 0);
	remove_action('wp_head', 'wp_shortlink_header', 10, 0);
}


/*****************************************************************/
/* REMOVE REST API */
/*****************************************************************/

if( $WPAT['wp_rest_api'] ) {
	
	remove_action('wp_head','rest_output_link_wp_head',10);
	add_filter('rest_enabled','_return_false');
	add_filter('rest_jsonp_enabled','_return_false'); 
	
}


/*****************************************************************/
/* REMOVE oEMBED */
/*****************************************************************/

if( $WPAT['wp_oembed'] ) {

	remove_action('wp_head', 'wp_oembed_add_discovery_links', 10);
	
	if ( ! function_exists( 'wpat_block_wp_embed' ) ) : 
	
		function wpat_block_wp_embed() {
			wp_deregister_script('wp-embed'); 
		}

    endif;

	add_action('init', 'wpat_block_wp_embed');

}


/*****************************************************************/
/* REMOVE XML-RPC */
/*****************************************************************/

if( $WPAT['wp_xml_rpc'] ) {
	
	add_filter( 'xmlrpc_enabled', '__return_false' );
	
	if ( ! function_exists( 'wpat_remove_x_pingback' ) ) : 
	
		function wpat_remove_x_pingback( $headers ) {
			unset( $headers['X-Pingback'] );
			return $headers;
		}

    endif;

	add_filter( 'wp_headers', 'wpat_remove_x_pingback' );

}


/*****************************************************************/
/* STOP WP HEARTBEAT */
/*****************************************************************/

if( $WPAT['wp_heartbeat'] ) {
	
	if ( ! function_exists( 'wpat_stop_heartbeat' ) ) : 
	
		function wpat_stop_heartbeat() {
			wp_deregister_script('heartbeat');
		}

    endif;

	add_action('init', 'wpat_stop_heartbeat', 1);

}


/*****************************************************************/
/* REMOVE REL LINKS PREV/NEXT  */
/*****************************************************************/

if( $WPAT['wp_rel_link'] ) {
	
	remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0);
	remove_action('wp_head', 'parent_post_rel_link', 10, 0);
	remove_action('wp_head', 'start_post_rel_link', 10, 0);
	remove_action('wp_head', 'index_rel_link');
	
}


/*****************************************************************/
/* DISABLE SELF PINGBACKS  */
/*****************************************************************/

if( $WPAT['wp_self_pingback'] ) {
    
	if ( ! function_exists( 'wpat_disable_self_pingback' ) ) : 
	
		function wpat_disable_self_pingback( &$links ) {
			$home = get_option( 'home' );
			foreach( $links as $l => $link ) {
				if( 0 === strpos( $link, $home ) ) {
					unset($links[$l]);  
				}
			}
		}

    endif;

    add_action( 'pre_ping', 'wpat_disable_self_pingback' );

}  


/*****************************************************************/
/* SET REFERRER POLICY META TAG */
/*****************************************************************/

if( $WPAT['meta_referrer_policy'] && $WPAT['meta_referrer_policy'] != 'none' ) {

    if ( ! function_exists( 'wpat_meta_referrer_policy' ) ) : 

        function wpat_meta_referrer_policy() {
            global $WPAT;
            echo '<meta name="referrer" content="' . $WPAT['meta_referrer_policy'] . '">';
        }

    endif;

    add_action('wp_head', 'wpat_meta_referrer_policy');

}


/*****************************************************************/
/* ADD CUSTOM CODE TO WP HEAD */
/*****************************************************************/

if( $WPAT['wp_header_code'] ) {

    if ( ! function_exists( 'wpat_add_code_to_wphead' ) ) :

        function wpat_add_code_to_wphead() {
            global $WPAT;
            echo $WPAT['wp_header_code'];
        }

    endif;

    add_action( 'wp_head', 'wpat_add_code_to_wphead' );
    
}


/*****************************************************************/
/* ADD CUSTOM CODE TO WP FOOTER */
/*****************************************************************/

if( $WPAT['wp_footer_code'] ) {
    
    if ( ! function_exists( 'wpat_add_code_to_wpfooter' ) ) :

        function wpat_add_code_to_wpfooter() {
            global $WPAT;
            echo $WPAT['wp_footer_code'];
        }

    endif;
	
    add_action( 'wp_footer', 'wpat_add_code_to_wpfooter', 999 );
    
}

    
/*****************************************************************/
/* REMOVE WP ADMIN META BOX  */
/*****************************************************************/

if( ! function_exists('wpat_remove_metaboxes') ) :

    function wpat_remove_metaboxes() {

        global $WPAT;
        
        if( $WPAT['mb_custom_fields'] ) {    
            remove_meta_box( 'postcustom', '', 'normal' );	
        }

        if( $WPAT['mb_commentstatus'] ) {  
            remove_meta_box( 'commentstatusdiv', '', 'normal' );
        }

        if( $WPAT['mb_comments'] ) {  
            remove_meta_box( 'commentsdiv', '', 'normal' );
        }

        if( $WPAT['mb_author'] ) {  
            remove_meta_box( 'authordiv', '', 'normal' );
        }

        if( $WPAT['mb_category'] ) {  
            remove_meta_box( 'categorydiv', '', 'side' );
        }

        if( $WPAT['mb_format'] ) {  
            remove_meta_box( 'formatdiv', '', 'side' );
        }

        if( $WPAT['mb_pageparent'] ) {  
            remove_meta_box( 'pageparentdiv', '', 'side' );
        }

        if( $WPAT['mb_postexcerpt'] ) {  
            remove_meta_box( 'postexcerpt', '', 'normal' );
        }

        if( $WPAT['mb_postimage'] ) {  
            remove_meta_box( 'postimagediv', '', 'side' );
        }

        if( $WPAT['mb_revisions'] ) {  
            remove_meta_box( 'revisionsdiv', '', 'normal' );
        }

        if( $WPAT['mb_slug'] ) {  
            remove_meta_box( 'slugdiv', '', 'normal' );
        }

        if( $WPAT['mb_tags'] ) {  
            remove_meta_box( 'tagsdiv-post_tag', '', 'side' );
        }

        if( $WPAT['mb_trackbacks'] ) {  
            remove_meta_box( 'trackbacksdiv', '', 'normal' );
        }

    }

endif;

add_action( 'do_meta_boxes' , 'wpat_remove_metaboxes' );


/*****************************************************************/
/* REMOVE WP ADMIN DASHBOARD WIDGETS  */
/*****************************************************************/

if( ! function_exists('wpat_remove_db_widgets') ) :

    function wpat_remove_db_widgets() {
        
        global $WPAT;
        
        if( $WPAT['dbw_quick_press'] ) {
            remove_meta_box ( 'dashboard_quick_press', 'dashboard', 'side' ); // Quick Draft
        }
        
        if( $WPAT['dbw_right_now'] ) {
            remove_meta_box ( 'dashboard_right_now', 'dashboard', 'normal' ); // At the Glance
            if( is_multisite() ) {
                remove_meta_box ( 'network_dashboard_right_now', 'dashboard-network', 'normal' );
            } 
        }
        
        if( $WPAT['dbw_activity'] ) {
            remove_meta_box ( 'dashboard_activity', 'dashboard', 'normal' ); // Activity
        }
        
        if( $WPAT['dbw_primary'] ) {
            remove_meta_box( 'dashboard_primary', 'dashboard', 'side' ); // WordPress Events and News
            if( is_multisite() ) {
                remove_meta_box( 'dashboard_primary', 'dashboard-network', 'side' );
            }
        }
        
        if( $WPAT['dbw_welcome'] ) {
            remove_action('welcome_panel', 'wp_welcome_panel'); // Welcome
        }

    }

endif;

add_action( 'wp_dashboard_setup' , 'wpat_remove_db_widgets' );

if( is_multisite() ) {
    add_action( 'wp_network_dashboard_setup' , 'wpat_remove_db_widgets' );
}

/*****************************************************************/
/* REMOVE WP ADMIN WIDGETS  */
/*****************************************************************/

if( ! function_exists('wpat_remove_widgets') ) :

    function wpat_remove_widgets() {
        
        global $WPAT;
        
        if( $WPAT['wt_pages'] ) {
            unregister_widget('WP_Widget_Pages');
        }
        
        if( $WPAT['wt_calendar'] ) {
            unregister_widget('WP_Widget_Calendar');
        }
        
        if( $WPAT['wt_archives'] ) {
            unregister_widget('WP_Widget_Archives');
        }
        
        if( $WPAT['wt_meta'] ) {
            unregister_widget('WP_Widget_Meta');
        }
        
        if( $WPAT['wt_search'] ) {
            unregister_widget('WP_Widget_Search');
        }
        
        if( $WPAT['wt_text'] ) {
            unregister_widget('WP_Widget_Text');
        }
        
        if( $WPAT['wt_categories'] ) {
            unregister_widget('WP_Widget_Categories');
        }
        
        if( $WPAT['wt_recent_posts'] ) {
            unregister_widget('WP_Widget_Recent_Posts');
        }
        
        if( $WPAT['wt_recent_comments'] ) {
            unregister_widget('WP_Widget_Recent_Comments');
        }
        
        if( $WPAT['wt_rss'] ) {
            unregister_widget('WP_Widget_RSS');
        }
        
        if( $WPAT['wt_tag_cloud'] ) {
            unregister_widget('WP_Widget_Tag_Cloud');
        }
        
        if( $WPAT['wt_nav'] ) {
            unregister_widget('WP_Nav_Menu_Widget');
        }
        
        if( $WPAT['wt_image'] ) {
            unregister_widget('WP_Widget_Media_Image');
        }
        
        if( $WPAT['wt_audio'] ) {
            unregister_widget('WP_Widget_Media_Audio');
        }
        
        if( $WPAT['wt_video'] ) {
            unregister_widget('WP_Widget_Media_Video');
        }
        
        if( $WPAT['wt_gallery'] ) {
            unregister_widget('WP_Widget_Media_Gallery');
        }
        
        if( $WPAT['wt_html'] ) {
            unregister_widget('WP_Widget_Custom_HTML');
        }

    }

endif;

add_action( 'widgets_init' , 'wpat_remove_widgets' );


/*****************************************************************/
/* REMOVE WP SCREEN OPTIONS  */
/*****************************************************************/

if( $WPAT['hide_screen_option'] ) {

    if( ! function_exists('wpat_remove_screen_options') ) :

        function wpat_remove_screen_options() {
            return false; 
        }

    endif;

    add_filter('screen_options_show_screen', 'wpat_remove_screen_options');

}


/*****************************************************************/
/* REMOVE WP CONTEXTUAL HELP  */
/*****************************************************************/

if( $WPAT['hide_help'] ) {

    if( ! function_exists('wpat_remove_contextual_help') ) :
    
        function wpat_remove_contextual_help( $old_help, $screen_id, $screen ) {
            $screen->remove_help_tabs();
            return $old_help;
        }
    
    endif;
    
    add_filter( 'contextual_help', 'wpat_remove_contextual_help', 999, 3 );
    
}


/*****************************************************************/
/* REMOVE COMMENTS MENU FROM ADMIN BAR  */
/*****************************************************************/

if( $WPAT['hide_adminbar_comments'] ) {

    if( ! function_exists('wp_admin_remove_adminbar_comments') ) :
    
        function wp_admin_remove_adminbar_comments() {
            global $wp_admin_bar;
            $wp_admin_bar->remove_menu('comments');
        }
    
    endif;
    
    add_action( 'wp_before_admin_bar_render', 'wp_admin_remove_adminbar_comments' );
    
}


/*****************************************************************/
/* REMOVE NEW CONTENT MENU FROM ADMIN BAR  */
/*****************************************************************/

if( $WPAT['hide_adminbar_new'] ) {

    if( ! function_exists('wpat_remove_adminbar_new') ) :

        function wpat_remove_adminbar_new() {
            global $wp_admin_bar;   
            $wp_admin_bar->remove_menu('new-content');   
        }
    
    endif;

    add_action( 'wp_before_admin_bar_render', 'wpat_remove_adminbar_new', 999 );
    
}


/*****************************************************************/
/* REMOVE WP (LOGO) MENU FROM ADMIN BAR  */
/*****************************************************************/

if( $WPAT['toolbar_wp_icon'] ) {

    if( ! function_exists('wpat_remove_adminbar_wp_logo') ) :
    
        function wpat_remove_adminbar_wp_logo() {
            global $wp_admin_bar;
            $wp_admin_bar->remove_menu('wp-logo');
        }
    
    endif;
    
    add_action('wp_before_admin_bar_render', 'wpat_remove_adminbar_wp_logo', 0);
    
}


/*****************************************************************/
/* REMOVE THE CUSTOMIZE LINK FROM ADMIN BAR  */
/*****************************************************************/

if( $WPAT['hide_adminbar_customize'] ) {

    if( ! function_exists('wpat_remove_adminbar_customize') ) :
    
        function wpat_remove_adminbar_customize() {
            global $wp_admin_bar;
            $wp_admin_bar->remove_menu('customize');
        }
    
    endif;
    
    add_action('wp_before_admin_bar_render', 'wpat_remove_adminbar_customize', 0);
    
}


/*****************************************************************/
/* REMOVE SEARCH FROM ADMIN BAR  */
/*****************************************************************/

if( $WPAT['hide_adminbar_search'] ) {

    if( ! function_exists('wpat_remove_adminbar_search') ) :
    
        function wpat_remove_adminbar_search() {
            global $wp_admin_bar;
            $wp_admin_bar->remove_menu('search');
        }
    
    endif;
    
    add_action('wp_before_admin_bar_render', 'wpat_remove_adminbar_search', 0);
    
}


/*****************************************************************/
/* REMOVE ADMIN BAR COMPLETELY */
/*****************************************************************/

if( $WPAT['toolbar'] ) {

    if( ! function_exists('wpat_remove_adminbar_complete') ) :

        function wpat_remove_adminbar_complete() {
            wp_deregister_script('admin-bar');
            wp_deregister_style('admin-bar');  
            remove_action('admin_init', '_wp_admin_bar_init');
            remove_action('in_admin_header', 'wp_admin_bar_render', 0);
        }

    endif;

    add_action('admin_head', 'wpat_remove_adminbar_complete', 0);
    
}
    
endif; // END of class_exists check

?>