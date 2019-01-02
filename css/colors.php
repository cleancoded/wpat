<?php 

// get the selected theme color from wp admin

global $WPAT; 

if( $WPAT['theme_color'] ) {

// get rgb color vales from hex code

$hex = $WPAT['theme_color'];
list( $r_color, $g_color, $b_color ) = sscanf( $hex, "#%02x%02x%02x" ); ?>

    /****************************************/
    /* Custom Hover Color in RGB */
    /****************************************/

    .wrap .add-new-h2:hover, 
    .wrap .add-new-h2:active:hover, 
    .wrap .page-title-action:hover, 
    .wrap .page-title-action:active:hover,
    #minor-publishing-actions input:hover, 
    #major-publishing-actions input:hover, 
    #minor-publishing-actions .preview:hover,
    .wp-core-ui .button-primary:hover,
    .wp-core-ui button.button.button-primary:hover,
    .wp-core-ui input.button.button-primary:hover,
    button.button.button-hero:hover,
    .split-page-title-action a:hover, 
    .split-page-title-action:hover .expander:after,
	.block-editor .editor-block-list__insertion-point-inserter .editor-inserter__toggle, 
	.block-editor .components-icon-button:not(:disabled):not([aria-disabled=true]):not(.is-default):hover,
	.block-editor .editor-block-list__breadcrumb div {
        background: rgba(<?php echo $r_color; ?>, <?php echo $g_color; ?>, <?php echo $b_color; ?>, 0.6)!important;
    }

<?php }

if( $WPAT['theme_background'] && $WPAT['theme_background_end'] ) { ?>

    /****************************************/
    /* CSS Background-Body-Gradient */
    /****************************************/

    body.wp-admin {
        background: linear-gradient(to bottom right, <?php echo esc_html( $WPAT['theme_background'] ); ?>, <?php echo esc_html( $WPAT['theme_background_end'] ); ?>)!important;
        background-repeat: no-repeat!important;
        background-attachment: fixed!important;
    }

<?php }

if( $WPAT['theme_color'] ) { ?>

    /****************************************/
    /* CSS Background-Color */
    /****************************************/

    input[type="radio"]:checked:before,
    #adminmenu li.wp-has-current-submenu a.wp-has-current-submenu, 
    #adminmenu li.current a.menu-top, 
    .folded #adminmenu li.wp-has-current-submenu, 
    .folded #adminmenu li.current.menu-top, 
    #adminmenu .wp-menu-arrow, 
    #adminmenu .wp-has-current-submenu .wp-submenu .wp-submenu-head, 
    #adminmenu .wp-menu-arrow div,
    .wrap h1:after,
    .theme-browser .theme.active .theme-name,
    .widget.open .widget-top,
    #available-widgets .widget:hover .widget-top,
    .widgets-chooser li.widgets-chooser-selected,
    .menu li.menu-item-edit-active .menu-item-handle,
    .wp-core-ui .attachment.details .check, .wp-core-ui .attachment.selected .check:focus, .wp-core-ui .media-frame.mode-grid .attachment.selected .check,
    .post-state,
    .theme-browser .theme.add-new-theme a:hover:after, .theme-browser .theme.add-new-theme a:focus:after,
    .acf-field-group .acf-label label:after,
    .acf-radio-list label.selected,
    #adminmenu div.wp-menu-image .wp-menu-img-wrap,
	#adminmenu li.wp-has-submenu > a.expanded {
        background-color: <?php echo esc_html( $WPAT['theme_color'] ); ?>;
    }

    /****************************************/
    /* CSS Background-Color - IMPORTANT */
    /****************************************/

    .wrap .add-new-h2, 
    .wrap .add-new-h2:active, 
    .wrap .page-title-action, 
    .wrap .page-title-action:active,
    #minor-publishing-actions input, 
    #major-publishing-actions input, 
    #minor-publishing-actions .preview,
    .wp-core-ui .button-primary,
    .wp-core-ui button.button.button-primary
    .wp-core-ui input.button.button-primary,
    button.button.button-hero,
    .wp-core-ui .button-primary[disabled],
    .acf-switch.-on,
    .switch-light.switch-yoast-seo a, .switch-toggle.switch-yoast-seo a,
    .select2-container--default .select2-results__option--highlighted[aria-selected],
    .acf-image-uploader .acf-actions a,
    .acf-field-object.open > .handle,
    .acf-box .footer,
    .composer-switch a,
    .wpat-logout-button,
    .split-page-title-action a, 
    .split-page-title-action a:active, 
    .split-page-title-action .expander:after {
        background-color: <?php echo esc_html( $WPAT['theme_color'] ); ?>!important;
    }

    /****************************************/
    /* CSS Border-Color */
    /****************************************/

    #adminmenu .wp-submenu, 
    .folded #adminmenu a.wp-has-current-submenu:focus + .wp-submenu, 
    .folded #adminmenu .wp-has-current-submenu .wp-submenu,
    .plugins .active th.check-column, .plugin-update-tr.active td,
    .theme-browser .theme.active,
    .menu li.menu-item-edit-active .menu-item-handle,
    .acf-radio-list label.selected,
    .block-editor .editor-inserter__tab.is-active,
    .block-editor .edit-post-sidebar__panel-tab.is-active,
    .split-page-title-action a, 
    .split-page-title-action a:active, 
    .split-page-title-action .expander:after {
        border-color: <?php echo esc_html( $WPAT['theme_color'] ); ?>!important;
    }

    /****************************************/
    /* CSS Border-Color - IMPORTANT */
    /****************************************/

    .wrap .add-new-h2, 
    .wrap .add-new-h2:active, 
    .wrap .page-title-action, 
    .wrap .page-title-action:active,
    #minor-publishing-actions input, 
    #major-publishing-actions input, 
    #minor-publishing-actions .preview,
    .wp-core-ui .button-primary,
    .wp-core-ui button.button.button-primary
    .wp-core-ui input.button.button-primary,
    .widget.open .widget-top,
    #available-widgets .widget .widget-top:hover,
    .acf-field-object.open > .handle {
        border-color: <?php echo esc_html( $WPAT['theme_color'] ); ?>!important;
    }

    /****************************************/
    /* CSS Border-Right-Color - IMPORTANT */
    /****************************************/

    #adminmenu .wp-has-submenu.opensub:after {
        border-right-color: <?php echo esc_html( $WPAT['theme_color'] ); ?>!important;
    }

    /****************************************/
    /* CSS BOX-SHADOW */
    /****************************************/

    .wp-core-ui .attachment.details {
        box-shadow: inset 0 0 0 3px #fff, inset 0 0 0 7px <?php echo esc_html( $WPAT['theme_color'] ); ?>;
    }

    /****************************************/
    /* CSS COLOR */
    /****************************************/

    a, .wp-core-ui .button-link, .media-menu > a, .media-frame a,
    #adminmenu a:hover, 
    #adminmenu li.menu-top.wp-not-current-submenu > a:focus, 
    #adminmenu .wp-submenu a:hover, 
    #adminmenu .wp-submenu a:focus, 
    #adminmenu li.opensub > a.menu-top,
    #adminmenu div.wp-menu-image:before,
    .wrap h1,
    table.widefat tr:hover td a:not(.submitdelete):not(.delete):not(.vim-u),
    h2,
    .theme-browser .theme.add-new-theme a:hover span:after, .theme-browser .theme.add-new-theme a:focus span:after,
    .acf-field-message > .acf-label label {
        color: <?php echo esc_html( $WPAT['theme_color'] ); ?>;
    }

    /****************************************/
    /* CSS COLOR - IMPORTANT */
    /****************************************/

    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove,
    .acf-tab-wrap.-left .acf-tab-group li a,
    .acf-field-group .acf-label label,
    #woocommerce_dashboard_status .wc_status_list li a strong {
        color: <?php echo esc_html( $WPAT['theme_color'] ); ?>;
    }

    /****************************************/
    /* RTL - CSS Border-Left-Color - IMPORTANT */
    /****************************************/

    .rtl #adminmenu .wp-has-submenu.opensub:after {
        border-right-color: transparent!important;
        border-left-color: <?php echo esc_html( $WPAT['theme_color'] ); ?>!important;
    }

    /****************************************/
    /* RTL - CSS Border-Right-Color - IMPORTANT */
    /****************************************/

    .rtl #adminmenu .wp-submenu {
        border-left-color: transparent!important;
        border-right-color: <?php echo esc_html( $WPAT['theme_color'] ); ?>!important;
    }

    /****************************************/
    /* CSS OUTLINE - IMPORTANT */
    /****************************************/

	.block-editor .editor-block-list__layout .editor-block-list__block.is-hovered > .editor-block-list__block-edit:before {
		outline-color: <?php echo esc_html( $WPAT['theme_color'] ); ?>!important;
	}

<?php }

if( $WPAT['toolbar'] ) { ?>

	/****************************************/
	/* Remove the Toolbar */
	/****************************************/

	html.wp-toolbar {
		padding-top: 0px!important
	}

    body.wp-admin-toolbar-hide.wp-admin-spacing #wpcontent,
    body.wp-admin-toolbar-hide.wp-admin-spacing #wpbody {
        min-height: calc(100vh - 172px);
    }

	.wp-admin-toolbar-hide #wpadminbar {
		display:none!important
	}

	.wp-admin-toolbar-hide .vc_fullscreen .vc_navbar, .vc_subnav-fixed {
		top: 0px!important;
	}

    .wp-admin-toolbar-hide .block-editor .block-editor__container {
        min-height: calc(100vh - 0px)!important;
    }

    .wp-admin-toolbar-hide.wp-admin-spacing .block-editor .block-editor__container {
        min-height: calc(100vh - 172px)!important;
    }

    .wp-admin-toolbar-hide .block-editor .edit-post-header {
        top: 0px;
    }

    .wp-admin-toolbar-hide .block-editor .edit-post-sidebar {
        top: 56px;
    }

    @media only screen and (max-width: 1249px) {

        body.wp-admin-toolbar-hide.wp-admin-spacing #wpcontent, 
        body.wp-admin-toolbar-hide.wp-admin-spacing #wpbody {
            min-height: calc(100vh - 140px);
        }

    }

	@media only screen and (max-width: 782px) {
	
		body.wp-admin-toolbar-hide.wp-admin {
			padding-top: 46px!important;
		}

        body.wp-admin-toolbar-hide.wp-admin-spacing #wpcontent, 
        body.wp-admin-toolbar-hide.wp-admin-spacing #wpbody {
            min-height: calc(100vh - 56px);
        }
	
		.wp-admin-toolbar-hide #wpadminbar {
			display:block!important
		}
		
		.wp-admin-toolbar-hide .wpat-logout {
			display:none!important
		}        

        body.wp-admin-toolbar-hide .block-editor .edit-post-header {
            top: 46px;
        }      

        body.wp-admin-toolbar-hide.wp-admin-spacing .block-editor .edit-post-header {
            top: 0px;
        }

        body.wp-admin-toolbar-hide .block-editor .edit-post-layout.has-fixed-toolbar {
            padding-top: 46px;
        }

        body.wp-admin-toolbar-hide.wp-admin-spacing .block-editor .edit-post-layout.has-fixed-toolbar {
            padding-top: 0px;
        }

        body.wp-admin-toolbar-hide .block-editor .edit-post-layout.has-fixed-toolbar .edit-post-layout__content {
            padding-top: 0px;
        }
	
	}
	
	@media only screen and (max-width: 600px) {
	
		body.wp-admin-toolbar-hide.wp-admin {
			padding-top: 0px!important;
		}

        body.wp-admin-toolbar-hide.wp-admin-spacing .block-editor .edit-post-header {
            top: 46px;
        }

        body.wp-admin-toolbar-hide.wp-admin-spacing .block-editor .edit-post-layout.has-fixed-toolbar {
            padding-top: 46px;
        }
	
	}

<?php }

if( $WPAT['spacing'] ) { ?>

	/****************************************/
	/* Remove the Spacing */
	/****************************************/

	body.wp-admin {
		padding: 0px!important;
	}

<?php }

if( $WPAT['spacing'] && $WPAT['toolbar'] ) { ?>

	html.wp-toolbar {
		padding-top: 0px!important
	}
	
	/****************************************/
	/* Logout Button */
	/****************************************/
	
	.wpat-logout {
		top: 0px;
		right: 0px;
		z-index: 9999;
	}

    .block-editor-page .wpat-logout {
		top: auto;
		bottom: 0px;
	}
	
	.wpat-logout-button {
		position: relative;
		background: #4777CD;
		width: 29px;
    	height: 29px;
		text-align: center
	}
	
	.wpat-logout-button:before {
		content: "\f110";
		font-family: dashicons;
		color: #fff;
		font-size: 20px		
	}
	
	.wpat-logout-content {
		position: absolute;
		top: -100px;
		right: 29px;
    	height: 19px;
    	padding: 5px;
		background: #4777CD;
        white-space: nowrap;
	}
	
	.block-editor-page .wpat-logout-content {
		position: fixed;
		top: auto;
		bottom: -100px;
	}
	
	.wpat-logout-content a {
		margin: 0px 3px;
	}
	
	.wpat-logout:hover .wpat-logout-content {
		top: 0px;
	}

    .block-editor-page .wpat-logout:hover .wpat-logout-content {
        top: auto;
		bottom: 0px;
	}
	
	@media only screen and (max-width: 782px) {
	
		.wpat-logout {
			display:none!important
		}
	
	}

<?php } elseif( $WPAT['spacing'] && ! $WPAT['toolbar'] ) { ?>

	html.wp-toolbar {
		padding-top: 50px
	}

    @media only screen and (max-width: 782px) {
	
		html.wp-toolbar {
            padding-top: 46px
        }
	
	}

    @media only screen and (max-width: 600px) {
	
		html.wp-toolbar {
            padding-top: 0px
        }
	
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
    	width: 26px;
    	height: 26px;
	}

<?php }

if( $WPAT['google_webfont'] ) { ?>

	/****************************************/
	/* Custom Google Webfont */
	/****************************************/

	body {
		font-family: "<?php echo str_replace( '+', ' ', esc_html( $WPAT['google_webfont'] ) ); ?>"!important
	}

<?php }

if( $WPAT['spacing_max_width'] ) { ?>

	/****************************************/
	/* Remove the Toolbar WP Icon */
	/****************************************/

	.body-spacer {
		max-width: <?php echo esc_html( $WPAT['spacing_max_width'] ); ?>px!important;
	}

<?php }

if( $WPAT['css_admin'] ) { ?>

    /****************************************/
    /* Custom CSS BY USER */
    /****************************************/

    <?php echo esc_html( $WPAT['css_admin'] ); 
                      
}

if( $WPAT['left_menu_width'] ) { ?>

	/****************************************/
	/* Custom Left Menu Width */
	/****************************************/

	#adminmenuback, 
	#adminmenuwrap {
		width: <?php echo esc_html( $WPAT['left_menu_width'] ); ?>px;
	}

	#adminmenu,
	#adminmenu .wp-submenu {
		width: <?php echo esc_html( $WPAT['left_menu_width'] - 40 ); ?>px;
	}

	#adminmenu .wp-submenu {
		left: <?php echo esc_html( $WPAT['left_menu_width'] - 40 ); ?>px;
	}

	.block-editor .edit-post-header,
	.auto-fold .components-notice-list {
		left: <?php echo esc_html( $WPAT['left_menu_width']); ?>px!important;
	}

	#wpcontent {
		margin-left: <?php echo esc_html( $WPAT['left_menu_width'] ); ?>px;
	}

	#wpfooter {
		margin-left: <?php echo esc_html( $WPAT['left_menu_width'] - 40 ); ?>px;
	}

<?php } ?>