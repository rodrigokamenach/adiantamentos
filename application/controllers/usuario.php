<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
//we need to call PHP's session object to access it through CI
class Usuario extends CI_Controller {
	function __construct() {
		parent::__construct();
		$this->load->model('user','',TRUE);
		$this->load->model('globals','',TRUE);
		$this->load->library('String');
		$this->load->library('form_validation');
	}
	
	function index() {
		if($this->session->userdata('newadt')) {
			$session_data = $this->session->userdata('newadt');
			$data['usuario'] = $session_data['usuario'];
			$data['usu_permissoes'] = $session_data['usu_permissoes'];
			$data['usu_filial'] = $session_data['usu_filial'];
			$data['usu_email'] = $session_data['usu_email'];
			$data['usu_area'] = $session_data['usu_area'];
			
			$permissoes = explode(';', $data['usu_permissoes']);
			if($permissoes[3] == 'S') {
				$dados = array();
				$dados['results'] = $this->user->listar();
				$dados['filiais'] = $this->globals->lista_filial();
				$dados['areas'] = $this->globals->lista_area(null);
	
				if($dados) {				
					$this->load->view('header_view', $data);
					$this->load->view('usuario_view', $dados);
					$this->load->view('footer_view');
						
				} else {
					return false;
				}
			} else {
				redirect('home', 'refresh');
			}
		} else {
			//If no session, redirect to login page
			redirect('login', 'refresh');
		}
	}
	
	function salvar() {
		
		$this->form_validation->set_message('required', '<div class="alert alert-danger">O campo %s é obrigatório</div>');			
		 
		$this->form_validation->set_rules('user', 'Usuário', 'trim|required|callback_check_user');
		//$this->form_validation->set_rules('keypass', 'Senha', 'trim|required');
		$this->form_validation->set_rules('filial[]', 'Filial', 'trim|required');
		$this->form_validation->set_rules('area[]', 'Área', 'trim|required');
		$this->form_validation->set_rules('email', 'Email', 'trim');
		$this->form_validation->set_rules('digitador', 'Digitador', 'trim|required');
		$this->form_validation->set_rules('gestor', 'Gestor', 'trim|required');
		$this->form_validation->set_rules('aprovador', 'Aprovador', 'trim|required');
		$this->form_validation->set_rules('admin', 'Administrador', 'trim|required');
		$this->form_validation->set_rules('consulta', 'Consulta', 'trim|required');
		$this->form_validation->set_rules('sistem', 'Sistema', 'trim|required');
	
		if ($this->form_validation->run() == FALSE)	{
			echo json_encode(array('st'=>1, 'msg' => validation_errors()));
		} else {
			$user = $this->input->post('user');
			//$keypass = sha1($this->input->post('keypass'));
			$filial = $this->input->post('filial[]');
			$area = $this->input->post('area[]');
			$email = $this->input->post('email');
			$digitador = $this->input->post('digitador');
			$gestor = $this->input->post('gestor');
			$aprovador = $this->input->post('aprovador');
			$admin = $this->input->post('admin');
			$consulta = $this->input->post('consulta');
			$sistema = $this->input->post('sistem');
			
			$filial_list = rtrim(implode(',', $filial), ',');
			$area_list = rtrim(implode(',', $area), ',');
			
			$getCodUsu = $this->user->busca_codusu($user);
			//$var_dump($getCodUsu);
			//echo $getCodUsu[0]['CODUSU'];
			//exit();
			$dados_user = array(
					intval($getCodUsu[0]['CODUSU']),
					$user,
					$keypass,
					$filial_list,
					$email,
					$sistema,
					$digitador.';'.$gestor.';'.$aprovador.';'.$admin.';'.$consulta,
					$area_list
			);
	
			$sql = "INSERT INTO usu_tadtusu (USU_CODUSU, USU_NOMUSU, USU_KEYUSU, USU_FILUSU, USU_MAILUS, USU_SISTEM, USU_PERMIS, USU_CODAREA) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
			
			$result = $this->user->inserir($sql, $dados_user);
				
			if ($result == FALSE) {				
				echo json_encode(array('st'=>1, 'msg' => '<div class="alert alert-danger">Erro ao inserir os dados. Repita o processo!</div>'));
			} else {				
				echo json_encode(array('st'=>0, 'msg' => '<div class="alert alert-success">Cadastro realizado com sucesso!</div>'));
			}
		}
	}
	
	function check_user($user) {
		//query the database
		$getCodUsu = $this->user->busca_codusu($user);
		//var_dump($getCodUsu);	
		//echo $getCodUsu[0]['CODUSU'];
		//exit();
		if ($getCodUsu == null) {
			$this->form_validation->set_message('check_user', '<div class="alert alert-danger">Usuário já n�o possui cadastro no Sapiens!</div>');
			return false;
		} else {
			$result = $this->user->verifica_user($getCodUsu[0]['CODUSU']);
			if($result) {
				$this->form_validation->set_message('check_user', '<div class="alert alert-danger">Usuário já cadastrado!</div>');
				return false;
			} else {
				return TRUE;				
			}
		}
	}
	
	function busca_user() {
		$id = $this->input->post("codigo");
		//$id = 'rodrigo.kamenach';
		$result = $this->user->verifica_user($id);	
		foreach ($result as $row) {
			$data = array(
					'USU_CODUSU' => $row->USU_CODUSU,
					'USU_NOMUSU' => $row->USU_NOMUSU,
					'USU_FILUSU' => $row->USU_FILUSU,
					'USU_MAILUS' => $row->USU_MAILUS,
					'USU_SISTEM' => $row->USU_SISTEM,
					'USU_PERMIS' => $row->USU_PERMIS,
					'USU_CODAREA'=> $row->USU_CODAREA
			);
		}
		/*
		 * Após os índices criados para o formato jSon, dou um echo no jsonEcode da array acima.
		 */
		//var_dump($data);
		echo json_encode($data);
	}
	
	function alterar() {
		//$this->load->library('form_validation');
		$this->form_validation->set_message('required', '<div class="alert alert-danger">O campo %s é obrigatório</div>');
			
		$this->form_validation->set_rules('user_ed', 'Usuário', 'trim|required');
		//$this->form_validation->set_rules('keypass_ed', 'Senha', 'trim');
		$this->form_validation->set_rules('filial_ed[]', 'Filial', 'trim|required');
		$this->form_validation->set_rules('area_ed[]', 'Área', 'trim|required');
		$this->form_validation->set_rules('email_ed', 'Email', 'trim');
		$this->form_validation->set_rules('digitador', 'Digitador', 'trim|required');
		$this->form_validation->set_rules('gestor', 'Gestor', 'trim|required');
		$this->form_validation->set_rules('aprovador', 'Aprovador', 'trim|required');
		$this->form_validation->set_rules('admin', 'Administrador', 'trim|required');
		$this->form_validation->set_rules('funcional', 'Funcional', 'trim|required');
		$this->form_validation->set_rules('consulta', 'Consulta', 'trim|required');
		$this->form_validation->set_rules('sistema_ed', 'Sistemas', 'trim|required');
	
		if ($this->form_validation->run() == FALSE)	{
			$data['st'] = 1;
			$data['msg'] = validation_errors();
			echo json_encode($data);
		} else {
			$user = strtoupper($this->input->post('user_ed'));
			//$keypass = $this->input->post('keypass_ed');
			$filial = $this->input->post('filial_ed[]');
			$area = $this->input->post('area_ed[]');
			$email = $this->input->post('email_ed');
			$digitador = $this->input->post('digitador');
			$gestor = $this->input->post('gestor');
			$aprovador = $this->input->post('aprovador');
			$admin = $this->input->post('admin');
			$funcional = $this->input->post('funcional');
			$consulta = $this->input->post('consulta');
			$sistema = $this->input->post('sistema_ed');
			$filial_list = rtrim(implode(',', $filial), ',');
			$area_list = rtrim(implode(',', $area), ',');
			if (empty($digitador)) {
				$digitador = 'N';
			}
			if (empty($gestor)) {
				$gestor = 'N';
			}
			if (empty($aprovador)) {
				$aprovador = 'N';
			}
			if (empty($admin)) {
				$admin = 'N';
			}
			if (empty($funcional)) {
				$funcional = 'N';
			}
			
			if (empty($consulta)) {
				$consulta = 'N';
			}
			
			$perms = $digitador.';'.$gestor.';'.$aprovador.';'.$admin.';'.$funcional.';'.$consulta;
			//echo json_encode(array('st'=>1, 'msg' => var_dump($keypass)));
			//exit();	
			//var_dump($keypass);
			//exit();		
			//if (empty($keypass)) {
			//	$sql = "UPDATE mgcli.est_adt_usuario SET USU_ST_FILIAL = ?, USU_ST_EMAIL = ?, USU_ST_DIGITADOR = ?, USU_ST_GESTOR = ?, USU_ST_APROVADOR = ?,  USU_ST_ADMIN = ? WHERE UPPER(USU_ST_CODIGO) = ?";
			/*	$dados_user = array(											
						$filial_list,
						$email,
						$digitador,
						$gestor,
						$aprovador,
						$admin,
						$user
				);*/
			//} else {
				$sql = "UPDATE usu_tadtusu SET USU_FILUSU = ?, USU_MAILUS = ?, USU_PERMIS = ?, USU_SISTEM = ?, USU_CODAREA = ? WHERE UPPER(USU_NOMUSU) = ?";
				$dados_user = array(												
						$filial_list,
						$email,
						$perms,
						$sistema,
						$area_list,
						$user
				);
			//}			
			//var_dump($dados_user);
			//exit();
			$result = $this->user->atualiza($sql, $dados_user);
			//echo json_encode(array('st'=>1, 'msg' => $dados_user));
			//var_dump($result);
			//exit();
			if ($result == FALSE) {
				echo json_encode(array('st'	=> 1, 'msg'	=> '<div class="alert alert-danger">Erro ao alterar os dados. Repita o processo!</div>'));				
			} else {
				echo json_encode(array('st' => 0, 'msg' => '<div class="alert alert-success">Alteração realizada com sucesso!</div>'));				
			}
		}
	}
	
	function muda_senha() {
		$this->load->library('form_validation');
		$this->form_validation->set_message('required', '<div class="alert alert-danger">O campo %s é obrigatório</div>');
		$this->form_validation->set_message('matches', '<div class="alert alert-danger">A senha dos campos %s e %s devem ser iguais!</div>');
			
		$this->form_validation->set_rules('id', 'Usuário', 'trim|required');
		$this->form_validation->set_rules('atual', 'Senha Atual', 'trim|required|callback_check_atual['.$this->input->post('id').']');
		$this->form_validation->set_rules('nova', 'Nova Senha', 'trim|required|matches[confirma]');
		$this->form_validation->set_rules('confirma', 'Confirmação', 'trim|required');
		
		if ($this->form_validation->run() == FALSE)	{
			echo json_encode(array('st' => 1, 'msg' => validation_errors()));
		} else {
			$id = strtoupper($this->string->Rmacento($this->input->post('id')));
			$nova = base64_encode($this->input->post('nova'));
			
			$dados_md_senha = array(
					$nova,
					$id
			);	
			
			$sql = "UPDATE mgcli.est_adt_usuario SET USU_ST_SENHA = ? WHERE UPPER(USU_ST_CODIGO) = ?";
			
			$result = $this->user->atualiza($sql, $dados_md_senha);
			
			if ($result == FALSE) {
				echo json_encode(array('st'=>1, 'msg' => '<div class="alert alert-danger">Erro ao alterar os dados. Repita o processo!</div>'));
			} else {
				echo json_encode(array('st'=>0, 'msg' => '<div class="alert alert-success">Alteração realizada com sucesso!</div>'));
			}
		}
		
	}
	
	function check_atual($atual, $id) {		
		$result = $this->user->login($id, $atual);		
		if ($result) {
			return TRUE;
		} else {
			$this->form_validation->set_message('check_atual', '<div class="alert alert-danger">A senha atual digitada não confere!</div>');
			return false;			
		}
		
	}
	
}