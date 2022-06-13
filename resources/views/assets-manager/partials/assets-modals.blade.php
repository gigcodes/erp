
<div id="assetsCreateModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <form action="{{ route('assets-manager.store') }}" method="POST">
        @csrf

        <div class="modal-header">
          <h4 class="modal-title">Store a Assets Manager</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <strong>Name:</strong>
            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>

            @if ($errors->has('name'))
              <div class="alert alert-danger">{{$errors->first('name')}}</div>
            @endif
          </div>
          <div class="form-group">
            <strong>Capacity:</strong>
            <input type="text" name="capacity" class="form-control" value="{{ old('capacity') }}">

            @if ($errors->has('capacity'))
              <div class="alert alert-danger">{{$errors->first('capacity')}}</div>
            @endif
          </div>

          <div class="form-group">
            <strong>User Name:</strong>
            <input type="text" name="user_name"  class="form-control" value="{{ old('user_name') }}">

            @if ($errors->has('user_name'))
              <div class="alert alert-danger">{{$errors->first('user_name')}}</div>
            @endif
          </div>

          <div class="form-group">
            <strong>Password:</strong>
            <input type="text" name="password" class="form-control" value="{{ old('password') }}" >

            @if ($errors->has('password'))
              <div class="alert alert-danger">{{$errors->first('password')}}</div>
            @endif
          </div>

          <div class="form-group">
            <strong>IP:</strong>
            <input type="text" name="ip"  class="form-control" value="{{ old('ip') }}">

            @if ($errors->has('ip'))
              <div class="alert alert-danger">{{$errors->first('ip')}}</div>
            @endif
          </div>

          <div class="form-group">
            <strong>Assigen to</strong>
            <select class="form-control select-multiple" name="assigned_to" >
              <option value="">Select</option>
              @foreach($users as $user)
                <option value="{{$user->id}}" {{ $user->id == old('assigned_to') ? 'selected' : '' }}>{{$user->name}}</option>
              @endforeach
          </select>
            @if ($errors->has('assigned_to'))
              <div class="alert alert-danger">{{$errors->first('assigned_to')}}</div>
            @endif
          </div>

          <div class="form-group">
            <strong>Asset Type:</strong>
            <select class="form-control" name="asset_type" id="asset_type">
                <option value="">Select</option>
                <option value="Hard" {{ Input::old('asset_type') == 'Hard'? 'selected' : '' }}>Hard</option>
                <option value="Soft" {{ Input::old('asset_type') == 'Soft'? 'selected' : '' }}>Soft</option>
            </select>
            @if ($errors->has('asset_type'))
              <div class="alert alert-danger">{{$errors->first('asset_type')}}</div>
            @endif
          </div>


          <div class="form-group">
            <strong>Category:</strong>
            <select class="form-control" name="category_id" id="category_id">
                <option value="">Select</option>
                @foreach($category as $cat)
                  <option value="{{$cat->id}}" {{ $cat->id == old('category_id') ? 'selected' : '' }}>{{$cat->cat_name}}</option>
                @endforeach
                <option value="-1" {{ old('category_id') == '-1'? 'selected' : '' }}>Other</option>
            </select>
            @if ($errors->has('category_id'))
              <div class="alert alert-danger">{{$errors->first('category_id')}}</div>
            @endif
          </div>


          <div class="form-group othercat" style="display: none;" >
            <input type="text" name="other" class="form-control" value="{{ old('other') }}">
          </div>

          <div class="form-group">
            <strong>Provider Name:</strong>
            <input type="text" name="provider_name" class="form-control" value="{{ old('provider_name') }}" required>

            @if ($errors->has('provider_name'))
              <div class="alert alert-danger">{{$errors->first('provider_name')}}</div>
            @endif
          </div>

          <div class="form-group">
            <strong>Purchase Type:</strong>
            <select class="form-control" name="purchase_type" id="purchase_type">
                <option value="">Select</option>
                <option value="Owned" {{ old('purchase_type') == 'Owned'? 'selected' : '' }}>Owned</option>
                <option value="Rented" {{ old('purchase_type') == 'Rented'? 'selected' : '' }}>Rented</option>
                <option value="Subscription" {{ old('purchase_type') == 'Subscription'? 'selected' : '' }}>Subscription</option>
            </select>
            @if ($errors->has('purchase_type'))
              <div class="alert alert-danger">{{$errors->first('purchase_type')}}</div>
            @endif
          </div>

          <div class="form-group">
            <strong>Payment Cycle:</strong>
            <select class="form-control" name="payment_cycle" id="payment_cycle">
                <option value="">Select</option>
                <option value="Daily" {{ old('payment_cycle') == 'Daily'? 'selected' : '' }}>Daily</option>
                <option value="Weekly" {{ old('payment_cycle') == 'Weekly'? 'selected' : '' }}>Weekly</option>
                <option value="Bi-Weekly" {{ old('payment_cycle') == 'Bi-Weekly'? 'selected' : '' }}>Bi-Weekly</option>
                <option value="Monthly" {{ old('payment_cycle') == 'Monthly'? 'selected' : '' }}>Monthly</option>
                <option value="Yearly" {{ old('payment_cycle') == 'Yearly'? 'selected' : '' }}>Yearly</option>
                <option value="One time" {{ old('payment_cycle') == 'One time'? 'selected' : '' }}>One time</option>
            </select>
            @if ($errors->has('payment_cycle'))
              <div class="alert alert-danger">{{$errors->first('payment_cycle')}}</div>
            @endif
          </div>

          <div class="form-group">
            <strong>Amount:</strong>
            <input type="number" name="amount" class="form-control" value="{{ old('amount') }}">

            @if ($errors->has('amount'))
              <div class="alert alert-danger">{{$errors->first('amount')}}</div>
            @endif
          </div>

          <div class="form-group">
            <strong>Start Date:</strong>
            <input type="date" name="start_date" id="start_date" class="form-control" value="{{ old('start_date') }}">

            @if ($errors->has('start_date'))
              <div class="alert alert-danger">{{$errors->first('start_date')}}</div>
            @endif
          </div>

          <div class="form-group">
            <strong>Currency:</strong>
            <input type="text" name="currency" class="form-control" value="{{ old('currency') }}" required>

            @if ($errors->has('currency'))
              <div class="alert alert-danger">{{$errors->first('currency')}}</div>
            @endif
          </div>

          <div class="form-group">
            <strong>Location:</strong>
            <input type="text" name="location" class="form-control" value="{{ old('location') }}">

            @if ($errors->has('location'))
              <div class="alert alert-danger">{{$errors->first('location')}}</div>
            @endif
          </div>

          <div class="form-group">
            <strong>Usage:</strong>
            <input type="text" name="usage" class="form-control" value="{{ old('usage') }}">

            @if ($errors->has('usage'))
              <div class="alert alert-danger">{{$errors->first('usage')}}</div>
            @endif
          </div>


        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-secondary">Add</button>
        </div>
      </form>
    </div>

  </div>
</div>

<div id="cashflows" class="modal fade" role="dialog" >
  <div class="modal-dialog" style="max-width:100%;width:70%">

    <!-- Modal content-->
    <div class="modal-content">


        <div class="modal-header">
          <h4 class="modal-title">All Cash Flows</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
        <div class="mt-3 col-md-12">
      <table class="table table-responsive table-bordered table-striped">
        <thead>
          <tr>
            <th width="3%">ID</th>
            <th width="5%">User</th>
            <th width="5%">Date</th>
            <th width="5%">Desc</th>
            <th width="5%">Amt</th>
            <th width="7%">E Amt</th>
            <th width="10%">E EU Amt</th>
            <th width="8%">EU Amt</th>
            <th width="11%">DU EU Amt</th>
            <th width="5%">Type</th>
            <th width="4%">Curr</th>
            <th width="7%">Ast Id</th>
            <th width="8%">Ast Typ</th>
            <th width="8%">Ord Sts</th>
            <th width="5%">Created</th>
          </tr>
        </thead>

        <tbody>
          @php $i=1; @endphp
          @foreach ($cashflows as $cash)
            <tr>
              <td>{{ $i }}</td>
              <td class="expand-row-msg" data-name="user_id" data-id="{{$i}}">
                <span class="show-short-user_id-{{$i}}">{{ str_limit($cash->user_id, 3, '..')}}</span>
                <span style="word-break:break-all;" class="show-full-user_id-{{$i}} hidden">{{$cash->user_id}}</span>
              </td>
              <td class="expand-row-msg" data-name="date" data-id="{{$i}}">
                <span class="show-short-date-{{$i}}">{{ str_limit($cash->date, 3, '..')}}</span>
                <span style="word-break:break-all;" class="show-full-date-{{$i}} hidden">{{$cash->date}}</span>
              </td>
              <td class="expand-row-msg" data-name="description" data-id="{{$i}}">
                <span class="show-short-description-{{$i}}">{{ str_limit($cash->description, 4, '..')}}</span>
                <span style="word-break:break-all;" class="show-full-description-{{$i}} hidden">{{$cash->description}}</span>
              </td>
              <td>{{ $cash->amount }}</td>
              <td>{{ $cash->erp_amount??'N/A' }}</td>

              <td>{{ $cash->erp_eur_amount??'N/A' }}</td>
              <td>{{ $cash->amount_eur??'N/A' }}</td>
              <td>{{ $cash->due_amount_eur??'N/A' }}</td>
              <td class="expand-row-msg" data-name="type" data-id="{{$i}}">
                <span class="show-short-type-{{$i}}">{{ str_limit($cash->type, 3, '..')}}</span>
                <span style="word-break:break-all;" class="show-full-type-{{$i}} hidden">{{$cash->type}}</span>
              </td>
              <td>{{ $cash->currency }}</td>
              <td>{{ $cash->cash_flow_able_id }}</td>
              <td class="expand-row-msg" data-name="cash_flow_able_type" data-id="{{$i}}">
                <span class="show-short-cash_flow_able_type-{{$i}}">{{ str_limit($cash->cash_flow_able_type, 4, '..')}}</span>
                <span style="word-break:break-all;" class="show-full-cash_flow_able_type-{{$i}} hidden">{{$cash->cash_flow_able_type}}</span>
              </td>
              <td class="expand-row-msg" data-name="order_status" data-id="{{$i}}">
                <span class="show-short-order_status-{{$i}}">{{ str_limit($cash->order_status, 3, '..')}}</span>
                <span style="word-break:break-all;" class="show-full-order_status-{{$i}} hidden">{{$cash->order_status}}</span>
              </td>
              <td class="expand-row-msg" data-name="created_at" data-id="{{$i}}">
                <span class="show-short-created_at-{{$i}}">{{ str_limit($cash->created_at->format('Y-m-d'), 3, '..')}}</span>
                <span style="word-break:break-all;" class="show-full-created_at-{{$i}} hidden">{{$cash->created_at->format('Y-m-d')}}</span>
              </td>
            </tr>
            @php $i++; @endphp
          @endforeach
        </tbody>
      </table>
    </div>

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>

    </div>

  </div>
</div>

<div id="assetsEditModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <form action="" method="POST">
        @csrf
        @method('PUT')

        <div class="modal-header">
          <h4 class="modal-title">Update a Assets Manager</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <strong>Name:</strong>
            <input type="text" name="name" class="form-control" value="{{ old('name') }}" id="asset_name" required>

            @if ($errors->has('name'))
              <div class="alert alert-danger">{{$errors->first('name')}}</div>
            @endif
          </div>
          <div class="form-group">
            <strong>Capacity:</strong>
            <input type="text" name="capacity" id="capacity" class="form-control" value="{{ old('capacity') }}">

            @if ($errors->has('capacity'))
              <div class="alert alert-danger">{{$errors->first('capacity')}}</div>
            @endif
          </div>

          <div class="form-group">
            <strong>User Name:</strong>
            <input type="text" name="user_name"  id="user_name"  class="form-control" value="{{ old('user_name') }}">
            <input type="hidden" name="old_user_name"  id="old_user_name"  class="form-control" value="{{ old('old_user_name') }}">
            @if ($errors->has('user_name'))
              <div class="alert alert-danger">{{$errors->first('user_name')}}</div>
            @endif
          </div>

          <div class="form-group">
            <strong>Password:</strong>
            <input type="text" name="password" class="form-control" value="{{ old('password') }}" id="password" required>
            <input type="hidden" name="old_password" class="form-control" value="{{ old('old_password') }}" id="old_password">
            @if ($errors->has('password'))
              <div class="alert alert-danger">{{$errors->first('password')}}</div>
            @endif
          </div>

          <div class="form-group">
            <strong>IP:</strong>
            <input type="text" name="ip" id="ip" class="form-control" value="{{ old('ip') }}">
            <input type="hidden" name="old_ip" class="form-control" value="{{ old('old_ip') }}" id="old_ip">
            @if ($errors->has('ip'))
              <div class="alert alert-danger">{{$errors->first('ip')}}</div>
            @endif
          </div>

          <div class="form-group">
            <strong>Assigen to</strong>
            <select class="form-control select-multiple" name="assigned_to" id="assigned_to">
              <option value="">Select</option>
              @foreach($users as $user)
                <option value="{{$user->id}}" {{ $user->id == old('assigned_to') ? 'selected' : '' }}>{{$user->name}}</option>
              @endforeach
          </select>
            @if ($errors->has('assigned_to'))
              <div class="alert alert-danger">{{$errors->first('assigned_to')}}</div>
            @endif
          </div>

          <div class="form-group">
            <strong>Asset Type:</strong>
            <select class="form-control" name="asset_type" id="asset_asset_type">
                <option value="">Select</option>
                <option value="Hard" {{ Input::old('asset_type') == 'Hard'? 'selected' : '' }}>Hard</option>
                <option value="Soft" {{ Input::old('asset_type') == 'Soft'? 'selected' : '' }}>Soft</option>
            </select>
            @if ($errors->has('asset_type'))
              <div class="alert alert-danger">{{$errors->first('asset_type')}}</div>
            @endif
          </div>


          <div class="form-group">
            <strong>Category:</strong>
            <select class="form-control" name="category_id" id="category_id2">
                <option value="">Select</option>
                @foreach($category as $cat)
                  <option value="{{$cat->id}}" {{ $cat->id == old('category_id') ? 'selected' : '' }}>{{$cat->cat_name}}</option>
                @endforeach
                <option value="-1" {{ old('category_id') == '-1'? 'selected' : '' }}>Other</option>
            </select>
            @if ($errors->has('category_id'))
              <div class="alert alert-danger">{{$errors->first('category_id')}}</div>
            @endif
          </div>
          <div class="form-group othercatedit" style="display: none;" >
            <input type="text" name="other" class="form-control" value="{{ old('other') }}">
          </div>

          <div class="form-group">
            <strong>Provider Name:</strong>
            <input type="text" name="provider_name" class="form-control" value="{{ old('provider_name') }}" id="provider_name" required>

            @if ($errors->has('provider_name'))
              <div class="alert alert-danger">{{$errors->first('provider_name')}}</div>
            @endif
          </div>


          <div class="form-group">
            <strong>Purchase Type:</strong>
            <select class="form-control" name="purchase_type" id="asset_purchase_type">
                <option value="">Select</option>
                <option value="Owned" {{ old('purchase_type') == 'Owned'? 'selected' : '' }}>Owned</option>
                <option value="Rented" {{ old('purchase_type') == 'Rented'? 'selected' : '' }}>Rented</option>
                <option value="Subscription" {{ old('purchase_type') == 'Subscription'? 'selected' : '' }}>Subscription</option>
            </select>
            @if ($errors->has('purchase_type'))
              <div class="alert alert-danger">{{$errors->first('purchase_type')}}</div>
            @endif
          </div>

          <div class="form-group">
            <strong>Payment Cycle:</strong>
            <select class="form-control" name="payment_cycle" id="asset_payment_cycle">
                <option value="">Select</option>
                <option value="Daily" {{ old('payment_cycle') == 'Daily'? 'selected' : '' }}>Daily</option>
                <option value="Weekly" {{ old('payment_cycle') == 'Weekly'? 'selected' : '' }}>Weekly</option>
                <option value="Bi-Weekly" {{ old('payment_cycle') == 'Bi-Weekly'? 'selected' : '' }}>Bi-Weekly</option>
                <option value="Monthly" {{ old('payment_cycle') == 'Monthly'? 'selected' : '' }}>Monthly</option>
                <option value="Yearly" {{ old('payment_cycle') == 'Yearly'? 'selected' : '' }}>Yearly</option>
                <option value="One time" {{ old('payment_cycle') == 'One time'? 'selected' : '' }}>One time</option>
            </select>
            @if ($errors->has('payment_cycle'))
              <div class="alert alert-danger">{{$errors->first('payment_cycle')}}</div>
            @endif
          </div>

          <div class="form-group">
            <strong>Amount:</strong>
            <input type="number" name="amount" id="asset_amount" class="form-control" value="{{ old('amount') }}">

            @if ($errors->has('amount'))
              <div class="alert alert-danger">{{$errors->first('amount')}}</div>
            @endif
          </div>

          <div class="form-group">
            <strong>Start Date:</strong>
            <input type="date" name="start_date" id="start_date" class="form-control" value="{{ old('start_date') }}">

            @if ($errors->has('start_date'))
              <div class="alert alert-danger">{{$errors->first('start_date')}}</div>
            @endif
          </div>

          <div class="form-group">
            <strong>Currency:</strong>
            <input type="text" name="currency" class="form-control" value="{{ old('currency') }}" id="currency" required>

            @if ($errors->has('currency'))
              <div class="alert alert-danger">{{$errors->first('currency')}}</div>
            @endif
          </div>

          <div class="form-group">
            <strong>Location:</strong>
            <input type="text" name="location" class="form-control" value="{{ old('location') }}" id="location" required>

            @if ($errors->has('location'))
              <div class="alert alert-danger">{{$errors->first('location')}}</div>
            @endif
          </div>

          <div class="form-group">
            <strong>Usage:</strong>
            <input type="text" id="usage" name="usage" class="form-control" value="{{ old('usage') }}">

            @if ($errors->has('usage'))
              <div class="alert alert-danger">{{$errors->first('usage')}}</div>
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

<div id="showAssetsHistoryLogModel" class="modal fade" role="dialog">
  <div class="modal-dialog">
    
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Asset Manament Log</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="mt-3 col-md-12">
        <div class="infinite-scroll">
          <table class="table table-bordered table-striped">
            <thead>
              <tr>
                <th width="4%">ID</th>
                <th width="9%">Change by</th>
                <th width="8%">Old User name</th>
                <th width="6%">Old Pwd</th>
                <th width="6%">Date</th>
              </tr>
            </thead>
  
            <tbody id="showAssetsHistoryLogView">
              
            </tbody>
          </table>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>