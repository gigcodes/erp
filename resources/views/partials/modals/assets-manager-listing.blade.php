<table id="assets-manager-table" class="table table-striped table-bordered table-sm" cellspacing="0" width="100%">
    <thead>
    <tr>
        @foreach($assetsManagers[0]->getTableFields() as $key => $value)
            <th class="th-sm">{{ $value }}</th>
        @endforeach
    </tr>
    </thead>
    <tbody>
    @foreach($assetsManagers as $asset)
        <tr>
                @foreach($asset->getAttributes() as $key => $value)
                    @if(in_array($key, $asset->getTableFields()))
                    <td>
                        {{ $value }}
                    </td>
                @endif
                @endforeach
        </tr>
    @endforeach
    </tbody>
</table>


