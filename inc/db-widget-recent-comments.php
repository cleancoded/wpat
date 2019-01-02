<?php 

/*****************************************************************/
/* WP RECENT COMMENTS DASHBOARD WIDGET */
/*****************************************************************/

// WP RECENT COMMENTS WIDGET --> CALLABLE WIDGET CONTENT

if( ! function_exists('wp_admin_recent_comments_widget_content') ) :

    function wp_admin_recent_comments_widget_content() { 

        $args = array(
            'number' => 5,
            'status' => array( 'hold', 'approve' ),
        ); 

        $comments_query = new WP_Comment_Query();
        $comments = $comments_query->query( $args ); ?>
        <style>
            .wpat-comments-list {margin:-15px}
            .wpat-comments-list table {width:100%;border-collapse:collapse}
            .wpat-comments-list tr:nth-child(even) {background:#f8f9fb}
            .wpat-comments-list tr {margin:0px;padding:10px 15px;border-bottom:1px solid #eee}
            .wpat-comments-list tr.unapproved {background:#fdfaf1}
            .wpat-comments-list td {padding:15px;vertical-align: middle}
            .wpat-comments-list td:last-child {border:0px}
            .wpat-comments-list td.wpat-comments-list-img {width:44px;vertical-align:top;padding-right:0px}
            .wpat-comments-list img {float:left;width:44px;height:44px;border-radius:50%}
            .wpat-comments-list p {color:#82878c;margin:6px 0px 0px 0px}
            .wpat-comments-list p a:not(:hover) {color:#82878c}
            .wpat-comments-list p:nth-child(3) {font-size:12px;}
        </style>

        <div class="wpat-comments-list">
            <?php if ( $comments ) : ?>
            
                <table>
                    <?php foreach ( $comments as $comment ) :   
        
                        $commen_status = wp_get_comment_status( $comment->comment_ID );
        
                        if( $commen_status == 'unapproved' ) {
                            $status_label = esc_html__( 'Approve', 'wpat' );
                            $status_base_url = admin_url( 'comment.php?action=approvecomment&p=' . $comment->comment_post_ID . '&c=' . $comment->comment_ID );
                        } else {
                            $status_label = esc_html__( 'Unapprove', 'wpat' );
                            $status_base_url = admin_url( 'comment.php?action=unapprovecomment&p=' . $comment->comment_post_ID . '&c=' . $comment->comment_ID );
                        }
        
                        //$spam_base_url = admin_url( 'comment.php?action=spamcomment&p=' . $comment->comment_post_ID . '&c=' . $comment->comment_ID );
                        //$trash_base_url = admin_url( 'comment.php?action=trashcomment&p=' . $comment->comment_post_ID . '&c=' . $comment->comment_ID );  
                    
                        $status_url = wp_nonce_url( $status_base_url, 'approve-comment_'. $comment->comment_ID );
                        //$spam_url = wp_nonce_url( $spam_base_url, 'spam-comment_'. $comment->comment_ID );
                        //$trash_url = wp_nonce_url( $trash_base_url, 'delete-comment_'. $comment->comment_ID );
        
                        $edit_url = admin_url( 'comment.php?action=editcomment&c=' . $comment->comment_ID );  
                        
                        $status = '<a href="' . $status_url . '">' . $status_label . '</a>';
                        //$spam = '<a href="' . $spam_url . '">' . esc_html__( 'Spam', 'wpat' ) . '</a>';
                        //$trash = '<a href="' . $trash_url . '">' . esc_html__( 'Trash', 'wpat' ) . '</a>';
                        $edit = '<a href="' . $edit_url . '">' . esc_html__( 'Edit', 'wpat' ) . '</a>'; ?>
                    
                        <tr class="<?php echo $commen_status; ?>">
                            <td class="wpat-comments-list-img">
                                <?php echo get_avatar( $comment->comment_author_email, 64 ); ?>
                            </td>
                            <td>
                                <a class="comment-title" href="<?php echo get_permalink( $comment->comment_post_ID ) ?>#comment-<?php echo esc_html( $comment->comment_ID ); ?>">
                                    <?php echo esc_html( get_comment_author( $comment->comment_ID ) ); ?>
                                    <?php echo esc_html__( 'on', 'wpat' ); ?>
                                    <?php echo esc_html( get_the_title($comment->comment_post_ID) ); ?>
                                </a>
                                <p><?php echo strip_tags( substr( apply_filters( 'get_comment_text', $comment->comment_content ), 0, 100 ) ); ?> ...</p>
                                <p><?php echo $status . ' | ' . $edit; ?></p>
                            </td>
                        </tr>

                    <?php endforeach; ?>
                </table>

            <?php endif; ?>            
        </div>

    <?php }

endif;

// WP RECENT COMMENTS WIDGET --> ADD DASHBOARD WIDGET

if( ! function_exists('wp_admin_recent_comments_widget') ) :

	function wp_admin_recent_comments_widget() {

		wp_add_dashboard_widget('wp_recent_comments_db_widget', esc_html__( 'Recent Comments', 'wpat' ), 'wp_admin_recent_comments_widget_content');

	}

endif;

add_action('wp_dashboard_setup', 'wp_admin_recent_comments_widget');