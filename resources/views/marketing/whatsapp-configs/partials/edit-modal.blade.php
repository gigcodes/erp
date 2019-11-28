<div id="whatsAppConfigEditModal{{$whatsAppConfig->id}}" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <form action="{{ route('whatsapp.config.edit') }}" method="POST">
                    @csrf

                    <div class="modal-header">
                        <h4 class="modal-title">Edit Whats App Config</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id" value="{{$whatsAppConfig->id}}">
                        <div class="form-group">
                            <strong>Username:</strong>
                            <input type="text" name="username" class="form-control" value="{{ $whatsAppConfig->username }}" required>

                            @if ($errors->has('username'))
                                <div class="alert alert-danger">{{$errors->first('username')}}</div>
                            @endif
                        </div>
                            <input type="hidden" name="id" value="{{ $whatsAppConfig->id }}"/>
                        <div class="form-group">
                            <strong>Password:</strong>
                            <input type="text" name="password" class="form-control" value="{{ Crypt::decrypt($whatsAppConfig->password) }}" required>

                            @if ($errors->has('password'))
                                <div class="alert alert-danger" >{{$errors->first('password')}}</div>
                            @endif
                        </div>
                        <div class="form-group">
                            <strong>Number:</strong>
                            <input type="text" name="number" class="form-control" value="{{ $whatsAppConfig->number }}">

                            @if ($errors->has('number'))
                                <div class="alert alert-danger" >{{$errors->first('number')}}</div>
                            @endif
                        </div>
                        <div class="form-group">
                            <strong>Provider:</strong>
                            <input type="text" name="provider" class="form-control" value="{{ $whatsAppConfig->provider }}">

                            @if ($errors->has('provider'))
                                <div class="alert alert-danger" >{{$errors->first('provider')}}</div>
                            @endif
                        </div>
                        <div class="form-group">
                            <strong>Customer Support:</strong>
                            <select class="form-control" name="customer_support">
                                <option value="1" @if($whatsAppConfig->is_customer_support == 1) selected @endif>Yes</option>
                                <option value="0" @if($whatsAppConfig->is_customer_support == 0) selected @endif>No</option>
                            </select>
                            @if ($errors->has('is_customer_support'))
                                <div class="alert alert-danger" >{{$errors->first('is_customer_support')}}</div>
                            @endif
                        </div>
                        <div class="form-group">
                            <strong>Frequency:</strong>
                            <input type="text" name="frequency" class="form-control" value="{{ $whatsAppConfig->frequency }}">
                            @if ($errors->has('frequency'))
                                <div class="alert alert-danger" >{{$errors->first('frequency')}}</div>
                            @endif
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-secondary">Update</button>
                    </div>
                </form>
            </div>

        </div>
    </div>