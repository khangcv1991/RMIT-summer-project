<?php
get_user_message_list ( "Australia" );
// get_words_list ();
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
// grab();
// data_read_json ();
// data_write_json ();
// fclose ( $file );
if (function_exists ( $_GET ['f'] )) {
	$_GET ['f'] ();
}
// get_user_info("data/Australia/keepedEngTweets_statuses.log.2014-02-01-00.xz.json",array());
// grab ();
// echo json_encode ( getTwitterListByFile ( "data/United States/keepedEngTweets_statuses.log.2014-02-01-00.xz.json" ) );
/**
 * **bad word analysis****
 */
function get_words_list() {
	$filename = "data/words_list";
	$list = data_read_json ( $filename );
	// echo json_encode ( $list ['words'] );
	return $list ['words'];
}
function count_bad_word_in_message($bad_list, $message) {
	// $words = array();
	$count = 0;
	foreach ( $bad_list as $word ) {
		$count += substr_count ( $message, $word );
	}
	if ($count > 0)
		echo $count . "<br/>";
	return $count;
}
function get_user_info($filename, &$list) {
	$file = fopen ( $filename, "r" ) or die ( $filename );
	// echo "cannot open file";
	$bad_word_list = get_words_list ();
	if ($file) {
		while ( ($line = fgets ( $file )) !== false ) {
			// process the line read.
			
			$tmp_object = json_decode ( $line );
			// print_r ( $tmp_object );
			$tmp_item = ( array ) $tmp_object;
			
			// echo $tmp_item ['user'] ->id_str;
			if (array_key_exists ( $tmp_item ['user']->id_str, $list ) == false) {
				$list [$tmp_item ['user']->id_str] = array ();
				$list [$tmp_item ['user']->id_str] ['messages'] = array ();
				$list [$tmp_item ['user']->id_str] ['messages_no'] = 0;
				$list [$tmp_item ['user']->id_str] ['bad_words'] = array ();
				$list [$tmp_item ['user']->id_str] ['bad_words_no'] = 0;
				$list [$tmp_item ['user']->id_str] ['weight'] = 0;
			}
			// $tmp_item = (array)$tmp_item ['user'];
			array_push ( $list [$tmp_item ['user']->id_str] ['messages'], $tmp_item ['text'] );
			$mess_no = $list [$tmp_item ['user']->id_str] ['messages_no'];
			$list [$tmp_item ['user']->id_str] ['messages_no'] = $mess_no + 1;
			
			$bad_word_no = $list [$tmp_item ['user']->id_str] ['bad_words_no'] ;
			$list [$tmp_item ['user']->id_str] ['bad_words_no'] = $bad_word_no + count_bad_word_in_message ( $bad_word_list, $tmp_item ['text'] );
			/*
			 * if ($tmp_item ['text'] != null) {
			 * $item = array ();
			 * // $item ['created_at'] = ( array ) $tmp_item ['created_at'];
			 * $tmp = ( array ) $tmp_item ['geo'];
			 * $item = ( array ) $tmp ['coordinates'];
			 * array_push ( $array, $item );
			 * }
			 */
		}
		// echo json_encode ( $list );
		
		fclose ( $file );
	} else {
		// error opening the file.
	}
}
function get_user_message_list($country) {
	// each item in the list <user_id, message, lastest location, weight>
	$list = array ();
	$dir = "data/" . $country . "/";
	
	// Open a directory, and read its contents
	if (is_dir ( $dir ) == true) {
		
		if ($dh = opendir ( $dir )) {
			// echo "read";
			// print_r ( readdir ( $dh ) );Ï
			while ( ($file = readdir ( $dh )) !== false ) {
				if ($file != "." && $file != "..") {
					get_user_info ( $dir . $file, $list );
				}
				// echo $file;
				
				// break;
			}
			
			// echo json_encode ( $array );
			echo json_encode ( $list );
			closedir ( $dh );
			return $array;
		} else {
			echo "cannot open dir";
		}
	}
}
function weight_user($country) {
}
/**
 * ******
 */
function get_customize_date($year, $month, $day, $hour, $is_full) {
	$date = "" . $year;
	if ($month < 10)
		$date = $date . "-0" . $month;
	else
		$date = $date . "-" . $month;
	
	if ($day < 10)
		$date = $date = $date . "-0" . $day;
	else
		$date = $date = $date . "-" . $day;
	if ($is_full == false)
		return trim ( $date );
	
	if ($hour < 10)
		$date = $date = $date . "-0" . $hour;
	else
		$date = $date = $date . "-" . $hour;
	
	return trim ( $date );
}
function count_message($date) {
	$foldername = "data/Australia";
	$path = $foldername . '/keepedEngTweets_statuses.log.' . $date . '.xz.json';
	$file = fopen ( $path, "r" ) or die ( "can not open" );
	$count = 0;
	if ($file) {
		while ( ($line = fgets ( $file )) !== false ) {
			// process the line read.
			$count ++;
		}
		return $count;
		
		fclose ( $file );
	} else {
		// error opening the file.
	}
}
function has_file($file_date) {
	$foldername = "data/" . $_GET ['country'];
	
	// Open a directory, and read its contents
	if (is_dir ( $foldername ) == true) {
		
		if ($dh = opendir ( $foldername )) {
			
			while ( ($file = readdir ( $dh )) !== false ) {
				if ($file != "." && $file != "..") {
					if (strpos ( $file, $file_date ) !== false) {
						// echo $query;
						return true;
					}
				}
			}
			
			return false;
			closedir ( $dh );
		} else {
			echo "cannot open dir";
		}
	}
}
function data_read_json($filename) {
	
	// $file = fopen ( $filename, "r" ) or die ( "can not open" );
	$data = file_get_contents ( $filename );
	return json_decode ( $data, true );
}
function data_write_json() {
	$foldername = "data/" . $_GET ['country'];
	$filename = $foldername . "/" . "user_message.json";
	$data = array ();
	$month = 1;
	$max_day = 31;
	$i = 0;
	$j = 0;
	for($month = 1; $month < 13; $month ++) {
		// $data[$month] = array();
		if ($month % 2 == 1) {
			$max_day = 31;
		} else {
			$max_day = 30;
		}
		if ($month == 2)
			$max_day = 28;
		for($i = 1; $i <= $max_day; $i ++) {
			$tmp = array ();
			for($j = 0; $j < 24; $j ++) {
				$file_date = get_customize_date ( 2014, $month, $i, $j, true );
				try {
					if (has_file ( $file_date )) {
						// $data [$file_date] = count_message ( $file_date );
						$data [$month] [$i] [$j] = count_message ( $file_date );
					} else {
						$data [$month] [$i] [$j] = 0;
					}
				} catch ( Exception $e ) {
				}
			}
		}
	}
	echo json_encode ( $data );
	
	file_put_contents ( $filename, json_encode ( $data ) );
}
function get_message_no_by_date($month, $day = null) {
	$foldername = "data/" . $_GET ['country'];
	$filename = $foldername . "/" . "user_message.json";
	$data = data_read_json ( $filename );
	if (isset ( $day )) {
		return $data [$month] [$day];
	}
	return $data [$month];
}
function get_user_no_by_date($date) {
}
function grab() {
	$data = array ();
	$data ['location'] = get_location_data ();
	$data ['message'] = get_message_no_by_date ( intval ( $_GET ['month'] ), intval ( $_GET ['day'] ) );
	echo json_encode ( $data );
}
function get_location_data() {
	$country = $_GET ['country'];
	$year = intval ( $_GET ['year'] );
	$month = intval ( $_GET ['month'] );
	$day = intval ( $_GET ['day'] );
	// $hour = intval($_GET['hour']);
	$hour = intval ( $_GET ['hour'] );
	// echo $hour;
	// echo $country;
	
	$query = get_customize_date ( intval ( $_GET ['year'] ), intval ( $_GET ['month'] ), intval ( $_GET ['day'] ), intval ( $_GET ['hour'] ), true );
	
	$array = array ();
	$dir = "data/" . $_GET ['country'];
	
	// $dir = "data/Australia/";
	// echo json_encode ( getTwitterListByFile ( "keepedEngTweets_statuses.log.2014-02-01-00.xz.json" ) );
	
	// Open a directory, and read its contents
	if (is_dir ( $dir ) == true) {
		
		if ($dh = opendir ( $dir )) {
			// echo "read";
			// print_r ( readdir ( $dh ) );Ï
			while ( ($file = readdir ( $dh )) !== false ) {
				if ($file != "." && $file != "..") {
					if (strpos ( $file, $query ) !== false) {
						// echo $query;
						$array = array_merge ( $array, getTwitterListByFile ( $dir . "/" . $file ) );
					}
				}
				// echo $file;
				
				// break;
			}
			
			// echo json_encode ( $array );
			
			closedir ( $dh );
			return $array;
		} else {
			echo "cannot open dir";
		}
	}
}
function getTwitterListByFile($path) {
	$array = array ();
	$file = fopen ( $path, "r" ) or die ( $path );
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