"use strict";

(function ($, window, document, undefined) {
  /**
   * On document ready.
   */
  $(document).ready(function () {
    $('.xbee-form').on('click', function () {
      var campaignReference = $(this).data('campaign-reference');
      var approveRedirect = $(this).data('approve-redirect');
      var declineRedirect = $(this).data('decline-redirect');
      var miniConsentUrl = xbeeFormParams.miniConsentURL + '?campaign_ref=' + campaignReference;
      var iframeParams = 'scrollbars=no,resizable=no,status=no,location=no,toolbar=no,menubar=no,width=500,height=500,left=0,top=0';
      var miniConsentWindow = window.open(miniConsentUrl, 'xbeeMiniConsent', iframeParams); // Form.

      var formId = $(this).data('form-id');
      var form = formId == undefined ? $(this).closest('form') : $('#' + formId); // Display overlay.

      form.css('position', 'relative');
      form.append('<div class="xbee-form-overlay"><img src="' + xbeeFormParams.images.loader + '"></div>'); // If window closed.

      var windowClosed = setInterval(function () {
        if (miniConsentWindow.closed) {
          // Remove overlay.
          form.find('.xbee-form-overlay').remove();
          clearInterval(windowClosed);
        }
      }, 500);
      window.addEventListener('message', function (event) {
        // Remove overlay.
        form.find('.xbee-form-overlay').remove(); // Redirect.

        if (event.data === 'approved' && typeof approveRedirect !== 'undefined') {
          window.location.assign(approveRedirect);
        } else if (event.data === 'declined' && typeof declineRedirect !== 'undefined') {
          window.location.assign(declineRedirect);
        }
      }, false);
    });
  });
})(jQuery, window, document);