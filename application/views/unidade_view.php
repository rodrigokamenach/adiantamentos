<div class="col-md-4">
			<div class="panel panel-custom">
				<div class="panel-heading">
			    	<h5><i class="fa fa-building-o"></i> Resumo por Unidade x Dia</h5>
				</div>
				<div class="panel-body">
					<?php if ($unidade) {?>
					
					<table class="table table-striped table-condensed table-hover small" style="font-size: 10px">
						<thead>
							<tr class="cabecalho">		    		
						    	<th class="text-center" style="width: 130px">UND</th>
						    	<th class="text-center">Previsto</th>
							    <th class="text-center">Total Adt</th>
							    <th class="text-center">Total Apr</th>				    		
						    </tr>
					    </thead>
					    <tbody>		    		    
					    	<?php 
					    		$tot_adtu = 0;
					    		$tot_apru = 0;
					    		$tot_pre = 0;
					    		foreach ($unidade as $row) { 
					    			if (empty($row->VLRPRE)) {
					    				$vlrpre = 0;					    				
					    			} else {
					    				$vlrpre = $row->VLRPRE;
					    			}
					    	?>					    	
							<tr>						
								<td class="text-left" style="width: 130px"><?php echo $row->USU_INSTAN.' - '.$row->SIGFIL ?></td>
								<td class="text-right">R$ <?php echo number_format(str_replace("," , "." , $vlrpre), 2, ',', '.') ?></td>
								<td class="text-right">R$ <?php echo number_format(str_replace("," , "." , $row->VLR_ADT), 2, ',', '.') ?></td>
								<td class="text-right">R$ <?php echo number_format(str_replace("," , "." , $row->VLR_APR), 2, ',', '.') ?></td>							
							</tr>
						</tbody>
							<?php 
								$tot_pre += str_replace("," , "." , $vlrpre);
								$tot_adtu += str_replace("," , "." , $row->VLR_ADT);
								$tot_apru += str_replace("," , "." , $row->VLR_APR);
								}
							?>
						<tfoot>
							<tr class="total">
								<td style="width: 130px"><strong>Total</strong></td>
								<td class="text-right"><strong>R$ <?php echo number_format(str_replace("," , "." , $tot_pre), 2, ',', '.') ?></strong></td>
								<td class="text-right"><strong>R$ <?php echo number_format(str_replace("," , "." , $tot_adtu), 2, ',', '.') ?></strong></td>
								<td class="text-right"><strong>R$ <?php echo number_format(str_replace("," , "." , $tot_apru), 2, ',', '.')  ?></strong></td>
								
							</tr>	
						</tfoot>
							<?php  
					    } else {
					    	echo '<div class="alert alert-warning"><i class="fa fa-exclamation-circle"></i> Não existem dados para exibir.</div>';
					    }
					?>
						
					</table>
				</div>				
			</div>
		</div>