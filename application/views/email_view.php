<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head><title>Aprovações de Adiantamentoss</title></head>
<link href="<?php echo base_url(); ?>assets/css/bootstrap.css" rel="stylesheet">
<script src="<?php echo base_url(); ?>assets/js/jquery-2.1.4.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/bootstrap.js"></script>
<body class="centro" data-spy="scroll" data-target="#sidebar">
<div id="wrap">
<div class="container">
	<nav class="navbar navbar-custom navbar-static-top">
		<div class="navbar-header">
 			<img alt="LabWeb" src="<?php echo base_url();?>assets/img/logo_menu.png"> 			
 		</div>
 		<div id="navbar" class="navbar-collapse collapse">
 			<h4>Adiantamentos aprovados e Títulos gerados</h4>
 		</div>
	</nav>	
	<div class="row-fluid">
		<div class="table-responsive">
			<table class="table table-striped table-hover table-condensed small">
				<thead>
					<tr class="cabecalho">
						<th class="text-center">Título</th>
						<th class="text-center">Pedido</th>
						<th class="text-center">Filial</th>
						<th class="text-center">Valor</th>
						<th class="text-center">Cod Forn</th>
						<th class="text-center">Fornecedor</th>
						<th class="text-center">Data Aprovação</th>
						<th class="text-center">Banco</th>
						<th class="text-center">Agência</th>
						<th class="text-center">Conta</th>						
					</tr>
				</thead>
				<tbody>					
				 <?php echo $msg_mail ?>					
				</tbody>
			</table>
		</div>
	</div>
</div>
</div>
</body>
</html>