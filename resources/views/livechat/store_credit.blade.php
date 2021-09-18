@extends('layouts.app')
 
@section('styles')
 <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
@endsection
 
@section('content')
 
   <div class="row">
       <div class="col-lg-12 margin-tb">
           <h2 class="page-heading">Store Credit</h2>
           <div class="pull-left">
             <form class="form-inline" action="{{url('livechat/store-credit')}}" method="GET">
               <div class="col">
                 <div class="form-group">
                   <div class='input-group'>
                     <input type='text' placeholder="Search name" class="form-control" name="name"  value="{{ isset($_GET['name'])?$_GET['name']:''}}" />
                   
 
                   
                   </div>
                 </div>
               </div>
               <div class="col">
                 <div class="form-group">
                   <div class='input-group'>
                    
                     <input type='text' placeholder="Search Email" class="form-control" name="email"  value="{{ isset($_GET['email'])?$_GET['email']:''}}"  />
                    
 
                   
                   </div>
                 </div>
               </div>
               <div class="col">
                 <div class="form-group">
                   <div class='input-group'>
                    
                    
                     <input type='text' placeholder="Search phone" class="form-control" name="phone"  value="{{ isset($_GET['phone'])?$_GET['phone']:''}}"  />
 
                   
                   </div>
                 </div>
               </div>
 
               <div class="col">
                 <div class="form-group">
                   <div class='input-group'>
                     <select class="form-control" name="store_website" >
                       <option value="">Select Store Website</option>
                     @foreach($store_website as $s)
                       @php
                         $sel='';
                         if( isset($_GET['store_website']) && $_GET['store_website']==$s->id)
                             $sel="selected='selected'";
                       @endphp     
 
                     <option {{ $sel}} value="{{$s->id}}">{{$s->title}} </option>
                    @endforeach
                    </select>
                   
                   </div>
                 </div>
               </div>
 
              
 
               <div class="col">
                 <button type="submit" class="btn btn-image"><img src="/images/filter.png" /></button>
               </div>
             </form>
           </div>
           <div class="pull-right">
          
           </div>
       </div>
   </div>
 
   @include('partials.flash_messages')
 
   
 
   <div class="table-responsive mt-3">
     <table class="table table-bordered">
       <thead>
         <tr>
           <th>Name</th>
           <th>Email</th>
           <th>Phone</th>
           <th>Store Website</th>
           <th>Credit</th>
           <th>Utilised</th>
           <th>Balance</th>
          
           
         </tr>
       </thead>
 
       <tbody class="pending-row-render-view infinite-scroll-cashflow-inner">
         @foreach ($customers_all as $c)
           <tr>
             <td>{{ $c->name }}</td>
             <td>{{ $c->email }}</td>
             <td>{{ $c->phone }}</td>
             <td>{{ $c->title }}</td>
             <td>{{ $c->credit }}</td>
             <td>{{ $c->used_credit }}</td>
             <td>{{ $c->credit - $c->used_credit }}</td>
            
             
           
           </tr>
         @endforeach
      
 
      
 
       
       </tbody>
     </table>
   </div>
 
   <img class="infinite-scroll-products-loader center-block" src="{{asset('/images/loading.gif')}}" alt="Loading..." style="display: none" />
 
 
 
 
@endsection
 
@section('scripts')
 <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
 <script>
      
       var isLoading = false;
       var page = 1;
       $(document).ready(function () {
          
           $(window).scroll(function() {
               if ( ( $(window).scrollTop() + $(window).outerHeight() ) >= ( $(document).height() - 2500 ) ) {
                   loadMore();
               }
           });
 
           function loadMore() {
               if (isLoading)
                   return;
               isLoading = true;
               var $loader = $('.infinite-scroll-products-loader');
               page = page + 1;
               $.ajax({
                   url: "{{url('livechat/store-credit')}}?ajax=1&page="+page,
                   type: 'GET',
                   data: $('.form-search-data').serialize(),
                   beforeSend: function() {
                       $loader.show();
                   },
                   success: function (data) {
                      
                       $loader.hide();
                       if('' === data.trim())
                           return;
                       $('.infinite-scroll-cashflow-inner').append(data);
                      
 
                       isLoading = false;
                   },
                   error: function () {
                       $loader.hide();
                       isLoading = false;
                   }
               });
           }           
       });
 
     
 
 </script>  
 @endsection
 
 
