<?php
//simple chart function
//Creator Seamus Kane
//The creator accepts no liability for usage of this function
//The code is freely available for download on the basis that the author is ackmowledged 
function draw_line_chart($my_array,$date_array,$chart_header,$xscale,$yscale,$y_title)
{
//print_r($my_array);
//print"</br>";
//print_r($date_array);
$array_max=max($my_array);
$array_min=min($my_array);
$array_range = $array_max -$array_min;
$array_count = count($my_array);
//$highest_array_value=$my_array[$array_count-1];
if($array_count>10){$date_interval=intval($array_count/5);}else{$date_interval=1;}
$prev_item=0;
$xmargin=$xscale/10;
$total_xwidth=$xscale+$xmargin;
$total_yheight=$yscale+$xmargin;
$ymagnify=intval(($yscale/$array_max))-2;
$start_x=$xmargin/2;
$x_interval = intval($xscale/$array_count)-1;
$start_y=$start_x;
PRINT <<< END
<canvas id="myCanvas" width="$total_xwidth" height="$total_yheight" style="border:1px solid #d3d3d3; padding:0; margin:auto; display:block;">
Your browser does not support the HTML5 canvas tag.</canvas>
<script>
var c = document.getElementById("myCanvas");
var ctx = c.getContext("2d");
ctx.font = "bold 12px verdana, sans-serif";
ctx.fillStyle= 'red';
//print chart header
ctx.save();
ctx.font = "bold 24px verdana, sans-serif";
ctx.fillStyle= 'black';
ctx.fillText("$chart_header",$total_xwidth/2 -125,20);
ctx.restore();
//print chart header
//print y-axis title
ctx.save();
ctx.font = "bold 24px verdana, sans-serif";
ctx.fillStyle= 'black';
ctx.translate( $x_interval/2, $start_y);
ctx.rotate( Math.PI / 2 );
ctx.fillText("$y_title",$x_interval/2 + 50,0);
ctx.restore();
//print y-axis title
//print x-axis lines
ctx.moveTo(0,$yscale+$start_y);
ctx.lineTo($total_xwidth,$yscale+$start_y);
ctx.moveTo(0,$start_y);
ctx.lineTo($total_xwidth,$start_y);
//print x-axis lines
END;
foreach($my_array as $key => $item) {
$show_item=$item*$ymagnify;
if($my_array[$key+1] >= $item){$txt_offset = 12;} else {$txt_offset = 0;}
$xcord=$key*$x_interval;
$date_item=$date_array[$key];
$pieces = explode(" ", $date_item);
PRINT <<< END
if($key>0)

{
        ctx.font = "bold 12px verdana, sans-serif";
        ctx.fillStyle= 'blue';
//plot graph lines
  if ($key==0)
	{
        ctx.moveTo($xcord+$start_x ,$yscale-$prev_item+$start_y);

	}else{		 
	ctx.moveTo($xcord+$start_x ,$yscale-$prev_item+$start_y);
	ctx.lineTo($xcord+$x_interval+$start_x,$yscale-$show_item+$start_y);
	}
//plot graph lines
}
ctx.stroke();
        ctx.font = "bold 12px verdana, sans-serif";
        ctx.fillStyle= 'blue';
	if ($prev_item!=$show_item)
	{
        ctx.fillText("$item",$xcord+$x_interval+$start_x,$yscale-$show_item+$txt_offset+$start_y);
	}
        ctx.stroke();
        ctx.font = "bold 12px verdana, sans-serif";
        ctx.fillStyle= 'red';
	if  (($key % $date_interval == 0) || $key >= $array_count-1)
	{
	// print date / time
        ctx.fillText("$pieces[1]",$xcord+$x_interval+$start_x,$yscale+15+$start_y);
        ctx.fillText("$pieces[0]",$xcord+$x_interval+$start_x,$yscale+25+$start_y);
	//print date / time 
	// print vertical lines
	ctx.moveTo($xcord+$x_interval+$start_x,$start_y);
	ctx.lineTo($xcord+$x_interval+$start_x,$yscale+$start_y);
	// print vertical lines
	}
END;
$prev_item=$show_item;
}
$line_spacing=intval($array_max/25)+1;
for ($j = 0; $j <= $array_max+1; $j++) {
PRINT <<< END
ctx.font = "bold 14px verdana, sans-serif";
ctx.fillStyle= 'green';
ctx.setLineDash([1, 2]);
if($j  %  $line_spacing == 0)
{
// print horizontal lines
ctx.moveTo($x_interval+$start_x,$yscale-($j*$ymagnify)+$start_y);
ctx.lineTo($xscale,$yscale-($j*$ymagnify)+$start_y);
// print horizontal lines
ctx.fillText("$j",$x_interval+$start_x-25,$yscale-($j*$ymagnify)+$start_y);
}
END;
}
PRINT <<< END
ctx.stroke();
ctx.drawImage(image1, 0, 0, $total_yheight, $total_xwidth);
</script>
END;
}
?>

