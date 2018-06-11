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
})(jQuery);