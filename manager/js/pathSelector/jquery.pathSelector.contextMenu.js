// This is a adapted version of plugin by Cory S.N. LaViska for using in Path Selector.
// 
// Changes by Victor Hugo Herrera Maldonado. 
//
// *****************************
// ****** Original License *****
// jQuery Context Menu Plugin
//
// Version 1.00
//
// Cory S.N. LaViska
// A Beautiful Site (http://abeautifulsite.net/)
//
// Visit http://abeautifulsite.net/notebook/80 for usage and more information
//
// Terms of Use
//
// This software is licensed under a Creative Commons License and is copyrighted
// (C)2008 by Cory S.N. LaViska.
//
// For details, visit http://creativecommons.org/licenses/by/3.0/us/
//
if(jQuery)( function() {
    $.extend($.fn, {
		
        contextMenu: function(o, callback) {
            // Defaults
            if( o.menu == undefined ) return false;
            if( o.inSpeed == undefined ) o.inSpeed = 50;
            if( o.outSpeed == undefined ) o.outSpeed = 75;
            // 0 needs to be -1 for expected results (no fade)
            if( o.inSpeed == 0 ) o.inSpeed = -1;
            if( o.outSpeed == 0 ) o.outSpeed = -1;
            // Loop each context menu
            $(this).each( function() {
                var el = $(this);

                var menu;
                var isString=(typeof o.menu == "string" || o.menu instanceof String);
                if(isString){
                    menu = $('#' + o.menu);
                }else{
                    menu = $(o.menu);
                }
                
                var offset = $(el).offset();
                // Add contextMenu class
                menu.addClass('contextMenu');
                menu.get(0).menuShown=o.menuShown;
                $(this).click( function(e) {
                        var srcElement = $(this);
                        $(this).unbind('mouseup');
                            // Hide context menus that may be showing
                            $(".contextMenu").hide("normal", o.afterHiding);
                            // Clear content
                            $(".contextMenu").html('');
                            // Get this context menu
							
                            // Detect mouse position
                            var d = {}, x, y;
                            if( self.innerHeight ) {
                                d.pageYOffset = self.pageYOffset;
                                d.pageXOffset = self.pageXOffset;
                                d.innerHeight = self.innerHeight;
                                d.innerWidth = self.innerWidth;
                            } else if( document.documentElement &&
                                document.documentElement.clientHeight ) {
                                d.pageYOffset = document.documentElement.scrollTop;
                                d.pageXOffset = document.documentElement.scrollLeft;
                                d.innerHeight = document.documentElement.clientHeight;
                                d.innerWidth = document.documentElement.clientWidth;
                            } else if( document.body ) {
                                d.pageYOffset = document.body.scrollTop;
                                d.pageXOffset = document.body.scrollLeft;
                                d.innerHeight = document.body.clientHeight;
                                d.innerWidth = document.body.clientWidth;
                            }
                            (e.pageX) ? x = e.pageX : x = e.clientX + d.scrollLeft;
                            (e.pageY) ? y = e.pageY : x = e.clientY + d.scrollTop;
							
                            // Show the menu
                            $(document).unbind('click');
                            

                            menu.get(0).proccessHTML=function(){
                            var menu=$(this);
                            menu.find('a').mouseover( function() {
                                menu.find('li.hover').removeClass('hover');
                                $(this).parent().addClass('hover');
                            }).mouseout( function() {
                                menu.find('li.hover').removeClass('hover');
                            });
							
                            // Keyboard
                            $(document).keypress( function(e) {
                                switch( e.keyCode ) {
                                    case 38: // up
                                        if( menu.find('li.hover').size() == 0 ) {
                                            menu.find('li:last').addClass('hover');
                                        } else {
                                            menu.find('li.hover').removeClass('hover').prevAll('li').eq(0).addClass('hover');
                                            if( menu.find('li.hover').size() == 0 ) menu.find('li:last').addClass('hover');
                                        }
                                        break;
                                    case 40: // down
                                        if( menu.find('li.hover').size() == 0 ) {
                                            menu.find('li:first').addClass('hover');
                                        } else {
                                            menu.find('li.hover').removeClass('hover').nextAll('li').eq(0).addClass('hover');
                                            if( menu.find('li.hover').size() == 0 ) menu.find('li:first').addClass('hover');
                                        }
                                        break;
                                    case 13: // enter
                                        menu.find('li.hover A').trigger('click');
                                        break;
                                    case 27: // esc
                                        $(document).trigger('click');
                                        break
                                }
                            });
							
                            // When items are selected
                            menu.find('a').unbind('click');
                            menu.find('li a').click( function() {
                                $(document).unbind('click').unbind('keypress');
                                $(".contextMenu").hide("normal", o.afterHiding);
                                // Callback
                                if( callback ) callback( {value:$(this).attr('href').substr($(this).attr('href').indexOf("#")+1), label:$(this).html()}, $(srcElement), {
                                    x: x - offset.left,
                                    y: y - offset.top,
                                    docX: x,
                                    docY: y
                                } );
                                return false;
                            });
							
                            // Hide bindings
                            setTimeout( function() { // Delay for Mozilla
                                $(document).click( function() {
                                    $(document).unbind('click').unbind('keypress');
                                    menu.fadeOut(o.outSpeed, o.afterHiding);
                                    return false;
                                });
                            }, 0);
                        };
                        menu.css({
                            top: srcElement.parent().offset().top + srcElement.parent().outerHeight(),
                            left: srcElement.offset().left
                        }).fadeIn(o.inSpeed);
                        if(menu.get(0).menuShown){
                            menu.get(0).menuShown(srcElement);
                        }
                });
				
                // Disable text selection
                
                menu.each(function() {
                	$(this).css({
                        'MozUserSelect' : 'none'
                    });
                    $(this).bind('mousedown.disableTextSelect', function() {
                        return false;
                    });
                });
                /*
                if( $.browser.mozilla ) {
                    menu.each( function() {
                        $(this).css({
                            'MozUserSelect' : 'none'
                        });
                    });
                } else if( $.browser.msie ) {
                    menu.each( function() {
                        $(this).bind('selectstart.disableTextSelect', function() {
                            return false;
                        });
                    });
                } else {
                    menu.each(function() {
                        $(this).bind('mousedown.disableTextSelect', function() {
                            return false;
                        });
                    });
                }
                */
                // Disable browser context menu (requires both selectors to work in IE/Safari + FF/Chrome)
                $(el).add('UL.contextMenu').bind('contextmenu', function() {
                    return false;
                });
				
            });
            return $(this);
        },
		

		
        // Destroy context menu(s)
        destroyContextMenu: function() {
            // Destroy specified context menus
            $(this).each( function() {
                // Disable action
                $(this).unbind('mousedown').unbind('mouseup');
            });
            return( $(this) );
        }
		
    });
})(jQuery);