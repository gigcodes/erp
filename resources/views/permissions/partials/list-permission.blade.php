@foreach ($permissions as $key => $permission)
            <tr>
                <td>{{ ++$i }}</td>
                <td>{{ $permission->name }}</td>
                <td>{{ $permission->route }}</td>
                <td>
                    <a class="btn btn-image" href="{{ route('permissions.show',$permission->id) }}"><img src="/images/view.png" /></a>
                    @if(auth()->user()->isAdmin())
                        <a class="btn btn-image" href="{{ route('permissions.edit',$permission->id) }}"><img src="/images/edit.png" /></a>
                    @endif
                    {{--@can('role-delete')
                        {!! Form::open(['method' => 'DELETE','route' => ['permissions.destroy', $role->id],'style'=>'display:inline']) !!}
                        {!! Form::submit('Delete', ['class' => 'btn btn-danger']) !!}
                        {!! Form::close() !!}
                    @endcan--}}
                </td>
            </tr>
        @endforeach