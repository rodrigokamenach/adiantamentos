<script>
	$(document).ready(function(){
	    $('[data-toggle="tooltip"]').tooltip(); 
	});
	
</script>
<div class="col-lg-12">								
					<?php if ($resultado) { ?>
					<div><h3>Fornecedor : <?php echo $resultado[0]->APEFOR ?></h3></div>					
					<table id="pedconsulta" class="table table-condensed table-hover small">
						<thead>
							<tr class="cabecalho">										    								    							    
							    <th class="text-center">Data</th>
							    <th class="text-center">Pedido</th>							    
							    <th class="text-center">Comprador</th>
							    <th class="text-center">Vlr Ped</th>
							    <th class="text-center">Vlr Adt</th>
							    <th class="text-center">Vlr Apr</th>
							    <th class="text-center">Saldo</th>
							    <th class="text-center">Situação</th>
							    <th class="text-center">Titulo</th>
							    <th class="text-center">Aprovador</th>							    
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
				   	$tot_pg = 0;
				   	$tot_fil = 0;
				   	$sub_fil_adt = 0;
				   	$sub_fil_apr = 0;
				   	$cont = 0;
					$sub_fil_ped = 0;
					$sub_fil_pg = 0;
				   	$ped_atual = 0;
					
			    	foreach ($resultado as $row) { 			    		
			    		if($row->SITUACAO == "Aguar Aprov")  {
			    			$situacao = '<span class="label label-warning">'.$row->SITUACAO.'</span>';
			    		} elseif ($row->SITUACAO == "Aberto") {
			    			$situacao = '<span class="label label-info">'.$row->SITUACAO.'</span>';
			    		} elseif ($row->SITUACAO == "Aberto Parcial") {
			    			$situacao = '<span class="label label-primary">'.$row->SITUACAO.'</span>';
			    		} elseif ($row->SITUACAO == "Pago") {
			    			$situacao = '<span class="label label-danger">'.$row->SITUACAO.'</span>';
			    		} elseif ($row->SITUACAO == "Pago") {
			    			$situacao = '<span class="label label-danger">'.$row->SITUACAO.'</span>';
			    		} elseif ($row->SITUACAO == "Erro OC") {
			    			$situacao = '<span class="label label-danger">'.$row->SITUACAO.'</span>';
			    		}
			    		
			    					    					    					    					    					    
			    		if($row->USU_ADTPRI == 1) {
			    			$ped = '<h4><span class="label label-custom">'.$row->USU_NUMOCP.'</span></h4>'; 
			    		} else {
			    			$ped = $row->USU_NUMOCP;
			    		}
			    		
			    		if (floatval($row->USU_VLRAPR) < floatval($row->USU_VLRADT)) {
			    			$valorapr = '<span class="label label-info">'. number_format(str_replace("," , "." , $row->USU_VLRAPR), 2, ',', '.') .'</span>';
			    		} else {
			    			$valorapr = '<span class="label label-default">'. number_format(str_replace("," , "." , $row->USU_VLRAPR), 2, ',', '.') .'</span>';
			    		}
			    					    		
			    		if($row->USU_FILIAL != $tot_fil) {
			    			if ($cont > 0) {
								
			    				
			    ?>					    	
						    <tr class="total">
						    	<td colspan="3"><strong>Total Unidade:</strong></td>
						    	<td colspan="1" class="text-right"><strong><?php echo number_format(str_replace("," , "." , $sub_fil_ped), 2, ',', '.')  ?></strong></td>
						    	<td colspan="1" class="text-right"><strong><?php echo number_format(str_replace("," , "." , $sub_fil_adt), 2, ',', '.')  ?></strong></td>
						    	<td colspan="1" class="text-right"><strong><?php echo number_format(str_replace("," , "." , $sub_fil_apr), 2, ',', '.') ?></strong></td>
						    	<td colspan="1" class="text-right"><strong><?php echo number_format(str_replace("," , "." , $sub_fil_pg), 2, ',', '.') ?></strong></td>
						    	<td colspan="8"></td>
						    </tr>						    
				<?php 
								$sub_fil_adt = 0;
								$sub_fil_apr = 0;
								$sub_fil_ped = 0;
								$sub_fil_pg = 0;
			    			} 
			    ?>						    	
					    	<tr class="info">					    		
					    		<td colspan="15"><strong>Unidade: <?php echo  $row->USU_FILIAL.' - '.$row->SIGFIL.' '.$row->USU_INSTAN ?></strong></td>
					    	</tr>
				<?php
			    		}  
			    ?>
							<tr>																
								<td class="text-center"><?php echo $row->USU_DTLANC ?></td>
								<td class="text-center"><a href="javascript:;" onclick="jVeItem(<?php echo $row->USU_CODEMP?>,<?php echo $row->USU_FILIAL?>,<?php echo $row->USU_NUMOCP?>)"><?php echo $ped ?></a></td>																				
								<td class="text-left"><?php echo $row->USULANC ?></td>								
								<td class="text-right" width="120"><?php echo number_format(str_replace("," , "." , $row->VLRLIQ), 2, ',', '.') ?></td>
								<td class="text-right" width="120"><?php echo number_format(str_replace("," , "." , $row->USU_VLRADT), 2, ',', '.') ?></td>
								<td class="text-right" width="120"><h4><?php echo $valorapr ?></h4></td>
								<td class="text-right" width="100"><?php echo number_format(str_replace("," , "." , $row->VLRABE), 2, ',', '.') ?></td>							
								<td class="text-center"><h5><?php echo $situacao ?></h5></td>								
								<td class="text-center"><?php echo $row->USU_NUMTIT ?></td>
								<td class="text-center"><?php echo $row->USUAPR ?></td>								
								<td class="text-left small"><a href="#" data-toggle="tooltip" data-placement="left" title="<?php echo $row->OBSOCP ?>"><?php echo substr_replace($row->OBSOCP, '...', 30); ?></a></td>
								<td class="text-left small"><?php echo utf8_decode($row->USU_OBS) ?></td>
								<td class="text-center small"><?php echo $row->USU_DESCAREA ?></td>								
							</tr>						
															
						
				<?php 								
						$tot_fil = $row->USU_FILIAL;																						
						$tot_adtr += str_replace("," , "." , $row->USU_VLRADT);
						$tot_aprr += str_replace("," , "." , $row->USU_VLRAPR);
						$tot_pg += str_replace("," , "." , $row->VLRABE);
						$cont++;
						$sub_fil_adt += str_replace("," , "." , $row->USU_VLRADT);
						$sub_fil_apr += str_replace("," , "." , $row->USU_VLRAPR);
						$sub_fil_pg += str_replace("," , "." , $row->VLRABE);
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
						    	<td colspan="3"><strong>Total Unidade:</strong></td>
						    	<td colspan="1" class="text-right"><strong><?php echo number_format(str_replace("," , "." , $sub_fil_ped), 2, ',', '.')  ?></strong></td>
						    	<td colspan="1" class="text-right"><strong><?php echo number_format(str_replace("," , "." , $sub_fil_adt), 2, ',', '.')  ?></strong></td>
						    	<td colspan="1" class="text-right"><strong><?php echo number_format(str_replace("," , "." , $sub_fil_apr), 2, ',', '.') ?></strong></td>
						    	<td colspan="1" class="text-right"><strong><?php echo number_format(str_replace("," , "." , $sub_fil_pg), 2, ',', '.') ?></strong></td>
						    	<td colspan="7"></td>
							</tr>
							<tr class="total">
								<td colspan="3"><strong>Total Geral</strong></td>
								<td colspan="1" class="text-right" ><strong><?php echo number_format(str_replace("," , "." , $tot_ped), 2, ',', '.') ?></strong></td>
								<td colspan="1" class="text-right" ><strong><?php echo number_format(str_replace("," , "." , $tot_adtr), 2, ',', '.') ?></strong></td>
								<td colspan="1" class="text-right" ><strong><?php echo number_format(str_replace("," , "." , $tot_aprr), 2, ',', '.')  ?></strong></td>
								<td colspan="1" class="text-right" ><strong><?php echo number_format(str_replace("," , "." , $tot_pg), 2, ',', '.')  ?></strong></td>
								<td colspan="7"></td>
							</tr>	
				    	</tfoot>
							<?php 
					    } else {
					    	echo '<div class="alert alert-warning"><i class="fa fa-exclamation-circle"></i> Não existem dados para exibir.</div>';
					    }
					?>				
				</table>
				<div class="clearfix"></div>		
	</div>