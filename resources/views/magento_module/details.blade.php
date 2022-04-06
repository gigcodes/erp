@extends('layouts.app')

@section('content')
    <div class="row mt-5">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2>Add {{ $title }}</h2>
            </div>
            <div class="pull-right mr-5">
                <a class="btn btn-primary" href="{{ route('magento_modules.index') }}"> Back</a>

            </div>
        </div>
    </div>

    <div class="row ml-2 mr-2">
        <div class="col-xs-6 col-sm-6">
            <div class="form-group">
                <strong>Module Category : </strong> {{ $magento_module->module_category->category_name }}
            </div>
        </div>

        <div class="col-xs-6 col-sm-6">
            <div class="form-group">
                <strong>Module Name : </strong> {{ $magento_module->module }}
            </div>
        </div>

        <div class="col-xs-6 col-sm-6">
            <div class="form-group">
                <strong>Current Version : </strong> {{ $magento_module->current_version }}
            </div>
        </div>
        <div class="col-xs-6 col-sm-6">
            <div class="form-group">
                <strong>Module Type : </strong> {{ $magento_module->module_type }}
            </div>
        </div>
        <div class="col-xs-6 col-sm-6">
            <div class="form-group">
                <strong>Payment Status : </strong> {{ $magento_module->payment_status == 'Free' ? 'Free' : 'Paid' }}
            </div>
        </div>
        <div class="col-xs-6 col-sm-6">
            <div class="form-group">
                <strong>Status : </strong> {{ $magento_module->status == 1 ? 'Enable' : 'Disabled' }}
            </div>
        </div>
        <div class="col-xs-6 col-sm-6">
            <div class="form-group">
                <strong>Developer Name : </strong> {{ $magento_module->developer_name }}
            </div>
        </div>
        <div class="col-xs-6 col-sm-6">
            <div class="form-group">
                <strong>Customized : </strong> {{ $magento_module->is_customized == 1 ? 'Yes' : 'No' }}
            </div>
        </div>
        <div class="col-xs-6 col-sm-6">
            <div class="form-group">
                <strong>Created At : </strong> {{ $magento_module->created_at->format('d-m-Y') }}
            </div>
        </div>
    </div>
@endsection
