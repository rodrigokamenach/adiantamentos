<script>
	$(document).ready(function(){
	    $('[data-toggle="tooltip"]').tooltip();   	    		       	 	     
	});	

	//MARCA TODOS OS CHECKS E PREENCHE TODOS OS INPUTS
	$("#selecionarTodos").click(function(){
		check = $("input[name^='chek_ger']");		
						
        if (check.prop("checked")) {  
            $(':checkbox').prop('checked', '');                                          
        } else {  
            $(':checkbox').prop('checked', 'checked');			                                            
        }        
    }); 	

	function jGeraAP() { 
		check = new Array();
		$("input[type=checkbox][name^='chek_ger']:checked").each(function(){
			check.push($(this).val());			
		});

		if (check.length == 0) {
			bootbox.alert({
				  size: 'small',
			      "message": '<h5 class="alert alert-danger"><i class="fa fa-exclamation-triangle"></i> Selecione ao menos um pedido!</h5>',
			      "className" : "alert-modal"			          
			  });
		} else {		   		
		//antes de abrir a janela, preciso carregar os dados do cliente e preencher os campos dentro do modal									        
	       	var dataString = $("#form_gera").serializeArray();
	       	//alert(dataString);       	
	        $.blockUI({ 
	        	message: '<h5><i class="fa fa-refresh fa-spin"></i> Gerando Titulos!<br>Aguarde....</h5>',
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
	        	url: "<?php echo base_url();?>index.php/gerar/gera_ap",
	        	data: dataString,
	        	success: function(data){			        		        	
	        		$.unblockUI(); 					        		        	        
	        	    $('#retorno').html(data);			        	            				        	            		        	            	      			        		            			        		     
	        		$('#mRetorno').modal('show');
	        		$('#mRetorno').on('hidden.bs.modal', function() {
	        			window.location="<?php echo base_url();?>index.php/gerar"; 
	        		});          
	        	}
	        }); 
		}        				 
	}

	function jDesAdt() {
		check = new Array();
		$("input[type=checkbox][name^='chek_ger']:checked").each(function(){
			check.push($(this).val());			
		});

		if (check.length == 0) {
			bootbox.alert({
				  size: 'small',
			      "message": '<h5 class="alert alert-danger"><i class="fa fa-exclamation-triangle"></i> Selecione ao menos um pedido!</h5>',
			      "className" : "alert-modal"			          
			  });
		} else {    		
		//antes de abrir a janela, preciso carregar os dados do cliente e preencher os campos dentro do modal									        
	       	var dataString = $("#form_gera").serializeArray();
	       	//alert(dataString);       	
	        $.blockUI({ 
	        	message: '<h5><i class="fa fa-refresh fa-spin"></i> Desaprovando Pedidos!<br>Aguarde....</h5>',
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
	        	url: "<?php echo base_url();?>index.php/gerar/desaprova",
	        	data: dataString,
	        	success: function(data){			        		        	
	        		$.unblockUI(); 					        		        	        
	        	    $('#retorno').html(data);			        	            				        	            		        	            	      			        		            			        		     
	        		$('#mRetorno').modal('show');
	        		$('#mRetorno').on('hidden.bs.modal', function() {
	        			window.location="<?php echo base_url();?>index.php/gerar"; 
	        		});          
	        	}
	        });
		}         				 
	}
</script>
<script type="text/javascript">
	function jVeItem(emp, filial, oc){    		
		//antes de abrir a janela, preciso carregar os dados do cliente e preencher os campos dentro do modal
		carregaDadosItemPed(emp, filial, oc);    		
		$('#mVeItem').modal('show');
	}

	function carregaDadosItemPed(emp, filial, oc){
		$.ajax({  
            type: "POST",  
            url : '<?php echo base_url(); ?>index.php/consulta/busca_peditem/',
            data: {emp:emp, filial:filial, oc:oc },              
            success: function(data){  
            	$('#tbitem').html(data); 
            }  
        }); 
	}

</script>
<div class="col-md-12">
<?php 
			$this->load->helper(array('form'));			
			$attributes = array('id' => 'form_gera', 'name' => 'form_gera', 'class' => 'form-inline');
			echo form_open('gerar/gera_ap', $attributes);
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
					<?php 						
						if ($resultado) { 
					?>
					<table class="table table-condensed table-hover small">
						<thead>
							<tr class="cabecalho">	
								<th class="text-center"><a href="javascript:;" class="btn-sm btn-info" id="selecionarTodos"><i class="fa fa-check-square-o fa-lg"></i></a></th>																	    								   
							    <th class="text-center">Data</th>
							    <th class="text-center">Pedido</th>							    
							    <th class="text-center">Fornecedor</th>
							    <th class="text-center">Comprador</th>
							    <th class="text-center">Vlr Ped</th>
							    <th class="text-center">Vlr Adt</th>
							    <th class="text-center">Vlr Apr</th>
							    <th class="text-center">Ger Apr</th>
							    <th class="text-center">Dir Apr</th>							    							    							    							    							    							
							    <th class="text-center">Aplic/Obs</th>
							    <th class="text-center">Obs</th>
							    <th class="text-center">Área</th>		    		    	
						    </tr>
					    </thead>
					    <tbody>		    		    
			   <?php 			   					  
			   		$tot_adtr = 0;
				   	$tot_aprr = 0;
				   	$tot_ped = 0;
				   	//$tot_pg = 0;
				   	$tot_fil = 0;
				   	$sub_fil_adt = 0;
				   	$sub_fil_apr = 0;
				   	$cont = 0;
					$sub_fil_ped = 0;
					//$sub_fil_pg = 0;
				   	$ped_atual = 0;
					
			    	foreach ($resultado as $row) { 			    					    					    					    		
			    		
			    		if($row->USU_ADTPRI == 1) {
			    			$ped = '<h4><span class="label label-custom">'.$row->USU_NUMOCP.'</span></h4>'; 
			    		} else {
			    			$ped = $row->USU_NUMOCP;
			    		}
			    					    		
			    		if($row->USU_FILIAL != $tot_fil) {
			    			if ($cont > 0) {
								
			    				
			    ?>					    	
						    <tr class="total">
						    	<td colspan="5"><strong>Total Unidade:</strong></td>
						    	<td colspan="1" class="text-right"><strong><?php echo number_format(str_replace("," , "." , $sub_fil_ped), 2, ',', '.')  ?></strong></td>
						    	<td colspan="1" class="text-right"><strong><?php echo number_format(str_replace("," , "." , $sub_fil_adt), 2, ',', '.')  ?></strong></td>
						    	<td colspan="1" class="text-right"><strong><?php echo number_format(str_replace("," , "." , $sub_fil_apr), 2, ',', '.')  ?></strong></td>				    							    
						    	<td colspan="8"></td>
						    </tr>
				<?php 
								$sub_fil_adt = 0;
								$sub_fil_apr = 0;
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
											name="chek_ger[]" 
											value="<?php echo $row->USU_CODEMP.$row->USU_FILIAL.$row->USU_NUMOCP.$row->USU_ID ?>"											
											title="<?php echo $row->USU_CODEMP.$row->USU_FILIAL.$row->USU_NUMOCP.$row->USU_ID ?>" 
									/></td>																												
								<td class="text-center"><?php echo $row->USU_DTLANC ?></td>
								<td class="text-center"><a href="javascript:;" onclick="jVeItem(<?php echo $row->USU_CODEMP ?>,<?php echo $row->USU_FILIAL ?>,<?php echo $row->USU_NUMOCP ?>)"><?php echo $ped ?></a></td>	
								<td class="text-left"><?php echo $row->APEFOR ?></td>
								<td class="text-left"><?php echo $row->USULANC ?></td>								
								<td class="text-right" width="100"><?php echo number_format(str_replace("," , "." , $row->VLRLIQ), 2, ',', '.') ?></td>
								<td class="text-right" width="100"><?php echo number_format(str_replace("," , "." , $row->USU_VLRADT), 2, ',', '.') ?></td>
								<td class="text-right" width="100"><?php echo number_format(str_replace("," , "." , $row->USU_VLRAPR), 2, ',', '.') ?></td>
								<td class="text-center"><?php echo $row->USUAPR ?></td>
								<td class="text-center"><?php echo $row->USUDIR ?></td>																																																																										
								<td class="text-left small"><a href="#" data-toggle="tooltip" data-placement="left" title="<?php echo $row->OBSOCP ?>"><?php echo substr_replace($row->OBSOCP, '...', 30); ?></a></td>
								<td class="text-left small"><?php echo utf8_decode($row->USU_OBS) ?></td>
								<td class="text-center small"><?php echo $row->USU_DESCAREA ?></td>
							</tr>						
															
						
				<?php 								
						$tot_fil = $row->USU_FILIAL;																						
						$tot_adtr += str_replace("," , "." , $row->USU_VLRADT);
						$tot_aprr += str_replace("," , "." , $row->USU_VLRAPR);
						//$tot_pg += str_replace("," , "." , $row->VLR_PAGO);
						$cont++;
						$sub_fil_adt += str_replace("," , "." , $row->USU_VLRADT);
						$sub_fil_apr += str_replace("," , "." , $row->USU_VLRAPR);
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
						    	<td colspan="1" class="text-right"><strong><?php echo number_format(str_replace("," , "." , $sub_fil_apr), 2, ',', '.')  ?></strong></td>							    							    	
						    	<td colspan="8"></td>
							</tr>
							<tr class="total">
								<td colspan="5"><strong>Total Geral</strong></td>
								<td colspan="1" class="text-right"><strong>R$ <?php echo number_format(str_replace("," , "." , $tot_ped), 2, ',', '.') ?></strong></td>
								<td colspan="1" class="text-right"><strong>R$ <?php echo number_format(str_replace("," , "." , $tot_adtr), 2, ',', '.') ?></strong></td>
								<td colspan="1" class="text-right"><strong>R$ <?php echo number_format(str_replace("," , "." , $tot_aprr), 2, ',', '.') ?></strong></td>
								<td colspan="6"></td>
							</tr>	
				    	</tfoot>
							<?php 							
					    } else {
					    	echo '<div class="alert alert-warning"><i class="fa fa-exclamation-circle"></i> Não existem dados para exibir. Verifique se o dia está FECHADO e se existem adiantamentos NÃO APROVADOS!</div>';
					    }
					?>				
				</table>
			</div>
			<div class="panel-footer">
		    	<div class="pull-right">
		    		<input type="hidden" name="cd_user" id="cd_user" value="<?php echo $user_codigo; ?>">		    			    		
		    		<button type="button" onclick="jGeraAP()" name="transdata" id="transdata" class="btn btn-success"><i class="fa fa-check"></i> Gerar APs</button> 
		    		<button type="button" onclick="jDesAdt()" name="transdata" id="transdata" class="btn btn-danger"><i class="fa fa-ban"></i> Desaprovar</button>		    		  	
		    	</div>		    	
		    	<div class="clearfix"></div>
		    </div> 
		    <?php echo form_close(); ?>	
		</div>
	</div>

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
