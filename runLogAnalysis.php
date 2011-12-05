<?php 
if(!empty($argv[1])){
	$filename = $argv[1];
	main($filename);
}else{
	die("usage: ".__FILE__." <full_path_to_log_file>\n");
}

/**
 * 
 * Main Entry Point
 * @param string $filename
 */
function main($filename){
	$analytics = new LogAnalytics($filename);
	$csv_report = $analytics->getBrowserStats();
	
	print_r($csv_report);
}

/**
 * 
 * LogAnalytics Class
 * @author Adam
 *
 */
class LogAnalytics {
	var $browserStats = array();
	
	/**
	 * 
	 * construct initiates stats extraction
	 * @param string $filename
	 */
	function __construct($filename){
		if(file_exists($filename)){
			$this->extractBrowserStats($filename);
		}else{
			die("File Does Not Exist [{$filename}]");
		}
	}

	/**
	 * 
	 * Extracts Browser Stats from log file looping over each line
	 * @param string $filename
	 */
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

	/**
	 * 
	 * RegEx Match extract browser info from log line and increment stats count
	 * @param string $line
	 */
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
	
	/**
	 * 
	 * returns a CSV formatted report given an array of counts keyed by browser  name
	 * @return array
	 */
	function getBrowserStats(){
		$csv_report = "browser,count\n";
		
		if($this->browserStats){
			foreach($this->browserStats as $browser=>$count){
				$csv_report .= "{$browser},{$count}\n";
			}
		}
		
		return $csv_report;
	}
}
?>