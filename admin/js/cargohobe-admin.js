(function ($) {
    'use strict';

    jQuery(document).ready(function () { // wait for page to finish loading
        /**
         * Send ajax request to a method when user click send data button
         */
        jQuery("#send-data").click(function () {

            jQuery.ajax({
                type: "POST",
                url: "/wp-admin/admin-ajax.php",
                data: {
                    action: 'cargohobe_remote_send_all_data',
                }
            });

        });
    });

})(jQuery);
