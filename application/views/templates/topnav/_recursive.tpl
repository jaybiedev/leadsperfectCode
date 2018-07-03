<ul class="dropdown-menu"  aria-labelledby="{$child->parent_path}">
{foreach name=entry item=entry from=$child}
    <li class="{if $entry->children||$depth>2}dropdown-submenu{/if}">
        <a href="{$entry->slug}"  aria-expanded="false" id="{$entry->path}">{$entry->menu}{if $entry->children} ...{/if}</a>
        {if $entry->children}
            {include file="{$APPPATH}views/templates/topnav/_recursive.tpl" child=$entry->children depth=$depth+1}
        {/if}
    </li>
{/foreach}
</ul>
