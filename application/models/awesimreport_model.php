<?php
/*
|---------------------------------------------------------------
| aweSimReport MODEL
|---------------------------------------------------------------
|
| File: models/awesimreport_model.php
| System Version: 2.0
| Author: Moriel Schottlender
|
*/

class Awesimreport_model extends CI_Model {

	public function __construct()
	{
		parent::__construct();
		$this->load->dbutil();
	}

}