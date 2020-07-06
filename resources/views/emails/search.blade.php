
    @foreach ($emails as $key => $email)
        <tr>
            <td>{{ Carbon\Carbon::parse($email->created_at)->format('d-m-Y') }}</td>
            <td>{{ $email->from }}</td>
            <td>{{ $email->to }}</td>
            <td>{{ $email->type }}</td>
            <td>{{$email->subject}}</td>
            <!-- <td>{{$email->subject}}</td> -->
            <td data-toggle="modal" data-target="#viewMail"  onclick="opnMsg({{$email}})" style="cursor: pointer;">{{ substr($email->message, 0,  50) }} {{strlen($email->message) > 50 ? '...' : '' }}</td>
            <td>
            <a title="Resend" class="btn btn-image resend-email-btn" data-id="{{ $email->id }}" >
            <i class="fa fa-repeat"></i>
                </a>
                <a title="Reply" class="btn btn-image reply-email-btn" data-toggle="modal" data-target="#replyMail" data-id="{{ $email->id }}" >
                    <i class="fa fa-reply"></i>
                </a>
            </td>
        </tr>
    @endforeach