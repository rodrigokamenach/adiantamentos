<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
//we need to call PHP's session object to access it through CI
class Aprovado extends CI_Controller {
	
	function __construct() {
		parent::__construct();
		$this->load->model('globals','',TRUE);
		$this->load->model('aprovados','',TRUE);		
	}
	
	function index() {
		if($this->session->userdata('newadt')) {
			$session_data = $this->session->userdata('newadt');
			$data['usuario'] = $session_data['usuario'];
			$data['usu_permissoes'] = $session_data['usu_permissoes'];
			$data['usu_filial'] = $session_data['usu_filial'];
			$data['usu_email'] = $session_data['usu_email'];
			$data['usu_codigo'] = $session_data['usu_codigo'];
			$data['usu_area'] = $session_data['usu_area'];
			
			
			$permissoes = explode(';', $data['usu_permissoes']);
			if($permissoes[1] == 'S' || $permissoes[2] == 'S' || $permissoes[3] == 'S') {
				if (empty($dia)) {
					$dia = date('d/m/Y');
				}
			
				if (empty($filial)) {
					$filial = null;
				}
			
				if (empty($area)) {
					$area = null;
				}
			
				$dados['unidade'] = $this->globals->unidade($dia, $filial, $area);
				$dados['area'] = $this->globals->area($dia, $filial, $area);
				$dados['regiao'] = $this->globals->regiao($dia, $filial, $area);			
				$dados['filiais'] = $this->globals->lista_filial($data['usu_filial']);
				$dados['fornec'] = $this->globals->lista_fornec();
				$dados['areas'] = $this->globals->lista_area($data['usu_area']);
				$dados['user_codigo'] = $data['usu_codigo'];				
				
				$this->load->view('header_view', $data);
				$this->load->view('aprovado_view', $dados);
				$this->load->view('footer_view');
			} else {
				redirect('home', 'refresh');
			}
		} else {
			//If no session, redirect to login page
			redirect('login', 'refresh');
		}
	}
	
	function carreg_apr() {
		$session_data = $this->session->userdata('newadt');
		$areauser = $session_data['usu_area'];
		
		$dtadt =  $this->input->post('dtadt');
		$acao = $this->input->post('acao');
		$filial =  $this->input->post('filial');		
		$area =  $this->input->post('area');
		$pedido =  $this->input->post('pedido');
		$codfor = $this->input->post('fornecedor');
		$session_data = $this->session->userdata('newadt');
		$dados['user_codigo'] = $session_data['usu_codigo'];
		//$dados['user_codigo'] = 423;
		//$dados['usu_filial'] = $session_data['usu_filial'];
		$permissoes = explode(';', $session_data['usu_permissoes']);
		
		if (!$filial) {
			$filial = $session_data['usu_filial'];
		}
		//var_dump($permissoes);
		$gerente = $permissoes[1];
		$diretor = $permissoes[2];

		$dados['unidade'] = $this->globals->unidade($dtadt, $filial, $area);
		$dados['regiao'] = $this->globals->regiao($dtadt, $filial, $area);
		$dados['area'] = $this->globals->area($dtadt, $filial, $area);
		$dados['resultado'] = $this->aprovados->listar($dtadt, $filial, $area, $pedido, $codfor, $areauser, $gerente, $diretor, $acao);
		$dados['acao'] = $acao;
		
		if ($dados['resultado']) {
			$heading = array('DATA','PEDIDO','EMPRESA','FILIAL','FORNECEDOR','COMPRADOR','VALOR PEDIDO','VALOR ADIANTAMENTO','APLIC/OBS','OBS','AREA','RECEBIMENTO');
			
			$this->load->library('Excel');
			
			$this->excel->setActiveSheetIndex(0);
			$this->excel->getActiveSheet()->setTitle('Aprovacao Adiantamentos');
			
			$rowNumberH = 1;
			$colH = 'A';
			
			foreach($heading as $h){
				$this->excel->getActiveSheet()->setCellValue($colH.$rowNumberH,$h);
				$this->excel->getActiveSheet()->getColumnDimension($colH)->setAutoSize(true);
				$colH++;
			}
			
			$this->excel->getActiveSheet()->getStyle('A1:L1')->getFont()->setBold(true);
			$this->excel->getActiveSheet()->getStyle('A1:L1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
			$this->excel->getActiveSheet()->getStyle('A1:L1')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			$this->excel->getActiveSheet()->getStyle('A1:L1')->getFill()->getStartColor()->setRGB('cbded0');
			$this->excel->getActiveSheet()->getStyle('A1:H1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$this->excel->getActiveSheet()->getStyle('J1:L1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$currencyFormat = '_(R$* #,##0.00_);_(R$* (#,##0.00);_(R$* "-"??_);_(@_)';
				
			$rowNumber = 2;
			foreach($dados['resultado'] as $row){
				$this->excel->getActiveSheet()->getStyle('A'.$rowNumber.'')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$this->excel->getActiveSheet()->getStyle('B'.$rowNumber.'')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$this->excel->getActiveSheet()->getStyle('C'.$rowNumber.'')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$this->excel->getActiveSheet()->getStyle('D'.$rowNumber.'')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$this->excel->getActiveSheet()->getStyle('E'.$rowNumber.'')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$this->excel->getActiveSheet()->getStyle('F'.$rowNumber.'')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$this->excel->getActiveSheet()->getStyle('G'.$rowNumber.'')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$this->excel->getActiveSheet()->getStyle('H'.$rowNumber.'')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$this->excel->getActiveSheet()->getStyle('I'.$rowNumber.'')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$this->excel->getActiveSheet()->getStyle('J'.$rowNumber.'')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$this->excel->getActiveSheet()->getStyle('K'.$rowNumber.'')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$this->excel->getActiveSheet()->getStyle('L'.$rowNumber.'')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				
					
				$this->excel->getActiveSheet()->setCellValue('A'.$rowNumber,$row->USU_DTLANC);
				$this->excel->getActiveSheet()->setCellValue('B'.$rowNumber,$row->USU_NUMOCP);				
				$this->excel->getActiveSheet()->setCellValue('C'.$rowNumber,$row->USU_CODEMP);
				$this->excel->getActiveSheet()->setCellValue('D'.$rowNumber,$row->USU_FILIAL.' - '.$row->SIGFIL.' - '.$row->USU_INSTAN);
				$this->excel->getActiveSheet()->setCellValue('E'.$rowNumber,$row->APEFOR);
				$this->excel->getActiveSheet()->setCellValue('F'.$rowNumber,$row->USULANC);
				$this->excel->getActiveSheet()->setCellValue('G'.$rowNumber,str_replace("," , "." , $row->VLRLIQ), 2, ',', '.');
				$this->excel->getActiveSheet()->setCellValue('H'.$rowNumber,str_replace("," , "." , $row->USU_VLRADT), 2, ',', '.');				
				$this->excel->getActiveSheet()->setCellValue('I'.$rowNumber,$row->OBSOCP);
				$this->excel->getActiveSheet()->setCellValue('J'.$rowNumber,$row->USU_OBS);
				$this->excel->getActiveSheet()->setCellValue('K'.$rowNumber,$row->USU_AREA);
				$this->excel->getActiveSheet()->setCellValue('L'.$rowNumber,$row->RECB);
				$this->excel->getActiveSheet()->getStyle('G'.$rowNumber)->getNumberFormat()->setFormatCode($currencyFormat);
				$this->excel->getActiveSheet()->getStyle('H'.$rowNumber)->getNumberFormat()->setFormatCode($currencyFormat);				
				$rowNumber++;
			}
			
			$filename = 'Aprovacao_Adiantamentos'; //save our workbook as this file name
			
			$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
			$objWriter->save(str_replace(__FILE__,APPPATH.'xls/'.$filename.'.xls',__FILE__));
				
			$dados['exporta'] = '<a class="btn btn-primary" href="'.base_url().'index.php/exporta/excel/'.$filename.'"><i class="fa fa-file-excel-o"></i> Exportar Excel</a>';
								
			$data['uni'] = $this->load->view('unidade_view', $dados);
			$data['reg'] = $this->load->view('regiao_view', $dados);
			$data['are'] = $this->load->view('area_view', $dados);
			$data['result'] = $this->load->view('aprovado_table_view', $dados);
		} else {
			echo '<div class="col-md-12">
					<div class="panel panel-custom">
						<div class="panel-heading">
			    			<h4><i class="fa fa-list"></i> Resultado Adiantamentos</h4>
						</div>
						<div class="panel-body">
							<div class="alert alert-warning"><i class="fa fa-exclamation-circle"></i> N�o existem dados para exibir. Verifique se o dia est� FECHADO e se existem adiantamentos N�O APROVADOS!</div>
						</div>
					</div>';
			exit();
		}
			
		return $data;
	
	}
	
	function aprovar() {
		$this->load->library('form_validation');
		$this->form_validation->set_message('required', '<div class="alert alert-danger">O campo %s é obrigatório</div>');
			
		$this->form_validation->set_rules(array_filter($this->input->post('vlrapr[]', TRUE)), 'Valor Aprovado', 'trim|required');
		$this->form_validation->set_rules('cd_user', 'Usuário', 'trim|required');
		
		if ($this->form_validation->run() == FALSE)	{
			echo json_encode(array('st'=>1, 'msg' => validation_errors()));
		} else {
			$vlrapr = array_filter($this->input->post('vlrapr[]', TRUE));
			$cd_user = $this->input->post('cd_user');
			$erro = 0;
			$msg_erro = '';
			$sucesso = 0;
			$msg_sucess = '';
			$msg_mail = '';
			$id_ok = '';
			
			$session_data = $this->session->userdata('newadt');
			$permissoes = explode(';', $session_data['usu_permissoes']);
			
			//var_dump($permissoes);
			$gerente = $permissoes[1];
			$diretor = $permissoes[2];
			//echo $cd_user.'<br>';
			//var_dump($vlrapr);
			//exit();
			foreach ($vlrapr as $id => $valor) {
				//echo $id;
				$result_pedido = $this->aprovados->getDadosPedido($id);
				//print_r($result_pedido);
				//var_dump($result_pedido);
				//exit();
				foreach ($result_pedido as $row) {
					$pedido = $row->USU_NUMOCP;
					$emp = $row->USU_CODEMP;
					$filial = $row->USU_FILIAL;
					$vlr_adt = $row->USU_VLRADT;
				}
								
				$vlr_adt_conv = str_replace("," , "." ,$vlr_adt);
				$vlr_apr_conv = str_replace("," , "." ,$valor);
				
				if ($vlr_adt_conv < $vlr_apr_conv) {
					$erro += 1;
					$msg_erro .= '<div class="alert alert-danger small">Erro ao aprovador pedido: <strong>'.$pedido.'</strong> - filial: <strong>'.$filial.'</strong>. O valor a ser aprovado ('.$valor.') não pode ser maior que o valor ('.$vlr_adt.') do adiantamento!</div>';
				} else {
					//$sucesso += 1;
					//$msg_sucess .= 'ok';
					//$valor = null;
					//$cd_user = null;
					if ($gerente == 'S') {
					
						$dados_apr = array(
								$valor,
								//$cd_user,
								intval($cd_user),
								$id
						);
						//var_dump($dados_apr);
						//exit();
						$sql = "UPDATE USU_TADTMOV SET USU_VLRAPR = ?, USU_APRUSU = ?, USU_DTAPR = SYSDATE WHERE USU_CODEMP||USU_FILIAL||USU_NUMOCP||USU_ID = ?";
						
						$result = $this->aprovados->crud($sql, $dados_apr);
						
						if($result == false) {
							$erro += 1;
							$msg_erro .= '<div class="alert alert-danger small">Erro ao aprovar pedido: <strong>'.$pedido.'</strong> - filial: <strong>'.$filial.'</strong>.</div>';						
						} else {					
							$sucesso += 1;																																																																
						}//aki
					
					}
					
					if($diretor == 'S') {
						
						$dados_apr = array(
								$valor,
								//$cd_user,
								intval($cd_user),
								$id
						);
						//var_dump($dados_apr);
						//exit();
						$sql = "UPDATE USU_TADTMOV SET USU_VLRAPR = ?, USU_APRDIR = ?, USU_DTDIR = SYSDATE WHERE USU_CODEMP||USU_FILIAL||USU_NUMOCP||USU_ID = ?";
						
						$result = $this->aprovados->crud($sql, $dados_apr);
						
						if($result == false) {
							$erro += 1;
							$msg_erro .= '<div class="alert alert-danger small">Erro ao aprovar pedido: <strong>'.$pedido.'</strong> - filial: <strong>'.$filial.'</strong>.</div>';
						} else {
							$sucesso += 1;
						}//aki
						
					}
					
				}																	
																											
			}				
			
			if ($erro > 0) {
				echo '<legend>Foram encontrados '.$erro.' erro(s)!</legend>';
				echo $msg_erro.'<br>';
			}
			echo '<legend>Foram processados '.$sucesso.' pedido(s)!</legend>';
			if ($sucesso > 0) {
				echo '<div class="alert alert-success small">Pedidos Aprovados com sucesso!</div>';				
			}
			
			$this->load->library('email');
			$config = array (
					'mailtype' 		=> 'html',
					'charset'  		=> 'utf-8',
					'priority' 		=> '1',
					'potocol'  		=> 'smtp',
					'smtp_host'		=> 'ssl://smtp.grupofarias.com.br',
					'smtp_port'		=> '587',
					'smtp_timeout' 	=> '7',
					'smtp_user'    	=> 'rodrigo.kamenach@grupofarias.com.br',
					'smtp_pass'    	=> 'Z1x2c34V%:',
					'validation' 	=> TRUE
			);
			$this->email->initialize($config);
			$this->email->from('financeiro@grupofarias.com.br', 'Financeiro');
			$this->email->to('rodrigo.kamenach@grupofarias.com.br, compras@grupofarias.com.br');
			//$this->email->to('rodrigo.kamenach@grupofarias.com.br');
			$this->email->subject('Adiantamentos Aprovados');			
			//$message = $this->load->view('email_view',$dados_mail,TRUE);
			$this->email->message('Adiantamentos foram aprovados. Por favor faça a geração das Titulos');
			$this->email->send();
						
		}
	}
	
	function desaprova() {
	
		$this->load->library('form_validation');
	
		$this->form_validation->set_message('required', '<div class="alert alert-danger">O campo %s é obrigatório</div>');
			
		$this->form_validation->set_rules(array_filter($this->input->post('vlrapr[]', TRUE)), 'Valor Aprovado', 'trim|required');
		$this->form_validation->set_rules('cd_user', 'Usuário', 'trim|required');
	
		if ($this->form_validation->run() == FALSE)	{
			echo json_encode(array('st'=>1, 'msg' => validation_errors()));
		} else {
				
			$id = array_filter($this->input->post('vlrapr[]', TRUE));
			$cd_user = $this->input->post('cd_user');
			$session_data = $this->session->userdata('newadt');
			$permissoes = explode(';', $session_data['usu_permissoes']);
				
			$gerente = $permissoes[1];
			$diretor = $permissoes[2];
			//var_dump($id);
			//exit();
			$erro = 0;
			$msg_erro = '';
			$sucesso = 0;
	
			foreach ($id as $key => $codigo) {
				//echo $id;
				if ($gerente == 'S') {
	
					$sql_des = "UPDATE USU_TADTMOV SET USU_DTAPR = '', USU_APRUSU = '', USU_VLRAPR = NULL WHERE USU_CODEMP||USU_FILIAL||USU_NUMOCP||USU_ID = ?";
	
					$result_des = $this->aprovados->crud($sql_des, $key);
						
					if ($result_des) {
						$sucesso += 1;
					} else {
						$erro += 1;
						$msg_erro .= '<div class="alert alert-danger small">Erro ao desaprovar pedido '.$codigo.'</div>';
					}
	
				}
	
				if ($diretor == 'S') {
						
					$sql_des = "UPDATE USU_TADTMOV SET USU_DTDIR = NULL, USU_APRDIR = NULL WHERE USU_CODEMP||USU_FILIAL||USU_NUMOCP||USU_ID = ?";
						
					$result_des = $this->aprovados->crud($sql_des, $key);
	
					if ($result_des) {
						$sucesso += 1;
					} else {
						$erro += 1;
						$msg_erro .= '<div class="alert alert-danger small">Erro ao desaprovar pedido '.$codigo.'</div>';
					}
						
				}
			}
				
			if ($erro > 0) {
				echo '<legend>Foram encontrados '.$erro.' erro(s)!</legend>';
				echo $msg_erro.'<br>';
			}
			echo '<legend>Foram processados '.$sucesso.' pedido(s)!</legend>';
			if ($sucesso > 0) {
				echo 'Pedidos desaprovados com sucesso';
			}
				
		}
	
	}

}