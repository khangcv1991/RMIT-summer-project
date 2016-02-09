<?php
ini_set ( 'max_execution_time', 300 );

$bad_word_list = get_words_list ();

$root_folder = "United States";

get_user_message_list ();

/**
 * general functions ***
 */
function has_file($file_date) {
	global $root_folder;
	$foldername = "data/" . $root_folder;
	
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
function get_customize_date2($month, $day, $day_no) {
	if ($day_no > 31)
		die ( "invalid input" );
	$out_day = $day + $day_no;
	$out_month = $month;
	if ($month == 2 && $out_day > 29) {
		$out_day = $out_day - 29;
		$out_month = $out_month + 1;
	}
	if ($out_day > 30 && $month % 2 == 0 && month != 2) {
		$out_day = $out_day - 30;
		$out_month = $out_month + 1;
	}
	
	if ($out_day > 31 && $month % 2 == 1) {
		$out_day = $out_day - 31;
		$out_month = $out_month + 1;
	}
	if ($out_day < 0) {
		if ($month - 1 < 1)
			die ( "invalid input" );
		if (($month - 1) % 2 == 1) {
			$out_day = 31 + $out_day;
			$out_month --;
		}
		if (($month - 1) % 2 == 0 && $month - 1 != 2) {
			$out_day = 30 + $out_day;
			$out_month --;
		}
		if ($month - 1 == 2) {
			$out_day = 29 + $out_day;
			$out_month --;
		}
	}
	return "" . $out_day . "-" . $out_month;
}

/**
 * ----end----
 */
/**
 * write and read a file which has the number of twitter message for each day****
 */
function data_read_json($filename) {
	
	// $file = fopen ( $filename, "r" ) or die ( "can not open" );
	$data = file_get_contents ( $filename );
	return json_decode ( $data, true );
}
function data_write_json() {
	// $foldername = "data/" . $_GET ['country'];
	global $root_folder;
	$root_folder = "Australia";
	echo $root_folder;
	$foldername = "data/" . $root_folder;
	$filename = $foldername . "/" . "user_message.json";
	echo $filename;
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
/**
 * end ****
 */

/**
 * write user information to a file which has name as user id **
 */
function write_user_info($user_id, $content, $root_folder) {
	global $bad_word_list;
	
	$file_name = "data/" . $root_folder . "/user/" . $user_id . ".json";
	if (file_exists ( $file_name ) == false) {
		$file = fopen ( $file_name, "w" ) or die ( $file_name );
		fclose ( $file );
		chmod ( $file_name, 0777 );
		$data = array (
				"messages" => array (),
				"messages_no" => 0,
				"bad_words" => array (),
				"bad_words_no" => 0,
				"weight" => 0,
				"location" => array () 
		);
		
		file_put_contents ( $file_name, json_encode ( $data ) );
	}
	$data_string = file_get_contents ( $file_name );
	$data = json_decode ( $data_string, true );
	$tmp_item = json_decode ( $content, true );
	var_dump ( $tmp_item );
	array_push ( $data ["messages"], $tmp_item ['text'] );
	$mess_no = $data ["messages_no"];
	$data ["messages_no"] = $mess_no + 1;
	$bad_word_no = $data ['bad_words_no'];
	$data ['bad_words_no'] = $bad_word_no + count_bad_word_in_message ( $bad_word_list, $tmp_item ['text'] );
	
	// $data ['location'] = $tmp_item ['geo'] ['coordinates'];
	if ($tmp_item ['geo'] ['coordinates'] != null)
		$data ['location'] = array_merge ( $data ['location'], $tmp_item ['geo'] ['coordinates'] );
	
	file_put_contents ( $file_name, json_encode ( $data ) );
}
/**
 * **
 */

/**
 * Bad people list functions ---- start ------
 */

// get bad word list
function get_words_list() {
	$filename = "data/words_list";
	$list = data_read_json ( $filename );
	// echo json_encode ( $list ['words'] );
	return $list ['words'];
}
// count the number of bad words which user used in their message
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

// return potential bad people list
function write_bad_people_list() {
	global $root_folder;
	$dir = "data/" . $root_folder . "/user/";
	echo $dir;
	$list = array ();
	$list ['users'] = array ();
	$list ['location'] = array ();
	if (is_dir ( $dir ) == true) {
		
		if ($dh = opendir ( $dir )) {
			// echo "read";
			// print_r ( readdir ( $dh ) );Ï
			while ( ($file = readdir ( $dh )) !== false ) {
				echo "<br/>";
				echo $dh;
				if ($file != "." && $file != "..") {
					// get_user_info ( $dir . $file, $list );
					$tmp_str = file_get_contents ( $dir . $file );
					
					$tmp_json = json_decode ( $tmp_str, true );
					if ($tmp_json ['bad_words_no'] > 0) {
						/*
						 * $tmp_user = array();
						 * $tmp_user['id_str'] = str_replace(".json", "", $file);
						 * $tmp_user['messages'] = $tmp_json['messages'];
						 * $tmp_user['messages_no'] = $tmp_json['messages_no'];
						 * $tmp_user['bad_words'] = $tmp_json['bad_words'];
						 * $tmp_user['bad_words_no'] = $tmp_json['bad_words_no'];
						 */
						$tmp_json ['id_str'] = str_replace ( ".json", "", $file );
						array_push ( $list ['users'], $tmp_json );
						$list ['location'] = array_merge ( $list ['location'], $tmp_json ['location'] );
					}
				}
				// echo $file;
				
				// break;
			}
			
			// echo json_encode ( $array );
			$re_data = array ();
			foreach ( array_filter ( $list ['location'] ) as $item )
				array_push ( $re_data, $item );
			return $re_data;
			closedir ( $dh );
			// return $array;
		} else {
			echo "cannot open dir";
		}
	}
}
?>