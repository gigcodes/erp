<script>


$(document).on('click', '.menu_editor_copy', function() {
        var content = $(this).data('content');

        menucopyToClipboard(content);
        /* Alert the copied text */
        toastr['success']("Copied the text: " + content);
        //alert("Copied the text: " + remark_text);
    });

    function menucopyToClipboard(text) {
        var sampleTextarea = document.createElement("textarea");
        document.body.appendChild(sampleTextarea);
        sampleTextarea.value = text; //save main text in it
        sampleTextarea.select(); //select textarea contenrs
        document.execCommand("copy");
        document.body.removeChild(sampleTextarea);
    }


    $(document).on('click', '.menu_editor_edit', function() {

        var $this = $(this);

        $.ajax({
            type: "GET",
            data: {
                id: $this.data("id")

            },
            url: "{{ route('editName') }}"
        }).done(function(data) {

            console.log(data.sopedit);

            $('#sop_edit_id').val(data.sopedit.id)
            $('#sop_edit_name').val(data.sopedit.name)
            $('#sop_edit_category').val(data.sopedit.category)
            $('#sop_old_name').val(data.sopedit.name)
            $('#sop_old_category').val(data.sopedit.category)

            CKEDITOR.instances['sop_edit_content'].setData(data.sopedit.content)

            $("#menu-sopupdate #menu_sop_edit_form").attr('data-id', $($this).attr('data-id'));
            $("#menu-sopupdate").modal("show");

        }).fail(function(data) {
            console.log(data);
        });
    });

    $(document).on('submit', '#menu_sop_edit_form', function(e) {
        e.preventDefault();
        const $this = $(this)
        $(this).attr('data-id', );

        $.ajax({
            type: "POST",
            data: $(this).serialize(),
            url: "{{ route('updateName') }}",
            datatype: "json"
        }).done(function(data) {

            if(data.success==false){
                toastr["error"](data.message, "Message");
                return false;
            }

            if(data.type=='edit'){
                var content = data.sopedit.content.replace( /(<([^>]+)>)/ig, '');

                let id = $($this).attr('data-id');

                $('#sid' + id + ' td:nth-child(1)').html(data.sopedit.id);
                $('#sid' + id + ' td:nth-child(2)').html(`
                            <span class="show-short-name-`+data.sopedit.id+`">`+data.sopedit.name.replace(/(.{17})..+/, "$1..")+`</span>
                            <span style="word-break:break-all;" class="show-full-name-`+data.sopedit.id+` hidden">`+data.sopedit.name+`</span>
                        `);
                $('#sid' + id + ' td:nth-child(3)').html(`
                            <span class="show-short-category-`+data.sopedit.id+`">`+data.sopedit.category.replace(/(.{17})..+/, "$1..")+`</span>
                            <span style="word-break:break-all;" class="show-full-category-`+data.sopedit.id+` hidden">`+data.sopedit.category+`</span>
                        `);
                $('#sid' + id + ' td:nth-child(4)').html(`
                            <span class="show-short-content-`+data.sopedit.id+`">`+content.replace(/(.{50})..+/, "$1..")+`</span>
                            <span style="word-break:break-all;" class="show-full-content-`+data.sopedit.id+` hidden">`+content+`</span>
                        `);
                $("#menu_sopupdate").modal("hide");
                toastr["success"]("Data Updated Successfully!", "Message")
            }else{
                //var content_class = data.sopedit.content.length < 270 ? '' : 'expand-row';
                //var content = data.sopedit.content.length < 270 ? data.sopedit.content : data.sopedit.content.substr(0, 270) + '.....';
                $("#NameTable-app-layout tbody").prepend(`
                        <tr id="sid`+data.sopedit.id+`" data-id="`+data.sopedit.id+`" class="parent_tr">
                                <td class="sop_table_id">`+data.sopedit.id+`</td>
                                <td class="expand-row-msg" data-name="name" data-id="`+data.sopedit.id+`">
                                    <span class="show-short-name-`+data.sopedit.id+`">`+data.sopedit.name.replace(/(.{17})..+/, "$1..")+`</span>
                                    <span style="word-break:break-all;" class="show-full-name-`+data.sopedit.id+` hidden">`+data.sopedit.name+`</span>
                                </td>
                                <td class="expand-row-msg" data-name="category" data-id="`+data.sopedit.id+`">
                                    <span class="show-short-category-`+data.sopedit.id+`">`+data.sopedit.category.replace(/(.{17})..+/, "$1..")+`</span>
                                    <span style="word-break:break-all;" class="show-full-category-`+data.sopedit.id+` hidden">`+data.sopedit.category+`</span>
                                </td>
                                <td class="expand-row-msg" data-name="content" data-id="`+data.sopedit.id+`">
                                    <span class="show-short-content-`+data.sopedit.id+`">`+data.sopedit.content.replace(/(.{50})..+/, "$1..")+`</span>
                                    <span style="word-break:break-all;" class="show-full-content-`+data.sopedit.id+` hidden">`+data.sopedit.content+`</span>
                                </td>
                                <td class="table-hover-cell p-1">
                                    <div>
                                        <div class="w-75 pull-left">
                                            <textarea rows="1" class="form-control" id="messageid_`+data.sopedit.user_id+`" name="message" placeholder="Message"></textarea>
                                        </div>
                                        <div class="w-25 pull-left">
                                            <button class="btn btn-xs send-message-open pull-left" data-user_id="`+data.sopedit.user_id+`">
                                                <i class="fa fa-paper-plane"></i>
                                            </button>
                                             <button type="button"
                                                    class="btn btn-xs load-communication-modal pull-left"
                                                    data-id="`+data.sopedit.user_id+`" title="Load messages"
                                                    data-object="SOP">
                                                    <i class="fa fa-comments"></i>
                                            </button>
                                        </div>
                                   </div>
                                </td>
                                <td>`+data.only_date+`</td>
                                <td class="p-1">
                                    <a href="javascript:;" data-id="`+data.sopedit.id+`" class="menu_editor_edit btn btn-xs p-2" >
                                        <i class="fa fa-edit"></i>
                                    </a>
                                    <a class="btn btn-image deleteRecord p-2 text-secondary" data-id="`+data.sopedit.id+`">
                                        <i class="fa fa-trash" ></i>
                                    </a>
                                    <a class="btn btn-xs view_log p-2 text-secondary" title="status-log"
                                        data-name="`+data.params.header_name+`"
                                        data-id="`+data.sopedit.id+`" data-toggle="modal" data-target="#ViewlogModal">
                                        <i class="fa fa-info-circle"></i>
                                    </a>
                                    <a title="Download Invoice" class="btn btn-xs p-2" href="sop/DownloadData/`+data.sopedit.id+`">
                                            <i class="fa fa-download downloadpdf"></i>
                                    </a>
                                    <button type="button" class="btn send-email-common-btn p-2" data-toemail="`+data.user_email[0].email+`" data-object="Sop" data-id="`+data.sopedit.user_id+`">
                                        <i class="fa fa-envelope-square"></i>
                                    </button>
                                    <button data-target="#Sop-User-Permission-Modal" data-toggle="modal" class="btn btn-secondaryssss sop-user-list  p-2" title="Sop User" data-sop_id="`+data.sopedit.user_id+`">
                                        <i class="fa fa-user-o"></i>
                                    </button>
                                </td>
                        </tr>
                        `);

                $("#menu_sopupdate").modal("hide");
                toastr["success"]("Data Updated Successfully!", "Message")
            }


        }).fail(function(data) {
            console.log(data);
        });
    });

    $('#FormModalAppLayout').submit(function(e) {
            e.preventDefault();
            let name = $("#name-app-layout").val();
            let category = $("#categorySelect-app-layout").val();
            if(category.length==0){
                toastr["error"]('Select Category', "Message");
                return false;
            }
            let content = CKEDITOR.instances['content-app-layout'].getData(); //$('#cke_content').html();//$("#content").val();
            if(content==''){
                toastr["error"]('Content not', "Message");
                return false;
            }
            let _token = $("input[name=_token]").val();
            $.ajax({
                url: "{{ route('sop.store') }}",
                type: "POST",
                data: {
                    name: name,
                    category: category,
                    content: content,
                    _token: _token
                },
                success: function(response) {
                    if (response) {
                        if(response.success==false){
                            toastr["error"](response.message, "Message");
                            return false;
                        }
                        location.reload();
                    }
                }

            });
        });

    $(document).on("click", ".menu-sop-search", function(e) {
        e.preventDefault();
        $("#menu-sop-search-model").modal("show");
    });

    // Global user search from the menu - S
    $(document).on("click", ".menu-user-search", function(e) {
        e.preventDefault();
        $("#menu-user-search-model").modal("show");
        get_user_data();
    });

    $(document).on("click", ".menu-user-search-btn", function(e) {
        e.preventDefault();
        get_user_data();
    });

    function get_user_data(){
        let _token = "{{csrf_token()}}";
        $(".processing-txt").removeClass('d-none');
        $.ajax({
            url: "{{ route('user-search-global') }}",
            type: "POST",
            data: {
                q: $("#menu_user_search").val().trim(),
                _token: _token
            },
            success: function(response) {
                var trData = "";
                $(".processing-txt").addClass('d-none');
                if (response) {
                    $.each(response, function(index, value) {
                        var user_email = (value.email != null) ? "<span class='copy_me'>"+value.email+"</span> <a href='javascript:void(0)' class='copy_the_text'><i class='fa fa-copy' aria-hidden='true'></i></a>" : "";
                        var user_name =  (value.name != null) ? "<span class='copy_me'>"+value.name+"</span> <a href='javascript:void(0)' class='copy_the_text'><i class='fa fa-copy' aria-hidden='true'></i></a>" : "";
                        var user_phone =  (value.phone != null) ? "<span class='copy_me'>"+value.phone+"</span> <a href='javascript:void(0)' class='copy_the_text'><i class='fa fa-copy' aria-hidden='true'></i></a>" : "";
                        trData += "<tr>";
                        trData += "<td>"+value.id+"</td>";
                        trData += "<td>"+user_name+"</td>";
                        trData += "<td>"+user_email+"</td>";
                        trData += "<td>"+user_phone+"</td>";
                        trData += "</tr>";
                    });
                    $(".user_search_global_result").html(trData);
                    console.log(trData);
                }
            }
        });
    }

    $(document).on("click", ".copy_the_text", function(e) {
        // Get the text content of the element
        var textToCopy = $(this).prev('span.copy_me').text();

        // Create a temporary input element
        var tempInput = $('<input>');
        
        // Set its value to the text content
        tempInput.val(textToCopy);

        // Append it to the body
        $('body').append(tempInput);

        // Select the text in the input
        tempInput.select();

        // Copy the selected text to the clipboard
        document.execCommand('copy');

        // Remove the temporary input element
        tempInput.remove();
        
        // Optionally, provide feedback to the user
        // alert('Text copied to clipboard: ' + textToCopy);
        toastr['success']('Text copied!', 'success');
    });

    // Global user search from the menu - E    

    $(document).on("click", ".menu-email-search", function(e) {
        e.preventDefault();
        $("#menu-email-search-model").modal("show");
    });

    $(document).on("click", ".sop_search_menu", function(e) {
        let $this = $('#menu_sop_search').val();
        var q = $this;
        $.ajax({
            url: '{{route('menu.sop.search')}}',
            type: 'GET',
            data: {
                search: q,
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            // dataType: 'json',
            beforeSend: function() {
                $("#loading-image").show();
            },
            success: function(response) {
                $("#loading-image").hide();
                $('.sop_search_result').empty();
                $('.sop_search_result').append(response);
                toastr['success']('Data updated successfully', 'success');
            },
            error: function() {
                $("#loading-image").hide();
                toastr["Error"]("An error occured!");
            }
        });
    });

    $(document).on("click", ".email_search_menu", function(e) {
        let $this = $('#menu_email_search').val();
        var q = $this;
        $.ajax({
            url: '{{route('menu.email.search')}}',
            type: 'GET',
            data: {
                search: q,
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            // dataType: 'json',
            beforeSend: function() {
                $("#loading-image").show();
            },
            success: function(response) {
                $("#loading-image").hide();
                $('.email_search_result').empty();
                $('.email_search_result').append(response);
                toastr['success']('Data updated successfully', 'success');
            },
            error: function() {
                $("#loading-image").hide();
                toastr["Error"]("An error occured!");
            }
        });
    });

        $(document).ready(function() {
            $('#searchField').on('keyup', function() {
                var searchText = $(this).val().toLowerCase().replace(/\s/g, ''); // Convert to lowercase and remove spaces

                if (searchText) {
                    $('.quick-icon').each(function() {
                        var title = $(this).attr('title').toLowerCase().replace(/\s/g, ''); // Convert to lowercase and remove spaces
                        var className = $(this).attr('class').toLowerCase().replace(/\s/g, ''); // Convert to lowercase and remove spaces

                        if (title.indexOf(searchText) !== -1 || className.indexOf(searchText) !== -1) {
                            $(this).closest('li').addClass('highlight'); // Add highlight class to the parent li element
                            $(this).addClass('highlight');
                            // $(this).closest('li').addClass('highlight'); // Add highlight class to the parent li element
                        } else {
                            $(this).removeClass('highlight');
                            $(this).closest('li').removeClass('highlight'); // Remove highlight class from the parent li element
                        }
                    });
                } else {
                    $('.quick-icon').removeClass('highlight');
                    $('.quick-icon').closest('li').removeClass('highlight'); // Remove highlight class from all parent li elements when searchText is empty
                }
            });
         });

    $(document).on('click', '.send-message-open-menu', function (event) {
        var thiss = $(this);
        var $this = $(this);
        var data = new FormData();
        var sop_user_id = $(this).data('user_id');
        var id = $(this).data('id');
        var sop_user_id = $('#user_'+id).val();
        var message = $(this).parents('td').find("#messageid_"+id).val();

        if (message.length > 0) {
            //  let self = textBox;
            $.ajax({
                url: "{{action([\App\Http\Controllers\WhatsAppController::class, 'sendMessage'], 'SOP-Data')}}",
                type: 'POST',
                data: {
                    "sop_user_id": sop_user_id,
                    "message": message,
                    "_token": "{{csrf_token()}}",
                    "status": 2,
                },
                dataType: "json",
                success: function (response) {
                    $this.parents('td').find("#messageid_"+sop_user_id).val('');
                    toastr["success"]("Message sent successfully!", "Message");
                    $('#message_list_' + sop_user_id).append('<li>' + response.message.created_at + " : " + response.message.message + '</li>');
                },
                error: function (response) {
                    toastr["error"]("There was an error sending the message...", "Message");
                }
            });
        } else {
            alert('Please enter a message first');
        }
    });

    $(document).on('hidden.bs.modal', '#chat-list-history', function() {
        $('body').removeClass('openmodel');
    });
    $(document).on('shown.bs.modal', '#chat-list-history', function() {
        $('body').addClass('openmodel');
    });

    $(document).on('change', '.sop_drop_down', function() {
        var val = $(this).val();

        if ($(this).val() == "knowledge_base") {
            $(this).parents('.add_sop_modal').find('.knowledge_base').removeAttr('hidden');
            $('.sop_solution').addClass('hidden');
        } else if ($(this).val() == "code_shortcut") {
            $('.sop_solution').removeClass('hidden');
            $(this).parents('.add_sop_modal').find('.knowledge_base').attr('hidden', true).val('');
            $(this).parents('.add_sop_modal').find('.knowledge_base_book').attr('hidden', true).val('');
        } else {
            $(this).parents('.add_sop_modal').find('.knowledge_base').attr('hidden', true).val('');
            $(this).parents('.add_sop_modal').find('.knowledge_base_book').attr('hidden', true).val('');
            $('.sop_solution').addClass('hidden');
        }

        var selectedOptionText = $(this).find('option:selected').text();
        
        $(this).parents('.add_sop_modal').find('.category').val(selectedOptionText);
        
    })

    $(document).on('click', '.expand-row-email', function () {
        var selection = window.getSelection();
        if (selection.toString().length === 0) {
            $(this).find('.td-mini-email-container').toggleClass('hidden');
            $(this).find('.td-full-email-container').toggleClass('hidden');
        }
    });

    $(document).ready(function(){
        $('#unreadEmail').change(function(){

            var userEmaillUrl = '/email/email-frame/'+$(this).val();

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: userEmaillUrl,
                type: 'get',
            }).done( function(response) {
            }).fail(function(errObj) {
            });
        });
    });

    function openQuickMsg(userEmail) {

        $('#unreadEmail').prop('checked', false);

        $('#iframe').attr('src', "");
        var userEmaillUrl = '/email/email-frame/'+userEmail.id;

        $('#unreadEmail').val(userEmail.id);

        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: userEmaillUrl,
            type: 'get',
        }).done( function(response) {
        }).fail(function(errObj) {
        });

        var isHTML = isHTMLContent(userEmail.message);
        if (isHTML) {
            $('#formattedContent').html(userEmail.message);
        } else {
            var formattedHTML = formatContentToHTML(userEmail.message);
            $('#formattedContent').html(formattedHTML);
        }

        $('#receiver_email').val(userEmail.to);
        $('#reply_email_id').val(userEmail.id);

        function isHTMLContent(content) {
            return /<[a-z][\s\S]*>/i.test(content);
        }

        function formatContentToHTML(rawContent) {
            var decodedContent = $('<textarea/>').html(rawContent).text();
            var formattedContent = decodedContent.replace(/\n/g, '<br>');
            formattedContent = formattedContent.replace(/(https?:\/\/[^\s]+)/g, '<a href="$1">$1</a>');
            formattedContent = '<div class="form-control" style=" height: auto;">' + formattedContent + '</div>';

            return formattedContent;
        }
        $('#quickemailSubject').val(userEmail.subject);
        $('#quickemailDate').html(moment(userEmail.created_at).format('YYYY-MM-DD H:mm:ss'));
        $('#iframe').attr('src', userEmaillUrl);
        
        var senderName = 'Hello '+userEmail.from.split('@')[0]+',';
        addTextToEditor(senderName);
    }

    $(document).on('click', '.updatedeclienremarks', function (e) {
        e.preventDefault();
        var appointment_requests_id = $("#appointment_requests_id").val();
        var appointment_requests_remarks = $('#appointment_requests_remarks').val();

        if(appointment_requests_id == '') {
            alert("Something went wrong. Please try again.");
            return false;
        }

        if(appointment_requests_remarks == '') {
            $('#appointment_requests_remarks').next().text("Please enter the subject");
            return false;
        }

        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: '{{route('appointment-request.declien.remarks')}}',
            type: 'post',
            data: {
                'appointment_requests_id': appointment_requests_id,
                'appointment_requests_remarks': appointment_requests_remarks,
            },
            beforeSend: function () {
                $("#loading-image").show();
            },
        }).done( function(response) {
            $("#loading-image").hide();

            if (data.code == 500) {
                toastr["error"]('Something went wrong. Please try again.');
            } else {
                
                $("#declien-remarks").modal("hide");
                toastr["success"](response.message);

                setTimeout(function() {
                    location.reload();
                }, 1500);
            }
        }).fail(function(errObj) {
            $("#loading-image").hide();
            toastr['error']('Something went wrong. Please try again.');
            location.reload();
        });
    });

    $(document).on('click', '.submit-reply-email', function (e) {
        e.preventDefault();

        var quickemailSubject = $("#quickemailSubject").val();
        var formattedContent = $("#formattedContent").html();
        var replyMessage = $("#reply-message").val();
        var receiver_email = $('#receiver_email').val();
        var reply_email_id= $('#reply_email_id').val();

        var pass_history = $('#pass_history').prop('checked');
        if (pass_history) {
          pass_history = 1;
        } else {
          pass_history = 0;
        }

            $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: '/email/replyAllMail',
            type: 'post',
            data: {
            'receiver_email': receiver_email,
            'subject': quickemailSubject,
            'message': replyMessage,
            'reply_email_id': reply_email_id,
            'pass_history': pass_history
            },
            beforeSend: function () {
                $("#loading-image").show();
            },
        }).done( function(response) {
            $("#loading-image").hide();
            toastr['success'](response.message);
        }).fail(function(errObj) {
            $("#loading-image").hide();
            toastr['error'](response.errors[0]);
        });
    });
    
    $(document).on("keyup", ".app-search-table", function (e) {
        var keyword = $(this).val();
        table = document.getElementById("database-table-list1");
        tr = table.getElementsByTagName("tr");
        // Loop through all table rows, and hide those who don't match the search query
        for (i = 0; i < tr.length; i++) {
            td = tr[i].getElementsByTagName("td")[1];
            if (td) {
                txtValue = td.textContent || td.innerText;
                if (txtValue.indexOf(keyword) > -1) {
                    tr[i].style.display = "";
                } else {
                    tr[i].style.display = "none";
                }
            }
        }
    });

    $(document).on("click", ".btn-task-search-menu", function (e) {
        var keyword = $('.task-search-table').val();
        var task_user_id = $('#task_user_id').val();
        var selectedValues = [];

        $.ajax({
            url: '{{route('task.module.search')}}',
            type: 'GET',
            data: {
                term: keyword,
                selected_user: task_user_id,
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            // dataType: 'json',
            beforeSend: function () {
                $("#loading-image").show();
            },
            success: function (response) {
                $("#loading-image").hide();
                $('.show-search-task-list').html(response);
            },
            error: function () {
                $("#loading-image").hide();
                toastr["Error"]("An error occured!");
            }
        });
    });

    $(document).on("click", ".btn-vendor-search-flowchart", function (e) {        
        var fc_vendor_id = $('#fc_vendor_id').val();

        if(fc_vendor_id>0){

            $.ajax({
                url: '{{route('vendors.flowcharts.search')}}',
                type: 'POST',
                data: {
                    vendor_id: fc_vendor_id,
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                // dataType: 'json',
                beforeSend: function () {
                    $("#loading-image").show();
                },
                success: function (response) {
                    $("#loading-image").hide();
                    $('.show-vendor-search-flowchart-list').html(response);
                },
                error: function () {
                    $("#loading-image").hide();
                    toastr["Error"]("An error occured!");
                }
            });
        } else {
            alert('Please select vendor.')
        }
    });

    $(document).on("click", ".btn-vendor-search-qa", function (e) {        
        var qa_vendor_id = $('#qa_vendor_id').val();

        if(qa_vendor_id>0){

            $.ajax({
                url: '{{route('vendors.qa.search')}}',
                type: 'POST',
                data: {
                    vendor_id: qa_vendor_id,
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                // dataType: 'json',
                beforeSend: function () {
                    $("#loading-image").show();
                },
                success: function (response) {
                    $("#loading-image").hide();
                    $('.show-vendor-search-qa-list').html(response);
                },
                error: function () {
                    $("#loading-image").hide();
                    toastr["Error"]("An error occured!");
                }
            });
        } else {
            alert('Please select vendor.')
        }
    });

    $(document).on("click", ".btn-vendor-search-rqa", function (e) {        
        var rqa_vendor_id = $('#rqa_vendor_id').val();

        if(rqa_vendor_id>0){

            $.ajax({
                url: '{{route('vendors.rqa.search')}}',
                type: 'POST',
                data: {
                    vendor_id: rqa_vendor_id,
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                // dataType: 'json',
                beforeSend: function () {
                    $("#loading-image").show();
                },
                success: function (response) {
                    $("#loading-image").hide();
                    $('.show-vendor-search-rqa-list').html(response);
                },
                error: function () {
                    $("#loading-image").hide();
                    toastr["Error"]("An error occured!");
                }
            });
        } else {
            alert('Please select vendor.')
        }
    });

    $(document).on("click", ".btn-dev-task-search-menu", function (e) {
        var keyword = $('.dev-task-search-table').val();
        var quicktask_user_id = $('#quicktask_user_id').val();
        var selectedValues = [];

        $.ajax({
            url: '{{route('devtask.module.search')}}',
            type: 'GET',
            data: {
                subject: keyword,
                selected_user: quicktask_user_id,
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            // dataType: 'json',
            beforeSend: function () {
                $("#loading-image").show();
            },
            success: function (response) {
                $("#loading-image").hide();
                $('.show-search-dev-task-list').html(response);
            },
            error: function () {
                $("#loading-image").hide();
                toastr["Error"]("An error occured!");
            }
        });
    });

    $(document).on('change', '.assign-user-menu', function () {
        let id = $(this).attr('data-id');
        let userId = $(this).val();

        if (userId == '') {
            return;
        }

        $.ajax({
            url: "{{ action([\App\Http\Controllers\DevelopmentController::class, 'assignUser']) }}",
            data: {
                assigned_to: userId,
                issue_id: id
            },
            success: function () {
                toastr["success"]("User assigned successfully!", "Message")
            },
            error: function (error) {
                toastr["error"](error.responseJSON.message, "Message")

            }
        });

    });

    $(document).on('click', '.expand-row-msg-menu', function () {
        var id = $(this).data('id');
        var full = '.expand-row-msg-menu .td-full-container-' + id;
        var mini = '.expand-row-msg-menu .td-mini-container-' + id;
        $(full).toggleClass('hidden');
        $(mini).toggleClass('hidden');
    });

    $(document).on('click', '.send-message-open-quick-menu', function (event) {
        var textBox = $(this).closest(".communication-td").find(".send-message-textbox");
        var sendToStr = $(this).closest(".communication-td").next().find(".send-message-number").val();
        let issueId = textBox.attr('data-id');
        let message = textBox.val();
        if (message == '') {
            return;
        }

        let self = textBox;

        $.ajax({
            url: "{{ action([\App\Http\Controllers\WhatsAppController::class, 'sendMessage'], 'issue') }}",
            type: 'POST',
            data: {
                "issue_id": issueId,
                "message": message,
                "sendTo": sendToStr,
                "_token": "{{ csrf_token() }}",
                "status": 2
            },
            dataType: "json",
            success: function (response) {
                toastr["success"]("Message sent successfully!", "Message");
                $('#message_list_' + issueId).append('<li>' + response.message.created_at + " : " +
                    response.message.message + '</li>');
                $(self).removeAttr('disabled');
                $(self).val('');
            },
            beforeSend: function () {
                $(self).attr('disabled', true);
            },
            error: function () {
                alert('There was an error sending the message...');
                $(self).removeAttr('disabled', true);
            }
        });
    });

    $(document).on("click", ".btn-file-upload-menu", function() {
        var $this = $(this);
        var task_id = $this.data("id");
        $("#menu-file-upload-area-section").modal("show");
        $("#hidden-task-id").val(task_id);
        $("#loading-image").hide();
    });

    $(document).on('change', '.menu-task-assign-user', function() {
        let id = $(this).attr('data-id');
        let userId = $(this).val();
        if (userId == '') {
            return;
        }
        $.ajax({
            url: "{{route('task.AssignTaskToUser')}}",
            data: {
                user_id: userId,
                issue_id: id
            },
            success: function() {
                toastr["success"]("User assigned successfully!", "Message")
            },
            error: function(error) {
                toastr["error"](error.responseJSON.message, "Message")
            }
        });
    });

    $(document).on("click", ".menu-upload-document-btn", function () {
        var id = $(this).data("id");
        $("#menu-upload-document-modal").find("#hidden-identifier").val(id);
        $("#menu-upload-document-modal").modal("show");
    });

    $(document).on('click', '.menu-show-user-history', function() {
        var issueId = $(this).data('id');
        $('#user_history_div table tbody').html('');
        $.ajax({
            url: "{{ route('task/user/history') }}",
            data: {
                id: issueId
            },
            success: function(data) {
                $.each(data.users, function(i, item) {
                    $('#user_history_div table tbody').append(
                        '<tr>\
                                <td>' + moment(item['created_at']).format('DD/MM/YYYY') + '</td>\
                                    <td>' + ((item['user_type'] != null) ? item['user_type'] : '-') + '</td>\
                                    <td>' + ((item['old_name'] != null) ? item['old_name'] : '-') + '</td>\
                                    <td>' + ((item['new_name'] != null) ? item['new_name'] : '-') + '</td>\
                                    <td>' + item['updated_by'] + '</td>\
                                </tr>'
                    );
                    $("#menu_user_history_modal").css('z-index','-1');
                });
            }
        });
        $('#menu_user_history_modal').modal('show');
    });

    $(document).on('click', '.menu-send-message', function() {
        var thiss = $(this);
        var data = new FormData();
        var task_id = $(this).data('taskid');
        if ($(this).hasClass("onpriority")) {
            var message = $('#getMsgPopup' + task_id).val();
        } else {
            var message = $('#getMsg' + task_id).val();
        }
        if (message != "") {
            $("#message_confirm_text").html(message);
            $("#confirm_task_id").val(task_id);
            $("#confirm_message").val(message);
            $("#confirm_status").val(1);
            $("#menu_confirmMessageModal").modal();
        }
    });

    $(document).ready(function() {
        // Change category on create page logic
        $('.add_todo_category').on('change', function(){
            var category_id = $('.add_todo_category').find(':selected').val();
            if( category_id != '' && category_id == '-1'){
                $('.othercat').show();
            } else{
                $('.othercat').hide();
            }
        });
    });

    $(document).on("click", ".submit-todolist-button", function(e) {
        e.preventDefault();
        var $this = $(this);
        var formData = new FormData($this.closest("form")[0]);
        var $form = $(this).closest("form");
        
        var title = $(this).parents('#todolist-request-model').find('.add_todo_title').val();
        var subject = $(this).parents('#todolist-request-model').find('.add_todo_subject').val();
        var category = $('.add_todo_category').find(':selected').val();
        var status = $('.add_todo_status').find(':selected').val();
        var date = $(this).parents('#todolist-request-model').find('.add_todo_date').val();
        var remark = $(this).parents('#todolist-request-model').find('.add_todo_remark').val();
        var other = $(this).parents('#todolist-request-model').find('.add_todo_other').val();

        $('.text-danger').html('');
        if(title == '') {
            $('.add_todo_title').next().text("Please enter the title");
            return false;
        }

        if(subject == '') {
            $('.add_todo_subject').next().text("Please enter the subject");
            return false;
        }

        if(category == '') {
            $('.add_todo_category').next().text("Please select the category");
            return false;
        } else if(category == '-1') {
            if(other == '') {
                $('.add_todo_other').next().text("Please add new category");
                return false;
            }
        }

        //-1

        if(status == '') {
            $('.add_todo_status').next().text("Please select the status");
            return false;
        }

        if(date == '') {
            $('.text-danger-date').text("Please select the date");
            return false;
        } else {
            $('.text-danger-date').text(" ");
        }

        $.ajax({
            url: '{{ route('todolist.ajax_store') }}',
            type: 'POST',
            cache: false,
            contentType: false,
            processData: false,
            data: formData,
            dataType: "json",
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
            beforeSend: function() {
                $("#loading-image-preview").show();
            }
        }).done(function(data) {
            $("#loading-image-preview").hide();
            if (data.code == 500) {
                toastr["error"](data.message);
            } else {
                $('.othercat').hide();
                $form[0].reset();
                $("#todolist-request-model").modal("hide");
                toastr["success"]("Your Todo List has been created!");

                setTimeout(function() {
                    location.reload();
                }, 2500);
                
            }
        }).fail(function(jqXHR, ajaxOptions, thrownError) {
            toastr["error"](jqXHR.responseJSON.message);
            $("#loading-image").hide();
        });
    });

    $(document).on('click', '.menu-confirm-messge-button', function() {
        var thiss = $(this);
        var data = new FormData();
        var task_id = $("#confirm_task_id").val();
        var message = $("#confirm_message").val();
        var status = $("#confirm_status").val();
        data.append("task_id", task_id);
        data.append("message", message);
        data.append("status", status);
        var checkedValue = [];
        var i = 0;
        $('.send_message_recepients:checked').each(function() {
            checkedValue[i++] = $(this).val();
        });
        data.append("send_message_recepients", checkedValue);
        
        if (message.length > 0) {
            if (!$(thiss).is(':disabled')) {
                $.ajax({
                    
                    url: "{{ route('whatsapp.send','task')}}",
                    type: 'POST',
                    "dataType": 'json', // what to expect back from the PHP script, if anything
                    "cache": false,
                    "contentType": false,
                    "processData": false,
                    "data": data,
                    beforeSend: function() {
                        $(thiss).attr('disabled', true);
                    }
                }).done(function(response) {
                    $(thiss).siblings('input').val('');
                    $('#getMsg' + task_id).val('');
                    $('#menu_confirmMessageModal').modal('hide');
                    toastr["success"]("Message sent successfully!", "Message");
                    if (cached_suggestions) {
                        suggestions = JSON.parse(cached_suggestions);
                        if (suggestions.length == 10) {
                            suggestions.push(message);
                            suggestions.splice(0, 1);
                        } else {
                            suggestions.push(message);
                        }
                        localStorage['message_suggestions'] = JSON.stringify(suggestions);
                        cached_suggestions = localStorage['message_suggestions'];
                    } else {
                        suggestions.push(message);
                        localStorage['message_suggestions'] = JSON.stringify(suggestions);
                        cached_suggestions = localStorage['message_suggestions'];
                    }
                    $(thiss).attr('disabled', false);
                }).fail(function(errObj) {
                    $('#menu_confirmMessageModal').modal('hide');
                    $(thiss).attr('disabled', false);
                    alert("Could not send message");
                });
            }
        } else {
            alert('Please enter a message first');
        }
    });

    $(document).on("submit", "#menu-upload-task-documents", function (e) {
        e.preventDefault();
        var form = $(this);
        var postData = new FormData(form[0]);
        $.ajax({
            method: "post",
            url: "{{ action([\App\Http\Controllers\DevelopmentController::class, 'uploadDocument']) }}",
            data: postData,
            processData: false,
            contentType: false,
            dataType: "json",
            success: function (response) {
                if (response.code == 200) {
                    toastr["success"]("Status updated!", "Message")
                    $("#menu-upload-document-modal").modal("hide");
                } else {
                    toastr["error"](response.error, "Message");
                }
            }
        });
    });

    $(document).on("click", ".menu-list-document-btn", function () {
        var id = $(this).data("id");
        $.ajax({
            method: "GET",
            url: "{{ action([\App\Http\Controllers\DevelopmentController::class, 'getDocument']) }}",
            data: {
                id: id
            },
            dataType: "json",
            success: function (response) {
                if (response.code == 200) {
                    $("#menu-blank-modal").find(".modal-title").html("Document List");
                    $("#menu-blank-modal").find(".modal-body").html(response.data);
                    $("#menu-blank-modal").modal("show");
                } else {
                    toastr["error"](response.error, "Message");
                }
            }
        });
    });

    $(document).on("click", ".menu-btn-save-documents", function(e) {
        e.preventDefault();
        var $this = $(this);
        var formData = new FormData($this.closest("form")[0]);
        $.ajax({
            url: '/task/save-documents',
            type: 'POST',
            enctype: 'multipart/form-data',
            cache: false,
            contentType: false,
            processData: false,
            data: formData,
            dataType: "json",
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
            beforeSend: function() {
                $("#loading-image").show();
            }
        }).done(function(data) {
            $("#loading-image").hide();
            if (data.code == 500) {
                toastr["error"](data.message);
            } else {
                toastr["success"]("Document uploaded successfully");
                //location.reload();
            }
        }).fail(function(jqXHR, ajaxOptions, thrownError) {
            toastr["error"](jqXHR.responseJSON.message);
            $("#loading-image").hide();
        });
    });

    $(document).on('change', '.choose-username', function() {
        var val = $(this).val();
        var db =$('.choose-db').val();
        $('.app-database-user-id').val(val);
        $('.btn-database-add').attr('data-id',val);
        $('.btn-delete-database-access').attr('data-id',val);
        $('.btn-delete-database-access').attr('data-connection',db);
        $('.btn-assign-permission').attr('data-id',val);
        var database_user_id = val;
        var url = '{{ route("user-management.get-database", ":id") }}';
        url = url.replace(':id', database_user_id);

        $.ajax({
            url: url ,
            type: 'GET',
            data: {
                id: database_user_id,
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            // dataType: 'json',
            beforeSend: function() {
                $("#loading-image").show();
            },
            success: function(response) {
                $("#loading-image").hide();
                if (response.code == 200) {
                    $('.database_password').val(response.data.password);
                    console.log(response.data.password);
                    if(response.data.password)
                    {
                        $('.btn-delete-database-access').removeClass('d-none');
                    }else{
                        $('.btn-delete-database-access').addClass('d-none');
                    }
                    var aa = '';
                    $('.menu_tbody').html('');
                    $.each(response.data.tables, function(i, record) {
                        var checkvalue = '';
                        if(record.checked)
                        {
                            checkvalue = 'checked';
                        }

                        aa += '<tr role="row"><td><input type="checkbox" name="tables[]" value='+record.table+' '+checkvalue+'></td><td>'+record.table+'</td></tr>';
                    });
                    $('.menu_tbody').html(aa);
                } else {
                    toastr['error'](response.message, 'error');
                }
            },
            error: function() {
                $("#loading-image").hide();
                toastr["Error"]("An error occured!");
            }
        });

    })

    $(document).on('change', '.knowledge_base', function() {
        var val = $(this).val();
        if ($(this).val() == "chapter" || $(this).val() == "page") {
            $(this).parents('.add_sop_modal').find('.knowledge_base_book').removeAttr('hidden');
        } else {
            $(this).parents('.add_sop_modal').find('.knowledge_base_book').attr('hidden', true).val('');
        }
    })

    $(document).on('change', '.knowledge_base_book', function() {
        var val = $(this).val();
        if (val.length > 0) {
            $(this).parents('#createShortcutForm').find('.books_error').text('');
        }
    })

    $(document).on('click', '.create_shortcut_submit', function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        var formdata = $('#createShortcutForm').serialize();
        var val = $(this).parents('#createShortcutForm').find('.knowledge_base').val();
        var chatID = $(this).parents('#createShortcutForm').find('[name="chat_message_id"]').val();
        var name = $(this).parents('#createShortcutForm').find('[name="name"]').val();
        var category = $(this).parents('#createShortcutForm').find('[name="category"]').val();
        var content = $(this).parents('#createShortcutForm').find('[name="description"]').text();
        var book_name = $(this).parents('#createShortcutForm').find('.knowledge_base_book').val();

        if($('.sop_drop_down').find(':selected').val()=='code_shortcut'){
            $.ajax({
                type: "POST",
                url: "{{ route('shortcut.code.create') }}",
                data: formdata,
                success: function(response) {
                    toastr.success('code Shortcut Added Successfully');
                    $('#Create-Sop-Shortcut').modal('hide');
                    $('#createShortcutForm')[0].reset();
                }
            })
        }

        if($('.sop_drop_down').find(':selected').val()=='sop'){
            if (val.length === 0) {
                $.ajax({
                    type: "POST",
                    url: "{{ route('shortcut.sop.create') }}",
                    data: formdata,
                    success: function(response) {
                        toastr.success('Sop Added Successfully');
                        $('#Create-Sop-Shortcut').modal('hide');
                    }
                })
            }
        }

        if($('.sop_drop_down').find(':selected').val()=='knowledge_base'){
            if (val == "book") {
                $.ajax({
                    type: "POST",
                    url: `/kb/books`,
                    data: formdata,
                    success: function(response) {
                        toastr.success('Book Added Successfully');
                        $('#Create-Sop-Shortcut').modal('hide');
                    }
                })
            }
            if (val == "chapter") {
                if (book_name.length == 0) {
                    $(this).parents('#createShortcutForm').find('.books_error').text('Please select Book');
                    return;
                }
                $.ajax({
                    type: "POST",
                    url: `/kb/books/${book_name}/create-chapter`,
                    data: formdata,
                    success: function(response) {
                        toastr.success('Chapter Added Successfully');
                        $('#Create-Sop-Shortcut').modal('hide');
                    }
                })
            }
            if (val == "page") {
                if (book_name.length == 0) {
                    $(this).parents('#createShortcutForm').find('.books_error').text('Please select Book');
                    return;
                }
                $.ajax({
                    type: "get",
                    url: `kb/books/${book_name}/create-page`,
                    data: formdata,
                    success: function(response) {
                        console.log(response, '======')
                        toastr.success('Page Added Successfully');
                        $('#Create-Sop-Shortcut').modal('hide');
                    }
                })
            }
            if (val == "shelf") {
                $.ajax({
                    type: "POST",
                    url: `/kb/shelves/${name}/add`,
                    data: formdata,
                    success: function(response) {
                        toastr.success('Bookshelf Added Successfully');
                        $('#Create-Sop-Shortcut').modal('hide');
                    }
                })
            }
        }
    })

    $(document).on('click', '.system-request', function() {
        loadUsersList();
    })
    $(document).on("click", ".addIp", function(e) {
        e.preventDefault();
        if ($('input[name="add-ip"]').val() != '') {
            if ($('#ipusers').val() === '') {
                alert('Please select User OR Other from list.');
            }
            else if($('#ipusers').val() === 'other' && $('input[name="other_user_name"]').val()==='')
            {
                alert('Please enter other name.');
            }
            else{
                $.ajax({
                url: '/users/add-system-ip',
                type: 'GET',
                data: {
                    _token: "{{ csrf_token() }}",
                    ip: $('input[name="add-ip"]').val(),
                    user_id: $('#ipusers').val(),
                    other_user_name: $('input[name="other_user_name"]').val(),
                    comment: $('input[name="ip_comment"]').val()
                },
                dataType: 'json',
                beforeSend: function() {
                    $("#loading-image").show();
                },
                success: function(result) {
                    $("#loading-image").hide();
                    toastr["success"]("IP added successfully");
                    location.reload();
                },
                error: function() {
                    $("#loading-image").hide();
                    toastr["Error"]("An error occured!");
                }
            });
            }

        } else {
            alert('please enter IP');
        }
    });

    $(document).on("click", ".btn-database-add", function(e) {
        e.preventDefault();
        // var ele = this;
        var connection = $('.choose-db').val();
        var username = $('.choose-username').find(':selected').attr('data-name');
         username = username.replace(/ /g,"_").toLowerCase();
        var password = $('.database_password').val();
        var database_user_id = $(this).data("id");
        var url = '{{ route("user-management.create-database", ":id") }}';
        url = url.replace(':id', database_user_id);

        $.ajax({
                url: url ,
                type: 'POST',
                data: {
                    database_user_id: database_user_id,
                    connection: connection,
                    username: username,
                    password: password,
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                // dataType: 'json',
                beforeSend: function() {
                    $("#loading-image").show();
                },
                success: function(response) {
                    $("#loading-image").hide();
                    if (response.code == 200) {
                        toastr['success'](response.message, 'success');
                    } else {
                        toastr['error'](response.message, 'error');
                    }
                },
                error: function() {
                    $("#loading-image").hide();
                    toastr["Error"]("An error occured!");
                }
            });
    });

    $(document).on("click", ".btn-assign-permission", function(e) {
        e.preventDefault();
        // var ele = this;
        var connection = $('.choose-db').val();
        var assign_permission = $('.assign-permission-type').find(':selected').val();
        var search = $('.app-search-table').val();
        var tables = $('.database_password').val();
        var checked = []
        $("input[name='tables[]']:checked").each(function ()
        {
            checked.push($(this).val());
        });

        var database_user_id = $('#database-user-id').val();
        if(database_user_id == '')
        {
            toastr['error']('Please select the user first', 'error');
            return false
        }
        var url = '{{ route("user-management.assign-database-table", ":id") }}';
        url = url.replace(':id', database_user_id);

        $.ajax({
            url: url ,
            type: 'POST',
            data: {
                database_user_id: database_user_id,
                connection: connection,
                search: search,
                assign_permission: assign_permission,
                tables: checked,
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            // dataType: 'json',
            beforeSend: function() {
                $("#loading-image").show();
            },
            success: function(response) {
                $("#loading-image").hide();
                if (response.code == 200) {
                    toastr['success'](response.message, 'success');
                } else {
                    toastr['error'](response.message, 'error');
                }
            },
            error: function() {
                $("#loading-image").hide();
                toastr["Error"]("An error occured!");
            }
        });
    });

    $(document).on("click", ".btn-delete-database-access", function(e) {
        e.preventDefault();
        if (!confirm("Are you sure you want to remove access for this user?")) {
            return false;
        } else {
            var connection = $('.choose-db').val();
            var database_user_id = $('#database-user-id').val();
            if (database_user_id == '') {
                toastr['error']('Please select the user first', 'error');
                return false
            }
            var url = '{{ route("user-management.delete-database-access", ":id") }}';
            url = url.replace(':id', database_user_id);

            $.ajax({
                url: url,
                type: 'POST',
                data: {
                    connection: connection,
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                // dataType: 'json',
                beforeSend: function () {
                    $("#loading-image").show();
                },
                success: function (response) {
                    $("#loading-image").hide();
                    if (response.code == 200) {
                        toastr['success'](response.message, 'success');
                        $("#menu-create-database-model").modal("hide");
                    } else {
                        toastr['error'](response.message, 'error');
                        $("#menu-create-database-model").modal("hide");
                    }
                },
                error: function () {
                    $("#loading-image").hide();
                    toastr["Error"]("An error occured!");
                }
            });
        }
    });

    $(document).ready(function() {
        $('#ipusers').change(function() {
            var selected = $(this).val();
            if (selected == 'other') {
                $('#other_user_name').show();
            } else {
                $('#other_user_name').hide();
            }
        });
    });
    $(document).on("click", ".deleteIp", function(e) {
        e.preventDefault();
        var btn = $(this);
        $.ajax({
            url: '/users/delete-system-ip',
            type: 'GET',
            data: {
                _token: "{{ csrf_token() }}",
                usersystemid: $(this).data('usersystemid')
            },
            dataType: 'json',
            beforeSend: function() {
                $("#loading-image").show();
            },
            success: function(result) {
                btn.parents('tr').remove();
                $("#loading-image").hide();
                toastr["success"]("IP Deteted successfully");
            },
            error: function() {
                $("#loading-image").hide();
                toastr["Error"]("An error occured!");
            }
        });
    });
    $(document).on("click", ".bulkDeleteIp", function(e) {
        e.preventDefault();
        var btn = $(this);
        if(confirm('Are you sure you want to perform this Action?') == false)
        {
            return false;
        }
        $.ajax({
            url: '/users/bulk-delete-system-ip',
            type: 'GET',
            data: {
                _token: "{{ csrf_token() }}",
            },
            dataType: 'json',
            beforeSend: function() {
                $("#loading-image").show();
            },
            success: function(result) {
                $("#userAllIps").empty();
                $("#loading-image").hide();
                toastr["success"]("IPs Deteted successfully");
            },
            error: function() {
                $("#loading-image").hide();
                toastr["Error"]("An error occured!");
            }
        });
    });
    function loadUsersList() {
        var t = "";
        var ip = "";
        $.ajax({
            url: '{{ route("get-user-list") }}',
            type: 'GET',
            data: {
                _token: "{{ csrf_token() }}",
            },
            dataType: 'json',
            success: function(result) {
               
                $.each(result.usersystemips, function(k, v) {
                    ip += '<tr>';
                    ip += '<td> ' + v.index_txt + ' </td>';
                    ip += '<td> ' + v.ip + '</td>';
                    ip += '<td>' +( (v.user!=null) ? v.user.name : v.other_user_name )+ '</td>';
                    ip += '<td> ' + v.source + '</td>';
                    ip += '<td>' + v.notes + '</td>';
                    ip += '<td> ' + v.command + ' </td>';
                    ip += '<td> ' + v.status + ' </td>';
                    ip += '<td> ' + v.message + ' </td>';
                    ip += '<td><button class="btn-warning btn deleteIp" data-usersystemid="' + v
                        .id + '">Delete</button></td>';
                    ip += '</tr>';
                });
                $("#userAllIps").html(ip);
            },
            error: function() {
                // alert('fail');
            }
        });
    }
    </script>

    @stack('scripts')

    <script>
    $(document).ready(function() {
        
        var autoRefresh = $.cookie('auto_refresh');
        if (typeof autoRefresh == "undefined" || autoRefresh == 1) {
            $(".auto-refresh-run-btn").attr("title", "Stop Auto Refresh");
            $(".auto-refresh-run-btn").find("i").removeClass("refresh-btn-stop").addClass("refresh-btn-start");
        } else {
            $(".auto-refresh-run-btn").attr("title", "Start Auto Refresh");
            $(".auto-refresh-run-btn").find("i").removeClass("refresh-btn-start").addClass("refresh-btn-stop");
        }
        //auto-refresh-run-btn

        $(document).on("click", ".auto-refresh-run-btn", function() {
            let autoRefresh = $.cookie('auto_refresh');
            if (autoRefresh == 0) {
                alert("Auto refresh has been enable for this page");
                $.cookie('auto_refresh', '1', {
                    path: '/{{ Request::path() }}'
                });
                $(".auto-refresh-run-btn").find("i").removeClass("refresh-btn-stop").addClass(
                    "refresh-btn-start");
            } else {
                alert("Auto refresh has been disable for this page");
                $.cookie('auto_refresh', '0', {
                    path: '/{{ Request::path() }}'
                });
                $(".auto-refresh-run-btn").find("i").removeClass("refresh-btn-start").addClass(
                    "refresh-btn-stop");
            }
        });

        $('#editor-note-content').richText();
        $('#editor-instruction-content').richText();

        $('#editor-notes-content').richText(); //Purpose : Add Text content - DEVTASK-4289

        $('#notification-date').datetimepicker({
            format: 'YYYY-MM-DD'
        });

        $('#notification-time').datetimepicker({
            format: 'HH:mm'
        });

        $('#repeat_end').datetimepicker({
            format: 'YYYY-MM-DD'
        });

        $(".selectx-vendor").select2({
            tags: true
        });
        $(".selectx-users").select2({
            tags: true
        });
    });
    window.token = "{{ csrf_token() }}";

    var url = window.location;
    window.collectedData = [{
            type: 'key',
            data: ''
        },
        {
            type: 'mouse',
            data: []
        }
    ];

    $(document).keypress(function(event) {
        var x = event.charCode || event.keyCode; // Get the Unicode value
        var y = String.fromCharCode(x);
        collectedData[0].data += y;
    });

    // started for help button
    $('.help-button').on('click', function() {
        $('.help-button-wrapper').toggleClass('expanded');
        $('.page-notes-list-rt').toggleClass('dis-none');
    });

    $('.instruction-button').on('click', function() {
        $("#quick-instruction-modal").modal("show");
    });


        var stickyNotesUrl = "{{ route('stickyNotesCreate') }}";
        var stickyNotesPage = "{{ request()->fullUrl() }}";

        var x = `<div class='sticky_notes_container pageNotesModal' style=" padding: 10px; margin: 20px;">
            <div class="icon-check">
            <div class='check-icon' title='Save'><i class='fa fa-check'></i></div>
              <div class='close-icon' title='Close'><i class='fa fa-times'></i></div>
                </div>
                   Sticky Note
                   <hr>
                   <div class="text_box-select mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="Title">
                        Type
                      </label>
                      <select class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="custom-text" style=" width: 100%;">
                      <option value="notes">Notes</option>
                      <option value="todolist">To do List</option>
                      </select>
                    </div>
                    <div class="text_box-text mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="Title">
                        Title
                      </label>
                      <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="custom-text" type="text" placeholder="Title" style=" width: 100%;">
                    </div>
                    
                    <div class='text_box-textarea mb-4'>
                        <label>Notes</label></br>
                        <textarea rows='5' cols='27' class='notes custom-textarea' name='notes' data-url='${stickyNotesUrl}' data-page='${stickyNotesPage}' placeholder="Notes" style=" background: #fff; width:100%"></textarea>
                    </div>
                </div>`;

        $('.sticky-notes').on('click', function() {
            StickyBox();
        });

        
        var marginVar = 20;
       
        function StickyBox () {

             marginVar += 20;

            $(".sticknotes_content").draggable();
            $('#sticky_note_boxes').append(x);

              var lastStickyNote = $("#sticky_note_boxes .sticky_notes_container:last");

              lastStickyNote.css("margin", marginVar+"px"); 


                $(".sticky_notes_container").draggable();
                $('.close-icon').each(function(){
                    $('.close-icon').click(function() {
                        $(this).closest('.sticky_notes_container').remove();
                    });
                });
                
            }

            $(document).on("click", ".check-icon", function (event) {
                event.preventDefault();
                var textareaValue = $(this).parent().siblings('.text_box-textarea').find('textarea').val();
                var page = $(this).parent().siblings('.text_box-textarea').find('textarea').data('page');

                var title = $(this).parent().siblings('.text_box-text').find('input').val();

                var type = $(this).parent().siblings('.text_box-select').find('select').val();

                $.ajax({
                    url: '{{ route('stickyNotesCreate') }}',
                    method: 'POST',
                    data: {
                        value: textareaValue,
                        page: page,
                        title: title,
                        type: type,
                        _token: "{{ csrf_token() }}",
                    },
                    success: function(response) {
                    toastr['success'](response.message, 'success');
                    },
                    error: function(xhr, status, error) {
                        console.log('Save Error:', error);
                    }
                });
                $(this).closest('.sticky_notes_container').remove();
            });

    //START - Purpose : Open Modal - DEVTASK-4289
    $('.create_notes_btn').on('click', function() {
        $("#quick_notes_modal").modal("show");
    });

    $('.btn_save_notes').on('click', function(e) {
        e.preventDefault();
        var data = $('#editor-notes-content').val();

        if ($(data).text() == '') {
            toastr['error']('Note Is Required');
            return false;
        }


        var url = window.location.href;
        $.ajax({
            type: "POST",
            url: "{{ route('notesCreate') }}",
            data: {
                data: data,
                url: url,
                _token: "{{ csrf_token() }}",
            },
            dataType: "json",
            success: function(data) {
                if (data.code == 200) {
                    toastr['success'](data.message, 'success');
                    $("#quick_notes_modal").modal("hide");
                }

            },
            error: function(xhr, status, error) {

            }
        });
    });
    //END - DEVTASK-4289

    $('.notification-button').on('click', function() {
        $("#quick-user-event-notification-modal").modal("show");
    });

    $('.ParticipantsList').on('click', function() {
        $("#participants-list-modal").modal("show");
    });
    
    function viewParticipantsIcon(pageNumber = 1) {
        var button = document.querySelector('.btn.btn-xs.ParticipantsList'); 

            $.ajax({
                url: "{{route('list.all.participants')}}",
                type: 'GET',
                dataType: "json",
                data: {
                    page:pageNumber,
                },
                beforeSend: function() {
                $("#loading-image-preview").show();
            }
            }).done(function(response) {
                $('#participants-list-modal-html').empty().html(response.html);
                $('#participants-list-modal').modal('show');
                renderdomainPagination(response.data);
                $("#loading-image-preview").hide();
            }).fail(function(response) {
                $('.loading-image-preview').show();
                console.log(response);
            });
    }

    function renderdomainPagination(response) {
        var paginationContainer = $(".pagination-container-participation");
        var currentPage = response.current_page;
        var totalPages = response.last_page;
        var html = "";
        var maxVisiblePages = 10;

        if (totalPages > 1) {
            html += "<ul class='pagination'>";
            if (currentPage > 1) {
            html += "<li class='page-item'><a class='page-link' href='javascript:void(0);' onclick='changeParticipantsPage(" + (currentPage - 1) + ")'>Previous</a></li>";
            }
            var startPage = 1;
            var endPage = totalPages;

            if (totalPages > maxVisiblePages) {
            if (currentPage <= Math.ceil(maxVisiblePages / 2)) {
                endPage = maxVisiblePages;
            } else if (currentPage >= totalPages - Math.floor(maxVisiblePages / 2)) {
                startPage = totalPages - maxVisiblePages + 1;
            } else {
                startPage = currentPage - Math.floor(maxVisiblePages / 2);
                endPage = currentPage + Math.ceil(maxVisiblePages / 2) - 1;
            }

            if (startPage > 1) {
                html += "<li class='page-item'><a class='page-link' href='javascript:void(0);' onclick='changeParticipantsPage(1)'>1</a></li>";
                if (startPage > 2) {
                html += "<li class='page-item disabled'><span class='page-link'>...</span></li>";
                }
            }
            }

            for (var i = startPage; i <= endPage; i++) {
            html += "<li class='page-item " + (currentPage == i ? "active" : "") + "'><a class='page-link' href='javascript:void(0);' onclick='changeParticipantsPage(" + i + ")'>" + i + "</a></li>";
            }
            html += "</ul>";
        }
        paginationContainer.html(html);
    }

    function changeParticipantsPage(pageNumber) {
        viewParticipantsIcon(pageNumber);
    }


    $('select[name="repeat"]').on('change', function() {
        $(this).val() == 'weekly' ? $('#repeat_on').removeClass('hide') : $('#repeat_on').addClass('hide');
    });

    $('select[name="ends_on"]').on('change', function() {
        $(this).val() == 'on' ? $('#repeat_end_date').removeClass('hide') : $('#repeat_end_date').addClass(
            'hide');
    });

    $('select[name="repeat"]').on('change', function() {
        $(this).val().length > 0 ? $('#ends_on').removeClass('hide') : $('#ends_on').addClass('hide');
    });

    $(document).on("submit", "#notification-submit-form", function(e) {
        e.preventDefault();
        var $form = $(this).closest("form");
        $.ajax({
            type: "POST",
            url: $form.attr("action"),
            data: $form.serialize(),
            dataType: "json",
            success: function(data) {
                if (data.code == 200) {
                    $form[0].reset();
                    $("#quick-user-event-notification-modal").modal("hide");
                    toastr['success'](data.message, 'Message');
                } else {
                    toastr['error'](data.message, 'Message');
                }
            },
            error: function(xhr, status, error) {
                var errors = xhr.responseJSON;
                $.each(errors, function(key, val) {
                    $("#" + key + "_error").text(val[0]);
                });
            }
        });
    });

    //setup before functions
    var typingTimer; //timer identifier
    var doneTypingInterval = 5000; //time in ms, 5 second for example
    var $input = $('#editor-instruction-content');
    //on keyup, start the countdown
    $input.on('keyup', function() {
        clearTimeout(typingTimer);
        typingTimer = setTimeout(doneTyping, doneTypingInterval);
    });

    //on keydown, clear the countdown
    $input.on('keydown', function() {
        clearTimeout(typingTimer);
    });

    //user is "finished typing," do something
    function doneTyping() {
        //do something
    }

    // started for chat button
    // open chatbox now into popup

    var chatBoxOpen = false;

    $("#message-chat-data-box").on("click", function(e) {
        e.preventDefault();
        $("#quick-chatbox-window-modal").modal("show");
        chatBoxOpen = true;
        openChatBox(true);
    });

    $('#quick-chatbox-window-modal').on('hidden.bs.modal', function() {
        chatBoxOpen = false;
        openChatBox(false);
    });

    $('.chat_btn').on('click', function(e) {
        e.preventDefault();
        $("#quick-chatbox-window-modal").modal("show");
        chatBoxOpen = true;
        openChatBox(true);
    });

   

    var notesBtn = $(".save-user-notes");

    notesBtn.on("click", function(e) {
        e.preventDefault();
        var $form = $(this).closest("form");
        $.ajax({
            type: "POST",
            url: $form.attr("action"),
            data: {
                _token: window.token,
                note: $form.find("#note").val(),
                category_id: $form.find("#category_id").val(),
                url: "<?php echo request()->url() ?>"
            },
            dataType: "json",
            success: function(data) {
                if (data.code > 0) {
                    $form.find("#note").val("");
                    var listOfN = "<tr>";
                    listOfN += "<td scope='row'>" + data.notes.id + "</td>";
                    listOfN += "<td>" + data.notes.note + "</td>";
                    listOfN += "<td>" + data.notes.category_name + "</td>";
                    listOfN += "<td>" + data.notes.name + "</td>";
                    listOfN += "<td>" + data.notes.created_at + "</td>";
                    listOfN += "</tr>";

                    $(".page-notes-list").prepend(listOfN);
                }
            },
        });
    });

    @if(session()->has('encrpyt'))

    var inactivityTime = function() {
        var time;
        window.onload = resetTimer;
        // DOM Events
        document.onmousemove = resetTimer;
        document.onkeypress = resetTimer;

        function remove_key() {
            $.ajax({
                    url: "{{ route('encryption.forget.key') }}",
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        private: '1',
                        "_token": "{{ csrf_token() }}",
                    },
                })
                .done(function() {
                    alert('Please Insert Private Key');
                    location.reload();
                    console.log("success");
                })
                .fail(function() {
                    console.log("error");
                })
        }

        function resetTimer() {
            clearTimeout(time);
            time = setTimeout(remove_key, 1200000);
            // 1000 milliseconds = 1 second
        }
    };

    window.onload = function() {
        inactivityTime();
    }

    @endif

    var getNotesList = function() {
        
    }

    if ($(".help-button-wrapper").length > 0) {
        getNotesList();
    }


    
    @if(Auth::check())
    $(document).ready(function() {
        var url = window.location.href;
        var user_id = "{{ Auth::id() }}";
        user_name = "{{ Auth::user()->name }}";
        $.ajax({
            type: "POST",
            url: "/api/userLogs",
            data: {
                "_token": "{{ csrf_token() }}",
                "url": url,
                "user_id": user_id,
                "user_name": user_name
            },
            dataType: "json",
            success: function(message) {}
        });
    });
    @endif
    </script>
    @if ( !empty($_SERVER['HTTP_HOST']) && !empty($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] != "127.0.0.1" &&
    !stristr($_SERVER['HTTP_HOST'], '.mac') )
    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ $account_id }}"></script>
    <script>
    window.dataLayer = window.dataLayer || [];

    function gtag() {
        dataLayer.push(arguments);
    }

    gtag('js', new Date());
    //gtag('config', 'UA-171553493-1');
    </script>
    @endif
    <script>
    <?php
if (!\Auth::guest()) {
    $path = Request::path();
    $hasPage = \App\AutoRefreshPage::where("page", $path)->where("user_id", \Auth()->user()->id)->first();
    if ($hasPage) {
        ?>

    var idleTime = 0;

    function reloadPageFun() {
        idleTime = idleTime + 1000;
        var autoRefresh = $.cookie('auto_refresh');
        if (idleTime > <?php echo $hasPage->time * 1000; ?> && (typeof autoRefresh == "undefined" || autoRefresh ==
                1)) {
            window.location.reload();
        }
    }

    $(document).ready(function() {
        //Increment the idle time counter every minute.
        setInterval(function() {
            reloadPageFun()
        }, 3000);
        //Zero the idle timer on mouse movement.
        $(this).mousemove(function(e) {
            idleTime = 0;
        });
        $(this).keypress(function(e) {
            idleTime = 0;
        });
    });

    <?php }}?>

    function filterFunction() {
        var input, filter, ul, li, a, i;
        //getting search values
        input = document.getElementById("search");
        //String to upper for search
        filter = input.value.toUpperCase();

        //Getting Values From DOM
        a = document.querySelectorAll("#navbarSupportedContent a");
        //Class to open bar
        $("#search_li").addClass('open');
        //Close when search becomes zero
        if (a.length == 0) {
            $("#search_li").removeClass('open');
        }
        //Limiting Search Count
        count = 1;
        //Empty Existing Values
        $("#search_container").empty();

        //Getting All Values
        for (i = 0; i < a.length; i++) {
            txtValue = a[i].textContent || a[i].innerText;
            href = a[i].href;
            //If value doesnt have link
            if (href == "#" || href == '' || href.indexOf('#') > -1) {
                continue;
            }
            //Removing old search Result From DOM
            if (a[i].getAttribute('class') != null && a[i].getAttribute('class') != '') {
                if (a[i].getAttribute('class').indexOf('old_search') > -1) {
                    continue;
                }
            }
            //break when count goes above 30
            if (count > 30) {
                break;
            }
            //Pusing values to DOM Search Input
            if (txtValue.toUpperCase().indexOf(filter) > -1) {
                $("#search_container").append('<li class="nav-item dropdown dropdown-submenu"><a class="dropdown-item old_search" href=' + href + '>' + txtValue + '</a></li>');
                count++
            } else {

            }
        }

        if(filter.length == 0)
        {
            $("#search_container").empty();
            $("#search_li").removeClass('open');
        }
    }

    $(document).on('change', '#autoTranslate', function(e) {
        e.preventDefault();
        var customerId = $("input[name='message-id']").val();
        var language = $(".auto-translate").val();
        let self = $(this);
        $.ajax({
            url: "/customer/language-translate/" + customerId,
            method: "PUT",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                id: customerId,
                language: language
            },
            cache: true,
            success: function(res) {
                $('.selectedValue option[value="' + language + '"]').prop('selected', true);
                alert(res.success);
            }
        })
    });

    $(document).ready(function() {
        $(window).scroll(function() {
            if ($(this).scrollTop() > 50) {
                $('#back-to-top').fadeIn();
            } else {
                $('#back-to-top').fadeOut();
            }
        });
        // scroll body to 0px on click
        $('#back-to-top').click(function() {
            $('body,html').animate({
                scrollTop: 0
            }, 400);
            return false;
        });

        $('#sidebarCollapse').on('click', function() {
            $('#sidebar').toggleClass('active');
        });
        $(".select2-vendor").select2({});

        @php
            $route = request()->route()->getName();
        @endphp
        @if (in_array($route, ["development.issue.index", "task.index", "development.summarylist", "chatbot.messages.list"]))
            $(".show-estimate-time").click(function (e) {
                e.preventDefault();
                var tasktype = $(this).data('task');
                $.ajax({
                    type: "GET",
                    url: "{{route('task.estimate.list')}}",
                    
                    success: function (response) {
                        $("#showLatestEstimateTime").modal('show');
                        $("#showLatestEstimateTime .modal-table").html(response);
                    },
                    error: function (error) {

                    }

                });
            });
            $("#shortcut-estimate-search").select2();

            $("#shortcut-estimate-search").change(function (e) {
                e.preventDefault();
                let task_id = $(this).val();
                @if ($route == "development.issue.index")
                    var  tasktype = "DEVTASK";
                @else
                    var tasktype = "TASK";
                @endif
                $.ajax({
                    type: "GET",
                    url: "{{route('task.estimate.list')}}",
                    data: {
                        task: tasktype,
                        task_id
                    },
                    success: function (response) {
                        $("#showLatestEstimateTime").modal('show');
                        $("#showLatestEstimateTime .modal-table").html(response);
                    },
                    error: function (error) {
                        toastr["error"]("Error while fetching data.");
                    }

                });
            });
        @endif

        $('#showLatestEstimateTime').on('hide.bs.modal', function (e) {
            $("#modalTaskInformationUpdates .modal-body .row").show()
            $("#modalTaskInformationUpdates .modal-body hr").show()
        })


        $(document).on("click", ".approveEstimateFromshortcutButton", function (event) {
            event.preventDefault();
            let type = $(this).data('type');
            let task_id = $(this).data('task');
            let history_id = $(this).data('id');
            
            if (type == "TASK") {
                $.ajax({
                url: "/task/time/history/approve",
                type: "POST",
                data: {
                    _token: "{{csrf_token()}}",
                    approve_time: history_id,
                    developer_task_id: task_id,
                    user_id: 0
                },
                success: function (response) {
                    toastr["success"]("Successfully approved", "success");
                    $("#showLatestEstimateTime").modal("hide");
                },
                error: function (error) {
                    toastr["error"](error.responseJSON.message);
                },
                });
            } else {
                $.ajax({
                url: "/development/time/history/approve",
                type: "POST",
                data: {
                    _token: "{{csrf_token()}}",
                    approve_time: history_id,
                    developer_task_id: task_id,
                    user_id: 0
                },
                success: function (response) {
                    toastr["success"]("Successfully approved", "success");
                    $("#showLatestEstimateTime").modal("hide");
                },
                error: function (error) {
                    toastr["error"](error.responseJSON.message);
                },
                });
            }
        });
    });

    $(document).on('click', '.save-meeting-zoom', function() {
        var user_id = $('#quick_user_id').val();
        var meeting_topic = $('#quick_meeting_topic').val();
        var csrf_token = $('#quick_csrfToken').val();
        var meeting_url = $('#quick_meetingUrl').val();
        $.ajax({
            url: meeting_url,
            type: 'POST',
            success: function(response) {
                var status = response.success;
                if (false == status) {
                    toastr['error'](response.data.msg);
                } else {
                    $('#quick-zoomModal').modal('toggle');
                    window.open(response.data.meeting_link);
                    var html = '';
                    html += response.data.msg + '<br>';
                    html += 'Meeting URL: <a href="' + response.data.meeting_link +
                        '" target="_blank">' + response.data.meeting_link + '</a><br><br>';
                    html += '<a class="btn btn-primary" target="_blank" href="' + response.data
                        .start_meeting + '">Start Meeting</a>';
                    $('#qickZoomMeetingModal').modal('toggle');
                    $('.meeting_link').html(html);
                    toastr['success'](response.data.msg);
                }
            },
            data: {
                user_id: user_id,
                meeting_topic: meeting_topic,
                _token: csrf_token,
                user_type: "vendor"
            },
            beforeSend: function() {
                $(this).text('Loading...');
            }
        }).fail(function(response) {
            toastr['error'](response.responseJSON.message);

        });
    });

    $(document).on('change', '.task_for', function(e) {
        var getTask = $(this).val();
        if(getTask == 'time_doctor'){
            $('.time_doctor_project_section').show();
            $('.time_doctor_account_section').show();
        } else {
            $('.time_doctor_project_section').hide();
            $('.time_doctor_account_section').hide();
        }
    });

    $(document).on("click", ".save-task-window", function(e) {
        e.preventDefault();
        var form = $(this).closest("form");
        $.ajax({
            url: form.attr("action"),
            type: 'POST',
            data: form.serialize(),
            beforeSend: function() {
                $(this).text('Loading...');
            },
            success: function(response) {
                if (response.code == 200) {
                    form[0].reset();
                    toastr['success'](response.message);
                    $("#quick-create-task").modal("hide");
                    $("#auto-reply-popup").modal("hide");
                    $("#auto-reply-popup-form").trigger('reset');
                    location.reload();
                } else {
                    toastr['error'](response.message);
                }
            }
        }).fail(function(response) {
            toastr['error'](response.responseJSON.message);
        });
    });

    $('select.select2-discussion').select2({
        tags: true
    });

    $(document).on("change", ".type-on-change", function(e) {
        e.preventDefault();
        var task_type = $(this).val();
        console.log(task_type);
        if (task_type == 3) {
            $.ajax({
                url: '/task/get-discussion-subjects',
                type: 'GET',
                success: function(response) {
                    $('select.select2-discussion').select2({
                        tags: true
                    });
                    var option = '<option value="" >Select</option>';
                    $.each(response.discussion_subjects, function(i, item) {
                        console.log(item);

                        option = option + '<option value="' + i + '">' + item + '</option>';
                    });
                    $('.add-discussion-subjects').html(option);
                }
            }).fail(function(response) {
                toastr['error'](response.responseJSON.message);
            });
        } else {
            // $('select.select2-discussion').select2({tags: true});
            $("select.select2-discussion").empty().trigger('change');
        }


    });

    $(document).on('change', '#keyword_category', function() {
        console.log("inside");
        if ($(this).val() != "") {
            var category_id = $(this).val();
            var store_website_id = $('#live_selected_customer_store').val();
            $.ajax({
                url: "{{ url('get-store-wise-replies') }}" + '/' + category_id + '/' + store_website_id,
                type: 'GET',
                dataType: 'json'
            }).done(function(data) {
                console.log(data);
                if (data.status == 1) {
                    $('#live_quick_replies').empty().append('<option value="">Quick Reply</option>');
                    var replies = data.data;
                    replies.forEach(function(reply) {
                        $('#live_quick_replies').append($('<option>', {
                            value: reply.reply,
                            text: reply.reply,
                            'data-id': reply.id
                        }));
                    });
                }
            });

        }
    });

    $('.quick_comment_add_live').on("click", function() {
        var textBox = $(".quick_comment_live").val();
        var quickCategory = $('#keyword_category').val();

        if (textBox == "") {
            alert("Please Enter New Quick Comment!!");
            return false;
        }

        if (quickCategory == "") {
            alert("Please Select Category!!");
            return false;
        }
        console.log("yes");

        $.ajax({
            type: 'POST',
            url: "{{ route('save-store-wise-reply') }}",
            dataType: 'json',
            data: {
                '_token': "{{ csrf_token() }}",
                'category_id': quickCategory,
                'reply': textBox,
                'store_website_id': $('#live_selected_customer_store').val()
            }
        }).done(function(data) {
            console.log(data);
            $(".live_quick_comment").val('');
            $('#live_quick_replies').append($('<option>', {
                value: data.data,
                text: data.data
            }));
        })
    });

    $('#live_quick_replies').on("change", function() {
        $('.type_msg').text($(this).val());
    });

    $(document).on('click', '.show_sku_long', function() {
        $(this).hide();
        var id = $(this).attr('data-id');
        $('#sku_small_string_' + id).hide();
        $('#sku_long_string_' + id).css({
            'display': 'block'
        });
    });

    $(document).on('click', '.show_prod_long', function() {
        $(this).hide();
        var id = $(this).attr('data-id');
        $('#prod_small_string_' + id).hide();
        $('#prod_long_string_' + id).css({
            'display': 'block'
        });
    });

    $(document).on('click', '.manual-payment-btn', function(e) {
        e.preventDefault();
        var thiss = $(this);
        var type = 'GET';
        $.ajax({
            url: '/voucher/manual-payment',
            type: type,
            beforeSend: function() {
                $("#loading-image").show();
            }
        }).done(function(response) {
            $("#loading-image").hide();
            $('#create-manual-payment').modal('show');
            $('#create-manual-payment-content').html(response);

            $('#date_of_payment').datetimepicker({
                format: 'YYYY-MM-DD'
            });
            $('.select-multiple').select2({
                width: '100%'
            });

            $(".currency-select2").select2({
                width: '100%',
                tags: true
            });
            $(".payment-method-select2").select2({
                width: '100%',
                tags: true
            });

        }).fail(function(errObj) {
            $("#loading-image").hide();
        });
    });

    $(document).on('click', '.manual-request-btn', function(e) {
        e.preventDefault();
        var thiss = $(this);
        var type = 'GET';
        $.ajax({
            url: '/voucher/payment/request',
            type: type,
            beforeSend: function() {
                $("#loading-image").show();
            }
        }).done(function(response) {
            $("#loading-image").hide();
            $('#create-manual-payment').modal('show');
            $('#create-manual-payment-content').html(response);

            $('#date_of_payment').datetimepicker({
                format: 'YYYY-MM-DD'
            });
            $('.select-multiple').select2({
                width: '100%'
            });

        }).fail(function(errObj) {
            $("#loading-image").hide();
        });
    });

    $(document).on('click','#repo_status_list',function(e){
        e.preventDefault();
        getPullRequestsForShortcut(true)
    });

    function getPullRequestsForShortcut(showModal = false) {
        $.ajax({
            type: "GET",
            url: "{{route('github.pr.request')}}",
            dataType:"json",
            beforeSend: function() {
                $("#loading-image-preview").show();
            }
        }).done(function (response) {
            $("#loading-image-preview").hide();
            $('#pull-request-alerts-modal-html').empty().html(response.tbody);
            if (showModal) {
                $('#pull-request-alerts-modal').modal('show');
            }
            if(response.count > 0) {
                $('.event-alert-badge').removeClass("hide");
            }
        }).fail(function (response) {
            $("#loading-image-preview").hide();
        });
    }

    function confirmMergeToMaster(branchName, url) {
        let result = confirm("Are you sure you want to merge " + branchName + " to master?");
        if (result) {
            window.location.href = url;
        }
    }

    $(document).on('click','#website_Off_status',function(e){
        e.preventDefault();
        $('#create-status-modal').modal('show');
        getWebsiteMonitorStatus(1);
    });

    function getWebsiteMonitorStatus(page) {
        var url = "/monitor-server/list?page=" + page

        $.ajax({
            type: "GET",
            url: url,
            dataType:"json",
            beforeSend:function(data){
                $('.ajax-loader').show();
            }
        }).done(function (response) {
            $('.ajax-loader').hide();
            var tableBody = $('#website-monitor-status-modal-html');
            tableBody.empty(); // Clear the table body
            // Loop through the data and populate the table rows
            $.each(response.data, function(index, item) {
                var row = $('<tr>');
                row.append($('<td>').text(item.server_id));
                row.append($('<td>').text(item.ip));
                tableBody.append(row);
            });
            var paginationLinks = $('#website-monitor-status-modal-table-paginationLinks');
            paginationLinks.empty(); // Clear the pagination links
            // Generate the pagination links manually
            var links = response.links;
            var currentPage = response.current_page;
            var lastPage = response.last_page;
            var pagination = $('<ul class="pagination"></ul>');
            // Previous page link
            if (currentPage > 1) {
                pagination.append('<li class="page-item"><a href="#" class="page-link" data-page="' + (currentPage - 1) + '">Previous</a></li>');
            }
            // Individual page links
            for (var i = 1; i <= lastPage; i++) {
                var activeClass = (i === currentPage) ? 'active' : '';
                pagination.append('<li class="page-item ' + activeClass + '"><a href="#" class="page-link" data-page="' + i + '">' + i + '</a></li>');
            }
            // Next page link
            if (currentPage < lastPage) {
                pagination.append('<li class="page-item"><a href="#" class="page-link" data-page="' + (currentPage + 1) + '">Next</a></li>');
            }
            paginationLinks.append(pagination);
            // Handle pagination link clicks
            paginationLinks.find('a').on('click', function(event) {
                event.preventDefault();
                var page = $(this).data('page');
                getWebsiteMonitorStatus(page);
            });
        }).fail(function (response) {
            $('.ajax-loader').hide();
            console.log(response);
        });
    }

    $(document).on('click','#live-laravel-logs',function(e){
        $.ajax({
            type: "GET",
            url: "{{route('logging.live.logs-summary')}}",
            dataType:"json",
            beforeSend:function(data){
                $('.ajax-loader').show();
            }
        }).done(function (response) {
            $('.ajax-loader').hide();
            $('#live-laravel-logs-summary-modal-html').empty().html(response.html);
            $('#live-laravel-logs-summary-modal').modal('show');
        }).fail(function (response) {
            $('.ajax-loader').hide();
            console.log(response);
        });
    });

    function getZabbixIssues(page) {
        var url = "/zabbix-webhook-data/issues-summary?page=" + page

        $.ajax({
            type: "GET",
            url: url,
            dataType:"json",
            beforeSend:function(data){
                $('.ajax-loader').show();
            }
        }).done(function (response) {
            $('.ajax-loader').hide();
            var tableBody = $('#zabbix-issues-summary-modal-html');
            tableBody.empty(); // Clear the table body
            // Loop through the data and populate the table rows
            $.each(response.data, function(index, item) {
                var row = $('<tr>');
                row.append($('<td>').text(item.subject));
                row.append($('<td>').text(item.short_message));
                row.append($('<td>').text(item.event_start));
                row.append($('<td>').text(item.event_name));
                row.append($('<td>').text(item.host));
                row.append($('<td>').text(item.severity));
                row.append($('<td>').text(item.short_operational_data));
                row.append($('<td>').text(item.event_id));
                // Add more table data cells as needed
                tableBody.append(row);
            });
            var paginationLinks = $('#zabbix-issues-summary-modal-table-paginationLinks');
            paginationLinks.empty(); // Clear the pagination links
            // Generate the pagination links manually
            var links = response.links;
            var currentPage = response.current_page;
            var lastPage = response.last_page;
            var pagination = $('<ul class="pagination"></ul>');
            // Previous page link
            if (currentPage > 1) {
                pagination.append('<li class="page-item"><a href="#" class="page-link" data-page="' + (currentPage - 1) + '">Previous</a></li>');
            }
            // Individual page links
            for (var i = 1; i <= lastPage; i++) {
                var activeClass = (i === currentPage) ? 'active' : '';
                pagination.append('<li class="page-item ' + activeClass + '"><a href="#" class="page-link" data-page="' + i + '">' + i + '</a></li>');
            }
            // Next page link
            if (currentPage < lastPage) {
                pagination.append('<li class="page-item"><a href="#" class="page-link" data-page="' + (currentPage + 1) + '">Next</a></li>');
            }
            paginationLinks.append(pagination);
            // Handle pagination link clicks
            paginationLinks.find('a').on('click', function(event) {
                event.preventDefault();
                var page = $(this).data('page');
                getZabbixIssues(page);
            });
        }).fail(function (response) {
            $('.ajax-loader').hide();
            console.log(response);
        });
    }

    $(document).on('click','#zabbix-issues',function(e){
        e.preventDefault();
        $('#zabbix-issues-summary-modal').modal('show');
        getZabbixIssues(1);
    });

    $(document).on('click','#create_event',function(e){
        e.preventDefault();     
        $('.select2').select2();
        $('#create-event-modal').modal('show');

        var days = ["sunday", "monday", "tuesday", "wednesday", "thursday", "friday", "saturday"];

        function updateDayRows(startDate, endDate) {
            $('.day-row').hide();

        var currentDate = new Date(startDate);

            while (currentDate <= endDate) {
                var currentDay = currentDate.getDay();
                $('.' + days[currentDay]).show();
                
                // Move to the next day
                currentDate.setDate(currentDate.getDate() + 1);
            }
        }

        function updateDayRowsWithEndDate(startDate, endDate) {
            $('.day-row').hide();
            var currentDay = startDate.getDay();
            var lastDay = endDate.getDay();

            for (var i = currentDay; i <= lastDay; i++) {
                $('.' + days[i]).show();
            }
        }

        function showAdditionalElements() {
            $('.day-row, .clockpicker').show();
        }

        $('#event-start-date, #event-end-date').on('change', function() {
            var startDate = new Date($('#event-start-date').val());
            var endDate = new Date($('#event-end-date').val());
            updateDayRows(startDate, endDate);
        });

        $('select[name="date_range_type"]').on('change', function() {
            if ($(this).val() == 'within') {
                $('#end-date-div').removeClass('hide');
                var startDate = new Date($('#event-start-date').val());
                var endDate = new Date($('#event-end-date').val());
                updateDayRowsWithEndDate(startDate, endDate);
            } else {
                $('#end-date-div').addClass('hide');
                var startDate = new Date($('#event-start-date').val());
                updateDayRows(startDate, startDate);
                showAdditionalElements(); // Hide additional elements
            }
        });

        // Initialize day rows based on current day
        var currentDay = new Date().getDay();
        $('.day-row').hide();
        $('.' + days[currentDay]).show();

        $('.clockpicker').on('change', function() {
            var startInput = $(this).closest('tr').find('.clockpicker[name$="[start_at]"]');
            var endInput = $(this).closest('tr').find('.clockpicker[name$="[end_at]"]');
            var selectedDuration = parseInt($('#event-duration').val()); // Get selected duration in minutes

            if (startInput.val()) {
                var start = moment(startInput.val(), 'HH:mm');
                // var duration = moment.duration(durationSelect.val(), 'minutes');
                var duration = moment.duration(selectedDuration, 'minutes');

                var end = start.clone().add(duration);

                endInput.val(end.format('HH:mm'));
            }
        });

        $('.clockpicker').clockpicker({
            autoclose: true,
            doneText: 'Done',
            placement: 'top' // Set the placement to 'top'

        });
    });

    $(document).on("submit", "#create-event-submit-form", function(e) {
            e.preventDefault();
            var $form = $(this).closest("form");
            $.ajax({
                type: "POST",
                url: $form.attr("action"),
                data: $form.serialize(),
                dataType: "json",
                beforeSend: function() {
                 $("#loading-image-preview").show();
                },
                success: function(data) {
                    if (data.code == 200) {
                        $("#loading-image-preview").hide();
                        $form[0].reset();
                        $("#create-event-modal").modal("hide");
                        toastr['success'](data.message, 'Message');
                    } else {
                        toastr['error'](data.message, 'Message');
                        $("#loading-image-preview").hide();
                    }
                },
                error: function(xhr, status, error) {
                    var errors = xhr.responseJSON;
                    $.each(errors, function(key, val) {
                        $("#create-event-submit-form " + "#" + key + "_error").text(val[0]);
                    });
                    $("#loading-image-preview").hide();
                }
            });
    });

    $(document).on('click','#database-backup-monitoring',function(e){      
        e.preventDefault();
        $('#db-errors-list-modal').modal('show');
        getdbbackupList(1);
    });

    function getdbbackupList(pageNumber = 1){
    $.ajax({
          url: '{{route("get.backup.monitor.lists")}}',
          type: 'GET',
          headers: {
            'X-CSRF-TOKEN': "{{ csrf_token() }}"
          },
          data: {
            page: pageNumber
          },
          dataType: "json",
          beforeSend: function () {
            $("#loading-image").show();
          }
        }).done(function (response) {
          $("#loading-image").hide();
          var html = "";
          var startIndex = (response.data.current_page - 1) * response.data.per_page;
          $.each(response.data.data, function (index, dberrorlist) {
            var sNo = startIndex + index + 1; 
            html += "<tr>";
            html += "<td>" + sNo + "</td>";
            html += "<td>" + (dberrorlist.server_name !== null ? dberrorlist.server_name : "") + "</td>";
            html += "<td>" + (dberrorlist.instance !== null ? dberrorlist.instance : "") + "</td>";
            html += "<td>" + (dberrorlist.database_name !== null ? dberrorlist.database_name : "") + "</td>";
            html += "<td class='expand-row-dblist' style='word-break: break-all'>";
           if (dberrorlist.error) {
            html += "<span class='td-mini-container'>" + (dberrorlist.error.length > 15 ? dberrorlist.error.substr(0, 15) + '...' : dberrorlist.error) + "</span>";
            html += "<span class='td-full-container hidden'>" + dberrorlist.error + "</span>";
            } else {
                html += "";
            }
            html += "</td>";
            html += "<td><input type='checkbox' name='is_resolved' value='1' data-id='" + dberrorlist.id + "' onchange='updateIsResolved(this)'></td>";
            html += "<td>" + (dberrorlist.date !== null ? dberrorlist.date : "") + "</td>";
            if (dberrorlist.db_status_colour) {
                html += "<td>" + dberrorlist.db_status_colour.name + "</td>";
            } else {
                html += "";
            }
            html += "</tr>";
          });
          $(".db-list").html(html);
          $("#db-errors-list-modal").modal("show");
            if(response.count > 0) {
                $('.database-alert-badge').removeClass("hide");
            }
          renderPagination(response.data);
        }).fail(function (response, ajaxOptions, thrownError) {
          toastr["error"](response.message);
          $("#loading-image").hide();
        });
    }

    $(document).on('click', '.expand-row-dblist', function () {
        var selection = window.getSelection();
        if (selection.toString().length === 0) {
            $(this).find('.td-mini-container').toggleClass('hidden');
            $(this).find('.td-full-container').toggleClass('hidden');
        }
    });

    function renderPagination(data) {
          var paginationContainer = $(".pagination-container");
          var currentPage = data.current_page;
          var totalPages = data.last_page;
          var html = "";
          if (totalPages > 1) {
            html += "<ul class='pagination'>";
            if (currentPage > 1) {
              html += "<li class='page-item'><a class='page-link' href='javascript:void(0);' onclick='changePage(" + (currentPage - 1) + ")'>Previous</a></li>";
            }
            for (var i = 1; i <= totalPages; i++) {
              html += "<li class='page-item " + (currentPage == i ? "active" : "") + "'><a class='page-link' href='javascript:void(0);' onclick='changePage(" + i + ")'>" + i + "</a></li>";
            }
            if (currentPage < totalPages) {
              html += "<li class='page-item'><a class='page-link' href='javascript:void(0);' onclick='changePage(" + (currentPage + 1) + ")'>Next</a></li>";
            }
            html += "</ul>";
          }
        paginationContainer.html(html);
      }
      function changePage(pageNumber) {
        getdbbackupList(pageNumber);
      }

      function updateIsResolved(checkbox) {
			var dbListId = checkbox.getAttribute('data-id');
			$.ajax({	
				url: '{{route('db.update.isResolved')}}',
				method: 'GET',
				data: {
					id:dbListId
				},
				success: function(response) {
                    toastr["success"](response.message, "Message");
				},
				error: function(xhr, status, error) {
					alert("Error occured.please try again");
				}
			});	
		};


		function updateReadEmail(checkbox) {
			var emailId = checkbox.getAttribute('data-id');
			$.ajax({	
                url: '{{route('website.email.update')}}',
                type: 'GET',
                headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
				data: {
					id: emailId
				},
				success: function(response) {
                    toastr["success"](response.message, "Message");
				},
				error: function(xhr, status, error) {
					alert("Error occured.please try again");
				}
			});	
	    };


    $(document).on('click','#jenkins-build-status',function(e){
        e.preventDefault();
        $('#create-jenkins-status-modal').modal('show');
        getJenkinsStatus(1);
    });

    function getJenkinsStatus(page) {
        var url = "/monitor-jenkins-build/list?page=" + page

        $.ajax({
            type: "GET",
            url: url,
            dataType:"json",
            beforeSend:function(data){
                $('.ajax-loader').show();
            }
        }).done(function (response) {
            $('.ajax-loader').hide();
            var tableBody = $('#jenkins-status-modal-html');
            tableBody.empty(); // Clear the table body
            // Loop through the data and populate the table rows
            $.each(response.data, function(index, item) {
                var row = $('<tr>');
                row.append($('<td>').text(item.id));
                row.append($('<td>').text(item.project));
                row.append($('<td>').text(item.failuare_status_list));
                tableBody.append(row);
            });
            var paginationLinks = $('#jenkins-status-modal-table-paginationLinks');
            paginationLinks.empty(); // Clear the pagination links
            // Generate the pagination links manually
            var links = response.links;
            var currentPage = response.current_page;
            var lastPage = response.last_page;
            var pagination = $('<ul class="pagination"></ul>');
            // Previous page link
            if (currentPage > 1) {
                pagination.append('<li class="page-item"><a href="#" class="page-link" data-page="' + (currentPage - 1) + '">Previous</a></li>');
            }
            // Individual page links
            for (var i = 1; i <= lastPage; i++) {
                var activeClass = (i === currentPage) ? 'active' : '';
                pagination.append('<li class="page-item ' + activeClass + '"><a href="#" class="page-link" data-page="' + i + '">' + i + '</a></li>');
            }
            // Next page link
            if (currentPage < lastPage) {
                pagination.append('<li class="page-item"><a href="#" class="page-link" data-page="' + (currentPage + 1) + '">Next</a></li>');
            }
            paginationLinks.append(pagination);
            // Handle pagination link clicks
            paginationLinks.find('a').on('click', function(event) {
                event.preventDefault();
                var page = $(this).data('page');
                getJenkinsStatus(page);
            });
        }).fail(function (response) {
            $('.ajax-loader').hide();
            console.log(response);
        });
    }

    function listmagnetoerros(pageNumber = 1) {
        $.ajax({
          url: '{{route("magento-cron-error-list")}}',
          type: 'GET',
          headers: {
            'X-CSRF-TOKEN': "{{ csrf_token() }}"
          },
          data: {
            page: pageNumber
          },
          dataType: "json",
          beforeSend: function () {
            $("#loading-image").show();
          }
        }).done(function (response) {
            console.log(response);
          $("#loading-image").hide();
          var html = "";
          var startIndex = (response.data.current_page - 1) * response.data.per_page;
          $.each(response.data.data, function (index, cronData) {
            var sNo = startIndex + index + 1; 
            html += "<tr>";
            html += "<td>" + sNo + "</td>";
            html += "<td>" + (cronData.website.length > 15 ? cronData.website.substring(0, 15) + "..." : cronData.website) + "</td>";
            html += "<td>" + cronData.cron_id + "</td>";
            html += "<td>" + (cronData.job_code.length > 15 ? cronData.job_code.substring(0, 15) + "..." : cronData.job_code) + "</td>";
            html += "<td>" + (cronData.cron_message.length > 15 ? cronData.cron_message.substring(0, 15) + "..." : cronData.cron_message) + "</td>";
            html += "<td>" + cronData.cron_created_at + "</td>";
            html += "<td>" + cronData.cron_scheduled_at + "</td>";
            html += "<td>" + cronData.cron_executed_at + "</td>";
            html += "<td>" + cronData.cron_finished_at + "</td>";
            html += "</tr>";
          });
          $(".magneto-error-list").html(html);
          $("#magento-cron-error-status-modal").modal("show");
          renderMangetoErrorPagination(response.data);
        }).fail(function (response, ajaxOptions, thrownError) {
          toastr["error"](response.message);
          $("#loading-image").hide();
        });
      }
   
      function renderMangetoErrorPagination(data) {
        var paginationContainer = $(".pagination-container");
        var currentPage = data.current_page;
        var totalPages = data.last_page;
        var html = "";
        var maxVisiblePages = 10;

        if (totalPages > 1) {
            html += "<ul class='pagination'>";
            if (currentPage > 1) {
            html += "<li class='page-item'><a class='page-link' href='javascript:void(0);' onclick='changeMagnetoErrorPage(" + (currentPage - 1) + ")'>Previous</a></li>";
            }

            var startPage = 1;
            var endPage = totalPages;

            if (totalPages > maxVisiblePages) {
            if (currentPage <= Math.ceil(maxVisiblePages / 2)) {
                endPage = maxVisiblePages;
            } else if (currentPage >= totalPages - Math.floor(maxVisiblePages / 2)) {
                startPage = totalPages - maxVisiblePages + 1;
            } else {
                startPage = currentPage - Math.floor(maxVisiblePages / 2);
                endPage = currentPage + Math.ceil(maxVisiblePages / 2) - 1;
            }

            if (startPage > 1) {
                html += "<li class='page-item'><a class='page-link' href='javascript:void(0);' onclick='changeMagnetoErrorPage(1)'>1</a></li>";
                if (startPage > 2) {
                html += "<li class='page-item disabled'><span class='page-link'>...</span></li>";
                }
            }
            }

            for (var i = startPage; i <= endPage; i++) {
            html += "<li class='page-item " + (currentPage == i ? "active" : "") + "'><a class='page-link' href='javascript:void(0);' onclick='changeMagnetoErrorPage(" + i + ")'>" + i + "</a></li>";
            }
            html += "</ul>";
        }
        paginationContainer.html(html);
    }


    function changeMagnetoErrorPage(pageNumber) {
        listmagnetoerros(pageNumber);
    }


    $(document).on('click','#code-shortcuts',function(e){
        e.preventDefault();
        getShortcutNotes(true)
    });

    function getShortcutNotes(pageNumber = 1) {
        $.ajax({
          url: '{{route("code.get.Shortcut.notes")}}',
          type: 'GET',
          headers: {
            'X-CSRF-TOKEN': "{{ csrf_token() }}"
          },
          data: {
            page: pageNumber
          },
          dataType: "json",
          beforeSend: function () {
            $("#loading-image").show();
          }
        }).done(function (response) {
          $("#loading-image").hide();
          var html = "";
          var startIndex = (response.data.current_page - 1) * response.data.per_page;
          $.each(response.data, function (index, shortnote) {
            html += "<tr>";
            html += "<td>" + shortnote.id + "</td>";
            if (shortnote.platform !== null) {
            html += "<td>" + shortnote.platform.name + "</td>"; 
            } else {
            html += "<td>-</td>"; 
            }
            
            html += '<td><button type="button" data-id="'+ shortnote.id+'" data-type="title" class="btn list-code-shortcut-title-view" style="padding:1px 0px;"><i class="fa fa-eye" aria-hidden="true"></i></button></td>';            
            html += '<td><button type="button" data-id="'+ shortnote.id+'" data-type="code" class="btn list-code-shortcut-title-view" style="padding:1px 0px;"><i class="fa fa-eye" aria-hidden="true"></i></button></td>';
            html += '<td><button type="button" data-id="'+ shortnote.id+'" data-type="description" class="btn list-code-shortcut-title-view" style="padding:1px 0px;"><i class="fa fa-eye" aria-hidden="true"></i></button></td>';
            html += '<td><button type="button" data-id="'+ shortnote.id+'" data-type="solution" class="btn list-code-shortcut-title-view" style="padding:1px 0px;"><i class="fa fa-eye" aria-hidden="true"></i></button></td>';
            html += "<td>" + shortnote.user_detail.name + "</td>";
            if (shortnote.supplier_detail !== null) {
            html += "<td>" + shortnote.supplier_detail.supplier + "</td>"; 
            } else {
            html += "<td>-</td>";
            }
            if (shortnote.filename !== null) {
            html += "<td><img src='./codeshortcut-image/" + shortnote.filename + " 'height='50' width='50' ></td>"; 
            } else {
            html += "<td>-</td>";
            }
            html += "<td>" + shortnote.created_at + "</td>";
            html += "</tr>";
            });

        $(".short-cut-notes-alerts-list").html(html);
        $("#short-cut-notes-alerts-modal").modal("show");
          renderShortcutNotesPagination(response.data);
        }).fail(function (data, ajaxOptions, thrownError) {
          toastr["error"](data.message);
          $("#loading-image").hide();
        });
      }



      function renderShortcutNotesPagination(data) {
          var codePagination = $(".pagination-container-short-cut-notes-alerts");
          var currentPage = data.current_page;
          var totalPages = data.last_page;
          var html = "";
          if (totalPages > 1) {
            html += "<ul class='pagination'>";
            if (currentPage > 1) {
              html += "<li class='page-item'><a class='page-link' href='javascript:void(0);' onclick='changePageForShortCut(" + (currentPage - 1) + ")'>Previous</a></li>";
            }
            for (var i = 1; i <= totalPages; i++) {
              html += "<li class='page-item " + (currentPage == i ? "active" : "") + "'><a class='page-link' href='javascript:void(0);' onclick='changePageForShortCut(" + i + ")'>" + i + "</a></li>";
            }
            if (currentPage < totalPages) {
              html += "<li class='page-item'><a class='page-link' href='javascript:void(0);' onclick='changePageForShortCut(" + (currentPage + 1) + ")'>Next</a></li>";
            }
            html += "</ul>";
          }
          codePagination.html(html);
      }
      function changePageForShortCut(pageNumber) {
        getShortcutNotes(pageNumber);
      }

      $(document).on('click','#create-documents',function(e){
        e.preventDefault();
        $('#short-cut-documentation-modal').modal('show');
        getDocumentations(true);
    });

    function getDocumentations(showModal = false) {
        $.ajax({
            type: "GET",
            url: "{{route('documentShorcut.list')}}",
            dataType:"json",
            beforeSend:function(data){
                $('.ajax-loader').show();
            }
        }).done(function (response) {
            $('.ajax-loader').hide();
            $('#list-documentation-shortcut-modal-html').empty().html(response.tbody);
            if (showModal) {
                $('#short-cut-documentation-modal').modal('show');
            }
        }).fail(function (response) {
            $('.ajax-loader').hide();
            console.log(response);
        });
    }

    function showdocumentCreateModal() {
      $('#short-cut-documentation-modal').modal('hide');
      $('#documentaddModal').modal('show');
    }

    $(document).on('click','#event-alerts',function(e){
        e.preventDefault();
        getEventAlerts(true);
    });
    $(document).ready(function() {
        @if(Auth::check())
        var Role = "{{ Auth::user()->hasRole('Admin') }}";
        if (Role) {
            getEventAlerts();
            getTimeEstimationAlerts();
            getScriptDocumentLogs();
        }
        @endif
    });

    function getEventAlerts(showModal = false) {
        $.ajax({
            type: "GET",
            url: "{{route('event.getEventAlerts')}}",
            dataType:"json",
            beforeSend:function(data){
                $('.ajax-loader').show();
            }
        }).done(function (response) {
            $('.ajax-loader').hide();
            $('#event-alerts-modal-html').empty().html(response.html);
            if (showModal) {
                $('#event-alerts-modal').modal('show');
            }
            if(response.count > 0) {
                $('.event-alert-badge').removeClass("hide");
            }
        }).fail(function (response) {
            $('.ajax-loader').hide();
            console.log(response);
        });
    }

    function getScriptDocumentLogs(showModal = false) {
        $.ajax({
            type: "GET",
            url: "{{route('script-documents.errorlogs')}}",
            dataType:"json",
            beforeSend:function(data){
                $('.ajax-loader').show();
            }
        }).done(function (response) {
            if(response.count > 0) {
                $('.script-document-error-badge').removeClass("hide");
            }
        }).fail(function (response) {
            $('.ajax-loader').hide();
            console.log(response);
        });
    }

    $(document).on('click','#search-command',function(e){
        e.preventDefault();
        getMagentoCommand(true);
    });

    function getMagentoCommand(showModal = false) {
        $.ajax({
            type: "GET",
            url: "{{route('magento.getMagentoCommand')}}",
            dataType:"json",
            beforeSend:function(data){
                $('.ajax-loader').show();
            }
        }).done(function (response) {
            $('.ajax-loader').hide();
            $('#magento-commands-modal-html').empty().html(response.html);
            //if (showModal) {
                $('#magento-commands-modal').modal('show');
            //}
        }).fail(function (response) {
            $('.ajax-loader').hide();
            console.log(response);
        });
    }

    $(document).on('click','#timer-alerts',function(e){
        e.preventDefault();
        getTimerAlerts(true);
    });

    function getTimerAlerts(showModal = false) {

        $.ajax({
            type: "GET",
            url: "{{route('get.timer.alerts')}}",
            dataType:"json",
            beforeSend:function(data){
                $('.ajax-loader').show();
            }
        }).done(function (response) {
            $('.ajax-loader').hide();
            $('#timer-alerts-modal-html').empty().html(response.tbody);
            if (showModal) {
                $('#timer-alerts-modal').modal('show');
            }
            if(response.count > 0) {
                $('.timer-alert-badge').removeClass("hide");
            }
        }).fail(function (response) {
            $('.ajax-loader').hide();
        });
    }

    function getTimeEstimationAlerts() {
        $.ajax({
            type: "GET",
            url: "{{route('task.estimate.alert')}}",
            dataType:"json",
            beforeSend:function(data){
                $('.ajax-loader').show();
            }
        }).done(function (response) {
            $('.ajax-loader').hide();
            if(response.count > 0) {
                $('.time-estimation-badge').removeClass("hide");
            }
        }).fail(function (response) {
            $('.ajax-loader').hide();
            console.log(response);
        });
    }

        $(document).on('submit', '#event-alert-date-form', function(event) {
            event.preventDefault();
            var dateValue = $('input[name="event_alert_date"]').val();
            $.ajax({
                    type: "GET",
                    url: "{{route('event.getEventAlerts')}}",
                    data: {
                        _token: "{{ csrf_token() }}",
                        date : dateValue
                    },
            }).done(function (response) {
                $('.ajax-loader').hide();
                $('#event-alerts-modal-html').empty().html(response.html);
                if (showModal) {
                    $('#event-alerts-modal').modal('show');
                }
                if(response.count > 0) {
                    $('.event-alert-badge').removeClass("hide");
                }
            }).fail(function (response) {
                $('.ajax-loader').hide();
            });
         });


    $(document).on('click','.event-alert-log-modal',function(e){
        var event_type = $(this).data("event_type");
        var event_id = $(this).data("event_id");
        var event_schedule_id = $(this).data("event_schedule_id");
        var assets_manager_id = $(this).data("assets_manager_id");
        var event_alert_date = $(this).data("event_alert_date");
        var is_read = $(this).prop('checked');

        $.ajax({
            type: "POST",
            url: "{{route('event.saveAlertLog')}}",
            data: {
                _token: "{{ csrf_token() }}",
                event_type,
                event_id,
                event_schedule_id,
                assets_manager_id,
                event_alert_date,
                is_read
            },
            dataType:"json",
            beforeSend:function(data){
                $('.ajax-loader').show();
            }
        }).done(function (response) {
            toastr["success"](response.message, "Message");
            $('.ajax-loader').hide();
        }).fail(function (response) {
            $('.ajax-loader').hide();
        });
    });


    $(document).on('click','#script-document-logs',function(e){
        e.preventDefault();
        $('#script-document-error-logs-alerts-modal').modal('show');
        getScriptDocumentErrorLogs(true);
    });

    $(document).on('click','#assets-manager-listing',function(e){
        e.preventDefault();
        getAssetsManager();
    });

    function getScriptDocumentErrorLogs(showModal = false) {
        $.ajax({
            type: "GET",
            url: "{{route('script-documents.getScriptDocumentErrorLogsList')}}",
            dataType:"json",
            beforeSend:function(data){
                $('.ajax-loader').show();
            }
        }).done(function (response) {
            $('.ajax-loader').hide();
            $('#script-document-error-logs-alerts-modal-html').empty().html(response.tbody);
            if (showModal) {
                $('#script-document-error-logs-alerts-modal').modal('show');
            }
        }).fail(function (response) {
            $('.ajax-loader').hide();
            console.log(response);
        });
    }

    function getAssetsManager() {
        $.ajax({
            type: "GET",
            url: "{{route('assetsManager.loadTable')}}",
            dataType:"json",
            beforeSend:function(data){
                $('.ajax-loader').show();
            }
        }).done(function (response) {
            $('.ajax-loader').hide();
            $("#ajax-assets-manager-listing-modal").empty().html(response.tpl);
            $("#assetsEditModal").modal('show');
        }).fail(function (response) {
            $('.ajax-loader').hide();
            $("#ajax-assets-manager-listing-modal").empty().html(response.tpl);
            $("#assetsEditModal").modal('show');
        });
    }

    $(document).on('click','.script-document-last_output-header-view',function(){
        id = $(this).data('id');
        $.ajax({
            method: "GET",
            url: `{{ route('script-documents.comment', [""]) }}/` + id,
            dataType: "json",
            success: function(response) {
               
                $("#script-document-last-output-list-header").find(".script-document-last-output-header-view").html(response.last_output);
                $("#script-document-last-output-list-header").modal("show");
         
            }
        });
    });

    $(document).on('click','#google-drive-screen-cast',function(e){
        e.preventDefault();
        $('#google-drive-screen-cast-alerts-modal').modal('show');
        getgooglescreencast(true);
    });

    function showCreateScreencastModal() {
      $('#google-drive-screen-cast-alerts-modal').modal('hide');
      $.ajax({
        url: "{{ route('getDropdownDatas') }}",
        type: "GET",
        dataType: "json",
        success: function(response) {

            var tasks = response.tasks;
            var users = response.users;
            var generalTask = response.generalTask;

            var $taskSelect = $("#id_label_task");
            var $userReadSelect = $("#id_label_multiple_user_read");
            var $userWriteSelect = $("#id_label_multiple_user_write");

            $taskSelect.empty();
            $taskSelect.append('<option value="" class="form-control">Select Task</option>');

            tasks.forEach(function(task) {
                $taskSelect.append('<option value="' + task.id + '">' + task.id + '-' + task.subject +  '</option>');
            });
             generalTask.forEach(function(generalTask) {
                $taskSelect.append('<option value="' + generalTask.id + '">' + generalTask.id + '-' + generalTask.subject +  '</option>');
            });

            $userReadSelect.empty();
            $userWriteSelect.empty();
            $userReadSelect.append('<option value="" class="form-control">Select User</option>');
            $userWriteSelect.append('<option value="" class="form-control">Select User</option>');

            users.forEach(function(user) {
                var optionText = user.name;
                $userReadSelect.append('<option value="' + user.gmail + '">' + optionText + '</option>');
                $userWriteSelect.append('<option value="' + user.gmail + '">' + optionText + '</option>');
            });
             },
            error: function(xhr, status, error) {
                console.error(error);
            }
            }); 
    }

    function getgooglescreencast(showModal = false) {
        $.ajax({
            type: "GET",
            url: "{{route('google-drive-screencast.getGooglesScreencast')}}",
            dataType:"json",
            beforeSend:function(data){
                $('.ajax-loader').show();
            }
        }).done(function (response) {
            $('.ajax-loader').hide();
            $('#google-drive-screen-cast-modal-html').empty().html(response.tbody);
            if (showModal) {
                $('#google-drive-screen-cast-modal').modal('show');
            }
        }).fail(function (response) {
            $('.ajax-loader').hide();
            console.log(response);
        });
    }

    $(document).on("click", ".permission-request", function(e) {
        e.preventDefault();
        $.ajax({
            url: '/user-management/request-list',
            type: 'POST',
            data: {
                _token: "{{ csrf_token() }}"
            },
            dataType: 'json',
            beforeSend: function() {
                $("#loading-image").show();
            },
            success: function(result) {
                $("#loading-image").hide();
                if (result.code == 200) {
                    var t = '';
                    $.each(result.data, function(k, v) {
                        t += `<tr><td>` + v.name + `</td>`;
                        t += `<td>` + v.permission_name + `</td>`;
                        t += `<td>` + v.request_date + `</td>`;
                        t += `<td><button class="btn btn-secondary btn-xs permission-grant" data-type="accept" data-id="` +
                            v.permission_id + `" data-user="` + v.user_id +
                            `">Accept</button>
                                 <button class="btn btn-secondary btn-xs permission-grant" data-type="reject" data-id="` +
                            v.permission_id + `" data-user="` + v
                            .user_id + `">Reject</button>
                              </td></tr>`;
                    });
                    if (t == '') {
                        t = '<tr><td colspan="4" class="text-center">No data found</td></tr>';
                    }
                }
                $("#permission-request-model").find(".show-list-records").html(t);
                $("#permission-request-model").modal("show");
            },
            error: function() {
                $("#loading-image").hide();
            }
        });
    });

    $('.add_todo_title').change(function() {
        if ($('.add_todo_subject').val() == "") {
            $('.add_todo_subject').val("");
            $('.add_todo_subject').val($('.add_todo_title').val());
        }
    })

    $('#todo-date').datetimepicker({
        format: 'YYYY-MM-DD',
    });

    $(document).on("click", ".todolist-request", function(e) {
        e.preventDefault();
        $("#todolist-request-model").modal("show");
    });

	$(document).on("click", ".todolist-get", function(e) {
		e.preventDefault();
		$("#todolist-get-model").modal("show");
	});

    $(document).on("click", ".menu-create-database", function(e) {
        e.preventDefault();
        $("#menu-create-database-model").modal("show");
    });

    $(document).on("click", ".menu-show-task", function(e) {
        e.preventDefault();
        $("#menu-show-task-model").modal("show");
    });

    $(document).on("click", ".vendor-flowchart-header", function(e) {
        e.preventDefault();
        $("#vendor-flowchart-header-model").modal("show");
    });

    $(document).on("click", ".vendor-qa-header", function(e) {
        e.preventDefault();
        $("#vendor-qa-header-model").modal("show");
    });

    $(document).on("click", ".vendor-rqa-header", function(e) {
        e.preventDefault();
        $("#vendor-rqa-header-model").modal("show");
    });

    $(document).on("click", ".menu-show-dev-task", function(e) {
        e.preventDefault();
        $("#menu-show-dev-task-model").modal("show");
    });

    $(document).on("click", ".menu-todolist-get", function(e) {
        e.preventDefault();

        getTodoListHeader();
        $("#menu-todolist-get-model").modal("show");
    });

    function saveRemarksHeaderFc(vendor_id, flow_chart_id){

        var remarks = $("#remark_header_"+vendor_id+"_"+flow_chart_id).val();

        if(remarks==''){
            alert('Please enter remarks.');
        } else {

            $.ajax({
                url: "{{route('vendors.flowchart.saveremarks')}}",
                type: 'POST',
                headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    'vendor_id' :vendor_id,
                    'flow_chart_id' :flow_chart_id,
                    'remarks' :remarks,
                },
                beforeSend: function() {
                    $(this).text('Loading...');
                    $("#loading-image").show();
                },
                success: function(response) {
                    $("#remark_header_"+vendor_id+"_"+flow_chart_id).val('');
                    $("#loading-image").hide();
                    toastr['success']('Remarks Added successfully!!!', 'success');
                }
            }).fail(function(response) {
                $("#loading-image").hide();
                toastr['error'](response.responseJSON.message);
            });
        }
    }
    
    $(document).on('click', '.remarks-history-show-header-fc', function() {
        var vendor_id = $(this).attr('data-vendorid');
        var flow_chart_id = $(this).attr('data-flow_chart_id');

        $.ajax({
            url: "{{route('vendors.flowchart.getremarks')}}",
            type: 'POST',
            headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                'vendor_id' :vendor_id,
                'flow_chart_id' :flow_chart_id,
            },
            success: function(response) {
                if (response.status) {
                    var html = "";
                    $.each(response.data, function(k, v) {
                        html += `<tr>
                                    <td> ${k + 1} </td>
                                    <td> ${(v.remarks != null) ? v.remarks : ' - ' } </td>
                                    <td> ${(v.user !== undefined) ? v.user.name : ' - ' } </td>
                                    <td> ${v.created_at} </td>
                                </tr>`;
                    });
                    $("#vfc-remarks-histories-list-header-fc").find(".vfc-remarks-histories-list-view-header-fc").html(html);
                    $("#vfc-remarks-histories-list-header-fc").modal("show");
                } else {
                    toastr["error"](response.error, "Message");
                }
            }
        });
    });

    $(document).on('change', '.status-dropdown-header-fc', function(e) {        
        e.preventDefault();
        var vendor_id = $(this).data('id');
        var flow_chart_id = $(this).data('flow_chart_id');
        var selectedStatus = $(this).val();

        // Make an AJAX request to update the status
        $.ajax({
            url: '/vendor/update-flowchartstatus',
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                vendor_id: vendor_id,
                flow_chart_id: flow_chart_id,
                selectedStatus: selectedStatus
            },
            success: function(response) {
                toastr['success']('Status  Created successfully!!!', 'success');
                console.log(response);
            },
            error: function(xhr, status, error) {
                // Handle the error here
                console.error(error);
            }
        });
    });

    $(document).on('click', '.status-history-show-header-fc', function() {
        var vendor_id = $(this).attr('data-id');
        var flow_chart_id = $(this).attr('data-flow_chart_id');

        $.ajax({
            url: "{{route('vendors.flowchartstatus.histories')}}",
            type: 'POST',
            headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                'vendor_id' :vendor_id,
                'flow_chart_id' :flow_chart_id,
            },
            success: function(response) {
                if (response.status) {
                    var html = "";
                    $.each(response.data, function(k, v) {
                        html += `<tr>
                                    <td> ${k + 1} </td>
                                    <td> ${(v.old_value != null) ? v.old_value.status_name : ' - ' } </td>
                                    <td> ${(v.new_value != null) ? v.new_value.status_name : ' - ' } </td>
                                    <td> ${(v.user !== undefined) ? v.user.name : ' - ' } </td>
                                    <td> ${v.created_at} </td>
                                </tr>`;
                    });
                    $("#fl-status-histories-list-header-fc").find(".fl-status-histories-list-view-header-fc").html(html);
                    $("#fl-status-histories-list-header-fc").modal("show");
                } else {
                    toastr["error"](response.error, "Message");
                }
            }
        });
    });

    function saveAnswerHeaderQa(vendor_id, question_id){

        var answer = $("#answer_header_"+vendor_id+"_"+question_id).val();

        if(answer==''){
            alert('Please enter answer.');
        } else {

            $.ajax({
                url: "{{route('vendors.question.saveanswer')}}",
                type: 'POST',
                headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    'vendor_id' :vendor_id,
                    'question_id' :question_id,
                    'answer' :answer,
                },
                beforeSend: function() {
                    $(this).text('Loading...');
                    $("#loading-image").show();
                },
                success: function(response) {
                    $("#answer_header_"+vendor_id+"_"+question_id).val('');
                    $("#loading-image").hide();
                    toastr['success']('Answer Added successfully!!!', 'success');
                }
            }).fail(function(response) {
                $("#loading-image").hide();
                toastr['error'](response.responseJSON.message);
            });
        }
    }

    $(document).on('click', '.answer-history-show-header-qa', function() {
        var vendor_id = $(this).attr('data-vendorid');
        var question_id = $(this).attr('data-qa_id');

        $.ajax({
            url: "{{route('vendors.question.getgetanswer')}}",
            type: 'POST',
            headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                'vendor_id' :vendor_id,
                'question_id' :question_id,
            },
            success: function(response) {
                if (response.status) {
                    var html = "";
                    $.each(response.data, function(k, v) {
                        html += `<tr>
                                    <td> ${k + 1} </td>
                                    <td> ${v.answer} </td>
                                    <td> ${v.created_at} </td>
                                </tr>`;
                    });
                    $("#vqa-answer-histories-list-header-qa").find(".vqa-answer-histories-list-view-header-qa").html(html);
                    $("#vqa-answer-histories-list-header-qa").modal("show");
                } else {
                    toastr["error"](response.error, "Message");
                }
            }
        });
    });

    $(document).on('change', '.status-dropdown-header-qa', function(e) {
        e.preventDefault();
        var vendor_id = $(this).data('id');
        var question_id = $(this).data('qa_id');
        var selectedStatus = $(this).val();

        // Make an AJAX request to update the status
        $.ajax({
            url: '/vendor/update-qastatus',
            method: 'POST',
            headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
              vendor_id: vendor_id,
              question_id: question_id,
              selectedStatus: selectedStatus
            },
            success: function(response) {
              toastr['success']('Status  Created successfully!!!', 'success');
              console.log(response);
            },
            error: function(xhr, status, error) {
              // Handle the error here
              console.error(error);
            }
        });
    });

    $(document).on('click', '.status-history-show-header-qa', function() {
        var vendor_id = $(this).attr('data-id');
        var question_id = $(this).attr('data-qa_id');

        $.ajax({
            url: "{{route('vendors.qastatus.histories')}}",
            type: 'POST',
            headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                'vendor_id' :vendor_id,
                'question_id' :question_id,
            },
            success: function(response) {
                if (response.status) {
                    var html = "";
                    $.each(response.data, function(k, v) {
                        html += `<tr>
                                    <td> ${k + 1} </td>
                                    <td> ${(v.old_value != null) ? v.old_value.status_name : ' - ' } </td>
                                    <td> ${(v.new_value != null) ? v.new_value.status_name : ' - ' } </td>
                                    <td> ${(v.user !== undefined) ? v.user.name : ' - ' } </td>
                                    <td> ${v.created_at} </td>
                                </tr>`;
                    });
                    $("#qa-status-histories-list-header-qa").find(".qa-status-histories-list-view-header-qa").html(html);
                    $("#qa-status-histories-list-header-qa").modal("show");
                } else {
                    toastr["error"](response.error, "Message");
                }
            }
        });
    });


    function saveAnswerHeaderRQa(vendor_id, question_id){

        var answer = $("#answerr_header_"+vendor_id+"_"+question_id).find("option:selected").val();

        if(answer==''){
            alert('Please select answer.');
        } else {

            $.ajax({
                url: "{{route('vendors.question.saveranswer')}}",
                type: 'POST',
                headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    'vendor_id' :vendor_id,
                    'question_id' :question_id,
                    'answer' :answer,
                },
                beforeSend: function() {
                    $(this).text('Loading...');
                    $("#loading-image").show();
                },
                success: function(response) {
                    $("#answerr_header_"+vendor_id+"_"+question_id+" option:first").prop('selected', true);                    
                    $("#loading-image").hide();
                    toastr['success']('Answer Added successfully!!!', 'success');
                }
            }).fail(function(response) {
                $("#loading-image").hide();
                toastr['error'](response.responseJSON.message);
            });
        }
    }

    $(document).on('click', '.ranswer-history-show-header-rqa', function() {
        var vendor_id = $(this).attr('data-vendorid');
        var question_id = $(this).attr('data-rqa_id');

        $.ajax({
            url: "{{route('vendors.rquestion.getgetanswer')}}",
            type: 'POST',
            headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                'vendor_id' :vendor_id,
                'question_id' :question_id,
            },
            success: function(response) {
                if (response.status) {
                    var html = "";
                    $.each(response.data, function(k, v) {
                        html += `<tr>
                                    <td> ${k + 1} </td>
                                    <td> ${v.answer} </td>
                                    <td> ${v.created_at} </td>
                                </tr>`;
                    });
                    $("#vqar-answer-histories-list-header-rqa").find(".vqar-answer-histories-list-view-header-rqa").html(html);
                    $("#vqar-answer-histories-list-header-rqa").modal("show");
                } else {
                    toastr["error"](response.error, "Message");
                }
            }
        });
    });

    $(document).on('change', '.status-dropdown-header-rqa', function(e) {
        e.preventDefault();
        var vendor_id = $(this).data('id');
        var question_id = $(this).data('rqa_id');
        var selectedStatus = $(this).val();

        // Make an AJAX request to update the status
        $.ajax({
            url: '/vendor/update-rqastatus',
            method: 'POST',
            headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
              vendor_id: vendor_id,
              question_id: question_id,
              selectedStatus: selectedStatus
            },
            success: function(response) {
              toastr['success']('Status  Created successfully!!!', 'success');
              console.log(response);
            },
            error: function(xhr, status, error) {
              // Handle the error here
              console.error(error);
            }
        });
    });

    $(document).on('click', '.status-history-show-header-rqa', function() {
        var vendor_id = $(this).attr('data-id');
        var question_id = $(this).attr('data-rqa_id');

        $.ajax({
            url: "{{route('vendors.rqastatus.histories')}}",
            type: 'POST',
            headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                'vendor_id' :vendor_id,
                'question_id' :question_id,
            },
            success: function(response) {
                if (response.status) {
                    var html = "";
                    $.each(response.data, function(k, v) {
                        html += `<tr>
                                    <td> ${k + 1} </td>
                                    <td> ${(v.old_value != null) ? v.old_value.status_name : ' - ' } </td>
                                    <td> ${(v.new_value != null) ? v.new_value.status_name : ' - ' } </td>
                                    <td> ${(v.user !== undefined) ? v.user.name : ' - ' } </td>
                                    <td> ${v.created_at} </td>
                                </tr>`;
                    });
                    $("#rqa-status-histories-list-header-rqa").find(".rqa-status-histories-list-view-header-rqa").html(html);
                    $("#rqa-status-histories-list-header-rqa").modal("show");
                } else {
                    toastr["error"](response.error, "Message");
                }
            }
        });
    });

    function getTodoListHeader(){
        var keyword = $('.dev-todolist-table').val();
        var todolist_start_date = $('#todolist_start_date').val();
        var todolist_end_date = $('#todolist_end_date').val();
        
        $.ajax({
            url: '{{route('todolist.module.search')}}',
            type: 'GET',       
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                keyword: keyword,
                todolist_start_date: todolist_start_date,
                todolist_end_date: todolist_end_date,
            },
            // dataType: 'json',
            beforeSend: function () {
                $("#loading-image").show();
            },
            success: function (response) {
                $("#loading-image").hide();
                $('.show-search-todolist-list').html(response);
            },
            error: function () {
                $("#loading-image").hide();
                toastr["Error"]("An error occured!");
            }
        });
    }

    $(document).on('click', '.btn-todolist-search-menu', function(e) {
        getTodoListHeader();
    });

    $(document).on('click', '.menu-preview-img-btn', function(e) {
        e.preventDefault();
        id = $(this).data('id');
        if (!id) {
            alert("No data found");
            return;
        }
        $.ajax({
            url: "/task/preview-img/" + id,
            type: 'GET',
            success: function(response) {
                $("#menu-preview-task-image").modal("show");
                $(".menu-task-image-list-view").html(response);
                initialize_select2()
            },
            error: function() {}
        });
    });


    $(document).on('click','#add-vochuer',function(e){
        e.preventDefault();
        $('#addvoucherModel').modal('show');
    });

    $(document).on("click", ".permission-grant", function(e) {
        e.preventDefault();
        var permission = $(this).data('id');
        var user = $(this).data('user');
        var type = $(this).data('type');

        $.ajax({
            url: '/user-management/modifiy-permission',
            type: 'POST',
            data: {
                _token: "{{ csrf_token() }}",
                permission: permission,
                user: user,
                type: type
            },
            dataType: 'json',
            beforeSend: function() {
                $("#loading-image").show();
            },
            success: function(result) {
                $("#loading-image").hide();
                if (result.code == 200) {
                    toastr["success"](result.data, "");
                } else {
                    toastr["error"](result.data, "");
                }
            },
            error: function() {
                $("#loading-image").hide();
            }
        });
    });

    function showCreatePasswordModal() {
      $('#searchPassswordModal').modal('hide');
    }

    $(document).on("click", ".permission-delete-grant", function(e) {
        e.preventDefault();
        $.ajax({
            url: '/user-management/request-delete',
            type: 'POST',
            data: {
                _token: "{{ csrf_token() }}"
            },
            dataType: 'json',
            beforeSend: function() {
                $("#loading-image").show();
            },
            success: function(result) {
                $("#loading-image").hide();
                if (result.code == 200) {
                    $("#permission-request").find(".show-list-records").html('');
                    toastr["success"](result.data, "");
                } else {
                    toastr["error"](result.data, "");
                }
            },
            error: function() {
                $("#loading-image").hide();
            }
        });
    });
    $('#id_label_task').select2({
        minimumInputLength: 3 // only start searching when the user has input 3 or more characters
        });
    $('#search_task').select2({
    minimumInputLength: 3 // only start searching when the user has input 3 or more characters
    });
    $("#id_label_multiple_user_read").select2();
    $("#id_label_multiple_user_write").select2();
    $("#search_user").select2();

        $(document).on('click', '.filepermissionupdate', function (e) {
                
                $("#updateGoogleFilePermissionModal #id_label_file_permission_read").val("").trigger('change');
                $("#updateGoogleFilePermissionModal #id_label_file_permission_write").val("").trigger('change');
                
                let data_read = $(this).data('readpermission');
                let data_write = $(this).data('writepermission');
                var file_id = $(this).data('fileid');
                var id = $(this).data('id');
                var permission_read = data_read.split(',');
                var permission_write = data_write.split(',');
                if(permission_read)
                {
                    $("#updateGoogleFilePermissionModal #id_label_file_permission_read").val(permission_read).trigger('change');
                }
                if(permission_write)
                {
                    $("#updateGoogleFilePermissionModal #id_label_file_permission_write").val(permission_write).trigger('change');
                }
                
                $('#file_id').val(file_id);
                $('#id').val(id);
            
            });

            $(document).on("click",".showFullMessage", function () {
                let title = $(this).data('title');
                let message = $(this).data('message');
                
                $("#showFullMessageModel .modal-body").html(message);
                $("#showFullMessageModel .modal-title").html(title);
                $("#showFullMessageModel").modal("show");
            });
            
            $(document).on("click",".filedetailupdate", function (e) {
                e.preventDefault();
                let id = $(this).data('id');
                let fileid = $(this).data('fileid');
                let fileremark = $(this).data('file_remark');
                let filename = $(this).data('file_name');

                $("#updateUploadedFileDetailModal .id").val(id);
                $("#updateUploadedFileDetailModal .file_id").val(fileid);
                $("#updateUploadedFileDetailModal .file_remark").val(fileremark);
                $("#updateUploadedFileDetailModal .file_name").val(filename);

            });

	function todoHomeStatusChange(id, xvla) {
			$.ajax({
			type: "POST",
					url: "{{ route('todolist.status.update') }}",
					data: {
					"_token": "{{ csrf_token() }}",
					"id": id,
					"status":xvla
				},
			dataType: "json",
			success: function(message) {
					$c = message.length;
					if ($c == 0) {
							alert('No History Exist');
					} else {
							toastr['success'](message.message, 'success');
					}
			},
			error: function(error) {
					toastr['error'](error, 'error');
			}
		});
	}

    function estimateFunTaskDetailHandler(elm) {
        let tasktype = $(elm).data('task');
        let taskid = $(elm).data('id');
        if(tasktype == "DEVTASK") {
            
            estimatefunTaskInformationModal(elm, taskid, tasktype)
        } else {
            
            estimatefunTaskInformationModal(elm, taskid, tasktype)
        }
    }

    $(document).on('submit', '#magento-command-date-form', function(event) {
        event.preventDefault();
        var $form = $(this).closest("form");
        $.ajax({
            type: "GET",
            url: "{{route('magento.getMagentoCommand')}}",
            data: $form.serialize(),
        }).done(function (response) {
            $('.ajax-loader').hide();
            $('#magento-commands-modal-html').empty().html(response.html);
            
            $('#magento-commands-modal').modal('show');
        }).fail(function (response) {
            $('.ajax-loader').hide();
        });
     });

    $(document).on('click','.list-code-shortcut-title-view',function(){
        id = $(this).data('id');
        type = $(this).data('type');
        $.ajax({
              method: "GET",
              url: `{{ route('code.get.Shortcut.data', [""]) }}/` + id,
              dataType: "json",
              success: function(response) {

                    if(type=='title'){
                        $("#list-code-shortcode-title-list-header").find(".modal-title").html('Title');
                        $("#list-code-shortcode-title-list-header").find(".list-code-shortcode-title-header-view").html(response.title);
                        $("#list-code-shortcode-title-list-header").modal("show");
                    } else if(type=='code'){    
                        $("#list-code-shortcode-title-list-header").find(".modal-title").html('Code');
                        $("#list-code-shortcode-title-list-header").find(".list-code-shortcode-title-header-view").html(response.code);
                        $("#list-code-shortcode-title-list-header").modal("show");
                    } else if(type=='description'){ 
                        $("#list-code-shortcode-title-list-header").find(".modal-title").html('Description');
                        $("#list-code-shortcode-title-list-header").find(".list-code-shortcode-title-header-view").html(response.description);
                        $("#list-code-shortcode-title-list-header").modal("show");
                    } else if(type=='solution'){
                        $("#list-code-shortcode-title-list-header").find(".modal-title").html('Solution');
                        $("#list-code-shortcode-title-list-header").find(".list-code-shortcode-title-header-view").html(response.solution);
                        $("#list-code-shortcode-title-list-header").modal("show");
                    }
              }
          });
    });

    function checkRecord() {
        $.ajax({
            url: `{{ route('event.getAppointmentRequest')}}`, // Replace with your server endpoint
            method: 'GET',
            success: function(responseData) {
                if (responseData.code == 200) {

                    if (responseData.result!='') {
                        Swal.fire({
                            title: responseData.result.user.name+' Want to connect on Zoom  - Accept Decline',
                            text: '',                            
                            showCancelButton: true,
                            confirmButtonText: 'Accept',
                            cancelButtonText: 'Decline'
                        }).then((result) => {
                            // Check if the user clicked the Accept button
                            if (result.isConfirmed) {
                                var requeststatus = 1;
                            } else if (result.dismiss === Swal.DismissReason.cancel) {
                                var requeststatus = 2;

                                $("#declien-remarks #appointment_requests_id").val(responseData.result.id);
                            }

                            if(requeststatus==1 || requeststatus==2){
                                $.ajax({
                                    type: 'POST',
                                    url: '{{route('event.updateAppointmentRequest')}}',
                                    beforeSend: function () {
                                        $("#loading-image-modal").show();
                                    },
                                    data: {
                                        _token: "{{ csrf_token() }}",
                                        id : responseData.result.id,
                                        request_status : requeststatus                              
                                    },
                                    dataType: "json"
                                }).done(function (response) {
                                    $("#loading-image-modal").hide();
                                    if (response.code == 200) {

                                        if(requeststatus==1){
                                            toastr['success']('You successfully accepeted request.', 'success');
                                        } else {
                                            toastr['success']('You successfully decline request.', 'success');
                                        }
                                    }
                                }).fail(function (response) {
                                    $("#loading-image-modal").hide();
                                    toastr['error']('Sorry, something went wrong', 'error');
                                });

                                if (requeststatus==2) {
                                    $("#declien-remarks").modal("show");
                                }     
                            }                       
                        });
                    } 

                    if (responseData.result_rerquest_user!='') {

                        if(responseData.result_rerquest_user.request_status==1){
                            var msgText = responseData.result_rerquest_user.userrequest.name+' Confirmed your meeting Request pls. Join zoom'
                        } else {
                            var msgText = responseData.result_rerquest_user.userrequest.name+' delicate your meeting request - '+responseData.result_rerquest_user.userrequest.decline_remarks
                        }

                        Swal.fire({
                            title: 'Hello!',
                            text: msgText,
                            showCancelButton: false,
                            confirmButtonText: 'Okay'
                        }).then((result) => {
                            // Check if the user clicked the Okay button
                            if (result.isConfirmed) {
                                // Perform an AJAX call when the Okay button is clicked
                                $.ajax({
                                    type: 'POST',
                                    url: '{{route('event.updateuserAppointmentRequest')}}',
                                    beforeSend: function () {
                                        $("#loading-image-modal").show();
                                    },
                                    data: {
                                        _token: "{{ csrf_token() }}",
                                        id : responseData.result_rerquest_user.id,
                                    },
                                    dataType: "json"
                                }).done(function (response) {
                                    $("#loading-image-modal").hide();
                                }).fail(function (response) {
                                    $("#loading-image-modal").hide();
                                });
                            }
                        });
                    }
                }
            },
            error: function(xhr, status, error) {
            }
        });
    }

    setInterval(checkRecord, 5000); 

    $(document).ready(function() {
        $('#availabilityToggle').change(function() {
                
            $('#availabilityText').removeClass('textLeft');
            $('#availabilityText').removeClass('textRight');

            var isChecked = $(this).prop('checked');
            var availabilityText = isChecked ? 'Online' : 'Offline';
            var alignmentText = isChecked ? 'textLeft' : 'textRight';

            // Update the text content within the toggle switch
            $('#availabilityText').text(availabilityText);
            var availabilityTextNew = isChecked ? 'On' : 'Off';
            $('#availabilityText').addClass(availabilityTextNew);

            $.ajax({
                type: 'POST',
                url: '{{route('useronlinestatus.status.update')}}',
                beforeSend: function () {
                    $("#loading-image-modal").show();
                },
                data: {
                    _token: "{{ csrf_token() }}",
                    is_online_flag : availabilityText,
                },
                dataType: "json",
            }).done(function (response) {
                $("#loading-image-modal").hide();
                toastr['success'](response.message, 'success');
            }).fail(function (response) {
                $("#loading-image-modal").hide();
            });
        });
    });

    $(document).on('click', '.send-ap-quick-request', function (event) {

        if($('#requested_ap_user_id').val()==''){
            alert('Please select user');
            return false;
        }

        if($('#requested_ap_remarks').val()==''){
            alert('Please enter remarks');
            return false;
        }     

        var currentDate = moment(); // Current date and time                              
        var dateAfterOneHour = moment(currentDate).add(1, 'hours');
        
        $.ajax({
            type: 'POST',
            url: '{{route('event.sendAppointmentRequest')}}',
            beforeSend: function () {
                $("#loading-image-modal").show();
            },
            data: {
                _token: "{{ csrf_token() }}",
                requested_user_id : $('#requested_ap_user_id').val(),
                requested_time : moment(currentDate).format('YYYY-MM-DD HH:mm:ss'),
                requested_time_end : moment(dateAfterOneHour).format('YYYY-MM-DD HH:mm:ss'),
                requested_remarks : $('#requested_ap_remarks').val(),
            },
            dataType: "json"
        }).done(function (response) {
            $("#loading-image-modal").hide();
            if (response.code == 200) {
                toastr['success'](response.message, 'success');
            }

            setTimeout(function() {
                location.reload();
            }, 60000);

        }).fail(function (response) {
            $("#loading-image-modal").hide();
            toastr['error'](response.message, 'error');
            console.log("Sorry, something went wrong");
        });
    });

    $(".select-multiple-s").select2({
          tags: true
      });

</script>