@foreach ($data as $key => $log)
                <tr>
                    <td>{{ $log->id }}</td>
                    <td>{{ $log->google_traslation_settings_id }}</td>
                    <td>{{ $log->messages }}</td>
                    <td>{{ $log->error_code }}</td>
                    <td>{{ $log->domain }}</td>
                    <td>{{ $log->reason }}</td>
                    <td>{{ $log->updated_at }}</td>
                    <td>{{ $log->created_at }}</td> 
                    <td>
                        {!! Form::open(['method' => 'DELETE','route' => ['translation.log.destroy', $log->id],'style'=>'display:inline']) !!}
                        <button type="submit" class="btn btn-image btn-delete-TranslationLog"><img src="/images/delete.png"/></button>
                        {!! Form::close() !!}

                    </td>
                </tr>
@endforeach