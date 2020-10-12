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
echo $_SESSION["input"];
if($_SESSION["input"] < $temperature){
    $command = escapeshellcmd('/home/pi/IoT/SmartNest/turnOn.py');
    $output=shell_exec($command);
    echo "Turning on";
    echo $output;
}
else{
  if($_SESSION["input"] > $temperature)
  {
    $command = escapeshellcmd('/home/pi/IoT/SmartNest/turnOff.py');
    $output=shell_exec($command);
    echo "Turning off";
    echo $output;
  }
}
if (isset($_POST['SetTemp'])) {
  $_SESSION["input"] = $_POST['input'];
  echo $_SESSION["input"];
}

?>
</table>
 <form method="post">
    <input type="text" name="input" id="input">
    <button class="btn" name="SetTemp">Set Temp</button>&nbsp;
 </form>
</body>
</html>
