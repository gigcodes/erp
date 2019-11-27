@foreach($numbers as $number)
                                    <tr>
                                        <td>{{ $number->id }}</td>
                                        <td>@if($number->is_customer_support == 1)<span style="color: red;">{{ $number->number }}</span>@else {{ $number->number }} @endif</td>
                                        <td>{{ $number->customer()->count() }}</td>
                                        @if(isset($date))
                                        @php
										$count = \App\ImQueue::where('number_from',$number->number)->whereDate('created_at', $date)->count();
										@endphp
										<td><button type="button" onclick="showMessage({{ $number->id }} ,{{ $number->number }} )" value="{{ $date }}" id="date{{ $number->id }}">{{ $count }}</button></td>
										@else
										<td>{{ $number->imQueueCurrentDateMessageSend->count() }}</td>
                                        @endif
                                        
                                        <td>{{ $number->last_online }}</td>
                                        <td> @if(isset($number->imQueueLastMessageSend)) @if($number->imQueueLastMessageSend->send_after == '2002-02-02 02:02:02') Message Failed @else Send SucessFully @endif @endif</td>

                                    </tr>
@endforeach