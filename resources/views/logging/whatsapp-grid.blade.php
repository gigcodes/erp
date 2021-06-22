@php
    $user_role = App\RoleUser::where('user_id',Auth::id())->first();
@endphp
            @foreach($array as $row)
                <tr>
                    <td>{{ $sr++ }}</td>
                    <td>{{ $row['date'] }}</td>
                    <td>No</td>
                        @if ($user_role->role_id == 1)
                            <td class="errorLog">
                                Message1 : {{$row['error_message1']}} <br>
                                Message2 : {{$row['error_message2']}}
                            </td>
                        @else
                            <td></td>
                        @endif

                    <td>
                        @if((isset($row['error_message1']) && getStr($row['error_message1'])) || (isset($row['error_message2']) && getStr($row['error_message2'])))
                            @if ($user_role->role_id == 1)
                                <button class="btn btn-success sentMessage text-center" >
                                    Resend
                                </button>
                            @else
                                <button class="btn btn-success text-center" disabled>
                                    Resend
                                </button>
                            @endif
                        @endif
                    </td>
                </tr>
                {{-- <tr>
                    <td>{{ $row['date'] }}</td>
                    <!-- <td>{{ $row['type'] == 1 ? 'Yes' : 'No'}}</td> -->
                    <td>{{ $row['type']}}</td>
                    @if($row['type'] == 1)
                        <td>
                            Receiver No. : {{ $row['number']}} <br>
                            ID : {{ $row['id']}} <br>
                            Message : {{$row['message']}}
                        </td>
                    @elseif($row['type'] == 2 || $row['type'] == 3)
                        <td class="errorLog">
                            Message1 : {{$row['error_message1']}} <br>
                            Message2 : {{$row['error_message2']}}
                        </td>
                    @elseif($row['type'] == 4)
                        <td>
                            Message : {{$row['error_message1']}}
                        </td>
                    @endif
                    <td>
                        @if((isset($row['error_message1']) && getStr($row['error_message1'])) || (isset($row['error_message2']) && getStr($row['error_message2'])))

                            <button class="btn btn-success sentMessage text-center" {{$row['type'] == 1 ? 'disabled' : ""}}>
                                Resend
                            </button>

                        @endif
                    </td>
                </tr> --}}
            @endforeach