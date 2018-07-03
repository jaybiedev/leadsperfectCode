<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <link rel="alternate" type="application/rss+xml" title="Lending">
    <title>{block name=title}{/block}</title>
    {block name=head}{/block}
    {assets_css files=$header_data.stylesheets}
    <link href="/assets/js/angular/lib/css/angular-material.css" rel="stylesheet">
    <link href="/assets/js/angular/lib/css/angular-material-sidemenu.css" rel="stylesheet">
    <link href="/assets/js/angular/lib/css/sidenav.css" rel="stylesheet">
    <link href="/assets/js/angular/lib/css/material-icons.css" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="/images/logo-savvy.png" />
</head>
<body id="{$page_id}" ng-app="ngMaterialSidemenu" ng-controller="DashboardSalesController as app">
    <div class="container">
        {include file="{$APPPATH}views/templates/dashboard/sidenav.tpl"}

        {if $View->page_title neq ''}
            <div class="row">
                <h3>{$View->page_title}</h3>
            </div>
        {/if}
        {block name=body}{/block}
    </div>

{assets_js files=$footer_data.javascripts}
<script src="/assets/js/angular/lib/js/angular-animate.js"></script>
<script src="/assets/js/angular/lib/js/angular-aria.js"></script>
<script src="/assets/js/angular/lib/js/angular-material.js"></script>
<script src="/assets/js/angular/lib/js/angular-material-sidemenu.js"></script>
<!-- todo: load depending on dashboard controller-->
<script src="/assets/js/angular/controllers/dashboard.sales.ng.controller.js"></script>

</body>

</html>
