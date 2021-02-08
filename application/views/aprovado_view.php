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

    	$('#acao').selectpicker({    	       
	        size: "auto"
	        ,width: "150px"
	        ,style:'btn-sm btn-default'
	    });

	    
    	$('#fornecedor').selectpicker({    	       
	        size: "auto"
	        ,width: "150px"
	        ,style:'btn-sm btn-default'
	    });
    	
    	$('#dtadt').datepicker({
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
	$("#busapr").click(function(){
    	$('#resultado').html('');    	
      	loading_show('load_apr');
      	dataString = $("#buscatrans").serialize();
      	$.ajax({
        	type: "POST",
        	url: "<?php echo base_url();?>index.php/aprovado/carreg_apr",
        	data: dataString,        	
        	datatype:'json',
        	success: function(data){           
            	loading_hide('load_apr');
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
			    	<h4><i class="fa fa-search"></i> Filtra Adiantamentos p/ Aprovação</h4>			    				   
			    	<h4><span class="label label-warning">Somente adiantamentos <strong>NãO APROVADOS com o dia FECHADO!</strong></span></h4>			    	
				</div>
				<div class="panel-body">
					<?php 
						$this->load->helper(array('form'));
						echo validation_errors(); 
						$attributes = array('id' => 'buscatrans', 'class' => 'form-horizontal'); 
						echo form_open('aprovado/carreg_apr', $attributes); 
					?>
					<div class="form-group col-sm-2">
					  		<label for="acao" class="col-sm-12 small left">Ação</label>
						    <div class="col-sm-3">
						    	<select name="acao" id="acao" class="selectpicker">
						    		<option SELECTED value="APR">Aprovar</option>
						    		<option value="DES">Desaprovar</option>						    								    								    
						    	</select>
						    </div>
					  	</div>					
					<div class="form-group col-sm-2">
						<label for="dtadt" class="col-sm-12 small">Data</label>
						<div class="col-sm-8"> 		    							    			
	    					<input type="text" class="form-control input-sm" id="dtadt" name="dtadt" value="<?php echo date('d/m/Y') ?>">
	    				</div>		    				
  					</div>	  						  					  
  					<div class="form-group col-sm-2">
  						<label for="filial" class="col-sm-12 small">Filial</label>	  						
  						<div class="col-sm-8">	  									    							    		
		    				<?php 
		    					$options = array ('' => 'Escolha');
	    						foreach($filiais as $fil)
	        						$options[$fil->CODFIL] = $fil->CODFIL.' - '.$fil->USU_INSTAN.' - '.$fil->SIGFIL;
	    						echo form_dropdown('filial', $options, 'class="selectpicker"', 'id="filial"');
							?>
	    				</div>
  					</div>
  					<div class="form-group col-sm-2">
	    				<label for="fornecedor" class="col-sm-12 small">Fornecedor</label>
	    				<div class="col-sm-6">
	    				<?php 
	    					$options = array ('' => 'Escolha');
    						foreach($fornec as $for)
        						$options[$for->CODFOR] = $for->CODFOR.' - '.$for->APEFOR;
    						echo form_dropdown('fornecedor', $options, 'class="selectpicker"', 'id="fornecedor" data-live-search="true"');
						?>
	    				</div>
  					</div>		  					
  					<div class="form-group col-sm-2">
  						<label for="area" class="col-sm-12 small">Área</label>
  						<div class="col-sm-2">					  									   
					    	<?php $options = array ('' => 'Escolha');
							    foreach($areas as $ar)
							        $options[$ar->USU_CODAREA] = $ar->USU_DESCAREA;
							    echo form_dropdown('area', $options, 'class="selectpicker"', 'id="area"');
							?>
					    </div>
				  	</div>
				  	<div class="form-group col-sm-3">
				  		<label for="pedido" class="col-sm-12 small">Pedido</label>
	    				<div class="col-sm-6">		    						    			
	    					<input type="text" class="form-control input-sm" id="pedido" name="pedido">
	    				</div>
  					</div>		  					  										  								
				</div>
				<div class="panel-footer">
					<div class="row">      					
      					<div class="col-sm-12 ">
      						<div id="load_apr" class="col-sm-11 left"></div>        				     
        					<button type="submit" name="busapr" id="busapr" class="btn btn-success" ><i class="fa fa-search"></i> Buscar</button>
        				</div>        				
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
<!-- ------------------------------------------------------Retorno ----------------------------------------------------------------------------------------------------- -->	
	<div class="modal fade" id="mRetorno" >
		<div class="modal-dialog modal-md">
	    <div class="modal-content">
	      <div class="modal-header modal-header-custom">
	        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Fechar</span></button>
	        <h4 class="modal-title">Resultado da Aprovação</h4>
	      </div>
	      <div class="modal-body">	         		      						  	 
			  <div id="retorno"></div>		 		  		 		   			    
	      </div>
	      <div class="modal-footer">
	      	<div class="row-fluid">
	      	<div id="load" class="col-sm-2"></div>
	        <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> Fechar</button>	       
	        </div>	        		       
	      </div>
	    </div><!-- /.modal-content -->
	  </div><!-- /.modal-dialog -->
	</div><!-- /.modal --> 