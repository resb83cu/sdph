		<script language="javascript">
			 /*aqui cojo los campos que vienen por el arreglo al llamado de esta vista por el controlador, luego estas variablaes en el js se ponen para pasar por el url del stored que llama al url de setdatagridconditional con sus paramertos corespondientes*/
		
		    var reservation_number= '<?php echo $reservation_number; ?>';
			var reservation_begindate= '<?php echo $reservation_begindate; ?>';
			
			var reservation_enddate= '<?php echo $reservation_enddate; ?>';

			var person_id= '<?php echo $person_id; ?>';
			var hotel_id= '<?php echo $hotel_id; ?>';
			/*var transport_itinerary= '<?php echo $transport_itinerary; ?>';*/
		
		
		</script>
<script type="text/javascript" src="<?php echo $this->config->item('base_app_url'); ?>views/js/lodging_reservations_report.js"></script>
<div id="requests_grid"></div>