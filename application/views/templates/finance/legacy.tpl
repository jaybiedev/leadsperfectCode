{extends file='finance/template.tpl'}
{block name=title}{$Helper->getConfig()->getCompanyName()} {$View->page_title}{/block}
{block name=body}
    {if $View->page_title neq ''}
      <div class="row">
         <h3>{$View->page_title}</h3>
      </div>
    {/if}
   {$contents}
{/block}