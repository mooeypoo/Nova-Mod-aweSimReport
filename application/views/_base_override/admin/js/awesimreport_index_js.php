<?php $string = random_string('alnum', 8);?>

<script type="text/javascript" src="<?php echo base_url() . APPFOLDER;?>/assets/js/jquery.ui.datepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo base_url() . APPFOLDER;?>/assets/js/jquery.ui.datepicker.css" />

<script type="text/javascript">

$(document).ready(function(){
//	$('table.zebra tbody > tr:nth-child(odd)').addClass('alt');
	
	var dates = $( "#txtReportDateStart" ).datepicker({
			defaultDate: "+1w",
			changeMonth: true,
			numberOfMonths: 1,
			dateFormat: 'm/d/yy', 
			onSelect: function( selectedDate ) {
				var option = this.id == "txtReportDateStart" ? "minDate" : "maxDate",
					instance = $( this ).data( "datepicker" ),
					date = $.datepicker.parseDate(
						instance.settings.dateFormat ||
						$.datepicker._defaults.dateFormat,
						selectedDate, instance.settings );
				dates.not( this ).datepicker( "option", option, date );
			}
	});
	dates.closest('body').find('#ui-datepicker-div').wrap('<span class="UITheme"></span>');

		$('#frmGenerate').attr('target', '_blank'); //open the form in a new window
		$('#frmGenerate').attr('action','<?php echo site_url('aweajax/awe_count_output') ?>');
		$('#frmGenerate').get(0).setAttribute('action', '<?php echo site_url('aweajax/awe_count_output') ?>');
	
	
});

</script>
