<table class="table table-bordered overlay api-token-table">
    <thead>
    <tr>
        <th width="15%">Index</th>
        <th>Updated At</th>
        <th>Status</th>
        <th>Logs</th>
        <th>Settings</th>
    </tr>
    </thead>
    <tbody>
    <?php /** @var \App\Models\IndexerState $indexer */ ?>
    @foreach($indexerStates as $indexer)
        <tr>
            <td class="td-index-{{ $indexer->getId() }}">
                {{ $indexer->getIndex() }}
            </td>
            <td class="td-updated-at-{{ $indexer->getId() }}">
                {{ (string)$indexer->getUpdatedAt() }}
            </td>
            <td class="td-status-{{ $indexer->getId() }} {{ $indexer->getStatus() === 'running' ? 'index-running' : ($indexer->getStatus() === 'invalidate' ? 'index-invalidate' : 'index-valid') }}">
                <span>{{ strtoupper($indexer->getStatus()) }}</span>
            </td>
            <td class="td-logs-{{ $indexer->getId() }}">
            <a href="#" class="btn btn-xs btn-secondary btn-edit-indexer td-edit-{{ $indexer->getId() }}" data-id="{{ $indexer->getId() }}" data-json='<?=json_encode($indexer)?>'>Logs</a>
            </td>
            <td>
                <a href="#" class="btn btn-xs btn-secondary btn-edit-indexer td-edit-{{ $indexer->getId() }}" data-id="{{ $indexer->getId() }}" data-json='<?=json_encode($indexer)?>'>Edit</a>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>