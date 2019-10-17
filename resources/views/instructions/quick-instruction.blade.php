@extends('layouts.modal')

@section('title', 'Quick instruction modal')

@section("styles")
@endsection

@section('content')
    <div class="container">
        <h1>Quick Instruction</h1>
        @if ( $instruction != null )
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
                <div class="col-md-5">
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
                    </table>
                </div>
                <div class="col-md-7">
                    <h3>Instruction</h3>
                    <span style="background-color: #FFFF00; padding: 3px; font-size: 1.5em;">{!! nl2br($instruction->instruction) !!}</span>
                    <h3>Chat</h3>
                    <div id="chat-history" class="load-communication-modal" data-object="customer" data-all="1" data-attached="1" data-id="{{ $instruction->customer_id }}" style="max-height: 80vh; overflow-x: scroll;">
                    </div>
                </div>
            </div>
        @else
            <h2>No more instructions</h2>
        @endif

    </div>
    @include('customers.partials.modal-remark')
@endsection

@section('scripts')
    <script>
        var customer_id = {{ $instruction->customer-> id}};
        var current_user = {{ Auth::id() }};
        var route = [];
        route.instruction_complete = "{{ route('instruction.complete') }}";
        route.instruction_pending = "{{ route('instruction.pending') }}";
        route.leads_store = "{{ route('leads.store') }}";
        route.leads_send_prices = "{{ route('leads.send.prices') }}";
        route.task_add_remark = "{{ route('task.addRemark') }}";
        route.task_get_remark = "{{ route('task.gettaskremark') }}";
        $(document).ready(function () {
            $('#chat-history').trigger('click');
        });
    </script>
@endsection