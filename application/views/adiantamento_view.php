<script type="text/javascript">
function jCadAdt(){    		
	//antes de abrir a janela, preciso carregar os dados do cliente e preencher os campos dentro do modal    		  	
	$('#mCadAdt').modal('show');
}

function jEdAdt(filial, oc, id){    		
	//antes de abrir a janela, preciso carregar os dados do cliente e preencher os campos dentro do modal
	carregaDadosAdt(filial, oc, id);    		
	$('#mEdAdt').modal('show');
}

function carregaDadosAdt(filial, oc, id){
	$.post('<?php echo base_url(); ?>index.php/adiantamento/busca_adt', {
		filial: filial,
		oc: oc,
		id: id
	}, function (data){ 		      											
		$("#dtadt_ed").val(data.USU_DTLANC);
		$("#id_ed").val(data.USU_ID);
		$("#pedido_ed").val(data.USU_NUMOCP);
		$("#valor_ed").val(data.USU_VLRADT);
		$("#valor_ant").val(data.USU_VLRADT);		
		$("#obs_ed").val(data.USU_OBS);
		$("#wmp_ped").val(data.USU_CODEMP);
		$("input[name=prioriza_ed][value="+data.USU_ADTPRI+"]").prop("checked", true);
		$('#area_ed option').each(function() {
		    if($(this).val() == data.USU_AREA) {
		        $(this).prop("selected", true);
		    }
		});
		$('#filial_ed option').each(function() {
		    if($(this).val() == data.USU_FILIAL) {
		        $(this).prop("selected", true);
		    }
		});	
		$('#filial_ed').selectpicker();																			
		$('#area_ed').selectpicker();																  			
	}, 'json');
}

function loading_show(e) {
    $('#'+e).html("<img src='<?php echo base_url();?>assets/img/ajax_loader_blue_32.gif'/>").fadeIn('fast');
}
//Aqui desativa a imagem de loading
function loading_hide(e) {
    $('#'+e).fadeOut('fast');
}

function salva(){ 
	loading_show('load_cadt');
	var form = $('#form_cad_adt').serializeArray();
	var oc = form[1].value;
	var fil = form[2].value;
	if (fil == '') {
		alert("Informe a filial");
	}
		
	$.ajax({  
        type: "POST",  
        url : '<?php echo base_url(); ?>index.php/adiantamento/busca_for/'+oc+'/'+fil,
        //data: {
            //oc: oc, 
            //filial: fil
        //},
        datatype:'json',              
        success: function(data){  
        	//alert(data);
        		var fornec = JSON.parse(data);
        		var codfor = fornec.CODFOR;
        		var apefor = fornec.APEFOR;
        		var vlrabe = parseFloat(fornec.VLRABE).toFixed(2);
        		if (fornec.VLRABE !== 0) {
					texto = 'Possui ADTs em Aberto no Total de: <strong>R$ ' +vlrabe + '</strong></h4>';
            	} else {
                	texto = 'Não possui valores em aberto</strong></h4>';
            	}

        	bootbox.dialog({
			    title: 'Confirma Lançamento',
			    message: '<div class="row">  ' +
                			'<div class="col-md-12">' +
							'<h4 class="alert alert-success">' +
							'Fornecedor: <strong>' +codfor+ ' - ' +apefor + '</strong></h4>' + 
							'<h4 class="alert alert-danger">' +	
    						texto + 
    						'</div>' +
                			'</div> </div>',
			    buttons:{
			    	cancel: {
			            'label': 'Cancel',
			            'className': 'btn-danger',
			            'callback': function () {
		                	$('#mCadAdt').modal('hide');
		                	loading_hide('load_cadt');
		                	$('#form_cad_adt').each (function(){
		                		  this.reset();
		                	});
		                }	
			        },
				    success: {
			        		'label'     : 'Confirma',
			        		'className' : "btn-success",
			        		'callback'  : function(){			        					        		      		        		     
			        			$.post($('#form_cad_adt').attr('action'), $('#form_cad_adt').serialize(), function( data ) {
			        				//alert(data);
			        				if(data.st == 0) {
			        					loading_hide('load_cadt');
			        					$('#validation-cadt').html(data.msg);
			        					window.location="<?php echo base_url();?>index.php/adiantamento";   		
			        				}
			        				if(data.st == 1) {
			        					loading_hide('load_cadt');
			        					$('#validation-cadt').html(data.msg);
			        				}
			        						
			        			}, 'json');			        				
			        		}
				    }
			    }
			}); 
        }  
    });
		    			
}

function alterar(){ 
	loading_show('load_edadt');    		
	$.post($('#form_ed_adt').attr('action'), $('#form_ed_adt').serialize(), function( data ) {
		if(data.st == 0) {
			loading_hide('load_edadt');
			$('#validation-error-edadt').html(data.msg);
			window.location="<?php echo base_url();?>index.php/adiantamento";   		
		}
		if(data.st == 1) {
			loading_hide('load_edadt');
			$('#validation-error-edadt').html(data.msg);
		}
				
	}, 'json');
	return false;		
}

function jExAdt(fil, oc, id){    		
	//antes de abrir a janela, preciso carregar os dados do cliente e preencher os campos dentro do modal
	//carregaDadosConvenio(codigo);    		
	bootbox.confirm("Confirma a exclusÃ£o?", function(result) {
		if (result) {
			$.ajax({  
                type: "POST",  
                url : '<?php echo base_url(); ?>index.php/adiantamento/deletar/',  
                data: {
                	fil: fil,
                	oc: oc,
                	id: id}, 
                success: function(data){  
                	window.location="<?php echo base_url();?>index.php/adiantamento";  
                }  
            }); 
		}
	});   
}
</script>
<script type="text/javascript">
$(document).ready(function () {		
	$('#filial').selectpicker();
	$('#area').selectpicker();
	$("#valor").maskMoney({symbol:"R$",decimal:",",thousands:"."});			  

	 $("#prioriza").click(function() {
	 	$("#observacao:hidden").show('slow');
	 });
	 $("#prioriza").click(function(){
	 	if($('#prioriza').prop('checked')===false) {
	    	$('#observacao').hide('slow');
	    }
	 });

	 $('#dtadt').datepicker({
	        format: "dd/mm/yyyy",
	        language: "pt-BR",
	        orientation: "top left",
	        calendarWeeks: true,
	        autoclose: true,
	        todayHighlight: true
	        
	    });

	 $('#dtadt_cad').datepicker({
	        format: "dd/mm/yyyy",
	        language: "pt-BR",
	        orientation: "top left",
	        calendarWeeks: true,
	        autoclose: true,
	        todayHighlight: true
	        
	    });

		$('#dtadt_ed').datepicker({
	        format: "dd/mm/yyyy",
	        language: "pt-BR",
	        orientation: "top left",
	        calendarWeeks: true,
	        autoclose: true,
	        todayHighlight: true
	        
	    });
});
</script>
<div class="container-fluid"> 
	<div class="row-fluid">
		<div class="panel panel-custom">
			<div class="panel-heading">
		  		<h4 class="panel-title">Adiantamentos</h4>		  				  	
		  	</div>
		  	<div class="panel-body">
		  		<div class="col-md-3">
		  			<a class="btn btn-md btn-success" onclick="jCadAdt()"><i class="fa fa-plus"></i> Adicionar Adiantamento</a>
		  		</div>
		  			<?php $this->load->helper(array('form')); 
				  		  $bus_attribute = array('id' => 'form_bus_adt', 'class' => 'form-inline');
					  	  echo form_open('adiantamento/index', $bus_attribute);
			  		?>
			  		<div id="custom-search-input">
			  			<div class="form-group">
							<div class="col-md-2"> 		    							    			
		    					<input type="text" class="form-control input-sm" id="dtadt" placeholder="Data" name="dtadt" value="" >
		    				</div>		    				
	  					</div>
	  					<div class="form-group">
							<div class="col-md-2"> 		    							    			
		    					<input type="text" class="form-control input-sm" id="pedido" placeholder="Pedido" name="pedido" value="">
		    				</div>		    				
	  					</div>
	                	<div class="input-group col-md-2">
	                		<input type="text" class="form-control input-sm" placeholder="Filial" id="bvalor" name="bvalor" value="">                    	
	                    	<span class="input-group-btn">
	                        	<button class="btn btn-info btn-sm" type="submit">
	                            	<i class="fa fa-search"></i>
	                        	</button>
	                    	</span>
	                	</div>
	            	</div>			  					  			
			  		<?php echo form_close(); ?>			  	
		  		<br></br>		  	
		  		<?php if ($results) { ?>
				<table class="table table-condensed table-hover small">
					<thead>
			    		<tr class="cabecalho">		    		
			    			<th class="text-center">Pedido</th>
			    			<th class="text-center">Empresa</th>
				    		<th class="text-center">Filial</th>
				    		<th class="text-center">Data</th>
				    		<th class="text-center">Vlr Adt</th>
				    		<th class="text-center">Usu Dig</th>
				    		<th class="text-center">Vlr Apr</th>
				    		<th class="text-center">Usu Apr</th>
				    		<th class="text-center">Tit</th>				    		
				    		<th class="text-center">Área</th>
				    		<th>Obs</th>		    			    		
				    		<th colspan="2">Ações</th>
			    		</tr>
		    		</thead>
		    		<tbody>		    		    
		    		<?php 
		    			foreach ($results as $row) { 
		    			
		    				if($row->USU_ADTPRI == 'S') {
		    					$ped = '<h4><span class="label label-custom">'.$row->USU_NUMOCP.'</span></h4>';
		    				} else {
		    					$ped = $row->USU_NUMOCP;
		    				}		    						    			
		    		?>
						<tr>						
							<td class="text-center"><?php echo $ped ?></td>
							<td class="text-center"><?php echo $row->USU_CODEMP; ?></td>
							<td class="text-center"><?php echo $row->USU_FILIAL; ?></td>
							<td class="text-center"><?php echo $row->USU_DTLANC ?></td>
							<td class="text-right"><?php echo number_format(floatval($row->USU_VLRADT), 2, ',', '.') ?></td>
							<td class="text-center"><?php echo $row->USU_LANCUSU ?></td>
							<td class="text-right"><?php echo number_format($row->USU_VLRAPR, 2, ',', '.') ?></td>
							<td><?php echo $row->USU_APRUSU ?></td>
							<td><?php //echo $row->CPA_IN_AP ?></td>							
							<td class="text-center"><?php echo $row->USU_DESCAREA ?></td>
							<td class="text-left"><?php echo utf8_decode(htmlspecialchars_decode($row->USU_OBS)) ?></td>															
							<td class="text-center">
								<a href="javascript:;" class="btn btn-warning btn-sm" onclick="jEdAdt(<?php echo $row->USU_FILIAL ?>,<?php echo $row->USU_NUMOCP ?>,<?php echo $row->USU_ID ?>)"><i class="fa fa-pencil-square-o fa-fw"></i></a>
							</td>
							<td class="text-center">
								<a href="javascript:;" class="btn btn-danger btn-sm" onclick="jExAdt(<?php echo $row->USU_FILIAL ?>,<?php echo $row->USU_NUMOCP ?>,<?php echo $row->USU_ID ?>)"><i class="fa fa-trash-o fa-fw"></i></a>
							</td>
						</tr>
					<?php } 
		    			} else {
		    				echo '<div class="alert alert-warning"><i class="fa fa-exclamation-circle"></i> Não existem pedidos cadastradors nesta data.</div>';
		    			}
		    		
		    		?>
					</tbody>
		  		</table>		  	
		  	</div>
		  	<div class="panel-footer clearfix">
		  		<div class="row-fluid">
			  		<div class="col-md-12 text-center">
	            		<?php echo $pagination; ?>
	        		</div>
        		</div>        		       	
		  	</div>
		  	<div class="clearfix"></div> 		  
		</div>	
		</div>		           	
</div>
<div class="clearfix"></div>

<!-- ------------------------------------------------------MODAL EDITAL ADIANTAMENTO ----------------------------------------------------------------------------------------------------- -->	
	<div class="modal fade" id="mEdAdt" >
	  <div class="modal-dialog modal-lg">
	    <div class="modal-content">
	      <div class="modal-header modal-header-custom">
	        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Fechar</span></button>
	        <h4 class="modal-title">Editar Adiantamento</h4>
	      </div>
	      <div class="modal-body">
	      	<?php 		      		
					$edadt_attribute = array('id' => 'form_ed_adt', 'class' => 'form-horizontal'); 
					echo form_open('adiantamento/alterar', $edadt_attribute); 
				?>	      		      				
			 <div class="row-fluid">
				<div id="validation-error-edadt"></div>
				<div class="form-group">
				    <label for="dtadt_ed" class="control-label col-sm-3">Data</label>
				    <div class="col-sm-6">
				    	<input type="text" class="form-control" id="dtadt_ed" name="dtadt_ed">
				    </div>
			  	</div>
				<div class="form-group">
				    <label for="pedido_ed" class="control-label col-sm-3">Pedido</label>
				    <div class="col-sm-6">
				    	<input type="text" class="form-control" id="pedido_ed" name="pedido_ed">
				    </div>
			  	</div>			  	
			  	<div class="form-group">
				    <label for="filial_ed" class="control-label col-sm-3">Filial</label>
				    <div class="col-sm-6">
				    	<?php $options = array ('' => 'Escolha');
						    foreach($filiais as $fil)
						        $options[$fil->CODFIL] = $fil->CODFIL.' - '.$fil->USU_INSTAN.' - '.$fil->SIGFIL;
						    echo form_dropdown('filial_ed', $options, 'class="selectpicker_ed"', 'id="filial_ed"');
						?>
				    </div>
			  	</div>
			  	<div class="form-group">
				    <label for="valor_ed" class="control-label col-sm-3">Valor</label>
				    <div class="col-sm-6">
				    	<input type="text" class="form-control" id="valor_ed" name="valor_ed">
				    	<input type="hidden" class="form-control" id="valor_ant" name="valor_ant">
				    </div>
			  	</div>
			  	<div class="form-group">
			  		<label for="area_ed" class="control-label col-sm-3">Área</label>
				    <div class="col-sm-2">
				    	<?php $options = array ('' => 'Escolha');
						    foreach($areas as $ar)
						        $options[$ar->USU_CODAREA] = $ar->USU_DESCAREA;
						    echo form_dropdown('area_ed', $options, 'class="selectpicker_ed"', 'id="area_ed"');
						?>
				    </div>
			  	</div>			  	
				<div class="form-group">
					<label for="prioriza_ed" class="control-label col-sm-3">Prioritário</label>
					<div class="col-sm-1">
						<div class="checkbox">
							<label><input type="checkbox" id="prioriza_ed" name="prioriza_ed" value="1" /></label>
						</div>
					</div>
				</div>
				<div class="form-group" id="observacao_ed" >
				    <label for="obs_ed" class="control-label col-sm-3">Observação</label>
				    <div class="col-sm-6">
				    	<textarea class="form-control" rows="3" cols="10" id="obs_ed" name="obs_ed"></textarea>
				    </div>				    
				    <input type="hidden" name="emp_ped" id="emp_ped">
				    <input type="hidden" name="codigo_ed" id="codigo_ed" value="<?php echo $user_codigo; ?>" >
				    <input type="hidden" name="id_ed" id="id_ed" >
			  	</div>	      			    			   
	      </div>
	      <div class="modal-footer">
	      	<div class="row-fluid">
		      	<div id="load_edadt" class="col-sm-2"></div>
		        <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> Cancelar</button>		        
		        <button type="button" name="eddadt" id="eddadt" onclick="alterar()" class="btn btn-success" ><i class="fa fa-floppy-o"></i> Alterar</button>
	        </div>	
	        <?php echo form_close();?>	        
	      </div>
	      </div>
	    </div><!-- /.modal-content -->
	  </div><!-- /.modal-dialog -->
	</div><!-- /.modal --> 

	<!-- ------------------------------------------------------MODAL CADASTRAR ADIANTAMENTO ----------------------------------------------------------------------------------------------------- -->
<div class="modal fade" id="mCadAdt" >
	  <div class="modal-dialog modal-lg">
	    <div class="modal-content">
	      <div class="modal-header modal-header-custom">
	        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Fechar</span></button>
	        <h4 class="modal-title">Cadastrar Adiantamento</h4>
	      </div>
	      <div class="modal-body">
	      	<?php 		      		
					$cadt_attribute = array('id' => 'form_cad_adt', 'class' => 'form-horizontal'); 
					echo form_open('adiantamento/salvar', $cadt_attribute); 
				?>	      		      				
			 <div class="row-fluid">
				<div id="validation-cadt"></div>
				<div class="form-group">
				    <label for="dtadt_cad" class="control-label col-sm-3">Data</label>
				    <div class="col-sm-6">
				    	<input type="text" class="form-control" id="dtadt_cad" name="dtadt_cad">
				    </div>
			  	</div>
				<div class="form-group">
				    <label for="pedido" class="control-label col-sm-3">Pedido</label>
				    <div class="col-sm-6">
				    	<input type="text" class="form-control" id="pedido" name="pedido">
				    </div>
			  	</div>			  	
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
				    <label for="valor" class="control-label col-sm-3">Valor</label>
				    <div class="col-sm-6">
				    	<input type="text" class="form-control" id="valor" name="valor">
				    </div>
			  	</div>
			  	<div class="form-group">
			  		<label for="area" class="control-label col-sm-3">Área</label>
				    <div class="col-sm-2">
				    	<?php $options = array ('' => 'Escolha');
						    foreach($areas as $ar)
						        $options[$ar->USU_CODAREA] = $ar->USU_DESCAREA;
						    echo form_dropdown('area', $options, 'class="selectpicker"', 'id="area"');
						?>
				    </div>
			  	</div>			  	
				<div class="form-group">
					<label for="valor" class="control-label col-sm-3">Prioritário</label>
					<div class="col-sm-1">
						<div class="checkbox">
							<label><input type="checkbox" id="prioriza" name="prioriza" value="1" /></label>
						</div>
					</div>
				</div>
				<div class="form-group" id="observacao" style="display:none;">
				    <label for="obs" class="control-label col-sm-3">Observação</label>
				    <div class="col-sm-6">
				    	<textarea class="form-control" rows="3" cols="10" id="obs" name="obs"></textarea>
				    </div>				    
				    <input type="hidden" name="codigo" id="codigo" value="<?php echo $user_codigo; ?>" >
			  	</div>	      			    			   
	      </div>
	      <div class="modal-footer">
	      	<div class="row-fluid">
		      	<div id="load_cadt" class="col-sm-2"></div>
		        <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> Cancelar</button>		        
		        <button type="button" name="cadadt" id="cadadt" onclick="salva()" class="btn btn-success" ><i class="fa fa-floppy-o"></i> Salvar</button>
	        </div>	
	        <?php echo form_close();?>	        
	      </div>
	      </div>
	    </div><!-- /.modal-content -->
	  </div><!-- /.modal-dialog -->
	</div><!-- /.modal --> 