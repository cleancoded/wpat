<?php 

/*****************************************************************/
/* WP LOGGED IN USERS DASHBOARD WIDGET */
/*****************************************************************/

// SET DATE / TIME
if( ! function_exists('wp_admin_set_date_time') ) :

    function wp_admin_set_date_time() {
        
        // update user last login date/time
        date_default_timezone_set( get_option('timezone_string') ); // set default timezone by wp settings
        
        // get date / time in wp format
        $date = date_i18n( get_option( 'date_format' ), strtotime( date( 'Y-m-d', current_time( 'timestamp', 1 ) ) ) );
        $time = date_i18n( get_option( 'time_format' ), strtotime( date( 'H:i:s', current_time( 'timestamp', 1 ) ) ) );
        
        $result = $date . ' ' . $time;
        return $result;
        
    }
    
endif;

// SET USER LOGGED IN STATUS IF THE USER IS ALREADY LOGGED IN
if( ! function_exists('wp_admin_first_login_status') ) :

    function wp_admin_first_login_status() {
        
        // get current logged in user ID
        $current_user = wp_get_current_user();
        
        // check if user login status is false
        if( get_user_meta( $current_user->ID, '_logged_in', true ) ) {
            // update user login status to logged in
            update_user_meta( $current_user->ID, '_logged_in', 1 ); 
        }
        
        // check if user last login is empty
        if( empty( get_user_meta( $current_user->ID, '_last_login', true ) ) ) {
            // update user last login date/time
            update_user_meta( $current_user->ID, '_last_login', wp_admin_set_date_time() ); 
        }
        
    }
    
endif;

add_action( 'admin_init', 'wp_admin_first_login_status', 1 );


// SET USER LOGGED IN STATUS AFTER LOG IN
if( ! function_exists('wp_admin_store_last_user_login') ) :

    function wp_admin_store_last_user_login( $user ) {
        
        // get current logged in user ID
        $current_user = get_user_by('login', $user);
        
        update_user_meta( $current_user->ID, '_last_login', wp_admin_set_date_time() ); 
        
        // update user login status to logged in
        update_user_meta( $current_user->ID, '_logged_in', 1 ); 
    }
    
endif;

add_action('wp_login', 'wp_admin_store_last_user_login', 10, 2);


// SET USER LOGGED IN STATUS AFTER LOG OUT
if( ! function_exists('wp_admin_user_logged_in') ) :

    function wp_admin_user_logged_in( $current_user ) {

        // get current logged in user ID
        $current_user = wp_get_current_user();

        // update user last logout date/time
        update_user_meta( $current_user->ID, '_last_logout', wp_admin_set_date_time() ); 
        
        // update user login status to logged out
        update_user_meta( $current_user->ID, '_logged_in', 0 ); 
    }

endif;

add_action('wp_logout', 'wp_admin_user_logged_in');


// SET USER LOGGED IN STATUS AFTER LOG OUT IF SESSION IS EXPIRED
add_action('auth_cookie_expired', function( $user ) {
    
    $user = get_user_by('login', $user['username']);
    
    if( $user ) {
        
        // update user last logout date/time
        update_user_meta( $user->ID, '_last_logout', wp_admin_set_date_time() ); 
        
        // update user login status to logged out
        update_user_meta( $user->ID, '_logged_in', 0 ); 
        
    }
    
}, 10, 1);


// USER ACTIVITY LIST
if( ! function_exists('wp_admin_list_online_users') ) :

    function wp_admin_list_online_users() {
        
        // check is multisite
        if( is_multisite() ) {				
            global $blog_id;
            $options = get_blog_option( $blog_id, 'wpat_settings_options' );
        } else {
            $blog_id = '1';
        }
        
        // get users by blog id
        $users = get_users( 'blog_id=' . $blog_id );      
        
        foreach( $users as $current_user ) {   
            
            // check last login date/time is false
            $getLastLogin = get_user_meta( $current_user->ID, '_last_login', true );
            
            if( empty( $getLastLogin ) ) {
                $last_user_login = esc_html__( 'N/A', 'wpat' );
            } else {
                $last_user_login = $getLastLogin;
            }
            
            // check last logout date/time is false
            $getLastLogout = get_user_meta( $current_user->ID, '_last_logout', true );
            
            if( empty( $getLastLogout ) ) {
                $last_user_logout = esc_html__( 'N/A', 'wpat' );
            } else {
                $last_user_logout = $getLastLogout;
            }
            
            // check if user login status is false            
            if( get_user_meta( $current_user->ID, '_logged_in', true ) ) {
                // check user is logged in
                $get_user_logged_in_status = get_user_meta( $current_user->ID );
                $is_logged_in = $get_user_logged_in_status['_logged_in'][0];
            } else {
                $is_logged_in = '0';
            }
            
            // get logged in status
            if( $is_logged_in != '1' ) {
                $login_status = esc_html__( 'is', 'wpat' ) . '<span class="user-status" style="background:#a5b1c2">' . esc_html__( 'logged out', 'wpat' ) . '</span>';
            } else { 
                $login_status = esc_html__( 'is', 'wpat' ) . '<span class="user-status" style="background:#20bf6b">' . esc_html__( 'logged in', 'wpat' ) . '</span>';
            }
            
            // show logged in status
            echo '<tr><td class="listing-img">' . get_avatar( $current_user->user_email, 64 ) . '</td><td><strong>' . esc_html( $current_user->display_name ) . '</strong> ' . $login_status . '<br><small>' . esc_html__( 'Last Login', 'wpat' ) . ': ' . esc_html( $last_user_login ) . '<br>' . esc_html__( 'Last Logout', 'wpat' ) . ': ' . esc_html( $last_user_logout ) . '</small></td>';
            
        }

    }
    
endif;
 

// CHANGE SESSION EXPIRATION FOR TESTING

/*
function wpdev_login_session( $user_id ) {
    return 10; // Set login session limit in seconds
}

add_filter( 'auth_cookie_expiration', 'wpdev_login_session' );
*/


// WP LOGGED IN USERS WIDGET --> CALLABLE WIDGET CONTENT

if( ! function_exists('wp_admin_logged_in_users_dashboard_widget_content') ) :

    function wp_admin_logged_in_users_dashboard_widget_content() { ?>

        <style>
            .wp-admin-users .table {margin:-15px}
            .wp-admin-users table {width:100%;border:0px;border-collapse:collapse}
            .wp-admin-users tr:nth-child(even) {background:#f8f9fb}
            .wp-admin-users th, .wp-admin-users td {padding:15px 16px;border-bottom:1px solid #eee}
            .wp-admin-users td.listing-img {padding:10px 0px 10px 16px;width:44px}
            .wp-admin-users td img {vertical-align:bottom;width:44px;height:44px;border-radius:50%}
            .wp-admin-users .user-status {padding:3px 6px;margin-left:5px;color:#fff;font-size:11px;border-radius:3px;line-height:1.1;display:inline-block;}
            .wp-admin-users small {font-size:12px;color:#82878c}
        </style>

        <div class="wp-admin-users">                
            <div class="table listing">                    
                <table>
                    <?php echo wp_admin_list_online_users(); ?>                        
                </table>                
            </div>            
        </div>

    <?php }

endif;


// WP LOGGED IN USERS WIDGET --> ADD DASHBOARD WIDGET

if( ! function_exists('wp_admin_logged_in_users_dashboard_widget') ) :

	function wp_admin_logged_in_users_dashboard_widget() {
        
		wp_add_dashboard_widget('logged_in_db_widget', esc_html__( 'User Activities', 'wpat' ), 'wp_admin_logged_in_users_dashboard_widget_content');

	}

endif;

add_action('wp_dashboard_setup', 'wp_admin_logged_in_users_dashboard_widget');