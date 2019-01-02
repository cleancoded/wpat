jQuery(function ($) {
	
	$(window).on('load', function() {
	
		$(document).ready(function() {
		
			/*****************************************************************/
			/* RESIZE + ON LOAD WRAPPER */
			/*****************************************************************/

			// run test on initial page load
			$(window).on('load', function() {
				check();
			});

			// run test on resize an scroll of the window
			$(window).resize(check);
			$(window).scroll(check);

			check();


			/*****************************************************************/
			/* WP ADMIN MENU CHECK */
			/*****************************************************************/

			var wpStickyMenu;

			function check() {
				if( $('body.sticky-menu').length ) {
					// Default Menu
					wpStickyMenu = true;
				} else {
					// Mobile Menu
					wpStickyMenu = false;
				}
			}


			/*****************************************************************/
			/* ADD BODY SPACER */
			/*****************************************************************/

			//$( 'body.wp-admin' ).wrapInner( '<div class="body-spacer">' );


			/*****************************************************************/
			/* ADD SUBSUBSUB REPLACE */
			/*****************************************************************/

			$('.subsubsub a .count').each(function() {
				var newValue = $(this).text().replace('(', '').replace(')', '');
				$(this).text( newValue );
			});

			$('.subsubsub a .count').fadeOut();
			$('.subsubsub a .count').fadeIn();


			/*****************************************************************/
			/* REORDER FIRST MENU ITEM */
			/*****************************************************************/

			// Avoiding flickering to reorder the first menu item (User Box) for left toolbar
			if( $("#adminmenu li:first-child").hasClass('adminmenu-container') ) {
				// nothing	
			} else {
				$("li.adminmenu-container").prependTo("#adminmenu");
				$("#adminmenu li.menu-top-first:first-child").show();
			}


			/*****************************************************************/
			/* WRAP LEFT WP MENU IMAGES */
			/*****************************************************************/

			$('#adminmenu .wp-menu-image img').wrap( '<span class="wp-menu-img-wrap"></span>' );	


			/*****************************************************************/
			/* GET RANGE INPUT FIELD VALUE */
			/*****************************************************************/

			// Send the range field value to the next element content
			$('.wpat-range-value').change( function() {

				var linkText = $(this).val();
				$(this).next().find('span').html(linkText);
				return false;                    

			}).change();
			

			/*****************************************************************/
			/* WP LEFT ADMIN MENU - EXPANDAPLE */
			/*****************************************************************/

			$('#adminmenu .wp-first-item').addClass('wp-has-submenu');

			$('.wp-admin-left-menu-expand #adminmenu li.wp-has-submenu.wp-not-current-submenu').each(function() {

				var SubMenuStartHeight = $(this).find('.wp-submenu').height();

				// Set sub menu height to null			
				if( wpStickyMenu === true ) {
					$(this).find('.wp-submenu').css('height', 0)
				} else {
					$(this).find('.wp-submenu').css('height', 'auto')
				}

				// Expand sub menu on click
				$(this).find('a.menu-top, a.menu-top-frist').on("click", function(e) {

					if( $(this).hasClass('expanded') ) {

						// Close the active sub menu on click				
						$(this).next('.wp-submenu').hide(100);

						if( wpStickyMenu === true ) {
							$(this).next('.wp-submenu').animate({ height : 0 }, 100);
						} else {
							$(this).next('.wp-submenu').css('height', 'auto');
						}

					} else {				

						// Close all other sub menus
						$('.wp-has-submenu.wp-not-current-submenu .wp-submenu').hide(100);

						if( wpStickyMenu === true ) {
							$('.wp-has-submenu.wp-not-current-submenu .wp-submenu').animate({ height : 0 }, 100);
						} else {
							$('.wp-has-submenu.wp-not-current-submenu .wp-submenu').css('height', 'auto');
						}

						$('.wp-has-submenu.wp-not-current-submenu').each(function() {
							if( $(this).hasClass('expanded') ) {
								$(this).toggleClass('expanded');
							}
						});				

						// Expand the active sub menu on click
						$(this).next('.wp-submenu').show(100);

						if( wpStickyMenu === true ) {
							$(this).next('.wp-submenu').animate({ height : SubMenuStartHeight }, 100);
						} else {
							$(this).next('.wp-submenu').css('height', 'auto');
						}

					}

					$(this).toggleClass('expanded');

					e.stopPropagation();
					e.preventDefault();

				});

			});

		});

	});
	
});