<script type="text/javascript">
    $(document).ready(function () {
    	$('#filial').selectpicker({    	       
    	        size: "auto"
    	        ,width: "150px"
    	        ,style:'btn-sm btn-default'
    	    }); 

    	$('#area').selectpicker({    	       
	        size: "auto"
	        ,width: "150px"
	        ,style:'btn-sm btn-default'
	    });

    	$('#situacao').selectpicker({    	       
	        size: "auto"
	        ,width: "150px"
	        ,style:'btn-sm btn-default'
	    });

    	$('#fornecedor').selectpicker({    	       
	        size: "auto"
	        ,width: "150px"
	        ,style:'btn-sm btn-default'
	    });   


    	$('#recebe').selectpicker({    	       
	        size: "auto"
	        ,width: "150px"
	        ,style:'btn-sm btn-default'
	    });
    	
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

    	$('#dtpg').datepicker({
            format: "dd/mm/yyyy",
            language: "pt-BR",
            orientation: "top left",
            calendarWeeks: true,
            autoclose: true,
            todayHighlight: true
            
        });

    	$('#dtpgfim').datepicker({
            format: "dd/mm/yyyy",
            language: "pt-BR",
            orientation: "top left",
            calendarWeeks: true,
            autoclose: true,
            todayHighlight: true
            
        });      	
    });   
</script>
<script type="text/javascript">
function loading_show(e) {
    $('#'+e).html("<img src='<?php echo base_url();?>/assets/img/ajax_loader_blue_32.gif'/>").fadeIn('fast');
}
//Aqui desativa a imagem de loading
function loading_hide(e) {
    $('#'+e).fadeOut('fast');
}

$(function(){
	$("#busadt").click(function(){
    	$('#resultado').html('');
    	//$('#regiao').html('');
      	loading_show('load_adt');
      	dataString = $("#buscaadt").serialize();
      	$.ajax({
        	type: "POST",
        	url: "<?php echo base_url();?>index.php/consulta/carreg_consulta",
        	data: dataString,        	
        	datatype:'json',
        	success: function(data){           
            	loading_hide('load_adt');
            	$('#resultado').html(data);            	            	            	          
			}
		}); 
    return false;  //stop the actual form post !important!
	});    
});
</script>
<div class="container-fluid">
	<div class="row">
		<div class="col-md-12">
			<div class="panel panel-custom">
				<div class="panel-heading">
			    	<h5><i class="fa fa-search"></i> Filtra Adiantamentos</h5>
				</div>
				<div class="panel-body">
					<?php 
						$this->load->helper(array('form'));
						echo validation_errors(); 
						$attributes = array('id' => 'buscaadt', 'class' => 'form-horizontal'); 
						echo form_open('consulta/carreg_consulta', $attributes); 
					?>										
						<div class="form-group col-sm-3">		    				
		    				<label for="dtadt" class="col-sm-12 small">Data</label>
		    				<div class="col-sm-6">
		    					<input type="text" class="form-control input-sm" id="dtini" name="dtini" value="<?php echo date('d/m/Y') ?>">
		    				</div>
		    				<div class="col-sm-6">
		    					<input type="text" class="form-control input-sm" id="dtfim" name="dtfim" value="<?php echo date('d/m/Y') ?>">
		    				</div>
		    			</div>
	  					<div class="form-group col-sm-2">
		    				<label for="filial" class="col-sm-12 small">Filial</label>
		    				<div class="col-sm-6">
		    				<?php 
		    					$options = array ('' => 'Escolha');
	    						foreach($filiais as $fil)
	        						$options[$fil->CODFIL] = $fil->CODFIL.' - '.$fil->USU_INSTAN.' - '.$fil->SIGFIL;
	    						echo form_dropdown('filial', $options, 'class="selectpicker"', 'id="filial"');
							?>
		    				</div>
	  					</div>	  					
	  					<div class="form-group col-sm-2">
					  		<label for="area" class="col-sm-12 small">Área</label>
						    <div class="col-sm-3">
						    	<?php $options = array ('' => 'Escolha');
								    foreach($areas as $ar)
								        $options[$ar->USU_CODAREA] = $ar->USU_DESCAREA;
								    echo form_dropdown('area', $options, 'class="selectpicker"', 'id="area"');
								?>
						    </div>
					  	</div>
					  	<div class="form-group col-sm-2">
		    				<label for="fornecedor" class="col-sm-12 small">Fornecedor</label>
		    				<div class="col-sm-3">
		    				<?php 
		    					$options = array ('' => 'Escolha');
	    						foreach($fornec as $for)
	        						$options[$for->CODFOR] = $for->CODFOR.' - '.$for->APEFOR;
	    						echo form_dropdown('fornecedor', $options, 'class="selectpicker"', 'id="fornecedor" data-live-search="true"');
							?>
		    				</div>
	  					</div>					  				  					  					  	
					  	<div class="form-group col-sm-2">
					  		<label for="situacao" class="col-sm-12 small">Situação</label>
						    <div class="col-sm-3">
						    	<select name="situacao" id="situacao" class="selectpicker">
						    		<option value="">Escolha</option>
						    		<option value="Aguar Aprov Ger">Aguardando Aprovação Gerente</option>
						    		<option value="Aguar Aprov Dir">Aguardando Aprovação Diretor</option>
						    		<option value="Aberto">Aberto</option>
						    		<option value="Aberto Parcial">Aberto Parcial</option>
						    		<option value="Pago">Pago</option>
						    		<option value="Liquidado">Liquidado</option>						    		
						    	</select>
						    </div>
					  	</div>
					  	<div class="form-group col-sm-3">
					  		<label for="recebe" class="col-sm-12 small left">Receb.</label>
						    <div class="col-sm-3">
						    	<select name="recebe" id="recebe" class="selectpicker">
						    		<option value="">Escolha</option>
						    		<option value="A Receber">A Receber</option>
						    		<option value="Total">Total</option>
						    		<option value="Parcial">Parcial</option>						    								    	
						    	</select>
						    </div>
					  	</div>
					  	<div class="form-group col-sm-4">		    				
		    				<label for="dtpg" class="col-sm-12 small">Data Pagto</label>
		    				<div class="col-sm-6">
		    					<input type="text" class="form-control input-sm" id="dtpg" name="dtpg" value="">
		    				</div>
		    				<div class="col-sm-6">
		    					<input type="text" class="form-control input-sm" id="dtpgfim" name="dtpgfim" value="">
		    				</div>		    				
		    			</div>					  					  		  					  	
						<div class="form-group col-sm-5">
		    				<label for="pedido" class="col-sm-12 small">Pedido</label>
	    					<div class="col-sm-5">
		    					<input type="text" class="form-control input-sm" id="pedido" name="pedido">
		    				</div>
	  					</div>	  					
	  				</div>		  																		
				<div class="panel-footer">
					<div class="row">      					
      					<div class="col-sm-4">        				     
        					<button type="submit" name="busadt" id="busadt" class="btn btn-success" ><i class="fa fa-search"></i> Buscar</button>
        				</div>
        				<div id="load_adt" class="col-sm-4"></div>
        			</div>	
        			<?php echo form_close();?>
				</div>
			</div>
		</div>		
		<div id="resultado">
			<?php $this->load->view('unidade_view', $unidade); ?>
			<?php $this->load->view('area_view', $area); ?>		
			<?php $this->load->view('regiao_view', $regiao); ?>			
		</div>
		<div class="clearfix"></div>									
	</div>	
</div>
<div class="clearfix"></div>