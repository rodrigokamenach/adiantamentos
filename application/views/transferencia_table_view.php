<script>	
	$("#selecionarTodos").click(function(){				
		check = $("input[name='chek_tra']");		
		vlrapr = $('input[rel^="valor"]'); 
				
        if (check.prop("checked")) {  
            $(':checkbox').prop('checked', '');
            for (var i = 0; i < check.length; i++) {                                
            	vlrapr[i].value = '';
            	check[i].value = check[i].value;            	           
            }                               
        } else {  
            $(':checkbox').prop('checked', 'checked');			                      
            for (var i = 0; i < check.length; i++) {                            
            	vlrapr[i].value = check[i].value;            	            	           
            }            
        }        
    }); 

	$(":checkbox").click(function(){
	    var options="";
	    options = $(this).val();
    	id = $(this).attr('id');	        	
    	//classe = $(this).attr('class');
    	
	    if ($('#'+id).prop('checked')) {
	    	$('#'+id).prop('checked', 'checked');
	    	$('#vlr'+id).val(options);	    	
		} else {
			$('#'+id).prop('checked', '');
			$('#vlr'+id).val('');			
		}			   
	});
	
	$(document).ready(function(){
	    $('[data-toggle="tooltip"]').tooltip(); 

	    $('input[id^="pri"]').bootstrapSwitch();

	    $('#data_nova').datepicker({
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
	function jTransData(){    		
		//antes de abrir a janela, preciso carregar os dados do cliente e preencher os campos dentro do modal
		check = new Array();
		$("input[type=checkbox][name='chek_tra']:checked").each(function(){		
			check.push($(this).val());		
		});
			
		if (check.length == 0) {
			bootbox.alert({
				  size: 'small',
			      "message": '<h5 class="alert alert-danger"><i class="fa fa-exclamation-triangle"></i> Selecione ao menos um pedido!</h5>',
			      "className" : "alert-modal"			          
			  });
		} else {
			bootbox.dialog({
			    title: 'Confirma Manutenções',
			    message:'<div class="container-fluid">  ' + 
				    	'<div class="row-fluid">  ' +
	            			'<div class="col-md-12">' +
	            			'<form class="form-horizontal" id="form_data"> ' +
	            				'<div class="form-group">' +
	        			    		'<label for="data_nova" class="control-label col-sm-3">Nova Data</label>' +
	        			    		'<div class="col-sm-4">' +
	        			    			'<input type="text" class="form-control datepicker" id="data_nova" name="data_nova">' +
	        			    		'</div>' +
	        			  		'</div>' +			  			  
	        			  		'<div class="form-group">' +
	        			    		'<label for="id_adt" class="control-label col-sm-3">Pedidos:</label>' +
	        			    		'<div class="col-sm-9">' +        			    				    	
	        			    			'<div id="vpedidos" class="alert alert-success">'+ check.length +' pedidos selecionados</div>' +	        			    			
	        			    		'</div>' +
	        			    	'</div>' +
	        			    	'</form>' +
	        			    '</div>' +
	        			    '</div>' +
	        			  '</div>',
	        	buttons:{
	        			cancel: {
	        			   label: 'Cancel',
	        			   className: 'btn-danger'
	        			},
	        			success: {
				        		'label'     : 'Confirma',
				        		'className' : "btn-success",
				        		'callback'  : function(){				        			 				        			  	        	
				        		      dataString = $("#form_tranf, #form_data").serialize();
				        		      $.blockUI({ 
				        		    	  message: '<h5><i class="fa fa-refresh fa-spin"></i>  Processando Manutenções!<br>Aguarde....</h5>',
					        		      css: { 
				        		            border: 'none', 
				        		            padding: '15px', 
				        		            backgroundColor: '#000', 
				        		            '-webkit-border-radius': '10px', 
				        		            '-moz-border-radius': '10px', 
				        		            opacity: .5, 
				        		            color: '#fff' 
				        		        } });					        		      				        		      			        		      			        		   
				        		      $.ajax({
				        		        type: "POST",
				        		        url: "<?php echo base_url();?>index.php/transferencia/alterar",
				        		        data: dataString,
				        		        success: function(data){
				        		        	$.unblockUI();			        		        					        		        	 					        		        	       
				        	            	$('#retorno').html(data);			        	            				        	            		        	            	      			        		            			        		     
				        		            $('#mRetorno').modal('show');
				        		            $('#mRetorno').on('hidden.bs.modal', function() {
				        		            	window.location="<?php echo base_url();?>index.php/transferencia"; 				           		            	
				        		            });          
				        		        }
				        		      }); 
				        		}
					    }
				    }
				});
				$(".datepicker").datepicker({
		            format: "dd/mm/yyyy",
		            language: "pt-BR",
		            orientation: "top left",
		            calendarWeeks: true,
		            autoclose: true,
		            todayHighlight: true
		            
		        });	   							    		      
			//$("#id_adt").val(check);
			//document.getElementById('vpedidos').innerHTML= check.length+ ' itens selecionados.';;					   
			//$('#mTransData').modal('show');
		}
	}
	
	function jExcluiData(){    		
		//antes de abrir a janela, preciso carregar os dados do cliente e preencher os campos dentro do modal
		check = new Array();
		$("input[type=checkbox][name='chek_tra']:checked").each(function(){
			check.push($(this).val());
		});
		if (check.length == 0) {
			bootbox.alert({
				  size: 'small',
			      "message": '<h5 class="alert alert-danger"><i class="fa fa-exclamation-triangle"></i> Selecione ao menos um pedido!</h5>',
			      "className" : "alert-modal"			          
			  });
		} else {
			//$("#id_exc").val(check);
			//document.getElementById('epedidos').innerHTML= check.length+ ' itens selecionados.';;					   
			//$('#mExcluiData').modal('show');

			bootbox.dialog({
			    title: 'Confirma Exclusão',
			    message:'<div class="container-fluid">  ' + 
				    	'<div class="row-fluid">  ' +
	            			'<div class="col-md-12">' +	            					  			 
	        			  		'<div class="form-group">' +
	        			    		'<label for="id_adt" class="control-label col-sm-3">Pedidos:</label>' +
	        			    		'<div class="col-sm-9">' +        			    				    	
	        			    			'<div id="vpedidos" class="alert alert-danger">'+ check.length +' pedidos selecionados</div>' +	        			    			
	        			    		'</div>' +
	        			    	'</div>' +	        			    
	        			    '</div>' +
	        			    '</div>' +
	        			  '</div>',
	        	buttons:{
	        			cancel: {
	        			   label: 'Cancel',
	        			   className: 'btn-danger'
	        			},
	        			success: {
				        		'label'     : 'Confirma',
				        		'className' : "btn-success",
				        		'callback'  : function(){				        			 				        			  	        	
				        		      dataString = $("#form_tranf").serialize();
				        		      $.blockUI({ 
				        		    	  message: '<h5><i class="fa fa-refresh fa-spin"></i>  Processando Exclusões!<br>Aguarde....</h5>',
					        		      css: { 
				        		            border: 'none', 
				        		            padding: '15px', 
				        		            backgroundColor: '#000', 
				        		            '-webkit-border-radius': '10px', 
				        		            '-moz-border-radius': '10px', 
				        		            opacity: .5, 
				        		            color: '#fff' 
				        		        } });					        		      				        		      			        		      			        		   
				        		      $.ajax({
				        		        type: "POST",
				        		        url: "<?php echo base_url();?>index.php/transferencia/excluir",
				        		        data: dataString,
				        		        success: function(data){
				        		        	$.unblockUI();			        		        					        		        	 					        		        	       
				        	            	$('#retorno').html(data);			        	            				        	            		        	            	      			        		            			        		     
				        		            $('#mRetorno').modal('show');
				        		            $('#mRetorno').on('hidden.bs.modal', function() {
				        		            	window.location="<?php echo base_url();?>index.php/transferencia"; 				           		            	
				        		            });          
				        		        }
				        		      }); 
				        		}
					    }
				    }
				});
		}
	}
	
</script>
<script type="text/javascript">
	function jVeItem(emp , filial, oc){    		
		//antes de abrir a janela, preciso carregar os dados do cliente e preencher os campos dentro do modal
		carregaDadosItemPed(emp , filial, oc);    		
		$('#mVeItem').modal('show');
	}

	function carregaDadosItemPed(emp , filial, oc){
		$.ajax({  
            type: "POST",  
            url : '<?php echo base_url(); ?>index.php/consulta/busca_peditem/',
            data: {emp: emp, filial:filial, oc:oc },              
            success: function(data){  
            	$('#tbitem').html(data); 
            }  
        }); 
	}

	function jVeOC(codfor) {
		$('#tbioc').html('');
		carregaDadosOc(codfor);    		
		$('#mVeOc').modal('show');
	}

	function carregaDadosOc(codfor){
		loading_show('load_oc'); 
		$.ajax({  
            type: "POST",  
            url : '<?php echo base_url(); ?>index.php/consulta/busca_adtfor/',
            data: {codfor: codfor },              
            success: function(data){
            	loading_hide('load_oc');   
            	$('#tbioc').html(data); 
            }  
        }); 
	}
	
	function excel() {
	    $("#pedtransf").table2excel({
	        exclude: ".noExl",
	        name: "Planilha de Pedidos Trasnferencia",
	        filename: "Lista_Pedidos_Transferencia"
	    });
	};

</script>
<div class="col-md-12">
<?php 
	$this->load->helper(array('form'));			
	$attributes = array('id' => 'form_tranf', 'name' => 'form_aprova', 'class' => 'form-horizontal'); 
	echo form_open('transferencia/alterar', $attributes); 
?>
		<div class="panel panel-custom">
				<div class="panel-heading">
			    	<div class="container-fluid">
						<div class="row">
							<div class="col-md-3">	
								<h4><i class="fa fa-list"></i> Resultado Adiantamentos</h4>
							</div>										    
			    			<div class="offset1 span2 pull-right">
			    				<?php echo $exporta; ?>
			    			</div>		
			    		</div>
			    	</div>	  
				</div>
				<div class="panel-body">
					<?php if ($resultado) { ?>
					<table id="pedtransf" class="table table-condensed table-hover small">
						<thead>
							<tr class="cabecalho">
								<th class="text-center"><a href="javascript:;" class="btn-sm btn-info" id="selecionarTodos"><i class="fa fa-check-square-o fa-lg"></i></a></th>										    								    
							    <th class="text-center">Pedido</th>
							    <th class="text-center">Empresa</th>
							    <th class="text-center">Filial</th>
							    <th class="text-center">Data</th>
							    <th class="text-center">Fornecedor</th>
							    <th class="text-center">Comprador</th>
							    <th class="text-center">Vlr Ped</th>
							    <th class="text-center">Vlr Adt</th>							    							    							    							    							    							
							    <th class="text-center">Aplic/Obs</th>
							    <th class="text-center">Prioridade</th>
							    <th class="text-center">Obs</th>
							    <th class="text-center">Área</th>
							    <th class="text-center">Receb.</th>
							    <th class="text-center">Vlr Abe Fornec</th>			    		    	
						    </tr>
					    </thead>
					    <tbody>		    		    
			   <?php 
			   		$this->load->helper(array('form'));
			   
			   		$tot_adtr = 0;
				   	//$tot_aprr = 0;
				   	$tot_ped = 0;
				   	//$tot_pg = 0;
				   	$tot_fil = 0;
				   	$sub_fil_adt = 0;
				   	//$sub_fil_apr = 0;
				   	$cont = 0;
					$sub_fil_ped = 0;
					//$sub_fil_pg = 0;
				   	$ped_atual = 0;
					
			    	foreach ($resultado as $row) { 			    		
			    					    					    				    	
			    		if ($row->RECB == 'LQ') {
			    			$recb = '<span class="label label-success">Liquidado</span>';
			    		} elseif ($row->RECB == 'AP') {
			    			$recb = '<span class="label label-warning">Aberto Parcial</span>';
			    		} elseif ($row->RECB == 'AT'){
			    			$recb = '<span class="label label-danger">Aberto Total</span>';
			    		} else {
			    			$recb = '';
			    		}
			    		
			    		if($row->USU_ADTPRI == '1') {
			    			$ped = '<h4><span class="label label-custom">'.$row->USU_NUMOCP.'</span></h4>';
			    			$pri = '<input type="checkbox" id="pri[]" name="vlrapr['.$row->USU_CODEMP.'-'.$row->USU_NUMOCP.'-'.$row->USU_FILIAL.'-'.$row->USU_ID.'][pri]" value="1" data-size="mini" data-on-color="danger" data-off-color="success" data-on-text="Sim" data-off-text="Não" checked>'; 
			    		} else {
			    			$ped = $row->USU_NUMOCP;
			    			$pri = '<input type="checkbox" id="pri[]" name="vlrapr['.$row->USU_CODEMP.'-'.$row->USU_NUMOCP.'-'.$row->USU_FILIAL.'-'.$row->USU_ID.'][pri]" value="1" data-size="mini" data-on-color="danger" data-off-color="success" data-on-text="Sim" data-off-text="Não" >'; 
			    		}
			    		
			    		if ($row->VLRABE == NULL) {
			    			$vlrabe = '-';
			    		} else {
			    			$vlrabe = '<a href="javascript:;" onclick="jVeOC('.$row->CODFOR.')"><h5><span class="label label-success">'.number_format(str_replace("," , "." , $row->VLRABE), 2, ',', '.').'</span></h5></a>';
			    		}
			    					    		
			    		if($row->USU_FILIAL != $tot_fil) {
			    			if ($cont > 0) {
								
			    				
			    ?>					    	
						    <tr class="total">
						    	<td colspan="5"><strong>Total Unidade:</strong></td>
						    	<td colspan="1" class="text-right"><strong><?php echo number_format(str_replace("," , "." , $sub_fil_ped), 2, ',', '.')  ?></strong></td>
						    	<td colspan="1" class="text-right"><strong><?php echo number_format(str_replace("," , "." , $sub_fil_adt), 2, ',', '.')  ?></strong></td>						    							    
						    	<td colspan="9"></td>
						    </tr>
				<?php 
								$sub_fil_adt = 0;
								//$sub_fil_apr = 0;
								$sub_fil_ped = 0;
								//$sub_fil_pg = 0;
			    			} 
			    ?>	
					    	<tr class="info">
					    		<td colspan="15"><strong>Unidade: <?php echo  $row->USU_FILIAL.' - '.$row->SIGFIL.' '.$row->USU_INSTAN ?></strong></td>
					    	</tr>
				<?php
			    		}  
			    ?>
							<tr>
								<td><input 	type="checkbox" 
											class="<?php echo $row->USU_FILIAL; ?>" id="<?php echo $row->USU_CODEMP.$row->USU_FILIAL.$row->USU_NUMOCP.$row->USU_ID ?>" 
											name="chek_tra" 
											value="<?php echo number_format(str_replace("," , "." , $row->USU_VLRADT), 2, ',', '') ?>" 
											rel="<?php echo number_format(str_replace("," , "." , $row->USU_VLRADT), 2, '.', '') ?>" 
											title="<?php echo $row->USU_CODEMP.$row->USU_FILIAL.$row->USU_NUMOCP.$row->USU_ID ?>" /></td>								
								<td class="text-center"><a href="javascript:;" onclick="jVeItem(<?php echo $row->USU_CODEMP ?>,<?php echo $row->USU_FILIAL ?>,<?php echo $row->USU_NUMOCP ?>)"><?php echo $ped ?></a></td>																					
								<td class="text-center"><?php echo $row->USU_CODEMP ?></td>
								<td class="text-center"><?php echo $row->USU_FILIAL ?></td>
								<td class="text-center"><?php echo $row->USU_DTLANC ?></td>
								<td class="text-left"><?php echo $row->APEFOR ?></td>
								<td class="text-left"><?php echo $row->NOMUSU ?></td>								
								<td class="text-right" width="100"><?php echo number_format(str_replace("," , "." , $row->VLRLIQ), 2, ',', '.') ?></td>
								<td class="text-right" width="100"><input type="text" rel="valor[]" class="<?php echo $row->USU_FILIAL; ?> form-control" style="text-align:right"  id="vlr<?php echo $row->USU_CODEMP.$row->USU_FILIAL.$row->USU_NUMOCP.$row->USU_ID; ?>" name="vlrapr[<?php echo $row->USU_CODEMP.'-'.$row->USU_NUMOCP.'-'.$row->USU_FILIAL.'-'.$row->USU_ID; ?>][valor]" placeholder="<?php echo number_format(str_replace("," , "." , $row->USU_VLRADT), 2, ',', '.') ?>"/></td>																																																												
								<td class="text-left"><a href="#" data-toggle="tooltip" data-placement="left" title="<?php echo $row->OBSOCP ?>"><?php echo substr_replace($row->OBSOCP, '...', 30); ?></a></td>
								<td class="text-center"><?php echo $pri ?></td>
								<td class="text-left" width="200"><textarea name="vlrapr[<?php echo $row->USU_CODEMP.'-'.$row->USU_NUMOCP.'-'.$row->USU_FILIAL.'-'.$row->USU_ID; ?>][obs]" id="obs" class="form-control" rows="2" ><?php echo utf8_decode($row->USU_OBS) ?></textarea></td>
								<td class="text-center small"><?php echo $row->USU_DESCAREA ?></td>
								<td class="text-center"><?php echo $recb ?></td>
								<td class="text-right"><?php echo $vlrabe ?></td>
							</tr>						
															
						
				<?php 								
						$tot_fil = $row->USU_FILIAL;																						
						$tot_adtr += str_replace("," , "." , $row->USU_VLRADT);
						//$tot_aprr += str_replace("," , "." , $row->VLR_APR);
						//$tot_pg += str_replace("," , "." , $row->VLR_PAGO);
						$cont++;
						$sub_fil_adt += str_replace("," , "." , $row->USU_VLRADT);
						//$sub_fil_apr += str_replace("," , "." , $row->VLR_APR);
						//$sub_fil_pg += str_replace("," , "." , $row->VLR_PAGO);
						if($row->USU_NUMOCP !== $ped_atual) {
							$sub_fil_ped += str_replace("," , "." , $row->VLRLIQ);
							$tot_ped += str_replace("," , "." , $row->VLRLIQ);
						} else {
							$sub_fil_ped = $sub_fil_ped;
							$tot_ped = $tot_ped;
						}
						$ped_atual = $row->USU_NUMOCP;
					}
				?>								
						</tbody>
						<tfoot>
							<tr class="total">
						    	<td colspan="5"><strong>Total Unidade:</strong></td>
						    	<td colspan="1" class="text-right"><strong><?php echo number_format(str_replace("," , "." , $sub_fil_ped), 2, ',', '.')  ?></strong></td>
						    	<td colspan="1" class="text-right"><strong><?php echo number_format(str_replace("," , "." , $sub_fil_adt), 2, ',', '.')  ?></strong></td>						    							    	
						    	<td colspan="8"></td>
							</tr>
							<tr class="total">
								<td colspan="5"><strong>Total Geral</strong></td>
								<td colspan="1" class="text-right"><strong>R$ <?php echo number_format(str_replace("," , "." , $tot_ped), 2, ',', '.') ?></strong></td>
								<td colspan="1" class="text-right"><strong>R$ <?php echo number_format(str_replace("," , "." , $tot_adtr), 2, ',', '.') ?></strong></td>								
								<td colspan="8"></td>
							</tr>	
				    	</tfoot>
							<?php 
					    } else {
					    	echo '<div class="alert alert-warning"><i class="fa fa-exclamation-circle"></i> NÃ£o existem dados para exibir. Verifique se o dia estÃ¡ ABERTO ou se existem adiantamentos NÃƒO APROVADOS!</div>';
					    }
					?>				
				</table>
			</div>
			<div class="panel-footer">
		    	<div class="pull-right">	
		    		<input type="hidden" name="cd_user" id="cd_user" value="<?php echo $user_codigo; ?>">	    		
		    		<button type="button" onclick="jTransData()" name="transdata" id="transdata" class="btn btn-success"><i class="fa fa-check"></i> Transferir</button>
		    		<button type="button" name="excldata" id="excldata" class="btn btn-danger" onclick="jExcluiData()" ><i class="fa fa-trash"></i> Excluir</button>    		
		    	</div>
		    	<div class="clearfix"></div>
		    </div>
		    <?php echo form_close();?> 			
		</div>
	</div>
		
<!-- ------------------------------------------------------VE PEDIDO ----------------------------------------------------------------------------------------------------- -->	
	<div class="modal fade" id="mVeOc" >
		<div class="modal-dialog modal-custom">
	    	<div class="modal-content">
	      		<div class="modal-header modal-header-custom">
	        		<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Fechar</span></button>
	        		<h4 class="modal-title">Pedidos Fornecedor</h4>
	      		</div>
	      		<div class="modal-body">
	      			<div id="load_oc" class="col-sm-2"></div>
	      	 		<div id="tbioc"></div>
	      	 		<div class="clearfix"></div>		    			   
	      		</div>
	      		<div class="modal-footer">	           
	      		</div>	      	
	    	</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal --> 
	<!-- ------------------------------------------------------VE ITEM PEDIDO ----------------------------------------------------------------------------------------------------- -->	
	<div class="modal fade" id="mVeItem" >
		<div class="modal-dialog modal-lg">
	    	<div class="modal-content">
	      		<div class="modal-header modal-header-custom">
	        		<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Fechar</span></button>
	        		<h4 class="modal-title">Itens do Pedido</h4>
	      		</div>
	      		<div class="modal-body">
	      	 		<div id="tbitem"></div>		    			   
	      		</div>
	      		<div class="modal-footer">	           
	      		</div>	      	
	    	</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal --> 