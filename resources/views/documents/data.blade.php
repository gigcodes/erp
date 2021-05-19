  @foreach ($documents as $document)
                <tr>
                    <td>{{ $document->updated_at->format('d-m-Y') }}</td>
                    <td>{{ $document->user->name }}</td>
                    <td>{{ $document->user->agent_role  }}</td>
                    <td>{{ $document->name}}</td>
                    <td>@if(isset($document->documentCategory->name)){{ $document->documentCategory->name }} @endif</td>
                    <td>{{ $document->filename }}</td>
                    <td>
                        <a href="{{ route('document.download', $document->id) }}" class="btn btn-xs btn-secondary">Download</a>
                        <button type="button" class="btn btn-image sendWhatsapp" data-id="{{ $document->id }}"><img src="/images/send.png" /></button>
                        <button type="button" class="btn btn-image sendEmail" data-id="{{ $document->id }}"><img src="/images/customer-email.png" /></button>

                        {!! Form::open(['method' => 'DELETE','route' => ['document.destroy', $document->id],'style'=>'display:inline']) !!}

                        <button type="submit" class="btn btn-image"><img src="/images/delete.png" /></button>
                        <button type="button" class="btn btn-image make-remark d-inline" data-toggle="modal" data-target="#makeRemarkModal" data-id="{{ $document->id }}"><img src="/images/remark.png" /></button>

                        {!! Form::close() !!}
                        <button type="button" class="btn btn-image uploadDocument" data-id="{{ $document->id }}"><img src="/images/upload.png" /></button>

                        V: {{ $document->version }}
                    </td>
                </tr>
            @endforeach