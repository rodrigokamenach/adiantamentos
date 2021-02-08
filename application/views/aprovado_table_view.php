<script>
	$(document).ready(function(){
	    $('[data-toggle="tooltip"]').tooltip();   	    		       	 	     
	});	

	//MARCA CHEKS INDIVIDUAIS E PREENCHE O INPUT COM O VALOR
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

	$("input[type=checkbox]").change(function() {
		classe = $(this).attr('class');			
        recalculate(classe);
        recalculateTotal();
    });

	$('input[name^="vlrapr"]').keyup(function() {
		classe = $(this).attr('class').replace(/\D/g,'');					
        recalculateInput(classe);
        recalculateTotalInput();
    });

	//MARCA TODOS OS CHECKS E PREENCHE TODOS OS INPUTS
	$("#selecionarTodos").click(function(){
		check = $("input[name='chek_apr']");		
		vlrapr = $('input[name^="vlrapr"]'); 
				
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

        check.map(function() {
        	recalculate($(this).attr('class'));
        	recalculateTotal();
        })
    }); 
    

	function recalculate(classe) {
		//alert(varsum);				
		sum = 0;
		$('input.'+classe+'[type=checkbox]:checked').map(function() {
        	sum += parseFloat($(this).attr("rel"));
        });
        $('#'+classe).html(CurrencyFormat(sum.toFixed(2)));
    }

	function recalculateTotal() {
		//alert(varsum);				
		sum = 0;
		$('input[type=checkbox]:checked').map(function() {
        	sum += parseFloat($(this).attr("rel"));
        });
        $('#total').html(CurrencyFormat(sum.toFixed(2)));
    }

	function recalculateInput(classe) {				
		sum = 0.00;		
		$('input.'+classe+'[name^="vlrapr"]').map(function() {							
				var valor = $(this).val();
				if (valor == 'undefined' || valor == '') {
					valor = 0.00;
				}	
				valor = String(valor);
				valor = valor.replace(/,/g, '.');			
        		sum += parseFloat(valor);        				
        });
        $('#'+classe).html(CurrencyFormat(sum.toFixed(2)));
    }

	function recalculateTotalInput() {				
		sum = 0.00;		
		$('input[name^="vlrapr"]').map(function() {							
				var valor = $(this).val();
				if (valor == 'undefined' || valor == '') {
					valor = 0.00;
				}	
				valor = String(valor);
				valor = valor.replace(/,/g, '.');			
        		sum += parseFloat(valor);        				
        });
        $('#total').html(CurrencyFormat(sum.toFixed(2)));
    }
	
	function CurrencyFormat(number)
	{
	   var decimalplaces = 2;
	   var decimalcharacter = ",";
	   var thousandseparater = ".";
	   number = parseFloat(number);
	   var sign = number < 0 ? "-" : "";
	   var formatted = new String(number.toFixed(decimalplaces));
	   if( decimalcharacter.length && decimalcharacter != "." ) { formatted = formatted.replace(/\./,decimalcharacter); }
	   var integer = "";
	   var fraction = "";
	   var strnumber = new String(formatted);
	   var dotpos = decimalcharacter.length ? strnumber.indexOf(decimalcharacter) : -1;
	   if( dotpos > -1 )
	   {
	      if( dotpos ) { integer = strnumber.substr(0,dotpos); }
	      fraction = strnumber.substr(dotpos+1);
	   }
	   else { integer = strnumber; }
	   if( integer ) { integer = String(Math.abs(integer)); }
	   while( fraction.length < decimalplaces ) { fraction += "0"; }
	   temparray = new Array();
	   while( integer.length > 3 )
	   {
	      temparray.unshift(integer.substr(-3));
	      integer = integer.substr(0,integer.length-3);
	   }
	   temparray.unshift(integer);
	   integer = temparray.join(thousandseparater);
	   return sign + integer + decimalcharacter + fraction;
	}	    

	function jAprovaAdt() {    		
		//antes de abrir a janela, preciso carregar os dados do cliente e preencher os campos dentro do modal
		check = new Array();
		$("input[type=checkbox][name='chek_apr']:checked").each(function(){
			check.push($(this).val());			
		});

		var soma = 0;
		$('input[name^="vlrapr"]').map(function() {							
			var valor = $(this).val();
			if (valor == 'undefined' || valor == '') {
				valor = 0.00;
			}	
			valor = String(valor);
			valor = valor.replace(/,/g, '.');			
    		soma += parseFloat(valor);        				
    	});    	

		soma = soma.toFixed(2);
		soma = CurrencyFormat(soma);
		
		if (check.length == 0) {
			bootbox.alert({
				  size: 'small',
			      "message": '<h5 class="alert alert-danger"><i class="fa fa-exclamation-triangle"></i> Selecione ao menos um pedido!</h5>',
			      "className" : "alert-modal"			          
			  });
		} else {
			bootbox.dialog({
			    title: 'Confirma Aprovação',
			    message: '<div class="row">  ' +
                			'<div class="col-md-12">' +
							'<h3 class="alert alert-danger">' +	
    						'Valor Total: <strong>R$ ' +soma + '</strong></h3>' + 
    						'<h3 class="alert alert-warning">' +	  						  						    					
                			'Quantidade de Pedidos: <strong>' + check.length + '</strong>' +
                			'<h3></div>' +
                			'</div> </div>',
			    buttons:{
			    	cancel: {
			            label: 'Cancel',
			            className: 'btn-danger'			            		            			           
			        },
				    success: {
			        		'label'     : 'Confirma',
			        		'className' : "btn-success",
			        		'callback'  : function(){			        		
			        		      dataString = $("#form_aprova").serialize();
			        		      $.blockUI({ 
			        		    	  message: '<h5><i class="fa fa-refresh fa-spin"></i>  Processando Aprovações!<br>Aguarde....</h5>',
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
			        		        url: "<?php echo base_url();?>index.php/aprovado/aprovar",
			        		        data: dataString,
			        		        success: function(data){			        		        	
			        		        	$.unblockUI(); 					        		        	        
			        	            	$('#retorno').html(data);			        	            				        	            		        	            	      			        		            			        		     
			        		            $('#mRetorno').modal('show');
			        		            $('#mRetorno').on('hidden.bs.modal', function() {
			        		            	window.location="<?php echo base_url();?>index.php/aprovado"; 
			        		            });          
			        		        }
			        		      }); 
			        		}
				    }
			    }                
			});
			//$("#pedidos").val(check);
			//document.getElementById('vpedidos').innerHTML= check.length+ ' itens selecionados.';;					   
			//$('#mLancaColetaAgrup').modal('show');
		}
	}

	function jDesAdt() {
		check = new Array();
		$("input[type=checkbox][name^='chek_apr']:checked").each(function(){
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
	       	var dataString = $("#form_aprova").serialize();
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
	        	url: "<?php echo base_url();?>index.php/aprovado/desaprova",
	        	data: dataString,
	        	success: function(data){			        		        	
	        		$.unblockUI(); 					        		        	        
	        	    $('#retorno').html(data);			        	            				        	            		        	            	      			        		            			        		     
	        		$('#mRetorno').modal('show');
	        		$('#mRetorno').on('hidden.bs.modal', function() {
	        			window.location="<?php echo base_url();?>index.php/aprovado"; 
	        		});          
	        	}
	        });
		}         				 
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

	function jVeItem(emp, filial, oc){    		
		//antes de abrir a janela, preciso carregar os dados do cliente e preencher os campos dentro do modal
		carregaDadosItemPed(emp, filial, oc);    		
		$('#mVeItem').modal('show');
	}	

	function carregaDadosItemPed(emp, filial, oc){
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
	

</script>
<div class="col-md-12">
<?php 
			$this->load->helper(array('form'));			
			$attributes = array('id' => 'form_aprova', 'name' => 'form_aprova', 'class' => 'form-inline');
			echo form_open('aprovado/aprovar', $attributes);
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
							    <th class="text-center">Pedido</th>
							    <th class="text-center">Data</th>
							    <th class="text-center">Fornecedor</th>
							    <th class="text-center">Comprador</th>
							    <th class="text-center">Vlr Ped</th>
							    <th class="text-center">Vlr Adt</th>
							    <th class="text-center" colspan="2">Aprovar</th>
							    <th class="text-center">Ger Apr</th>							    							    							    							    							    							
							    <th class="text-center">Aplic/Obs</th>
							    <th class="text-center">Obs</th>
							    <th class="text-center">Área</th>
							    <th class="text-center">Vlr Abe Fornec</th>		    		    	
						    </tr>
					    </thead>
					    <tbody>		    		    
			   <?php 			   					  
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
			    					    					    			    	
			    		if($row->USU_ADTPRI == 1) {
			    			$ped = '<h4><span class="label label-custom">'.$row->USU_NUMOCP.'</span></h4>'; 
			    		} else {
			    			$ped = $row->USU_NUMOCP;
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
						    	<td colspan="2" class="text-right"><strong><div id="<?php echo $tot_fil ?>"></div></strong></td>				    							    
						    	<td colspan="7"></td>
						    </tr>
				<?php 
								$sub_fil_adt = 0;
								//$sub_fil_apr = 0;
								$sub_fil_ped = 0;
								//$sub_fil_pg = 0;
			    			} 
			    ?>	
					    	<tr class="info">
					    		<td colspan="15"><strong>Unidade: <?php echo  $row->USU_FILIAL.' - '.$row->SIGFIL.' - '.$row->USU_INSTAN ?></strong></td>
					    	</tr>
				<?php
			    		}  
			    ?>
							<tr>
								<td><input 	type="checkbox" 
											class="<?php echo $row->USU_FILIAL; ?>" id="<?php echo $row->USU_CODEMP.$row->USU_FILIAL.$row->USU_NUMOCP.$row->USU_ID ?>" 
											name="chek_apr" 
											value="<?php echo number_format(str_replace("," , "." , $row->USU_VLRADT), 2, ',', '') ?>" 
											rel="<?php echo number_format(str_replace("," , "." , $row->USU_VLRADT), 2, '.', '') ?>" 
											title="<?php echo $row->USU_CODEMP.$row->USU_FILIAL.$row->USU_NUMOCP.$row->USU_ID ?>" />
								</td>								
								<td class="text-center"><a href="javascript:;" onclick="jVeItem(<?php echo $row->USU_CODEMP ?>,<?php echo $row->USU_FILIAL ?>,<?php echo $row->USU_NUMOCP ?>)"><?php echo $ped ?></a></td>													
								<td class="text-center"><?php echo $row->USU_DTLANC ?></td>	
								<td class="text-left"><?php echo $row->APEFOR .' - '.$row->SIGUFS?></td>
								<td class="text-left"><?php echo $row->USULANC ?></td>								
								<td class="text-right" width="100"><?php echo number_format(str_replace("," , "." , $row->VLRLIQ), 2, ',', '.') ?></td>
								<td class="text-right" width="100"><?php echo number_format(str_replace("," , "." , $row->USU_VLRADT), 2, ',', '.') ?></td>
								<td></td>
								<td width="100"><input type="text" class="<?php echo $row->USU_FILIAL; ?> form-control" style="text-align:right"  id="vlr<?php echo $row->USU_CODEMP.$row->USU_FILIAL.$row->USU_NUMOCP.$row->USU_ID; ?>" name="vlrapr[<?php echo $row->USU_CODEMP.$row->USU_FILIAL.$row->USU_NUMOCP.$row->USU_ID; ?>]" /></td>																																																												
								<td class="text-center small"><?php echo $row->USUAPR; ?></td>
								<td class="text-left small"><a href="#" data-toggle="tooltip" data-placement="left" title="<?php echo $row->OBSOCP ?>"><?php echo substr_replace($row->OBSOCP, '...', 30); ?></a></td>
								<td class="text-left small"><?php echo utf8_decode($row->USU_OBS) ?></td>
								<td class="text-center small"><?php echo $row->USU_DESCAREA ?></td>
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
						    	<td colspan="2" class="text-right"><strong><div id="<?php echo $row->USU_FILIAL ?>"></div></strong></td>							    							    	
						    	<td colspan="7"></td>
							</tr>
							<tr class="total">
								<td colspan="5"><strong>Total Geral</strong></td>
								<td colspan="1" class="text-right"><strong>R$ <?php echo number_format(str_replace("," , "." , $tot_ped), 2, ',', '.') ?></strong></td>
								<td colspan="1" class="text-right"><strong>R$ <?php echo number_format(str_replace("," , "." , $tot_adtr), 2, ',', '.') ?></strong></td>
								<td colspan="2" class="text-right"><strong><div id="total"></div></strong></td>							
								<td colspan="5"></td>
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
		    		<?php
		    			if($acao == 'APR') {
		    		?>
		    			<button type="button" onclick="jAprovaAdt()" name="transdata" id="transdata" class="btn btn-success"><i class="fa fa-check"></i> Aprovar</button>
		    		<?php 
		    			} 
		    			if ($acao == 'DES') {
		    		?>
		    			<button type="button" onclick="jDesAdt()" name="transdata" id="transdata" class="btn btn-danger"><i class="fa fa-ban"></i> Desaprovar</button>
		    		<?php }?>    		
		    	</div>		    	
		    	<div class="clearfix"></div>
		    </div> 
		    <?php echo form_close(); ?>	
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
	