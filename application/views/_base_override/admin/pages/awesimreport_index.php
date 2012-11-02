<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');?>

<div id='ajaxnotice' class='hidden'></div>

<?php echo text_output($header, 'h1', 'page-head');?>

<?php echo form_open('awesimreport/count'); ?>

	<input type="hidden" name="action" value="generate_count" />

	<div class="indent-left">
	<!--span class="UITheme" -->
		<kbd><?php echo form_label('Report Date:', 'txtReportDateStart');?><?php echo form_input($inputs['txtReportDateStart']); ?></kbd>
		<kbd>Report Duration: <?php echo form_dropdown('selReportDuration',$inputs['selReportDuration'],'7'); /*form_input($inputs['txtReportDuration']);*/ ?> Days</kbd>
		<kbd>Backwards Count: <?php echo form_dropdown('selBackwardsCount',$inputs['selBackwardsCount'],'5');?></kbd>
		<small>This will show a history of log count, per report duration.  Choosing 1 will include another count duration back, choosing 2 will show two back, etc.</small>
	</span>
	<!-- /div -->

	<hr />

	<?php if (isset($roster['manifest'])): ?>
		<?php foreach ($roster['manifest'] as $manif): ?>
				
				<?php if (isset($manif['depts'])): ?>
					<?php //if (isset ($manif['depts']['pos'])) { ?>
						<br /><h2><?php echo $manif['name'];?></h2>
						<table class="" cellpadding="3" border="0">
						
						<?php foreach ($manif['depts'] as $dept): ?>
							<?php if (isset($dept['pos'])): ?>
								<tr>
									<td colspan="5"><h3><?php echo $dept['name'];?></h3></td>
								</tr>
								<?php foreach ($dept['pos'] as $pos): ?>
								
									<?php if (isset($pos['chars'])): ?>
										<?php foreach ($pos['chars'] as $char): ?>
											<?php if ($char['crew_type'] == 'inactive'): ?>
												<?php $display = ' hidden'; ?>
											<?php else: ?>
												<?php $display = ''; ?>
											<?php endif; ?>
									
											<tr class="fontSmall <?php echo $char['crew_type'] . $display;?>">
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
													<?php if ($char['crew_type'] == 'inactive'): ?>
														<?php $display = ' hidden'; ?>
													<?php else: ?>
														<?php $display = ''; ?>
													<?php endif; ?>
											
													<tr class="fontSmall <?php echo $char['crew_type'] . $display;?>">
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
	
	
	
	
	
	
	
<?php echo form_close();?>
	
<hr />
<?php //print_r($roster); ?>

<hr />