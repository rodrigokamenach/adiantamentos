<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
//we need to call PHP's session object to access it through CI
class Transferencia extends CI_Controller {
	
	function __construct() {
		parent::__construct();
		$this->load->model('globals','',TRUE);
		$this->load->model('transferencias','',TRUE);
		$this->load->model('dias','',TRUE);
		$this->load->model('adiantamentos','',TRUE);
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
			
			
			if (empty($dia)) {
				$dia = date('d/m/Y');
			}
			
			if (empty($filial)) {
				$filial = null;
			}
			
			if (empty($area)) {
				$area = null;
			}
					
			$dados['filiais'] = $this->globals->lista_filial();
			$dados['fornec'] = $this->globals->lista_fornec();
			$dados['unidade'] = $this->globals->unidade($dia, $filial, $area);
			$dados['area'] = $this->globals->area($dia, $filial, $area);
			$dados['regiao'] = $this->globals->regiao($dia, $filial, $area);
			$dados['areas'] = $this->globals->lista_area($data['usu_area']);
			$dados['user_codigo'] = $data['usu_codigo'];
			
			$this->load->view('header_view', $data);
			$this->load->view('transferencia_view', $dados);
			$this->load->view('footer_view');
		} else {
			//If no session, redirect to login page
			redirect('login', 'refresh');
		}
	}
	
	function carreg_adt() {		
		$data = array();
		$session_data = $this->session->userdata('newadt');
		$data['usu_codigo'] = $session_data['usu_codigo'];
		$dtadt =  $this->input->post('dtadt');
		$filial =  $this->input->post('filial');
		$area =  $this->input->post('area');
		$pedido = $this->input->post('pedido');
		$codfor = $this->input->post('fornecedor');
		
		if (!$filial) {
			$filial = $session_data['usu_filial'];
		}
		
		$dados['unidade'] = $this->globals->unidade($dtadt, $filial, $area);
		$dados['regiao'] = $this->globals->regiao($dtadt, $filial, $area);
		$dados['area'] = $this->globals->area($dtadt, $filial, $area);
		$dados['user_codigo'] = $data['usu_codigo'];			
			
		$dados['resultado'] = $this->transferencias->listar($dtadt, $filial, $area, $pedido, $codfor);
		
		if ($dados['resultado'] != null) {
			$heading = array('DATA','ORDEM DE COMPRA','EMPRESA','FILIAL','FORNECEDOR','COMPRADOR','VALOR OC','VALOR ADT','APLIC/OBS','OBS','AREA','RECEBIMENTO');
			
			$this->load->library('Excel');
			
			$this->excel->setActiveSheetIndex(0);
			$this->excel->getActiveSheet()->setTitle('Manutencao Adiantamentos');
			
			$rowNumberH = 1;
			$colH = 'A';
			
			foreach($heading as $h){
				$this->excel->getActiveSheet()->setCellValue($colH.$rowNumberH,$h);
				$this->excel->getActiveSheet()->getColumnDimension($colH)->setAutoSize(true);
				$colH++;
			}
				
			$this->excel->getActiveSheet()->getStyle('A1:N1')->getFont()->setBold(true);
			$this->excel->getActiveSheet()->getStyle('A1:N1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
			$this->excel->getActiveSheet()->getStyle('A1:N1')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			$this->excel->getActiveSheet()->getStyle('A1:N1')->getFill()->getStartColor()->setRGB('cbded0');
			$this->excel->getActiveSheet()->getStyle('A1:H1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$this->excel->getActiveSheet()->getStyle('J1:K1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
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
			
				$this->excel->getActiveSheet()->setCellValue('A'.$rowNumber,$row->USU_DTLANC);
				$this->excel->getActiveSheet()->setCellValue('B'.$rowNumber,$row->USU_NUMOCP);
				$this->excel->getActiveSheet()->setCellValue('C'.$rowNumber,$row->USU_CODEMP);
				$this->excel->getActiveSheet()->setCellValue('D'.$rowNumber,$row->SIGFIL.' - '.$row->USU_INSTAN);
				$this->excel->getActiveSheet()->setCellValue('E'.$rowNumber,$row->APEFOR);
				$this->excel->getActiveSheet()->setCellValue('F'.$rowNumber,$row->NOMUSU);
				$this->excel->getActiveSheet()->setCellValue('G'.$rowNumber,str_replace("," , "." , $row->VLRLIQ), 2, ',', '.');
				$this->excel->getActiveSheet()->setCellValue('H'.$rowNumber,str_replace("," , "." , $row->USU_VLRADT), 2, ',', '.');
				$this->excel->getActiveSheet()->setCellValue('I'.$rowNumber,$row->OBSOCP);
				$this->excel->getActiveSheet()->setCellValue('J'.$rowNumber,$row->USU_AREA);
				$this->excel->getActiveSheet()->setCellValue('K'.$rowNumber,$row->RECB);
				$this->excel->getActiveSheet()->getStyle('G'.$rowNumber)->getNumberFormat()->setFormatCode($currencyFormat);
				$this->excel->getActiveSheet()->getStyle('H'.$rowNumber)->getNumberFormat()->setFormatCode($currencyFormat);				
				$rowNumber++;
			}
				
			$filename = 'Manutencao_Adiantamentos'; //save our workbook as this file name
				
			$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
			$objWriter->save(str_replace(__FILE__,APPPATH.'xls/'.$filename.'.xls',__FILE__));
			
			$dados['exporta'] = '<a class="btn btn-primary" href="'.base_url().'index.php/exporta/excel/'.$filename.'"><i class="fa fa-file-excel-o"></i> Exportar Excel</a>';
		
		} else {
			$dados['exporta'] = '';
		}
		
		
		$data['uni'] = $this->load->view('unidade_view', $dados);
		$data['reg'] = $this->load->view('regiao_view', $dados);
		$data['are'] = $this->load->view('area_view', $dados);
		$data['result'] = $this->load->view('transferencia_table_view', $dados);
		
		//var_dump($data);
		//exit();
		//echo json_encode($data);
		return $data;
	
	}
	
	function alterar() {
		$this->load->library('form_validation');
		$this->form_validation->set_message('required', '<div class="alert alert-danger">O campo %s é obrigatório</div>');
		 
		$this->form_validation->set_rules('data_nova', 'Nova Data', 'trim|required');
		//$this->form_validation->set_rules('cd_user', 'Usuário', 'trim|required');
		$this->form_validation->set_rules($this->input->post('vlrapr[][]', TRUE), 'Valor Aprovado', 'trim|required');		

		if ($this->form_validation->run() == FALSE)	{
			echo json_encode(array('st'=>1, 'msg' => validation_errors()));
		} else {
			function filt($match) {
				if($match['valor']!="")
					return true;
				else
					return false;
			}
			
			
			$nova_dt = $this->input->post('data_nova');			
			$cd_user = $this->input->post('cd_user');
			$vlrapr = array_filter($this->input->post('vlrapr[][]', TRUE), 'filt');						
			$erro = 0;
			$msg_erro = '';
			$sucesso = 0;
			$msg_sucess = '';
			$continua = 0;
			//echo $nova_dt.'<br>';			
			//echo $cd_user.'<br>';	
			//var_dump($vlrapr);					
			//exit();
			$result_dia = $this->dias->check_india($nova_dt);					
			//var_dump($result_dia[0]['USU_STDIA']);
									
			if($result_dia[0]['USU_STDIA'] == null) {
				$status = 'A';
								
				$dados_dia = array(
						$nova_dt,
						$status,
						intval($cd_user)
				);
					
				$sql = "INSERT INTO USU_TADTDIA (USU_DT, USU_STDIA, USU_DTABE, USU_CODABE) VALUES (?, ?, TO_CHAR(SYSDATE,'DD/MM/YYYY'), ?)";
					
				$result = $this->dias->crud($sql, $dados_dia);
				$continua = 1;
			} else {
				if ($result_dia[0]['USU_STDIA'] == 'F') {
					$erro += 1;
					$msg_erro .= '<div class="alert alert-danger small">O dia '.$nova_dt.' está fechado. Não é possível realizar a tranferência para um dia fechado.</div>';
					$continua = 0;
				} else {
					$continua = 1;										
				}				
			}
			
			if ($continua == 1) {
				foreach ($vlrapr as $id => $valor) {
					list($cd['emp'], $cd['pedido'], $cd['filial'], $cd['cod']) = explode("-", $id);					
					//var_dump($cd);
					//var_dump($valor);					
					//exit();	
					$result_ped = $this->adiantamentos->verifica_pedido($cd['pedido'], $cd['filial']);
					//var_dump($result_ped);	
					$vlr_pedido = str_replace(",",".",$result_ped[0]['VLRLIQ']);
					if ($result_ped[0]['VLRABE']) {
						$vlr_abe = str_replace(",",".",$result_ped[0]['VLRABE']);
					} else {
						$vlr_abe = null;
					}
					$vlr_adt = str_replace(",",".",$result_ped[0]['USU_VLRADT']);
					$vlr_apr = str_replace(",",".",$result_ped[0]['USU_VLRAPR']);
					$aberto = $result_ped[0]['QTDE'];
					$valor_dig = str_replace(",",".",$valor['valor']);
					if ($vlr_apr == 0) {
						$valor_ant = (str_replace(",",".",$result_ped[0]['USU_VLRADT'])-(str_replace(",",".",$result_ped[0]['USU_VLRAPR'])));
					} else {
						$valor_ant = str_replace(",",".",$result_ped[0]['USU_VLRAPR']);
					}
					
					//if ($aberto > 1) {
					//	$valor_ant = $valor_ant/$aberto;
					//}
					//var_dump($vlr_abe);
					//var_dump($valor_dig);
					//var_dump($valor_ant);
					//verifica se o valor anteriro e 0
					if ($valor_ant == 0) {
						//verifica se o valor aberto e nulo
						if ($vlr_abe == null) {
							//verifica de valor adt e nulo ou 0
							if ($vlr_adt == null or $vlr_adt == 0) {
								$saldo = $vlr_pedido;
							} else {
							//seo valor adt for maior que 0 verifica se o vlr apr e nulo ou 0			
								if ($vlr_apr == null or $vlr_apr == 0) {
									$saldo = $vlr_pedido - $vlr_adt;
								} else {
									$saldo = $vlr_pedido - $vlr_apr;
								}														
							}
																					
						} elseif ($vlr_abe > 0) {
							
							if ($vlr_adt == null or $vlr_adt == 0) {
								$saldo = $vlr_abe;
							} else {
								if ($vlr_apr == null or $vlr_apr == 0) {
									$saldo = $vlr_abe - $vlr_adt;
								} else {
									$saldo = $vlr_abe - $vlr_apr;
								}				
							}								
						} elseif ($vlr_abe == 0) {
							$saldo = $vlr_abe;
						}									
					} else { //valor anterior e maior que 0
						
						if ($vlr_abe == null) {
							//verifica de valor adt e nulo ou 0
							if ($vlr_adt == null or $vlr_adt == 0) {
								$saldo = $vlr_pedido;
							} else {
								//seo valor adt for maior que 0 verifica se o vlr apr e nulo ou 0
								if ($vlr_apr == null or $vlr_apr == 0) {
									$saldo = $vlr_pedido - $vlr_adt;
								} else {
									$saldo = $vlr_pedido - $vlr_apr;
								}
							}
						
						} elseif ($vlr_abe > 0) {
						
							if ($vlr_adt == null or $vlr_adt == 0) {
								$saldo = $vlr_pedido - $vlr_abe;
							} else {
								if ($vlr_apr == null or $vlr_apr == 0) {
									$saldo = $vlr_pedido - $vlr_adt;
								} else {
									$saldo = $vlr_pedido - $vlr_apr;
								}
							}
						} elseif ($vlr_abe == 0) {
							$saldo = 0;
						}
						
						if ($aberto > 1) {
							$saldo = $valor_dig + $saldo;
						} else {
							if ($valor_dig > $valor_ant) {							
								$saldo = $saldo - ($valor_dig - $valor_ant);
							} elseif ($valor_ant < $valor_ant) {
								$saldo = $saldo + ($valor_ant - $valor_dig);
							} elseif ($valor_dig == $valor_ant) {
								$saldo = $valor_dig;
							}
						}
					}		
					//echo $saldo.'<br>';
					//echo $valor_dig.'<br>';
					//echo $valor_ant.'<br>';
					//exit();
					if($saldo+0.01 < $valor_dig) {
						$erro += 1;
						$msg_erro .= '<div class="alert alert-danger small">Erro na manutenção do pedido: <strong>'.$cd['pedido'].'</strong> - filial: <strong>'.$cd['filial'].'</strong>. O valor alterado ('.$valor_dig.') não pode ser maior que o valor ('.$saldo.') do adiantamento!</div>';
					} else {
						if (!isset($valor['pri'])) {
							$valor['pri'] = '';
						}
						//ORGANIZA VALORES PARA INSERIR
						$dados_transf = array(
								$nova_dt,
								$valor['valor'],
								$valor['pri'],
								$valor['obs'],
								$cd['pedido'],
								$cd['filial'],
								$cd['emp'],
								$cd['cod']
						);
						//var_dump($dados_transf);
						
						$sql = "UPDATE USU_TADTMOV SET USU_DTLANC = ?, USU_VLRADT = ?, USU_ADTPRI = ?, USU_OBS = ? WHERE USU_NUMOCP = ? and USU_FILIAL = ? and USU_CODEMP = ? and USU_ID = ?";
				
						//FAZ O INSERT NO BANCOS
						$result = $this->transferencias->crud($sql, $dados_transf);
				
						if ($result == FALSE) {
							$erro += 1;
							$msg_erro .= '<div class="alert alert-danger">Erro na tranfer�ncia do pedido: <strong>'.$cd['pedido'].'</strong> - filial: <strong>'.$cd['filial'].'</strong>. Repita o processo!</div>';
						} else {
							$sucesso += 1;
							$msg_sucess .=	$cd['emp'].'-'.$cd['filial'].'-'.$cd['pedido'].'-'.$vlr_pedido.'-'.$vlr_abe.'-'.$valor_ant.'-'.$valor_dig.'-'.$saldo.'<br>';
						}
					}
				}
									
			
		}
		if ($erro > 0) {
			echo '<legend>Foram encontrados '.$erro.' erro(s)!</legend>';
			echo $msg_erro.'<br>';
		}
			
		if ($sucesso > 0) {
			echo '<legend>Foram processados '.$sucesso.' pedido(s)!</legend>';
			echo '<div class="alert alert-success small">Manuten��o realizada com sucesso!</div>';
		}
		
	}
	}
	
	function excluir() {
		function filt($match) {
			if($match['valor']!="")
				return true;
			else
				return false;
		}
		//var_dump($this->input->post('vlrapr[][]', TRUE));
		$vlrapr = array_filter($this->input->post('vlrapr[][]', TRUE), 'filt');
				
		$erro = 0;
		$msg_erro = '';
		$sucesso = 0;
		$msg_sucess = '';
		//var_dump($vlrapr);
		//exit();
		
		foreach ($vlrapr as $id => $valor) {
			list($cd['emp'], $cd['pedido'], $cd['filial'], $cd['cod']) = explode("-", $id);
			//echo $cd['emp'].'-'.$cd['pedido'].'-'.$cd['filial'].'<br>';
			//var_dump($cd);
			//exit();
			$sql = "DELETE FROM USU_TADTMOV WHERE USU_ID = ? AND USU_FILIAL = ? and USU_NUMOCP = ? and USU_CODEMP = ?";
			
			$result = $this->transferencias->crud($sql, $cd);
			
			if ($result == FALSE) {
				$erro += 1;
				$msg_erro .= '<div class="alert alert-danger">Erro ao excluir pedido: <strong>'.$cd['pedido'].'</strong> - filial: <strong>'.$cd['filial'].'</strong>. Repita o processo!</div>';
			} else {
				$sucesso += 1;				
			}
						
		}
		
		if ($erro > 0) {
			echo '<legend>Foram encontrados '.$erro.' erro(s)!</legend>';
			echo $msg_erro.'<br>';
		}
		echo '<legend>Foram processados '.$sucesso.' pedido(s)!</legend>';
		if ($sucesso > 0) {
			echo '<div class="alert alert-success small">Exclusão realizada com sucesso!</div>';
		}
		
	}
	
	

}