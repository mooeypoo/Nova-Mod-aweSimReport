<?php $string = random_string('alnum', 8);?>

<script type="text/javascript" src="<?php echo base_url() . APPFOLDER;?>/assets/js/jquery.ui.datepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo base_url() . APPFOLDER;?>/assets/js/jquery.ui.datepicker.css" />

<script type="text/javascript">

$(document).ready(function(){
//	$('table.zebra tbody > tr:nth-child(odd)').addClass('alt');
	
	var dates = $( "#txtReportDateStart, #txtReportDateEnd" ).datepicker({
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

});

</script>
