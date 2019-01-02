
#wpadminbar {
	background: #32373c;
	box-shadow: 0 0 15px rgba(0,0,0,0.2);
}

#wpadminbar #wp-admin-bar-my-account.with-avatar > .ab-empty-item img, 
#wpadminbar #wp-admin-bar-my-account.with-avatar > a img {
    height: 20px;
    border: none;
    border-radius: 50%;
    margin: -3px 0 0 12px;
	background: none!important;
}

.rtl #wpadminbar #wp-admin-bar-my-account.with-avatar > .ab-empty-item img, 
.rtl #wpadminbar #wp-admin-bar-my-account.with-avatar > a img {
    margin: -3px 12px 0px 0px;
}

<?php global $WPAT;

if( $WPAT['toolbar_wp_icon'] ) { ?>

	/****************************************/
	/* Remove the Toolbar WP Icon */
	/****************************************/

	#wp-admin-bar-wp-logo {
		display: none!important;
	}

<?php }

if( $WPAT['toolbar_icon'] ) { ?>

	/****************************************/
	/* Custom Toolbar Icon */
	/****************************************/

	#wpadminbar #wp-admin-bar-wp-logo > .ab-item .ab-icon:before {
		content: '';
		background-image: url('<?php echo esc_html( $WPAT['toolbar_icon'] ); ?>');
		background-position: center center;
		background-repeat: no-repeat;
		background-size: cover;
		position: absolute;
		top: 50%;
		margin-top: -10px;
    	width: 23px;
    	height: 23px;
	}

<?php } ?>