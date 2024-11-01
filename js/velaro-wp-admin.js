(function ($) {
    // function that gets selected page, and sets currently assigned group
    var matchVelaroGroup = function () {
        var pageID = $('.velaro-page-select option:selected').val();
        var val = $('[name=velaro_page_assignments]').val();
        var currentAssingments = val ? JSON.parse(val) : {};
        var pageAssignment = currentAssingments['page_' + pageID];
        if (pageAssignment) {
            $(".velaro-group-select option:selected").prop("selected", false);
            $('.velaro-group-select option[value="' + pageAssignment + '"]').prop("selected", true);
        } else {
            $(".velaro-group-select option:selected").prop("selected", false);
            $('.velaro-group-select option[value="0"]').prop("selected", true);
        }
    };
    // when the group select changes, set the value that will be saved in our plugins options
    $('.velaro-group-select').off();
    $('.velaro-group-select').on('change', function (e) {
        var pageID = $('.velaro-page-select option:selected').val();
        var val = $('[name=velaro_page_assignments]').val();
        var currentAssingments = val ? JSON.parse(val) : {};
        currentAssingments['page_' + pageID] = $('.velaro-group-select option:selected').val();
        $('[name=velaro_page_assignments]').attr('value', JSON.stringify(currentAssingments));
    });
    // when the page select changes, set the group select to the correct value
    $('.velaro-page-select').off();
    $('.velaro-page-select').on('change', function (e) {
        matchVelaroGroup();
    });
    // on click link, fire request to velaro to get the site identifier/api key for this login, will be stored in our options
    $('#velaro_attach').off();
    $('#velaro_attach').on('click', function () {
        var userName = $('[name=velaro_username]').val();
        var password = $('[name=velaro_password]').val();
        var model = {
            UserName: userName,
            Password: password
        };
        $.ajax({
            url: velaro_args.velaro_url,
            type: 'POST',
            data: JSON.stringify(model),
            success: function (data) {
                data = JSON.parse(data.Content);
                $('[name=velaro_site_identifier]').attr('value', data.Identifier);
                $('[name=velaro_api_key]').attr('value', data.ApiKey);
                $('input[type=submit]').click();
            },
            error: function (data) {
                $('[name=velaro_site_identifier]').attr('value', null);
                $('[name=velaro_api_key]').attr('value', null);
                $('input[type=submit]').click();
            },
            dataType: 'json',
            contentType: 'application/json'
        });
    });
    // match on initial load to show correct group
    matchVelaroGroup();

})(jQuery);