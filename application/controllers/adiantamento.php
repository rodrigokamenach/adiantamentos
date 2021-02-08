<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
//we need to call PHP's session object to access it through CI
class Adiantamento extends CI_Controller {
	//CARREGA OS MODELS
	function __construct() {
		parent::__construct();	
		$this->load->model('globals','',TRUE);
		$this->load->model('adiantamentos','',TRUE);
		$this->load->model('previstos','',TRUE);
		$this->load->model('dias','',TRUE);
		$this->load->library('pagination');
		$this->load->library('String');
	}
	
	//FUNCAO DE INICIO DA PAGINA CARREGA OS PEDIDO LANÃ‡ADOS QUE NAO FORAM APROVADOS COM OS BOTOES DE MANUTENÃ‡AO INDIVIDUAL
	function index() {
		if($this->session->userdata('newadt')) {
			//COMANDOS DE SESSAO DO USUARIO
			$session_data = $this->session->userdata('newadt');
			$data['usuario'] = $session_data['usuario'];
			$data['usu_permissoes'] = $session_data['usu_permissoes'];
			$data['usu_filial'] = $session_data['usu_filial'];
			$data['usu_email'] = $session_data['usu_email'];
			$data['usu_codigo'] = $session_data['usu_codigo'];
			$data['usu_area'] = $session_data['usu_area'];
			
			//CAMPO DE BUSCA VERIFICA SE EXISTE E TRATA
			if ($this->input->post('bvalor')) {
				$bvalor = $this->input->post('bvalor');
				$bvalor = str_replace ("'", "", $bvalor);				
			} else {
				$bvalor = '';
			}
			
			if ($this->input->post('dtadt')) { 
				$dia = $this->input->post('dtadt');
			} else {
				$dia = date('d/m/Y');
			}
			
			if ($this->input->post('pedido')) {
				$pedido = $this->input->post('pedido');
			} else {
				$pedido = '';
			}
					
			
			//CONFIGURACAO PARA A PAGINAÃ‡AO DOS REGISTROS
			$config['base_url'] = base_url('index.php/adiantamento/index');
			$config['total_rows'] = $this->adiantamentos->conta($bvalor, $dia, $pedido);
			//var_dump($config['total_rows']);
			$config['per_page'] = 100;
			$config["uri_segment"] = 3;
			$choice = $config["total_rows"] / $config["per_page"];
			$config["num_links"] = 2;
			
			//config for bootstrap pagination class integration
			$config['full_tag_open'] = '<div><ul class="pagination pagination-small pagination-centered">';
			$config['full_tag_close'] = '</ul></div>';	
					
			$config['first_link'] = false;
			$config['last_link'] = false;
			$config['first_tag_open'] = '<li>';
			$config['first_tag_close'] = '</li>';
			$config['prev_link'] = '&laquo';
			$config['prev_tag_open'] = '<li class="prev">';
			$config['prev_tag_close'] = '</li>';
			$config['next_link'] = '&raquo';
			$config['next_tag_open'] = '<li>';
			$config['next_tag_close'] = '</li>';
			$config['last_tag_open'] = '<li>';
			$config['last_tag_close'] = '</li>';
			$config['cur_tag_open'] = '<li class="active"><a href="#">';
			$config['cur_tag_close'] = '</a></li>';
			$config['num_tag_open'] = '<li>';
			$config['num_tag_close'] = '</li>';				
			
			$dados = array();			
			$this->pagination->initialize($config);
			$dados['page'] = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;										
			$dados['pagination'] = $this->pagination->create_links();											
			$dados['results'] = $this->adiantamentos->listar($config["per_page"], $dados['page'], $bvalor, $dia, $pedido);
			//var_dump($dados['results']);
			$dados['filiais'] = $this->globals->lista_filial();
			$dados['areas'] = $this->globals->lista_area($data['usu_area']);
			$dados['user_codigo'] = $data['usu_codigo'];

			//CARREGA AS VIEWS COM OS REGISTROS
			if($dados) {
				$this->load->view('header_view', $data);
				$this->load->view('adiantamento_view', $dados);
				$this->load->view('footer_view');
					
			} else {
				return false;
			}
	
		} else {
			//If no session, redirect to login page
			redirect('login', 'refresh');
		}
	}
	
	//FUNCAO PARA GRAVAR UM NOVO ADIANTAMENTO
	function salvar() {
		$this->load->library('form_validation');
		$this->form_validation->set_message('required', '<div class="alert alert-danger">O campo %s é obrigatório</div>');
			
		$this->form_validation->set_rules('dtadt_cad', 'Data', 'trim|required');
		$fil_val = $this->input->post('filial') .'-'.$this->input->post('valor').'- 0';
		$this->form_validation->set_rules('pedido', 'Pedido', 'trim|required|callback_check_pedido['.$fil_val.']');
		$this->form_validation->set_rules('filial', 'Filial', 'trim|required');
		$this->form_validation->set_rules('valor', 'Valor', 'trim|required');
		$this->form_validation->set_rules('area', 'Área', 'trim|required');
		$this->form_validation->set_rules('prioriza', 'Prioriza', 'trim');
		$this->form_validation->set_rules('obs', 'Observação', 'trim');
		$this->form_validation->set_rules('codigo', 'Usuário', 'trim|required|is_natural_no_zero');
	
		if ($this->form_validation->run() == FALSE)	{
			echo json_encode(array('st'=>1, 'msg' => validation_errors()));
		} else {
			$data_adt = $this->input->post('dtadt_cad');
			$pedido = $this->input->post('pedido');
			$filial = $this->input->post('filial');
			$valor = str_replace(".","",$this->input->post('valor'));
			$area = $this->input->post('area');
			$prioriza = $this->input->post('prioriza');
			$usu_codigo = $this->input->post('codigo');
			$tam = strlen($filial);
			
			if ($tam == 3) {			
				$emp = substr($filial, 0, 1);
			} else {
				$emp = substr($filial, 0, 2);
			}
			//var_dump($emp);			
			$continua = 0;
			//SE A OBS EXISTE REMOVE OS ACENTOS E COLOCA TUDO EM MAIUSCULO			
			if($prioriza == 1) {
				$obs = utf8_encode(strtoupper($this->string->Rmacento($this->input->post('obs'))));
			} else {
				$obs = $this->input->post('obs');
				$prioriza = 0;
			}
						
			//VERIFICA DIA
			$dia = $this->dias->check_india($data_adt);			
			
			if($dia[0]['USU_STDIA'] == null) {
				$status = 'A';
			
				$dados_dia = array(
						$data_adt,
						$status,
						intval($usu_codigo)
				);
					
				$sql = "INSERT INTO USU_TADTDIA (USU_DT, USU_STDIA, USU_DTABE, USU_CODABE) VALUES (?, ?, TO_CHAR(SYSDATE,'DD/MM/YYYY'), ?)";
					
				$result = $this->dias->crud($sql, $dados_dia);
			} else {
				if ($dia[0]['USU_STDIA'] == 'F') {					
					echo json_encode(array('st'=>1, 'msg' => '<div class="alert alert-danger small">O dia '.$data_adt.' está fechado. Não é possível lançar um adiantamento para um dia fechado.</div>'));
					$continua = 0;					
				} else {
					$continua = 1;
				}
			}
			
			if($continua == 1) {
				/*$continua = 0;
				$check_prv = $this->previstos->checkprv($data_adt, $area);
				
				//var_dump($check_prv);
				//if ($check_prv) {
					$vlradt = floatval(str_replace(",",".",str_replace(".","",$check_prv[0]['VLRADT'])));
					$vlrtotal = floatval(str_replace(",",".",str_replace(".","",$check_prv[0]['VLRTOTAL'])));
					$vlr = floatval(str_replace(",",".",str_replace(".","",$valor)));
					
					$soma = $vlradt + $vlr;
					$saldo_area = $vlrtotal - $vlradt;
					
					if ($soma <= $vlrtotal) {
						$continua = 1;
					} else {
						echo json_encode(array('st'=>1, 'msg' => '<div class="alert alert-danger">O valor digitado ultrapassa o valor previsto para essa data na área selecionada! Saldo: '.$saldo_area.'</div>'));
						$continua = 0;
						exit();
					}
				//} else {
					//echo json_encode(array('st'=>1, 'msg' => '<div class="alert alert-danger">Erro ao verificar as previsões. Verifique se existe alguma previsão cadastrada ou tente novamente.</div>'));
					//exit();
				//}*/
			//ORGANIZA VALORES PARA INSERIR
				if($continua == 1) {
					//echo 'ok';
					//exit();
					$dados_adt = array(
							$pedido,
							$filial,
							$data_adt,
							$valor,					
							intval($usu_codigo),
							$prioriza,
							$obs,
							$area,
							$emp
					);
		
					$sql = "INSERT INTO USU_TADTMOV (USU_NUMOCP, USU_FILIAL, USU_DTLANC, USU_VLRADT, USU_LANCUSU, USU_ADTPRI, USU_OBS, USU_AREA, USU_CODEMP) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
				//FAZ O INSERT NO BANCOS	
					$result = $this->adiantamentos->crud($sql, $dados_adt);						
					
					
					if ($result == FALSE) {
						echo json_encode(array('st'=>1, 'msg' => '<div class="alert alert-danger">Erro ao inserir os dados. Repita o processo!</div>'));
					} else {
						echo json_encode(array('st'=>0, 'msg' => '<div class="alert alert-success">Adiantamento cadastrado com sucesso!</div>'));
					}
				} else {
					exit();
				}
			}
		}
	}
	
	//VALIDAÃ‡OES PARA INCLUSAO DE UM NOVO ADIANTAMENTO, SE JA EXISTE, CALCULO DO SALDO
	function check_pedido($pedido, $fil_val) {
		
		list($filial, $valor, $valor_ant) = explode('-', $fil_val);
		
		if(empty($valor_ant)) {
			$valor_ant = 0;
		}
		
		$resit = $this->adiantamentos->sitped($filial, $pedido);
		
		if ($resit) {
			
			if ($resit[0]['SITOCP'] == 4 and ($resit[0]['OCPNFCP'] <> NULL or $resit[0]['OCPNFCS'] <> NULL)) {
				$this->form_validation->set_message('check_pedido', '<div class="alert alert-danger">Pedido Liquidado, já possui Nota Fiscal lançada! Não é possível lançá-lo.</div>');
				return false;				
			}
			//echo $pedido;
			//PEGA DADOS DO PEDIDO
			$result = $this->adiantamentos->verifica_pedido($pedido, $filial);
			//var_dump($result);
			//exit();
			if ($result) {
		
				$vlr_pedido = str_replace(",",".",$result[0]['VLRLIQ']);
				$vlr_abe = str_replace(",",".",$result[0]['VLRABE']);
				$vlr_adt = str_replace(",",".",$result[0]['USU_VLRADT']);
				$vlr_apr = str_replace(",",".",$result[0]['USU_VLRAPR']);
				//$aberto = $result[0]['ABERTO'];
				$valor = str_replace(',', '.', str_replace(".","",$valor));				
			
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
																			
					} elseif (floatval($vlr_abe) > 0) {
					
						if ($vlr_adt == null or floatval($vlr_adt) == 0) {
							$saldo = $vlr_abe;
						} else {
							if ($vlr_apr == null or floatval($vlr_apr) == 0) {
								$saldo = $vlr_abe - $vlr_adt;
							} elseif (floatval($vlr_apr) > 0 and $vlr_pedido > $vlr_apr) {
								$saldo = $vlr_pedido - $vlr_abe;
							} elseif ($vlr_abe > $vlr_pedido) {
								$saldo = 0;
							} elseif ($vlr_apr >= $vlr_pedido) {
								$saldo = 0;
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
				
					if ($valor > $valor_ant) {
						$saldo = $saldo - ($valor - $valor_ant);
					} elseif ($valor < $valor_ant) {
						$saldo = $saldo + ($valor_ant - $valor);
					} elseif ($valor == $valor_ant) {
						$saldo = $valor;
					}
				}
				//echo $valor_ant;
				//echo $valor;
				//echo $saldo;
				//exit();
				if($saldo+0.01 >= $valor) {					
					return TRUE;			
				} else {
					if($saldo < 0) {
						$this->form_validation->set_message('check_pedido', '<div class="alert alert-danger">A soma dos valores lançados no sistema adiantamento para esse pedido ultrapassam o valor total do pedido '.$pedido.': Valor Pedido: R$ '.$vlr_pedido.' - Valor Adt: R$ '.$vlr_adt.'</div>');
						return false;
					} else {			
						$this->form_validation->set_message('check_pedido', '<div class="alert alert-danger">O saldo disponível para o pedido '.$pedido.' é de R$ '.number_format($saldo,2,",",".").'</div>');
						return false;
					}
					return false;			
				}
			} else {			
				$this->form_validation->set_message('check_pedido', '<div class="alert alert-danger">Pedido não encontrado! Verifique se o pedido está FECHADO e sua situação está como: Aberto Total ou Aberto Parcial</div>');
				return false;
			}
		} else {
			$this->form_validation->set_message('check_pedido', '<div class="alert alert-danger">Pedido não encontrado! Verifique se o pedido está FECHADO e sua situação está como: Aberto Total ou Aberto Parcial</div>');
			return false;
		}
	}
	
	//BUSCA DADOS DO ADIANTAMENTO PARA ALTERACAO
	function busca_adt() {
		$fil = $this->input->post("filial");
		$oc = $this->input->post("oc");
		$id = $this->input->post("id");
		$result = $this->adiantamentos->verifica_adt($fil, $oc, $id);
		//var_dump($result);
		foreach ($result as $row) {
			$data = array(					
					'USU_NUMOCP'=> $row->USU_NUMOCP,
					'USU_FILIAL'=> $row->USU_FILIAL,
					'USU_DTLANC'=> $row->USU_DTLANC,
					'USU_VLRADT'=> $row->USU_VLRADT,
					'USU_ADTPRI'=> $row->USU_ADTPRI,
					'USU_OBS'   => utf8_decode($row->USU_OBS),
					'USU_AREA'	=> $row->USU_AREA,
					'USU_CODEMP'=> $row->USU_CODEMP,
					'USU_ID'	=> $row->USU_ID
			);
		}
		/*
		 * ApÃ³s os Ã­ndices criados para o formato jSon, dou um echo no jsonEcode da array acima.
		 */
		echo json_encode($data);
	}
	
	function busca_for($oc, $fil) {
		//$fil = $this->input->post("filial");
		//$oc = $this->input->post("oc");
		
		$fornecedor = $this->adiantamentos->getFor($oc, $fil);
		//var_dump($fornecedor);
		if ($fornecedor) {	
			$resultfor = $this->adiantamentos->getSitFor($fornecedor[0]['CODFOR']);
		//var_dump($resultfor);
		//exit();		
		//var_dump($result);
			if ($resultfor) {
				foreach ($resultfor as $row) {
					$fornec = array(
							'CODFOR' => $row->CODFOR,
							'APEFOR' => $row->APEFOR,
							'VLRABE' => $row->VLRABE					
					);
				}
			} else {
				$fornec = array(
							'CODFOR' => $fornecedor[0]['CODFOR'],
							'APEFOR' => $fornecedor[0]['APEFOR'],
							'VLRABE' => 0					
					);
			}
		} else {
			$fornec = array(
							'CODFOR' => NULL,
							'APEFOR' => NULL,
							'VLRABE' => NULL					
					);
		}
		/*
		 * ApÃ³s os Ã­ndices criados para o formato jSon, dou um echo no jsonEcode da array acima.
		 */		
		echo json_encode($fornec);
	}
	
	//GRAVA A ALTERAÃ‡AO DO ADIANTAMENTO
	function alterar() {
		$this->load->library('form_validation');
		$this->form_validation->set_message('required', '<div class="alert alert-danger">O campo %s é obrigatório</div>');

		//VALIDAÃ‡OES DO FORM
		$this->form_validation->set_rules('dtadt_ed', 'Data', 'trim|required');
		$fil_val = $this->input->post('filial_ed') .'-'.$this->input->post('valor_ed').'-'.$this->input->post('valor_ant');
		$this->form_validation->set_rules('pedido_ed', 'Pedido', 'trim|required|callback_check_pedido['.$fil_val.']');
		$this->form_validation->set_rules('filial_ed', 'Filial', 'trim|required');
		$this->form_validation->set_rules('valor_ed', 'Valor', 'trim|required');
		$this->form_validation->set_rules('area_ed', 'Área', 'trim|required');
		$this->form_validation->set_rules('prioriza_ed', 'Prioriza', 'trim');
		$this->form_validation->set_rules('obs_ed', 'Observação', 'trim');
	
		if ($this->form_validation->run() == FALSE)	{
			echo json_encode(array('st'=>1, 'msg' => validation_errors()));
		} else {
			$data_adt = $this->input->post('dtadt_ed');
			$pedido = $this->input->post('pedido_ed');
			$filial = $this->input->post('filial_ed');
			$valor = $this->input->post('valor_ed');
			$area = $this->input->post('area_ed');
			$id = $this->input->post('id_ed');
			$prioriza = $this->input->post('prioriza_ed');
			if ($this->input->post('emp_ped') != null) {
				$emp = $this->input->post('emp_ped');
			} else {
				$emp = substr($filial, 0, 1);
			}
			$usu_codigo = $this->input->post('codigo_ed');
			$continua = 0;
			//SE A OBS EXISTE REMOVE OS ACENTOS E COLOCA TUDO EM MAIUSCULO
			if($prioriza == 1) {
				$obs = utf8_encode(strtoupper($this->string->Rmacento($this->input->post('obs_ed'))));
			} else {
				$obs = $this->input->post('obs_ed');
			}

			
			//VERIFICA DIA
			$dia = $this->dias->check_india($data_adt);

			if($dia[0]['USU_STDIA'] == null) {
				$status = 'A';
					
				$dados_dia = array(
						$data_adt,
						$status,
						intval($usu_codigo)
				);
					
				$sql = "INSERT INTO USU_TADTDIA (USU_DT, USU_STDIA, USU_DTABE, USU_CODABE) VALUES (?, ?, TO_CHAR(SYSDATE,'DD/MM/YYYY'), ?)";
					
				$result = $this->dias->crud($sql, $dados_dia);
			} else {
				if ($dia[0]['USU_STDIA'] == 'F') {
					echo json_encode(array('st'=>1, 'msg' => '<div class="alert alert-danger small">O dia '.$data_adt.' está fechado. Não é possível lançar um adiantamento para um dia fechado.</div>'));
					$continua = 0;
					exit();
				} else {
					$continua = 1;
				}
			}					
			
			if($continua == 1) {	
				$dados_adt = array(
						$data_adt,
						$valor,					
						$prioriza,
						$obs,
						$area,
						$pedido,
						$filial,
						$emp,
						$id
				);
				//var_dump($dados_adt);
				//exit();
	
				$sql = "UPDATE USU_TADTMOV SET USU_DTLANC = ?, USU_VLRADT = ?, USU_ADTPRI = ?, USU_OBS = ?, USU_AREA = ? WHERE USU_NUMOCP = ? AND USU_FILIAL = ? AND USU_CODEMP = ? AND USU_ID = ?";
				
				$result = $this->adiantamentos->crud($sql, $dados_adt);
				
				if ($result == FALSE) {
					echo json_encode(array('st'=>1, 'msg' => '<div class="alert alert-danger">Erro ao inserir os dados. Repita o processo!</div>'));
				} else {
					echo json_encode(array('st'=>0, 'msg' => '<div class="alert alert-success">Adiantamento alterado com sucesso!</div>'));
				}
			}
		}
	}
	
	function deletar() {
		$fil = $this->input->post('fil');
		$oc = $this->input->post('oc');
		$id = $this->input->post('id');
		//var_dump($fil);
		//var_dump($oc);
		//exit();
		$dados_del = array(
				$fil,
				$oc,
				$id
		);
		$sql = "DELETE FROM USU_TADTMOV WHERE USU_FILIAL = ? AND USU_NUMOCP = ? AND USU_ID = ?";
		$result = $this->adiantamentos->crud($sql, $dados_del);
	
		if($result) {
			return false;
		} else {
			return true;
		}
	}
}