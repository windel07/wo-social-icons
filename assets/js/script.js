(function($) {
	// Convert query string to object
	$.extend({
		getQueryParameters : function(str) {
			return ( str || document.location.search ).replace( /(^\?)/, '' ).split( "&" ).map(function(n){
				return n = n.split( "=" ), this[n[0]] = n[1],this
			}.bind({}))[0];
		}
	});	

	// WO_SocialIcons
	function WO_SocialIcons( el ) {
		this.element = el;
	}

	/**
	 * Initilialize sortable.
	 */
	WO_SocialIcons.prototype.init = function() {
		this.element
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
						icons 		: newIcons
					},
					error 		: function( r ) {
						console.log( r );
					}
				});
			}
		})
		.disableSelection();

		this.element.find( '.wosi-color-picker' ).wpColorPicker({
			change 			: function( e, ui ) {
				$( "#widget-" + wosi.id + "-savewidget" ).removeAttr( 'disabled' );
			}
		});
	};

	$( document ).on( 'widget-added widget-updated', function( e, wdg ) {
		if( wosi.widget_options.classname != 'wo_social_icons' ) return;

		// Initialize sortable
		var w = new WO_SocialIcons( $( wdg ).find( '.wo_social_icons-sortable' ) );
		w.init();
	});

	$(function() {
		// Initialize sortable
		var w = new WO_SocialIcons( $( "." + wosi.widget_options.classname + "-sortable" ) );
		w.init();
	});
})(jQuery);