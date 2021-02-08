<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="pt-br">
 <head>
 	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="shortcut icon" href="<?php echo base_url(); ?>assets/img/gf.ico" />
	<link href="<?php echo base_url(); ?>assets/css/bootstrap.css" rel="stylesheet">
	<link href="<?php echo base_url(); ?>assets/css/font-awesome.css" rel="stylesheet">
	<link href="<?php echo base_url(); ?>assets/css/bootstrap-datepicker.css" rel="stylesheet">
	<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/theme.bootstrap.css">
	<link href="<?php echo base_url(); ?>assets/css/bootstrap-select.css" rel="stylesheet" media="screen">
	<link href="<?php echo base_url(); ?>assets/css/bootstrap-switch.css" rel="stylesheet">	
	
	<!--<script src="<?php echo base_url(); ?>assets/js/jquery-2.1.4.min.js"></script>-->
	<script src="<?php echo base_url(); ?>assets/js/jquery-2.2.0.min.js"></script>
	<!--  <script src="<?php echo base_url(); ?>assets/js/jquery-1.11.3.min.js"></script>-->	
	<script src="<?php echo base_url(); ?>assets/js/bootstrap.js"></script>	
	<script src="<?php echo base_url(); ?>assets/js/bootstrap-datepicker.js"></script>
    <script src="<?php echo base_url(); ?>assets/js/bootstrap-datepicker.pt-BR.js" charset="UTF-8"></script> 
    <script src="<?php echo base_url(); ?>assets/js/bootstrap-select.js"></script>
    <script src="<?php echo base_url(); ?>assets/js/jquery.maskMoney.js" type="text/javascript"></script>
    <script src="<?php echo base_url(); ?>assets/js/jquery.maskedinput.js" type="text/javascript"></script>
    	
	<script src="<?php echo base_url(); ?>assets/js/jquery.tablesorter.js"></script>
	<script src="<?php echo base_url(); ?>assets/js/jquery.tablesorter.widgets.js"></script>
	<script src="<?php echo base_url(); ?>assets/js/jquery.table2excel.js"></script>
	
	<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/bootstrap-multiselect.js"></script>
	<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/bootstrap-multiselect.css" type="text/css"/>
 	
 	<script src="<?php echo base_url(); ?>assets/js/bootbox.min.js"></script>
 	<script src="<?php echo base_url(); ?>assets/js/password-score.js"></script> 	 
 	<script src="<?php echo base_url(); ?>assets/js/password-score-options.js"></script> 	  		
 	<script src="<?php echo base_url(); ?>assets/js/bootstrap-strength-meter.js"></script>
 	<script src="<?php echo base_url(); ?>assets/js/jquery.blockUI.js"></script> 
 	<script src="<?php echo base_url(); ?>assets/js/bootstrap-switch.js"></script>
 		    
 	
   <title>GF - Sistema de Controle de Pagamentos à Vista</title>
 </head>
 <?php 
 	$data = $this->session->userdata('newadt');
 	$username = $data['usuario'];
 	$permissoes = explode(';', $data['usu_permissoes']);
 	$usu_filial = $data['usu_filial'];
 	$usu_email = $data['usu_email']; 
 	$usu_codigo = $data['usu_codigo']; 	
 ?>
<body class="centro" data-spy="scroll" data-target="#sidebar">
<script type="text/javascript">
		function jMudaSenha(){    		
			//antes de abrir a janela, preciso carregar os dados do cliente e preencher os campos dentro do modal    		  	
			$('#mMudaSenha').modal('show');
		}

		function loading_show(e) {
			    $('#'+e).html("<img src='<?php echo base_url();?>assets/img/ajax_loader_blue_32.gif'/>").fadeIn('fast');
		}
			//Aqui desativa a imagem de loading
		function loading_hide(e) {
		    $('#'+e).fadeOut('fast');
		}
	
	 	function mudasenha(){ 
			loading_show('load_ed');    		
			$.post($('#form_md_user').attr('action'), $('#form_md_user').serialize(), function( data ) {
				if(data.st == 0) {
					loading_hide('load_ed');
					$('#validation-error-ed').html(data.msg);
					window.location="<?php echo base_url();?>index.php/home";   		
				}
				if(data.st == 1) {
					loading_hide('load_ed');
					$('#validation-error-ed').html(data.msg);
				}
						
			}, 'json');
			return false;		
		}
</script>

<div id="wrap">
 	<nav class="navbar navbar-custom navbar-static-top">
 		<div class="container-fluid">
 			<div class="navbar-header">
 				<img alt="LabWeb" src="<?php echo base_url();?>assets/img/logo_menu.png">
 			</div>
 			<div id="navbar" class="navbar-collapse collapse">
 				<ul class="nav navbar-nav">
 					<li class="active"><a href="<?php echo base_url(); ?>index.php/home"><i class="fa fa-home"></i> Home</a></li>
 					<li class="dropdown">
              		<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><i class="fa fa-pencil-square-o"></i> Cadastro <span class="caret"></span></a>
              			<ul class="dropdown-menu">
              				<li class="dropdown-header">Básicos</li>
              				<li><a href="<?php echo base_url(); ?>index.php/previsto"><i class="fa fa-usd" aria-hidden="true"></i> Previsões</a></li>
                			<li><a href="<?php echo base_url(); ?>index.php/adiantamento"><i class="fa fa-plus-circle"></i> Adiantamento</a></li>
                			<?php if ($usu_codigo == 109 || $usu_codigo == 114 || $usu_codigo == 18) {?>
                			<li><a href="<?php echo base_url(); ?>index.php/dia"><i class="fa fa-calendar"></i> Abre/Fecha Dia</a></li>
                			<?php }?>
                			<li><a href="<?php echo base_url(); ?>index.php/transferencia"><i class="fa fa-wrench"></i> Manutenção</a></li>
                			<?php if($permissoes[3] == 'S') {
                				echo '<li role="separator" class="divider"></li>';
                				echo '<li class="dropdown-header">Acesso</li>';
                				echo '<li><a href="'.base_url().'index.php/usuario"><i class="fa fa-user"></i> Usúario</a></li>';
                			}
                			?>                			               			              	
              			</ul>
            		</li>
 					<li class="active"><a href="<?php echo base_url(); ?>index.php/consulta"><i class="fa fa-search"></i> Consulta</a></li>
 					<?php 
 						if($permissoes[1] == 'S' || $permissoes[2] == 'S' || $permissoes[3] == 'S') {
 							echo '<li class="active"><a href="'.base_url('index.php/aprovado').'"><i class="fa fa-check-square-o"></i> Aprovação</a></li>';
 						}
 					?>
 					<li class="active"><a href="<?php echo base_url(); ?>index.php/gerar"><i class="fa fa-cog"></i> Gera AP</a></li> 					            		            	
            	</ul>
 				<ul class="nav navbar-nav navbar-right">
 					<li class="dropdown">
 						<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><i class="fa fa-user"></i> <?php echo $username; ?> <span class="caret"></span></a>
 						<ul class="dropdown-menu">
 							<!--  <li><a href="javascript:;" onclick="jMudaSenha()"><i class="fa fa-key"></i> Alterar Senha</a></li>-->
 							<li><a href="home/logout"><i class="fa fa-sign-out"></i> Sair</a></li>
 						</ul>
 					</li>
 				</ul> 				
 			</div>
 		</div>
 	</nav>   
 <!----------------------------------------------------------------- MUDA SENHA  ------------------------------------------------------------------------------------------------->
 <script type="text/javascript">
	$(document).ready(function() {
		$('#nova').strengthMeter('progressBar', {
            container: $('#example-progress-bar-container')
        });
	});
</script> 
 <div class="modal fade" id="mMudaSenha" >
	  <div class="modal-dialog modal-md">
	    <div class="modal-content">
	      <div class="modal-header modal-header-custom">
	        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Fechar</span></button>
	        <h4 class="modal-title">Alterar Senha</h4>
	      </div>
	      <div class="modal-body">
	      <?php 	
	      		$this->load->helper(array('form'));
				$ed_attribute = array('id' => 'form_md_user', 'class' => 'form-horizontal'); 
				echo form_open('usuario/muda_senha', $ed_attribute); 
			?>	      		      				
			 <div class="row-fluid">
				<div id="validation-error-ed"></div>
				<div class="form-group">
				    <label for="user_md" class="control-label col-sm-3">Usuário</label>
				    <div class="col-sm-9">
				    	<input type="text" class="form-control" id="user_md" name="user_md" value="<?php echo $username ?>" readonly="readonly">
				    	<input type="hidden" name="id" id="id" value="<?php echo $username ?>" />
				    </div>
			  	</div>
			  	<div class="form-group">
				    <label for="atual" class="control-label col-sm-3">Senha Atual</label>
				    <div class="col-sm-9">
				    	<input type="password" class="form-control" id="atual" name="atual">
				    </div>				    		   
			  	</div>			  	
				<div class="form-group">
				    <label for="nova" class="control-label col-sm-3">Nova Senha</label>
				    <div class="col-sm-9">
				    	<input type="password" class="form-control" id="nova" name="nova">
				    </div>				    
			  	</div>
			  	<div class="form-group">
                	<label class="control-label col-sm-3">Força da Senha</label>
                    <div class="col-sm-9" id="example-progress-bar-container"></div>
                </div>				  				  	
			  	<div class="form-group">
				    <label for="confirma" class="control-label col-sm-3">Confirmação</label>
				    <div class="col-sm-9">
				    	<input type="password" class="form-control" id="confirma" name="confirma">
				    </div>
			  	</div>	  				  				  			                    	
			</div>  				    			   
	      </div>
	      <div class="modal-footer">
	      	<div class="row-fluid">
		      	<div id="load_ed" class="col-sm-2"></div>
		        <button type="button" class="btn btn-danger" name="closepac" id="closepac" data-dismiss="modal"><i class="fa fa-times"></i> Cancelar</button>		        
		        <button type="button" name="edpac" id="edpac" onclick="mudasenha()" class="btn btn-success" ><i class="fa fa-floppy-o"></i> Salvar</button>
	        </div>	
	        <?php echo form_close();?>	        
	      </div>
	    </div><!-- /.modal-content -->
	  </div><!-- /.modal-dialog -->
	</div><!-- /.modal -->