<style>
    .dropdown-submenu {
        position: relative;
    }

    .dropdown-submenu>.dropdown-menu {
        top: 0;
        left: 100%;
        margin-top: -6px;
        margin-left: -1px;
        -webkit-border-radius: 0 6px 6px 6px;
        -moz-border-radius: 0 6px 6px;
        border-radius: 0 6px 6px 6px;
    }

    .dropdown-submenu:hover>.dropdown-menu {
        display: block;
    }
</style>
<ul class="nav navbar-nav navbar-left">
    {foreach name=entry item=entry from=$menu}
        <li class="dropdown">
            <a href="{$entry->slug}" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false" id="{$entry->path}">{$entry->menu}</a>
            {if $entry->children}
                {include file="{$APPPATH}views/templates/topnav/_recursive.tpl" child=$entry->children depth=2}
            {/if}
        </li>
    {/foreach}
</ul>
