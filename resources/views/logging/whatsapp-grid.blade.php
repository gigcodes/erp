            @foreach($array as $row)
                @php
                    $message = strpos($row['error_message1'],'"message');
                    $message_str = strtok(substr($row['error_message1'],$message), ',');
                    // dump(str_replace($message_str,"",$row['error_message1']));
                    $message1 = strpos($row['error_message1'],'whatsapp_number');
                    $receiver_number = substr($row['error_message1'],$message1+18,12);
                    $null = substr($row['error_message1'],$message1+17,4);
                @endphp
                    <tr>
                        <td>{{ $sr++ }}</td>
                        <td>{{ $row['date'] }}</td>
                        <td>No</td>
                        @if ($message1 == '' || $null == "null")
                            <td></td>
                        @else
                            <td>{{ $receiver_number }}</td>
                        @endif
                        <td class="errorLog">
                            <div class="log-text-style">
                                @if ($isAdmin)
                                Message1 : {{$row['error_message1']}} <br>
                            @else
                                @if ($message)
                                    Message1 : {{str_replace($message_str,"",$row['error_message1'])}} <br>    
                                @else
                                    Message1 : {{$row['error_message1']}} <br>
                                @endif
                            @endif
                            Message2 : {{$row['error_message2']}}
                            </div>
                        </td>
                        <td>
                        @if((isset($row['error_message1']) && getStr($row['error_message1'])) || (isset($row['error_message2']) && getStr($row['error_message2'])))
                            @if ($isAdmin)
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