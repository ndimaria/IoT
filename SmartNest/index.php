<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<body>
	
	<?php
		session_start();
		$input=$_GET['input'];
		$page = $_SERVER['PHP_SELF'];
		$sec = "10";

		$hostname = "localhost";
		$username = "review_site";
		$password = "JxSLRkdutW";
		$db = "reviews";

		$dbconnect=mysqli_connect($hostname,$username,$password,$db);

		if ($dbconnect->connect_error) {
			die("Database connection failed: " . $dbconnect->connect_error);
		}
	?>
	
	<meta http-equiv="refresh" content="<?php echo $sec?>;URL='<?php echo $page?>'">
	<table border="1" align="center">
	<tr>
	  <td>Date and Time</td>
	  <td>Temp</td>
	</tr>

	<?php
	
	$query = mysqli_query($dbconnect, "SELECT * FROM tempLog ORDER BY datetime DESC LIMIT 1")
	   or die (mysqli_error($dbconnect));

	while ($row = mysqli_fetch_array($query)) {
	  $temperature = $row['temperature'];
	  echo
	   "<tr>
		<td>{$row['datetime']}</td>
		<td>{$row['temperature']}</td>
	   </tr>\n";
	}
	
	$query = mysqli_query($dbconnect, "SELECT * FROM info ORDER BY ID DESC LIMIT 1")
	   or die (mysqli_error($dbconnect));
	   
	$row = mysqli_fetch_array($query);
	$_SESSION["input"] = $row['temp'];
	$_SESSION["status"] = $row['status'];
	
	echo "<div align='center'>The temperature is currently set to: </div>";
	echo "<h1 align='center'>".$_SESSION["input"]."</h1>";
	echo "<div align='center'>The AC is currently ".$_SESSION["status"]."<br><br></div>";
	
	if($_SESSION["input"] < $temperature && ($_SESSION["status"] == "TRANSITIONING" || $_SESSION["status"] == "OFF")){
		$command = escapeshellcmd('/home/pi/NickIoT/SmartNest/turnOn.py');
		$output=shell_exec($command);
		$_SESSION["status"] = "ON";
		$sql="INSERT INTO info (temp, status) VALUES ('".$_SESSION["input"]."','".$_SESSION["status"]."')";
		if (mysqli_query($dbconnect, $sql)) {
			echo "<div align='center'>The AC is turning ON. Status: ".$output."<br><br></div>";
		} else {
			echo "<div align='center'>Error: ". $sql ."". mysqli_error($conn)."<br><br></div>";
		}
		
	}
	else if($_SESSION["input"] > $temperature && ($_SESSION["status"] == "TRANSITIONING" || $_SESSION["status"] == "ON")){
		$command = escapeshellcmd('/home/pi/NickIoT/SmartNest/turnOff.py');
		$output=shell_exec($command);
		$_SESSION["status"] = "OFF";
		$sql="INSERT INTO info (temp, status) VALUES ('".$_SESSION["input"]."','".$_SESSION["status"]."')";
		if (mysqli_query($dbconnect, $sql)) {
			echo "<div align='center'>The AC is turning OFF. Status: ".$output."<br><br></div>";
		} else {
			echo "<div align='center'>Error: " . $sql . "" . mysqli_error($conn)."<br><br></div>";
		}
	}
	
	if (isset($_POST['SetTemp'])) {
	  $_SESSION["input"] = intval($_POST['input']);
	  $_SESSION["status"] = "TRANSITIONING";
	  $sql="INSERT INTO info (temp, status) VALUES ('".$_SESSION["input"]."','".$_SESSION["status"]."')";
	  if (mysqli_query($dbconnect, $sql)) {
            echo  "<div align='center'>Changing thermostat temperature to: ".$_SESSION["input"]."<br><br></div>";
          } else {
            echo "<div align='center'>Error: " . $sql . "" . mysqli_error($conn)."<br><br></div>";
          }
          $dbconnect->close();
        }
	
	?>
	
	</table>
	<h1></h1>
	<form method="post" align = "center">
		<input type="text" name="input" id="input">
		<button class="btn" name="SetTemp">Set Temp</button>&nbsp;
	</form>

	<?php
	require("PhpSimpleChart2.php");
	$temps = array();
	$dates = array();
	$count = 0;
	$query = mysqli_query($dbconnect, "SELECT * FROM tempLog ORDER BY datetime DESC LIMIT 360")
	   or die (mysqli_error($dbconnect));
	while ($row = mysqli_fetch_array($query)) {
		if($count == 36){
			$temps[] = $row['temperature'];
			$dates[] = $row['datetime'];
			$count = 0;
		}
		else{
			$count = $count + 1;
		}
	}
	
	$temps = array_reverse($temps);
	$dates = array_reverse($dates);
	//$temps=array("12","14","15","16");
	//$dates=array("2020-11-02", "2020-11-03","2020-11-04", "2020-11-05");
	$chart_text="Temperature Logs";
	$y_title="Temp Deg C";
	$x_scale=1000;
	$y_scale=400;

	draw_line_chart($temps,$dates,$chart_text,$x_scale,$y_scale,$y_title);
	?>
	
</body>
</html>
