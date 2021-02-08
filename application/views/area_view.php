<div class="col-md-4">
			<div class="panel panel-custom">
				<div class="panel-heading">
			    	<h5><i class="fa fa-building-o"></i> Resumo por Área x Dia</h5>
				</div>
				<div class="panel-body">
					<?php if ($area) { ?>
					<table class="table table-striped table-condensed table-hover small" style="font-size: 10px">
						<thead>
							<tr class="cabecalho">		    		
						    	<th class="text-center" style="width: 90px">Área</th>
						    	<th class="text-center">Previsto</th>
							    <th class="text-center">Total Adt</th>
							    <th class="text-center">Total Apr</th>				    		
						    </tr>
					    </thead>
					    <tbody>		    		    
					    	<?php 
					    		$tot_adta = 0;
					    		$tot_apra = 0;
					    		$tot_pre = 0;
					    		foreach ($area as $row) { 
					    			if (empty($row->VLRPRE)) {
					    				$vlrpre = 0;
					    			} else {
					    				$vlrpre = $row->VLRPRE;
					    			}
					    			
					    			if (empty($row->VLR_ADT)) {
					    				$vlradt = 0;
					    			} else {
					    				$vlradt = $row->VLR_ADT;
					    			}
					    			
					    			if (empty($row->VLR_APR)) {
					    				$vlrapr = 0;
					    			} else {
					    				$vlrapr = $row->VLR_APR;
					    			}
					    	?>
							<tr>						
								<td class="text-left" style="width: 90px"><?php echo $row->USU_DESCAREA ?></td>
								<td class="text-right">R$ <?php echo number_format(str_replace("," , "." , $vlrpre), 2, ',', '.') ?></td>
								<td class="text-right">R$ <?php echo number_format(str_replace("," , "." , $vlradt), 2, ',', '.') ?></td>
								<td class="text-right">R$ <?php echo number_format(str_replace("," , "." , $vlrapr), 2, ',', '.') ?></td>							
							</tr>
						</tbody>
							<?php
								$tot_pre += str_replace("," , "." , $vlrpre);
								$tot_adta += str_replace("," , "." , $vlradt);
								$tot_apra += str_replace("," , "." , $vlrapr);
								}
							?>
						<tfoot>
							<tr class="total">
								<td><strong>Total</strong></td>
								<td class="text-right"><strong>R$ <?php echo number_format(str_replace("," , "." , $tot_pre), 2, ',', '.') ?></strong></td>
								<td class="text-right"><strong>R$ <?php echo number_format(str_replace("," , "." , $tot_adta), 2, ',', '.') ?></strong></td>
								<td class="text-right"><strong>R$ <?php echo number_format(str_replace("," , "." , $tot_apra), 2, ',', '.')  ?></strong></td>
								
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