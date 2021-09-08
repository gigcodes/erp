@extends('layouts.app')

@section('favicon' , 'lead.png')

@section('title', 'Lead and Order Pricing')

@section('content')

    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">Lead and Order Pricing</h2>
            <div class="row">
                <div class="col-md-12 col-sm-12">
                  <div class="col-10" style="padding-left:0px;">
                        <div>
                            <form class="form-inline" action="{{ route('lead-order.index') }}" method="GET">
                                <div class="form-group col-md-4 pd-3">
                                  <input style="width:100%;" name="term" type="text" class="form-control" value="{{ isset($term) ? $term : '' }}" 
                                         placeholder="Customer Name, Lead or Order Id, Product Id">
                                </div>
                                <div class="form-group col-md-3 pd-3">
                                    <?php echo Form::select("brand_id",["" => "-- Select Brands --"]+$brandList,request('brand_id',[]),["class" => "form-control select2"]); ?>
                                </div>                                
                                <div class="form-group col-md-3 pd-3">
                                    <select class="form-control select2" name="order_or_lead" tabindex="-1" aria-hidden="true">
                                        <option value="">-- Lead Or Order --</option>
                                        <option value="lead" {{ (isset($orderOrLead) && $orderOrLead == 'lead') ? 'selected' : '' }} >Lead</option>
                                        <option value="order" {{ (isset($orderOrLead) && $orderOrLead == 'order') ? 'selected' : '' }} >Order</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-1 pd-3">
                                    <button type="submit" class="btn btn-image ml-3"><img src="{{asset('images/filter.png')}}" /></button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if ($message = Session::get('success'))
        <div class="alert alert-success">
            <p>{{ $message }}</p>
        </div>
    @endif

    <div class="productGrid" id="productGrid">
      @include('lead-order.lead-order-item')
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/js/bootstrap-multiselect.min.js"></script>
    <script type="text/javascript">
    $(document).ready(function() {
      $('#order-datetime').datetimepicker({
        format: 'YYYY-MM-DD'
      });
      $(".select2").select2({tags:true});
    });
      $(document).on('click', '.pagination a, th a', function(e) {
        e.preventDefault();
        var url = $(this).attr('href');
        getProducts(url);
      });
      $(document).on('click', '.check-lead', function() {
        var id = $(this).data('leadid');
        if ($(this).prop('checked') == true) {
          // $(this).data('attached', 1);
          attached_leads.push(id);
        } else {
          var index = attached_leads.indexOf(id);
          // $(this).data('attached', 0);
          attached_leads.splice(index, 1);
        }
        console.log(attached_leads);
      });
    </script>

@endsection
@section('scripts')
  
@endsection