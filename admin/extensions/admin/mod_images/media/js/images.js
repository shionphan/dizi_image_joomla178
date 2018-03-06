var _images = [];
var _quantity = 5;

var _rl = [];
var _rc = 0;
var _rcb = 0;

//  upload
function onFiles( files ){
	jQuery( '#progress > .progress-bar' ).width( 0 );
	jQuery( '#progress' ).show();
	
	var file = typeof files[ 0 ] !== 'undefined' && files[ 0 ] ? files[ 0 ] : null;
	if( file ){
		jQuery( '#progress > .progress-bar' ).width( 0 );
		
		var xhr = FileAPI.upload( {
			url: di.url.ajax + '?option=com_di&task=ajax.upload&object_id=' + di.object_id + '&session_name=' + di.form_data.session_name + '&session_id=' + di.form_data.session_id,
			data: {},
			files: {
				Filedata: file
			},
			progress: function ( evt, file, xhr, options ){
				jQuery( '#progress > .progress-bar' ).width( evt.loaded / evt.total * 100 + "%" );
			},
			complete: function ( err, xhr ){
				jQuery( '#progress' ).hide();
				
				if( !err ){
					jQuery.ajax( {
						type: 'GET',
						url: di.url.ajax,
						dataType: 'JSON',
						data: {
							option: 'com_di',
							task: 'ajax.getImages',
							object_id: di.object_id
						},
						error: function(){
							//  alert('Error loading XML document');
						},
						success: function( response ){
							if( parseInt( response.status ) > 0 && typeof( response.data ) !== 'undefined' ){
								jQuery( '#diImageList .item' ).remove();
								
								for( var i = 0; i < response.data.length; i++ ){
									if( typeof( response.data[ i ] ) == 'object' ){
										response.data[ i ].media_url 	= di.url.media;
										response.data[ i ].di_size 		= di.size;
										
										response.data[ i ].featured_icon = 'star-empty';
										if( response.data[ i ].featured == 1 ){
											response.data[ i ].featured_icon = 'star';
										}
										
										var li = '';
											li += '<li id="nli_' + response.data[ i ].object_image_id + '" class="item ' + response.data[ i ].featured + '" data-object-image-id="' + response.data[ i ].object_image_id + '" data-object-id="' + response.data[ i ].object_id + '" data-state="' + response.data[ i ].state + '" data-title="' + response.data[ i ].title + '" data-description="' + response.data[ i ].description + '" data-featured="' + response.data[ i ].featured + '" data-filename="' + response.data[ i ].filename + '" data-link="' + response.data[ i ].link + '" data-link-target="' + response.data[ i ].link_target + '">';
												li += '<input type="hidden" name="jform[object_image_id][]" value="' + response.data[ i ].object_image_id + '" />';
												li += '<table class="preview-container"><tr><td><img class="preview" src="' + response.data[ i ].media_url + '/' + response.data[ i ].object_id + '_' + response.data[ i ].object_image_id + '_' + response.data[ i ].di_size + '_' + response.data[ i ].filename + '" alt="' + response.data[ i ].title + '" /></td></tr></table>';
												li += '<div class="tool drag-selector" title="' + di.labels.drag_title + '"><span class="dicon-move"></span></div>';
												li += '<div class="tool toggle-featured" title="' + di.labels.feature_title + '" data-featured="' + response.data[ i ].featured + '"><span class="dicon-' + response.data[ i ].featured_icon + '"></span></div>';
												li += '<div class="tool check"><input type="checkbox" name="di[check][]" value="' + response.data[ i ].object_image_id + '" /></div>';
												li += '<a class="tool edit" title="' + di.labels.edit_title + '" href="#"><span class="dicon-edit"></span></a>';
												li += '<a class="tool remove" title="' + di.labels.remove_title + '" href="#"><span class="dicon-remove"></span></a>';
											li += '</li>';
										
										jQuery( '#diImageList' ).append( li );
									}
								}
								
								if( jQuery( '#diImageList .item' ).length ){
									jQuery( '.di-toolbar' ).addClass( 'visible' );
								}
							}
						}
					} );
				}
				
				//  forward
				files.splice( 0, 1 );
				if( files.length > 0 ){
					onFiles( files );
				}
			}
		} );
	}
};

jQuery( function(){
	var hash = location.hash.substring( 1 );
	
	if( typeof( hash ) !== 'undefined' && hash && hash == di.url.hash ){
		jQuery( '.nav-tabs:first A[href="#' + di.url.hash + '"]' ).trigger( 'click' );
	}
	
	//  drag and drop
	if( FileAPI.support.dnd ){
		jQuery( document ).dnd( function( over ){
			jQuery( '#drop-zone' ).addClass( 'over' );
		}, function( files ){
			onFiles( files );
		} );
	}
	else{
		jQuery( '#drop-zone' ).hide();
	}
	
	//  files change
	jQuery( '#choose' ).on( 'change', function( evt ){
		var files = FileAPI.getFiles( evt );
		onFiles( files );
		FileAPI.reset( evt.currentTarget );
	} );
	
	//  sortable image list
	jQuery( '#diImageList' ).sortable( {
		placeholder: 'ui-state-highlight',
		handle : '.drag-selector',
		update : function () {
			jQuery.ajax( {
				type: 'GET',
				url: di.url.ajax + '?' + jQuery( this ).sortable( 'serialize' ),
				dataType: 'JSON',
				data: {
					option: 'com_di',
					task: 'ajax.order',
					object_id: di.object_id
				},
				error: function(){
					alert('Error loading XML document');
				},
				success: function( response ){
					//  note about success...
				}
			} );
		}
	} );
	//  /sortable image list
	
	//  delete image link click
	jQuery( '#diImageList .remove' ).live( 'click', function( event ){
		event.preventDefault();
		
		var element = jQuery( this ).parents( '.item' );
		
		jQuery.ajax( {
			type: 'GET',
			url: di.url.ajax,
			dataType: 'JSON',
			data: {
				option: 'com_di',
				task: 'ajax.remove',
				object_image_id: element.attr( 'data-object-image-id' )
			},
			error: function(){
				//  alert('Error loading XML document');
			},
			success: function( response ){
				if( typeof( response.status ) !== 'undefined' && parseInt( response.status ) > 0 && typeof( response.data ) !== 'undefined' ){
					for( var i = 0; i < response.data.length; i++ ){
						jQuery( '#diImageList .item[data-object-image-id="' + response.data[ i ] + '"]' ).remove();
					}
				}
				
				if( !jQuery( '#diImageList .item' ).length ){
					jQuery( '#di-toolbar' ).removeClass( 'visible' );
				}
			}
		} );
	} );
	//  /delete image link click
	
	//  select/deselect all
	jQuery( '.di-toggle-select' ).click( function( event ){
		var input = jQuery( this ).find( 'INPUT' );
		
		if( input.is(':checked') ){
			jQuery( '#diImageList .item' ).addClass( 'active' );
			jQuery( '#di .delete-selected' ).addClass( 'active' );
			jQuery( '#di INPUT[name="di[check][]"]' ).attr( 'checked', true );
			jQuery( '#di .di-toggle-select span' ).text( di.labels.deselect_all );
		}
		else{
			jQuery( '#diImageList .item' ).removeClass( 'active' );
			jQuery( '#di .delete-selected' ).removeClass( 'active' );
			jQuery( '#di INPUT[name="di[check][]"]' ).attr( 'checked', false );
			jQuery( '#di .di-toggle-select span' ).text( di.labels.select_all );
		}
	} );
	//  /select/deselect all
	
	//  select/deselect click
	jQuery( '#diImageList INPUT[name="di[check][]"]' ).live( 'click', function(){
		jQuery( this ).parents( '.item' ).toggleClass( 'active' );
		
		if( jQuery( '#diImageList INPUT[name="di[check][]"]:checked' ).length ){
			jQuery( '#di .delete-selected' ).addClass( 'active' );
		}
		else{
			jQuery( '#di .delete-selected' ).removeClass( 'active' );
		}
	} );
	//  /select/deselect click
	
	//  delete selected
	jQuery( '.delete-selected' ).click( function( event ){
		event.preventDefault();
		
		var ids = '';
		jQuery( '#diImageList INPUT[name="di[check][]"]:checked' ).each( function(){
			ids += jQuery( this ).parents( '.item' ).attr( 'data-object-image-id' ) + ',';
		} );
		
		jQuery.ajax( {
			type: 'GET',
			url: di.url.ajax,
			dataType: 'JSON',
			data: {
				option: 'com_di',
				task: 'ajax.remove',
				object_image_id: jQuery.trim( ids )
			},
			error: function(){
				//  alert('Error loading XML document');
			},
			success: function( response ){
				if( typeof( response.status ) !== 'undefined' && parseInt( response.status ) > 0 && typeof( response.data ) !== 'undefined' ){
					for( var i = 0; i < response.data.length; i++ ){
						jQuery( '#diImageList .item[data-object-image-id="' + response.data[ i ] + '"]' ).remove();
					}
				}
				
				if( jQuery( '#diImageList INPUT[name="di[check][]"]:checked' ).length ){
					jQuery( '#di .delete-selected' ).addClass( 'active' );
				}
				else{
					jQuery( '#di .delete-selected' ).removeClass( 'active' );
				}
				
				if( !jQuery( '#diImageList .item' ).length ){
					jQuery( '.di-toolbar' ).removeClass( 'visible' );
					jQuery( '.di-toolbar .di-toggle-select INPUT' ).attr( 'checked', false );
				}
			}
		} );
	} );
	//  /delete selected
	
	//  resize images
	jQuery( '.resize-images' ).click( function( event ){
		event.preventDefault();
		
		var element = jQuery( this );
			element.attr( 'disabled', true );
		
		jQuery.fancybox( {
			hideOnOverlayClick: false,
			overlayColor: '#FFF',
			overlayOpacity: 0.9,
			padding: 0,
			margin: 10,
			modal: true,
			content: function(){
				return jQuery( '.di-resize-status' ).clone();
			},
			onStart: function(){
				var resize_status = jQuery( '.di-resize-status .big-box' );
					resize_status.height( jQuery( window ).height() - 100 );
					resize_status.width( jQuery( window ).width() - 100 );
			}
		} );
		
		jQuery( '#diImageList .item' ).each( function(){
			var el = jQuery( this );
			var el_checkbox = el.find( 'INPUT[name="di[check][]"]:checked' );
			
			if( el_checkbox.length )
				_images.push( el.attr( 'data-object-image-id' ) );
		} );
		
		// if there is no selection, resizing all images
		if( !_images.length )
			jQuery( '#diImageList .item' ).each( function(){
				var el = jQuery( this );
				
				_images.push( el.attr( 'data-object-image-id' ) );
			} );
		
		if( _rc == 0 ){
			_createLists();
			_resize( _rc );
		}
	} );
	
	function _createLists(){
		for( var i in _images ){
			var index = parseInt( i / _quantity );
			if( typeof( _rl[ index ] ) == 'undefined' ){
				_rl[ index ] = [];
			}
			
			_rl[ index ].push( _images[ i ] );
		}
	}
	
	function _resize(){
		if( typeof( _rl[ _rc ] ) !== 'undefined' ){
			var is = '';
			for( var i = 0; i < _rl[ _rc ].length; i++ ){
				is += '&object_image_id[]=' + _rl[ _rc ][ i ];
				_rcb++;
			}
			_rc++;
			
			jQuery.ajax( {
				type: 'GET',
				url: di.url.ajax + '?' + is,
				dataType: 'JSON',
				data: {
					option: 'com_di',
					task: 'ajax.resize',
					object_id: di.object_id
				},
				error: function(){
					alert('Error loading XML document');
				},
				success: function( data ){
					jQuery( '#fancybox-content .bar' ).width( parseInt( jQuery( '#fancybox-content .progress' ).width() / _images.length * _rcb ) );
					
					_resize();
				}
			} );
		}
		else{
			_rc = 0;
			_rcb = 0;
			
			jQuery( '.resize-images' ).attr( 'disabled', false );
			jQuery.fancybox.close();
		}
	}
	//  /resize images
	
	//  edit item
	jQuery( '#diImageList .item .edit' ).live( 'click', function( event ){
		event.preventDefault();
		
		var item = jQuery( this ).parents( '.item' );
		
		jQuery.fancybox( {
			hideOnOverlayClick: false,
			hideOnContentClick: false,
			overlayColor: '#FFF',
			overlayOpacity: 0.9,
			padding: 0,
			margin: 10,
			modal: true,
			content: function(){
				return jQuery( '.di-edit-box' ).clone();
			},
			onStart: function(){
				var resize_status = jQuery( '.di-edit-box .big-box' );
					resize_status.height( jQuery( window ).height() - 100 );
					resize_status.width( jQuery( window ).width() - 100 );
			},
			onComplete: function(){
				var container = jQuery( '#fancybox-content' );
					container.find( 'input[name="object_image_id"]' ).val( item.attr( 'data-object-image-id' ) );
					container.find( 'input[name="title"]' ).val( item.attr( 'data-title' ) );
					container.find( 'textarea[name="description"]' ).val( item.attr( 'data-description' ) );
					container.find( 'input[name="link"]' ).val( item.attr( 'data-link' ) );
					container.find( 'input[name="link_target"]' ).val( item.attr( 'data-link-target' ) );
					container.find( 'input[name="featured"][value="' + parseInt( item.attr( 'data-featured' ) ) + '"]' ).attr( 'checked', true );
					container.find( 'input[name="state"][value="' + parseInt( item.attr( 'data-state' ) ) + '"]' ).attr( 'checked', true );
					
					var src = di.url.media + '/' + item.attr( 'data-object-id' ) + '_' + item.attr( 'data-object-image-id' ) + '_' + item.attr( 'data-filename' );
					container.find( '.preview img' ).attr( 'src', src );
			}
		} );
	} );
	
	jQuery( '.big-box .close' ).live( 'click', function(){
		jQuery.fancybox.close();
	} );
	//  /edit item
	
	//  featured state change
	jQuery( '.toggle-featured' ).live( 'click', function(){
		var element = jQuery( this );
		
		var item = element.parents( '.item' );
		
		var featured = parseInt( item.attr( 'data-featured' ) );
		var featured_value = 1;
		
		if( featured == 1 ){
			featured_value = 0;
		}
		
		jQuery.ajax( {
			type: 'GET',
			url: di.url.ajax,
			dataType: 'JSON',
			data: {
				option: 'com_di',
				task: 'ajax.featured',
				object_image_id: element.parents( '.item' ).attr( 'data-object-image-id' ),
				value: featured_value
			},
			error: function(){
				//  alert('Error loading XML document');
			},
			success: function( response ){
				jQuery( '#diImageList .item .toggle-featured' ).each( function(){
					var tf = jQuery( this );
						tf.attr( 'data-featured', '0' );
						tf.find( 'span' ).attr( 'class', 'dicon-star-empty' );
				} );
				
				item.attr( 'data-featured', featured_value );
				
				if( featured_value == 1 ){
					if( parseInt( response.status ) > 0 && di.use_featured > 0 ){
						var src_thumb 	= di.url.media.replace( di.url.root, '' ) + '/' + item.attr( 'data-object-id' ) + '_' + item.attr( 'data-object-image-id' ) + '_thumb_' + item.attr( 'data-filename' );
						var src_regular = di.url.media.replace( di.url.root, '' ) + '/' + item.attr( 'data-object-id' ) + '_' + item.attr( 'data-object-image-id' ) + '_regular_' + item.attr( 'data-filename' );
						
						jQuery( 'input[name="jform[images][image_intro]"]' ).val( src_thumb );
						if( item.attr( 'data-title' ) && item.attr( 'data-title' ) !== '' && item.attr( 'data-title' ) !== 'null' ){
							jQuery( 'input[name="jform[images][image_intro_alt]"]' ).val( item.attr( 'data-title' ) );
						}
						if( item.attr( 'data-description' ) && item.attr( 'data-description' ) !== '' && item.attr( 'data-description' ) !== 'null' ){
							jQuery( 'input[name="jform[images][image_intro_caption]"]' ).val( item.attr( 'data-description' ) );
						}
						
						jQuery( 'input[name="jform[images][image_fulltext]"]' ).val( src_regular );
						if( item.attr( 'data-title' ) && item.attr( 'data-title' ) !== '' && item.attr( 'data-title' ) !== null ){
							jQuery( 'input[name="jform[images][image_fulltext_alt]"]' ).val( item.attr( 'data-title' ) );
						}
						if( item.attr( 'data-description' ) && item.attr( 'data-description' ) !== '' && item.attr( 'data-description' ) !== 'null' ){
							jQuery( 'input[name="jform[images][image_fulltext_caption]"]' ).val( item.attr( 'data-description' ) );
						}
					}
					
					element.find( 'span' ).attr( 'class', 'dicon-star' );
				}
				else{
					if( di.use_featured > 0 ){
						jQuery( 'input[name="jform[images][image_intro]"]' ).val( '' );
						jQuery( 'input[name="jform[images][image_intro_alt]"]' ).val( '' );
						jQuery( 'input[name="jform[images][image_intro_caption]"]' ).val( '' );
						
						jQuery( 'input[name="jform[images][image_fulltext]"]' ).val( '' );
						jQuery( 'input[name="jform[images][image_fulltext_alt]"]' ).val( '' );
						jQuery( 'input[name="jform[images][image_fulltext_caption]"]' ).val( '' );
					}
					
					element.find( 'span' ).attr( 'class', 'dicon-star-empty' );
				}
			}
		} );
	} );
	//  /featured state change
	
	// update item
	jQuery( '.big-box .update' ).live( 'click', function(){
		
		var container = jQuery( '#fancybox-content' );
		
		var object_image_id = container.find( 'input[name="object_image_id"]' ).val();
		var title = container.find( 'input[name="title"]' ).val();
		var description = container.find( 'textarea[name="description"]' ).val();
		var link = container.find( 'input[name="link"]' ).val();
		var link_target = container.find( 'input[name="link_target"]' ).val();
		var featured = parseInt( container.find( 'input[name="featured"]:checked' ).val() );
		var state = parseInt( container.find( 'input[name="state"]:checked' ).val() );
		
		jQuery.ajax( {
			type: 'GET',
			url: di.url.ajax,
			dataType: 'JSON',
			data: {
				option: 'com_di',
				task: 'ajax.update',
				
				object_image_id: object_image_id,
				title: title,
				description: description,
				link: link,
				link_target: link_target,
				featured: featured,
				state: state
			},
			error: function(){
				//  alert('Error loading XML document');
			},
			success: function( response ){
				if( parseInt( response.status ) > 0 ){
					var element = jQuery( '#diImageList .item[data-object-image-id="' + object_image_id + '"]' );
						element.attr( 'data-title', title );
						element.attr( 'data-description', description );
						element.attr( 'data-link', link );
						element.attr( 'data-link-target', link_target );
						element.attr( 'data-featured', featured );
						element.attr( 'data-state', state );
					
					if( featured == 1 ){
						jQuery( '#diImageList .item .toggle-featured' ).each( function(){
							var tf = jQuery( this );
								tf.attr( 'data-featured', '0' );
								tf.find( 'span' ).attr( 'class', 'dicon-star-empty' );
						} );
						
						element.find( '.toggle-featured' ).attr( 'data-featured', '1' );
						element.find( '.toggle-featured span' ).attr( 'class', 'dicon-star' );
						
						if( di.use_featured > 0 ){
							var src_thumb 	= di.url.media.replace( di.url.root, '' ) + '/' + element.attr( 'data-object-id' ) + '_' + element.attr( 'data-object-image-id' ) + '_thumb_' + element.attr( 'data-filename' )
							var src_regular = di.url.media.replace( di.url.root, '' ) + '/' + element.attr( 'data-object-id' ) + '_' + element.attr( 'data-object-image-id' ) + '_regular_' + element.attr( 'data-filename' )
							
							jQuery( 'input[name="jform[images][image_intro]"]' ).val( src_thumb );
							if( element.attr( 'data-title' ) && element.attr( 'data-title' ) !== '' && element.attr( 'data-title' ) !== 'null' ){
								jQuery( 'input[name="jform[images][image_intro_alt]"]' ).val( element.attr( 'data-title' ) );
							}
							if( element.attr( 'data-description' ) && element.attr( 'data-description' ) !== '' && element.attr( 'data-description' ) !== 'null' ){
								jQuery( 'input[name="jform[images][image_intro_caption]"]' ).val( element.attr( 'data-description' ) );
							}
							
							jQuery( 'input[name="jform[images][image_fulltext]"]' ).val( src_regular );
							if( element.attr( 'data-title' ) && element.attr( 'data-title' ) !== '' && element.attr( 'data-title' ) !== null ){
								jQuery( 'input[name="jform[images][image_fulltext_alt]"]' ).val( element.attr( 'data-title' ) );
							}
							if( element.attr( 'data-description' ) && element.attr( 'data-description' ) !== '' && element.attr( 'data-description' ) !== 'null' ){
								jQuery( 'input[name="jform[images][image_fulltext_caption]"]' ).val( element.attr( 'data-description' ) );
							}
						}
					}
				}
				jQuery.fancybox.close();
			}
		} );
	} );
	// /update item
} );