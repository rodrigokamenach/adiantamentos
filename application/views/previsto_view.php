<script type="text/javascript">
function jCadPrv(){    		
	//antes de abrir a janela, preciso carregar os dados do cliente e preencher os campos dentro do modal    		  	
	$('#mCadPrv').modal('show');
}

function jEdPrv(id){    		
	//antes de abrir a janela, preciso carregar os dados do cliente e preencher os campos dentro do modal
	carregaDadosPrevisao(id);    		  	
	$('#mEdPrv').modal('show');
}

function carregaDadosPrevisao(id){
	 $.ajax({	        
	        url: "<?php echo base_url();?>index.php/previsto/buscaprv/"+id,
	        datatype: 'json',
	        type: 'post',
	        data: '',	        
	        success: function(data){           
	            //loading_hide('loading');	            
	            data = JSON.parse(data);	           	            	            	           		            		          
	            $(data).each(function(i, item) {
	            	$('#filial_ed option').each(function() {
		    		    if($(this).val() == item.USU_CODFIL) {
		    		        $(this).prop("selected", true);
		    		    }
		    		});
		            $("#dtini_ed").val(item.USU_DATINI);
		            $("#dtfim_ed").val(item.USU_DATFIM);
		            $("#cod").val(item.USU_CODPREV);					    
				    $("input[id^="+item.USU_CODAREA+"]").val(item.USU_VLRPRE);				    
				});
	            $('#filial_ed').selectpicker({    	       
	                size: "auto"
	        	        ,width: "200px"
	        	        ,style:'btn-sm btn-default'
	        	    });	
	            $('#filial_ed').selectpicker('refresh');
	            	            		                
	        }
	      }); 
	      return false;		     
}

function salvar(){ 
	loading_show('load_cprv');    		
	$.post($('#form_cad_prv').attr('action'), $('#form_cad_prv').serialize(), function( data ) {
		if(data.st == 0) {
			loading_hide('load_cprv');
			$('#validation-cprv').html(data.msg);
			window.location="<?php echo base_url();?>index.php/previsto";   		
		}
		if(data.st == 1) {
			loading_hide('load_cprv');
			$('#validation-cprv').html(data.msg);
		}
				
	}, 'json');
	return false;		
}

function alterar(){ 
	loading_show('load_eprv');
	$('#filial_ed').prop('disabled', false);    		
	$.post($('#form_ed_prv').attr('action'), $('#form_ed_prv').serialize(), function( data ) {
		if(data.st == 0) {
			loading_hide('load_eprv');
			$('#validation-eprv').html(data.msg);
			window.location="<?php echo base_url();?>index.php/previsto";   		
		}
		if(data.st == 1) {
			loading_hide('load_eprv');
			$('#validation-eprv').html(data.msg);
		}
				
	}, 'json');
	return false;		
}

function jExPrv(id){    		
	//antes de abrir a janela, preciso carregar os dados do cliente e preencher os campos dentro do modal
	//carregaDadosConvenio(codigo);    		
	bootbox.confirm("Confirma a exclusão?", function(result) {
		if (result) {
			$.ajax({  
                type: "POST",  
                url : '<?php echo base_url(); ?>index.php/previsto/deletar/',  
                data: {	id: id},
                dataType: "json", 
                success: function(data){
	                //alert(data.msg);
                	Example.show("Retorno: "+data.msg);
                	setInterval(function () {window.location="<?php echo base_url();?>index.php/previsto"; }, 5000);	                		                	
                }  
            }); 
		}
	});   
}
</script>
<script type="text/javascript">
$(document).ready(function () {	
	$('#filial').selectpicker();
	$('input[name^="vlrarea"]').maskMoney({symbol:"R$",decimal:",",thousands:"."});
	$('#dtini').datepicker({
        format: "dd/mm/yyyy",
        language: "pt-BR",
        orientation: "top left",
        calendarWeeks: true,
        autoclose: true,
        todayHighlight: true
        
    });

 $('#dtfim').datepicker({
        format: "dd/mm/yyyy",
        language: "pt-BR",
        orientation: "top left",
        calendarWeeks: true,
        autoclose: true,
        todayHighlight: true
        
    });
});
</script>
<script>
        $(function () {
            Example.init({
                "selector": ".bb-alert"
            });
        });
</script>
<div class="container-fluid">
	<div class="row-fluid">
		<div class="panel panel-custom">
			<div class="panel-heading">
		  		<h4 class="panel-title">Previsões</h4>		  				  	
		  	</div>
		  	<div class="panel-body">
		  		<div class="col-md-3">
		  			<a class="btn btn-md btn-success" onclick="jCadPrv()"><i class="fa fa-plus"></i> Adicionar Previsão</a>
		  		</div>
		  		<br></br>
		  		<div class="bb-alert alert alert-info" style="display:none;"></div>
		  		<?php if ($results) { ?>
		  		<table class="table table-condensed table-hover small">
		  			<thead>
			    		<tr class="cabecalho">		    		
			    			<th class="text-center" style="width: 30px">Previsão</th>
				    		<th class="text-center">Vigência</th>
				    		<th class="text-center">Filial</th>				    			    	
				    		<th class="text-center" style="width: 50px">Valor Total</th>
				    		<th class="text-center" colspan="2" style="width: 60px">Ações</th>
			    		</tr>
		    		</thead>
		  			<tbody>
		  			<?php foreach ($results as $row) { ?>			
		    			<tr>
		    				<td class="text-center" style="width: 30px"><?php echo $row->USU_CODPREV; ?></td>
		    				<td class="text-center"><?php ECHO $row->USU_DATINI ?> a <?php echo $row->USU_DATFIM ?></td>
		    				<td class="text-center"><?php ECHO $row->USU_CODFIL ?></td>
		    				<td class="text-right" style="width: 50px"><?php ECHO number_format(str_replace("," , "." , $row->VLRTOTAL), 2, ',', '.') ?></td>
		    				<td class="text-center" style="width: 30px"><a href="javascript:;" class="btn btn-warning btn-sm" onclick="jEdPrv(<?php echo $row->USU_CODPREV ?>)"><i class="fa fa-pencil-square-o fa-fw"></i></a></td>
		    				<td class="text-center" style="width: 30px"><a href="javascript:;" class="btn btn-danger btn-sm" onclick="jExPrv(<?php echo $row->USU_CODPREV ?>)"><i class="fa fa-trash-o fa-fw"></i></a></td>
		    			</tr>		    						    							    				    					    		
		    		<?php } ?>
		  			</tbody>		  		
		  		</table>
		  		<?php 
				} else {
				   	echo '<div class="alert alert-warning"><i class="fa fa-exclamation-circle"></i> Não existem dados para exibir.</div>';
				}
				?>
		  	</div>
		  	<div class="panel-footer clearfix">
		  		<div class="row-fluid">			  		
        		</div>        		       	
		  	</div>
		  	<div class="clearfix"></div>
		</div>
	</div>
</div>
<div class="clearfix"></div>

<!-- ------------------------------------------------------MODAL CADASTRAR PREVISAO ----------------------------------------------------------------------------------------------------- -->
<div class="modal fade" id="mCadPrv" >
	  <div class="modal-dialog modal-lg">
	    <div class="modal-content">
	      <div class="modal-header modal-header-custom">
	        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Fechar</span></button>
	        <h4 class="modal-title">Cadastrar Previsão</h4>
	      </div>
	      <div class="modal-body">
	      	<?php 		      		
					$cadp_attribute = array('id' => 'form_cad_prv', 'class' => 'form-horizontal'); 
					echo form_open('previsto/salvar', $cadp_attribute); 
				?>	      		      				
			 <div class="row-fluid">														 
			  	<div class="form-group">
				    <label for="keypass" class="control-label col-sm-3">Filial</label>
				    <div class="col-sm-6">
				    	<?php $options = array ('' => 'Escolha');
						    foreach($filiais as $fil)
						        $options[$fil->CODFIL] = $fil->CODFIL.' - '.$fil->USU_INSTAN.' - '.$fil->SIGFIL;
						    echo form_dropdown('filial', $options, 'class="selectpicker"', 'id="filial"');
						?>
				    </div>
			  	</div>
			  	<div class="form-group">
				    <label for="dtini" class="control-label col-sm-3">Período de Vigência</label>
				    <div class="col-sm-3">
				    	<input type="text" class="form-control" id="dtini" name="dtini">
				    </div>
				    <div class="col-sm-3">
				    	<input type="text" class="form-control" id="dtfim" name="dtfim">
				    </div>
			  	</div>
				<div class="container-fluid">					
						<legend><h4>Área x Valor</h4></legend>
						<div class="form-group">
						<?php 
						$cont = 1;
						if ($areas) { 
							foreach ($areas as $ar) {	
						?>						
						<div class="col-sm-6">
							<label for="nome_area" class="control-label col-sm-7 small"><?php echo $ar->USU_DESCAREA ?></label>
							<div class="col-sm-5">
								<input type="text" class="form-control" style="text-align:right" name="vlrarea[<?php echo $ar->USU_CODAREA ?>]" rel="<?php echo $ar->USU_CODAREA ?>" id="<?php echo $ar->USU_CODAREA ?>" value="0">
							</div>
						</div>
						
						<?php 
							$resto = $cont%2;
							if ($cont>1 and $resto == 0) {?>
								<br></br>																			
						<?php
								}
							$cont = $cont + 1;	
							}
						} else {
						    	echo '<div class="alert alert-warning"><i class="fa fa-exclamation-circle"></i> Não áreas cadastradas.</div>';
						}
						?>
					</div>
				</div>							  				  	      			    			   
	      </div>
	      <div class="modal-footer">
	      	<div class="row-fluid">
		      	<div id="load_cprv" class="col-sm-2"></div>
		      	<div id="validation-cprv" class="col-sm-6 left"></div>
		        <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> Cancelar</button>		        
		        <button type="button" name="cadadt" id="cadadt" onclick="salvar()" class="btn btn-success" ><i class="fa fa-floppy-o"></i> Salvar</button>
	        </div>	
	        <?php echo form_close();?>	        
	      </div>
	      </div>
	    </div><!-- /.modal-content -->
	  </div><!-- /.modal-dialog -->
	</div><!-- /.modal -->
	
	<!-- ------------------------------------------------------MODAL EDITAR PREVISAO ----------------------------------------------------------------------------------------------------- -->
<div class="modal fade" id="mEdPrv" >
	  <div class="modal-dialog modal-lg">
	    <div class="modal-content">
	      <div class="modal-header modal-header-custom">
	        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Fechar</span></button>
	        <h4 class="modal-title">Editar Previsão</h4>
	      </div>
	      <div class="modal-body">
	      	<?php 		      		
					$cadp_attribute = array('id' => 'form_ed_prv', 'class' => 'form-horizontal'); 
					echo form_open('previsto/alterar', $cadp_attribute); 
				?>	      		      				
			 <div class="row-fluid">														 
			  	<div class="form-group">
				    <label for="filial_ed" class="control-label col-sm-3">Filial</label>
				    <div class="col-sm-6">
				    	<?php $options = array ('' => 'Escolha');
						    foreach($filiais as $fil)
						        $options[$fil->CODFIL] = $fil->CODFIL.' - '.$fil->USU_INSTAN.' - '.$fil->SIGFIL;
						    echo form_dropdown('filial_ed', $options, 'class="selectpicker_ed"', 'id="filial_ed" disabled');
						?>
				    </div>
				    <input type="hidden" class="form-control" id="cod" name="cod">
			  	</div>
			  	<div class="form-group">
				    <label for="dtini_ed" class="control-label col-sm-3">Período de Vigência</label>
				    <div class="col-sm-3">
				    	<input type="text" class="form-control" id="dtini_ed" name="dtini_ed" readonly>
				    </div>
				    <div class="col-sm-3">
				    	<input type="text" class="form-control" id="dtfim_ed" name="dtfim_ed" readonly>
				    </div>
			  	</div>
				<div class="container-fluid">					
						<legend><h4>Área x Valor</h4></legend>
						<div class="form-group">
						<?php 
						$cont = 1;
						if ($areas) { 
							foreach ($areas as $ar) {	
						?>						
						<div class="col-sm-6">
							<label for="nome_area" class="control-label col-sm-7 small"><?php echo $ar->USU_DESCAREA ?></label>
							<div class="col-sm-5">
								<input type="text" class="form-control" style="text-align:right" name="vlrarea_ed[<?php echo $ar->USU_CODAREA ?>]" rel="<?php echo $ar->USU_CODAREA ?>" id="<?php echo $ar->USU_CODAREA ?>">
							</div>
						</div>
						
						<?php 
							$resto = $cont%2;
							if ($cont>1 and $resto == 0) {?>
								<br></br>																			
						<?php
								}
							$cont = $cont + 1;	
							}
						} else {
						    	echo '<div class="alert alert-warning"><i class="fa fa-exclamation-circle"></i> Não áreas cadastradas.</div>';
						}
						?>
					</div>
				</div>							  				  	      			    			   
	      </div>
	      <div class="modal-footer">
	      	<div class="row-fluid">
		      	<div id="load_eprv" class="col-sm-2"></div>
		      	<div id="validation-eprv" class="col-sm-6 left"></div>
		        <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> Cancelar</button>		        
		        <button type="button" name="cadadt" id="cadadt" onclick="alterar()" class="btn btn-success" ><i class="fa fa-floppy-o"></i> Alterar</button>
	        </div>	
	        <?php echo form_close();?>	        
	      </div>
	      </div>
	    </div><!-- /.modal-content -->
	  </div><!-- /.modal-dialog -->
	</div><!-- /.modal -->  
