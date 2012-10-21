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

	//GET USER POSTS
	public function count_user_posts($user_id, $start_date, $end_date) {
		$this->db->from('posts');

		$string = "(post_authors_users LIKE '%,$user_id' OR post_authors_users LIKE '$user_id,%' OR post_authors_users = '%,$user_id,%' OR post_authors_users = $user_id)";
		$this->db->where("$string", null);
		if ($start_date > 0) {
			$this->db->where('post_date >=', $start_date);
		}
		if ($end_date > 0) {
			$this->db->or_where('post_date <=', $end_date);
		}
		$query = $this->db->get();
		
		$postnum = $query->num_rows();
		
		return $postnum;
		
	}
}