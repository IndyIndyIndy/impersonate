/***
 *
 * This file is part of the "Impersonate" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 *  (c) 2019 Christian Eßl <indy.essl@gmail.com>, https://christianessl.at
 *
 ***/

define(["jquery"], function($) {
    "use strict";

    var ImpersonateButton = {

    };

    ImpersonateButton.init = function() {
        $(document).on('click', '.t3-impersonate-button', function (e) {
            var uid = $(this).data('uid');
            var href = $(this).attr('href');

            e.preventDefault();

            ImpersonateButton.loginFrontendUser(uid, function () {
                window.open(href, '_blank');
            });
        });
    };

    /**
     *  @param {int} uid
     *  @param {function} callback
     */
    ImpersonateButton.loginFrontendUser = function(uid, callback) {
        $.post(
            TYPO3.settings.ajaxUrls.impersonate_frontendlogin,
            {
                uid: uid
            }, function(data) {
                // @todo handle errors
                console.log(data);
                callback();
            }
        );
    };

    return ImpersonateButton;
});