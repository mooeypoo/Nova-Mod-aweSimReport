<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * SEO controller
 *
 * @package		Nova
 * @category	Controller
 * @author		Moriel Schottlender
 * @copyright	2012 Moriel Schottlender GNU
 */

require_once MODPATH.'core/controllers/nova_admin.php';

class Awesimreport extends Nova_admin {

	public function __construct()
	{
		parent::__construct();
	}

	public function index($action = false) {
		
		$data['header'] = 'aweSimReport LITE';

		// load the models
		$this->load->model('depts_model', 'dept');
		$this->load->model('ranks_model', 'ranks');
		$this->load->model('positions_model', 'pos');
		$this->load->model('personallogs_model', 'logs');
		$this->load->model('posts_model', 'posts');
		$this->load->model('awesimreport_model', 'awe');
		
		//Get all manifests:
		$manifests = $this->dept->get_all_manifests();
		
		if ($manifests->num_rows() > 0) {
			if ($manifests->num_rows() > 1) {
				foreach ($manifests->result() as $m) {
					$data['manifests'][$m->manifest_id] = array(
						'id' => $m->manifest_id,
						'name' => $m->manifest_name,
						'desc' => $m->manifest_desc,
					);
					$manifest = $m->manifest_id;
					$data['roster']['manifest'][$m->manifest_id]['name'] = $m->manifest_name;

					// get the manifest details (MD)
					$MD = $this->dept->get_manifest($manifest);

					// build the blank image array (ranks)
					$blank_img = array(
						'src' => Location::rank($this->rank, 'blank', $rank->rankcat_extension),
						'alt' => '',
						'class' => 'image');

					// run the methods
					$this->db->where('dept_manifest', $manifest);
					$depts = $this->dept->get_all_depts();
					$rank = $this->ranks->get_rankcat($this->rank);

					if ($depts->num_rows() > 0) {
						$a = 1;
						foreach ($depts->result() as $depts) {
							// set the dept id as a variable
							$dept = $depts->dept_id;
							
							// set the dept name
							$data['roster']['manifest'][$m->manifest_id]['depts'][$dept]['name'] = $depts->dept_name;
							$data['roster']['manifest'][$m->manifest_id]['depts'][$dept]['type'] = $depts->dept_type;
							
							// get the sub depts
							$subdepts = $this->dept->get_sub_depts($dept);
							if ($subdepts->num_rows() > 0) {
								$a = 1;
								foreach ($subdepts->result() as $sub) {
									// set the name of the sub dept
									$data['roster']['manifest'][$m->manifest_id]['depts'][$dept]['sub'][$a]['name'] = $sub->dept_name;
									$data['roster']['manifest'][$m->manifest_id]['depts'][$dept]['sub'][$a]['type'] = $sub->dept_type;
									
									// grab the positions for the sub dept
									$positions = $this->pos->get_dept_positions($sub->dept_id);
							
									if ($positions->num_rows() > 0) {
										$b = 1;
										foreach ($positions->result() as $pos) {
											// set the sub dept position data
											$data['roster']['manifest'][$m->manifest_id]['depts'][$dept]['sub'][$a]['pos'][$b]['name'] = $pos->pos_name;
											$data['roster']['manifest'][$m->manifest_id]['depts'][$dept]['sub'][$a]['pos'][$b]['pos_id'] = $pos->pos_id;
											$data['roster']['manifest'][$m->manifest_id]['depts'][$dept]['sub'][$a]['pos'][$b]['open'] = $pos->pos_open;
											$data['roster']['manifest'][$m->manifest_id]['depts'][$dept]['sub'][$a]['pos'][$b]['blank_img'] = $blank_img;
											
											// get any characters in a position in a sub dept
											$characters = $this->char->get_characters_for_position($pos->pos_id, array('rank' => 'asc'));
											if ($characters->num_rows() > 0) {
												$c = 1;
												foreach ($characters->result() as $char) {
													//skip NPCs
													if ($char->crew_type != 'npc') {
														// grab the rank data we need
														$rankdata = $this->ranks->get_rank($char->rank, array('rank_name', 'rank_image'));
														
														// build the rank image array
														$rank_img = array(
															'src' => Location::rank(
																$this->rank, 
																$rankdata['rank_image'],
																$rank->rankcat_extension),
															'alt' => $rankdata['rank_name'],
															'class' => 'image');
															
														// set the color
														$color = '';
														
														if ($char->user > 0) {
															$color = ($this->user->get_loa($char->user) == 'loa') ? '_loa' : $color;
															$color = ($this->user->get_loa($char->user) == 'eloa') ? '_eloa' : $color;
														}
														
														// get the character name and rank
														$name = $this->char->get_character_name($char->charid, true);
														
														if ($char->crew_type == 'active' and empty($char->user)) {
															// don't do anything
														} else {
															//GET LOG COUNT:
															$postlist = $awe->get_user_posts($char->user);
															// set the data for the characters in a position in a sub dept
															$data['roster']['manifest'][$m->manifest_id]['depts'][$dept]['sub'][$a]['pos'][$b]['chars'][$c]['char_id'] = $char->charid;
															$data['roster']['manifest'][$m->manifest_id]['depts'][$dept]['sub'][$a]['pos'][$b]['chars'][$c]['name'] = $name;
															$data['roster']['manifest'][$m->manifest_id]['depts'][$dept]['sub'][$a]['pos'][$b]['chars'][$c]['rank_img'] = $rank_img;
															$data['roster']['manifest'][$m->manifest_id]['depts'][$dept]['sub'][$a]['pos'][$b]['chars'][$c]['crew_type'] = $char->crew_type;
			//												$data['depts'][$dept]['sub'][$a]['pos'][$b]['chars'][$c]['combadge'] = $cb_img;
															
															++$c;
														}
													} // end if not npc
												}
											}
											
											++$b;
										}
									}
									
									++$a;
								} //foreach subepts 
							} //subdepts >0

							// get the positions for the dept
							$positions = $this->pos->get_dept_positions($dept);
							
							if ($positions->num_rows() > 0)
							{
								$b = 1;
								foreach ($positions->result() as $pos)
								{
									// set the data for the dept positions
									$data['roster']['manifest'][$m->manifest_id]['depts'][$dept]['pos'][$b]['name'] = $pos->pos_name;
									$data['roster']['manifest'][$m->manifest_id]['depts'][$dept]['pos'][$b]['pos_id'] = $pos->pos_id;
									$data['roster']['manifest'][$m->manifest_id]['depts'][$dept]['pos'][$b]['open'] = $pos->pos_open;
									$data['roster']['manifest'][$m->manifest_id]['depts'][$dept]['pos'][$b]['blank_img'] = $blank_img;
									
									// get any characters in a position in the dept
									$characters = $this->char->get_characters_for_position($pos->pos_id, array('rank' => 'asc'));
									
									if ($characters->num_rows() > 0) {
										$c = 1;
										foreach ($characters->result() as $char) {
											//check char is not NPC
											if ($char->crew_type != 'npc') {
												// get the rank data we need
												$ranksdata = $this->ranks->get_rank($char->rank, array('rank_name', 'rank_image'));
												
												// build the rank image array
												$rank_img = array(
													'src' => Location::rank(
														$this->rank,
														$ranksdata['rank_image'],
														$rank->rankcat_extension),
													'alt' => $ranksdata['rank_name'],
													'class' => 'image');
												
												// set the color
			/*									$color = '';
												
												if ($char->user > 0)
												{
													$color = ($this->user->get_loa($char->user) == 'loa') ? '_loa' : $color;
													$color = ($this->user->get_loa($char->user) == 'eloa') ? '_eloa' : $color;
												}
												
												$color = ($char->crew_type == 'inactive') ? '' : $color;
												$color = ($char->crew_type == 'npc') ? '_npc' : $color;
												
												// build the combadge image array
												$cb_img = array(
													'src' => Location::cb('combadge'. $color .'.png', $this->skin, 'main'),
													'alt' => ucwords(lang('actions_view') 
														.' '. lang('labels_bio')),
													'class' => 'image'
												);
			*/									
												// get the character name and rank
												$name = $this->char->get_character_name($char->charid, true);
												
												if ($char->crew_type == 'active' and empty($char->user)) {
													// don't do anything
												} else {
													// set the data for characters in a position in the dept
													$data['roster']['manifest'][$m->manifest_id]['depts'][$dept]['pos'][$b]['chars'][$c]['char_id'] = $char->charid;
													$data['roster']['manifest'][$m->manifest_id]['depts'][$dept]['pos'][$b]['chars'][$c]['name'] = $name;
													$data['roster']['manifest'][$m->manifest_id]['depts'][$dept]['pos'][$b]['chars'][$c]['rank_img'] = $rank_img;
													$data['roster']['manifest'][$m->manifest_id]['depts'][$dept]['pos'][$b]['chars'][$c]['crew_type'] = $char->crew_type;
			//										$data['roster']['manifest'][$m->manifest_id]['depts'][$dept]['pos'][$b]['chars'][$c]['combadge'] = $cb_img;
													
													++$c;
												}
											} // end if not npc
										}
									}
									
									++$b;
								}
							}							
							
							

							
							
							
						
						} //foreach depts
					} //if depts > 0
			
					
					
					
				} //foreach manifest
			} //if manifest >1
			
		} //if manifest>0

		$this->_regions['content'] = Location::view('awesimreport_index', $this->skin, 'admin', $data);
		$this->_regions['javascript'] = Location::js('awesimreport_index_js', $this->skin, 'admin');
		$this->_regions['title'].= $data['header'];
		
		Template::assign($this->_regions);
		
		Template::render();
		

	}
	
}
