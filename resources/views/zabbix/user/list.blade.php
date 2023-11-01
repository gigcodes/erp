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
    <?php /** @var \App\Models\Zabbix\User $user */ ?>
    @foreach($users as $user)
        <tr>
            <td class="td-id-{{ $user->getId() }}">
                {{ $user->getId() }}
            </td>
            <td class="td-username-{{ $user->getId() }}">
                {{ $user->getUsername() }}
            </td>
            <td class="td-name-{{ $user->getId() }}">
                {{ $user->getName() }}
            </td>
            <td class="td-surname-{{ $user->getId() }}">
                {{ $user->getSurname() }}
            </td>
            <td class="td-role-id-{{ $user->getId() }}">
                {{ $user->getRoleId() }}
            </td>
            <td class="td-url-{{ $user->getId() }}">
                {{ $user->getUrl() }}
            </td>
            <td class="td-timezone-{{ $user->getId() }}">
                {{ $user->getTimezone() }}
            </td>
            <td class="td-autologin-{{ $user->getId() }}">
                {{ $user->getAutologin() }}
            </td>
            <td>
                <a href="#" class="btn btn-xs btn-secondary btn-edit-user td-edit-{{ $user->getId() }}" data-id="{{ $user->getId() }}" data-json='<?=json_encode($user)?>'>Edit</a>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>