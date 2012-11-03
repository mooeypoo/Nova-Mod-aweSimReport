<?php
/*
|---------------------------------------------------------------
| aweSimReport Log-Count GUI
|---------------------------------------------------------------
|
| File: views/_base_override/admin/pages/awesimreport_index.php
| System Version: Nova 2.0
| Author: Moriel Schottlender, 2012
|
*/
?>
<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');?>
<style>
.npc {
	background-color: #222;
}
</style>
<div id='ajaxnotice' class='hidden'></div>

<?php echo text_output($header, 'h1', 'page-head');?>

<?php echo form_open('aweajax/awe_count_output', $inputs['formAttributes']); ?>

	<input type="hidden" name="action" value="generate_count" />

	<div class="indent-left">
	<!--span class="UITheme" -->
		<kbd><?php echo form_label('Report Date:', 'txtReportDateStart');?><?php echo form_input($inputs['txtReportDateStart']); ?></kbd>
		<kbd>Report Duration: <?php echo form_dropdown('selReportDuration',$inputs['selReportDuration'],'7'); /*form_input($inputs['txtReportDuration']);*/ ?></kbd>
		<kbd>Backwards Count: <?php echo form_dropdown('selBackwardsCount',$inputs['selBackwardsCount'],'2');?></kbd>
		<small>This will show a history of log count, per report duration.  Choosing 1 will include another count duration back, choosing 2 will show two back, etc.</small>
		<kbd>Separate NPC Log Count: <?php echo form_dropdown('selSepNPCs',$inputs['selSepNPCs'],'n', "id='selSepNPCs'");?></kbd>

	</span>
	<!-- /div -->

	<hr />
	<small>To remove someone from the roster, uncheck their character.</small>

	<?php if (isset($roster['manifest'])): ?>
		<?php foreach ($roster['manifest'] as $manif): ?>
				
				<?php if (isset($manif['depts'])): ?>
					<?php //if (isset ($manif['depts']['pos'])) { ?>
						<br /><h2><?php echo $manif['name'];?></h2>
						<table class="" cellpadding="3" border="0">
						
						<?php foreach ($manif['depts'] as $dept): ?>
							<?php if (isset($dept['pos']) && count($dept['pos'])>0): ?>
								<tr>
									<td colspan="5"><h3><?php echo $dept['name'];?></h3></td>
								</tr>
								<?php foreach ($dept['pos'] as $pos): ?>
								
									<?php if (isset($pos['chars'])): ?>
										<?php foreach ($pos['chars'] as $char): ?>
									
											<tr class="fontSmall <?php echo $char['crew_type']?>">
												<td class="col_15"></td>
												<td class="col_150"><?php echo img($char['rank_img']);?></td>
												<td>
													<strong class="fontMedium"><?php echo $char['name'];?></strong><br />
													<?php echo $pos['name'];?>
													
													<?php if ($char['crew_type'] == 'npc'): ?>
														<br /><?php echo text_output($label['npc'], 'span', 'gray');?>
													<?php elseif ($char['crew_type'] == 'inactive'): ?>
														<br /><?php echo text_output($label['inactive'], 'span', 'gray');?>
													<?php endif; ?>
<?php 												if ($char['crew_type'] == 'npc') { ?>
														<span class="npc">(Played by <?php echo $char['main_char'] ?>)</span>
<?php												} ?>
												</td>
												<td></td>
												<td class="col_75 align_right">
													<?php echo form_checkbox($char['inpcheck']); ?>
												</td>
											</tr>
										<?php endforeach; ?>
									<?php endif; ?>
								
								
								<?php endforeach; ?>
							<?php endif; ?>

							<?php if (isset($dept['sub'])): ?>
								<?php foreach ($dept['sub'] as $sub): ?>
									<tr>
										<td class="col_15"></td>
										<td colspan="4"><h4><?php echo $sub['name'];?></h4></td>
									</tr>
								
									<?php if (isset($sub['pos'])): ?>
										<?php foreach ($sub['pos'] as $spos): ?>
										
											<?php if (isset($spos['chars'])): ?>
												<?php foreach ($spos['chars'] as $char): ?>
													<tr class="fontSmall <?php echo $char['crew_type']?>">
														<td class="col_15"></td>
														<td class="col_150"><?php echo img($char['rank_img']);?></td>
														<td>
															<strong class="fontMedium"><?php echo $char['name'];?></strong><br />
															<?php echo $spos['name'];?>
															
															<?php if ($char['crew_type'] == 'npc'): ?>
																<br /><?php echo text_output($label['npc'], 'span', 'gray');?>
															<?php elseif ($char['crew_type'] == 'inactive'): ?>
																<br /><?php echo text_output($label['inactive'], 'span', 'gray');?>
															<?php endif; ?>
<?php 												if ($char['crew_type'] == 'npc') { ?>
														<span class="npc">(Played by <?php echo $char['main_char'] ?>)</span>
<?php												} ?>
														</td>
														<td></td>
														<td class="col_75 align_right">
															<?php echo form_checkbox($char['inpcheck']); ?>
															<?php //echo anchor('personnel/character/'. $char['char_id'], img($char['combadge']), array('class' => 'bold image'));?>
														</td>
													</tr>
												<?php endforeach; ?>
											<?php endif; ?>
										
									
										<?php endforeach; ?>
									<?php endif; ?>
								
								<?php endforeach; ?>
							<?php endif; ?>
						
						<?php endforeach; ?>
					
						</table>
					<?php //} ?>
				<?php endif; ?>

				
		<?php endforeach; //manifests ?>
		<?php endif; //manifests ?>
	
	<br />
		<center><?php echo form_button($inputs['butGenerate']);?></center>
	
	
	
	
	
<?php echo form_close();?>
	
<?php //print_r($roster); ?>

