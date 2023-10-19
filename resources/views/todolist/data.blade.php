 @if($todolists->isEmpty())

            <tr>
                <td>
                    No Result Found
                </td>
            </tr>
        @else

          @foreach ($todolists as $todolist)

            <tr style="background-color: {{$todolist->color?->color}}";>
			        <td>{{ $todolist->id ?? '' }}</td>
             <td>{{ $todolist->title ?? '' }}</td>
             <td>{{ $todolist->subject ?? '' }}</td>
              {{-- <td>{{ $todolist->username->name ?? '' }}</td> --}}
             <td>
                <select name="status" id="status" class="form-control" onchange="todoCategoryChange({{$todolist->id}}, this.value)" data-id="{{$todolist->id}}">
                    <option>--Select--</option>
                    @foreach ($todoCategories as $todoCategory )
                       <option value="{{$todoCategory->id}}" @if ($todolist->todo_category_id == $todoCategory->id) selected @endif>{{$todoCategory->name}}</option>
                    @endforeach
                </select>
              </td>
              <td>
                <select name="status" id="status" class="form-control" onchange="statusChange({{$todolist->id}}, this.value)" data-id="{{$todolist->id}}">
                  <option>--Select--</option>
                  @foreach ($statuses as $status )
                    <option value="{{$status['id']}}" @if ($todolist->status == $status['id']) selected @endif>{{$status['name']}}</option>
                    @endforeach
                </select>
              </td>
              <td>{{ $todolist->todo_date ?? '' }}</td>
              <td>{{ $todolist->remark ?? '' }}</td>
              <td>
                <a class="btn btn-image" onclick="changetodolist({{ $todolist->id }})" ><img src="{{asset('/images/edit.png')}}" style="cursor: nwse-resize; width: 14px !important;"></a>
                <a onclick="getRemarkHistoryData({{ $todolist->id }})" class="btn" title="Remark history"><i class="fa fa-info-circle" aria-hidden="true"></i></a>
                {{-- <button onclick="sendtoWhatsapp({{ $todolist->id }})" class="btn btn-secondary btn-sm">Send to Whatsapp</button>< --}}
                {!! Form::open(['method' => 'DELETE','route' => ['todolist.destroy', $todolist->id],'style'=>'display:inline']) !!}
                <button type="submit" class="btn btn-image" onclick="return confirm('{{ __('Are you sure you want to delete?') }}')"><img src="{{asset('/images/delete.png')}}" /></button>
                {!! Form::close() !!}
              </td>
            </tr>


          @endforeach

          @endif
