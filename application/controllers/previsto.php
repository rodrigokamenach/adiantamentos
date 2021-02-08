<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
//we need to call PHP's session object to access it through CI
class Previsto extends CI_Controller {
	
	function __construct() {
		parent::__construct();
		$this->load->model('globals','',TRUE);
		$this->load->model('previstos','',TRUE);
		$this->load->model('dias','',TRUE);
		$this->load->library('String');
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
			
			
			$dados = array();
			$dados['filiais'] = $this->globals->lista_filial();
			$dados['areas'] = $this->globals->lista_area($data['usu_area']);
			$dados['results'] = $this->previstos->listar();
			$dados['user_codigo'] = $data['usu_codigo'];
			
			if($dados) {
				$this->load->view('header_view', $data);
				$this->load->view('previsto_view', $dados);
				$this->load->view('footer_view');
					
			} else {
				return false;
			}
			
		} else {
			//If no session, redirect to login page
			redirect('login', 'refresh');
		} 
	}
	
	function salvar() {
		$this->load->library('form_validation');
		$this->form_validation->set_message('required', '<div class="alert alert-danger">O campo %s é obrigatório</div>');
			
		$this->form_validation->set_rules('dtini', 'Data', 'trim|required');
		$this->form_validation->set_rules('dtfim', 'Data', 'trim|required|callback_compareDates['.$this->input->post('dtini').']');
		$this->form_validation->set_rules('filial', 'Filial', 'trim|required');
		$this->form_validation->set_rules($this->input->post('vlrarea[]', TRUE), 'Pagina', 'trim|required');
	
		if ($this->form_validation->run() == FALSE)	{
			echo json_encode(array('st'=>1, 'msg' => validation_errors()));
			exit();
		} else {
			$dtini = $this->input->post('dtini');
			$dtfim = $this->input->post('dtfim');
			$filial = $this->input->post('filial');
			$vlrarea = $this->input->post('vlrarea[]', TRUE);
			
			$valida_prev = $this->previstos->valida_prev($filial);
			$erro = 0;
			$fail = 0;
			$sucess = 0;
			$msg_final = '';
			
			//var_dump($valida_prev);
			//exit();
			if ($valida_prev) {
				foreach ($valida_prev as $row) {
					$vigenini = strtotime(str_replace('/', '-', $row->USU_DATINI));
					$vigenfim = strtotime(str_replace('/', '-', $row->USU_DATFIM));
					
					if ($dtini < $vigenini and $dtini <= $dtfim and $dtfim < $vigenini)  {
						$erro = 0;
					} elseif ($dtini > $vigenfim and $dtini <= $dtfim and $dtfim > $vigenfim) {
						$erro = 0;
					} else {
						$erro += 1;
						//var_dump($dtini);
						//var_dump($dtfim);
						//var_dump($vigenini);
						//var_dump($vigenfim);
					}
				}
				
				if ($erro > 0) {
					echo json_encode(array('st'=>1, 'msg' => '<div class="alert alert-danger small">Já existe um previsão lançada para esse périodo.</div>'));
					exit();
				} else {					
					$dados_cab = array(
						$filial,
						$dtini,
						$dtfim
					);
					
					$sql_cab = "INSERT INTO USU_TADTPREV (USU_CODFIL, USU_DATINI, USU_DATFIM) VALUES (?, ?, ?)";
					
					$result_cab = $this->dias->crud($sql_cab, $dados_cab);
					
					if ($result_cab == FALSE) {
						echo json_encode(array('st'=>1, 'msg' => '<div class="alert alert-danger">Erro ao inserir os dados. Repita o processo!</div>'));
						exit();
					} else {
						foreach ($vlrarea as $key => $vlr) {
							if (empty($vlr)) {
								$vlr = 0;
							}
							
							$vlr = str_replace(".","",$vlr);
							$vlr = str_replace(",",".",$vlr);
							
							$dados_det = array(
								$key,
								floatval($vlr)
							);
							//var_dump($dados_det);
							$sql_det = "INSERT INTO USU_TPREVDET (USU_CODPREV, USU_CODAREA, USU_VLRPRE) VALUES (USU_TADTPREV_SEQ.CURRVAL, ?, ?)";
							
							$result_det = $this->dias->crud($sql_det, $dados_det);
							
							if ($result_det == FALSE) {
								$fail += 1;
								$msg_final .= '<div class="alert alert-danger">Erro ao inserir acesso da valor da área '.$key.'. Repita o processo!</div>';
							} else {
								$sucess += 1;
							}
						}																		
					}					
				}
			} else {
				$dados_cab = array(
						$filial,
						$dtini,
						$dtfim
				);
					
				$sql_cab = "INSERT INTO USU_TADTPREV (USU_CODFIL, USU_DATINI, USU_DATFIM) VALUES (?, ?, ?)";
					
				$result_cab = $this->dias->crud($sql_cab, $dados_cab);
					
				if ($result_cab == FALSE) {
					echo json_encode(array('st'=>1, 'msg' => '<div class="alert alert-danger">Erro ao inserir os dados. Repita o processo!</div>'));
					exit();
				} else {
					foreach ($vlrarea as $key => $vlr) {
						$vlr = str_replace(".","",$vlr);
						$vlr = str_replace(",",".",$vlr);
						
						$dados_det = array(
								$key,
								floatval($vlr)
						);
						//var_dump($dados_det);	
						$sql_det = "INSERT INTO USU_TPREVDET (USU_CODPREV, USU_CODAREA, USU_VLRPRE) VALUES (USU_TADTPREV_SEQ.CURRVAL, ?, ?)";
							
						$result_det = $this->dias->crud($sql_det, $dados_det);
							
						if ($result_det == FALSE) {
							$fail += 1;
							$msg_final .= '<div class="alert alert-danger">Erro ao inserir acesso da valor da área '.$key.'. Repita o processo!</div>';
						} else {
							$sucess += 1;
						}
					}
				
				}
			}
			
			if ($erro > 0) {
				$msg_final .= '<legend>Foram encontrados '.$erro.' erro(s)!</legend>';
				$msg_final .= $msg_final.'<br>';
			}
			if ($sucess > 0) {
				$msg_final .= '<div class="alert alert-success small">Previsões salvas com sucesso!</div>';
			}
				
			echo json_encode(array('st'=>0, 'msg' => $msg_final));
			
			//var_dump($dtini);
			//var_dump($dtfim);
			//var_dump($filial);
			//var_dump($vlrarea);
			
		}
	}
	
	function compareDates($start,  $end) {
	
		$start = strtotime(str_replace('/', '-', $start));
		$end = strtotime(str_replace('/', '-', $end));
		if($end > $start) {
			$this->form_validation->set_message('compareDates', '<div class="alert alert-danger">A data inicial deve ser menor que a final.</div>');
			//$this->form_validation->set_message('compareDates','A data inicial deve ser menor que a final.');
			return false;
		}
	}
	
	function buscaprv($id) {
		$result = $this->previstos->buscaprv($id);
		echo json_encode($result);
	}
	
	function alterar() {
		$this->load->library('form_validation');
		$this->form_validation->set_message('required', '<div class="alert alert-danger">O campo %s é obrigatório</div>');
			
		$this->form_validation->set_rules('cod', 'ID', 'trim|required');		
		$this->form_validation->set_rules($this->input->post('vlrarea_ed[]', TRUE), 'Pagina', 'trim|required');
		
		if ($this->form_validation->run() == FALSE)	{
			echo json_encode(array('st'=>1, 'msg' => validation_errors()));
			exit();
		} else {
			$id = $this->input->post('cod');			
			$vlrarea = $this->input->post('vlrarea_ed[]', TRUE);
			//var_dump($vlrarea);
			//exit();
			$erro = 0;
			$fail = 0;
			$sucess = 0;
			$msg_final = '';
			
			foreach ($vlrarea as $key => $vlr) {
				if (empty($vlr)) {
					$vlr = 0;
				}
				
				$vlr = str_replace(".","",$vlr);
				$vlr = str_replace(",",".",$vlr);
					
				$dados_det = array(						
						floatval($vlr),
						$key,
						$id
				);
				//var_dump($dados_det);
				$sql_det = "UPDATE USU_TPREVDET SET USU_VLRPRE = ? WHERE USU_CODAREA = ? AND USU_CODPREV = ?";
					
				$result_det = $this->dias->crud($sql_det, $dados_det);
					
				if ($result_det == FALSE) {
					$fail += 1;
					$msg_final .= '<div class="alert alert-danger">Erro ao alterar valor da área '.$key.'. Repita o processo!</div>';
				} else {
					$sucess += 1;
				}
			}
			
			if ($erro > 0) {
				$msg_final .= '<legend>Foram encontrados '.$erro.' erro(s)!</legend>';
				$msg_final .= $msg_final.'<br>';
			}
			if ($sucess > 0) {
				$msg_final .= '<div class="alert alert-success small">Previsões salvas com sucesso!</div>';
			}
			
			echo json_encode(array('st'=>0, 'msg' => $msg_final));
			//var_dump($id);			
			//var_dump($vlrarea);
		}
	}
	
	function deletar() {
		$id = $this->input->post('id');
		$success = 0;
		$fail = 0;
		$msg_final = '';
		$sql_det = "DELETE FROM USU_TPREVDET WHERE USU_CODPREV = ?";
		$result_det = $this->dias->crud($sql_det, $id);
		
		if ($result_det) {
			$success += 1;
			$sql_cab = "DELETE FROM USU_TADTPREV WHERE USU_CODPREV = ?";
			$result_cab = $this->dias->crud($sql_cab, $id);
			
			if ($result_cab) {
				$success += 1;
			} else {
				$fail += 1;
			}
		} else {
			$fail += 1;
		}
			//var_dump($result);
			//exit();
		if ($fail > 0) {
			$msg_final .= '<legend>Foram encontrados '.$erro.' erro(s)!</legend>';
			$msg_final .= $msg_final.'<br>';			
		} 
		if ($success > 0) {
			$msg_final .= '<div class="alert alert-success small">Registro excluido com sucesso!</div>';
		}
		
		echo json_encode(array('st'=>0, 'msg' => $msg_final));
				
	}
}
