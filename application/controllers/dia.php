<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
//we need to call PHP's session object to access it through CI
class Dia extends CI_Controller {
	function __construct() {
		parent::__construct();
		$this->load->model('dias','',TRUE);
		$this->load->model('globals','',TRUE);
		$this->load->library('String');		
	}
	
	function index($year = null, $month = null) {
		if($this->session->userdata('newadt')) {
			$session_data = $this->session->userdata('newadt');
			$data['usuario'] = $session_data['usuario'];
			$data['usu_permissoes'] = $session_data['usu_permissoes'];
			$data['usu_filial'] = $session_data['usu_filial'];
			$data['usu_email'] = $session_data['usu_email'];
			$data['usu_codigo'] = $session_data['usu_codigo'];
			
			if($year == null) {
				$year = date('Y');
			}
			if($month == null) {
				$month = date('m');
			}
									
			
			$config['day_type'] = 'short';
			$config['start_day'] = 'monday';
			$config['month_type'] = 'long';
			$config['show_next_prev'] = true;
			$config['next_prev_url'] = base_url('index.php/dia/index');
			$config['template'] = '			
			{table_open}<table border="0" cellpadding="0" cellspacing="0" class="calendar table table-condensed">{/table_open}

			{heading_row_start}<tr class="row-fluid">{/heading_row_start}

			{heading_previous_cell}<th><a href="{previous_url}"><i class="fa fa-angle-double-left fa-3x"></i></a></th>{/heading_previous_cell}
		    {heading_title_cell}<th colspan="{colspan}"><h2 class="text-center">{heading}</h2></th>{/heading_title_cell}
		    {heading_next_cell}<th class="text-right"><a href="{next_url}"><i class="fa fa-angle-double-right fa-3x"></i></a></th>{/heading_next_cell}
													
			{heading_row_end}</tr>{/heading_row_end}
					
			{week_row_start}<tr>{/week_row_start}
		    {week_day_cell}<th class="day_header cabecalho">{week_day}</th>{/week_day_cell}
		    {week_row_end}</tr>{/week_row_end}				    

			{cal_row_start}<tr>{/cal_row_start}
   			{cal_cell_start}<td>{/cal_cell_start}
			
			{cal_cell_content}
				<div class="day_listing"><span class="badge badge-inverse">{day}</span></div>
                <div class="content text-center">{content}</div>
             {/cal_cell_content}
					
   			{cal_cell_content_today}
				<div class="today"><span class="badge badge-warning">{day}</span>
                <div class="content text-center">{content}</div>
				</div>
            {/cal_cell_content_today}

   			{cal_cell_no_content}
				<div class="day_listing">{day}</div>
				<div class="content text-center"><a href="javascript:;" class="btn btn-info btn-lg" onclick="jCadDia({day},'.$month.','.$year.')"><i class="fa fa-plus-square"></i> Abrir</a></div>					             
            {/cal_cell_no_content}
					
   			{cal_cell_no_content_today}
				<div class="today">{day}
				<div class="content text-center"><a href="javascript:;" class="btn btn-info btn-lg" onclick="jCadDia({day},'.$month.','.$year.')"><i class="fa fa-plus-square"></i> Abrir</a></div>
				</div>														              
            {/cal_cell_no_content_today}		
					
			{cal_cell_blank}&nbsp;{/cal_cell_blank}

		    {cal_cell_end}</td>{/cal_cell_end}
		    {cal_row_end}</tr>{/cal_row_end}
		
		    {table_close}</table>{/table_close}		   
							   
			'; 					
			//$config[''] =
			//$config[''] =			
			
			$this->load->library('calendar', $config);
			
			$dados = array();
			$info = array();
			$results = $this->dias->listar($year, $month);
			//var_dump($results);
			foreach ($results as $row) {
				$iddt	= $row->USU_CODDIA;
				$dia 	= ltrim($row->USU_DT, "0");						
				$status = $row->USU_STDIA;

				if ($status == 'A') {
					$info[$dia] = '<a href="javascript:;" class="btn btn-success btn-lg" onclick="jEdDia('.$iddt.')"><i class="fa fa-chevron-circle-down"></i> Aberto</a>';
				} elseif ($status == 'F') {
					$info[$dia] = '<a href="javascript:;" class="btn btn-danger btn-lg" onclick="jEdDia('.$iddt.')"><i class="fa fa-times-circle"></i> Fechado</a>';
				}
			}
			
			$dados['user_codigo'] = $data['usu_codigo'];
			$dados['calendario'] = $this->calendar->generate($year, $month, $info);
						
			if($dados) {				
				$this->load->view('header_view', $data);
				$this->load->view('dia_view', $dados);
				$this->load->view('footer_view');
					
			} else {
				return false;
			}
	
		} else {
			//If no session, redirect to login page
			redirect('login', 'refresh');
		}
	}
	
	function busca_dia() {
		$id = $this->input->post("codigo");
		//$id = 'rodrigo.kamenach';
		$result = $this->dias->verifica_dia($id);
		foreach ($result as $row) {
			$data = array(
				'USU_CODDIA'=> $row->USU_CODDIA,
				'USU_DT'	=> $row->USU_DT,
				'USU_STDIA' => $row->USU_STDIA					
			);
		}
		/*
		 * Após os índices criados para o formato jSon, dou um echo no jsonEcode da array acima.
		 */
		echo json_encode($data);
	}
	
	function salvar() {
		$this->load->library('form_validation');
		$this->form_validation->set_message('required', '<div class="alert alert-danger">O campo %s � obrigat�rio</div>');
					
		$this->form_validation->set_rules('status', 'Status', 'trim|required');
		$this->form_validation->set_rules('cd_user', 'Usu�rio', 'trim|required');		
	
		if ($this->form_validation->run() == FALSE)	{
			echo json_encode(array('st'=>1, 'msg' => validation_errors()));
		} else {
			
			$dt_dia = $this->input->post('dt_dia');
			$status = $this->input->post('status');
			$cd_user = $this->input->post('cd_user');			
											
			$dados_dia = array(
					$dt_dia,
					$status,
					intval($cd_user)					
			);
	
			$sql = "INSERT INTO USU_TADTDIA (USU_DT, USU_STDIA, USU_DTABE, USU_CODABE) VALUES (?, ?, TO_CHAR(SYSDATE,'DD/MM/YYYY'), ?)";
				
			$result = $this->dias->crud($sql, $dados_dia);
	
			if ($result == FALSE) {
				echo json_encode(array('st'=>1, 'msg' => '<div class="alert alert-danger">Erro ao inserir os dados. Repita o processo!</div>'));
			} else {
				echo json_encode(array('st'=>0, 'msg' => '<div class="alert alert-success">Cadastro realizado com sucesso!</div>'));
			}
		}
	}
	
	function alterar() {
		$this->load->library('form_validation');
		$this->form_validation->set_message('required', '<div class="alert alert-danger">O campo %s é obrigatório</div>');
					
		$this->form_validation->set_rules('status_ed', 'Status', 'trim|required');
		$this->form_validation->set_rules('codigo', 'Código', 'trim|required');
		$this->form_validation->set_rules('cd_user_ed', 'Usu�rio', 'trim|required');
	
		if ($this->form_validation->run() == FALSE)	{
			echo json_encode(array('st'=>1, 'msg' => validation_errors()));
		} else {
				
			$dt_dia = $this->input->post('dt_dia_ed');
			$status = $this->input->post('status_ed');
			$codigo = $this->input->post('codigo');
			$cd_user = $this->input->post('cd_user_ed');
						
			
			if($status == 'A') {
				$dados_dia = array(
						$status,
						$codigo
				);
				
				$sql = "UPDATE USU_TADTDIA SET USU_STDIA = ? WHERE USU_CODDIA = ?";
			} elseif ($status == 'F') {
				$dados_dia = array(
						$status,
						intval($cd_user),
						intval($codigo)
				);
				
				$sql = "UPDATE USU_TADTDIA SET USU_STDIA = ?, USU_DTFEC = TO_CHAR(SYSDATE,'DD/MM/YYYY'), USU_CODFEC = ? WHERE USU_CODDIA = ?";
			}			
	
			$result = $this->dias->crud($sql, $dados_dia);
	
			if ($result == FALSE) {
				echo json_encode(array('st'=>1, 'msg' => '<div class="alert alert-danger">Erro ao inserir os dados. Repita o processo!</div>'));
			} else {
				echo json_encode(array('st'=>0, 'msg' => '<div class="alert alert-success">Cadastro realizado com sucesso!</div>'));
			}
		}
	}
}