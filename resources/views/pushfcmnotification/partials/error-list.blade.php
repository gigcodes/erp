<div class="row">
    <div class="col-md-12">
        <table class="table table-bordered table-striped sort-priority-scrapper">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Token</th>
                    <th>Error</th>
                    <th>Error At</th>
                </tr>
            </thead>
            <tbody class="conent">
                @foreach ($errors as $i => $error)
                    <tr>
                        <td>{{ $i+1 }}</td>
                        <td>{{ $error->token }}</td>
                        <td>{{ $error->error_message }}</td>
                        <td>{{ $error->created_at }}</td>
                    </tr>
                @endforeach
           </tbody>
        </table> 
    </div>
</div>