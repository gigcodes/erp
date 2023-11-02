<table class="table table-bordered overlay api-token-table">
    <thead>
    <tr>
        <th>Id</th>
        <th width="15%">Name</th>
        <th width="20%">Expression</th>
        <th>Event Name</th>
        <th>Template ID</th>
        <th>Edit</th>
    </tr>
    </thead>
    <tbody>
    <?php /** @var \App\Models\Zabbix\Trigger $trigger */ ?>
    @foreach($triggers as $trigger)
        <tr>
            <td class="td-id-{{ $trigger->getId() }}">
                {{ $trigger->getId() }}
            </td>
            <td class="td-name-{{ $trigger->getId() }}">
                {{ $trigger->getName() }}
            </td>
            <td class="td-expression-{{ $trigger->getId() }}">
                {{ $trigger->getExpression() }}
            </td>
            <td class="td-event-name-{{ $trigger->getId() }}">
                {{ $trigger->getEventName() }}
            </td>
            <td class="td-template-id-{{ $trigger->getId() }}">
                {{ $trigger->getTemplateId() }}
            </td>
            <td>
                <a href="#" class="btn btn-xs btn-secondary btn-edit-trigger td-edit-{{ $trigger->getId() }}" data-id="{{ $trigger->getId() }}" data-json='<?=json_encode($trigger)?>'>Edit</a>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
<nav aria-label="Page navigation example">
  <ul class="pagination">
    <li class="page-item"><a class="page-link" href="{{ route('zabbix.trigger.index') }}/?page={{ $page - 1 }}">Previous</a></li>
    <li class="page-item"><a class="page-link" href="{{ route('zabbix.trigger.index') }}/?page={{ $page - 1 }}">{{ $page - 1 }}</a></li>
    <li class="page-item active"><a class="page-link" href="{{ route('zabbix.trigger.index') }}/?page={{ $page }}">{{ $page }}</a></li>
    <li class="page-item"><a class="page-link" href="{{ route('zabbix.trigger.index') }}/?page={{ $page + 1 }}">{{ $page + 1 }}</a></li>
    <li class="page-item"><a class="page-link" href="{{ route('zabbix.trigger.index') }}/?page={{ $page + 1 }}">Next</a></li>
  </ul>
</nav>