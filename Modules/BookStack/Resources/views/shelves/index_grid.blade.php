@extends('bookstack::tri-layout_grid')
@extends('bookstack::base')

@section('favicon' , 'shstid.png')

@section('title', 'Shelves')

@section('styles')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.5/css/bootstrap-select.min.css">
    <link rel="stylesheet" href="//cdn.datatables.net/1.10.7/css/jquery.dataTables.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
    <style type="text/css">
        .dis-none {
            display: none;
        } 

        #remark-list li {
            width: 100%;
            float: left;
        }

        .fixed_header {
            table-layout: fixed;
            border-collapse: collapse;
        }

        .fixed_header tbody {
            width: 100%;
            overflow: auto;
            height: 250px;
        }

        .fixed_header thead {
            background: black;
            color: #fff;
        }
        .modal-lg{
            max-width: 1500px !important; 
        }

        .remark-width{
            white-space: nowrap;
            overflow-x: auto;
            max-width: 20px;
        }

        .status .select2 .select2-selection{
            width:80px;
        }
    </style>
@endsection

@section('large_content')
        @php
            $user = auth()->user();
            $isAdmin = $user->isAdmin();
            $hod = $user->hasRole('HOD of CRM');
        @endphp

    <div class="row mb-5">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">Knowledge Base List <span class="total-info"></span></h2>
        </div>
    </div>

    @include('partials.flash_messages')
    <?php $status = request()->get('status', ''); ?>
    <?php $excelOnly = request()->get('excelOnly', ''); ?>
    <form action="#">
        <div class="row">
            <div class="form-group mb-3 col-md-2">
                <select name="pageType" class="form-control form-group select2 pageType">
                    <option value="shelves" selected>Shelves</option>
                    <option value="books">Books</option>
                </select> 
            </div> 
            <div class="form-group mb-3 col-md-2">
                <select name="sortByView" class="form-control form-group select2 sortByView">
                    <option value="">All</option> 
                    <option value="recent_viewed">Recently Viewed</option> 
                    <option value="popular_shelves">Popular Shelves</option> 
                    <option value="new_shelves">New Shelves</option> 
                </select>
            </div>  
            <div class="form-group mb-3 col-md-2">
                <select name="sortByDate" class="form-control form-group select2 sortByDate">
                    <option value="name">Name</option>
                    <option value="created_at">Created Date</option>
                    <option selected value="updated_at">Updated Date</option>
                </select>
            </div> 
            <div class="form-group mb-3 col-md-2">
                <button type="submit" class="btn btn-image filter_btn" ><img src="/images/filter.png"></button>
            </div>
        </div>
    </form>   
   <?php $totalCountedUrl = 0; ?>
    <div class="row no-gutters mt-3">
        <div class="col-md-12" id="plannerColumn">
            <div class="">
                <table class="table table-bordered table-striped sort-priority-scrapper">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th> 
                        <th>Description</th> 
                        <th>Created at</th> 
                        <th>Updated at</th> 
                        <th>Action</th> 
                    </tr>
                    </thead>
                    <tbody>
                    @php $i=1 @endphp
                    @foreach($shelves as $sh)
                    <tr>
                        <td width="5%">{{$i++}}</td> 
                        <td width="15%">{{$sh->name}}</td> 
                        <td width="15%">{{$sh->description}}</td> 
                        <td width="15%">{{$sh->created_at}}</td> 
                        <td width="15%">{{$sh->updated_at}}</td> 
                        <td width="50%">
                            <div style="float:left;">       
                                <button style="padding:1px;" type="button" class="btn btn-image d-inline" onclick="showShelf('${val.slug}')" title="Show Shelf"><i class="fa fa-eye"></i></button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                    </tbody>
                </table>  
            </div>
        </div>
    </div>

    <div id="loading-image" style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif') 
               50% 50% no-repeat;display:none;">
    </div>
@endsection

@section('scripts')
<script type="text/javascript" src="/js/bootstrap-datepicker.min.js"></script>
<script src="/js/jquery-ui.js"></script>
<script type="text/javascript">


    $('.sortByDate').select2();
    $('.sortByView').select2();
    $('.pageType').select2();
    $(document).on('change', '.pageType', function(){
        let pageType = $('.pageType').val();
        let html = '';
        if(pageType == 'shelves'){
            html = `<option value="">All</option> 
                        <option value="recent_viewed">Recently Viewed</option> 
                        <option value="popular_shelves">Popular Shelves</option> 
                        <option value="new_shelves">New Shelves</option>`;
        }else{
            html = `<option value="">All</option> 
                        <option value="recent_viewed">Recently Viewed</option> 
                        <option value="popular_books">Popular Books</option> 
                        <option value="new_books">New Books</option>`;
        }
        $('.sortByView').html(html);
        $('.sortByView').select2();
    });

    $('.filter_btn').click(function(e){
        e.preventDefault();
        let pageType = $('.pageType').val();
        let sortByDate = $('.sortByDate').val();
        let sortByView = $('.sortByView').val(); 
            $.ajax({
                url: `/kb/${pageType}/show/${sortByDate}/${sortByView ? sortByView : 'all'}`,
                type: 'GET',
                beforeSend: function () {
                    $("#loading-image").show();
                }
            }).done(function(response) {
                $("#loading-image").hide();
                let rows = [];
                if(sortByView !== null && sortByView == 'recent_viewed'){
                    rows = response.recents;  
                }else if(sortByView !== null && (sortByView == 'popular_shelves' || sortByView == 'popular_books')){
                    rows = response.popular;  
                }else if(sortByView !== null && (sortByView == 'new_shelves' || sortByView == 'new_books')){
                    rows = response.new;  
                }else{
                    pageType == 'books' ? rows = response.books.data : rows = response.shelves.data;
                }
                $("#plannerColumn tbody").empty();
                let id = 0;
                $.each(rows, function(index, val){
                    let html = `
                    <tr>
                            <td width="5%">${++id}</td> 
                            <td width="15%">${val.name}</td> 
                            <td width="15%">${val.description}</td> 
                            <td width="15%">${val.created_at}</td> 
                            <td width="15%">${val.updated_at}</td> 
                            <td width="50%">
                                <div style="float:left;">       
                                    <button style="padding:1px;" type="button" class="btn btn-image d-inline" onclick="show${pageType == 'books' ? 'Book' : 'Shelf'}('${val.slug}')" title="Show Shelf"><i class="fa fa-eye"></i></button>
                                </div>
                            </td>
                        </tr>
                    `;
                    $("#plannerColumn tbody").append(html);
                });
            }).fail(function() {
                $("#loading-image").hide();
                alert('Please check laravel log for more information')
            });

    });
  
  function showShelf(slug){
      window.location = '/kb/shelves/' + slug;
  }
  
  function showBook(slug){
      window.location = '/kb/books/' + slug;
  }

    
</script>
@endsection
