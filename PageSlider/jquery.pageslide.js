/*
 * jQuery pageSlide
 * Version 2.0
 * http://srobbin.com/jquery-pageslide/
 *
 * jQuery Javascript plugin which slides a webpage over to reveal an additional interaction pane.
 *
 * Copyright (c) 2011 Scott Robbin (srobbin.com)
 * Dual licensed under the MIT and GPL licenses.
 *
 * Edited 2013-09-24 12:40:00 PM WAT by Obinwanne Hill (https://about.me/obinwanne.hill)
 */

;(function($){
    // Convenience vars for accessing elements
    var $body,
        $pageslide;

    $(function(){
        $body = $('body');
        $pageslide = $('#pageslide');

        // If the pageslide element doesn't exist, create it
        if( $pageslide.length == 0 ) {
            $pageslide = $('<div />').attr( 'id', 'pageslide' )
                .css( 'display', 'none' )
                .appendTo( $body );
        }

        /* Events */

        // Don't let clicks to the pageslide close the window
        $pageslide.click(function(e) {
            e.stopPropagation();
        });

        // Close the pageslide if the document is clicked or the users presses the ESC key, unless the pageslide is modal
        $(document).bind('click keyup', function(e) {
            // If this is a keyup event, let's see if it's an ESC key
            if( e.type == "keyup" && e.keyCode != 27) return;

            // Make sure it's visible, and we're not modal
            if( $pageslide.is( ':visible' ) && !$pageslide.data( 'modal' ) ) {
                $.pageslide.close();
            }
        });
    });

    var _sliding = false,   // Mutex to assist closing only once
        _lastCaller;        // Used to keep track of last element to trigger pageslide

    /*
     * Special Functions
     */
    Function.prototype.getFuncBody = function()
    {
        // Get content between first { and last }
        var m = this.toString().match(/\{([\s\S]*)\}/m)[1];
        // Strip comments
        return m.replace(/^\s*\/\/.*$/mg,'');
    };

    /*
     * Private methods 
     */
    function _load( url, useIframe ) {
        // Are we loading an element from the page or a URL?
        if ( url.indexOf("#") === 0 ) {
            // Load a page element                
            $(url).clone(true).appendTo( $pageslide.empty() ).show();
        } else {
            // Load a URL. Into an iframe?
            if( useIframe ) {
                var iframe = $("<iframe />").attr({
                    src: url,
                    frameborder: 0,
                    hspace: 0
                })
                    .css({
                        width: "100%",
                        height: "100%"
                    });

                $pageslide.html( iframe );
            } else {
                $pageslide.load( url );
            }

            $pageslide.data( 'localEl', false );

        }
    }

    // Function that controls opening of the pageslide
    function _start() {
        var myArgs = Array.prototype.slice.call(arguments);
        var settings = myArgs[0],
            direction = settings.direction,
            speed = settings.speed;

        var slideWidth = $pageslide.outerWidth( true ),
            bodyAnimateIn = {},
            slideAnimateIn = {};

        // If the slide is open or opening, just ignore the call
        if( $pageslide.is(':visible') || _sliding ) return;
        _sliding = true;

        switch( direction ) {
            case 'left':

                $pageslide.css({ left: 'auto', right: '-' + slideWidth + 'px' });
                bodyAnimateIn['margin-left'] = '-=' + slideWidth;
                slideAnimateIn['right'] = '+=' + slideWidth;
                break;
            default:
                $pageslide.css({ left: '-' + slideWidth + 'px', right: 'auto' });
                bodyAnimateIn['margin-left'] = '+=' + slideWidth;
                slideAnimateIn['left'] = '+=' + slideWidth;
                break;
        }

        // Animate the slide, and attach this slide's settings to the element
        _callback(settings, 'onPreOpen');
        $body.animate(bodyAnimateIn, speed);
        $pageslide.show()
            .animate(slideAnimateIn, speed, function() {
                _sliding = false;
                _callback(settings, 'onOpen');
            });
    }

    //Additional Callback features function
    function _callback(options, func_name)
    {
        var callback_res,
            callback_func_body_count;

        switch(true)
        {
            case (func_name == "onOpen"):
                callback_res = options.onOpen;
                callback_func_body_count = options.onOpen.getFuncBody().length;
                break;

            case (func_name == "onPreOpen"):
                callback_res = options.onPreOpen;
                callback_func_body_count = options.onPreOpen.getFuncBody().length;
                break;

            case (func_name == "onPreClose"):
                callback_res = options.onPreClose;
                callback_func_body_count = options.onPreClose.getFuncBody().length;
                break;

            case (func_name == "onClose"):
                callback_res = options.onClose;
                callback_func_body_count = options.onClose.getFuncBody().length;
                break;
        }

        switch(true)
        {
            case (($.isFunction(callback_res) === true) && (callback_func_body_count > 0)):
                //Execute Callback
                callback_res();
                break;
        }
    }

    /*
     * Declaration 
     */
    $.fn.pageslide = function(options) {
        var $elements = this;

        // On click
        $elements.click( function(e) {
            var $self = $(this),
                settings = $.extend({ href: $self.attr('href') }, options);

            // Prevent the default behavior and stop propagation
            e.preventDefault();
            e.stopPropagation();

            if ( $pageslide.is(':visible') && $self[0] == _lastCaller ) {
                // If we clicked the same element twice, toggle closed
                $.pageslide.close();
            } else {
                // Open
                $.pageslide( settings );

                // Record the last element to trigger pageslide
                _lastCaller = $self[0];
            }
        });
    };

    /*
     * Default settings 
     */
    $.fn.pageslide.defaults = {
        speed:      200,        // Accepts standard jQuery effects speeds (i.e. fast, normal or milliseconds)
        direction:  'right',    // Accepts 'left' or 'right'
        modal:      false,      // If set to true, you must explicitly close pageslide using $.pageslide.close();
        iframe:     true,       // By default, linked pages are loaded into an iframe. Set this to false if you don't want an iframe.
        href:       null,        // Override the source of the content. Optional in most cases, but required when opening pageslide programmatically.
        onPreOpen: function(){},    //Execute function before Pageslide opens
        onOpen: function(){},       //Execute function on Pageslide open
        onPreClose: function(){},   //Execute function just before Pageslide closes
        onClose: function(){}       //Execute function on Pageslide close
    };

    /*
     * Public methods 
     */

    // Open the pageslide
    $.pageslide = function( options ) {
        // Extend the settings with those the user has provided
        var settings = $.extend({}, $.fn.pageslide.defaults, options);

        // Are we trying to open in different direction?
        if( $pageslide.is(':visible') && $pageslide.data( 'direction' ) != settings.direction) {
            $.pageslide.close(function(){
                _load( settings.href, settings.iframe );
                _start( settings);
            });
        } else {
            _load( settings.href, settings.iframe );
            if( $pageslide.is(':hidden') ) {
                _start( settings);
            }
        }

        $pageslide.data( settings );
    };

    // Close the pageslide
    $.pageslide.close = function( callback ) {
        var $pageslide = $('#pageslide'),
            settings = $pageslide.data(),
            slideWidth = $pageslide.outerWidth( true ),
            speed = $pageslide.data( 'speed' ),
            bodyAnimateIn = {},
            slideAnimateIn = {};

        // If the slide isn't open, just ignore the call
        if( $pageslide.is(':hidden') || _sliding ) return;
        _sliding = true;

        switch( $pageslide.data( 'direction' ) ) {
            case 'left':
                bodyAnimateIn['margin-left'] = '+=' + slideWidth;
                slideAnimateIn['right'] = '-=' + slideWidth;
                break;
            default:
                bodyAnimateIn['margin-left'] = '-=' + slideWidth;
                slideAnimateIn['left'] = '-=' + slideWidth;
                break;
        }

        $pageslide.animate(slideAnimateIn, speed);
        $body.animate(bodyAnimateIn, speed, function() {
            _callback(settings, 'onPreClose');
            $pageslide.hide();
            _sliding = false;
            _callback(settings, 'onClose');
            if( typeof callback != 'undefined' ) callback();
        });
    };

})(jQuery);