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
                                      {{$key}}
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

    
@endsection

