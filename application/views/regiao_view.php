<div class="col-md-4">
	<div class="panel panel-custom">
		<div class="panel-heading">
			<h5><i class="fa fa-globe"></i> Resumo por Região x Dia</h5>
		</div>
		<div class="panel-body">
					<?php if ($regiao) { ?>
					<table class="table table-striped table-condensed table-hover small" style="font-size: 10px">
				<thead>
					<tr class="cabecalho">
						<th class="text-center" style="width: 20px">UF</th>
						<th class="text-center">Previsto</th>
						<th class="text-center">Total Adt</th>
						<th class="text-center">Total Apr</th>
					</tr>
				</thead>
				<tbody>		    		    
					    	<?php
						$tot_adtr = 0;
						$tot_aprr = 0;
						$tot_pre = 0;
						foreach ( $regiao as $row ) {
							if (empty($row->VLRPRE)) {
								$vlrpre = 0;
							} else {
								$vlrpre = $row->VLRPRE;
							}
							?>
							<tr>
						<td class="text-center" style="width: 20px"><?php echo $row->SIGUFS ?></td>
						<td class="text-right">R$ <?php echo number_format(str_replace("," , "." , $vlrpre), 2, ',', '.') ?></td>
						<td class="text-right">R$ <?php echo number_format(str_replace("," , "." , $row->VLR_ADT), 2, ',', '.') ?></td>
						<td class="text-right">R$ <?php echo number_format(str_replace("," , "." , $row->VLR_APR), 2, ',', '.') ?></td>
					</tr>
				</tbody>
						<?php
							$tot_pre += str_replace("," , "." , $vlrpre);
							$tot_adtr += str_replace ( ",", ".", $row->VLR_ADT );
							$tot_aprr += str_replace ( ",", ".", $row->VLR_APR );
						}
						?>
						<tfoot>
					<tr class="total">
						<td><strong>Total</strong></td>
						<td class="text-right"><strong>R$ <?php echo number_format(str_replace("," , "." , $tot_pre), 2, ',', '.') ?></strong></td>
						<td class="text-right"><strong>R$ <?php echo number_format(str_replace("," , "." , $tot_adtr), 2, ',', '.') ?></strong></td>
						<td class="text-right"><strong>R$ <?php echo number_format(str_replace("," , "." , $tot_aprr), 2, ',', '.')  ?></strong></td>

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
