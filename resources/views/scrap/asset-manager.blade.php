@extends('layouts.app')

@section('title', 'Broadcast Report')

@section('styles')
  <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.5/css/bootstrap-select.min.css">
@endsection

@section('content')

  <div class="row mb-5">
      <div class="col-lg-12 margin-tb">
          <h2 class="page-heading">Asset Manager </h2>

          <div class="pull-left">
            <form class="form-inline" action="" method="GET">
              
               <div class="form-group ml-3">
               <input type='text' class="form-control" name="search" placeholder="Search" required />
              </div>

              <div class="form-group ml-3">
               <select name="status" class="form-control">
                  <option>Select Category</option>
                  <option>Sucess</option>
                  <option>Failed</option>
              </select> 
              </div>

              

              <button type="submit" class="btn btn-secondary ml-3">Submit</button>
            </form>
          </div>

          <div class="pull-right mt-4">
           
          </div>
      </div>
  </div>

  @include('partials.flash_messages')

    

    <div class="row no-gutters mt-3">
      <div class="col-xs-12 col-md-12" id="plannerColumn">
        <div class="table-responsive">
          <table class="table table-bordered table-sm">
            <thead>
              <tr>
                <th>Sr. No</th>
                <th>Name</th>
                <th>Type </th>
                <th>Category</th>
                <th>Purchase Type</th>
                <th>Payment Cycle</th>
                <th>Amount</th>
                <th>Remarks</th>
              </tr>
            </thead>

            <tbody>
              
                                                     
              <tr>
                    <td class="p-2"></td>
                    <td class="p-2"></td>
                    <td class="p-2"></td>
                    <td class="p-2"></td>
                    <td class="p-2"></td>
                    <td class="p-2"></td>
                    <td class="p-2"></td>
                    <td class="p-2"></td>
              <tr>      
                  
            
                               
                
            </thead>

            <tbody>
                          </tbody>
          </table>
        </div>
      </div>
    </div> 
  
  @endsection

@section('scripts')
  <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.min.js" type="text/javascript"></script>
  <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.5/js/bootstrap-select.min.js"></script>


@endsection
