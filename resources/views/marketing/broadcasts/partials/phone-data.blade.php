@foreach($numbers as $number)
                                    <tr>
                                        <td>{{ $number->id }}</td>
                                        <td>@if($number->is_customer_support == 1)<span style="color: red;">{{ $number->number }}</span>@else {{ $number->number }} @endif</td>
                                        <td>{{ $number->customer()->count() }}</td>
                                        @if($date != '' || $startDate != '') 
                                        @php
                                        if($date != ''){
										$count = \App\ImQueue::where('number_from',$number->number)->whereDate('created_at', $date)->count();
                                        }
                                        elseif($startDate != '' && $endDate != ''){
                                        $count = \App\ImQueue::where('number_from',$number->number)->whereBetween('created_at', [$startDate,$endDate])->count();
                                        }
                                        else{
                                        $count = 0;
                                        }
                                        @endphp
										<td><button type="button" onclick="showMessage({{ $number->id }} ,{{ $number->number }} )" value="{{ $date }}" id="date{{ $number->id }}">{{ $count }}</button></td>
										@else
										<td>{{ $number->imQueueCurrentDateMessageSend->count() }}</td>
                                        @endif
                                        <td>@if(isset($number->imQueueLastMessagePending)){{ $number->imQueueLastMessagePending->count() }}@else 0 @endif</td>
                                        <td>{{ $number->last_online }}</td>
                                        <td> @if(isset($number->imQueueLastMessageSend)) @if($number->imQueueLastMessageSend->send_after == '2002-02-02 02:02:02') Message Failed @else Send SucessFully @endif @endif</td>
                                        <td>{{ $number->send_start }}</td>
                                        <td>{{ $number->send_end }}</td>

                                    </tr>
@endforeach

