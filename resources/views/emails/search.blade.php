
    @foreach ($emails as $key => $email)
        <tr id="{{ $email->id }}-email-row" class="search-rows">
            <td>{{ Carbon\Carbon::parse($email->created_at)->format('d-m-Y') }}</td>
            <td>{{ $email->from }}</td>
            <td>{{ $email->to }}</td>
            <td>{{ $email->type }}</td>
			<td data-toggle="modal" data-target="#viewMail"  onclick="opnMsg({{$email}})" style="cursor: pointer;">{{ substr($email->subject, 0,  10) }} {{strlen($email->subject) > 10 ? '...' : '' }}</td>
			<td>{{ substr($email->message, 0,  10) }} {{strlen($email->message) > 10 ? '...' : '' }}</td>
			<td>
				@foreach ($email_categories as $category)
					@if($category->id == $email->email_category_id)
						{{$category->category_name}} 
					@endif
				@endforeach
			</td>
			<td>
				@foreach ($email_status as $status)
					@if($status->id == $email->status)
						{{$status->email_status}} 
					@endif
				@endforeach
			</td>
			<td>
				
                <a title="Resend" class="btn-image resend-email-btn" data-type="resend" data-id="{{ $email->id }}" >
                    <i class="fa fa-repeat"></i>
                </a>
                <a title="Reply" class="btn-image reply-email-btn" data-toggle="modal" data-target="#replyMail" data-id="{{ $email->id }}" >
                    <i class="fa fa-reply"></i>
                </a>
                <a title="Forward" class="btn-image forward-email-btn" data-toggle="modal" data-target="#forwardMail" data-id="{{ $email->id }}" >
                    <i class="fa fa-share"></i>
                </a>
                <a title="Bin" class="btn-image bin-email-btn" data-id="{{ $email->id }}" >
                    <i class="fa fa-trash"></i>
                </a>
                <button style="padding:3px;" type="button" class="btn btn-image make-remark d-inline" data-toggle="modal" data-target="#makeRemarkModal" data-id="{{ $email->id }}"><img width="2px;" src="/images/remark.png"/></button>
                <button style="padding:3px;" type="button" class="btn-image make-remark d-inline mailupdate" data-toggle="modal" data-status="{{ $email->status }}" data-category="{{ $email->email_category_id}}" data-target="#UpdateMail" data-id="{{ $email->id }}"><img width="2px;" src="images/edit.png"/></button>   

                 <a title="Import Excel Imported" href="javascript:void(0);">  <i class="fa fa-cloud-download" aria-hidden="true" onclick="excelImporter({{ $email->id }})"></i></a>

                @if($email->email_excel_importer == 1)
                  <a href="javascript:void(0);">  <i class="fa fa-check"></i></a>
                @endif

                @if($email->approve_mail == 1)
                  <a title="Approve and send watson reply" class="btn-image resend-email-btn" data-id="{{ $email->id }}" data-type="approve" href="javascript:void(0);">  <i class="fa fa-check-circle"></i></a>
                @endif
            </td>
        </tr>
    @endforeach
    {{-- {{$emails->links()}} --}}