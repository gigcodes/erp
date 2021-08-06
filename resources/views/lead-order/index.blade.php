@extends('layouts.app')

@section('favicon' , 'lead.png')

@section('title', 'Lead and Order Pricing')

@section('content')

    <div class="row">

        <div class="col-lg-12 margin-tb">

            <h2 class="page-heading">Lead and Order Pricing</h2>

            <div class="row">

                <div class="col-md-10 col-sm-12">


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


    <script type="text/javascript">

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
