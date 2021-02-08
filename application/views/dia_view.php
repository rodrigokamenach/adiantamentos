<script>
	function zeroPad(num, places) {
		  var zero = places - num.toString().length + 1;
		  return Array(+(zero > 0 && zero)).join("0") + num;
	}

	function jCadDia(dia, mes, ano){
		dia = zeroPad(dia,2);
		mes = zeroPad(mes,2);		
		dt_dia = dia+"/"+mes+"/"+ano;
		$("#dt_dia").val(dt_dia);   		
		//antes de abrir a janela, preciso carregar os dados do cliente e preencher os campos dentro do modal    		  	
		$('#mCadDia').modal('show');		
	}
	
	function loading_show(e) {
	    $('#'+e).html("<img src='<?php echo base_url();?>assets/img/ajax_loader_blue_32.gif'/>").fadeIn('fast');
	}
	//Aqui desativa a imagem de loading
	function loading_hide(e) {
	    $('#'+e).fadeOut('fast');
	}

	function salvar(){ 
		loading_show('load_cad');    		
		$.post($('#form_cad_dia').attr('action'), $('#form_cad_dia').serialize(), function( data ) {
    		if(data.st == 0) {
    			loading_hide('load_cad');
    			$('#validation-error-cad').html(data.msg);
    			window.location="<?php echo current_url();?>";   		
    		}
    		if(data.st == 1) {
    			loading_hide('load_cad');
    			$('#validation-error-cad').html(data.msg);
    		}
					
		}, 'json');
		return false;		
	}
	
	function jEdDia(codigo){    		
		//antes de abrir a janela, preciso carregar os dados do cliente e preencher os campos dentro do modal
		carregaDadosDia(codigo);    		
		$('#mEdDia').modal('show');		
	}

	function carregaDadosDia(codigo){
		$.post('<?php echo base_url(); ?>index.php/dia/busca_dia', {
			codigo: codigo
		}, function (data){        									
			$("#codigo").val(data.USU_CODDIA);
			$("#dt_dia_ed").val(data.USU_DT);
			$("input:radio[name=status_ed][value="+data.USU_STDIA+"]").prop("checked", true);								    		
			//aqui eu seto a o input hidden com o id do cliente, para que a edição funcione. Em cada tela aberta, eu seto o id do cliente. 
		}, 'json');
	}

	function alterar(){ 
		loading_show('load_ed');    		
		$.post($('#form_ed_dia').attr('action'), $('#form_ed_dia').serialize(), function( data ) {
    		if(data.st == 0) {
    			loading_hide('load_ed');
    			$('#validation-error-ed').html(data.msg);
    			window.location="<?php echo current_url();?>";   		
    		}
    		if(data.st == 1) {
    			loading_hide('load_ed');
    			$('#validation-error-ed').html(data.msg);
    		}
					
		}, 'json');
		return false;		
	}	
</script>
<div class="container-fluid">
	<div class="row-fluid">
		<div class="panel panel-custom">
			<div class="panel-heading">
		  		<h4 class="panel-title">Abre/Fecha Dias</h4>		  				  	
		  	</div> 
			<div class="panel-body">
				<?php echo $calendario; ?>
			</div>
			<div class="panel-footer">
			</div>
	</div>
	<div id="push"></div>
</div>
</div>
<div class="clearfix"></div>
<!----------------------------------------------------------------------------- EDITAR STATUS DIA -------------------------------------------------------------------------------------------->
<div class="modal fade" id="mEdDia" >
	  <div class="modal-dialog modal-sm">
	    <div class="modal-content">
	      <div class="modal-header modal-header-custom">
	        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Fechar</span></button>
	        <h4 class="modal-title">Editar Dia</h4>
	      </div>
	      <div class="modal-body">
	      <?php 
	      		$this->load->helper(array('form'));
				$cad_attribute = array('id' => 'form_ed_dia', 'class' => 'form-horizontal'); 
				echo form_open('dia/alterar', $cad_attribute); 
			?>	      		      				
			 <div class="row-fluid">
				<div id="validation-error-ed"></div>
				<div class="form-group">
				    <label for="dt_dia_ed" class="control-label col-sm-3">Data</label>
				    <div class="col-sm-9">
				    	<input type="text" class="form-control" id="dt_dia_ed" name="dt_dia_ed" readonly >
				    </div>
			  	</div>			  				 
			  	<div class="form-group">
				    <label for="status_ed" class="control-label col-sm-3">Status</label>
				    <div class="col-sm-9">
				    	<label class="radio-inline">
						  <input type="radio" name="status_ed" id="status_ed" value="A" >
						  Aberto
						</label>
						<label class="radio-inline">
						  <input type="radio" name="status_ed" id="status_ed" value="F">
						  Fechado
						</label>
				    </div>
				    <input type="hidden" name="codigo" id="codigo">
				    <input type="hidden" name="cd_user_ed" id="cd_user_ed" value="<?php echo $user_codigo; ?>">
			  	</div>			  		                    		      							  			 			  						   
			</div>  				    			   
	      </div>
	      <div class="modal-footer">
	      	<div class="row-fluid">
		      	<div id="load_ed" class="col-sm-2"></div>
		        <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> Cancelar</button>		        
		        <button type="button" name="cadconv" id="cadconv" onclick="alterar()" class="btn btn-success" ><i class="fa fa-floppy-o"></i> Salvar</button>
	        </div>	
	        <?php echo form_close();?>	        
	      </div>
	    </div><!-- /.modal-content -->
	  </div><!-- /.modal-dialog -->
	</div><!-- /.modal -->
	
<!----------------------------------------------------------------------------- CADASTRAR DIA -------------------------------------------------------------------------------------------->
<div class="modal fade" id="mCadDia" >
	  <div class="modal-dialog modal-sm">
	    <div class="modal-content">
	      <div class="modal-header modal-header-custom">
	        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Fechar</span></button>
	        <h4 class="modal-title">Abrir Dia</h4>
	      </div>
	      <div class="modal-body">
	      <?php 
	      		$this->load->helper(array('form'));
				$cad_attribute = array('id' => 'form_cad_dia', 'class' => 'form-horizontal'); 
				echo form_open('dia/salvar', $cad_attribute); 
			?>	      		      				
			 <div class="row-fluid">
				<div id="validation-error-cad"></div>
				<div class="form-group">
				    <label for="dt_dia" class="control-label col-sm-3">Data</label>
				    <div class="col-sm-9">
				    	<input type="text" class="form-control" id="dt_dia" name="dt_dia" readonly>
				    </div>
			  	</div>			  				 
			  	<div class="form-group">
				    <label for="status" class="control-label col-sm-3">Status</label>
				    <div class="col-sm-9">
				    	<label class="radio-inline">
						  <input type="radio" name="status" id="status" value="A" checked>
						  Aberto
						</label>
						<label class="radio-inline">
						  <input type="radio" name="status" id="status" value="F">
						  Fechado
						</label>
				    </div>
				    <input type="hidden" name="cd_user" id="cd_user" value="<?php echo $user_codigo; ?>">
			  	</div>			  		                    		      							  			 			  						   
			</div>  				    			   
	      </div>
	      <div class="modal-footer">
	      	<div class="row-fluid">
		      	<div id="load_cad" class="col-sm-2"></div>
		        <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> Cancelar</button>		        
		        <button type="button" name="cadconv" id="cadconv" onclick="salvar()" class="btn btn-success" ><i class="fa fa-floppy-o"></i> Salvar</button>
	        </div>	
	        <?php echo form_close();?>	        
	      </div>
	    </div><!-- /.modal-content -->
	  </div><!-- /.modal-dialog -->
	</div><!-- /.modal -->