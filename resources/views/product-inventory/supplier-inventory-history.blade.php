@extends('layouts.app')

@section('title', 'Supplier Inventory History')

@section('large_content')
    <div class="row">
        <div class="col-md-12">
            <h2 class="page-heading">Supplier Inventory History</h2>
        </div>

        <div class="col-12">
          <div class="pull-left"></div>

          <div class="pull-right">
            <div class="form-group">
              &nbsp;
            </div>
          </div>
        </div>
    </div>

    @include('partials.flash_messages')






    <div class="row">
        <div class="col-md-12">
          
            <table id="table" class="table table-striped table-bordered">
                <thead>
                
                      
         

                    <tr>
                        <th>S.N</th>
                        <th>Supplier Name</th>
                        <th>Product Name</th>
                        @for($i = 0;$i < 7; $i++)

                        <th>{{\Carbon\Carbon::now()->subDays($i)->toDateString()}}</th>
                          @endfor
                      
                      
                    </tr>
                </thead>
                <tbody>
                    @foreach ($allHistory as $key=> $row ) 
                    <tr>
                      <td>{{$row->id}}</td>
                      <td>{{$row->supplier_name}}</td>
                     
                      <td>{{$row->product_name}}</td>

                       @for($i=0;$i < 7;$i++)



                       @php

                       $in_stock=' - ';

                       foreach($row->dates as $value)
                       {
                          if($value->date===\Carbon\Carbon::now()->subDays($i)->toDateString())
                          {
                             $in_stock=$value->in_stock;
                          }
                       }

                       @endphp

                        <td>{{$in_stock}}</td>
                          @endfor
                     
                    </tr>

                    @endforeach
                    <tr>
                         <td colspan="10">
        {{ $inventory->appends(request()->except("page"))->links() }}
    </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    
@endsection



