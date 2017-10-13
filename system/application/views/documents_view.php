<br>
<br>
<body>

<font color="#000066" style="font: xx-large">Modelos Oficiales</font>
<br>
<?php 
$pathname='';
$name='';
$res=$this->db->query('select * from documents order by name,dateput');
		if ($res->result () != null) {
		   foreach ( $res->result () as $row ){  //esta en esta tabla
			$pathname = $row->pathname;
			$name= $row->name;  			
			echo '<br><p><a href=\''.$this->config->item ( 'base_app_url' ).'docs/'.$pathname.'\''." target='_blank'><PRE>  -". $name.'-</PRE></a></p>';
		   }
		 }
?>
</body>