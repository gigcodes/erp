
    @foreach ($emails as $key => $email)
        <tr>
            <td>{{ Carbon\Carbon::parse($email->created_at)->format('d-m-Y') }}</td>
            <td>{{ $email->from }}</td>
            <td>{{ $email->to }}</td>
            <td>{{ $email->type }}</td>
            <td>{{$email->subject}}</td>
            <td data-toggle="modal" data-target="#viewMail"  onclick="opnMsg({{$email}})" style="cursor: pointer;">{{ substr($email->message, 0,  50) }} {{strlen($email->message) > 50 ? '...' : '' }}</td>
            <td>
                <a title="Resend" class="btn btn-image resend-email-btn" data-id="{{ $email->id }}" >
                    <i class="fa fa-repeat"></i>
                </a>
                <a title="Reply" class="btn btn-image reply-email-btn" data-toggle="modal" data-target="#replyMail" data-id="{{ $email->id }}" >
                    <i class="fa fa-reply"></i>
                </a>
                <a title="Forward" class="btn btn-image forward-email-btn" data-toggle="modal" data-target="#forwardMail" data-id="{{ $email->id }}" >
                    <i class="fa fa-share"></i>
                </a>
                <a title="Bin" class="btn btn-image bin-email-btn" data-toggle="modal" data-target="#binMail" data-id="{{ $email->id }}" >
                    <i class="fa fa-trash"></i>
                </a>
                <a title="Remark" class="btn btn-image remark-email-btn" data-toggle="modal" data-target="#remarkMail" data-id="{{ $email->id }}" >
                    <i class="fa fa-clipboard"></i>
                </a>
            </td>
        </tr>
    @endforeach