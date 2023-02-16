@extends('layouts.app')

@section('title', 'App Store Sales')


@section('styles')
<style type="text/css">
    .switch {
  position: relative;
  display: inline-block;
  width: 60px;
  height: 34px;
}

.switch input { 
  opacity: 0;
  width: 0;
  height: 0;
}

.slider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: #ccc;
  -webkit-transition: .4s;
  transition: .4s;
}

.slider:before {
  position: absolute;
  content: "";
  height: 26px;
  width: 26px;
  left: 4px;
  bottom: 4px;
  background-color: white;
  -webkit-transition: .4s;
  transition: .4s;
}

input:checked + .slider {
  background-color: #2196F3;
}

input:focus + .slider {
  box-shadow: 0 0 1px #2196F3;
}

input:checked + .slider:before {
  -webkit-transform: translateX(26px);
  -ms-transform: translateX(26px);
  transform: translateX(26px);
}

/* Rounded sliders */
.slider.round {
  border-radius: 34px;
}

.slider.round:before {
  border-radius: 50%;
}
 #loading-image {
            position: fixed;
            top: 50%;
            left: 50%;
            margin: -50px 0px 0px -50px;
        }
</style>
@endsection
@section('content')
 <div id="myDiv">
      <img id="loading-image" src="/images/pre-loader.gif" style="display:none;"/>
   </div>
    <div class="row">
        <div class="col-md-12">
           
        </div>
        <div class="col-md-12">
            @if(Session::has('message'))
                <script>
                    alert("{{Session::get('message')}}")
                </script>
            @endif
            <div class="row">
                <div class="col-lg-12 margin-tb mb-3">
        <h2 class="page-heading">IOS App Sales Report </h2>
            </div>
          
    
      
        <div class="col-md-12">
 <div class="table-responsive mt-3">
      <table class="table table-bordered">
        <thead>
          <tr>
                  <th width="5%">ID</th>
            <th width="5%">App Name</th>
            <th width="5%">Start Date</th>
            <th width="2%">End Date</th>
            <th width="2%">downloads</th> <th width="2%">re_downloads</th> <th width="2%">uninstalls</th> <th width="2%">updates</th> <th width="2%">returns</th> <th width="2%">net_downloads</th> <th width="2%">promos</th> <th width="2%">revenue</th> <th width="2%">returns_amount</th> <th width="2%">edu_downloads</th> <th width="2%">gifts</th> <th width="2%">gift_redemptions</th> <th width="2%">edu_revenue</th> <th width="2%">gross_revenue</th> <th width="2%">gross_returns_amount</th> <th width="2%">gross_edu_revenue</th> <th width="2%">business_downloads</th> <th width="2%">business_revenue</th> <th width="2%">gross_business_revenue</th> <th width="2%">standard_downloads</th> <th width="2%">standard_revenue</th> <th width="2%">gross_standard_revenue</th> <th width="2%">app_downloads</th> <th width="2%">app_returns</th> <th width="2%">iap_amount</th> <th width="2%">iap_returns</th> <th width="2%">subscription_purchases</th> <th width="2%">subscription_returns</th> <th width="2%">app_revenue</th> <th width="2%">app_returns_amount</th> <th width="2%">gross_app_revenue</th> <th width="2%">gross_app_returns_amount</th> <th width="2%">iap_revenue</th> <th width="2%">iap_returns_amount</th> <th width="2%">gross_iap_revenue</th> <th width="2%">gross_iap_returns_amount</th> <th width="2%">subscription_revenue</th> <th width="2%">subscription_returns_amount</th> <th width="2%">gross_subscription_revenue</th> <th width="2%">gross_subscription_returns_amount</th> <th width="2%">pre_orders</th>
            
            
          </tr>
        </thead>

        <tbody>
   



@foreach ($reports as $report)
 
<tr>
<td>{{ $id+=1 }}</td>
<td>{{ env('APPFIGURE_APP_NAME') }}</td>
<td>{{ $report->start_date }}</td>
<td>{{ $report->end_date }}</td>

 <td>{{ $report->downloads}}</td> <td>{{ $report->re_downloads}}</td> <td>{{ $report->uninstalls}}</td> <td>{{ $report->updates}}</td> <td>{{ $report->returns}}</td> <td>{{ $report->net_downloads}}</td> <td>{{ $report->promos}}</td> <td>{{ $report->revenue}}</td> <td>{{ $report->returns_amount}}</td> <td>{{ $report->edu_downloads}}</td> <td>{{ $report->gifts}}</td> <td>{{ $report->gift_redemptions}}</td> <td>{{ $report->edu_revenue}}</td> <td>{{ $report->gross_revenue}}</td> <td>{{ $report->gross_returns_amount}}</td> <td>{{ $report->gross_edu_revenue}}</td> <td>{{ $report->business_downloads}}</td> <td>{{ $report->business_revenue}}</td> <td>{{ $report->gross_business_revenue}}</td> <td>{{ $report->standard_downloads}}</td> <td>{{ $report->standard_revenue}}</td> <td>{{ $report->gross_standard_revenue}}</td> <td>{{ $report->app_downloads}}</td> <td>{{ $report->app_returns}}</td> <td>{{ $report->iap_amount}}</td> <td>{{ $report->iap_returns}}</td> <td>{{ $report->subscription_purchases}}</td> <td>{{ $report->subscription_returns}}</td> <td>{{ $report->app_revenue}}</td> <td>{{ $report->app_returns_amount}}</td> <td>{{ $report->gross_app_revenue}}</td> <td>{{ $report->gross_app_returns_amount}}</td> <td>{{ $report->iap_revenue}}</td> <td>{{ $report->iap_returns_amount}}</td> <td>{{ $report->gross_iap_revenue}}</td> <td>{{ $report->gross_iap_returns_amount}}</td> <td>{{ $report->subscription_revenue}}</td> <td>{{ $report->subscription_returns_amount}}</td> <td>{{ $report->gross_subscription_revenue}}</td> <td>{{ $report->gross_subscription_returns_amount}}</td> <td>{{ $report->pre_orders}}</td>

</tr>
@endforeach
 </tbody>
</table>   


    
        </div>
    </div>
    <div style="height: 600px;">
    </div>
    </div>
@endsection

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/media-card.css') }}">
@endsection

@section('scripts')

@endsection