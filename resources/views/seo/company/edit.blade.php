@extends('layouts.app')

@section('content')
<div class="page-heading">Edit {{$moduleName}}</div>
<div class="container-fluid">
    <div class="mt-3">
        <div class="">
            <form action="{{ route('seo.company.update', $seoCompany->id) }}" method="POST" autocomplete="off" id="seoForm"> @csrf
                <div class="row">
                    <div class="form-group col-md-4">
                        <label class="form-label">Type</label>
                        <input type="hidden" value="" name="type_id">
                        <select name="type" class="form-control">
                            <option value="">-- SELECT --</option>
                            @foreach ($companyTypes as $item)
                                <option value="{{ $item->id }}" {{ $seoCompany->company_type_id == $item->id ? 'selected' : '' }}>{{ $item->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="form-group col-md-4 mt-3">
                        <label class="form-label">Website</label>
                        <select name="website_id" class="form-control">
                            <option value="">-- SELECT --</option>
                            @foreach ($webistes as $item)
                                <option value="{{ $item->id }}" {{ $seoCompany->website_id == $item->id ? 'selected' : '' }}>{{ $item->website }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="form-group col-md-4 mt-3">
                        <label class="form-label">DA</label>
                        <input type="text" name="da" class="form-control" value="{{ $seoCompany->da }}" >
                    </div>
                </div>

                <div class="row">
                    <div class="form-group col-md-4 mt-3">
                        <label class="form-label">PA</label>
                        <input type="text" name="pa" class="form-control" value="{{ $seoCompany->pa }}">
                    </div>
                </div>

                <div class="row">
                    <div class="form-group col-md-4 mt-3">
                        <label class="form-label">SS</label>
                        <input type="text" name="ss" class="form-control" value="{{ $seoCompany->ss }}" >
                    </div>
                </div>

                <div class="row">
                    <div class="form-group col-md-4 mt-3">
                        <label class="form-label">Username & Password</label>
                        <select name="email_address_id" class="form-control">
                            <option value="">-- SELECT --</option>
                            @foreach ($emailAddresses as $item)
                                <option value="{{ $item->id }}" {{ $seoCompany->email_address_id == $item->id ? 'selected' : '' }} >{{ 'username:' . $item->username . ', password' . $item->password }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="form-group col-md-4 mt-3">
                        <label class="form-label">Live link</label>
                        <input type="text" name="live_link" class="form-control" value="{{ $seoCompany->live_link }}" >
                    </div>
                </div>

                <hr>
                <div class="row mt-3">
                    <div class="col-md-12">
                        <a href="{{ route('seo.company.index') }}" class="btn btn-notification">Cancel</a>
                        <button type="submit" class="btn btn-secondary">Submit</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>
<script>
    $(document).ready(function () {
        $("select[name=type]").select2({
            tags: true,
        });

        $("select[name=type]").on("select2:select", function(e) {
            let data = e.params.data;
            $.ajax({
                type: "POST",
                url: `{{ route('seo.content-type.store')}}`,
                data: {
                    name: data.text,
                    _token:`{{ csrf_token() }}`
                },
                dataType: "json",
                success: function (response) {
                    $(document).find("input[name=type_id]").val(response.data.id)
                }
            });
        });

        $('#seoForm').validate({
            rules: {
                'type':{
                    required:true,
                },
                'website_id':{
                    required:true,
                },
                'da':{
                    required:true,
                },
                'pa':{
                    required:true,
                },
                'ss':{
                    required:true,
                },
                'email_address_id':{
                    required:true,
                },
                'live_link':{
                    required:true,
                },
            },
            messages: {
                'type':{
                    required:"Please select type.",
                },
                'website_id':{
                    required:"Please select website.",
                },
                'da':{
                    required:"Please enter DA.",
                },
                'pa':{
                    required:"Please enter PA.",
                },
                'ss':{
                    required:"Please enter SS.",
                },
                'email_address_id':{
                    required:"Please select username & password.",
                },
                'live_link':{
                    required:"Please enter live link.",
                },
            },
        });
    });
</script>
@endsection