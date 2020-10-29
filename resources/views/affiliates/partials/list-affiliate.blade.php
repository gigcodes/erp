@foreach ($data as $key => $affiliate)
                <tr>
                    <td><input type="checkbox" id ="affilate_multi_select" name="affilate_multi_select[]" value="{{$affiliate->id}}"></td>
                    <td>{{ ++$i }}</td>
                    <td>{{ $affiliate->first_name }}</td>
                    <td>{{ $affiliate->last_name }}</td>
                    <td>{{ $affiliate->phone }}</td>
                    <td>{{ $affiliate->url }}</td>
                    <td>{{ $affiliate->source }}</td>
                    <td>{{ $affiliate->emailaddress }}</td>
                    <td>{{ $affiliate->unique_visitors_per_month}}</td> 
                    <td>{{ $affiliate->page_views_per_month }}</td>
                    <td>{{ $affiliate->country }}</td>
                    <td>
                        {!! Form::open(['method' => 'POST','route' => ['affiliates.destroy'],'style'=>'display:inline']) !!}
                        <input type="hidden" value="{{$affiliate->id}}" name="id">
                        <button type="submit" class="btn btn-image"><img src="/images/delete.png"/></button>
                        {!! Form::close() !!}

                    </td>
                </tr>
@endforeach