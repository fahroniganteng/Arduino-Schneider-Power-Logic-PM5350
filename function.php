<?php
/*
 * DATABASE CONNECTION
 * ***********************************************************************************
 * Code by : fahroni|ganteng
 * contact me : fahroniganteng@gmail.com
 * Date : feb 2021
 * License :  MIT
 * 
 */
const DB_HOST 	= 'localhost';  
const DB_USER 	= 'root';  
const DB_PASS 	= '';  
const DB_NAME	= 'meter';

class db extends mysqli {
	protected $res;
    function __construct() {
        mysqli_report(MYSQLI_REPORT_OFF); // turn of error reporting
        @parent::__construct(DB_HOST,DB_USER,DB_PASS,DB_NAME);
        if(mysqli_connect_errno())
            throw new exception(mysqli_connect_error(), mysqli_connect_errno());
    }
    function runQry($query) {
        if(!$this->real_query($query))
            throw new exception( $this->error, $this->errno );
        $this->res = new mysqli_result($this);
		return $this->res;
    }
	function getAll($resultType){
		$ret	= $this->res->fetch_all($resultType);
		if(!$ret)
			throw new exception($this->error, $this->errno);
		else
			return $ret;
	}
	function escStr($str){
		$ret	= $this->real_escape_string($str);
		if(!$ret)
			throw new exception($this->error, $this->errno);
		else
			return $ret;
	}
	function numRow(){
		$ret	= $this->res->num_rows;
		if($ret>=0)return $ret;
		else throw new exception($this->error, $this->errno);
	}
}


/* 
 * for test connection only
 * ***************************************************************
$db = new db;
try {
	$result = $db->query("select * from pengaturan");
	while( $row = $result->fetch_assoc() ){ 
		printf("%s (%s) <br>", $row['nama'], $row['deskripsi']); 
	} 
} catch (Exception $e) {
	echo "ERROR : " . $e->getMessage();
	exit;
}
*/

/* 
 * for debuging only
 * ***************************************************************
 */
function dd($dt){//dump data --> for debuging only......
	echo "<pre>".print_r($dt,1)."</pre>";
	exit;
}


/* 
 * simple class for handle http request
 * ***************************************************************
 */
class ronn{
	protected $data		= NULL;//feedback data
	function retData($html=false){ //return
		echo $html?$html:$this->data;
		exit;
	}
	function retErr($err){
		echo "ERROR!!!<br>".$err;
		exit;
	}
	function verification($id){
		if(method_exists($this,$id)){// request exist ?
			$this->id	= $id;
			$this->dt	= isset($_POST)?$_POST:"";// data
			$this->db_open();
			return true; //gak perlu callback
        }
        else{
			$this->retErr("Request not exist...");
		}
	}
	function checkRequiredInput($arr,$ret=false){//array data, return?
		$dt = true;
		$input = $this->dt;
		if(is_array($input)){
			foreach($arr as $v){
				if(!array_key_exists($v,$input)){ // required post data not found
					$dt = $v;
					break;
				}
				else if(strlen(trim($input[$v]))<1){ // post data length = 0
					$dt = $v;
					break;
				}
				else{
					$_trim = trim($input[$v]);
					$this->dt[$v]	= $_trim=='0'?0:$this->db_escStr($_trim);// zero error on mysql_real_escape_string
				}
			}
			
			if($ret)return $dt;
			else if($dt!==true) $this->retErr("Required data : ". $dt);
		}
		else $this->retErr("Not valid data...");
	}
	
	// 'try' style for handle mysql....
	function db_open(){
		try{
			$this->db	= new db();
		}catch (Exception $e) {
			$this->retErr("Cannot connect DB : ".$e->getMessage());
		}
	}
	function db_query($qry){
		try {
			$this->db->runQry($qry);
		} catch (Exception $e) {
			$this->retErr("Error Query : ".$e->getMessage());
		}
	}
	function db_getAll($resultType=MYSQLI_ASSOC){//Default is assoc, other ==> MYSQLI_ASSOC,MYSQLI_NUM,MYSQLI_BOTH
		try {
			return $this->db->getAll($resultType);
		} catch (Exception $e) {
			$this->retErr("Error get all record db : ".$e->getMessage());
		}
	}
	function db_escStr($str){
		try {
			return $this->db->escStr($str);
		} catch (Exception $e) {
			$this->retErr("ERROR ESC String : ".$e->getMessage());
		}
	}
	function db_count(){
		try {
			$count = $this->db->numRow();
		} catch (Exception $e) {
			$this->retErr("Error count db : ".$e->getMessage());
		}
		return $count>0?$count:0;
	}
	
	// Generate Table
	function makeTable($dbTableName){
		$dbName = DB_NAME;
		
		// Get table header
		$query	= "SELECT * FROM information_schema.columns WHERE table_name = '$dbTableName' AND table_schema = '{$dbName}'";
		$this->db_query($query);
		$this->data	.= '<table class="table table-sm table-striped">';
		$this->data	.= '<thead class="thead-dark"><tr>';
		$this->data	.= '<th>#</th>';
		$countColumn = 0;
		foreach($this->db_getAll() as $r){
			$headerName	 	= str_replace('_',' ',$r['COLUMN_NAME']);
			$this->data	.= "<th>{$headerName}</th>";
			$countColumn++;
		}
		$this->data	.= '</tr></thead>';
		
		// Get table body
		$this->data	.= '<tbody>';
		$query	= "SELECT * FROM `{$dbName}`.`{$dbTableName}`";
		// $this->retErr($query);
		$this->db_query($query);
		if($this->db_count()>0){
			$i = 1;
			$data = $this->db_getAll();
			foreach($data as $r) {  // ROW
				$this->data	.=	"<tr>";
				$this->data	.=	"<td>{$i}</td>";
				foreach($r as $val) // COLUMN
					$this->data	.=	"<td>{$val}</td>";
				$this->data	.=	"</tr>";
				$i++;
			}
		}
		else {
			$data		= false;
			$this->data	.=	"<tr>";
			$this->data	.=	"<td colspan='". ($countColumn + 1) ."' style='text-align:center;font-weight:bold'>no data...</td>";
			$this->data	.=	"</tr>";
		}
		$this->data	.= '</tbody>';
		$this->data	.= '</table>';
	}
	
	// Generate List
	function makeList($dbTableName){
		$dbName = DB_NAME;
		$query	= "SELECT * FROM `{$dbName}`.`{$dbTableName}`";
		// $this->retErr($query);
		$this->db_query($query);
		if($this->db_count()>0){
			$i = 1;
			$data = $this->db_getAll();
			foreach($data as $r) {  // ROW
				$this->data	.=	'<div class="card bg-info mb-2">';
				$this->data	.=	'<div class="card-header text-white">'.$i.'. '.$r['Device_ID'].'</div>';
				$this->data	.=	'<div class="card-body bg-light">';
				$this->data	.=	'<dl>';
				foreach($r as $key=>$val){ // COLUMN
					if($key!='Device_ID'){
						$title	 	= str_replace('_',' ',$key);
						$this->data	.=	'
							<div class="row border-bottom">
								<dt class="col-sm-4 my-0 pt-1">'.$title.'</dt>
								<dd class="col-sm-8 my-0 pt-1">'.$val.'</dd>
							</div>
						';
					}
				}
				$this->data	.=	"</dl></div></div>";
				$i++;
			}
		}
		else {
			$data		= false;
			$this->data	.=	"No data..";
		}
		$this->data	.= '</tbody>';
		$this->data	.= '</table>';
	}
}


?>