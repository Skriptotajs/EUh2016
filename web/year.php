<?php
$months = ["Jan.","Feb. ","Mar.","Apr.","May ","June","July","Aug.","Sept.","Oct. ","Nov.","Dec."];
$data = json_decode(file_get_contents("groups_month.json"), true); 
$year = intval($_GET['year']);
$groups = [];
for($i=1; $i<=12; $i++)
{
	$key = $year.'-'.($i < 10 ? '0' : '').$i;
	if(isset($data[$key]))
	{
		$data[$key]['name'] = $months[$i-1];
		$groups[] = $data[$key];
	}
	else
	{
		$groups[] = [
			'size' => 0,
			'name' => $months[$i-1],
		];
	}
	
}
?>
<?php include('functions.php'); ?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>Year <?php echo $year; ?> | EuroParl on Copyrights</title>

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
					<span><?php echo $year;?></span>
				</h1>
			</div>
			
			<ul class="timeline">
				<?php foreach($groups AS $m => $group): ?>
				<li>
					<div class="timeline-badge <?php echo !isset($group['top_phrases']) ? 'disabled' : '';?>" style="<?php echo radius2style($group['radius']); ?>" >
						<div class="inner-content-wrapper">
							<div class="inner-content">
								<div class="center">
									<?php echo $group['name']; ?>
								</div>
							</div>
						</div>
					</div>
					<div class="timeline-panel">
					<div class="timeline-body">
					<?php
						if(isset($group['top_phrases']))
						{
							?>
							<?php for($j=0; $j<=7; $j++): ?>
							<?php
								$url = "proxy.php?term=".urlencode($group['top_phrases'][$j]['token'])."&month=".$year.'/'.($m+1);
							?>
								<div>
									<a 
										target="_blank" href="<?php echo $url; ?>"
										style="font-size:<?php echo $group['top_phrases'][$j]['font_size'];?>px"
									>
										<?php echo $group['top_phrases'][$j]['token']; ?>
									</a>
								</div>
						
							<?php endfor; ?>
							<?php
						}
						else
						{
							echo "<div class='no-data'>NO DATA AVAILAVLE</div>";
						}
					?>
					</div>
					</div>
				</li>
				<?php endforeach; ?>
			</ul>
		</div>

		

		<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
		<script src="jquery.min.js"></script>
		<!-- Include all compiled plugins (below), or include individual files as needed -->
		<script src="bootstrap/js/bootstrap.min.js"></script>
	</body>
</html>
