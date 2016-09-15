/*
 *    jquery.wpjb_suggest 1.0 - 2011-10-27
 *    
 *    Based on:
 *    jquery.suggest 1.1b - 2007-08-06
 * Patched by Mark Jaquith with Alexander Dick's "multiple items" patch to allow for auto-suggesting of more than one tag before submitting
 * See: http://www.vulgarisoip.com/2007/06/29/jquerysuggest-an-alternative-jquery-based-autocomplete-library/#comment-7228
 *
 *    Uses code and techniques from following libraries:
 *    1. http://www.dyve.net/jquery/?autocomplete
 *    2. http://dev.jquery.com/browser/trunk/plugins/interface/iautocompleter.js
 *
 *    All the new stuff written by Peter Vulgaris (www.vulgarisoip.com)
 *    Feel free to do whatever you want with this file
 *
 */

(function($) {

    $.wpjb_suggest = function(input, options) {
        var $input, $results, timeout, prevLength, cache, cacheSize;

        $input = $(input).attr("autocomplete", "off");
        $results = $(document.createElement("ul"));
        $results.width($input.width()+parseFloat($input.css("padding-left"))+parseFloat($input.css("padding-right")));

        timeout = false;        // hold timeout ID for suggestion results to appear
        prevLength = 0;            // last recorded length of $input.val()
        cache = [];                // cache MRU list
        cacheSize = 0;            // size of cache in chars (bytes?)

        $results.addClass(options.resultsClass).appendTo('body');


        resetPosition();
        $(window)
            .load(resetPosition)        // just in case user is changing size of page while loading
            .resize(resetPosition);

        $input.blur(function() {
            setTimeout(function() { $results.hide() }, 200);
        });


        // help IE users if possible
        if ( $.browser.msie ) {
            try {
                $results.bgiframe();
            } catch(e) { }
        }

        // I really hate browser detection, but I don't see any other way
        if ($.browser.mozilla)
            $input.keypress(processKey);    // onkeypress repeats arrow keys in Mozilla/Opera
        else
            $input.keydown(processKey);        // onkeydown repeats arrow keys in IE/Safari




        function resetPosition() {
            // requires jquery.dimension plugin
            var offset = $input.offset();
            $results.css({
                top: (offset.top + input.offsetHeight) + 'px',
                left: offset.left + 'px'
            });
        }


        function processKey(e) {

            // handling up/down/escape requires results to be visible
            // handling enter/tab requires that AND a result to be selected
            if ((/27$|38$|40$/.test(e.keyCode) && $results.is(':visible')) ||
                (/^13$|^9$/.test(e.keyCode) && getCurrentResult())) {

                if (e.preventDefault)
                    e.preventDefault();
                if (e.stopPropagation)
                    e.stopPropagation();

                e.cancelBubble = true;
                e.returnValue = false;

                switch(e.keyCode) {

                    case 38: // up
                        prevResult();
                        break;

                    case 40: // down
                        nextResult();
                        break;

                    case 9:  // tab
                    case 13: // return
                        selectCurrentResult();
                        break;

                    case 27: //    escape
                        $results.hide();
                        break;

                }

            } else if ($input.val().length != prevLength) {

                if (timeout)
                    clearTimeout(timeout);
                timeout = setTimeout(suggest, options.delay);
                prevLength = $input.val().length;

            }


        }


        function suggest() {

            var q = $.trim($input.val()), multipleSepPos, items;

            if ( options.multiple ) {
                multipleSepPos = q.lastIndexOf(options.multipleSep);
                if ( multipleSepPos != -1 ) {
                    q = $.trim(q.substr(multipleSepPos + options.multipleSep.length));
                }
            }
            if (q.length >= options.minchars) {

                q = {q: q};
                if(options.onSearch) {
                    q = options.onSearch(q);
                }
                
                var c = "";
                $.each(q, function(i,n) {
                    c += i+":"+n;
                });
                
                cached = checkCache(c);

                if (cached) {

                    displayItems(cached['items']);

                } else {

                    $.get(options.source, q, function(txt) {

                        $results.hide();

                        items = parseTxt(txt, q.q);

                        displayItems(items);
                        addToCache(c, items, txt.length);

                    });

                }

            } else {

                $results.hide();

            }

        }


        function checkCache(q) {
            var i;
            for (i = 0; i < cache.length; i++)
                if (cache[i]['q'] == q) {
                    cache.unshift(cache.splice(i, 1)[0]);
                    return cache[0];
                }

            return false;

        }

        function addToCache(q, items, size) {
            var cached;
            while (cache.length && (cacheSize + size > options.maxCacheSize)) {
                cached = cache.pop();
                cacheSize -= cached['size'];
            }

            cache.push({
                q: q,
                size: size,
                items: items
                });

            cacheSize += size;

        }

        function displayItems(items) {
            var html = '', i;
            if (!items)
                return;

            if (!items.length) {
                $results.hide();
                return;
            }

            resetPosition(); // when the form moves after the page has loaded

            for (i = 0; i < items.length; i++)
                html += '<li>' + items[i] + '</li>';

            $results.html(html).show();

            $results
                .children('li')
                .mouseover(function() {
                    $results.children('li').removeClass(options.selectClass);
                    $(this).addClass(options.selectClass);
                })
                .click(function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    selectCurrentResult();
                });

        }

        function parseTxt(txt, q) {

            var items = [], tokens = txt.split(options.delimiter), i, token;

            // parse returned data for non-empty items
            for (i = 0; i < tokens.length; i++) {
                token = $.trim(tokens[i]);
                if (token) {
                    var $text = null;
                    var $token = $(token);
                    
                    if($token.length == 0) {
                        $text = token
                    } else {
                        $text = $token.text();
                    }
                    
                    token = $text.replace(
                        new RegExp(q, 'ig'),
                        function(q) { return '<span class="' + options.matchClass + '">' + q.replace(q, "<b>"+q+"</b>") + '</span>' }
                    );
                        
                    if($token.length == 0) {
                        items[items.length] = token;
                    } else {
                        $token.html(token);
                        items[items.length] = $token[0].outerHTML;
                    }
                    
                }
            }

            return items;
        }

        function getCurrentResult() {
            var $currentResult;
            if (!$results.is(':visible'))
                return false;

            $currentResult = $results.children('li.' + options.selectClass);

            if (!$currentResult.length)
                $currentResult = false;

            return $currentResult;

        }

        function selectCurrentResult() {

            $currentResult = getCurrentResult();

            if ($currentResult) {
                if ( options.multiple ) {
                    if ( $input.val().indexOf(options.multipleSep) != -1 ) {
                        $currentVal = $input.val().substr( 0, ( $input.val().lastIndexOf(options.multipleSep) + options.multipleSep.length ) );
                    } else {
                        $currentVal = "";
                    }
                    $input.val( $currentVal + $currentResult.text() + options.multipleSep);
                    $input.focus();
                } else {
                    $input.val($currentResult.text());
                }
                $results.hide();

                if (options.onSelect)
                    options.onSelect.apply($input[0]);

            }

        }

        function nextResult() {

            $currentResult = getCurrentResult();

            if ($currentResult)
                $currentResult
                    .removeClass(options.selectClass)
                    .next()
                        .addClass(options.selectClass);
            else
                $results.children('li:first-child').addClass(options.selectClass);

        }

        function prevResult() {
            var $currentResult = getCurrentResult();

            if ($currentResult)
                $currentResult
                    .removeClass(options.selectClass)
                    .prev()
                        .addClass(options.selectClass);
            else
                $results.children('li:last-child').addClass(options.selectClass);

        }
    }

    $.fn.wpjb_suggest = function(source, options) {

        if (!source)
            return;

        options = options || {};
        options.multiple = options.multiple || false;
        options.multipleSep = options.multipleSep || ", ";
        options.source = source;
        options.delay = options.delay || 100;
        options.resultsClass = options.resultsClass || 'wpjb-ac-results';
        options.selectClass = options.selectClass || 'wpjb-ac-over';
        options.matchClass = options.matchClass || 'wpjb-ac-match';
        options.minchars = options.minchars || 2;
        options.delimiter = options.delimiter || '\n';
        options.onSelect = options.onSelect || false;
        options.onSearch = options.onSearch || false;
        options.maxCacheSize = options.maxCacheSize || 65536;

        this.each(function() {
            new $.wpjb_suggest(this, options);
        });

        return this;

    };

})(jQuery);

jQuery(function($) {
    $(".wpjb-location-state").wpjb_suggest(ajaxurl, {
        onSearch: function(q) {
            q.action = "wpjb_suggest_state";
            q.country = 0;
            
            if($(".wpjb-location-country").length == 1) {
                q.country = $(".wpjb-location-country").val();
            }
            
            return q;
        }
    });
    $(".wpjb-location-city").wpjb_suggest(ajaxurl, {
        onSearch: function(q) {
            q.action = "wpjb_suggest_city";
            q.country = 0;
            q.state = "";
            
            if($(".wpjb-location-country").length == 1) {
                q.country = $(".wpjb-location-country").val();
            }
            
            if($(".wpjb-location-state").length == 1) {
                q.state = $(".wpjb-location-state").val();
            }
            
            return q;
        }
    });
});