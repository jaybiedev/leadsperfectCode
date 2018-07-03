<header>
    <nav class="nav-top-menu navbar navbar-default navbar-fixed-top" role="navigation">
        <div class="container">
            <div class="navbar-header">
                <a class="navbar-brand" href="{$Helper->getUrl()->getBaseUrl()}">
                    {$Helper->getConfig()->getCompanyName()}
                </a>

                <button type="button" class="navbar-toggle"
                        data-toggle="collapse"
                        data-target=".navbar-collapse">
                    <span class="sr-only">Menu</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
            </div>
            <div class="navbar-collapse collapse">
                {include file="{$APPPATH}views/templates/topnav/index.tpl" menu=$menu depth=1}

                <ul class="nav navbar-nav navbar-right">
                    {if $Helper->getSecurity()->IsAdmin()}
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false" id="admin-menu">
                            <i class="fa fa-bank" aria-hidden="true"></i>
                            Admin
                            <span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu admin-menu" role="menu" aria-labelledby="admin-menu">
                            {* for nav in navigation('Nav.Admin')  *}
                            <li role="menuitem"><a href="#">Admin Menu</a></li>
                            {* endfor *}
                        </ul>
                    </li>

                    {/if}

                    {if $Helper->getSecurity()->Islogged() == false}
                    <li>
                        <a href="{$Helper->getUrl()->getLoginUrl()}">
                            <i class="fa fa-sign-in" aria-hidden="true"></i> Login
                        </a>
                    </li>
                    {/if}

                </ul>
                {if $Helper->getSecurity()->IsLogged()}
                    <ul class="nav navbar-nav navbar-right">
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <span class="fa fa-user"></span>
                                <strong>{$Helper->getSecurity()->getUser()->name}</strong>
                                <span class="fa fa-chevron-down"></span>
                            </a>
                            <ul class="dropdown-menu" style="width:260px;">
                                <li>
                                    <div class="navbar-login">
                                        <div class="row">
                                            <div class="col-lg-4">
                                                <p class="text-center">
                                                    <span class="fa fa-user fa-4x"></span>
                                                </p>
                                            </div>
                                            <div class="col-lg-8">
                                                <p class="text-left"><strong>{$Helper->getSecurity()->getUser()->name}</strong></p>
                                                <p class="text-left small">{$Helper->getSecurity()->getUser()->name}</p>
                                                <p class="text-left" style="padding-right:10px;">
                                                    <a href="#" class="btn btn-primary btn-block btn-sm">Profile</a>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                                <li class="divider navbar-login-session-bg"></li>
                                <li><a href="#">Account Settings <span class="glyphicon glyphicon-cog pull-right"></span></a></li>
                                <li class="divider"></li>
                                <li><a href="#">User stats <span class="glyphicon glyphicon-stats pull-right"></span></a></li>
                                <li class="divider"></li>
                                <li><a href="#">Messages <span class="badge pull-right"> 42 </span></a></li>
                                <li class="divider"></li>
                                <li><a href="#">Favourites Snippets <span class="glyphicon glyphicon-heart pull-right"></span></a></li>
                                <li class="divider"></li>
                                <li><a href="/logout">Sign Out <span class="glyphicon glyphicon-log-out pull-right"></span></a></li>
                            </ul>
                        </li>
                    </ul>
                {/if}
            </div>

        </div>
    </nav>
</header>