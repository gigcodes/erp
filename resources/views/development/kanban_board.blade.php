@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-lg-12 margin-tb">
        <h2 class="page-heading">Kanban Board</h2>
    </div>
</div>

<main class="content">
    <div class="container p-0">

        <h1 class="h3 mb-3">Kanban Board</h1>

        <div class="row">
            @foreach($users as $id=>$name)
                <?php $tasks = \App\Helpers::getDeveloperTasks($id); ?>
                    <div class="col-12 col-lg-6 col-xl-3">
                        <div class="card card-border-primary">
                            <div class="card-header">
                                <div class="card-actions float-right" style="display: none;">
                                    <div class="dropdown show" >
                                        <a href="#" data-toggle="dropdown" data-display="static">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-more-horizontal align-middle">
                                                <circle cx="12" cy="12" r="1"></circle>
                                                <circle cx="19" cy="12" r="1"></circle>
                                                <circle cx="5" cy="12" r="1"></circle>
                                            </svg>
                                        </a>

                                        <div class="dropdown-menu dropdown-menu-right">
                                            <a class="dropdown-item" href="#">Action</a>
                                            <a class="dropdown-item" href="#">Another action</a>
                                            <a class="dropdown-item" href="#">Something else here</a>
                                        </div>
                                    </div>
                                </div>
                                <h5 class="card-title">{{ucwords($name)}}</h5>
                            </div>
                            <div class="card-body p-3">

                            @foreach ($tasks as $task)
                                @if($task->user_id == $id)
                                    <div class="card mb-3 bg-light">
                                        <div class="card-body p-3">
                                            <div class="float-right mr-n2">
                                                <label class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input" checked="">
                                                    <span class="custom-control-label"></span>
                                                </label>
                                            </div>
                                            <h4>{{ ucfirst($task->subject) }}</h4>
                                            <p>{{ $task->task }}</p>
                                            <div class="float-right mt-n1">
                                                <img src="https://bootdey.com/img/Content/avatar/avatar6.png" width="32" height="32" class="rounded-circle" alt="Avatar">
                                            </div>
            {{--                                <a class="btn btn-outline-primary btn-sm" href="#">View</a>--}}
                                        </div>
                                    </div>
                                @endif
                            @endforeach
{{--                                <a href="#" class="btn btn-primary btn-block">Add new</a>--}}

                            </div>
                        </div>
                    </div>
            @endforeach


        </div>

    </div>
</main>





@endsection
