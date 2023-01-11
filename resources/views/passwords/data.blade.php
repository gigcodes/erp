 @if($passwords->isEmpty())

            <tr>
                <td>
                    No Result Found
                </td>
            </tr>
        @else

          @foreach ($passwords as $password)

            <tr>
			 <td class="text-center">
				<input type="checkbox" class="checkbox_ch" id="u{{ $password->id }}" name="userIds[]" value="{{ $password->id }}"></td>
             <td>
                {{ $password->website }}
                <br>
                <a href="{{ $password->url }}" target="_blank"><small class="text-muted">{{ $password->url }}</small></a>
              </td>
                <td>{{ $password->username }}
                  <button type="button" data-id="" class="btn btn-copy-username btn-sm"  data-value="{{ $password->username }}">
                      <i class="fa fa-clone" aria-hidden="true" ></i>
                  </button>
              </td>
              <td><span class="user-password">{{ Crypt::decrypt($password->password) }}</span>
                <button type="button" data-id="" class="btn btn-copy-password btn-sm"  data-value="{{ Crypt::decrypt($password->password) }}">
                  <i class="fa fa-clone" aria-hidden="true"></i>
                </button>
              </td>
              <td>{{ $password->registered_with }}</td>
              <td>
                  <div style="margin-bottom:10px;width: 100%;">
                  <div class="d-flex">
                    <input type="text" name="remark_pop" class="form-control remark_pop" placeholder="Please enter remark" style="width: 200px;">
                    <button class="btn btn-sm btn-image send-message sub_remark pointer" title="Send message" data-password_id="{{$password->id}}">
                        <img src="{{asset('images/filled-sent.png')}}">
                    </button>
                  <button data-password_id="{{ $password->id }}" data-password_type="Quick-dev-task" class="btn btn-xs btn-image set-remark" title="Remark"><img src="{{asset('images/chat.png')}}" alt=""></button>
                  </div>
                  @if (isset($password_remark))
                      <div style="margin-bottom:10px;width: 100%;">
                          <div class="expand-row-msg" data-id="{{$password->id}}">
                              <div class="d-flex justify-content-between expand-row-msg" data-id="{{$password->id}}">
                                    <span class="td-password-remark" style="margin:0px;">
                                        @php($i = 1)
                                        @foreach($password_remark as $pwd_remark)
                                            {{$i}}.{{$pwd_remark->remark}}
                                            @php($i++)
                                        @endforeach
                                    </span>
                              </div>
                          </div>
                      </div>
                  @endif
                  </div>
{{--                 <button type="button" class="btn btn-default sub_remark" >Save</button>--}}
              </td>
                <td><button onclick="changePassword({{ $password->id }})" class="btn btn-secondary btn-sm" title="Change"><i class="fa fa-pencil"></i></button>
                <button onclick="getData({{ $password->id }})" class="btn btn-secondary btn-sm" title="History"><i class="fa fa-info-circle"></i></button>
                <button onclick="sendtoWhatsapp({{ $password->id }})" class="btn btn-secondary btn-sm" title="Send to Whatsapp"><i class="fa fa-whatsapp"></i></button>
{{--                onclick="sendtoWhatsapp({{ $password->id }})"--}}
            </tr>


          @endforeach

          @endif