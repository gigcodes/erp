@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-lg-12 margin-tb">
      <h2 class="page-heading">Products Push List</h2>
      <div class="pull-left">
        <form action="{{url('products/pushproductlist')}}" method="GET">
          <div class="form-group">
            <div class="row">
              <div class="col-md-3 pr-0">
                 
                <select name="website">
                    <option value="">Select Website</option>
                    @foreach($websiteList as $w)
                    @php
                      $sel='';
                      if (isset($_GET['website']) && $_GET['website']==$w->id)
                        $sel="selected='selected'";
                    @endphp
                       <option {{$sel}} value="{{$w->id}}">{{$w->title}}</option>
                    @endforeach
                </select>
                              
               </div>
               <div class="col-md-3 pr-0">
                <select name="category">
                    <option value="">Select Category</option>
                    @foreach($categoryList as $c)
                    @php
                      $sel='';
                      if (isset($_GET['category']) && $_GET['category']==$c->id)
                        $sel="selected='selected'";
                    @endphp
                       <option {{$sel}} value="{{$c->id}}">{{$c->title}}</option>
                    @endforeach
                </select>
                              
               </div>
               <div class="col-md-3 pr-0">
                <select name="brand">
                    <option value="">Select Brand</option>
                    @foreach($brandList as $b)
                    @php
                      $sel='';
                      if (isset($_GET['brand']) && $_GET['brand']==$b->id)
                        $sel="selected='selected'";
                    @endphp
                       <option {{$sel}} value="{{$b->id}}">{{$b->name}}</option>
                    @endforeach
                </select>
                              
               </div>
              <div class="col-md-3 pl-0">
                <button type="submit" class="btn btn-image"><img src="/images/search.png" /></button>
                
              </div>
            </div>
          </div>
        </form>
      </div>
      <div class="pull-right">
       
      </div>
    </div>
</div>


@if ($message = Session::get('success'))
<div class="alert alert-success">
  <p>{{ $message }}</p>
</div>
@endif

<div class="table-responsive">
  <table class="table table-bordered">
    <tr>
      <th>Product Id</th>
      <th>Product Name</th>
      <th>Category</th>
      <th>Brand</th>
      <th>Store Website </th>
      <th>Magento Url</th>
      <th>Published On</th>
      
    </tr>
    @foreach ($products as $product)
    <tr>
      <td>{{ $product->product_id }}</td>
      <td>{{ $product->product_name }}</td>
      <td>{{ $product->category }}</td>
      
      <td>{{ $product->brand }}</td>
      <td>{{ $product->store_website_name  }}</td>
      <td>{{ $product->store_website_url }}</td>
      <td>{{ $product->created_at }}</td>
      
    </tr>
    @endforeach
  </table>
</div>

{!! $products->appends(Request::except('page'))->links() !!}

<script type="text/javascript">
  

</script>

@endsection
