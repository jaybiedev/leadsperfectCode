{extends file='finance/template.tpl'}
{block name=title}User login{/block}
{block name=body}
    <div class="col-lg-5 col-md-8 col-sm-12">
        <div class="well">
            <form action="{$Helper->getUrl()->getLoginUrl()}" method="post">
                <fieldset>
                    <legend><i class="fa fa-lock" aria-hidden="true"></i> Secure Sign in</legend>
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" id="username" name="username" value="" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" class="form-control">
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-sign-in" aria-hidden="true"></i> Sign in
                    </button>
                </fieldset>
            </form>
        </div>
    </div>
{/block}