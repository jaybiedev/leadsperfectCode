<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <title>Savvy Software Development</title>
    <link rel="alternate" type="application/rss+xml" title="Lending">
    {assets_css files='/bootstrap-flatly-3.3.7.min.css'}
    {assets_css files='/font-awesome-4.6.3.min.css'}
    {assets_css files='/font-lato.css'}
    {assets_css files='/bootstrap-datetimepicker.min.css'}
    {*assets_css files='/highlight-solarized-light.css'*}
    {*assets_css files='/bootstrap-tagsinput.css'*}
    {assets_css files='/main.css'}

    <!-- legacy code -->
    {assets_css files='/bubble-tooltip.css '}
    {assets_js files='/bubble-tooltip.js'}

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

<body id="body_id" ng-app="BaseApp">
    {include file="{$APPPATH}views/templates/finance/_header.tpl"}
    <div class="container-fluid body-container">
        {$contents}
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

    {assets_js files='/jquery.min.js'}
    {assets_js files='/bootstrap.min.js'}
    {assets_js files='/moment.min.js'}
    {assets_js files='/bootstrap-datetimepicker.min.js'}
    {* assets_js files='/angular.min.js' *}
    {* assets_js files='/main.js' *}
    {assets_js files='/jquery.dataTables.min.js'}
</body>
