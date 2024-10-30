jQuery(document).ready(function ($) {
    // Get nonce in meta and ajaxsetup for verifing nonce
    var nonce = $('meta[name="hbrab-token"]').attr('content');
    $.ajaxSetup({
        headers: {
            'X-HBRAB-TOKEN': nonce
        }
    });

    // Variable for display option in table hbrab_roles_list
    var display = '';

    $(".checkDisplay").on('change', function () {

        // Attribute at variable display depending on the choice of user clickbox checkbox 
        if ($(this).is(':checked')) {
            $(this).attr('value', 'true');
            display = 1;
        } else {
            $(this).attr('value', 'false');
            display = 0;
        }

        // Selectors DOM Elements and attributes
        var idRoleData = $(this).closest('tr').attr('data-id'),
            roleDisplay = $(this).val(),
            roleName = $(this).closest('tr').find(':first').attr('data-role'),
            roleStatus = $('#' + idRoleData),
            okStatus = $('#statusSave').text(),
            errorStatus = $('#statusError').text();

        $.ajax({
            'url': hbrabAjax.ajaxurl,
            'data': {
                "action": 'hbrabAjax',
                "id": idRoleData,
                "role": roleName,
                "roleDisplay": display
            },
            'type': 'POST',
            'beforeSend': function (xhr, settings) {
                console.log('ABOUT TO SEND');
            },
            'success': function (result, status_code, xhr) {
                console.log('SUCCESS!');
                $('#' + idRoleData).attr('data-color', 'green');
                $('#' + idRoleData).text(okStatus).delay(10).animate({
                    opacity: 1
                }, 100);
                $('#' + idRoleData).delay(1500).animate({
                    opacity: 0
                }, 3000);
            },
            'complete': function (xhr, text_status) {
                console.log('Done.');
            },
            'error': function (xhr, text_status, error_thrown) {
                console.log('ERROR!');
                $('#' + idRoleData).attr('data-color', 'red');
                $('#' + idRoleData).text(errorStatus).delay(10).animate({
                    opacity: 1
                }, 100);
                $('#' + idRoleData).delay(1500).animate({
                    opacity: 0
                }, 3000);
            }
        });
    });
});
