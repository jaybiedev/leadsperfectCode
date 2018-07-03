<table class="table">
    <thead>
        <tr>
            <th scope="col">First Name</th>
            <th scope="col">Last Name</th>
            <th scope="col">Email</th>
            <th scope="col">Username</th>
            <th scope="col">Action</th>
        </tr>
    </thead>
    <tbody>
        {foreach from=$Users item=$User}
            <tr>
                <td>{$User.first_name}</td>
                <td>{$User.last_name}</td>
                <td>{$User.email}</td>
                <td>{$User.username}</td>
                <td><a href="/admin/user/{$User.id}?action=edit">Edit</a> | <a href="/admin/user/{$User.id}?action=business">Business</td>
            </tr>
        {/foreach}
    </tbody>
</table>
<div>
    <a href="/admin/user/?action=add" class="btn btn-primary">Add New</a>
</div>