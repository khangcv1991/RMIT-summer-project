<?php
ini_set ( 'max_execution_time', 300 );

$bad_word_list = get_words_list ();

$root_folder = "United States";
read_bad_people_list ( 1000 );

//initate_user_data();
// get_user_message_list ();
// write_bad_people_list ();
// get_user_message_list ();
function add_dummy_data_for_user() {
	// each item in the list <user_id, message, lastest location, weight>
	global $root_folder;
	$list = array ();
	$dir = "data/" . $root_folder . "/"."user/";
	
	// Open a directory, and read its contents
	if (is_dir ( $dir ) == true) {
		
		if ($dh = opendir ( $dir )) {
			// echo "read";
			// print_r ( readdir ( $dh ) );Ï
			while ( ($file = readdir ( $dh )) !== false ) {
				echo $file;
				if ($file != "." && $file != ".." && intval($file) >= 0) {
					/*
					 42.080617734132, -93.26355664062498
					 40.629301849760935, -87.70447460937498
					 33.04404077043604, -112.13806835937498
					 30.957267749065043, -95.293287109375
					 */
				}
			}
			
			echo json_encode ( $list );
			closedir ( $dh );
			return $array;
		} else {
			echo "cannot open dir";
		}
	}
}
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
function initate_user_data() {
	// each item in the list <user_id, message, lastest location, weight>
	global $root_folder;
	$list = array ();
	$dir = "data/" . $root_folder . "/";
	
	// Open a directory, and read its contents
	if (is_dir ( $dir ) == true) {
		
		if ($dh = opendir ( $dir )) {
			// echo "read";
			// print_r ( readdir ( $dh ) );Ï
			while ( ($file = readdir ( $dh )) !== false ) {
				echo $file;
				if ($file != "." && $file != ".." && strpos ( $file, "keepedEngTweets_statuses" ) >= 0) {
					get_user_info ( $dir . $file, $list );
				}
			}
			
			echo json_encode ( $list );
			closedir ( $dh );
			return $array;
		} else {
			echo "cannot open dir";
		}
	}
}
function get_user_info($filename, &$list) {
	$file = fopen ( $filename, "r" ) or die ( $filename );
	echo $filename;
	global $bad_word_list; // = get_words_list ();
	global $root_folder;
	if ($file) {
		while ( ($line = fgets ( $file )) !== false ) {
			// process the line read.
			
			$tmp_object = json_decode ( $line, true );
			// print_r ( $tmp_object );
			// $tmp_item = ( array ) $tmp_object;
			
			// echo $tmp_item ['user'] ->id_str;
			try {
				echo $line;
				write_user_info ( $tmp_object ['user'] ['id_str'], $line, $root_folder );
			} catch ( Exception $e ) {
				echo $filename;
			}
		}
		// echo json_encode ( $list );
		
		fclose ( $file );
	} else {
		// error opening the file.
	}
}
function write_user_info($user_id, $content, $root_folder) {
	global $bad_word_list;
	
	$file_name = "data/" . $root_folder . "/user/" . $user_id . ".json";
	echo $file_name;
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
				"location" => array (),
				"friends" => array()
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
	//add dummy location data for user's friends
	/*
	 42.080617734132, -93.26355664062498
	 40.629301849760935, -87.70447460937498
	 33.04404077043604, -112.13806835937498
	 30.957267749065043, -95.293287109375
	 */
	
	$data ['friends'][0] = 1;
	$data ['friends'][1] = 2;
	$data ['friends'][2] = 3;
	$data ['friends'][3] = 4;
	
	
	
	// $data ['location'] = $tmp_item ['geo'] ['coordinates'];
	if ($tmp_item ['geo'] ['coordinates'] != null)
		// $data ['location'] = array_merge ( $data ['location'], $tmp_item ['geo'] ['coordinates'] );
		$data ['location'] [count ( $data ['location'] )] = $tmp_item ['geo'] ['coordinates'];
	
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
function read_bad_people_list($no_record = 10) {
	global $root_folder;
	$dir = "data/" . $root_folder . "/user/";
	$data_str = file_get_contents ( $dir . "black_list.json" );
	$data = json_decode ( $data_str, true );
	$count = 0;
	$list = array ();
	
	// echo $dir;
	foreach ( $data ['users'] as $user ) {
		if ($count >= $no_record)
			break;
		array_push ( $list, $user );
		$count ++;
	}
	echo json_encode ( $list );
}
function write_bad_people_list() {
	global $root_folder;
	$dir = "data/" . $root_folder . "/user/";
	// echo $dir;
	$list = array ();
	$list ['users'] = array ();
	$list ['location'] = array ();
	if (is_dir ( $dir ) == true) {
		
		if ($dh = opendir ( $dir )) {
			// echo "read";
			// print_r ( readdir ( $dh ) );Ï
			while ( ($file = readdir ( $dh )) !== false ) {
				
				if ($file != "." && $file != "..") {
					// get_user_info ( $dir . $file, $list );
					$tmp_str = file_get_contents ( $dir . $file );
					
					$tmp_json = json_decode ( $tmp_str, true );
					if ($tmp_json ['bad_words_no'] > 0) {
						// echo $tmp_str;
						
						$tmp_json ['id_str'] = str_replace ( ".json", "", $file );
						array_push ( $list ['users'], $tmp_json );
						$list ['location'] = array_merge ( ( array ) $list ['location'], ( array ) $tmp_json ['location'] );
					}
				}
				// echo $file;
				
				// break;
			}
			
			// echo json_encode ( $list );
			$re_data = array ();
			foreach ( array_filter ( $list ['location'] ) as $item )
				array_push ( $re_data, $item );
				// return $re_data;
			if (file_exists ( $dir . "black_list.json" ) == false) {
				fopen ( $dir . "black_list.json", "w" ) or die ( "cant open " . $dir . "black_list.json" );
			}
			for($i = 0; $i < count ( $list ['users'] ); $i ++) {
				for($j = $i; $j < count ( $list ['users'] ); $j ++) {
					if ($list ['users'] [$i] ['bad_words_no'] < $list ['users'] [$j] ['bad_words_no']) {
						$tmp = $list ['users'] [$i];
						$list ['users'] [$i] = $list ['users'] [$j];
						$list ['users'] [$j] = $tmp;
					}
				}
			}
			
			file_put_contents ( $dir . "black_list.json", json_encode ( $list ) );
			
			closedir ( $dh );
			// return $array;
		} else {
			echo "cannot open dir";
		}
	}
}
?>