@extends('layouts.app')

@section('favicon' , 'instagram.png')

@section('title', 'Influencer Info')

@section('styles')
<style type="text/css">
         #loading-image {
            position: fixed;
            top: 50%;
            left: 50%;
            margin: -50px 0px 0px -50px;
        }
    </style>
@endsection
@section('large_content')
    <div id="myDiv">
      <img id="loading-image" src="/images/pre-loader.gif" style="display:none;"/>
   </div>
    <div class="row">
        <div class="col-md-12">
           <h2 class="page-heading">Influencers (<span id="total">{{ $influencers->total() }}</span>)</h2>
            <div class="pull-left">
                <div class="form-group mr-3 mb-3">
                        <input name="term" type="text" class="form-control global" id="term"
                               value="{{ isset($term) ? $term : '' }}"
                               placeholder="name">
                </div>
            </div>
        </div>
        <div class="col-md-12">
            @if(Session::has('message'))
                <div class="alert alert-success">
                    {{ Session::get('message') }}
                </div>
            @endif
        </div>
        <div class="col-md-12">
            <div class="table-responsive">
                <table class="table-striped table table-bordered" id="data-table">
                    <thead >
                    <tr>
                        <th>Username</th>
                        <th>Posts</th>
                        <th>URL</th>
                        <th>Followers</th>
                        <th>Following</th>
                        <th>Description</th>
                    </tr>
                   </thead>
                     <tbody>
                   @include('instagram.hashtags.partials.influencer-data')
                    </tbody>
                </table>
                
                 {!! $influencers->render() !!}
            </div>
        </div>

        
    </div>
@endsection

@section('scripts')
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script type="text/javascript">
        $(document).on('click', '.expand-row', function () {
            var selection = window.getSelection();
            if (selection.toString().length === 0) {
                $(this).find('.td-mini-container').toggleClass('hidden');
                $(this).find('.td-full-container').toggleClass('hidden');
            }
        });

         $(document).ready(function() {
        src = "{{ route('influencers.index') }}";
        $(".global").autocomplete({
        source: function(request, response) {
            term = $('#term').val();
            
            
            $.ajax({
                url: src,
                dataType: "json",
                data: {
                    term : term,
                },
                beforeSend: function() {
                       $("#loading-image").show();
                },
            
            }).done(function (data) {
                 $("#loading-image").hide();
                console.log(data);
                $('#total').val(data.total)
                $("#data-table tbody").empty().html(data.tbody);
                if (data.links.length > 10) {
                    $('ul.pagination').replaceWith(data.links);
                } else {
                    $('ul.pagination').replaceWith('<ul class="pagination"></ul>');
                }
                
            }).fail(function (jqXHR, ajaxOptions, thrownError) {
                alert('No response from server');
            });
        },
        minLength: 1,
       
        });
    });
    </script>

@endsection