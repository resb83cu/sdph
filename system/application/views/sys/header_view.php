<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html;charset=utf-8">
<title>Sistema de Dieta Pasaje y Hospedaje</title>
<?php

        $fecha=getdate();
		$mes=$fecha ['mon'] ;
		strlen($mes) ==1?$mes='0'.$mes:$mes=$mes;
		$dia=$fecha ['mday'];
		strlen($dia) ==1?$dia='0'.$dia:$dia=$dia;
		$hora=$fecha ['hours'];
		strlen($hora) ==1?$hora='0'.$hora:$hora=$hora;
		$minuto=$fecha ['minutes'];
		strlen($minuto) ==1?$minuto='0'.$minuto:$minuto=$minuto;
		$segundo=$fecha ['seconds'];
		strlen($segundo) ==1?$segundo='0'.$segundo:$segundo=$segundo;
        $enddate = $fecha['year'] . '-' . $mes. '-' .$dia . ' ' . $hora . ':' . $minuto . ':' . $segundo;

	if ($this->session->userdata('id')!=FALSE)//verificando que no ha expirado aun la sesion,si expiro no se hace nada

        $this->db->query ( 'update user_sessions  set session_id=\'' .session_id(). '\'      , session_enddate=\''.$enddate.'\'  where  id = '. $this->session->userdata('id').'   ');//

?>
<link rel="icon" href="<?php echo base_url (); ?>images/etecsaicon.ico" type="image/x-icon" />
<link rel="stylesheet" type="text/css" href="<?php echo base_url (); ?>js/ext-3.0.0/resources/css/ext-all.css" />
<link href="<?php echo base_url (); ?>css/basico.css" rel="stylesheet" type="text/css" />
<link href="<?php echo base_url (); ?>css/ejemplo.css" rel="stylesheet" type="text/css" />

<script type="text/javascript" src="<?php echo base_url (); ?>js/ext-3.0.0/adapter/ext/ext-base.js"></script>
<script type="text/javascript" src="<?php echo base_url (); ?>js/ext-3.0.0/ext-all.js"></script>
<script type="text/javascript" src="<?php echo base_url (); ?>js/ext-3.0.0/examples/shared/examples.js"></script>	
<script type="text/javascript" src="<?php echo base_url (); ?>js/ext-3.0.0/src/locale/ext-lang-es.js"></script>
<script language="javascript"> var baseUrl='<?php echo base_url (); ?>'; </script>
<script language="javascript"> var baseAppUrl ='<?php echo $this->config->item ( 'base_app_url' );?>'; </script>
<script language="javascript"> if(history.forward(1)){history.replace(history.forward(1));} </script>
<style type="text/css">

body {
 background-color: #fff;
 font-family: Lucida Grande, Verdana, Sans-serif;
 font-size: 12px;
}

a {
 color: #003399;
 background-color: transparent;
 font-weight: normal;
}

h1 {
 color: #444;
 background-color: transparent;
 border-bottom: 1px solid #D0D0D0;
 font-size: 16px;
 font-weight: bold;
}

</style>

</head>
<body>
<div id="cabecera">
<div id="cab_1"><a href="<?php echo base_url ();?>"> <img id="logo" src="<?php echo base_url ();?>images/logo.png" /> </a>
<h1>Sistema de dieta, pasaje y hospedaje</h1>
</div>
<div id="sys_menu" class="clearfix">
<div id="user">
	<?php $centinela = new Centinela ( );
	if ($centinela->_auth == FALSE) :?>
		<a href="<?php echo base_url (); ?>index.php/sys/system/">Ingresar</a>
 	<?php else :?>
		<?php echo 'Usuario: <u>' . $this->session->userdata ( 'person_fullname' ) . ' </u>';?>         
		<a target="_blank" href="http://utilidades.etecsa.cu/index2.html">Cambiar Contrase&ntilde;a</a>
		<a href="<?php echo base_url (); ?>index.php/sys/system/logout">Salir</a>
        <script language="javascript"> var session_rollId ='<?php echo $this->session->userdata('roll_id'); ?>'; </script>
                
	<?php endif;?>
</div>
</div>
</div>
	<?php
		$this->load->view ( 'sys/sys_menu_view' );
	?>
	<div class="clearfix"></div>
<div id="conten" class="clearfix">