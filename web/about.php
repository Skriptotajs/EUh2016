<?php $groups = json_decode(file_get_contents("groups.json"), true); ?>
<?php include('functions.php'); ?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>About | EuroParl on copyrights</title>

		<!-- Bootstrap -->
		<link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
		<link href="style.css" rel="stylesheet">

	</head>
	<body>
		<nav class="navbar navbar-default navbar-static-top">
			<div class="container">
				<div class="navbar-header">
					<a class="navbar-brand" href="index.php">EuroParl on Copyrights</a>
				</div>
				<ul class="nav navbar-nav">
					<li><a href="index.php">Breakdown by Year</a></li>
					<li><a href="country.php">Breakdown by Country</a></li>
					<li class="active"><a href="about.php">About</a></li>
				</ul>
			</div>
		</nav>

		<div class="container" id="about">
			<div class="page-header">
				<h1 id="timeline" class="text-center">
					<strong><em>Europarl on Copyrights</em></strong>
				</h1>
				<h2 class="text-center" style="color:#666">
					Roberts Darģis
				</h2>
			</div>

			<div style="font-size:30px;text-align:justify;line-height: 150%;">
				<p>
					<strong><em>EuroParl</em></strong> is acronym for <em><a href="http://www.europarl.europa.eu/aboutparliament/en" target="_blank">European Parliament</a></em>.
				</p>
				<p>
					<strong><em>EuroParl on Copyrights</em></strong> contains overview of debates from plenary sittings of <em>EuroParl</em> about copyrights, agregated by year or by country.
				</p>
				<p>
					A debate is cosnidered to be about copyrights, if it contains <strong><em>copyright</em></strong>, <strong><em>patent</em></strong>, <strong><em>trademark</em></strong> or <strong><em>intellectual property</em></strong>.
				</p>
				<p>
					Some fancy <strong><em>Natural Langauge Processing</em></strong> is used to find the most relevant phraes for each year.
				</p>
				<ul>
					<li><strong><em>Part-of-Speech tagger</em></strong> to find phrases from text.</li>
					<li>Two level wheighting using <strong><em>term frequency–inverse document frequency</em></strong> models.</li>
				</ul>
				<p>
					Data has been taken from: <em>Aggelen, A.E. van, Hollink, L. Plenary debates of the European Parliament as Linked Open Data.</em> <a href="http://www.talkofeurope.eu/data/" target="_blank">Talk of Europe</a>.
				</p>
			</div>
		</div>

		

		<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
		<script src="jquery.min.js"></script>
		<!-- Include all compiled plugins (below), or include individual files as needed -->
		<script src="bootstrap/js/bootstrap.min.js"></script>
	</body>
</html>
