jQuery(function($) {
    $("input[name=urls_mode]").click(function() {
        var $this = $(this);
        
        if($this.val() == 1) {
            $(".wpjb-form-group-embedded").show();
            $(".wpjb-form-group-shortcoded").hide();
        } else {
            $(".wpjb-form-group-embedded").hide();
            $(".wpjb-form-group-shortcoded").show();
        }
    });
    
    $('input[name=urls_mode]:checked').click();
});