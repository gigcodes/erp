@extends('layouts.app')


@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2> User Actions</h2>
            </div>
            <div class="pull-right">
                <a class="btn btn-secondary" href="{{ route('users.index') }}"> Back</a>
            </div>
        </div>
    </div>


    <div class="row">
        <div class="col-md-12">
            <table class="table table-striped">
                <tr>
                    <th>
                        Date
                    </th>
                    <td>
                        Action
                    </td>
                    <td>
                        Page
                    </td>
                    <td>
                        Description
                    </td>
                </tr>
                @foreach($actions as $action)
                    <tr>
                        <td>
                            {{ $action->date }} ({{$action->created_at->diffForHumans()}})
                        </td>
                        <td>
                            <span class="tag tag-info">
                                @if ($action->action == 'click_a')
                                    CLICKED LINK
                                @elseif ($action->action == 'key')
                                    KEYSTROKES
                                @elseif($action->action == 'click_button')
                                    CLICKED BUTTON
                                @elseif($action->action == 'click_img')
                                    CLICKED IMAGE
                                @elseif($action->action == 'click_select')
                                    CLICKED SELECT
                                @elseif($action->action == 'click_input')
                                    CLICKED Input
                                @elseif($action->action == 'click_div')
                                    CLICKED A SECTION
                                @else
                                    {{ $action->action }}
                                @endif
                            </span>
                        </td>
                        <td>
                            <a href="{{ $action->page }}">{{ $action->page }}</a>
                        </td>
                        <td>
                            @if ($action->action == 'click_a')
                                <a href="{{ $action->details }}">{{ $action->details }}</a>
                            @elseif ($action->action == 'key')
                                {{ substr($action->details, 0 , 500) }}
                            @elseif($action->action == 'click_button')
                                {{ substr($action->details, 0 , 500) }}
                            @elseif($action->action == 'click_img')
                                <img style="width: 250px;" src="{{ $action->details }}" alt="{{ $action->details }}">
                            @elseif($action->action == 'click_select')
                                {{ substr($action->details, 0 , 500) }}
                            @elseif($action->action == 'click_input')
                                {{ substr($action->details, 0 , 500) }}
                            @elseif($action->action == 'click_div')
                                {{ substr($action->details, 0 , 500) }}
                            @else
                                {{ substr($action->details, 0 , 500) }}
                            @endif
                        </td>
                    </tr>
                @endforeach
            </table>
        </div>
    </div>
@endsection
