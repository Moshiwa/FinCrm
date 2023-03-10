<table>
    <thead>
    <tr>
        <th colspan="3" style="text-align: center;  vertical-align: middle; background: #3ba787">
            <b>Пользователи</b>
        </th>
    </tr>
    <tr>
        <th style="text-align: center;  vertical-align: middle; width: 200px; background: #d5d5d5">
            <i>Имя</i>
        </th>
        <th style="text-align: center;  vertical-align: middle; width: 200px; background: #d5d5d5">
            <i>Email</i>
        </th>
        <th style="text-align: center;  vertical-align: middle; width: 200px; background: #d5d5d5">
            <i>Дата регистрации</i>
        </th>
    </tr>
    </thead>
    <tbody>
    @foreach($users as $user)
        <tr>
            <td style="height: 35px">{{ $user->name }}</td>
            <td style="height: 35px">{{ $user->email }}</td>
            <td style="height: 35px">{{ $user->created_at }}</td>
        </tr>
        <tr>
            <td colspan="3" style="background: #d5d5d5;"></td>
        </tr>
    @endforeach
    </tbody>
</table>
