@extends('layouts.app')

@section('content')
    <style>
        #devOverview {
            overflow-x: auto;
            padding: 20px 0;
        }

        .success {
            background: #00B961;
            color: #fff
        }

        .info {
            background: #2A92BF;
            color: #fff
        }

        .warning {
            background: #F4CE46;
            color: #fff
        }

        .error {
            background: #FB7D44;
            color: #fff
        }
    </style>
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">Work in progress</h2>
        </div>
    </div>

    @php
        $count = 0;
    @endphp

    <main class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div id="devOverview">
                        <div class="overview-container">
                            @foreach($users as $user)
                                @php
                                    $tasks = \App\Helpers\DevelopmentHelper::getDeveloperTasks($user->id);
                                @endphp
                                @if(!empty($tasks) && count($tasks)>0)
                                    <div style="width: 200px; display: inline-block;">
                                        <div class="card card-border-warning">
                                            <div class="card-header">
                                                <h5 class="card-title">{{ucwords($user->name)}}</h5>
                                            </div>
                                            <div class="card-body">
                                                @foreach ($tasks as $task)
                                                    @if($task->user_id == $user->id)
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
                                                            </div>
                                                        </div>
                                                    @endif
                                                @endforeach
                                                @php
                                                    $count++;
                                                @endphp
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection

@section('scripts')
    <script type="text/javascript">
        $(document).ready(function () {
            $('.overview-container').css("width", "<?= $count*200 ?>px !important;");
            $('.overview-container').width(<?= $count*200 ?>);
        });
    </script>
@endsection