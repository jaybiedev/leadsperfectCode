<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <link rel="alternate" type="application/rss+xml" title="Lending">
    <title>{block name=title}{/block}</title>
    {block name=head}{/block}
    {assets_css files=$header_data.stylesheets}

    <script language="javascript">

        function wait($message)
        {
            xajax.$('message.layer').innerHTML = '';
            xajax.$('wait.layer').style.display = 'block';
            xajax.$('wait.layer').innerHTML = $message + "<br><img src='../graphics/wait.gif'>";
            return;
        }
    </script>
    <!-- legacy code -->

    <link rel="icon" type="image/x-icon" href="/images/logo-savvy.png" />
</head>
<body id="{$page_id}" ng-app="BaseApp">
    {include file="{$APPPATH}views/templates/finance/_header.tpl"}
    <div class="container body-container" style="margin-top:90px;">
        {block name=body}{/block}
    </div>
    {include file="{$APPPATH}views/templates/finance/_footer.tpl"}

    <!-- legacy placeholders -->
    <div id="bubble_tooltip">
        <div class="bubble_top"><span></span></div>

        <div class="bubble_middle"><span id="bubble_tooltip_content">Content for Tooltip</span></div>

        <div class="bubble_bottom"></div>
    </div>
    <div name="wait.layer" id="wait.layer"  style="position:absolute;left: 40%; top: 50%; background-color: #CCCCCC; layer-background-color: #FFFFFF; border: 1px none #000000;"></div>
    <div name="message.layer" id="message.layer"></div>
    <!-- end of legacy place holder -->

    {assets_js files=$footer_data.javascripts}
</body>

</html>
