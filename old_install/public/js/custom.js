
jQuery(document).ready(function () {

    jQuery('li.notification-dropdown .dropdown-toggle').on('click', function (event) {
        event.preventDefault();
        jQuery(this).parent().toggleClass('show');
        jQuery(this).next().toggleClass('show');
    });

    jQuery('body').on('click', function (e) {

        let dropdown = jQuery('li.dropdown.notification-dropdown');

        if (!dropdown.is(e.target)
            && dropdown.has(e.target).length === 0
            && jQuery('.show').has(e.target).length === 0
        ) {
            dropdown.removeClass('show');
            jQuery('li.dropdown.notification-dropdown ul').removeClass('show');
        }
    });

    jQuery('.btn-notify').click(function () {

        let btnNotify = jQuery(this);
        let id = btnNotify.attr('data-id');

        jQuery.ajax({
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            },
            type:'POST',
            url:'notificationMarkRead/'+id,
            success: function (data) {

                if(data.msg === 'success'){
                    btnNotify.parent().parent().addClass('isread');
                }
            }
        });

    });

    jQuery('.removeOldImage').click(function (e) {
        e.preventDefault();

        let id = jQuery(this).attr('data-id');
        let image_id = jQuery(this).attr('media-id');

       jQuery('input[name="oldImage'+id+'"]').val( image_id);
       jQuery('.old-image'+id).hide();
       jQuery('.new-image'+id).show();
    });

    jQuery('input[name="measurement_size_type"]').on('change',function () {

        let checked_value = jQuery('input[name="measurement_size_type"]:checked').val();

        if( checked_value === 'measurement' )
        {
            jQuery('#measurement_row').show();
            jQuery('#size_row').hide();
        }
        else if( checked_value === 'size' ) {
            jQuery('#measurement_row').hide();
            jQuery('#size_row').show();
        }
        else {
            jQuery('#measurement_row').hide();
            jQuery('#size_row').hide();
        }

    });

    jQuery('input[name="measurement_size_type"]').trigger('change');

});

function getTodayYesterdayDate(date) {

    let a = moment(new Date());
    let b = moment(date);

    if( a.diff(b, 'days') === 0){
        return 'Today';
    }
    else if( a.diff(b, 'days') === 1){
        return 'Yesterday';
    }

    return moment(date).format('DD/MM/YYYY');;
}

function attactApproveEvent() {

    jQuery('.btn-approve').click(function (e) {

        e.preventDefault();

        let btnApprove = jQuery(this);
        let id = btnApprove.attr('data-id');

        jQuery.ajax({
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            },
            type:'POST',
            url:'productsupervisor/approve/'+id,
            success: function (data) {

                if(data.msg === 'success'){

                    if( data.isApproved ) {
                        btnApprove.addClass('btn-success');
                        btnApprove.html('Approved');
                    }
                    else {
                        btnApprove.removeClass('btn-success');
                        btnApprove.html('Approve');
                    }
                }
            }
        });

    });

}
