@extends('layouts.app')

@section('content')
<style>
    #myKanban{overflow-x: auto; padding:20px 0;}

    .success{background: #00B961; color:#fff}
    .info{background: #2A92BF; color:#fff}
    .warning{background: #F4CE46; color:#fff}
    .error{background: #FB7D44; color:#fff}
</style>
<div class="row">
    <div class="col-lg-12 margin-tb">
        <h2 class="page-heading">Work in progress</h2>
    </div>
</div>

<main class="content" style="display: none;">
    <div class="container p-0">

        <h1 class="h3 mb-3">Work in progress</h1>

        <div class="row" >
            <?php $count = 0;?>
            @foreach($users as $id=>$name)
                <?php $count++; $tasks = \App\Helpers::getDeveloperTasks($id); ?>

                    <div class="col-12 col-lg-6 col-xl-3 {{ ($count > 0 && $count <= 4) ? 'show' : 'hide' }}" id="{{$count}}" >
                        <div class="card card-border-primary">
                            <div class="card-header">

                                <h5 class="card-title">{{ucwords($name)}}</h5>
                            </div>
                            <div class="card-body p-3">
                            @if(!empty($tasks) && count($tasks) >0)
                                @foreach ($tasks as $task)
                                    @if($task->user_id == $id)
                                        @if($task->priority == 1)
                                            <?php $border = 'border-left: 4px solid green;'; ?>
                                        @elseif($task->priority == 2)
                                                <?php $border = 'border-left: 4px solid orange;'; ?>
                                        @elseif($task->priority == 3)
                                                <?php $border = 'border-left: 4px solid red;'; ?>
                                        @endif
                                        <div class="card mb-3 bg-light" style=" {{$border}} ">
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
                            @else
                                <div style="text-align: center;font-size: medium;">Record not Found</div>
                            @endif
{{--                                <a href="#" class="btn btn-primary btn-block">Add new</a>--}}

                            </div>
                        </div>
                    </div>

            @endforeach


        </div>

    </div>
</main>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div id="myKanban">
                <?php $width = count($users) * 230; ?>
                <div class="kanban-container" style="width: {{$width}}px;">
                    @foreach($users as $id=>$name)
                        <?php $count++; $tasks = \App\Helpers::getDeveloperTasks($id); ?>
                            <div class="" style="width: 200px;display: inline-block;">
                                <div class="card card-border-warning">
                                    <div class="card-header">
                                        <h5 class="card-title">{{ucwords($name)}}</h5>

                                    </div>
                                    <div class="card-body">
                                        @if(!empty($tasks) && count($tasks) >0)
                                            @foreach ($tasks as $task)
                                                @if($task->user_id == $id)
                                                    @if($task->priority == 1)
                                                        <?php $border = 'border-left: 4px solid green;'; ?>
                                                    @elseif($task->priority == 2)
                                                        <?php $border = 'border-left: 4px solid orange;'; ?>
                                                    @elseif($task->priority == 3)
                                                        <?php $border = 'border-left: 4px solid red;'; ?>
                                                    @endif
                                                    <div class="card mb-3 bg-light" style=" {{$border}} ">
                                                        <div class="card-body p-3">

                                                            <h4><a href="taskDetail/{{$task->id}}">{{ '#'.$task->name.'-'.$task->id.' '.ucfirst($task->subject) }} </a></h4>
                                                            <p>{{ $task->task }}</p>
                                                            <div class="float-right mt-n1">
                                                                <img src="https://bootdey.com/img/Content/avatar/avatar6.png" width="32" height="32" class="rounded-circle" alt="Avatar">
                                                            </div>
{{--                                                            <a class="btn btn-outline-primary btn-sm" href="#">View</a>--}}
                                                        </div>
                                                    </div>
                                                @endif
                                            @endforeach
                                        @else
                                            <div style="text-align: center;font-size: small;">Record not Found</div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>



@endsection
