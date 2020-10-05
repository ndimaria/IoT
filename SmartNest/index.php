<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<body>
<?php

$hostname = "localhost";
$username = "review_site";
$password = "JxSLRkdutW";
$db = "reviews";

$dbconnect=mysqli_connect($hostname,$username,$password,$db);

if ($dbconnect->connect_error) {
  die("Database connection failed: " . $dbconnect->connect_error);
}

?>
<table border="1" align="center">
<tr>
  <td>Date and Time</td>
  <td>Temp</td>
</tr>

<?php

$query = mysqli_query($dbconnect, "SELECT * FROM tempLog")
   or die (mysqli_error($dbconnect));

while ($row = mysqli_fetch_array($query)) {
  echo
   "<tr>
    <td>{$row['datetime']}</td>
    <td>{$row['temperature']}</td>
   </tr>\n";

}

?>
</table>
<?php    
    if (isset($_POST['TurnOn'])) {
      $command = escapeshellcmd('/home/pi/IoT/SmartNest/turnOn.py');
      $output=shell_exec($command);
      echo $output;
    }
    if (isset($_POST['TurnOff'])) {
      $command = escapeshellcmd('/home/pi/IoT/SmartNest/turnOff.py');
      $output=shell_exec($command);
      echo $output;
    }
?>
 <form method="post">
    <button class="btn" name="TurnOn">Turn On</button>&nbsp;
    <button class="btn" name="TurnOff">Turn Off</button><br><br>
 </form>
</body>
</html>
