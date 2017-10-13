<script type="text/javascript" src="<?php echo base_url (); ?>js/ext-3.0.0/examples/ux/RowExpander.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo base_url (); ?>js/ext-3.0.0/examples/grid/grid-examples.css" />
<script type="text/javascript" src="<?php echo $this->config->item('base_app_url'); ?>views/js/request_change_motive.js"></script>
<script language="javascript"> var session_personId ='<?php echo $this->session->userdata('person_id'); ?>'; </script>
<script language="javascript"> var session_rollId ='<?php echo $this->session->userdata('roll_id'); ?>'; </script>
<script language="javascript"> var session_centerId ='<?php echo $this->session->userdata('center_id'); ?>'; </script>
<br>
<div id="panel-basic" class="container"></div>
<div id="requests_grid"></div>