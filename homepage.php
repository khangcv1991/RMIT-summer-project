<?php

// $file = fopen ( "keepedEngTweets_statuses.log.2014-02-01-00.xz.json", "r" ) or die ( "can not open" );
// while ( ! feof ( $file ) ) {
// $line = fgets ( $file );
// $twitter = json_decode ( $line );
// echo $line;
// //echo $twitter->id;
// //print_r($twitter);
// echo"<br/> -------------------------- <br/>";
// //
// }
session_start ();
// fclose ( $file );
if (function_exists ( $_GET ['f'] )) {
	$_GET ['f'] ();
}
//grab ();
// echo json_encode ( getTwitterListByFile ( "data/United States/keepedEngTweets_statuses.log.2014-02-01-00.xz.json" ) );
function grab() {
	// echo $_REQUEST['date'];
// 	// $_SESSION["date"] = $_REQUEST["date"];
// 	$year = "2014";
// 	$month = "02";
// 	$day = "01";
// 	$hour = "";
	$year = intval($_GET['year']);
	$month = intval($_GET['month']);
	$day = intval($_GET['day']);
	$hour = intval($_GET['hour']);
	//echo $hour;
	if($day < 10)
		$day = "0".$day;
	if($month < 10)
		$month = "0".$month;
	if($hour < 10)
		$hour = "0".$hour;
	$query = $year . "-" . $month . "-" . $day. "-".$hour;
	
	
	$array = array ();
	$dir = "data/United States/";
	// echo json_encode ( getTwitterListByFile ( "keepedEngTweets_statuses.log.2014-02-01-00.xz.json" ) );
	
	// Open a directory, and read its contents
	if (is_dir ( $dir ) == true) {
		
		if ($dh = opendir ( $dir )) {
// 			echo "read";
// 			print_r ( readdir ( $dh ) );Ï
			while ( ($file = readdir ( $dh )) !== false ) {
				if ($file != "." && $file != "..") {
					if (strpos ( $file, $query ) !== false) {
						//echo $query;
						$array = array_merge($array,getTwitterListByFile($dir.$file));
					}
				}
				// echo $file;
				
				// break;
			}
			
			echo json_encode ( $array);
			closedir ( $dh );
		} else {
			echo "cannot open dir";
		}
	}
}
function getTwitterListByFile($path) {
	$array = array ();
	$file = fopen ( $path, "r" ) or die ( "can not open" );
	// echo "cannot open file";
	if ($file) {
		while ( ($line = fgets ( $file )) !== false ) {
			// process the line read.
			
			$tmp_object = json_decode ( $line );
			// print_r ( $tmp_object );
			$tmp_item = ( array ) $tmp_object;
			if (( array ) $tmp_item ['geo'] != null) {
				$item = array ();
				// $item ['created_at'] = ( array ) $tmp_item ['created_at'];
				$tmp = ( array ) $tmp_item ['geo'];
				$item = ( array ) $tmp ['coordinates'];
				array_push ( $array, $item );
			}
		}
		return $array;
		
		fclose ( $file );
	} else {
		// error opening the file.
	}
}
?>