<?php if ($result) { ?>
	<table class="table table-striped table-condensed table-hover small">
		<thead>
			<tr class="cabecalho">		    		
			   	<th colspan="2" class="text-center">Produto/Serviço</th>
			    <th class="text-center">Valor Unit</th>
			    <th class="text-center">Quantidade</th>				    		
			</tr>
		</thead>
		<tbody>		    		    
<?php foreach ($result as $row) { ?>
			<tr>						
				<td class="text-center"><?php echo $row->PROSER ?></td>
				<td class="text-left"><?php echo $row->DESCRI ?></td>
				<td class="text-right">R$ <?php echo number_format(str_replace("," , "." , $row->PREUNI), 2, ',', '.') ?></td>
				<td class="text-center"><?php echo $row->QTDPED ?></td>							
			</tr>
		</tbody>
<?php 				
		}							
} else {
	echo '<div class="alert alert-warning"><i class="fa fa-exclamation-circle"></i> Não existem dados para exibir.</div>';
}
?>
	</table>