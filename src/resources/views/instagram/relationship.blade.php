<table>
    <tr>
        <th>id</th>
        <th></th>
        <th>follows</th>
        <th>following</th>
        <th>updated at</th>
        <th></th>
    </tr>
    <?php foreach($data as $user) { ?>
    <tr>
        <td>{{ $user->f_user_id }}</td>
        <td>
            <a href="//instagram.com/{{ $user->username }}" target="_blank"><img src="{{ $user->profile_picture }}"></a>
        </td>
        <td>{{ $user->follows ? $user->follows : "No." }}</td>
        <td>{{ $user->following ? $user->following : "No." }}</td>
        <td>{{ $user->updated_at }}</td>
        <td><a href="{{ route() }}">validate</a></td>
    </tr>
    <?php } ?>
</table>