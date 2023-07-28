@foreach($repositories as $repository)
    <tr>
        <td>{{$repository['id']}}</td>
        <td>{{$repository['name']}}</td>
        <td>{{$repository['updated_at']}}</td>
        <td>
            <a class="btn btn-default" href="{{ url('/github/repos/'.$repository['id'].'/branches') }}">
                <span title="Branches" class="glyphicon glyphicon-tasks"></span>
            </a>
            <a class="btn btn-default" href="{{ url('/github/repos/'.$repository['id'].'/users') }}">
                <span title="Users" class="glyphicon glyphicon-user"></span>
            </a>
            <a class="btn btn-default" href="{{ url('/github/repos/'.$repository['id'].'/pull-request') }}">
                <span title="Pull Request" class="glyphicon glyphicon-import"></span>
            </a>
            <a class="btn btn-default" href="{{ url('/github/repos/'.$repository['id'].'/actions') }}">
                <span title="Actions" class="glyphicon glyphicon-play"></span>
            </a>
            <button class="btn btn-default sync-labels-button" data-repo_id="{{$repository['id']}}" title="Sync Labels">
                <span class="fa fa-repeat"></span>
            </button>
            <button class="btn btn-default show-labels-button" data-repo_id="{{$repository['id']}}" title="Show Labels">
                <span class="fa fa-list"></span>
            </button>
        </td>
    </tr>
@endforeach