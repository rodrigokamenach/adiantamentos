<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="pt-br">
 <head>
 	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="shortcut icon" href="<?php echo base_url(); ?>assets/img/gf.ico" />  
 	<link href="<?php echo base_url(); ?>assets/css/bootstrap.css" rel="stylesheet">
	<link href="<?php echo base_url(); ?>assets/css/font-awesome.css" rel="stylesheet">	
	
	<script src="<?php echo base_url(); ?>assets/js/jquery-1.11.3.min.js"></script>
	<script src="<?php echo base_url(); ?>assets/js/bootstrap.js"></script>
   <title>GF - Sistema de Controle de Pagamentos à Vista</title>
 </head>
<body class="login">
<div class="container-fluid">
	<div class="row">
        <div class="col-md-4 col-md-offset-4">
            <div class="panel panel-custom">
            	<div class="panel-heading">
            		<!--<h5>Sistema de Controle de Coletas</h5>-->
					<img alt="LabWeb" src="<?php echo base_url(); ?>assets/img/logo.png" class="center-block">
				</div>
				 <div class="panel-body">													
				<?php echo validation_errors(); ?>				
				<?php echo form_open('verifylogin'); ?>
				<fieldset>										
				<div style="margin-bottom: 25px" class="input-group">	
					<span class="input-group-addon"><i class="fa fa-user"></i></span>
					<input type="text" size="20" id="username" name="username" class="form-control" placeholder="usuario"/>														
				</div>
				<div style="margin-bottom: 25px" class="input-group">			
					<span class="input-group-addon"><i class="fa fa-unlock-alt"></i></span>
					<input type="password" size="20" id="passowrd" name="password" class="form-control" placeholder="senha"/>
				</div>																					
				<div class="control-group">
					<input type="submit" value="Acessar" class="btn btn-success btn-md btn-block"/>
					<div class="clearfix"></div>
				</div>
				</fieldset>
				<?php echo form_close();?>
				</div>							
			</div>
		</div>
	</div>
</div>
</body>
</html>