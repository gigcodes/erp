@extends('layouts.app')
@section('favicon', 'task.png')

@section('title', 'Message List | Inbox')

@section('styles')
    <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="/css/dialog-node-editor.css">
    <style>
        .panel-img-shorts .remove-img {
            display: block;
            float: right;
            width: 15px;
            height: 15px;
        }

        form.chatbot .col {
            flex-grow: unset !important;
        }

        .cls_remove_rightpadding {
            padding-right: 0px !important;
        }

        .cls_remove_allpadding {
            padding-left: 0px !important;
            padding-right: 0px !important;
        }

        #chat-list-history tr {
            word-break: break-word;
        }

        .reviewed_msg {
            word-break: break-word;
        }

        .chatbot .communication {
        }

        .background-grey {
            color: grey;
        }

        @media (max-width: 1400px) {
            .btns {
                padding: 3px 2px;
            }
        }

        .select2-container--default .select2-selection--multiple {
            border: 1px solid #ddd !important;
        }

        .d-inline.form-inline .select2-container {
            max-width: 100% !important;
            /*width: unset !important;*/
        }

        .actions {
            display: flex !important;
            align-items: center;
        }

        .actions a {
            padding: 0 3px !important;
            display: flex !important;
            align-items: center;
        }

        .actions .btn-image img {
            width: 13px !important;
        }

        .read-message {
            float: right;
        }

    </style>
@endsection

@section('content')
    <div class="row m-0">
        <div class="col-lg-12 margin-tb p-0">
            <h2 class="page-heading">Message List | Inbox</h2>
        </div>
    </div>

    <div class="row m-0">
        <div class="col-md-12 pl-3 pr-3">
            <div class="table-responsive-lg" id="page-view-result">
                @include("instagram.partials.message",[
                    'socialContact' => $socialContact
                ])
            </div>
        </div>
    </div>

    <div id="contact-chat-list-history" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Communication</h4>
                </div>
                <div class="modal-body" />
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div id="loading-image" style="position: fixed;left: 0;top: 0;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif')
      50% 50% no-repeat;display:none;">
    </div>
@endsection

@section('scripts')
    <script src="/js/bootstrap-toggle.min.js"></script>
    <script type="text/javascript" src="/js/jsrender.min.js"></script>
    <script type="text/javascript" src="/js/common-helper.js"></script>

    <script>
      $(document).on("click", ".load-contact-communication-modal", function() {
        $("#loading-image").show();
        const data = $(this).data("id");
        $.ajax({
          url: "{{ route('social.message.list') }}",
          method: "POST",
          data: {
            _token: "{{ csrf_token() }}",
            id: data
          },
          success: function(response) {
            const res = response.messages;
            $("#loading-image").hide();
            $("#contact-chat-list-history .modal-body").empty();
            if (res.length > 0) {
              $(res).each(function(key, value) {
                const sentBy =
                  `From ${value.from.name} To ${value.to[0].name} On ${value.created_time}`;

                $("#contact-chat-list-history .modal-body").append(`
                            <table class="table table-bordered">
                                <tr>
                                    <td style="width:50%">${value.message}</td>
                                    <td style="width:50%">${sentBy}</td>
                                </tr>
                            </table>
                            `);
              });
            } else {
              $("#contact-chat-list-history .modal-body").append(`
                            <table class="table table-bordered">
                                <tr>
                                    <td colspan="2">No conversations found</td>
                                </tr>
                            </table>
                            `);
            }
            $("#contact-chat-list-history").modal("show");
          },
          error: function(error) {
            alert("Counldn't load messages");
            $("#loading-image").hide();
          }
        });
      });
    </script>
@endsection
