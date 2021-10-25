@extends('layouts.app')

@section('title', 'Google Server List')

@section('content')

    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">Google Translation Settings List</h2>
            <div class="pull-left col-md-12">
                <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#googleServerCreateModal"><i class="fa fa-plus"></i></button>
            </div>
        </div>
    </div>

    @include('partials.flash_messages')
    <div id="addGooleSetting" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content ">
                <form class="add_translation_language" action="{{ route('google-traslation-settings.store') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h4 class="modal-title">Add Goole Translation Setting</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                         <!-- email , account_json , status, last_note , created_at -->
                        <div class="form-group">
                            <strong>Email:</strong>
                            <input type="text" name="email" class="form-control" value="{{ old('email') }}">

                            @if ($errors->has('email'))
                            <div class="alert alert-danger">{{$errors->first('email')}}</div>
                            @endif
                        </div>

                        <div class="form-group">
                            <strong>Account JSON:</strong>
                            <textarea class="form-control" name="account_json" value="{{ old('account_json') }}" required>
                            </textarea>
                            
                            @if ($errors->has('account_json'))
                            <div class="alert alert-danger">{{$errors->first('account_json')}}</div>
                            @endif
                        </div>

                        <div class="form-group">
                            <strong>Status:</strong>
                            <input type="text" name="status" class="form-control" value="{{ old('status') }}" required>

                            @if ($errors->has('status'))
                            <div class="alert alert-danger">{{$errors->first('status')}}</div>
                            @endif
                        </div>

                        <div class="form-group">
                            <strong>Last Note:</strong>
                            <input type="text" name="last_note" class="form-control" value="{{ old('last_note') }}" required>

                            @if ($errors->has('last_note'))
                            <div class="alert alert-danger">{{$errors->first('last_note')}}</div>
                            @endif
                        </div>



                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-secondary">Store</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="mt-3 col-md-12">
      <table class="table table-bordered table-striped">
        <thead>
          <tr>
            <th width="5%">ID</th>
            <th width="10%">Email</th>
            <th width="30%">Account JSON</th>
            <th width="5%">status</th>
            <th width="5%">Last Note</th>
            <th width="10%">Action</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($settings as $setting)
            <tr>
              <td>{{ $setting->id }}</td>
              <td>
                {{ $setting->email }}
              </td>
              <td>
                {!! ($setting->account_json )?"<span class='lesstext'>".(\Illuminate\Support\Str::limit($setting->account_json , 10, '<a href="javascript:void(0)" class="readmore btn btn-xs text-dark">...<i class="fa fa-plus" aria-hidden="true"></i></a>'))."</span>":"-" !!}
                {!! ($setting->account_json )?"<span class='alltext' style='display:none;'>".$setting->account_json ."<a href='javascript:void(0)' class='readless btn btn-xs text-dark'>...<i class='fa fa-minus' aria-hidden='true'></i></a></span>":"-" !!}
                <?php if(!empty($setting->account_json)){ ?>
                  <button class="btn btn-xs text-dark" onclick="copyDataToClipBoard('<?php echo $setting->account_json; ?>')"><i class="fa fa-copy"></i></button>
                <?php } ?>
              </td>
              <td>
                {{ $setting->status }}
              </td>
              <td>
                {{ $setting->last_note }}
              </td>
              <td>
                <a href="{{ route('google-traslation-settings.edit',$setting->id) }}" class="btn btn-xs text-dark pull-left"><i class="fa fa-edit"></i></a>
                <form action="{{ route('google-traslation-settings.destroy', $setting->id) }}" method="POST" onsubmit="return confirm('Are you sure?');">
                    <input type="hidden" name="setting" value="{{ $setting->id }}">
                    <input type="hidden" name="_method" value="DELETE">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <button type="submit" class="btn btn-xs text-dark pull-left"><i class="fa fa-trash"></i></button>
                </form>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
@endsection

@section('scripts')
<script>
    $(document).on('click', '.readmore', function() {
        $(this).parent('.lesstext').hide();
        $(this).parent('.lesstext').next('.alltext').show();
    });
    $(document).on('click', '.readless', function() {
        $(this).parent('.alltext').hide();
        $(this).parent('.alltext').prev('.lesstext').show();
    });
    function copyDataToClipBoard(data) {
      navigator.clipboard.writeText(data);
    }
</script>
@endsection
