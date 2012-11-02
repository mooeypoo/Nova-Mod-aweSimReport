<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once MODPATH.'core/controllers/nova_ajax.php';

class Aweajax extends Nova_ajax {
	public function __construct()
	{
		parent::__construct();
	}
	
	/**
	 * Put your own methods below this...
	 */
	function awe_count_output() {

		print "Log count: ";
		// load the models
		$this->load->model('awesimreport_model', 'awe');
		$this->load->model('characters_model', 'char');
		$this->load->model('positions_model', 'pos');
		$this->load->model('depts_model', 'dept');
		$this->load->model('personallogs_model', 'logs');
		$this->load->model('posts_model', 'posts');
		$this->load->model('ranks_model', 'ranks');
		$this->load->model('news_model', 'news');
		$this->load->model('users_model', 'user');
	
		// Get form data:
		$tReportStart = $this->input->post('txtReportDateStart', TRUE);
		$tReportDuration = $this->input->post('selReportDuration', TRUE);
		$tBackwardsCount = $this->input->post('selBackwardsCount', TRUE);
		$tCharChecks = $this->input->post('chkCount', TRUE);
		//print "<hr>".$tReportStart."<hr>";

		$tReportStart = strtotime($tReportStart); //convert to epoch
//		print "<hr>".$tReportStart."<hr>";
		$tReportEnd = ($tReportDuration * 24 * 60 * 60); //translate to sections
		$tReportEnd = $tReportStart + $tReportEnd;
		//get the rosters again:

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
					$roster['manifest'][$m->manifest_id]['name'] = $m->manifest_name;

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
							
/*							// set the dept name
							$roster['manifest'][$m->manifest_id]['depts'][$dept]['name'] = $depts->dept_name;
							$roster['manifest'][$m->manifest_id]['depts'][$dept]['type'] = $depts->dept_type;
*/							
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
											$characters = $this->awe->get_characters_for_position($pos->pos_id, array('rank' => 'asc'));
											
											if ($characters->num_rows() > 0) {
												// set the name of the sub dept
												$roster['manifest'][$m->manifest_id]['depts'][$dept]['sub'][$a]['name'] = $sub->dept_name;
												$roster['manifest'][$m->manifest_id]['depts'][$dept]['sub'][$a]['type'] = $sub->dept_type;
												// set the sub dept position data
												$roster['manifest'][$m->manifest_id]['depts'][$dept]['sub'][$a]['pos'][$b]['name'] = $pos->pos_name;
												$roster['manifest'][$m->manifest_id]['depts'][$dept]['sub'][$a]['pos'][$b]['pos_id'] = $pos->pos_id;
												$roster['manifest'][$m->manifest_id]['depts'][$dept]['sub'][$a]['pos'][$b]['open'] = $pos->pos_open;
												$roster['manifest'][$m->manifest_id]['depts'][$dept]['sub'][$a]['pos'][$b]['blank_img'] = $blank_img;
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
															'width' => '150',
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
															if (in_array($char->charid, $tCharChecks)) {
																// set the data for the characters in a position in a sub dept
																$roster['manifest'][$m->manifest_id]['depts'][$dept]['sub'][$a]['pos'][$b]['chars'][$c]['char_id'] = $char->charid;
																$roster['manifest'][$m->manifest_id]['depts'][$dept]['sub'][$a]['pos'][$b]['chars'][$c]['name'] = $name;
																$roster['manifest'][$m->manifest_id]['depts'][$dept]['sub'][$a]['pos'][$b]['chars'][$c]['rank_img'] = $rank_img;
																$roster['manifest'][$m->manifest_id]['depts'][$dept]['sub'][$a]['pos'][$b]['chars'][$c]['crew_type'] = $char->crew_type;
																// GET COUNT:
																if (($this->user->get_loa($char->user) == 'loa') || ($this->user->get_loa($char->user) == 'eloa')) {
																	$roster['manifest'][$m->manifest_id]['depts'][$dept]['sub'][$a]['pos'][$b]['chars'][$c]['logcount'] = $this->user->get_loa($char->user);
																} else {
																	$roster['manifest'][$m->manifest_id]['depts'][$dept]['sub'][$a]['pos'][$b]['chars'][$c]['logcount'] = $this->awe->count_user_posts($char->user, $tReportStart, $tReportDuration, $tBackwardsCount);
																}
															}
															if (!isset($logDates)) {
																$logDates = $this->awe->count_user_posts($char->user, $tReportStart, $tReportDuration, $tBackwardsCount);
															}
															
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
								foreach ($positions->result() as $pos) {
									// get any characters in a position in the dept
									$characters = $this->awe->get_characters_for_position($pos->pos_id, array('rank' => 'asc'));
									
									if ($characters->num_rows() > 0) {
										// set the dept name
										$roster['manifest'][$m->manifest_id]['depts'][$dept]['name'] = $depts->dept_name;
										$roster['manifest'][$m->manifest_id]['depts'][$dept]['type'] = $depts->dept_type;
										
										// set the data for the dept positions
										$roster['manifest'][$m->manifest_id]['depts'][$dept]['pos'][$b]['name'] = $pos->pos_name;
										$roster['manifest'][$m->manifest_id]['depts'][$dept]['pos'][$b]['pos_id'] = $pos->pos_id;
										$roster['manifest'][$m->manifest_id]['depts'][$dept]['pos'][$b]['open'] = $pos->pos_open;
										$roster['manifest'][$m->manifest_id]['depts'][$dept]['pos'][$b]['blank_img'] = $blank_img;
										
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
													'width' => '150',
													'class' => 'image');
												
												// get the character name and rank
												$name = $this->char->get_character_name($char->charid, true);
												
												if ($char->crew_type == 'active' and empty($char->user)) {
													// don't do anything
												} else {
													if (in_array($char->charid, $tCharChecks)) {
														// set the data for characters in a position in the dept
														$roster['manifest'][$m->manifest_id]['depts'][$dept]['pos'][$b]['chars'][$c]['char_id'] = $char->charid;
														$roster['manifest'][$m->manifest_id]['depts'][$dept]['pos'][$b]['chars'][$c]['name'] = $name;
														$roster['manifest'][$m->manifest_id]['depts'][$dept]['pos'][$b]['chars'][$c]['rank_img'] = $rank_img;
														$roster['manifest'][$m->manifest_id]['depts'][$dept]['pos'][$b]['chars'][$c]['crew_type'] = $char->crew_type;
														if (($this->user->get_loa($char->user) == 'loa') || ($this->user->get_loa($char->user) == 'eloa')) {
															$roster['manifest'][$m->manifest_id]['depts'][$dept]['pos'][$b]['chars'][$c]['logcount'] = $this->user->get_loa($char->user);
														} else {
															$roster['manifest'][$m->manifest_id]['depts'][$dept]['pos'][$b]['chars'][$c]['logcount'] = $this->awe->count_user_posts($char->user, $tReportStart, $tReportDuration, $tBackwardsCount);
															
														}
													}
													if (!isset($logDates)) {
														$logDates = $this->awe->count_user_posts($char->user, $tReportStart, $tReportDuration, $tBackwardsCount);
													}
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
	//END ROSTER!

	/** LOGCOUNT VIEW! **/


	/** END LOGCOUNT VIEW **/
?>
<style type="text/css">
body, table {
	font-family: verdana;
	font-size: 11px;
	margin: 15px;
}
table.roster {
	border-width: 1px;
	border-spacing: 0px;
	border-style: solid;
	border-color: gray;
	border-collapse: separate;
	background-color: white;
}
table.roster th {
	border-width: 1px;
	padding: 5px;
	border-style: solid;
	border-color: gray;
	-moz-border-radius: ;
}
table.roster td {
	border-width: 1px;
	padding: 5px;
	border-style: solid;
	border-color: gray;
	-moz-border-radius: ;
}

td.manifest {
	margin:0px;
	padding:0px 2px;
	font-size: 16px;
	font-weight: bold;
	text-align: center;
	background-color: #cfcfcf;
}
td.dept {
	margin:0px;
	padding:0px 2px;
	font-size: 14px;
	font-weight: bold;
	text-align: center;
}
td.subdept {
	margin:0px;
	padding:0px 2px;
	font-size: 12px;
	font-weight: bold;
	text-align: center;
}

td.lowlimit {
	color: #E01B1B;
}
</style>

	<?php if (isset($roster['manifest'])): ?>
		<table class="roster">
		<tr>
			<?php /*<th>rank</th>*/ ?>
			<th colspan=2>Character Name</th>
			<th colspan="<?php echo ($tBackwardsCount+1); ?>">Log Count</th>
		</tr>
		<tr>
			<th colspan=2></th>
	<?php foreach ($logDates as $key => $val) { ?>
			<th><?php echo date("M d",$key) ?></th>
	<?php } ?>
		</tr>
		<?php foreach ($roster['manifest'] as $manif): ?>
				
				<?php if (isset($manif['depts'])): ?>
					<?php //if (isset ($manif['depts']['pos'])) { ?>
						<tr>
							<td colspan="50" class="manifest"><?php echo $manif['name'];?></td>
						</tr>
						
						<?php foreach ($manif['depts'] as $dept): ?>
							<?php if (isset($dept['pos']) && count($dept['pos'])>0): ?>
								<tr>
									<td colspan="10" class="dept"><?php echo $dept['name'];?></td>
								</tr>
								<?php foreach ($dept['pos'] as $pos): ?>
								
									<?php if (isset($pos['chars'])): ?>
										<?php foreach ($pos['chars'] as $char): ?>
											<tr class="fontSmall">
<?php /*										<td class="col_15"></td> */ ?>
												<?php /*<td class="col_150"><?php echo img($char['rank_img']);?></td>*/ ?>
												<td colspan=2>
													<strong class="fontMedium"><?php echo $char['name'];?></strong><br />
													<?php echo $pos['name'];?>
												</td>
<?php 											foreach ($char['logcount'] as $key => $lc) { 
													$countstyle ='';
													if ($lc == 0) { $countstyle = 'lowlimit'; }
?>
													<td class="col_75 align_right <?php echo $countstyle;?>">
														<?php echo $lc; ?>
													</td>
<?php											} ?>
											</tr>
										<?php endforeach; ?>
									<?php endif; ?>
								
								
								<?php endforeach; ?>
							<?php endif; ?>

							<?php if (isset($dept['sub'])): ?>
								<?php foreach ($dept['sub'] as $sub): ?>
									<tr>
										<td class="col_15"></td>
										<td colspan="10" class="subdept"><?php echo $sub['name'];?></td>
									</tr>
								
									<?php if (isset($sub['pos'])): ?>
										<?php foreach ($sub['pos'] as $spos): ?>
										
											<?php if (isset($spos['chars'])): ?>
												<?php foreach ($spos['chars'] as $char): ?>
													<tr class="fontSmall">
													<td class="col_15"></td>
<?php /*														<td class="col_150"><?php echo img($char['rank_img']);?></td>*/ ?>
														<td>
															<strong class="fontMedium"><?php echo $char['name'];?></strong><br />
															<?php echo $spos['name'];?>
														</td>
		<?php 											foreach ($char['logcount'] as $key => $lc) { 
															$countstyle ='';
															if ($lc == 0) { $countstyle = 'lowlimit'; }
		?>
															<td class="col_75 align_right <?php echo $countstyle; ?>">
																<?php echo $lc; ?>
															</td>
		<?php											} ?>
													</tr>
												<?php endforeach; ?>
											<?php endif; ?>
										
									
										<?php endforeach; ?>
									<?php endif; ?>
								
								<?php endforeach; ?>
							<?php endif; ?>
						
						<?php endforeach; ?>
					
					<?php //} ?>
				<?php endif; ?>

				
		<?php endforeach; //manifests ?>
		</table>
		<?php endif; //manifests ?>
	
	
	<hr><hr>
<?php		
		//print_r($roster);
	} // end awe_count_output();
}
