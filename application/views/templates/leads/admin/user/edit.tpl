<div class="col-lg-8 col-md-10 col-sm-12">
        <form action="/admin/user/{$Model->id}" method="post">
            <div class="well">
            <div class="form-group">
                <label for="email">Email address:</label>
                <input type="email" class="form-control" id="email" value="{$Model->email}">
            </div>
            <div class="form-group">
                <label for="pwd">Password:</label>
                <input type="password" class="form-control" id="pwd">
            </div>
            <div class="form-group">
                <label for="first_name">First Name :</label>
                <input type="first_name" class="form-control" id="first_name" value="{$Model->first_name}">
            </div>
            <div class="form-group">
                <label for="first_name">Last Name :</label>
                <input type="last_name" class="form-control" id="last_name" value="{$Model->last_name}">
            </div>
            <div class="form-group">
                <label for="first_name">Address1 :</label>
                <input type="address1" class="form-control" id="address1">
            </div>
            <div class="form-group">
                <label for="first_name">Address2  :</label>
                <input type="address2" class="form-control" id="address2">
            </div>
            <div class="form-group">
                <label for="first_name">City :</label>
                <input type="city" class="form-control" id="city">
            </div>
            <div class="form-group">
                <label for="first_name">State/Province :</label>
                <input type="state" class="form-control" id="state">
            </div>
            <div class="form-group">
                <label for="first_name">Country :</label>
                <input type="country" class="form-control" id="state">
            </div>
            <div class="form-group">
                <label for="first_name">Zip :</label>
                <input type="zip" class="form-control" id="zip">
            </div>
            </div>
        <button type="submit" class="btn btn-primary">Save</button>
        <button type="button" class="btn btn-default">Cancel</button>
    </form>

</div>