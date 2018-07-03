<table class="table">
    <thead>
    <tr>
        <th scope="col">Name</th>
        <th scope="col">Path</th>
        <th scope="col">Enabled</th>
    </tr>
    </thead>
    <tbody>
    {foreach from=$Categories item=$Category}
        <tr>
            <td>{$Category.category}</td>
            <td>{$Category.path}</td>
            <td>{$Category.enabled}</td>
            <td><a href="/admin/category/{$Category.id}?action=edit">Edit</a> </td>
        </tr>
    {/foreach}
    </tbody>
</table>
<div>
    <a href="/admin/category/?action=add" class="btn btn-primary">Add New</a>
</div>