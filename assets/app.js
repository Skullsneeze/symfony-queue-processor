import './bootstrap.js';
/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
import './styles/app.css';

function showPageLoader() {
    $('#page-loader').removeClass('hidden');
}

function hidePageLoader() {
    $('#page-loader').addClass('hidden');
}

function createAlert(type, title, message) {
    let $messageContainer = $('#message-container');
    let templateHtml = $('#alert-template-' + type).html();
    let $alert = $(templateHtml);

    $alert.find('.alert-title').html(title);
    $alert.find('.alert-message').html(message);

    $messageContainer.append($alert);

    let closeTimeout = setTimeout(function () {
        $alert.slideUp(200, function () {
            $alert.remove()
        });
    }, 3000);

    $alert.find('button.alert-close').on('click', function (event) {
        event.preventDefault();
        clearTimeout(closeTimeout);
        $alert.slideUp(200, function () {
            $alert.remove()
        });
    });


}

$(document).ready(function () {
    let $queueTrigger = $('#message-triggers-container').find('button');
    $queueTrigger.on('click', function (event) {
        event.preventDefault();

        showPageLoader();

        let postAction = $(this).data('post-action');
        $.ajax({
            type: "POST",
            url: postAction,
            dataType: 'json',
            success: function (response) {
                console.log(response);
                if (response.success === true) {
                    createAlert('success', 'Messages added', response.message);
                } else {
                    createAlert('error', 'Something went wrong!', response.message);
                }
            }
        }).always(function () {
            hidePageLoader();
        });
    })
});