<table class="table table-bordered overlay api-token-table">
    <thead>
    <tr>
        <th>Id</th>
        <th width="15%">Username</th>
        <th width="20%">Name</th>
        <th width="20%">Surname</th>
        <th>Role ID</th>
        <th width="45%">Url</th>
        <th width="45%">Timezone</th>
        <th>Autologin</th>
        <th>Edit</th>
    </tr>
    </thead>
    <tbody>
    <?php /** @var \App\Models\Zabbix\Role $role */ ?>
    @foreach($roles as $role)
        <tr>
            <td class="td-id-{{ $role->getId() }}">
                {{ $role->getId() }}
            </td>
            <td class="td-rolename-{{ $role->getId() }}">
                {{ $role->getUsername() }}
            </td>
            <td class="td-name-{{ $role->getId() }}">
                {{ $role->getName() }}
            </td>
            <td class="td-surname-{{ $role->getId() }}">
                {{ $role->getSurname() }}
            </td>
            <td class="td-role-id-{{ $role->getId() }}">
                {{ $role->getRoleId() }}
            </td>
            <td class="td-url-{{ $role->getId() }}">
                {{ $role->getUrl() }}
            </td>
            <td class="td-timezone-{{ $role->getId() }}">
                {{ $role->getTimezone() }}
            </td>
            <td class="td-autologin-{{ $role->getId() }}">
                {{ $role->getAutologin() }}
            </td>
            <td>
                <a href="#" class="btn btn-xs btn-secondary btn-edit-role td-edit-{{ $role->getId() }}" data-id="{{ $role->getId() }}" data-json='<?=json_encode($role)?>'>Edit</a>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>