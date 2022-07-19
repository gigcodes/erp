function funDevelopmentTaskStartDateModal(taskId, startDate) {
  jQuery('#modalDevelopmentTaskStartTimeUpdate').modal('show');
  jQuery('#modalDevelopmentTaskStartTimeUpdate').attr('data-row_id', taskId);
  jQuery('#start_date').val(startDate);
}
function funDevelopmentTaskStartDateUpdate(url) {
  jQuery.ajax({
    headers: {
      'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
    },
    url: url,
    type: 'POST',
    data: {
      id: jQuery('#modalDevelopmentTaskStartTimeUpdate').attr('data-row_id'),
      start_date: jQuery('#start_date').val(),
    }
  }).done(function (response) {
    toastr['success']('Successfully updated');
    jQuery('#modalDevelopmentTaskStartTimeUpdate').modal('hide');
  }).fail(function (errObj) {
    if (errObj.responseJSON != undefined) {
      toastr['error'](errObj.responseJSON.message);
    }
    else if (errObj.message != undefined) {
      toastr['error'](errObj.message);
    }
    else if (errObj.message != undefined) {
      toastr['error']('Unknown error occured.');
    }
  });
}
function funDevelopmentTaskStartDateHistory(id) {
  let mdl = jQuery('#modalDevelopmentTaskStartTimeHistory');
  jQuery.ajax({
    headers: {
      'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
    },
    url: mdl.attr('data-url'),
    type: 'GET',
    data: {
      id: id
    },
    beforeSend: function () {
      jQuery("#loading-image").show();
    }
  }).done(function (response) {
    jQuery("#loading-image").hide();
    mdl.find('tbody').html(response.data);
    mdl.modal("show");
    // toastr['success'](response.msg);
  }).fail(function (errObj) {
    if (errObj.responseJSON != undefined) {
      toastr['error'](errObj.responseJSON.message);
    }
    else if (errObj.message != undefined) {
      toastr['error'](errObj.message);
    }
    else if (errObj.message != undefined) {
      toastr['error']('Unknown error occured.');
    }
  });
}

jQuery(document).ready(function () {
  if (jQuery('#start_date').length) {
    jQuery('#start_date').datetimepicker({
      format: 'YYYY-MM-DD HH:mm:ss'
    });
  }
});