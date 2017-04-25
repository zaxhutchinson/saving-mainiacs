<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <link href="Profile.css" rel="stylesheet" media="all" />
        <meta http-equiv="content-type" content="text/html; charset=UTF-8">
        <title></title>
    </head>
    <div id ="banner">
    	<h1><strong><span style="color: #fff;">Bangor </span></strong><span style="color: #939598;">Community</span></h1>
    	<div class = "bannerRight">
    		<?php
				$url = 'https://abnet.ddns.net/mucoftware/remote/get_user.php?user=helpfulguy78&password=helpfulguy78';
				$jsondata = file_get_contents($url);
				$obj = json_decode($jsondata,true);
				echo "<h3>Welcome, ".$obj["results"][0]['UserName']."!</h3>";
			?>
			<form action="index.php">
			<input type="submit" name="logout" value="Logout">
			</form>
    		<!-- <h3>Hello </h3> -->
    	</div>
    </div>
    <div id="contain">
		<body>
			<main>
				<div id="bod">
					<div id="donationLeftUser">
						<div id ="chart">
							<?php
								$url = 'https://abnet.ddns.net/mucoftware/remote/get_user_donationrate.php?user=helpfulguy78&password=helpfulguy78';
								$jsondata = file_get_contents($url);
								$obj = json_decode($jsondata,true);
								$CharityName	= $obj['results'][0]['CharityName'];
								$Percent = $obj['results'][0]['Percent'];
							?>
							<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
							    <script>
							      	google.charts.load('current', {'packages':['corechart']});
							      	google.charts.setOnLoadCallback(drawChart);
							      	var one = "Work";
							      	var two = "Work";
							      	var three = "Work";
							      	var four = "Work";
							      	var five = "Work";
							      	var name = '<?php echo $CharityName; ?>';
							      	var perc = '<?php echo $Percent; ?>';
							      	var oneH = 20;
							      	var twoH = 20;
							      	var threeH = 20;
							      	var fourH = 20;
							      	var fiveH = 20;
							    	function drawChart() {

							        var data = google.visualization.arrayToDataTable([
							          ['Task', 'Percent'],
							          [name,     perc],
							          [two,      twoH],
							          [three,  threeH],
							          [four, fourH],
							          [five,    fiveH]
							        ]);

							        var options = {
							        'legend':'none',
							        'backgroundColor':'transparent',
							        pieHole :.5,
							        chartArea:{right: 120,width:'100%',height:'100%'},
							          colors: ['#064F94', '#054582', '#053B6F', '#04315D', '#03284A']
							        };

							        var chart = new google.visualization.PieChart(document.getElementById('piechart'));

							        chart.draw(data, options);
							      }
							  </script>
							  <div id="piechart" style="width: 500px; height: 75%;"></div>
						</div>
						<div id ="charityList">
							<?php
								// $json_string = file_get_contents("https://abnet.ddns.net/mucoftware/remote/get_all_quests.php");
								$json_string = file_get_contents("quest.json");
								$array = json_decode($json_string, true);

								echo '<table>';
								echo '<tr><th>Quantity</th><th>Name</th><th>Drop Off Location</th><th>Coin Payment</th></tr>';

								$n = 0;

								foreach ($array as $key => $jsons) {
									foreach ($jsons as $key => $value) {
										echo '<tr>';
										echo '<td>'.$array["results"][$n]['CharityName'].'</td>';
										echo '<td>'.$array["results"][$n]['QuestBank'].'</td>';
										echo '<td>'.$array["results"][$n]['DropOffLocation'].'</td>';
										echo '<td>'.$array["results"][$n]['Payment'].'</td>';
										echo '</tr>';

									$n++;
								} 
								}
								echo '</table>';
							?>
						</div>
					</div>
					<center>
					<div id="profileUser">
						<div id="pic">
							<?php
								// $url = 'https://abnet.ddns.net/mucoftware/remote/get_user_picture.php?userid=1';
								// $jsondata = file_get_contents($url);
								// $obj = json_decode($jsondata,true);
								// echo '<img class =img-circle src="' . $obj["type"] . ',' . $obj["data"] .'"/>';
							?>						
					</div>
						<div id="coinsCurrent">
							<div class ="coins">
							<h2>Coins: </h2>
							<h4>Since 1/2/2012</h4>
							<br>
							<br>
							<br>
								<?php
									$url = 'https://abnet.ddns.net/mucoftware/remote/get_user.php?user=helpfulguy78&password=helpfulguy78';
									$jsondata = file_get_contents($url);
									$obj = json_decode($jsondata,true);
									echo "<h1>".$obj["results"][0]['Coins']."</h1>";
								?>
							</div>
						<div class ="steps">
							<h2>Steps: </h2>
							<h4>Since 1/2/2012</h4>
							<br>
							<br>
							<br>
								<?php
									$url = 'https://abnet.ddns.net/mucoftware/remote/get_user.php?user=helpfulguy78&password=helpfulguy78';
									$jsondata = file_get_contents($url);
									$obj = json_decode($jsondata,true);
									echo "<h1>".$obj["results"][0]['DaySteps']."</h1>";
								?>
						</div>
						</div>
					</div>
					</center>
					<div id="coinTotalUser">
						<div id ="totalCoinsEarned">
						<h2>Total Coins Earned: </h2>
						<h4>Since 1/2/2012</h4>
						<br>
						<br>
						<br>
							<?php
								$url = 'https://abnet.ddns.net/mucoftware/remote/get_user.php?user=helpfulguy78&password=helpfulguy78';
								$jsondata = file_get_contents($url);
								$obj = json_decode($jsondata,true);
								echo "<h1>".$obj["results"][0]['TotalCoins']."</h1>";
							?>
						</div>
						<div id ="totalSteps">
						<h2>Total Steps Taken: </h2>
						<h4>Since 1/2/2012</h4>
						<br>
						<br>
						<br>
							<?php
								$url = 'https://abnet.ddns.net/mucoftware/remote/get_user.php?user=helpfulguy78&password=helpfulguy78';
								$jsondata = file_get_contents($url);
								$obj = json_decode($jsondata,true);
								echo "<h1>".$obj["results"][0]['TotalSteps']."</h1>";
							?>
						</div>
						<div id="totalDonations">
						<h2>Total Donations: </h2>
						<h4>Since 1/2/2012</h4>
						<br>
						<br>
						<br>
							<?php
								$url = 'https://abnet.ddns.net/mucoftware/remote/get_user.php?user=helpfulguy78&password=helpfulguy78';
								$jsondata = file_get_contents($url);
								$obj = json_decode($jsondata,true);
								echo "<h1>$".$obj["results"][0]['TotalCoins']*.189."</h1>";
							?>
						</div>
					</div>
				 </div>
			</main>
		</body>
    </div>
    <footer>
	<p style="height: 26px; padding-bottom: 0px; margin-bottom: 10px;">
        <strong>Bangor Support: 1.877.226.4671</strong> <span class="eh"><a href="mailto:bangorsupport@bangor.com" onclick="PopEmail(this.href); return false;">bangorsupport@bangor.com</a></span>
        <a href="https://www.facebook.com/bangorsavingsbank" onclick="Departure(this.href, 'general'); return false;" style="height: 25px; position: relative; top: 4px;"><img src="fb-icons-foot.png" width="25px" height="25px"></a>
        <a href="https://twitter.com/bangorsavings" onclick="Departure(this.href, 'general'); return false;" style="height: 25px; position: relative; top: 4px; left: 12px;"><img src="tw-icons-foot.png" width="31px" height="25px"></a>
        <a href="https://www.youtube.com/channel/UC6hwOwRZiNJGfBnmLgMxSAA" onclick="Departure(this.href, 'general'); return false;" style="height: 25px; position: relative; top: 4px; left: 12px;"><img src="yt-logo-foot.png" height="25px" width="60px"></a>
        <a href="https://www.linkedin.com/company/bangor-savings-bank" onclick="Departure(this.href, 'general'); return false;" style="height: 25px; position: relative; top: 4px; left: 12px;"><img src="li-icons-foot.png" height="25px" width="28px"></a>        
      </p>
      <br>
	<div id="footer">
		<ul>
			<li>
				<a href="http://www.bangor.com/About-Us/Career-Opportunities.aspx" target="_self">Careers</a>
			</li>
			<li>
				<a href="http://www.bangor.com/About-Us/Contact-Us.aspx" target="_self">Contact Us</a>
			</li>
		</ul>
		<br>
		<p><a href="http://www.bangor.com/Utility/Copyright.aspx">Copyright © 2017 Bangor Savings Bank</a> <span>Member FDIC</span> <span>Equal Housing Lender</span></p>
	</div>
</footer>

</html>