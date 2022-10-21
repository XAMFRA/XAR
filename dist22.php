<?php
require 'sky_conn.php';
header("DON'T-TRY: TO-HACK-US");
header('X-Powered-By: skylimit');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');
header('X-Content-Type-Options: nosniff');
header("Referrer-Policy: origin-when-cross-origin");
header("Cache-Control: no-store");
header("Feature-Policy: microphone 'none'; camera 'none'");

date_default_timezone_set("America/Los_Angeles");

$sky_lead_id = array();
$sky_nol = 0;
$sky_nols = 0;
$sky_order_id = array();
$stime = idate("H").":".idate("i");
echo $stime;
echo "<br>";
// GET ALL NOT ASSIGNED LEADS ID
// SELECT * FROM `order` WHERE `order_date`='2022-10-13' AND order_time < '19:28' AND leads > lead_n 
$notAssinedLead = mysqli_query($connl, "SELECT lead_id FROM `leads` WHERE `assigned_to_order` = 'Not Assigned'");
while($naRow = mysqli_fetch_assoc($notAssinedLead)) {
	array_push($sky_lead_id,$naRow["lead_id"]);
}

var_dump($sky_lead_id);
echo "<br>";

$sqlconnect1 = mysqli_query($connl, "SELECT * FROM `order` WHERE `order_date`='".date('Y-m-d')."' AND order_time < '".$stime."'  AND leads > lead_n");

echo "SELECT * FROM `order` WHERE `order_date`='".date('Y-m-d')."' AND order_time < '".$stime."'  AND leads > lead_n";
echo "<br>";
// GET BIGGER LEAD FORM ORDERS
while($goRow = mysqli_fetch_assoc($sqlconnect1)) {
	if ($goRow['order_time'] < $stime) {
		if ($goRow['lead_n'] < $goRow['leads']) {
			array_push($sky_order_id,$goRow['order_id']);
			echo $goRow['lead_n'] < $goRow['leads'];
		}
	}

	echo $goRow['order_id'];
	echo ' : : ';
	echo $goRow['leads'];
	echo ' : : ';
	echo $goRow['lead_n'];
	echo '<br>';

}

var_dump($sky_order_id);
echo "<br>";

if (count($sky_order_id) > 0) {
	$sky_order_for = 0;
	# Distribution all The Leads On orders
	for ($z = 0; $z < count($sky_lead_id); $z++) {
		$sky_or = mysqli_fetch_array(mysqli_query($connl,'SELECT * FROM `order` WHERE `order_id`='.$sky_order_id[$sky_order_for]));

		if ($sky_or['lead_n'] < $sky_or['leads']) {
			$sky_l_i = $sky_or['order_id'];
			$sky_l_n = $sky_or['lead_n'];
			echo $z;
			echo "<br>";
			$sky_num_old = $sky_l_n+1;
			mysqli_query($connl,"UPDATE `order` SET `lead_n`=".$sky_num_old." WHERE `order_id`=".$sky_order_id[$sky_order_for]);
			mysqli_query($connl,"UPDATE `leads` SET `assigned_to_order`='assigned',`order_i`='".$sky_order_id[$sky_order_for]."' WHERE `lead_id` =".$sky_lead_id[$z]);
		} else {
			array_splice($sky_order_id, $sky_order_for, 1);
			if (count($sky_order_id) == 0) {
				break;
			}
			$sky_order_for = -1;
			$z = $z-1;
		}
		// RE-Fill
		$sky_order_for = $sky_order_for+1;
		if ($sky_order_for >= count($sky_order_id)) {
			$sky_order_for = 0;
		} elseif (count($sky_order_id) == 0) {
			break;
		}
	}
}
?>