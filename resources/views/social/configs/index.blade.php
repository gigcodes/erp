@extends('layouts.app')

@section('styles')
    <link rel="stylesheet" type="text/css"
          href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.css">
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
    <style type="text/css">
        #loading-image {
            position: fixed;
            top: 50%;
            left: 50%;
            margin: -50px 0px 0px -50px;
        }

        .fb-login-btn {
            padding: 7px;
            background-color: #6c757d;
            color: #fff;
            border-radius: 4px;
            margin-left: 5px;
            display: inline-block;
            vertical-align: middle;
        }
    </style>
@endsection

@section('content')
    <div id="myDiv">
        <img id="loading-image" src="{{config('app.url')}}/images/pre-loader.gif" style="display:none;" alt="" />
    </div>
    <div class="col-md-12">
        <div class="row">
            <div class="col-lg-12 margin-tb">
                <h2 class="page-heading"> Configs</h2>
                <div class="col-lg-12">
                    <form action="{{route('social.config.index')}}" method="GET" class="form-inline align-items-start">
                        <div class="row mr-3 mb-3">
                            <div class="form-group">
                                <select id="store_website_id" class="form-control store_website_id"
                                        name="store_website_id[]" multiple>
                                    @foreach ($websites as $id => $website)
                                        <option value="{{ $website->id }}" {{ in_array($website->id,$selected_website?? []) ? 'selected' : '' }}>{{ $website->title }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group ml-3">
                                <select id="user_name" class="form-control user_name" name="user_name[]" multiple>
                                    @foreach ($user_names as $id => $user_name)
                                        <option value="{{ $user_name->email }}" {{ in_array($user_name->email,$selected_user_name?? []) ? 'selected' : '' }}>{{ $user_name->email }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group ml-3">
                                <select id="platform" class="form-control platform" name="platform[]" multiple>
                                    @foreach ($platforms as $id => $platform)
                                        <option value="{{ $platform->platform }}" {{ in_array($platform->platform,$selected_platform?? []) ? 'selected' : '' }}>{{ $platform->platform }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit" class="btn btn-image"><img src="{{asset('images/filter.png')}}" />
                            </button>
                        </div>
                    </form>
                </div>
                <div class="pull-right">
                    <button type="button" class="btn btn-secondary" data-toggle="modal"
                            data-target="#ConfigCreateModal">+
                    </button>
                </div>
            </div>
        </div>

        @if ($message = Session::get('success'))
            <div class="alert alert-success">
                <p>{{ $message }}</p>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <strong>Whoops!</strong> There were some problems with your input.<br><br>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @include("social.header_menu")
        <div class="table-responsive mt-3">
            <table class="table table-bordered" id="passwords-table">
                <thead>
                <tr>
                    <th style="width: 5% !important;">Website</th>
                    <th style="width: 5% !important;">Platform</th>
                    <th style="width: 5% !important;">Name</th>
                    <th style="width: 5% !important;">UserName</th>
                    <th style="width: 5% !important;">Status</th>
                    <th style="width: 5% !important;">Started At</th>
                    <th style="width: 7% !important;">Actions</th>
                </tr>
                </thead>

                <tbody>

                @include('social.configs.partials.data')
                {!! $socialConfigs->render() !!}
                </tbody>
            </table>
        </div>
    </div>
    @include('social.configs.partials.add-modal')
@endsection

@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/js/bootstrap-multiselect.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
    <script>
      $("#fb_redirect").click(function() {
        $.ajax({
          url: '{{route("social.config.fbtoken")}}',
          dataType: "json",
          data: {
            token: token
          },
          success: function(result) {
            if (result) {
              $("#loading-image").hide();
              let html = `<option value="">-----Select Adsets-----</option>`;
              if (result) {
                console.log("come toadsets adsets ");
                console.log(result);
                $.each(result, function(key, value) {
                  html += `<option value="${value.id}" rel="${value.name}" >${value.name}</option>`;
                });
              }
              $("#adset_id").html(html);

            } else {
              $("#loading-image").hide();
              alert("token Expired");
            }
          },
          error: function(exx) {

          }
        });

      });
      $("#token").focusout(function() {
        let token = $("#token").val();
        if (!token) {
          alert("please enter token first");
        }
        src = "{{ route('social.config.adsmanager') }}";

        $.ajax({
          url: '{{route("social.config.adsmanager")}}',
          dataType: "json",
          data: {
            token: token
          },
          success: function(result) {
            if (result) {
              $("#loading-image").hide();
              let html = `<option value="">-----Select Adsets-----</option>`;
              if (result) {
                console.log("come toadsets adsets ");
                console.log(result);
                $.each(result, function(key, value) {
                  html += `<option value="${value.id}" rel="${value.name}" >${value.name}</option>`;
                });
              }
              $("#adset_id").html(html);

            } else {
              $("#loading-image").hide();
              alert("token Expired");
            }
          },
          error: function(exx) {

          }
        });

      });

      $(document).ready(function() {
        $(".select-multiple").multiselect();
        $(".select-multiple2").select2();
        $(".store_website_id").select2({
          placeholder: "Select Store Website"
        });
        $(".user_name").select2({
          placeholder: "Select User Name"
        });
        $(".platform").select2({
          placeholder: "Select Platform"
        });
      });


      $("#filter-date").datetimepicker({
        format: "YYYY-MM-DD"
      });

      $("#filter-whats-date").datetimepicker({
        format: "YYYY-MM-DD"
      });

      function changesocialConfig(config) {
        $("#ConfigEditModal" + config.id + "").modal("show");

        let token = $("#edit_token").val();

        if (!token) {
          alert("please enter token first");
        }
        src = "{{ route('social.config.adsmanager') }}";
        $.ajax({
          url: '{{route("social.config.adsmanager")}}',
          dataType: "json",
          data: {
            token: token
          },
          success: function(result) {
            //console.log(result);
            if (result) {
              $("#loading-image").hide();
              let htmledit = `<option value="">-----Select Ad-Manager-Account-----</option>`;
              if (result) {
                console.log("come toadsets adsets ");
                console.log(result);
                $.each(result, function(key, value) {
                  console.log("-----------dieedit", value.name);
                  if (config.ads_manager) {
                    if (value.id == config.ads_manager) {
                      htmledit += `<option value="${value.id}" selected>${value.name}</option>`;
                    } else {
                      htmledit += `<option value="${value.id}" rel="${value.name}" >${value.name}</option>`;
                    }

                  } else {
                    htmledit += `<option value="${value.id}" rel="${value.name}" >${value.name}</option>`;
                  }


                });
                $(".adsmanager").html(htmledit);
              }


            } else {
              $("#loading-image").hide();
              alert("token Expired");
            }
          },
          error: function(exx) {

          }
        });

      }

      function deleteConfig(config_id) {
        event.preventDefault();
        if (confirm("Are you sure?")) {
          $.ajax({
            type: "POST",
            url: "{{ route('social.config.delete') }}",
            data: { "_token": "{{ csrf_token() }}", "id": config_id },
            dataType: "json",
            success: function(message) {
              alert("Deleted Config");
              location.reload(true);
            }, error: function() {
              alert("Something went wrong");
            }

          });
        }
        return false;

      }

      $(document).ready(function() {
        src = "{{ route('social.config.index') }}";
        $(".search").autocomplete({
          source: function(request, response) {
            // number = $('#number').val();
            // username = $('#username').val();
            // provider = $('#provider').val();
            // customer_support = $('#customer_support').val();


            $.ajax({
              url: src,
              dataType: "json",
              data: {
                // number : number,
                // username : username,
                // provider : provider,
                // customer_support : customer_support,

              },
              beforeSend: function() {
                $("#loading-image").show();
              }

            }).done(function(data) {
              $("#loading-image").hide();
              console.log(data);
              $("#passwords-table tbody").empty().html(data.tbody);
              if (data.links.length > 10) {
                $("ul.pagination").replaceWith(data.links);
              } else {
                $("ul.pagination").replaceWith("<ul class=\"pagination\"></ul>");
              }

            }).fail(function(jqXHR, ajaxOptions, thrownError) {
              alert("No response from server");
            });
          },
          minLength: 1

        });
      });


    </script>
@endsection
