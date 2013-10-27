/*!
 * Auto Complete 5.1
 * April 13, 2010
 * Corey Hart @ http://www.codenothing.com
 * Modified by John Vasko (Search for the string [iii])
 */ 
(function( $, window, undefined ) {
	$.fn.autoComplete = function() {
		var args = Slice.call( arguments ),
			self = this, 
			first = args.shift(),
			isMethod = typeof first === 'string',
			handler, el;
		if ( isMethod ) {
			first = first.replace( rdot, '-' );
		}
		args = ( AutoComplete.arrayMethods[ first ] === TRUE && $.isArray( args[0] ) && $.isArray( args[0][0] ) ) || 
			( args.length === 1 && $.isArray( args[0] ) ) ? 
				args[0] : args;
		handler = isMethod && ( AutoComplete.handlerMethods[ first ] === -1 || args.length < ( AutoComplete.handlerMethods[ first ] || 0 ) ) ? 
			'triggerHandler' : 'trigger';
		return isMethod ?
			self[ handler ]( 'autoComplete.' + first, args ) :
			first && first.preventDefault !== undefined ? self.trigger( first, args ) :
			self.each(function(){
				if ( $( el = this ).data( 'autoComplete' ) !== TRUE ) {
					AutoCompleteFunction( el, first );
				}
			});
	};
	$.fn.bgiframe = $.fn.bgiframe ? $.fn.bgiframe : $.fn.bgIframe ? $.fn.bgIframe : function() {
		return this;
	};
	function setup( $input, inputIndex ) {
		if ( setup.flag !== TRUE ) {
			setup.flag = TRUE;
			rootjQuery.bind( 'click.autoComplete', function( event ) {
				AutoComplete.getFocus( TRUE ).trigger( 'autoComplete.document-click', [ event ] );
			});
		}
		var $form = $input.closest( 'form' ), formList = $form.data( 'ac-inputs' ) || {}, $el;
		formList[ inputIndex ] = TRUE;
		$form.data( 'ac-inputs', formList );
		if ( $form.data( 'autoComplete' ) !== TRUE ) {
			$form.data( 'autoComplete', TRUE ).bind( 'submit.autoComplete', function( event ) {
				return ( $el = AutoComplete.getFocus( TRUE ) ).length ?
					$el.triggerHandler( 'autoComplete.form-submit', [ event, this ] ) :
					TRUE;
			});
		}
	}
	function teardown( $input, inputIndex ) {
		AutoComplete.remove( inputIndex );
		if ( setup.flag === TRUE && AutoComplete.length === 0 ) {
			setup.flag = FALSE;
			rootjQuery.unbind( 'click.autoComplete' );
		}
		var $form = $input.closest( 'form' ), formList = $form.data( 'ac-inputs' ) || {}, i;
		formList[ inputIndex ] = FALSE;
		for ( i in formList ) {
			if ( formList.hasOwnProperty( i ) && formList[ i ] === TRUE ) {
				return;
			}
		}
		$form.unbind( 'submit.autoComplete' );
	}
	function allSupply( event, ui ) {
		if ( ! $.isArray( ui.supply ) ) {
			return [];
		}
		for ( var i = -1, l = ui.supply.length, ret = [], entry; ++i < l; ) {
			entry = ui.supply[ i ];
			entry = entry && entry.value ? entry : { value: entry };
			ret.push( entry );
		}
		return ret;
	}
	var
	TRUE = true,
	FALSE = false,
	Slice = Array.prototype.slice,
	rootjQuery = $( window.document ),
	emptyjQuery = $( ),
	rdot = /\./,
	keypress = window.opera || ( /macintosh/i.test( window.navigator.userAgent ) && $.browser.mozilla ),
	ExpandoFlag = 'autoComplete_' + $.expando,
	KEY = {
		esc: 27,
		backspace: 8,
		tab: 9,
		enter: 13,
		shift: 16,
		space: 32,
		pageup: 33,
		pagedown: 34,
		left: 37,
		up: 38,
		right: 39,
		down: 40
	},
	AutoComplete = $.autoComplete = {
		version: '5.1',
		counter: 0,
		length: 0,
		stack: {},
		jqStack: {},
		order: [],
		hasFocus: FALSE,
		keys: KEY,
		arrayMethods: {
			'button-supply': TRUE,
			'direct-supply': TRUE
		},
		handlerMethods: {
			'option': 2
		},
		focus: undefined,
		blur: undefined,
		getFocus: function( jqStack ) {
			return ! AutoComplete.order[0] ? jqStack ? emptyjQuery : undefined :
				jqStack ? AutoComplete.jqStack[ AutoComplete.order[0] ] :
				AutoComplete.stack[ AutoComplete.order[0] ];
		},
		getPrevious: function( jqStack ) {
			for ( var i = 0, l = AutoComplete.order.length; ++i < l; ) {
				if ( AutoComplete.order[i] ) {
					return jqStack ?
						AutoComplete.jqStack[ AutoComplete.order[i] ] :
						AutoComplete.stack[ AutoComplete.order[i] ];
				}
			}
			return jqStack ? emptyjQuery : undefined;
		},
		remove: function( n ) {
			for ( var i = -1, l = AutoComplete.order.length; ++i < l; ) {
				if ( AutoComplete.order[i] === n ) {
					AutoComplete.order[i] = undefined;
				}
			}
			AutoComplete.length--;
			delete AutoComplete.stack[n];
		},
		getAll: function(){
			for ( var i = -1, l = AutoComplete.counter, stack = []; ++i < l; ) {
				if ( AutoComplete.stack[i] ) {
					stack.push( AutoComplete.stack[i] );
				}
			}
			return $( stack );
		},
		defaults: {
			backwardsCompatible: FALSE,
			ajax: 'ajax.php',
			ajaxCache: $.ajaxSettings.cache,
			dataSupply: [],
			dataFn: undefined,
			formatSupply: undefined,
			list: 'auto-complete-list',
			rollover: 'auto-complete-list-rollover',
			width: undefined, 
			striped: undefined,
			maxHeight: undefined,
			bgiframe: undefined,
			newList: FALSE,
			postVar: 'value',
			postData: {},
			postFormat: undefined,
			minChars: 1,
			maxItems: -1,
			maxRequests: 0,
			maxRequestsDeep: FALSE,
			requestType: 'POST',
			inputControl: undefined,
			autoFill: TRUE, // TODO: eliminate this setting in favor of always autofilling
			// Unless we can include [ctrl, shift, alt, meta] why include just [shift] as nonInput
			nonInput: [ KEY.left, KEY.right, KEY.shift ],
			multiple: FALSE,
			multipleSeparator: ' ',
			onBlur: undefined,
			onFocus: undefined,
			onHide: undefined,
			onLoad: undefined,
			onMaxRequest: undefined,
			onRollover: undefined,
			onSelect: undefined,
			onShow: undefined,
			onListFormat: undefined,
			onSubmit: undefined,
			spinner: undefined,
			preventEnterSubmit: FALSE,
			delay: 0,
			useCache: TRUE,
			cacheLimit: 50
		}
	},
	AutoCompleteFunction = function( self, options ) {
		AutoComplete.length++;
		AutoComplete.counter++;
		var $input = $( self ).attr( 'autocomplete', 'off' ),
			ACData = {},
			LastEvent = {},
			inputval = '',
			currentList = [],
			$elems = { length: 0 },
			$li,
			view, ulHeight, liHeight, liPerView,
			ulOpen = FALSE,
			timeid,
			xhr,
			liFocus = -1, liData,
			separator,
			inputIndex = AutoComplete.counter,
			requests = 0,
			cache = {
				length: 0,
				val: undefined,
				list: {}
			},
			settings = $.extend(
				{ width: $input.outerWidth() },
				AutoComplete.defaults, 
				options||{},
				$.metadata ? $input.metadata() : {}
			),
			$ul = ! settings.newList && rootjQuery.find( 'ul.' + settings.list )[ 0 ] ?
				rootjQuery.find( 'ul.' + settings.list ).eq( 0 ).bgiframe( settings.bgiframe ) :
				$('<ul/>').appendTo('body').addClass( settings.list ).bgiframe( settings.bgiframe ).hide().data( 'ac-selfmade', TRUE );
		$input.data( 'autoComplete', ACData = {
			index: inputIndex,
			hasFocus: FALSE,
			active: TRUE,
			settings: settings,
			initialSettings: $.extend( TRUE, {}, settings )
		});
		if ( $.browser.msie ) {
			$input.bind( 'keypress.autoComplete', function( event ) {
				if ( ! ACData.active ) {
					return TRUE;
				}
				if ( event.keyCode === KEY.enter ) {
					var enter = TRUE;
					if ( $li && $li.hasClass( settings.rollover ) ) {
						enter = settings.preventEnterSubmit && ulOpen ? FALSE : TRUE;
						select( event );
					}
					else if ( ulOpen ) { 
						$ul.hide( event );
					}
					return enter;
				}
			});
		}
		$input.bind( keypress ? 'keypress.autoComplete' : 'keydown.autoComplete' , function( event ) {
			if ( ! ACData.active ) {
				return TRUE;
			}
			var key = ( LastEvent = event ).keyCode, enter = FALSE;
			if ( key === KEY.esc ) { 
				$ul.hide( event ); 
			}
			else if ( key === KEY.tab && ulOpen ) {
				if ($li && $li.hasClass( settings.rollover)) {
					autoFill( liData.value );
				}
				// select( event );
				$input.val( liData.value ); // does not work with opera
				$ul.hide();  // lets us exit the tab trap but messes up autocompletion
				event.KEY.tab;
			}
			else if ( key === KEY.enter && ulOpen ) {
				enter = TRUE;
				if ($li && $li.hasClass( settings.rollover)) {
					autoFill( liData.value );
				}
				$input.val( liData.value );
				$ul.hide( ); // lets us exit the tab trap but messes up autocompletion
			}
			else if ( key === KEY.enter ) {
				enter = TRUE;
				if ( $li && $li.hasClass( settings.rollover ) ) {
					enter = settings.preventEnterSubmit && ulOpen ? FALSE : TRUE;
					autoFill( liData.value );
					select( event );
				}
				else if ( ulOpen ) { 
					$ul.hide( event );
				}
			}
			else if ( key === KEY.up && ulOpen ) {
				if ( liFocus > 0 ) {
					liFocus--;
					up( event );
				} else {
					liFocus = -1;
					$input.val( inputval );
					$ul.hide( event );
				}
			}
			else if ( key === KEY.down && ulOpen ) {
				if ( liFocus < $elems.length - 1 ) {
					liFocus++;
					down( event );
				}
			}
			else if ( key === KEY.pageup && ulOpen ) {
				if ( liFocus > 0 ) {
					liFocus -= liPerView;
					if ( liFocus < 0 ) {
						liFocus = 0;
					}
					up( event );
				}
			}
			else if ( key === KEY.pagedown && ulOpen ) {
				if ( liFocus < $elems.length - 1 ) {
					liFocus += liPerView;
					if ( liFocus > $elems.length - 1 ) {
						liFocus = $elems.length - 1;
					}
					down( event );
				}
			}
			else if ( settings.nonInput && $.inArray( key, settings.nonInput ) > -1 ) {
				$ul.html('').hide( event );
				enter = TRUE;
			}
			else {
				return TRUE;
			}
			LastEvent[ 'keydown_' + ExpandoFlag ] = TRUE;
			return enter;
		})
		.bind({
			'keyup.autoComplete': function( event ) {
				if ( ! ACData.active || LastEvent[ 'keydown_' + ExpandoFlag ] ) {
					return TRUE;
				}
				inputval = $input.val();
				var key = ( LastEvent = event ).keyCode, val = separator ? inputval.split( separator ).pop() : inputval;
				if ( key != KEY.enter ) {
					cache.val = settings.inputControl === undefined ? val : 
						settings.inputControl.apply( self, settings.backwardsCompatible ? 
							[ val, key, $ul, event, settings, cache ] :
							[ event, {
								val: val,
								key: key,
								settings: settings,
								cache: cache,
								ul: $ul
							}]
						);
					if ( cache.val.length >= settings.minChars ) {
						// Prevent request call if field is highlighted
						// IE8
						if (document.selection) {
							if (document.selection.createRange().text.length != cache.val.length) {
								sendRequest( event, settings, cache, ( key === KEY.backspace || key === KEY.space ) );
							}
						}
						// Firefox 3.6.3 OSX, Opera 10 OSX, Chrome OSX
						else if (self.selectionStart > 0) {
							sendRequest( event, settings, cache, ( key === KEY.backspace || key === KEY.space ) );
						}
					}
					else if ( key == KEY.backspace ) {
						$ul.html('').hide( event );
					}
				}
			},
			'blur.autoComplete': function( event ) {
				if ( ! ACData.active || ulOpen ) {
					return TRUE;
				}
				if ( AutoComplete.order[0] !== undefined ) {
					AutoComplete.order.unshift( undefined );
				}
				AutoComplete.hasFocus = FALSE;
				ACData.hasFocus = FALSE;
				liFocus = -1;
				$ul.hide( LastEvent = event );
				if ( AutoComplete.blur ) {
					AutoComplete.blur.call( self, event, { settings: settings, cache: cache, ul: $ul } );
				}
				if ( settings.onBlur ) {
					settings.onBlur.apply( self, settings.backwardsCompatible ?
						[ inputval, $ul, event, settings, cache ] : [ event, {
							val: inputval,
							settings: settings,
							cache: cache,
							ul: $ul
						}]
					);
				}
			},
			'focus.autoComplete': function( event, flag ) {
				if ( ! ACData.active || ( ACData.hasFocus && flag === ExpandoFlag ) || LastEvent[ 'enter_' + ExpandoFlag ] ) {
					return TRUE;
				}
				if ( inputIndex !== $ul.data( 'ac-input-index' ) ) {
					$ul.html('').hide( event );
				}
				if ( AutoComplete.order[0] === undefined ) {
					if ( AutoComplete.order[1] === inputIndex ) {
						AutoComplete.order.shift();
					} else {
						AutoComplete.order[0] = inputIndex;
					}
				}
				else if ( AutoComplete.order[0] != inputIndex && AutoComplete.order[1] != inputIndex ) {
					AutoComplete.order.unshift( inputIndex );
				}
				if ( AutoComplete.defaults.cacheLimit !== -1 && AutoComplete.order.length > AutoComplete.defaults.cacheLimit ) {
					AutoComplete.order.pop();
				}
				AutoComplete.hasFocus = TRUE;
				ACData.hasFocus = TRUE;
				LastEvent = event;
				if ( AutoComplete.focus ) {
					AutoComplete.focus.call( self, event, { settings: settings, cache: cache, ul: $ul } );
				}
				if ( settings.onFocus ) {
					settings.onFocus.apply( self, 
						settings.backwardsCompatible ? [ $ul, event, settings, cache ] : [ event, {
							settings: settings,
							cache: cache,
							ul: $ul
						}]
					);
				}
			},
			'autoComplete.document-click': function( e, event ) {
				if ( ACData.active && ulOpen &&
					( ! LastEvent || event.timeStamp - LastEvent.timeStamp > 200 ) && 
					$( event.target ).closest( 'ul' ).data( 'ac-input-index' ) !== inputIndex ) {
						$ul.hide( LastEvent = event );
						$input.blur();
				}
			},
			'autoComplete.form-submit': function( e, event, form ) {
				if ( ! ACData.active ) {
					return TRUE;
				}
				LastEvent = event;
				return settings.preventEnterSubmit && ( ulOpen || LastEvent[ 'enter_' + ExpandoFlag ] ) ? FALSE : 
					settings.onSubmit === undefined ? TRUE : 
					settings.onSubmit.call( self, event, { form: form, settings: settings, cache: cache, ul: $ul } );
			},
			'autoComplete.ul-mouseenter': function( e, event, li ) {
				if ( $li ) {
					$li.removeClass( settings.rollover );
				}
				$li = $( li ).addClass( settings.rollover );
				liFocus = $elems.index( li );
				liData = currentList[ liFocus ];
				view = $ul.scrollTop() + ulHeight;
				LastEvent = event;
				if ( settings.onRollover ) {
					settings.onRollover.apply( self, settings.backwardsCompatible ? 
						[ liData, $li, $ul, event, settings, cache ] : 
						[ event, {
							data: liData,
							li: $li,
							settings: settings,
							cache: cache,
							ul: $ul
						}]
					);
				}
			},
			'autoComplete.ul-click': function( e, event ) {
				$input.trigger( 'focus', [ ExpandoFlag ] );
				$input.val( inputval === separator ? 
					inputval.substr( 0, inputval.length - inputval.split( separator ).pop().length ) + liData.value + separator :
					liData.value 
				);
				$ul.hide( LastEvent = event );
				autoFill();
				if ( settings.onSelect ) {
					settings.onSelect.apply( self, settings.backwardsCompatible ? 
						[ liData, $li, $ul, event, settings, cache ] :
						[ event, {
							data: liData,
							li: $li,
							settings: settings,
							cache: cache,
							ul: $ul
						}]
					);
				}
			},
			'autoComplete.settings': function( event, newSettings ) {
				if ( ! ACData.active ) {
					return TRUE;
				}
				var ret, $el;
				LastEvent = event;
				if ( $.isFunction( newSettings ) ) {
					ret = newSettings.apply( self, settings.backwardsCompatible ? 
						[ settings, cache, $ul, event ] : [ event, { settings: settings, cache: cache, ul: $ul } ]
					);
					if ( $.isArray( ret ) && ret[0] !== undefined ) {
						$.extend( TRUE, settings, ret[0] || settings );
						$.extend( TRUE, cache, ret[1] || cache );
					}
				} else {
					$.extend( TRUE, settings, newSettings || {} );
				}
				$ul = ! settings.newList && $ul.hasClass( settings.list ) ? $ul : 
					! settings.newList && ( $el = rootjQuery.find( 'ul.' + settings.list ).eq( 0 ) ).length ? 
						$el.bgiframe( settings.bgiframe ) :
						$('<ul/>').appendTo('body').addClass( settings.list )
							.bgiframe( settings.bgiframe ).hide().data( 'ac-selfmade', TRUE );
				newUl();
				settings.requestType = settings.requestType.toUpperCase();
				separator = settings.multiple ? settings.multipleSeparator : undefined;
				ACData.settings = settings;
			},
			'autoComplete.flush': function( event, cacheOnly ) {
				if ( ! ACData.active ) {
					return TRUE;
				}
				
				if ( ! cacheOnly ) {
					requests = 0;
				}
				cache = { length: 0, val: undefined, list: {} };
				LastEvent = event;
			},
			'autoComplete.button-ajax': function( event, postData, cacheName ) {
				if ( ! ACData.active ) {
					return TRUE;
				}
				if ( typeof postData === 'string' ) {
					cacheName = postData;
					postData = {};
				}
				LastEvent = event;
				$input.trigger( 'focus', [ ExpandoFlag ] );
				cache.val = cacheName || 'button-ajax_' + ExpandoFlag;
				return sendRequest(
					event, 
					$.extend( TRUE, {}, settings, { maxItems: -1, postData: postData || {} } ),
					cache
				);
			},
			'autoComplete.button-supply': function( event, data, cacheName ) {
				if ( ! ACData.active ) {
					return TRUE;
				}
				if ( typeof data === 'string' ) {
					cacheName = data;
					data = undefined;
				}
				LastEvent = event;
				$input.trigger( 'focus', [ ExpandoFlag ] );
				cache.val = cacheName || 'button-supply_' + ExpandoFlag;
				data = $.isArray( data ) ? data : settings.dataSupply;
				return sendRequest(
					event,
					$.extend( TRUE, {}, settings, { maxItems: -1, dataSupply: data, formatSupply: allSupply } ),
					cache
				);
			},
			'autoComplete.direct-supply': function( event, data, cacheName ) {
				if ( ! ACData.active ) {
					return TRUE;
				}
				if ( typeof data === 'string' ) {
					cacheName = data;
					data = undefined;
				}
				LastEvent = event;
				$input.trigger( 'focus', [ ExpandoFlag ] );
				cache.val = cacheName || 'direct-supply_' + ExpandoFlag;
				data = $.isArray( data ) && data.length ? data : settings.dataSupply;
				return loadResults(
					event,
					data,
					$.extend( TRUE, {}, settings, { maxItems: -1, dataSupply: data, formatSupply: allSupply } ),
					cache
				);
			},
			'autoComplete.search': function( event, value ) {
				if ( ! ACData.active ) {
					return TRUE;
				}
				cache.val = value || '';
				return sendRequest( LastEvent = event, settings, cache );
			},
			'autoComplete.option': function( event, name, value ) {
				if ( ! ACData.active ) {
					return TRUE;
				}
				LastEvent = event;
				switch ( Slice.call( arguments ).length ) {
					case 3: 
						settings[ name ] = value;
						return value;
					case 2:
						return name === 'ul' ? $ul :
							name === 'cache' ? cache :
							name === 'xhr' ? xhr :
							name === 'input' ? $input :
							settings[ name ] || undefined;
					default:
						return settings;
				}
			},
			'autoComplete.enable': function( event ) {
				ACData.active = TRUE;
				LastEvent = event;
			},
			'autoComplete.disable': function( event ) {
				ACData.active = FALSE;
				$ul.html('').hide( LastEvent = event );
			},
			'autoComplete.destroy': function( event ) {
				var list = $ul.html('').hide( LastEvent = event ).data( 'ac-inputs' ) || {}, i;
				$input.removeData( 'autoComplete' ).unbind( '.autoComplete autoComplete' );
				teardown( $input, inputIndex );
				list[ inputIndex ] = undefined;
				for ( i in list ) {
					if ( list.hasOwnProperty( i ) && list[ i ] === TRUE ) {
						return LastEvent;
					}
				}
				if ( $ul.data( 'ac-selfmade' ) === TRUE ) {
					$ul.remove();
				}
				else {
					$ul.removeData( 'autoComplete' ).removeData( 'ac-input-index' ).removeData( 'ac-inputs' );
				}
			}
		});
		function sendRequest( event, settings, cache, backSpace, timeout ) {
			if ( settings.maxRequestsDeep === true && requests >= settings.maxRequests ) {
				return FALSE;
			}
			if ( settings.spinner ) {
				settings.spinner.call( self, event, { active: TRUE, settings: settings, cache: cache, ul: $ul } );
			}
			if ( timeid ) {
				timeid = clearTimeout( timeid );
			}
			if ( settings.delay > 0 && timeout === undefined ) {
				timeid = window.setTimeout(function(){
					sendRequest( event, settings, cache, backSpace, TRUE );
				}, settings.delay );
				return timeid;
			}
			if ( xhr ) {
				xhr.abort();
			}
			if ( settings.useCache && $.isArray( cache.list[ cache.val ] ) ) {
				return loadResults( event, cache.list[ cache.val ], settings, cache, backSpace );
			}
			if ( settings.dataSupply.length ) {
				return userSuppliedData( event, settings, cache, backSpace );
			}
			if ( settings.maxRequests && ++requests >= settings.maxRequests ) {
				$ul.html('').hide( event );
				if ( settings.spinner ) {
					settings.spinner.call( self, event, { active: FALSE, settings: settings, cache: cache, ul: $ul } );
				}
				if ( settings.onMaxRequest && requests === settings.maxRequests ) {
					return settings.onMaxRequest.apply( self, settings.backwardsCompatible ? 
						[ cache.val, $ul, event, inputval, settings, cache ] : 
						[ event, {
							search: cache.val,
							val: inputval,
							settings: settings,
							cache: cache,
							ul: $ul
						}]
					);
				}
				return FALSE;
			}
			settings.postData[ settings.postVar ] = cache.val;
			xhr = $.ajax({
				type: settings.requestType,
				url: settings.ajax,
				cache: settings.ajaxCache,
				dataType: 'json',
				data: settings.postFormat ?
					settings.postFormat.call( self, event, {
						data: settings.postData,
						search: cache.val,
						val: inputval,
						settings: settings,
						cache: cache,
						ul: $ul
					}) :
					settings.postData,
				success: function( list ) {
					loadResults( event, list, settings, cache, backSpace );
				},
				error: function() {
					$ul.html('').hide( event );
					if ( settings.spinner ) {
						settings.spinner.call( self, event, { active: FALSE, settings: settings, cache: cache, ul: $ul } );
					}
				}
			});
			return xhr;
		}
		function userSuppliedData( event, settings, cache, backSpace ) {
			var list = [], args = [],
				fn = $.isFunction( settings.dataFn ),
				regex = fn ? undefined : new RegExp( '^'+cache.val, 'i' ),
				items = 0, entry, i = -1, l = settings.dataSupply.length;
			if ( settings.formatSupply ) {
				list = settings.formatSupply.call( self, event, {
					search: cache.val,
					supply: settings.dataSupply,
					settings: settings,
					cache: cache,
					ul: $ul
				});
			} else {
				for ( ; ++i < l ; ) {
					entry = settings.dataSupply[i];
					entry = entry && typeof entry.value === 'string' ? entry : { value: entry };
					args = settings.backwardsCompatible ? 
						[ cache.val, entry.value, list, i, settings.dataSupply, $ul, event, settings, cache ] :
						[ event, {
							search: cache.val,
							entry: entry.value,
							list: list,
							i: i,
							supply: settings.dataSupply,
							settings: settings,
							cache: cache,
							ul: $ul
						}];
					if ( ( fn && settings.dataFn.apply( self, args ) ) || ( ! fn && entry.value.match( regex ) ) ) {
						if ( settings.maxItems > -1 && ++items > settings.maxItems ) {
							break;
						}
						list.push( entry );
					}
				}
			}
			return loadResults( event, list, settings, cache, backSpace );
		}
		function select( event ) {
			if ( ulOpen ) {
				if ( settings.onSelect ) {
					settings.onSelect.apply( self, settings.backwardsCompatible ? 
						[ liData, $li, $ul, event, settings, cache ] :
						[ event, {
							data: liData,
							li: $li,
							settings: settings,
							cache: cache,
							ul: $ul
						}]
					);
				}
				autoFill();
				inputval = $input.val();
				if ( LastEvent.type === 'keydown' ) {
					LastEvent[ 'enter_' + ExpandoFlag ] = TRUE;
				}
				$ul.hide( event );
			}
			$li = undefined;
		}
		function up( event ) {
			if ( $li ) {
				$li.removeClass( settings.rollover );
			}
			$ul.show( event );
			$li = $elems.eq( liFocus ).addClass( settings.rollover );
			liData = currentList[ liFocus ];
			if ( ! $li.length || ! liData ) {
				return FALSE;
			}
			//autoFill( liData.value );
			if ( settings.onRollover ) {
				settings.onRollover.apply( self, settings.backwardsCompatible ? 
					[ liData, $li, $ul, event, settings, cache ] :
					[ event, {
						data: liData,
						li: $li,
						settings: settings,
						cache: cache,
						ul: $ul
					}]
				);
			}
			var scroll = liFocus * liHeight;
			if ( scroll < view - ulHeight ) {
				view = scroll + ulHeight;
				$ul.scrollTop( scroll );
			}
		}
		function down( event ) {
			if ( $li ) {
				$li.removeClass( settings.rollover );
			}
			$ul.show( event );
			$li = $elems.eq( liFocus ).addClass( settings.rollover );
			liData = currentList[ liFocus ];
			if ( ! $li.length || ! liData ) {
				return FALSE;
			}
			// autoFill( liData.value );
			var scroll = ( liFocus + 1 ) * liHeight;
			if ( scroll > view ) {
				$ul.scrollTop( ( view = scroll ) - ulHeight );
			}
			if ( settings.onRollover ) {
				settings.onRollover.apply( self, settings.backwardsCompatible ? 
					[ liData, $li, $ul, event, settings, cache ] : [ event, {
						data: liData,
						li: $li,
						settings: settings,
						cache: cache,
						ul: $ul
					}]
				);
			}
		}
		function newUl() {
			var hide = $ul.hide, show = $ul.show, list = $ul.data( 'ac-inputs' ) || {};
			if ( ! $ul[ExpandoFlag] ) {
				$ul.hide = function( event, speed, callback ) {
					if ( settings.onHide && ulOpen ) {
						settings.onHide.call( self, event, { ul: $ul, settings: settings, cache: cache } );
					}
					ulOpen = FALSE;
					return hide.call( $ul, speed, callback );
				};
				$ul.show = function( event, speed, callback ) {
					if ( settings.onShow && ! ulOpen ) {
						settings.onShow.call( self, event, { ul: $ul, settings: settings, cache: cache } );
					}
					ulOpen = TRUE;
					return show.call( $ul, speed, callback );
				};
				$ul[ExpandoFlag] = TRUE;
			}
			if ( $ul.data( 'autoComplete' ) !== TRUE ) {
				$ul.data( 'autoComplete', TRUE )
				.delegate( 'li', 'mouseenter.autoComplete', function( event ) {
					AutoComplete.getFocus( TRUE ).trigger( 'autoComplete.ul-mouseenter', [ event, this ] );
				})
				.bind( 'click.autoComplete', function( event ) {
					AutoComplete.getFocus( TRUE ).trigger( 'autoComplete.ul-click', [ event ] );
					return FALSE;
				});
			}
			list[ inputIndex ] = TRUE;
			$ul.data( 'ac-inputs', list );
		}
		function autoFill( val ) {
			return true; // setting autoFill: FALSE malfunctions but this works!
			var start, end, range;
			if ( val === undefined || val === '' ) {
				start = end = $input.val().length;
			} else {
				if ( separator ) {
					val = inputval.substr( 0, inputval.length - inputval.split( separator ).pop().length ) + val + separator;
				}
				start = inputval.length;
				end = val.length;
				//$input.val( val );
			}
			if ( ! settings.autoFill || start > end ) {
				return FALSE;
			}
			else if ( self.createTextRange ) {
				range = self.createTextRange();
				if ( val === undefined ) {
					range.move( 'character', start );
					range.select();
				} else {
					range.collapse( TRUE );
					range.moveStart( 'character', start );
					range.moveEnd( 'character', end );
					range.select();
				}
			}
			else if ( self.setSelectionRange ) {
				self.setSelectionRange( start, end );
			}
			else if ( self.selectionStart ) {
				self.selectionStart = start;
				self.selectionEnd = end;
			}
		}
		function loadResults( event, list, settings, cache, backSpace ) {
			currentList = settings.onLoad ?
				settings.onLoad.call( self, event, { list: list, settings: settings, cache: cache, ul: $ul } ) : list;
			if ( settings.spinner ) {
				settings.spinner.call( self, event, { active: FALSE, settings: settings, cache: cache, ul: $ul } );
			}
			if ( settings.useCache && ! $.isArray( cache.list[ cache.val ] ) ) {
				cache.length++;
				cache.list[ cache.val ] = list;
				if ( settings.cacheLimit !== -1 && cache.length > settings.cacheLimit ) {
					cache.list = {};
					cache.length = 0;
				}
			}
			if ( ! currentList || currentList.length < 1 ) {
				return $ul.html('').hide( event );
			}
			liFocus = -1;
			var offset = $input.offset(), 
				container = [], 
				items = 0, i = -1, striped = FALSE, length = currentList.length; 
			if ( settings.onListFormat ) {
				settings.onListFormat.call( self, event, { list: currentList, settings: settings, cache: cache, ul: $ul } );
			}
			else {
				for ( ; ++i < length; ) {
					if ( currentList[i].value ) {
						if ( settings.maxItems > -1 && ++items > settings.maxItems ) {
							break;
						}
						container.push(
							settings.striped && striped ? '<li class="' + settings.striped + '">' : '<li>',
							currentList[i].display || currentList[i].value,
							'</li>'
						);
						striped = ! striped;
					}
				}
				$ul.html( container.join('') );
			}
			$elems = $ul.children( 'li' );
			if ( settings.autoFill && ! backSpace ) {
				liFocus = 0;
				liData = currentList[ 0 ];
				autoFill( liData.value );
				$li = $elems.eq( 0 ).addClass( settings.rollover );
			}
			$ul.data( 'ac-input-index', inputIndex ).scrollTop( 0 ).css({
				top: offset.top + $input.outerHeight(),
				left: offset.left,
				width: settings.width
			})
			.show( event );
			liHeight = $elems.eq( 0 ).outerHeight();
			if ( settings.maxHeight ) {
				$ul.css({
					height: liHeight * $elems.length > settings.maxHeight ? settings.maxHeight : 'auto', 
					overflow: 'auto'
				});
			}
			ulHeight = $ul.outerHeight();
			view = ulHeight;
			liPerView = liHeight === 0 ? 0 : Math.floor( view / liHeight );
			LastEvent.timeStamp = ( new Date() ).getTime();
		}
		newUl();
		settings.requestType = settings.requestType.toUpperCase();
		separator = settings.multiple ? settings.multipleSeparator : undefined;
		AutoComplete.stack[ inputIndex ] = self;
		AutoComplete.jqStack[ inputIndex ] = $input;
		setup( $input, inputIndex );
	};
})( jQuery, window || this );
