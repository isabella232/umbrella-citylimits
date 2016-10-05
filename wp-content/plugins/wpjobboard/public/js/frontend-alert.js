jQuery(function($) {
    $(".wpjb-widget-alert-save").click(function(e) {
        
        e.preventDefault();
        
        var frequency = 1;
        var has_meta = false;
        var meta = {};
        var criteria = {
            keyword: $(".wpjb-widget-alert-keyword").val()
        };
        
        // Collect data from default fields
        $(".wpjb-widget-alert-param").each(function(index, item) {
            var $this = $(item);
            criteria[$this.attr("name")] = $this.val();
        });
        
        // Collect data from meta fields
        $(".wpjb-widget-alert-meta").each(function(index, item) {
            var $this = $(item);
            meta[$this.attr("name")] = $this.val();
            has_meta = true;
        });
        
        // Collect data from multiselect input fields
        $(".wpjb-widget-alert .daq-multiselect-holder").each(function(index, item) {
            var $this = $(item);
            var $input = $this.find(".daq-multiselect-input");
            
            if($input.attr("id") == "type" || $input.attr("id") == "category") {
                criteria[$input.attr("id")] = [];
                $this.find(".daq-multiselect-options input[type=checkbox]:checked").each(function() {
                    criteria[$input.attr("id")].push($(this).val());
                });
            } else {
                meta[$input.attr("id")] = [];
                $this.find(".daq-multiselect-options input[type=checkbox]:checked").each(function() {
                    meta[$input.attr("id")].push($(this).val());
                });
                has_meta = true;
            }
        });
        
        if($(".wpjb-widget-alert-frequency").length > 0) {
            frequency = $(".wpjb-widget-alert-frequency").val();
        }
        
        if(has_meta) {
            criteria.meta = meta;
        }
        
        
        var data = {
            action: "wpjb_main_subscribe",
            email: $(".wpjb-widget-alert-email").val(),
            frequency: frequency,
            criteria: criteria
        };
        
        $(".wpjb-widget-alert-result").hide();
        
	$.post(ajaxurl, data, function(response) {
            

            var span = $(".wpjb-widget-alert-result");
            
            span.text(response.msg);
            span.removeClass("wpjb-flash-info");
            span.removeClass("wpjb-flash-error");
            
            if(response.result == "1") {
                span.addClass("wpjb-flash-info");
            } else {
                span.addClass("wpjb-flash-error"); 
                
            }
            
            span.fadeIn("fast");
            
	}, "json");
        
        return false;
    }); 
});