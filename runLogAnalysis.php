<?php 
if(!empty($argv[1])){
	$filename = $argv[1];
	main($filename);
}else{
	die("usage: ".__FILE__." <full_path_to_log_file>\n");
}

function main($filename){
	$analytics = new LogAnalytics($filename);
	$stats = $analytics->getBrowserStats();
	$csv_report = $analytics->arrayToCSV($stats);
	
	print_r($csv_report);
}

class LogAnalytics {
	var $browserStats = array();
	
	function __construct($filename){
		if(file_exists($filename)){
			$this->extractBrowserStats($filename);
		}else{
			die("File Does Not Exist [{$filename}]");
		}
	}
	
	function extractBrowserStats($filename){
		if($file_handle = fopen($filename, "r")){
			while (!feof($file_handle)) {
				$line = fgets($file_handle);
				$this->extractBrowserFromLine($line);
			}
			fclose($file_handle);
		}else{
			die("Could Not Open File [{$filename}]");
		}
	}

	function extractBrowserFromLine($line){
		// referrer:  Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1)
		$pattern = '/referrer:\s\S*\s(\S*)(.*)$/';
		preg_match($pattern, $line, $matches);
		
		if(!empty($matches[1])){
			if(empty($this->browserStats[$matches[1]])){
				$this->browserStats[$matches[1]] = 1;
			}else{
				$this->browserStats[$matches[1]]++;
			}
		}
	}
	
	function getBrowserStats(){
		return $this->browserStats;
	}
	
	function arrayToCSV($data_array){
		$csv_report = "browser,count\n";
		
		if($data_array){
			foreach($data_array as $browser=>$count){
				$csv_report .= "{$browser},{$count}\n";
			}
		}
		
		return $csv_report;
	}
}
?>