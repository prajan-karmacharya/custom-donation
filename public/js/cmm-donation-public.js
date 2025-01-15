(function($) {
    'use strict';

    /**
     * All of the code for your public-facing JavaScript source
     * should reside in this file.
     *
     * Note: It has been assumed you will write jQuery code here, so the
     * $ function reference has been prepared for usage within the scope
     * of this function.
     *
     * This enables you to define handlers, for when the DOM is ready:
     *
     * $(function() {
     *
     * });
     *
     * When the window is loaded:
     *
     * $( window ).load(function() {
     *
     * });
     *
     * ...and/or other possibilities.
     *
     * Ideally, it is not considered best practise to attach more than a
     * single DOM-ready or window-load handler for a particular page.
     * Although scripts in the WordPress core, Plugins and Themes may be
     * practising this, we should strive to set a better example in our own work.
     */

    $(document).ready(function() {
        $(document).on('change', '.cmm-donation-content .type-list input.donation-radio, .cmm-donation-content .type-grid input.donation-radio', function(e) {
            var $get_val = $(this).val();
            if ($get_val == 'custom') {
                $(this).parents('.cmm-donation-content').find('.custom-amount-input-wrap').removeClass('hide');
                $(this).parents('.cmm-donation-content').find('.custom-amount-input-wrap input.donation-custom-amount').attr('required', true);
            } else {
                $(this).parents('.cmm-donation-content').find('.custom-amount-input-wrap').addClass('hide');
                $(this).parents('.cmm-donation-content').find('.custom-amount-input-wrap input.donation-custom-amount').removeAttr('required');
            }
        });


        if ($('body').find('#cmm-donation-checkout-form').length) {
            $('#cmm-donation-checkout-form').validate();
            var get_clientId = $('#clientId').val();
            var get_merchantCode = $('#merchantCode').val();
            var mySecurePayUI = new securePayUI.init({
                containerId: 'securepay-ui-container',
                scriptId: 'securepay-ui-js-js',
                clientId: get_clientId,
                merchantCode: get_merchantCode,
                card: { // card specific config and callbacks
                    allowedCardTypes: ['visa', 'mastercard', 'amex', 'diners'],
                    showCardIcons: true,
                    onTokeniseSuccess: function(tokenisedCard) {
                        // card was successfully tokenised
                        var card_token = tokenisedCard.token;
                        var id = $('#id').val();
                        var amount = $('#amount').val();
                        var type = $('#type').val();
                        var frequency = $('#frequency').val();

                        var firstname = $('#firstname-cmm-donation').val();
                        var lastname = $('#lastname-cmm-donation').val();
                        var email = $('#email-cmm-donation').val();
                        var phone = $('#phone-cmm-donation').val();
                        var company = $('#company-cmm-donation').val();
                        var country = $('#country-cmm-donation').val();
                        var address_1 = $('#address-1-cmm-donation').val();
                        var address_2 = $('#address-2-cmm-donation').val();
                        var abn = $('#company-abn-cmm-donation').val();
                        var suburb = $('#suburb-cmm-donation').val();
                        var state = $('#state-cmm-donation').val();
                        var postcode = $('#postcode-cmm-petition').val();

                        var str = '&id=' + id + '&amount=' + amount + '&type=' + type + '&frequency=' + frequency + '&firstname=' + firstname + '&lastname=' + lastname + '&email=' + encodeURIComponent(email) + '&phone=' + phone + '&company=' + company + '&country=' + country + '&address_1=' + address_1 + '&address_2=' + address_2 + '&suburb=' + suburb + '&abn=' + abn + '&state=' + state + '&postcode=' + postcode + '&tokenisedCard=' + card_token + '&action=cmm_donation_process';

                        $.ajax({
                            type: 'POST',
                            datatype: 'JSON',
                            url: cmm_petition_ajax.ajaxurl,
                            data: str,
                            beforeSend: function() {
                                $('#cmm-donation-loading').show();
                                $('#cmm_donation_submit_button').addClass('pointer-none');
                            },
                            success: function(data) {
                                var newData = JSON.parse(data);

                                $('#cmm-donation-loading').hide();
                                $('#cmm_donation_submit_button').removeClass('pointer-none');
                                if (newData.response == 'success') {

                                    var form = $('<form id="cmm-donation-thankyou" action="' + newData.page + '" method="post">' +
                                        '<input type="text" name="billing_id" value="' + newData.billing_id + '" />' +
                                        '</form>');
                                    $('body').append(form);
                                    form.submit();

                                } else if (newData.response == 'error') {
                                    $('body').find('.cmm-donation-message').removeClass('error success');
                                    $('body').find('.cmm-donation-message').addClass('error').html('').html('Donation Error !! Please try again.');
                                }
                            },

                        });

                    },
                    onTokeniseError: function(error) {
                        // card was successfully tokenised
                        $('body').find('.cmm-donation-message').removeClass('error success');
                        $('body').find('.cmm-donation-message').addClass('error').html('').html('Card Error !! Please try again.');
                    },
                },
                onLoadComplete: function() {
                    // the SecurePay UI Component has successfully loaded and is ready to be interacted with
                }
            });
            $(document).on('submit', '#cmm-donation-checkout-form', function(e) {
                e.preventDefault();
                mySecurePayUI.tokenise();
            });
        }




    });

})(jQuery);