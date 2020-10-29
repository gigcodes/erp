@extends('layouts.app')
@section('favicon' , 'affilate-management.png')

@section('title', 'Affiliates Info')

@section('styles')

<style type="text/css">
    #loading-image {
            position: fixed;
            top: 50%;
            left: 50%;
            margin: -50px 0px 0px -50px;
            z-index: 60;
        }
</style>
@endsection
@section('content')
    <div id="myDiv">
        <img id="loading-image" src="/images/pre-loader.gif" style="display:none;"/>
    </div>
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">Affiliates Listing (<span id="affiliate_count">{{ $data->total() }}</span>)</h2>
            <div class="pull-left">
                <div class="form-group">
                        <div class="row">
                            <div class="col-md-4">
                                <input name="term" type="text" class="form-control"
                                       value="{{ isset($term) ? $term : '' }}"
                                       placeholder="search affiliate" id="term">
                            </div>
                            <div class="col-md-2">
                               <button type="button" class="btn btn-image" onclick="submitSearch()"><img src="/images/filter.png"/></button>
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-image" id="resetFilter" onclick="resetSearch()"><img src="/images/resend2.png"/></button>    
                            </div>
                            <div class="col-md-2">
                                    <button type="button" onclick="delete_multiple()" class="btn btn-image" title="delete multiple affiliates"><img src="/images/delete.png"/></button>
                            </div>
                        </div>
                    </div>
            </div>
        </div>
    </div>

    @include('partials.flash_messages')

    <div class="table-responsive">
        <table class="table table-bordered" id="affiliates-table">
              <thead>
            <tr>
                <th>Select</th>
                <th>No</th>
                <th>Affiliate First Name</th>
                <th>Affiliate Last Name</th>
                <th>Affiliate Phone</th>
                <th>url</th>
                <th>Source</th>
                <th>Email</th>
                <th>Visitors/month</th>
                <th>Page views/month</th>
                <th>country</th>
                <th>Action</th>
            </tr>
            </thead>
            <tbody>
                @include('affiliates.partials.list-affiliate')
            </tbody>
        </table>
    </div>

    {!! $data->render() !!}


@endsection

@section('scripts')
 <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/js/bootstrap-multiselect.min.js"></script>
<script type="text/javascript">
    $('.select-multiple').select2({width: '100%'});

    function submitSearch(){
        src = "{{route('affiliates.list')}}"
        term = $('#term').val()
        $.ajax({
            url: src,
            dataType: "json",
            data: {
                term : term,
            },
            beforeSend: function () {
                $("#loading-image").show();
            },

        }).done(function (data) {
            $("#loading-image").hide();
            $("#affiliates-table tbody").empty().html(data.tbody);
            $("#affiliate_count").text(data.count);
            if (data.links.length > 10) {
                $('ul.pagination').replaceWith(data.links);
            } else {
                $('ul.pagination').replaceWith('<ul class="pagination"></ul>');
            }

        }).fail(function (jqXHR, ajaxOptions, thrownError) {
            alert('No response from server');
        });
        
    }

    function resetSearch(){
        src = "{{route('affiliates.list')}}"
        blank = ''
        $.ajax({
            url: src,
            dataType: "json",
            data: {
               
               blank : blank, 

            },
            beforeSend: function () {
                $("#loading-image").show();
            },

        }).done(function (data) {
            $("#loading-image").hide();
            $('#term').val('')
            $('#affiliate-select').val('')
            $("#affiliates-table tbody").empty().html(data.tbody);
            $("#affiliate_count").text(data.count);
            if (data.links.length > 10) {
                $('ul.pagination').replaceWith(data.links);
            } else {
                $('ul.pagination').replaceWith('<ul class="pagination"></ul>');
            }

        }).fail(function (jqXHR, ajaxOptions, thrownError) {
            alert('No response from server');
        });
    }
    function delete_multiple(){
        var values = new Array();
        $.each($("input[name='affilate_multi_select[]']:checked"), function() {
          values.push($(this).val());
        });
        if(values==''){
            alert('please select affilates for removing first !');
            return;
        }
        $.ajax({
            url:"{{route('affiliates.destroy')}}",
            type:'POST',
            data: {"_token": "{{ csrf_token() }}",
                    id:values
                  },
            success:function(data){
                location.reload();
            }
        });
    } 
</script>

@endsection
