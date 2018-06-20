(function($) {
	// Convert query string to object
	$.extend({
		getQueryParameters : function(str) {
			return ( str || document.location.search ).replace( /(^\?)/, '' ).split( "&" ).map(function(n){
				return n = n.split( "=" ), this[n[0]] = n[1],this
			}.bind({}))[0];
		}
	});	

	/**
	 * Renders WP Media Uploader.
	 *
	 * Displays the media uploader for selecting an image.
	 *
	 * @since 0.1.0
	 */
	function renderMediaUploader( mediaContainer ) {
	    'use strict';
	 
	    var fileFrame, 
	    	file,
	    	iconContainer;
	
	    if( fileFrame !== undefined ) {
	        fileFrame.open();
	        return;
	    }
	 
	    fileFrame = wp.media.frames.fileFrame = wp.media({
	    	title 		: 'Select or Upload your icon.',
	        frame 		: 'post',
	        state 		: 'insert',
	        library 	: {
	        	type 		: [ 'image/svg', 'image/svg+xml', 'image/png' ]
	        },
	        multiple 	: false,
	        button 		: {
	    		text 		: 'Use this icon'
	    	}
	    });
	 
	    /**
	     * On insert
	     */
	    fileFrame.on( 'insert', function() {
			var attachment = fileFrame.state().get( 'selection' ).first().toJSON();

			mediaContainer.find( '.wosi-icon-holder' ).html( '<img src="' + attachment.url + '" alt="' + attachment.title + '"/>' );
			mediaContainer.find( 'input[type="hidden"]' ).val( attachment.id );

			mediaContainer.find( '.wosi-media-buttons .wosi-add-media' ).addClass( 'hidden' );
			mediaContainer.find( '.wosi-media-buttons .wosi-delete-media' ).removeClass( 'hidden' );
	    });
	 
	    fileFrame.open();

	    $( "#widget-" + wosi.id + "-savewidget" ).removeAttr( 'disabled' );
	}

	/**
	 * Remove media.
	 */
	function removeMedia( mediaContainer ) {
		'use strict';

		mediaContainer.find( '.wosi-icon-holder' ).html( '' );
	    mediaContainer.find( 'input[type="hidden"]' ).val( '' );

	    mediaContainer.find( '.wosi-media-buttons .wosi-add-media' ).removeClass( 'hidden' );
		mediaContainer.find( '.wosi-media-buttons .wosi-delete-media' ).addClass( 'hidden' );

		$( "#widget-" + wosi.id + "-savewidget" ).removeAttr( 'disabled' );
	}

	/**
	 * Initilialize.
	 */
	function init( el ) {
		var element = $( el );

		element
		.accordion({
			active 			: false,
			header 			: "> li > h3",
			heightStyle  	: "content",
			collapsible 	: true
		})
		.sortable({
			axis 			: "y",
			handle 			: "h3",
			items 			: "> li",
			placeholder 	: "wosi-state-highlight",
			stop 			: function( e, ui ) {
				ui.item.children( "h3" ).triggerHandler( "focusout" );
			},
			update 			: function( e, ui ) {
				var newIcons = $( this ).sortable( "toArray" );

				$( this ).accordion( "refresh" );
				$( "#widget-" + wosi.id + "-savewidget" ).removeAttr( 'disabled' );

				$.ajax({
					url 		: wosi.ajax.url,
					method 		: 'POST',
					dataType 	: 'json',
					data 		: {
						action 		: wosi.ajax.actions.reorder,
						number 		: $( ui.item ).attr( 'data-number' ),
						icons 		: newIcons
					},
					success 	: function( r ) {
						console.log( r );
					},
					error 		: function( r ) {
						console.log( r );
					}
				});
			}
		})
		.disableSelection();

		element.find( '.wosi-color-picker' ).wpColorPicker({
			change 			: function( e, ui ) {
				$( "#widget-" + wosi.id + "-savewidget" ).removeAttr( 'disabled' );
			}
		});
	}

	function onFormUpdate( e, wdg ) {
		init( $( wdg ).find( '.wo_social_icons-sortable' ) );
	}

	// On widget add
	$( document ).on( 'widget-added widget-updated', onFormUpdate );

	// On document ready
	$(function() {
		// Initialize
		$( '#widgets-right .widget:has(.wo_social_icons-sortable)' ).each(function() {
			init( $( this ).find( '.wo_social_icons-sortable' ) );
		});
	});

	$( document ).on( 'click', '.wosi-add-media', function( e ) {
        e.preventDefault();
        var _this = $( this ),
        	media = _this.closest( '.wosi-media' );

        renderMediaUploader( media );
    });

    $( document ).on( 'click', '.wosi-delete-media', function( e ){
	    e.preventDefault();
	    var _this = $( this ),
	    	media = _this.closest( '.wosi-media' );

	    removeMedia( media );
	  });
})(jQuery);