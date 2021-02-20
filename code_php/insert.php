<?php
/*
 * INSERT DATA (to MySQL) - Arduino PM5350
 * ***********************************************************************************
 * Code by : fahroni|ganteng
 * contact me : fahroniganteng@gmail.com
 * Date : feb 2021
 * License :  MIT
 * 
 */
 
include_once "function.php";

class insertData extends ronn{
	function  __construct(){
		// check basic auth
		if (!isset($_SERVER['PHP_AUTH_USER'])) {
			header('WWW-Authenticate: Basic realm="My Realm"');
			header('HTTP/1.0 401 Unauthorized');
			$this->retErr('have no access...');
		}
		// check user & password
		else if($_SERVER['PHP_AUTH_USER']!='fahroni' || $_SERVER['PHP_AUTH_PW'] != 'ganteng'){
			$this->retErr('not registered...');
		}
		// start db connection, sorry function name is verification ==> for check request on process.php ^___^
		else if($this->verification('saveData')){
			$this->saveData();
		}
	}
	private function saveData() {
		//check required http request (POST)
		$this->checkRequiredInput(['id','dt']);
		
		// validate data
		if($this->isJson($this->dt['dt'])){
			$arrDt = json_decode($this->dt['dt'], true);
			$query = "CALL `insert_rec`(SYSDATE(),'{$this->dt['id']}'," . implode(',',$arrDt) . ")";
			$this->db_query($query);
			$this->retData('success');
		}
		else $this->retErr('not registered...');
	}
	private function isJson($string) {
		json_decode($string);
		return (json_last_error() == JSON_ERROR_NONE);
	}
}
new insertData();

?>