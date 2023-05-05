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
        </td>
    </tr>
@endforeach