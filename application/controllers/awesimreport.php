<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
|---------------------------------------------------------------
| aweSimReport Controller
|---------------------------------------------------------------
|
| File: controllers/awesimreport.php
| System Version: Nova 2.0
| Author: Moriel Schottlender, 2012
|
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

/*
		$data['images']['loading'] = array(
			'src' => img_location('loading-bar.gif', $this->skin, 'admin'),
			'alt' => 'Loading',
			'class' => 'image'
		);
		*/
		$seloptions = array(
                  '1'  => '1 Day',
                  '2'  => '2 Days',
                  '3'  => '3 Days',
                  '4'  => '4 Days',
                  '5'  => '5 Days',
                  '6'  => '6 Days',
                  '7'  => '1 Week',
                  '8'  => '8 Days',
                  '9'  => '9 Days',
                  '10'  => '10 Days',
                  '14'  => '2 Weeks',
                  '30'  => '30 Days',
                );
		$seloptions2 = array(
                  '1'  => '1',
                  '2'  => '2',
                  '3'  => '3',
                  '4'  => '4',
                  '5'  => '5',
				);
		$seloptions3 = array(
                  'n'  => 'Combine NPC log count with main character',
                  'y'  => 'Show NPCs separately',
				);
		
		$data['inputs'] = array(
			'formAttributes' => array(
				'name' => 'frmGenerate',
				'id' => 'frmGenerate',
				'target' => "_blank"),
			'txtReportDateStart' => array(
				'style' => 'width:150px;',
				'name' => 'txtReportDateStart',
				'id' => 'txtReportDateStart',
				'value' => date("M j, Y")),
			'txtReportDuration' => array(
				'style' => 'width:150px;',
				'name' => 'txtReportDuration',
//				'value' => $inputVal['txtReportDuration'],
				'id' => 'txtReportDuration'),
			'selReportDuration' => $seloptions,
			'selBackwardsCount' => $seloptions2,
			'selSepNPCs' => $seloptions3,
			'butGenerate' => array(
				'type' => 'submit',
				'class' => 'button-main',
				'name' => 'submit',
				'value' => 'generate',
				'id' => 'submitGenerate',
				'content' => ucwords('Generate Report')),
		);
		
		//Get all manifests:
		$manifests = $this->dept->get_all_manifests();
		
		if ($manifests->num_rows() > 0) {
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
							
							// get the sub depts
							$subdepts = $this->dept->get_sub_depts($dept);
							if ($subdepts->num_rows() > 0) {
								$a = 1;
								foreach ($subdepts->result() as $sub) {
									
									// grab the positions for the sub dept
									$positions = $this->pos->get_dept_positions($sub->dept_id);
							
									if ($positions->num_rows() > 0) {

										$b = 1;
										foreach ($positions->result() as $pos) {
											// get any characters in a position in a sub dept
//											$characters = $this->awe->get_characters_for_position($pos->pos_id, array('rank' => 'asc'));
											$characters = $this->char->get_characters_for_position($pos->pos_id, array('rank' => 'asc'));
											
											if ($characters->num_rows() > 0) {
												// set the name of the sub dept
												$data['roster']['manifest'][$m->manifest_id]['depts'][$dept]['sub'][$a]['name'] = $sub->dept_name;
												$data['roster']['manifest'][$m->manifest_id]['depts'][$dept]['sub'][$a]['type'] = $sub->dept_type;
												// set the sub dept position data
												$data['roster']['manifest'][$m->manifest_id]['depts'][$dept]['sub'][$a]['pos'][$b]['name'] = $pos->pos_name;
												$data['roster']['manifest'][$m->manifest_id]['depts'][$dept]['sub'][$a]['pos'][$b]['pos_id'] = $pos->pos_id;
												$data['roster']['manifest'][$m->manifest_id]['depts'][$dept]['sub'][$a]['pos'][$b]['open'] = $pos->pos_open;
												$data['roster']['manifest'][$m->manifest_id]['depts'][$dept]['sub'][$a]['pos'][$b]['blank_img'] = $blank_img;
												$c = 1;
												foreach ($characters->result() as $char) {
													//ignore position_2
													if ($char->position_2 != $pos->pos_id) {
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
															
														
														// get the character name and rank
														$name = $this->char->get_character_name($char->charid, true);
														
														if ($char->crew_type == 'active' and empty($char->user)) {
															// don't do anything
														} else {
															// set the data for the characters in a position in a sub dept
															$data['roster']['manifest'][$m->manifest_id]['depts'][$dept]['sub'][$a]['pos'][$b]['chars'][$c]['char_id'] = $char->charid;
															$data['roster']['manifest'][$m->manifest_id]['depts'][$dept]['sub'][$a]['pos'][$b]['chars'][$c]['name'] = $name;
															$data['roster']['manifest'][$m->manifest_id]['depts'][$dept]['sub'][$a]['pos'][$b]['chars'][$c]['rank_img'] = $rank_img;
															$data['roster']['manifest'][$m->manifest_id]['depts'][$dept]['sub'][$a]['pos'][$b]['chars'][$c]['crew_type'] = $char->crew_type;
															$data['roster']['manifest'][$m->manifest_id]['depts'][$dept]['sub'][$a]['pos'][$b]['chars'][$c]['inpcheck'] = array(
																																											'name' => 'chkCount['.$char->charid.']',
																																											'id' => 'chkCount['.$char->charid.']',
																																											'value' => $char->charid,
																																											'checked' => TRUE,
																																											);
															unset($maincharname);
															unset($tmainchar);
															unset($mainchar);
															if ($char->crew_type == 'npc') {
																$tmainchar = $this->char->get_user_characters($char->user,'active');
																$mainchar = $tmainchar->row();
																$maincharname = $this->char->get_character_name($mainchar->charid,true,false);
																$data['roster']['manifest'][$m->manifest_id]['depts'][$dept]['sub'][$a]['pos'][$b]['chars'][$c]['main_char'] = $maincharname;
															}
															++$c;
														}
													}//end ignore position_2
												}//foreach chars
											}//if chars numrows>0
											++$b;
										}//foreach positions
									} //if positions numrows >0
									++$a;
								} //foreach subepts 
							} //subdepts >0

							// get the positions for the dept
							$positions = $this->pos->get_dept_positions($dept);
							
							if ($positions->num_rows() > 0)
							{
								$b = 1;
								foreach ($positions->result() as $pos) {
									// get any characters in a position in the dept
//									$characters = $this->awe->get_characters_for_position($pos->pos_id, array('rank' => 'asc'));
									$characters = $this->char->get_characters_for_position($pos->pos_id, array('rank' => 'asc'));
									
									if ($characters->num_rows() > 0) {
										// set the dept name
										$data['roster']['manifest'][$m->manifest_id]['depts'][$dept]['name'] = $depts->dept_name;
										$data['roster']['manifest'][$m->manifest_id]['depts'][$dept]['type'] = $depts->dept_type;
										
										// set the data for the dept positions
										$data['roster']['manifest'][$m->manifest_id]['depts'][$dept]['pos'][$b]['name'] = $pos->pos_name;
										$data['roster']['manifest'][$m->manifest_id]['depts'][$dept]['pos'][$b]['pos_id'] = $pos->pos_id;
										$data['roster']['manifest'][$m->manifest_id]['depts'][$dept]['pos'][$b]['open'] = $pos->pos_open;
										$data['roster']['manifest'][$m->manifest_id]['depts'][$dept]['pos'][$b]['blank_img'] = $blank_img;
										
										$c = 1;
										foreach ($characters->result() as $char) {
											//ignore position_2
											if ($char->position_2 != $pos->pos_id) {
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
													$data['roster']['manifest'][$m->manifest_id]['depts'][$dept]['pos'][$b]['chars'][$c]['inpcheck'] = array(
																																						'name' => 'chkCount['.$char->charid.']',
																																						'id' => 'chkCount['.$char->charid.']',
																																						'value' => $char->charid,
																																						'checked' => TRUE,
																																						);
														unset($maincharname);
														unset($tmainchar);
														unset($mainchar);
															if ($char->crew_type == 'npc') {
																$tmainchar = $this->char->get_user_characters($char->user,'active');
																$mainchar = $tmainchar->row();
																$maincharname = $this->char->get_character_name($mainchar->charid,true,false);
																$data['roster']['manifest'][$m->manifest_id]['depts'][$dept]['pos'][$b]['chars'][$c]['main_char'] = $maincharname;
															}
													++$c;
												}
											} // end ignore position_2
										}
									}
									
									++$b;
								}
							}							
							
							

							
							
							
						
						} //foreach depts
					} //if depts > 0
								
				} //foreach manifest
			
		} //if manifest>0

		$this->_regions['content'] = Location::view('awesimreport_index', $this->skin, 'admin', $data);
		$this->_regions['javascript'] = Location::js('awesimreport_index_js', $this->skin, 'admin');
		$this->_regions['title'].= $data['header'];
		
		Template::assign($this->_regions);
		
		Template::render();
	}
	
}
