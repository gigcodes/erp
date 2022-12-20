 @if($passwords->isEmpty())

            <tr>
                <td>
                    No Result Found
                </td>
            </tr>
        @else

          @foreach ($passwords as $password)

            <tr>
			 <td>
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
                <td><button onclick="changePassword({{ $password->id }})" class="btn btn-secondary btn-sm">Change</button>
                <button onclick="getData({{ $password->id }})" class="btn btn-secondary btn-sm">History</button>
                <button onclick="sendtoWhatsapp({{ $password->id }})" class="btn btn-secondary btn-sm">Send to Whatsapp</button></td>
            </tr>


          @endforeach

          @endif