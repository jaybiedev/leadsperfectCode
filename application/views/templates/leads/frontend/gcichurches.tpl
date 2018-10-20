<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Grace Communion International Local Churches</title>

    <!-- Bootstrap core CSS -->
    <link href="/css/bootstrap.min.css" rel="stylesheet">


<link rel="apple-touch-icon" sizes="57x57" href="/uploads/9c1db3c9-67ae-4948-8ab7-e4e44634d43d/apple-touch-icon-57x57.png">
  <link rel="apple-touch-icon" sizes="60x60" href="/uploads/9c1db3c9-67ae-4948-8ab7-e4e44634d43d/apple-touch-icon-60x60.png">
  <link rel="apple-touch-icon" sizes="72x72" href="/uploads/9c1db3c9-67ae-4948-8ab7-e4e44634d43d/apple-touch-icon-72x72.png">
  <link rel="apple-touch-icon" sizes="76x76" href="/uploads/9c1db3c9-67ae-4948-8ab7-e4e44634d43d/apple-touch-icon-76x76.png">
  <link rel="apple-touch-icon" sizes="114x114" href="/uploads/9c1db3c9-67ae-4948-8ab7-e4e44634d43d/apple-touch-icon-114x114.png">
  <link rel="apple-touch-icon" sizes="120x120" href="/uploads/9c1db3c9-67ae-4948-8ab7-e4e44634d43d/apple-touch-icon-120x120.png">
  <link rel="apple-touch-icon" sizes="144x144" href="/uploads/9c1db3c9-67ae-4948-8ab7-e4e44634d43d/apple-touch-icon-144x144.png">
  <link rel="apple-touch-icon" sizes="152x152" href="/uploads/9c1db3c9-67ae-4948-8ab7-e4e44634d43d/apple-touch-icon-152x152.png">
  <link rel="apple-touch-icon" sizes="180x180" href="/uploads/9c1db3c9-67ae-4948-8ab7-e4e44634d43d/apple-touch-icon-180x180.png">
  <link rel="icon" type="image/png" sizes="32x32" href="/uploads/9c1db3c9-67ae-4948-8ab7-e4e44634d43d/favicon-32x32.png">
  <link rel="icon" type="image/png" sizes="16x16" href="/uploads/9c1db3c9-67ae-4948-8ab7-e4e44634d43d/favicon-16x16.png">    
    <!-- Custom styles for this template -->
    <link href="/uploads/9c1db3c9-67ae-4948-8ab7-e4e44634d43d/main.css" rel="stylesheet">
  
  
    <!-- Custom styles for this template -->
    <style>
        body {
            padding-top: 54px;
        }
        #global-search-box  {
        	border: 1px solid #ccc;
        }
        #global-search-box input {
        	border: none;
        	border-right: 1px solid #ccc;
        }
        #global-search-box select {
        	border: none;
        	background-color:transparent;
        }
        #global-search-box button {
        	border: none;
        	background-color: #d3ab39;
        	color: #fff;
        }
        #global-search-box button:hover {
		}
		
		.active-country {
        	background-color: #d3ab39;
        	color: #fff;
		}
		.active-state {
        	border-color: #d3ab39;
		}
		
        @media (min-width: 992px) {
            body {
                padding-top: 56px;
            }
        }
    </style>

</head>

<body>

<!-- Navigation -->
<nav class="navbar navbar-expand-lg  fixed-top">
    <div class="container">
        <a class="navbar-brand" href="/">
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarResponsive">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item active">
                    <a class="nav-link" href="#">
                        <span class="sr-only">(current)</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#"></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#"></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#"></a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Page Content -->
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12 text-center">
			<div>
		        <img src="/uploads/9c1db3c9-67ae-4948-8ab7-e4e44634d43d/logo.png" 
		        alt="Grace Communion International Logo"
		        class="img-responsive"
		        style="padding: 2rem 3rem;margin: 20px 0; background-color:#ccc;border-radius:8px;">
			</div>
			        	
            <div class="col-lg-6 col-md-8 col-sm-12 text-center" style="margin-left:auto;margin-right:auto;">            
	            <div class="input-group" id="global-search-box">
	            	<input type="text" class="form-control" aria-label="Church search keyword" name="keyword" placeholder="Search Name or keyword">
	  				<div class="input-group-append">
			          [[html_options name=country options=$countries selected=$UserGeolocation->country_code]]
			          [[html_options name=state options=$states selected=$UserGeolocation->region_code]]
					    <button class="btn btn-outline-secondary" type="button">Search</button>
				  	</div>
				</div>
            </div>
        </div>
    </div>
    <div class="row" style="margin-left:auto;margin-right:auto;width: 80%;margin-top:5rem;">
    	<ul class="list-group">
  			[[assign var="state" value=""]]
  			[[assign var="country" value=""]]
    		[[foreach from=$Sites item=$Site]]
    			[[if $Site->country != $country]]
					<li class="list-group-item active-country">[[$Site->country]]</li>
		  			[[assign var="country" value=$Site->country|upper]]
    			[[/if]]
    			[[if $Site->state|upper != $state]]
					<li class="list-group-item active-state">[[$Site->state]]</li>
		  			[[assign var="state" value=$Site->state|upper]]
    			[[/if]]
				<li class="list-group-item">
					<a href="[[$Site->getUrl()]]" target="_localchurch">[[$Site->name]]</a>
					<br>
					<small class="info-address">
						[[if $Site->address1|trim neq '']]
							[[$Site->address1]]
							<br />
						[[/if]]
						[[$Site->city]]  [[$Site->state]] [[$Site->zip]] [[$Site->country]]
					</small>
				</li>
  	    	[[/foreach]]
		</ul>
    </div>
</div>

<!-- Bootstrap core JavaScript -->
<!-- jQuery library -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

<!-- Popper JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>

<!-- Latest compiled JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.3/js/bootstrap.min.js"></script>
</body>

</html>