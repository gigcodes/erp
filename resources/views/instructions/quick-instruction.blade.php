@extends('layouts.modal')

@section('title', 'Quick instruction modal')

@section("styles")
@endsection

@section('content')
    <div class="container">
        <h1>Quick Instruction</h1>
        @if ( $instruction != null && isset($instruction->customer->id) )
            <div class="row">
                <div class="col-md-12">
                    <table class="table table-bordered">
                        <tbody>
                        <tr>
                            <th>Number</th>
                            <th>Category</th>
                            <th>Instructions</th>
                            <th colspan="3" class="text-center">Action</th>
                            <th>Created at</th>
                            <th>Remark</th>
                        </tr>
                        <tr>
                            <td>
                                <span data-twilio-call data-context="customers" data-id="{{ $instruction->customer->id }}">{{ $instruction->customer->phone }}</span>
                            </td>
                            <td>{{ $instruction->category ? $instruction->category->name : 'Non Existing Category' }}</td>
                            <td>{{ $instruction->instruction }}</td>
                            <td>
                                @if ($instruction->completed_at)
                                    {{ Carbon\Carbon::parse($instruction->completed_at)->format('d-m H:i') }}
                                @else
                                    <a href="#" class="btn-link complete-call" data-id="{{ $instruction->id }}" data-assignedfrom="{{ $instruction->assigned_from }}">Complete</a>
                                @endif
                            </td>
                            <td>
                                @if ($instruction->completed_at)
                                    Completed
                                @else
                                    @if ($instruction->pending == 0)
                                        <a href="#" class="btn-link pending-call" data-id="{{ $instruction->id }}">Mark as Pending</a>
                                    @else
                                        Pending
                                    @endif
                                @endif
                            </td>
                            <td>
                                @if ($instruction->verified == 1)
                                    <span class="badge">Verified</span>
                                @elseif ($instruction->assigned_from == Auth::id() && $instruction->verified == 0)
                                    <a href="#" class="btn btn-xs btn-secondary verify-btn" data-id="{{ $instruction->id }}">Verify</a>
                                @else
                                    <span class="badge">Not Verified</span>
                                @endif
                            </td>
                            <td>{{ $instruction->created_at->diffForHumans() }}</td>
                            <td>
                                <a href class="add-task" data-toggle="modal" data-target="#addRemarkModal" data-id="{{ $instruction->id }}">Add</a>
                                <span> | </span>
                                <a href class="view-remark" data-toggle="modal" data-target="#viewRemarkModal" data-id="{{ $instruction->id }}">View</a>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <h3>Customer</h3>
                    <table class="table table-striped">
                        <tr>
                            <td>ID</td>
                            <td>{{ $instruction->customer->id}}</td>
                        </tr>
                        <tr>
                            <td>Name</td>
                            <td>{{ $instruction->customer->name }}</td>
                        </tr>
                        <tr>
                            <td>Address</td>
                            <td>{{ $instruction->customer->address }}</td>
                        </tr>
                        <tr>
                            <td>Shoe size</td>
                            <td>{{ $instruction->customer->shoe_size }}</td>
                        </tr>
                        <tr>
                            <td>Clothing size</td>
                            <td>{{ $instruction->customer->clothing_size }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <h3>Instruction</h3>
                    <span style="background-color: #FFFF00; padding: 3px; font-size: 1.5em;">{!! nl2br($instruction->instruction) !!}</span>
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#attachImagesModal">
                        Attach Images
                    </button>
                    <h3>Chat</h3>
                    <div id="chat-history" class="load-communication-modal" data-object="customer" data-all="1" data-attached="1" data-id="{{ $instruction->customer_id }}" style="max-height: 80vh; overflow-x: hidden; overflow-y: scroll;">
                    </div>
                </div>
            </div>
        @else
            <h2>No more instructions or no customer found (code: {{ $instruction->id }})</h2>
        @endif
    </div>

    <div class="modal fade" style="width: 95vw; height: 95vh;" id="attachImagesModal" tabindex="-1" role="dialog" aria-labelledby="attachImagesModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Attach Images</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <iframe id="iFrameModal"></iframe>
                </div>
            </div>
        </div>
    </div>
    <style>
        iframe {
            margin: 0px auto;
            border: none;
            width: 100% !important;
            height: 100% !important;
        }

        .modal {
            margin: 0px auto;
            width: 95vw;
        }

        .modal-dialog {
            width: 100vw;
            height: 95vh;
            margin: 0;
            padding: 0;
        }

        .modal-content {
            height: auto;
            min-height: 95vh;
            width: 95vw;
            border-radius: 0;
        }

        .modal-body {
            height: 80vh !important;
        }
    </style>

    @include('customers.partials.modal-remark')
@endsection

@section('scripts')
    @if ($instruction != null && isset($instruction->customer->id) )
        <script>
            var customer_id = {{ $instruction->customer->id }};
            var current_user = {{ Auth::id() }};
            var route = [];
            route.instruction_complete = "{{ route('instruction.complete') }}";
            route.instruction_pending = "{{ route('instruction.pending') }}";
            route.leads_store = "{{ route('leads.store') }}";
            route.leads_send_prices = "{{ route('leads.send.prices') }}";
            route.task_add_remark = "{{ route('task.addRemark') }}";
            route.task_get_remark = "{{ route('task.gettaskremark') }}";
            $('#add-remark input[name="id"]').val({{ $instruction->id }});
            $('.modal').on('shown.bs.modal', function () {
                $(this).find('iframe').attr('src', '/attachImages/customer/{{ $instruction->customer->id }}/1')
                // $(this).find('iframe').attr('src', '/attachImages/customer/44/1')
            });
            $(document).ready(function () {
                $('#chat-history').trigger('click');
            });
            $('#iFrameModal').on('load', function () {
                // console.log(window.location.protocol + '//' + document.domain + '/customer/44');
                // console.log(document.getElementById("iFrameModal").contentWindow.location.href);
                if (document.getElementById("iFrameModal").contentWindow.location.href == window.location.protocol + '//' + document.domain + '/customer/{{ $instruction->customer->id }}') {
                    $(function () {
                        $('#attachImagesModal').modal('toggle');
                    });
                }
            });
        </script>
    @endif
@endsection