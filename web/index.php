<?php $groups = json_decode(file_get_contents("groups.json"), true); ?>
<?php include('functions.php'); ?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>Breakdown by Year | EuroParl on copyrights</title>

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
					<li class="active"><a href="index.php">Breakdown by Year</a></li>
					<li><a href="country.php">Breakdown by Country</a></li>
					<li><a href="about.php">About</a></li>
				</ul>
			</div>
		</nav>

		<div class="container">
			<div class="page-header">
				<h1 id="timeline">
					<span>Years</span>
				</h1>
			</div>

			
			<ul class="timeline">
				<?php for($i=1999; $i<=2014; $i++): ?>
				<li>
					<a class="timeline-badge active" 
						href="year.php?year=<?php echo $i;?>"
						style="<?php echo radius2style($groups[$i]['radius']); ?>" 
					>
						<div class="inner-content-wrapper">
							<div class="inner-content">
								<div class="center">
									<?php echo $i; ?>
								</div>
							</div>
						</div>
					</a>
					<div class="timeline-panel">
					<div class="timeline-body">
					<?php for($j=0; $j<=7; $j++): ?>
					<?php
						$url = "proxy.php?term=".urlencode($groups[$i]['top_phrases'][$j]['token'])."&year=".$i;
					?>
						<div>
							<a 
								target="_blank" href="<?php echo $url; ?>"
								style="font-size:<?php echo $groups[$i]['top_phrases'][$j]['font_size'];?>px"
							>
								<?php echo $groups[$i]['top_phrases'][$j]['token']; ?>
							</a>
						</div>
				
					<?php endfor; ?>
					</div>
					</div>
				</li>
				<?php endfor; ?>
			</ul>
		</div>

		

		<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
		<script src="jquery.min.js"></script>
		<!-- Include all compiled plugins (below), or include individual files as needed -->
		<script src="bootstrap/js/bootstrap.min.js"></script>
	</body>
</html>
