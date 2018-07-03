<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title><?=$title;?></title>
    <meta name="description" content="">
    <meta name="author" content="">

    <?= assets_css($stylesheets); ?>

    <link rel="icon" href="asset/favicon.ico">
</head>
<body>
<div class="nav-side-menu">
    <div class="brand">Brand Logo</div>
    <i class="fa fa-bars fa-2x toggle-btn" data-toggle="collapse" data-target="#menu-content"></i>

    <div class="menu-list">

        <ul id="menu-content" class="menu-content collapse out">
            <li>
                <a href="#">
                    <i class="fa fa-dashboard fa-lg"></i> Dashboard
                </a>
            </li>

            <li  data-toggle="collapse" data-target="#products" class="collapsed active">
                <a href="#"><i class="glyphicon glyphicon-tasks"></i> Products <span class="arrow"></span></a>
            </li>
            <ul class="sub-menu collapse" id="products">
                <li class="active"><a href="#">Catalog</a></li>
                <li><a href="#">Categories</a></li>
            </ul>

            <li  data-toggle="collapse" data-target="#sales" class="collapsed">
                <a href="#"><i class="glyphicon glyphicon-usd"></i> Sales <span class="arrow"></span></a>
            </li>
            <ul class="sub-menu collapse" id="sales">
                <li class="active"><a href="#">Reading</a></li>
                <li><a href="#">End of Day</a></li>
            </ul>


            <li data-toggle="collapse" data-target="#service" class="collapsed">
                <a href="#"><i class="glyphicon glyphicon-signal"></i> Services <span class="arrow"></span></a>
            </li>
            <ul class="sub-menu collapse" id="service">
                <li>Products</li>
                <li>Users</li>
                <li>Payment Types</li>
            </ul>


            <li data-toggle="collapse" data-target="#settings" class="collapsed">
                <a href="#"><i class="glyphicon glyphicon-cog"></i> Settings <span class="arrow"></span></a>
            </li>
            <ul class="sub-menu collapse" id="settings">
                <li>New New 1</li>
                <li>New New 2</li>
                <li>New New 3</li>
            </ul>


            <li>
                <a href="#">
                    <i class="glyphicon glyphicon-user"></i> Profile
                </a>
            </li>

            <li>
                <a href="#">
                    <i class="fa fa-users fa-lg"></i> Users
                </a>
            </li>
        </ul>
    </div>
</div>