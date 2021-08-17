@extends('layouts.app')

@section('title', 'Magento Settings')

@section('content')

<div class="row">
    <div class="col-12">
        <h2 class="page-heading">Magento Settings</h2>
        <button class="btn btn-default" data-toggle="modal" data-target="#add-setting-popup">ADD Setting</button>
    </div>

    <div class="col-12 mb-3">

        <div class="pull-left"></div>
        <div class="pull-right"></div>
        <div class="col-12">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Website</th>
                            <th>Store</th>
                            <th>Store View</th>
                            <th>Scope</th>
                            <th>Name</th>
                            <th>Path</th>
                            <th>Value</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($magentoSettings as $magentoSetting)
                            <tr>
                                <td>{{ $magentoSetting->id }}</td>

                                @if($magentoSetting->scope === 'default')

                                        <td>{{ $magentoSetting->website->website }}</td>
                                        <td>-</td>
                                        <td>-</td>

                                @elseif($magentoSetting->scope === 'websites')
                                        
                                        <td>{{ $magentoSetting->store &&  $magentoSetting->store->website &&  $magentoSetting->store->website->storeWebsite ? $magentoSetting->store->website->storeWebsite->website : '-' }}</td>
                                        <td>{{ $magentoSetting->store->website->name }}</td>
                                        <td>-</td>
                                @else
                                        <td>{{ $magentoSetting->storeview && $magentoSetting->storeview->websiteStore && $magentoSetting->storeview->websiteStore->website ? $magentoSetting->storeview->websiteStore->website->storeWebsite->website : '-' }}</td>
                                        <td>{{ $magentoSetting->storeview && $magentoSetting->storeview->websiteStore ? $magentoSetting->storeview->websiteStore->name : '-' }}</td>
                                        <td>{{ $magentoSetting->storeview->code }}</td>
                                @endif

                                <td>{{ $magentoSetting->scope }}</td>
                                <td>{{ $magentoSetting->name }}</td>
                                <td>{{ $magentoSetting->path }}</td>
                                <td>{{ $magentoSetting->value }}</td>
                                <td></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<div id="add-setting-popup" class="modal fade" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form name="add-magento-setting-form" method="post" action="{{ route('magento.setting.create') }}">
                {{ csrf_field() }}
                <div class="modal-header">
                    <h5 class="modal-title">Add Magento Setting</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                        <label for="">Scope</label>
                        <div class="form-group form-check">
                                
                                <input class="form-check-input" type="radio" value="default" name="scope[]" checked>
                                <label class="form-check-label pl-4" for="flexCheckDefault">
                                  Default
                                </label>
                              </div>
                              <div class="form-group form-check">
                                <input class="form-check-input" type="radio" value="websites" name="scope[]" >
                                <label class="form-check-label pl-4" for="flexCheckChecked">
                                  Websites
                                </label>
                              </div>
                              <div class="form-group form-check">
                                <input class="form-check-input" type="radio" value="stores" name="scope[]" >
                                <label class="form-check-label pl-4" for="flexCheckChecked">
                                  Stores
                                </label>
                              </div>
                        
                    <div class="form-group">
                        <label for="">Name</label>
                        <input type="text" class="form-control" name="name" placeholder="Enter setting name">

                    </div>
                    <div class="form-group">
                        <label for="">Path</label>
                        <input type="text" class="form-control" name="path" placeholder="Enter setting path">

                    </div>
                    <div class="form-group">
                        <label for="">Value</label>
                        <input type="text" class="form-control" name="value" placeholder="Enter setting value">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary form-save-btn">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script type="text/javascript">
    $(document).on('submit', '[name="add-magento-setting-form"]', function(e) {
        e.preventDefault();

        let formData = new FormData(this);

        formData.append('_token', "{{ csrf_token() }}");

        $.ajax({
            url: $(this).attr('action'),
            method: $(this).attr('method'),
            data: formData,
            processData: false,
        contentType: false,
        enctype: 'multipart/form-data',
        //     dataType: 'json',
        }).done(function(response) {

        }).fail(function() {
            console.log("error");
        });

        return false;
    })
</script>
@endsection
