<script type="text/javascript">
	$(function() {
		$('#fecha').on('click', function() {
			$('#form_ed_user').find('input:text, input:password, select, textarea').val('');
			$('#form_ed_user').find('input:radio, input:checkbox').prop('checked', false);
			$("#filial_ed").multiselect("refresh");
		});
	});

	function jCadUser(){    		
		//antes de abrir a janela, preciso carregar os dados do cliente e preencher os campos dentro do modal    		  	
		$('#mCadUser').modal('show');
		$("select[name='filial[]']").multiselect({
		   	 includeSelectAllOption: true,
		        enableFiltering: true,
		        enableCaseInsensitiveFiltering: true,
		        maxHeight: 300
			 });
		$("select[name='area[]']").multiselect({
		   	 includeSelectAllOption: true,
		        enableFiltering: true,
		        enableCaseInsensitiveFiltering: true,
		        maxHeight: 300
			 });
	}

	function carregaDadosUser(codigo){
		$.post('<?php echo base_url(); ?>index.php/usuario/busca_user', {
			codigo: codigo
		}, function (data){        									
			$("#user_ed").val(data.USU_NOMUSU);
			$("#email_ed").val(data.USU_MAILUS);
			$("#sistema_ed").val(data.USU_SISTEM);
			var perm = data.USU_PERMIS;
			var permissao = new Array();
			var permissao = perm.split(";");
			$("input:radio[name=digitador][value="+permissao[0]+"]").prop("checked", true);
			$("input[name=gestor][value="+permissao[1]+"]").prop("checked", true);
			$("input[name=aprovador][value="+permissao[2]+"]").prop("checked", true);
			$("input[name=admin][value="+permissao[3]+"]").prop("checked", true);
			$("input[name=funcional][value="+permissao[4]+"]").prop("checked", true);
			$("input[name=consulta][value="+permissao[5]+"]").prop("checked", true);
			var fil = data.USU_FILUSU;
			if (fil != null) {
				var filial = new Array();				
				var filial = fil.split(",");		
				var i = 0;							
				$('#filial_ed option').each(function (){
				    var option_val = this.value;
				    for (var i in filial) {
				        if(option_val === filial[i]){
				            $("#filial_ed option[value='"+this.value+"']").attr("selected", 1);
				        }
				    }			    
				});
				$("#filial_ed").multiselect("refresh");
			}
			var are = data.USU_CODAREA;
			if (are != null) {
				var area = new Array();				
				var area = are.split(",");		
				var i = 0;							
				$('#area_ed option').each(function (){
				    var option_val = this.value;
				    for (var i in area) {
				        if(option_val === area[i]){
				            $("#area_ed option[value='"+this.value+"']").attr("selected", 1);
				        }
				    }			    
				});
				$("#area_ed").multiselect("refresh");
			}									    		
			//aqui eu seto a o input hidden com o id do cliente, para que a edição funcione. Em cada tela aberta, eu seto o id do cliente. 
		}, 'json');
	}

	function jEditarUser(codigo){    		
		//antes de abrir a janela, preciso carregar os dados do cliente e preencher os campos dentro do modal
		carregaDadosUser(codigo);    		
    	$('#mEditUser').modal('show');
    	$("select[name='filial_ed[]']").multiselect({
		   	 includeSelectAllOption: true,
		        enableFiltering: true,
		        enableCaseInsensitiveFiltering: true,
		        maxHeight: 300,		        
			 });
    	$("select[name='area_ed[]']").multiselect({
		   	 includeSelectAllOption: true,
		        enableFiltering: true,
		        enableCaseInsensitiveFiltering: true,
		        maxHeight: 300,		        
			 });
	}
</script>
<script type="text/javascript">	
	function loading_show(e) {
	    $('#'+e).html("<img src='<?php echo base_url();?>assets/img/ajax_loader_blue_32.gif'/>").fadeIn('fast');
	}
	//Aqui desativa a imagem de loading
	function loading_hide(e) {
	    $('#'+e).fadeOut('fast');
	}

	function altera() { 
		loading_show('load_ed'); 		
	      var dataString = $("#form_ed_user").serialize();
	      $.ajax({	        
	        url: "<?php echo base_url();?>index.php/usuario/alterar",
	        datatype: 'json',
	        type: "post",
	        data: dataString,	        
	        success: function(data){           
	            loading_hide('loading');
	            data = JSON.parse(data);	            	            	           
	            if(data.st == 0) {
	    			loading_hide('load_ed');
	    			$('#validation-ed').html(data.msg);
	    			window.location="<?php echo base_url();?>index.php/usuario";   		
	    		}
	    		if(data.st == 1) {
	    			loading_hide('load_ed');
	    			$('#validation-ed').html(data.msg);
	    		}	           
	        }
	      }); 
	      return false;   				
	}
	
	function salvar(){ 
		loading_show('load_cad');    		
		$.post($('#form_cad_user').attr('action'), $('#form_cad_user').serialize(), function( data ) {
    		if(data.st == 0) {
    			loading_hide('load_cad');
    			$('#validation-error-cad').html(data.msg);
    			window.location="<?php echo base_url();?>index.php/usuario";   		
    		}
    		if(data.st == 1) {
    			loading_hide('load_cad');
    			$('#validation-error-cad').html(data.msg);
    		}
					
		}, 'json');
		return false;		
	}

	

</script>
<script>
	$(document).ready(function(){
	    $('[data-toggle="tooltip"]').tooltip(); 
	});
	
</script>
<div class="container-fluid">
	<div class="row-fluid">
		<div class="panel panel-custom">
			<div class="panel-heading">
				<h4 class="panel-title">Usuários</h4>
			</div>
			<div class="panel-body">
				<div class="col-md-12">
					<!--  <a class="btn btn-md btn-success" onclick="jCadUser()"><i class="fa fa-plus"></i> Adicionar Usuário</a>-->
				</div>
				<br></br>
		  	<?php if ($results) { ?>
		  	<div class="table-responsive">
					<table
						class="col-md-12 table table-striped table-condensed small cf">
						<thead>
							<tr>
								<th>Código</th>
								<th>Usuário</th>
								<th>Permissão(D-G-A-Ad-C)</th>
								<th>Sistemas</th>
								<th>Filiais</th>
								<th>Email</th>
								<th>Área</th>
								<th colspan="2">Ações</th>
							</tr>
						</thead>
						<tbody>		    	
		    	<?php foreach ($results as $row) { ?>
					<tr>
								<td><?php echo $row->USU_CODUSU; ?></td>
								<td><?php echo $row->USU_NOMUSU; ?></td>
								<td><?php echo $row->USU_PERMIS; ?></td>
								<td><?php echo $row->USU_SISTEM; ?></td>
								<td nowrap="TRUE"><a href="#" data-toggle="tooltip" data-placement="left" title="<?php echo $row->USU_FILUSU ?>"><?php echo substr_replace($row->USU_FILUSU, '...', 30); ?></a></td>
								<td><?php echo $row->USU_MAILUS; ?></td>
								<td nowrap="TRUE"><a href="#" data-toggle="tooltip" data-placement="left" title="<?php echo $row->USU_CODAREA ?>"><?php echo substr_replace($row->USU_CODAREA, '...', 30); ?></a></td>
								<td class="text-center"><a href="javascript:;"
									class="btn btn-warning btn-md"
									onclick="jEditarUser('<?php echo $row->USU_CODUSU; ?>')"><i
										class="fa fa-pencil-square-o fa-fw"></i></a></td>
								<td class="text-center"><a href="javascript:;"
									class="btn btn-danger btn-md"
									onclick="jExcluirUser(<?php echo $row->USU_CODUSU; ?>)"><i
										class="fa fa-trash-o"></i></a></td>
							</tr>
				<?php
						
}
					} else {
						echo '<div class="alert alert-warning"><i class="fa fa-exclamation-circle"></i> Não existem convênios cadastrados.</div>';
					}
					
					?>
				</tbody>
					</table>
				</div>
			</div>
			<div class="panel-footer">				
			</div>			
		</div>
	</div>	

</div>
<div class="clearfix"></div>


<!-- /.MODAL ADD USUARIO -->
<div class="modal fade" id="mCadUser">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header modal-header-custom">
				<button type="button" class="close" data-dismiss="modal">
					<span aria-hidden="true">&times;</span><span class="sr-only">Fechar</span>
				</button>
				<h4 class="modal-title">Cadastrar Usuário</h4>
			</div>
			<div class="modal-body">
	      <?php
							$this->load->helper ( array (
									'form' 
							) );
							$cad_attribute = array (
									'id' => 'form_cad_user',
									'class' => 'form-horizontal' 
							);
							echo form_open ( 'usuario/salvar', $cad_attribute );
							?>	      		      				
			 <div class="row-fluid">
					<div id="validation-error-cad"></div>
					<div class="form-group">
						<label for="user" class="control-label col-sm-2">Usuário</label>
						<div class="col-sm-9">
							<input type="text" class="form-control" id="user" name="user">
						</div>
					</div>
					<!--  <div class="form-group">
				    <label for="keypass" class="control-label col-sm-2">Senha</label>
				    <div class="col-sm-9">
				    	<input type="password" class="form-control" id="keypass" name="keypass">
				    </div>
			  	</div>-->
					<div class="form-group">
						<label for="keypass" class="control-label col-sm-2">Filial</label>
						<div class="col-sm-9">
				    	<?php								
									$options = array ();
									foreach ( $filiais as $fil )
										$options [$fil->CODFIL] = $fil->SIGFIL;
									echo form_dropdown ( 'filial[]', $options, '', 'id="filial" multiple' );
									?>
				    	</div>
					</div>
					<div class="form-group">
						<label for="email" class="control-label col-sm-2">Email</label>
						<div class="col-sm-9">
							<input type="text" class="form-control" id="email" name="email">
						</div>
					</div>
					<div class="form-group">
						<label for="sistem" class="control-label col-sm-2">Sistemas</label>
						<div class="col-sm-9">
							<input type="text" class="form-control" id="sistem" name="sistem">
						</div>
					</div>
					<div class="form-group">
						<label for="area" class="control-label col-sm-2">Área</label>
						<div class="col-sm-9">
				    	<?php								
									$options = array ();
									foreach ( $areas as $ar )
										$options[$ar->USU_CODAREA] = $ar->USU_DESCAREA;
									echo form_dropdown ( 'area[]', $options, '', 'id="area" multiple' );
									?>
				    	</div>
					</div>
					<div class="container-fluid">
						<div class="form-group">
							<legend>
								<h4>Permissões</h4>
							</legend>
							<label for="digitador" class="control-label col-sm-3">Digitador</label>
							<div class="col-sm-9">
								<label class="radio-inline"> <input type="radio"
									name="digitador" id="digitador" value="S"> Sim
								</label> <label class="radio-inline"> <input type="radio"
									name="digitador" id="digitador" value="N" checked> Não
								</label>
							</div>
							<label for="digitador" class="control-label col-sm-3">Gerente</label>
							<div class="col-sm-9">
								<label class="radio-inline"> <input type="radio" name="gestor"
									id="gestor" value="S"> Sim
								</label> <label class="radio-inline"> <input type="radio"
									name="gestor" id="gestor" value="N" checked> Não
								</label>
							</div>
							<label for="digitador" class="control-label col-sm-3">Diretor</label>
							<div class="col-sm-9">
								<label class="radio-inline"> <input type="radio"
									name="aprovador" id="aprovador" value="S"> Sim
								</label> <label class="radio-inline"> <input type="radio"
									name="aprovador" id="aprovador" value="N" checked> Não
								</label>
							</div>
							<label for="digitador" class="control-label col-sm-3">Administrador</label>
							<div class="col-sm-9">
								<label class="radio-inline"> <input type="radio" name="admin"
									id="admin" value="S"> Sim
								</label> <label class="radio-inline"> <input type="radio"
									name="admin" id="admin" value="N" checked> Não
								</label>
							</div>
							<label for="funcional" class="control-label col-sm-3">Funcional</label>
							<div class="col-sm-9">
								<label class="radio-inline"> <input type="radio" name="funcional"
									id="funcional" value="S"> Sim
								</label> <label class="radio-inline"> <input type="radio"
									name="funcional" id="funcional" value="N" checked> Não
								</label>
							</div>
							<label for="digitador" class="control-label col-sm-3">Consulta</label>
							<div class="col-sm-9">
								<label class="radio-inline"> <input type="radio"
									name="consulta" id="consulta" value="S"> Sim
								</label> <label class="radio-inline"> <input type="radio"
									name="consulta" id="consulta" value="N" checked> Não
								</label>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<div class="row-fluid">
					<div id="load_cad" class="col-sm-2"></div>
					<button type="button" class="btn btn-danger" data-dismiss="modal">
						<i class="fa fa-times"></i> Cancelar
					</button>
					<button type="button" name="cadconv" id="cadconv"
						onclick="salvar()" class="btn btn-success">
						<i class="fa fa-floppy-o"></i> Salvar
					</button>
				</div>	
	        <?php echo form_close();?>	        
	      </div>
		</div>
		<!-- /.modal-content -->
	</div>
	<!-- /.modal-dialog -->
</div>
<!-- /.modal -->

<!-- /.MODAL EDIAR USUARIO -->
<div class="modal fade" id="mEditUser">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
	    <?php
					$ed_attribute = array (
							'id' => 'form_ed_user',
							'class' => 'form-horizontal' 
					);
					echo form_open ( 'usuario/alterar', $ed_attribute );
					?>	
	      <div class="modal-header modal-header-custom">
				<button type="button" class="close" data-dismiss="modal">
					<span aria-hidden="true">&times;</span><span class="sr-only">Fechar</span>
				</button>
				<h4 class="modal-title">Editar Usuário</h4>
			</div>
			<div class="modal-body">
				<div class="row-fluid">
					<div id="validation-ed"></div>
					<div class="form-group">
						<label for="user" class="control-label col-sm-2">Usuário</label>
						<div class="col-sm-9">
							<input type="text" class="form-control" id="user_ed"
								name="user_ed" readonly="readonly">
						</div>
					</div>
					<div class="form-group">
						<label for="sistema_ed" class="control-label col-sm-2">Sistemas</label>
						<div class="col-sm-9">
							<input type="tect" class="form-control" id="sistema_ed"
								name="sistema_ed">
						</div>
					</div>
					<div class="form-group">
						<label for="keypass" class="control-label col-sm-2">Filial</label>
						<div class="col-sm-9">
				    	<?php
									
$options = array ();
									foreach ( $filiais as $fil )
										$options [$fil->CODFIL] = $fil->SIGFIL;
									echo form_dropdown ( 'filial_ed[]', $options, '', 'id="filial_ed" multiple' );
									?>
				    </div>
					</div>
					<div class="form-group">
						<label for="email_ed" class="control-label col-sm-2">Email</label>
						<div class="col-sm-9">
							<input type="text" class="form-control" id="email_ed"
								name="email_ed">
						</div>
					</div>
					<div class="form-group">
						<label for="area_ed" class="control-label col-sm-2">Área</label>
						<div class="col-sm-9">
				    	<?php								
									$options = array ();
									foreach ( $areas as $ar )
										$options[$ar->USU_CODAREA] = $ar->USU_DESCAREA;
									echo form_dropdown ( 'area_ed[]', $options, '', 'id="area_ed" multiple' );
									?>
				    	</div>
					</div>
					<div class="container-fluid">
						<div class="form-group">
							<legend>
								<h4>Permissões</h4>
							</legend>
							<label for="digitador_ed" class="control-label col-sm-3">Digitador</label>
							<div class="col-sm-9">
								<label class="radio-inline"> <input type="radio"
									name="digitador" id="digitador" value="S"> Sim
								</label> <label class="radio-inline"> <input type="radio"
									name="digitador" id="digitador" value="N"> Não
								</label>
							</div>
							<label for="digitador" class="control-label col-sm-3">Gerente</label>
							<div class="col-sm-9">
								<label class="radio-inline"> <input type="radio" name="gestor"
									id="gestor" value="S"> Sim
								</label> <label class="radio-inline"> <input type="radio"
									name="gestor" id="gestor" value="N"> Não
								</label>
							</div>
							<label for="digitador" class="control-label col-sm-3">Diretor</label>
							<div class="col-sm-9">
								<label class="radio-inline"> <input type="radio"
									name="aprovador" id="aprovador" value="S"> Sim
								</label> <label class="radio-inline"> <input type="radio"
									name="aprovador" id="aprovador" value="N"> Não
								</label>
							</div>
							<label for="digitador" class="control-label col-sm-3">Administrador</label>
							<div class="col-sm-9">
								<label class="radio-inline"> <input type="radio" name="admin"
									id="admin" value="S"> Sim
								</label> <label class="radio-inline"> <input type="radio"
									name="admin" id="admin" value="N"> Não
								</label>
							</div>
							<label for="funcional" class="control-label col-sm-3">Funcional</label>
							<div class="col-sm-9">
								<label class="radio-inline"> <input type="radio" name="funcional"
									id="funcional" value="S"> Sim
								</label> <label class="radio-inline"> <input type="radio"
									name="funcional" id="funcional" value="N"> Não
								</label>
							</div>
							<label for="digitador" class="control-label col-sm-3">Consulta</label>
							<div class="col-sm-9">
								<label class="radio-inline"> <input type="radio"
									name="consulta" id="consulta" value="S"> Sim
								</label> <label class="radio-inline"> <input type="radio"
									name="consulta" id="consulta" value="N"> Não
								</label>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<div class="row-fluid">
					<div id="load_ed" class="col-sm-2"></div>
					<button type="button" class="btn btn-danger" name="fecha"
						id="fecha" data-dismiss="modal">
						<i class="fa fa-times"></i> Cancelar
					</button>
					<button type="button" name="edconv" id="edconv" onclick="altera()"
						class="btn btn-success">
						<i class="fa fa-floppy-o"></i> Salvar
					</button>
				</div>	
	        <?php echo form_close();?>	        
	      </div>
		</div>
		<!-- /.modal-content -->
	</div>
	<!-- /.modal-dialog -->
</div>
<!-- /.modal -->
