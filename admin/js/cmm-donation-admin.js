function copyToClip(el) {
    /* Get the text field */
    var copyText = document.getElementById(el);

    /* Select the text field */
    copyText.select();
    copyText.setSelectionRange(0, 99999); /*For mobile devices*/

    /* Copy the text inside the text field */
    document.execCommand("copy");

    /* Alert the copied text */
    jQuery(copyText).parent('.column-shortcode').find('.cmm-donation-shortcode-copy-success').show();
    setTimeout(function() {
        jQuery(copyText).parent('.column-shortcode').find('.cmm-donation-shortcode-copy-success').hide();
    }, 500);

    return;
}
(function($) {
    'use strict';

    /**
     * All of the code for your admin-facing JavaScript source
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
        if ($('body').find('#cmm-donation-reports').length) {
            $('#cmm-donation-reports').DataTable({
                paging: false,
                info: false,
                searching: false,
                order: [
                    [0, 'desc']
                ],
            });
        }

        $(document).on('click', '#tabs-section .tab-link', function(e) {
            e.preventDefault();
            e.stopPropagation();
            $('#tabs-section .tab-link').removeClass('active ');
            $('#tabs-section .tab-body').removeClass('active ');
            $('#tabs-section .tab-body').removeClass('active-content');

            $(this).addClass('active');
            var id = $(this).attr('href');
            $('section' + id).addClass('active');
            $('section' + id).addClass('active-content');
        });

        $(document).on('click', '.single-donation-amount-wrap .amount-add', function(e) {
            e.preventDefault();
            e.stopPropagation();
            var i = $(this).parent('.donation-amount-inner').attr('data-index');
            i = parseInt(i) + 1;
            var new_html = '<div class="donation-amount-inner" data-index="' + i + '"><div class="form-inner donation-amount"><label>Amount</label><input class="input-text regular-input" type="text" name="cmm-donation-single-amt[' + i + '][amount]" value="" /></div><div class="form-inner donation-label"><label>Label</label><input class="input-text regular-input" type="text" name="cmm-donation-single-amt[' + i + '][label]" value="" /></div><div class="form-inner donation-desc"><label>Description</label><textarea class="input-text regular-input" type="text" name="cmm-donation-single-amt[' + i + '][desc]"></textarea></div><a href="" class="amount-add">Add New</a><a href="" class="amount-delete">Delete</a></div>';

            $(this).hide();
            $(this).parent('.donation-amount-inner').find('.amount-delete').show();
            $(this).parent('.donation-amount-inner').after(new_html);
        });

        $(document).on('click', '.single-donation-amount-wrap .amount-delete', function(e) {
            e.preventDefault();
            e.stopPropagation();
            if ($(this).parent('.donation-amount-inner').next().length == 0) {
                $(this).parent('.donation-amount-inner').prev().find('.amount-add').show();

            }
            $(this).parent('.donation-amount-inner').remove();
            var total = $('.donation-amount-inner').length;

            if ($('.single-donation-amount-wrap .donation-amount-inner').length == 1) {
                $('.single-donation-amount-wrap .donation-amount-inner').find('.amount-delete').hide();
            }
        });

        $(document).on('change', '#cmm-donation-single-other-amt', function(e) {
            if ($(this).is(':checked')) {
                $('.form-inner.single-other-amount-text').show();
                $('.form-inner.single-other-amount-desc').show();
            } else {
                $('.form-inner.single-other-amount-text').hide();
                $('.form-inner.single-other-amount-desc').hide();
            }
        });

        $(document).on('click', '.recurring-donation-amount-wrap .amount-add', function(e) {
            e.preventDefault();
            e.stopPropagation();
            var i = $(this).parent('.donation-amount-inner').attr('data-index');
            i = parseInt(i) + 1;
            var new_html = '<div class="donation-amount-inner" data-index="' + i + '"><div class="form-inner donation-amount"><label>Amount</label><input class="input-text regular-input" type="text" name="cmm-donation-recurring-amt[' + i + '][amount]" value="" /></div><div class="form-inner donation-label"><label>Label</label><input class="input-text regular-input" type="text" name="cmm-donation-recurring-amt[' + i + '][label]" value="" /></div><div class="form-inner donation-desc"><label>Description</label><textarea class="input-text regular-input" type="text" name="cmm-donation-recurring-amt[' + i + '][desc]"></textarea></div><a href="" class="amount-add">Add New</a><a href="" class="amount-delete">Delete</a></div>';

            $(this).hide();
            $(this).parent('.donation-amount-inner').find('.amount-delete').show();
            $(this).parent('.donation-amount-inner').after(new_html);
        });

        $(document).on('click', '.recurring-donation-amount-wrap .amount-delete', function(e) {
            e.preventDefault();
            e.stopPropagation();
            if ($(this).parent('.donation-amount-inner').next().length == 0) {
                $(this).parent('.donation-amount-inner').prev().find('.amount-add').show();

            }
            $(this).parent('.donation-amount-inner').remove();
            var total = $('.donation-amount-inner').length;

            if ($('.recurring-donation-amount-wrap .donation-amount-inner').length == 1) {
                $('.recurring-donation-amount-wrap .donation-amount-inner').find('.amount-delete').hide();
            }
        });

        $(document).on('change', '#cmm-donation-recurring-other-amt', function(e) {
            if ($(this).is(':checked')) {
                $('.form-inner.recurring-other-amount-text').show();
                $('.form-inner.recurring-other-amount-desc').show();
            } else {
                $('.form-inner.recurring-other-amount-text').hide();
                $('.form-inner.recurring-other-amount-desc').hide();
            }
        });

        $(document).on('change', 'select#cmm-donation-single-layout', function() {
            var get_val = $(this).val();
            var layout = 'layout-' + get_val;
            $('.single-donation-amount-wrap').removeClass('layout-list layout-grid');
            $('.single-donation-amount-wrap').addClass(layout);

            $('.form-inner.single-other-amount-desc').removeClass('layout-list layout-grid');
            $('.form-inner.single-other-amount-desc').addClass(layout);
        });

        $(document).on('change', 'select#cmm-donation-recurring-layout', function() {
            var get_val = $(this).val();
            var layout = 'layout-' + get_val;
            $('.recurring-donation-amount-wrap').removeClass('layout-list layout-grid');
            $('.recurring-donation-amount-wrap').addClass(layout);
            $('.form-inner.recurring-other-amount-desc').removeClass('layout-list layout-grid');
            $('.form-inner.recurring-other-amount-desc').addClass(layout);

        });

        // all export campaign function
        $(document).on('click', 'a.all-export-cmm-donation', function(e) {
            e.preventDefault();

            var campaignID = $(this).parents('tr').attr('data-id');

            var str = '&campaignID=' + campaignID + '&action=export_campaign_data_by_id';
            cmm_donation_call_export_data_ajax(str, campaignID);

        });


        var fullDate = new Date();

        var currentDate = ('0' + (fullDate.getMonth() + 1)).slice(-2) + '/' + ('0' + fullDate.getDate()).slice(-2) + '/' + fullDate.getFullYear();

        fullDate.setDate(fullDate.getDate() - 7);
        var oldCurrentDate = ('0' + (fullDate.getMonth() + 1)).slice(-2) + '/' + ('0' + fullDate.getDate()).slice(-2) + '/' + fullDate.getFullYear();

        // date filter export petition function
        if ($('body').find('a.date-filter-cmm-donation').length) {
            $('a.date-filter-cmm-donation').daterangepicker({

                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                },
                "alwaysShowCalendars": true,
                // "startDate": "03/03/2023",
                // "endDate": "03/09/2023",
                "startDate": currentDate,
                "endDate": oldCurrentDate,
                "opens": "left"
            });

            $('a.date-filter-cmm-donation').on('apply.daterangepicker', function(ev, picker) {

                var campaignID = $(this).parents('tr').attr('data-id');

                var startDate = picker.startDate.format('YYYY-MM-DD');
                var endDate_raw = picker.endDate.format('YYYY-MM-DD');

                var date = new Date(endDate_raw);

                var endDate = new Date(date);
                endDate.setDate(date.getDate() + 1);

                var day = endDate.getDate();
                var month = endDate.getMonth() + 1;
                var year = endDate.getFullYear();

                month = month.toString().padStart(2, "0");
                day = day.toString().padStart(2, "0");

                var new_endDate = year + '-' + month + '-' + day;
                var str = '&campaignID=' + campaignID + '&startDate=' + startDate + '&endDate=' + new_endDate + '&action=export_campaign_data_by_id_date';

                cmm_donation_call_export_data_ajax(str, campaignID);
            });
        }

        function cmm_donation_call_export_data_ajax(str, campaignID) {
            $.ajax({
                type: 'POST',
                datatype: 'JSON',
                url: cmm_donation_ajax.ajaxurl,
                data: str,
                beforeSend: function() {
                    $('.cmm-donation-report-page-wrap').addClass('loading');
                },
                success: function(data) {

                    $('.cmm-donation-report-page-wrap').removeClass('loading');

                    $('.cmm-donation-report-page-wrap .alert').hide();
                    if ($.trim(data)) {
                        var currentdate = new Date();
                        var datetime = currentdate.getDate() + "-" +
                            (currentdate.getMonth() + 1) + "-" +
                            currentdate.getFullYear() + "-" +
                            currentdate.getHours() + "-" +
                            currentdate.getMinutes() + "-" +
                            currentdate.getSeconds();
                        var filename = 'campaign-' + campaignID + '-' + datetime + '.csv';
                        /*
                         * Make CSV downloadable
                         */
                        var downloadLink = document.createElement("a");
                        var fileData = [$.trim(data)];

                        var blobObject = new Blob(fileData, {
                            type: "text/csv;charset=utf-8;"
                        });

                        var url = URL.createObjectURL(blobObject);
                        downloadLink.href = url;
                        downloadLink.download = filename;

                        /*
                         * Actually download CSV
                         */
                        document.body.appendChild(downloadLink);
                        downloadLink.click();
                        document.body.removeChild(downloadLink);
                    } else {
                        $('.cmm-donation-report-page-wrap .alert').html('').html('No data found to export for Campgin ID:' + campaignID);
                        $('.cmm-donation-report-page-wrap .alert').show();
                    }
                },

            });
        }

    });

})(jQuery);