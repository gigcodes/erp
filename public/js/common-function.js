// Edit Button
function actionEditButton(url) {
    return `<a href="${url}" title="Edit" class="btn btn-image"><img src="/images/edit.png" /></a>`;
}

// String Limit set
function setStringLength(string_value, length = 20) {
    return string_value.length > length ? string_value.substring(0, length) + "..." : string_value;
}

// get Date in formatting 
function getDateByFormat(date) {
    const d = new Date(date);
    const ye = new Intl.DateTimeFormat('en', {
        year: 'numeric'
    }).format(d);
    const mo = new Intl.DateTimeFormat('en', {
        month: 'short'
    }).format(d);
    const da = new Intl.DateTimeFormat('en', {
        day: '2-digit'
    }).format(d);
    return `${da} ${mo} ${ye}`;
}

// get DateTime in formatting 
function getDateTimeByFormat(date) {
    const d = new Date(date);
    const ye = new Intl.DateTimeFormat('en', {
        year: 'numeric'
    }).format(d);
    const mo = new Intl.DateTimeFormat('en', {
        month: 'short'
    }).format(d);
    const da = new Intl.DateTimeFormat('en', {
        day: '2-digit'
    }).format(d);
    // const h = new Intl.DateTimeFormat('en', {
    //     hour: '2-digit'
    // }).format(d);
    // const i = new Intl.DateTimeFormat('en', {
    //     minute: '2-digit'
    // }).format(d);
    // return `${da} ${mo} ${ye} ${h}:${i}`;
    return `${da} ${mo} ${ye}`;
}

// Show details page button
function actionShowButton(url) {
    return `<a href="${url}" title="Details Page" class="btn btn-image"><img src="/images/view.png" /></a>`;
}

// Title or String persent to better way
function actionShowTitle(url, stringTitle) {
    return `<a class="btn btn-sm btn-clean" href="` + url + `" title="` + stringTitle + `">` + stringTitle + `</a>`;
}

// delete button
function actionDeleteButton(id, deleteclass = "clsdelete") {
	return `<a  class="btn btn-image ${deleteclass}" data-id="${id}"><img src="/images/delete.png" /></a>`;
}

// status Button 
function actionActiveButton(data, attr, statusclass = "clsstatus") {
    // return parseInt(data) ? "<span class=\"badge badge-success cursor-pointer "+statusclass+" \" "+ attr +" >"+"{{ trans_choice('content.active_title', 1) }}"+"</span>" : "<span class=\"badge badge-danger cursor-pointer  "+statusclass+" \" "+ attr +">"+"Deactivate"+"</span>";
    if (data == 1) {
        return `<div class="badge badge-light-success fw-bolder ${statusclass}" ${attr}>{{ trans_choice('content.active_title', 1) }}</div>`;
    } else {
        return `<div class="badge badge-light-danger fw-bolder ${statusclass}" ${attr}>{{ trans_choice('content.inactive_title', 1) }}</div>`;
    }
}

// Ajax For delete row 
function tableDeleteRow(url, oTable) {
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!',
        showLoaderOnConfirm: true,
        preConfirm: function() {
            return new Promise(function(resolve) {
                $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        url: url,
                        type: 'DELETE',
                        dataType: 'json'
                    })
                    .done(function(response) {
                        oTable.draw();
                        Swal.fire('Deleted!', response.message, 'success');
                    })
                    .fail(function(response) {
                        console.log(response);
                        console.log(url);
                        Swal.fire('Oops...', 'Something went wrong with ajax !', 'error');
                    });
            });
        },
        allowOutsideClick: false
    });
}

// Ajax update status 
function tableChnageStatus(url, oTable, message = 'You will be able to revert this') {
    Swal.fire({
        title: "Are you sure?",
        text: message,
        type: "info",
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: "Yes, delete it!",
        showLoaderOnConfirm: true,
        preConfirm: function() {
            return new Promise(function(resolve) {
                $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                                'content')
                        },
                        url: url,
                        type: 'GET',
                        dataType: 'json'
                    })
                    .done(function(response) {
                        if (response.status == 1) {
                            oTable.draw();
                            Swal.fire('Updated!', response.message, 'success');
                        } else {
                            Swal.fire('Info!', response.message, 'info');
                        }
                    })
                    .fail(function() {
                        Swal.fire('Oops...', 'Something went wrong with ajax !',
                            'error');
                    });
            });
        },
        allowOutsideClick: false
    });
}

$(document).ready(function() {
    setTimeout(function() {
        if ($('#ns').length > 0) {
            $('#ns').remove();
        }
    }, 5000)
});

