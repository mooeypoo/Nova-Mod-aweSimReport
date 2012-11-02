<?php
/*
|---------------------------------------------------------------
| aweSimReport MODEL
|---------------------------------------------------------------
|
| File: models/awesimreport_model.php
| System Version: 2.0
| Author: Moriel Schottlender, 2012
|
*/

class Awesimreport_model extends CI_Model {

	public function __construct()
	{
		parent::__construct();
		$this->load->dbutil();
	}

	//GET USER POSTS
	public function count_user_posts($user_id, $start_date, $duration, $iteration = 1) {

		$string = "(post_authors_users LIKE '%,$user_id' OR post_authors_users LIKE '$user_id,%' OR post_authors_users = '%,$user_id,%' OR post_authors_users = $user_id)";

		$duration = $duration * 24 * 60 * 60; //translate to epoch seconds
		$en_date = $start_date;
		$st_date = $start_date - $duration;
		for ($i=1; $i<=$iteration; $i++) {
			$querystr[] = "SELECT * FROM trlsta_posts WHERE ".$string;
			$querystr[] = "AND post_date > ".$st_date;
			$querystr[] = "AND post_date <= ".$en_date;

			$rawquery = implode(" ", $querystr);
			$query = $this->db->query($rawquery);

			$postnum[$en_date] = $query->num_rows();
/*			//if 0, check if user was on loa:
			if ($postnum[$en_date] == 0) {
				$this->db->select('loa');
				$this->db->from('users');
				$this->db->where('userid', $id);
				$loaquery = $this->db->get();
				
				if ($loaquery->num_rows() > 0)
				{
					$row = $loaquery->row();
					if ($row->loa) {
						$postnum[$en_date] = $row->loa;
					}
				}
			}
*/
			$en_date = $st_date;
			$st_date = $st_date - $duration;
			unset($querystr);
		}
		ksort($postnum);
		return $postnum;
		
	}
	
	//GET ALL *PLAYING* ACTIVE CHARS FOR POST:
	public function get_characters_for_position($position = '', $order = '')
	{
		$this->db->from('characters');
		$this->db->where('crew_type !=', 'pending');
		$this->db->where('crew_type !=', 'npc');
		$this->db->where('position_1', $position);
//		$this->db->or_where('position_2', $position);
		
		if ( ! empty($order))
		{
			if (is_array($order))
			{
				foreach ($order as $field => $order)
				{
					$this->db->order_by($field, $order);
				}
			}
		}
		
		$query = $this->db->get();
		
		return $query;
	}
	
	
}