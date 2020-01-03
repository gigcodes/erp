@extends('layouts.app')

@section('link-css')
<style type="text/css">
  .form-group {
    padding: 10px;
  }
</style>
@endsection

@section('content')

@if(!empty($auth) && $auth['should_show_login'] == true)
<div class="text-center">
  <p>You are not authorized on hubstaff</p>
  <a class="btn btn-primary" href="{{ $auth['link'] }}">Authorize</a>
</div>
@endif

@if(Session::has('message'))
<div class="alert alert-success alert-block">
  <button type="button" class="close" data-dismiss="alert">×</button>
  <strong>{{ Session::get('message') }}</strong>
</div>
@endif

<h2 class="text-center">Users List from Hubstaff </h2>

@if(empty($auth))
<div class="text-right">
  <a href="/hubstaff/members/json" class="btn btn-primary">Refresh hubstaff users</a>
</div>
@endif

<div class="container">
  @if(!empty($members))
  <div class="row">
    <table class="table table-bordered">
      <thead>
        <tr>
          <th>HubStaff Id</th>
          <th>User</th>

        </tr>
      </thead>
      @foreach($members as $member)
      <tbody>
        <tr>
          <td>{{ $member->hubstaff_user_id }}</td>
          <td>
            <select onchange="saveUser(this)">
              <option value="unassigned">Unassigned</option>
              @foreach($users as $user)
              <option value="{{$user->id}}|{{ $member->hubstaff_user_id }}" <?= ($member->user_id == $user->id) ? 'selected' : '' ?>>{{$user->name}}</option>
              @endforeach
            </select>
          </td>

        </tr>
      </tbody>
      @endforeach
    </table>
    <br>
    <hr>
  </div>
  @else
  <div style="text-align: center;color: red;font-size: 14px;">
    {{$members['error_description']}}
  </div>
  @endif
</div>
@endsection

<script type="text/javascript">
  function saveUser(a) {
    var selectedValue = (a.value || a.options[a.selectedIndex].value); //crossbrowser solution =)
    console.log('selectedValue', selectedValue);
    if (selectedValue != 'unassigned') {
      var splitValues = selectedValue.split('|');
      var userId = splitValues[0];
      var hubstaffUserId = splitValues[1];

      var xhr = new XMLHttpRequest();
      var url = "linkuser";
      xhr.open("POST", url, true);
      xhr.setRequestHeader("Content-Type", "application/json");
      xhr.onreadystatechange = function() {
        if (xhr.readyState === 4 && xhr.status === 200) {
          var json = JSON.parse(xhr.responseText);
          console.log(json.email + ", " + json.password);
        }
      };
      var data = JSON.stringify({
        "user_id": userId,
        "hubstaff_user_id": hubstaffUserId
      });
      xhr.send(data);
    }

  }
</script>