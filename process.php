<?php
/*
 * EXAMPLE DASHBOARD - Arduino PM5350
 * ***********************************************************************************
 * Code by : fahroni|ganteng
 * contact me : fahroniganteng@gmail.com
 * Date : feb 2021
 * License :  MIT
 * 
 */
 
 
include_once "function.php";
class process extends ronn{
	public function __construct($request){
		if($this->verification($request)){
			$request = str_replace(' ', '', $request);// remove all space on request
			$this->req 	= $request;
			$this->$request();
		}
    }
	private function generateAuth(){
		$this->checkRequiredInput(['User','Password']);
		$this->data = '
			<div class="mt-2 bg-secondary p-2">
				<code class="text-light" style="font-size:14px">
				User : '.$this->dt['User'].'<br>
				Pass : '.$this->dt['Password'].'<br><br>
				CODE : <br>
				Authorization: Basic '.base64_encode("{$this->dt['User']}:{$this->dt['Password']}").'
				</code>
			</div>
		';
		$this->retData();
	}
	private function getPage(){
		$this->checkRequiredInput(['pageRequest']);
		$pageReq = $this->dt['pageRequest'];
		switch ($pageReq){
			case 'Real Time':
				$this->data .= '<h5 id="idForCheckRefresh"><span>Real Time Recording</span><small class="text-secondary"> auto refresh every 5 second</small></h5>';
				$this->makeList('view_real_time');
				break;
			case 'Record avg 15m':
				$this->data .= '<h5><span>Average every 15 minutes</span><small class="text-secondary"> only display 100 record max --> lazy create paging page... ^___^</small></h5>';
				$this->makeTable('view_15m');
				break;
			case 'Basic Auth Generator':
				$this->data .= '
				<form onsubmit="ronn.generateAuth(this);return false;">
				  <div class="form-group row">
					<label for="User" class="col-sm-2 col-form-label">User</label>
					<div class="col-sm-10">
						<input type="text" class="form-control" id="User" placeholder="Enter user">
					</div>
				  </div>
				  <div class="form-group row">
					<label for="Password" class="col-sm-2 col-form-label">Password</label>
					<div class="col-sm-10">
						<input type="text" class="form-control" id="Password" placeholder="Secret Password">
					</div>
				  </div>
				  <div class="text-secondary">
					This is tool for make http basic authentication in arduino code (security issue).
				  </div>
				  <div id="authCode"></div>
				  <button type="submit" class="btn btn-primary mt-4">GENERATE</button>
				</form>
				';
				break;
			case 'About this App':
				$this->data = '
					<div class="card text-white bg-dark">
					  <div class="card-header">About</div>
					  <div class="card-body">
						<h5 class="card-title">PM5350 Recording</h5>
						<p class="card-text">
							This is just a sample application for displaying PM5350 recordings.<br>
							You need arduino to get data from PM5350 with modbus RTU protocol.<br>
							For detail, check out my repo on github or my youtube channel.
						</p>
						<ul class="list-group list-group-flush">
							<li class="list-group-item text-white bg-dark py-1">Code by : fahroni|ganteng</li>
							<li class="list-group-item text-white bg-dark py-1">Contact me : fahroniganteng@gmail.com</li>
							<li class="list-group-item text-white bg-dark py-1">Date : Feb 2021</li>
							<li class="list-group-item text-white bg-dark py-1">License : MIT</li>
						</ul>
						<a href="https://www.youtube.com/channel/UCC5ulau9sAq7zcJ9rsAXWYQ" class="btn btn-danger float-right mt-5 mx-1" target="_blank">YouTube Channel</a>
						<a href="https://github.com/fahroniganteng" class="btn btn-primary float-right mt-5 mx-1" target="_blank">Fork me on GitHub</a>
					  </div>
					</div>
				';
				break;
			default :
				$this->data .= 'Yeah, page not found...';
				break;
		}
		$this->retData();
	}
}

$request=isset($_POST['id'])?$_POST['id']:"UNKNOWN_REQUEST_________________________________________";
new process($request);
?>