@extends('layouts.app')

@section('title', 'Google Web Master')

@section('large_content')
    <div class="row">
        <div class="col-md-12">
            <h2 class="page-heading">Google Web Master</h2>
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
                
                      <span><a class="btn btn-secondary pull-right m-2" href="{{route('googlewebmaster.get-access-token')}}"> Refresh Record</a></span>
                      


                    <tr>
                        <th>S.N</th>
                        <th>Site URL</th>
                        <th>Crawls</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($getSites as $key=> $site ) 
                    <tr>
                      <td>{{$site->id}}</td>
                      <td>{{$site->sites}}</td>
                      <td><button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#bulkWhatsappModal_{{$key}}">View Crawls</button>
                         <div id="bulkWhatsappModal_{{$key}}" class="modal fade" role="dialog">
                      <div class="modal-dialog modal-lg ">
                          <!-- Modal content-->
                          <div class="modal-content">
                              <div class="modal-header">
                                  <h4 class="modal-title">Coverage</h4>
                                  <button type="button" class="close" data-dismiss="modal">&times;</button>
                              </div>
                              <div class="modal-body">
                                  <div class="form-group">
                                      <label for="frequency">Error's</label>
                                      <a href="https://search.google.com/search-console/index?resource_id={{$site->sites}}" target="_blank">{{ $site->crawls }}</a>
                                  </div>
                              </div>
                              <div class="modal-footer">
                                  <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                              </div>
                          </div>
                      </div>
                  </div>
                      </td>
                      <td>Push | Down | Delete</td>
                     
                    </tr>

                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <h2 class="page-heading">Google Search Analytics</h2>
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

    <form method="get" action="{{route('googlewebmaster.index')}}">

     <div class="form-group">
                        <div class="row">
                            
                            

                            <div class="col-md-2">
                               <select class="form-control select-multiple" id="web-select" tabindex="-1" aria-hidden="true" name="site" onchange="//showStores(this)">
                                    <option value="">Select Site</option>

                                    @foreach($sites as $site)

                                    @if(isset($request->site) && $site->id==$request->site)

                                     <option value="{{$site->id}}" selected="selected">{{$site->site_url}}</option>


                                    @else

                                     <option value="{{$site->id}}">{{$site->site_url}}</option>


                                    @endif

                                   
                                         @endforeach
                                        </select>
                            </div>

                            <div class="col-md-2">
                                <input name="start_date" type="date" class="form-control" value="{{$request->start_date??''}}"  placeholder="Start Date" id="search">
                            </div>

                            <div class="col-md-2">
                                <input name="end_date" type="date" class="form-control" value="{{$request->end_date??''}}"  placeholder="End Date" id="search">
                            </div>

                             <div class="col-md-2">

                              <select class="form-control select-multiple" id="web-select" tabindex="-1" aria-hidden="true" name="device" onchange="//showStores(this)">
                                    <option value="">Select Device</option>

                                    @foreach($devices as $device)

                                    @if(isset($request->device) && $device->device==$request->device)

                                     <option value="{{$device->device}}" selected="selected">{{ucwords($device->device)}}</option>


                                    @else

                                    <option value="{{$device->device}}">
                                      {{ucwords($device->device)}}</option>


                                    @endif

                                   
                                         @endforeach
                                        </select>
                               
                            </div>

                            <div class="col-md-2">

                              <select class="form-control select-multiple" id="web-select" tabindex="-1" aria-hidden="true" name="country" onchange="//showStores(this)">
                                    <option value="">Select Country</option>
                                    <option value="all">All</option>

                                    @foreach($countries as $country)

                                    @if(isset($request->country) && $country->country==$request->country)

                                     <option value="{{$country->country}}" selected="selected">{{ucwords($country->country)}}</option>


                                    @else

                                    <option value="{{$country->country}}">
                                      {{ucwords($country->country)}}</option>


                                    @endif

                                   
                                         @endforeach
                                        </select>
                               
                            </div>
                            <div class="col-md-1 d-flex justify-content-between">
                               <button type="submit" class="btn btn-image" ><img src="/images/filter.png"></button><button type="button" onclick="resetForm(this)" class="btn btn-image" id=""><img src="/images/resend2.png"></button>  
                            </div>
                          <!--   <div class="col-md-1">
                                  
                            </div> -->
                        </div>

                    </div>

</form>

    <div class="row">
        <div class="col-md-12">
      
            <table id="table" class="table table-striped table-bordered">
                <thead>
                
                     <!--  <span><a class="btn btn-secondary pull-right m-2" href="{{route('googlewebmaster.get-access-token')}}"> Refresh Record</a></span> -->
                      
    @php 
         $currentQueries = $request->query();


    @endphp

                    <tr>
                        <th>S.N</th>
                        <th>Site URL</th>
                        <th>Country</th>
                        <th>Device</th>
                        <th>Query</th>
                        <th>Search Apperiance</th>
                        <th>Page</th>
                        @php
                          $clickType=$ctrType=$positionType=$impressionsType='asc';

                        if(isset($request->clicks) && $request->clicks=='asc'):
                        $clickType='desc';
                        endif;

                        if(isset($request->ctr) && $request->ctr=='asc'):
                        $ctrType='desc';
                        endif;

                        if(isset($request->position) && $request->position=='asc'):
                        $positionType='desc';
                        endif;

                        if(isset($request->impression) && $request->impression=='asc'):
                        $impressionsType='desc';
                        endif;

                        


                        $allQueries=array_merge($currentQueries,['clicks'=>$clickType,]);

                        $ctrURL=$request->fullUrlWithQuery(array_merge($currentQueries,['ctr'=>$ctrType]));

                        $positionURL=$request->fullUrlWithQuery(array_merge($currentQueries,['position'=>$positionType]));

                        $impressionsURL=$request->fullUrlWithQuery(array_merge($currentQueries,['impression'=>$impressionsType]));

                        $clicksURL=$request->fullUrlWithQuery($allQueries);

                        @endphp
                        <th style="text-align:center;">Clicks
                          <a style="color:black;" href="{{$clicksURL}}">
                          @if($clickType=='asc')

                          <i class="fa fa-angle-down" ></i>
                          @else
                          <i class="fa fa-angle-up"></i>
                          @endif
                        </a>
                      </th>
                       <th style="text-align:center;">Ctr
                          <a style="color:black;" href="{{$ctrURL}}">
                          @if($ctrType=='asc')

                          <i class="fa fa-angle-down" ></i>
                          @else
                          <i class="fa fa-angle-up"></i>
                          @endif
                        </a>
                      </th>
                        <th style="text-align:center;">Position
                          <a style="color:black;" href="{{$positionURL}}">
                          @if($positionType=='asc')

                          <i class="fa fa-angle-down" ></i>
                          @else
                          <i class="fa fa-angle-up"></i>
                          @endif
                        </a>
                      </th>
                        <th style="text-align:center;">Impression
                          <a style="color:black;" href="{{$impressionsURL}}">
                          @if($impressionsType=='asc')

                          <i class="fa fa-angle-down" ></i>
                          @else
                          <i class="fa fa-angle-up"></i>
                          @endif
                        </a>
                      </th>
                        
                        <th>Date</th>


                   
                    </tr>
                </thead>
                <tbody>
                    @foreach ($sitesData as $key=> $row ) 
                    <tr>
                      <td>{{$row->id}}</td>

                      <td>{{$row->site->site_url}}</td>
                      <td>{{$row->country}}</td>
                       <td>{{$row->device}}</td>
                      <td>{{$row->query}}</td>
                      <td>{{$row->search_apperiance}}</td>

                      <td>{{$row->page}}</td>
                      <td>{{$row->clicks}}</td>
                      <td>{{$row->ctr}}</td>
                      <td>{{$row->position}}</td>
                      <td>{{$row->impressions}}</td>


                      <td>{{$row->date}}</td>

                     

                   
                      
                     
                    </tr>

                    @endforeach

                    <tr>
    <td colspan="12">
        {{ $sitesData->appends(request()->except("page"))->links() }}
    </td>
</tr>
                </tbody>
            </table>
        </div>
    </div>


<div class="row">
        <div class="col-md-12">
            <h2 class="page-heading">Sites Logs</h2>
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

    <div class="row">
        <div class="col-md-12">
          
            <table id="table" class="table table-striped table-bordered">
                <thead>
                
                      
         

                    <tr>
                        <th>S.N</th>
                        <th>Name</th>
                        <th>Description</th>
                      
                    </tr>
                </thead>
                <tbody>
                    @foreach ($logs as $key=> $log ) 
                    <tr>
                      <td>{{$log->id}}</td>
                      <td>{{$log->log_name}}</td>
                     
                      <td>{{$log->description}}</td>
                     
                    </tr>

                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    
@endsection

@section('scripts')

<script type="text/javascript">
 

    function resetForm(selector)
        {
            
           $(selector).closest('form').find('input,select').val('');

           $(selector).closest('form').submit();
        }



</script>

@endsection

