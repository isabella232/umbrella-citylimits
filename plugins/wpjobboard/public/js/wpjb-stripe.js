jQuery(function($) {
    $('#payment-form').submit(function(event) {
        var $form = $(this);

        $form.find('button').prop('disabled', true);
        Stripe.card.createToken($form, wpjbStripeResponse);
        
        return false;
    });
  
    var wpjbStripeResponse = function(status, response) {
        var $form = $('#payment-form');

        if (response.error) {
            
            $form.find('.payment-errors').addClass("wpjb-flash-error").text(response.error.message);
            $form.find('button').prop('disabled', false);
        } else {
            
            var token = response.id;
            var data = {
                echo: "1",
                action: "wpjb_payment_accept",
                engine: "Stripe",
                id: WPJB_PAYMENT_ID,
                token: token
            };
        
            $.ajax({
                url: ajaxurl,
                cache: false,
                type: "POST",
                data: data,
                dataType: "json",
                success: function(response) {
                    if(response.external_id) {
                        $form.find('.payment-errors').removeClass("wpjb-flash-error").addClass("wpjb-flash-info").text(wpjb_stripe.payment_accepted);
                        $form.find('div.form-row').hide();
                        $form.find('button').hide();
                    } else {
                        $form.find(".payment-errors").removeClass("wpjb-flash-info").addClass("wpjb-flash-error").text(response.message);
                    }
                    
                }
            });
        }
    };
  
});