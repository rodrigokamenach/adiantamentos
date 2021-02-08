<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
//we need to call PHP's session object to access it through CI
class Consulta extends CI_Controller {
	
	function __construct() {
		parent::__construct();
		$this->load->model('globals','',TRUE);
		$this->load->model('consultas','',TRUE);
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
			
			//var_dump($data['usu_filial']);

			if (empty($dia)) {
				$dia = date('d/m/Y');
			}
			
			if (empty($filial)) {
				$filial = null;
			}
			
			if (empty($area)) {
				$area = null;
			}
			
			$dados['filiais'] = $this->globals->lista_filial($data['usu_filial']);
			$dados['fornec'] = $this->globals->lista_fornec();
			$dados['unidade'] = $this->globals->unidade($dia, $filial, $area);
			$dados['area'] = $this->globals->area($dia, $filial, $area);
			$dados['regiao'] = $this->globals->regiao($dia, $filial, $area);
			$dados['areas'] = $this->globals->lista_area($data['usu_area']);
			
			$this->load->view('header_view', $data);
			$this->load->view('consulta_view', $dados);
			$this->load->view('footer_view');
		} else {
			//If no session, redirect to login page
			redirect('login', 'refresh');
		}
	}
	
	
	function carreg_consulta() {
		
		$dtini =  $this->input->post('dtini');
		$dtfim =  $this->input->post('dtfim');
		$dtpg =  $this->input->post('dtpg');
		$dtpgfim =  $this->input->post('dtpgfim');
		$filial =  $this->input->post('filial');
		$area =  $this->input->post('area');
		$pedido = $this->input->post('pedido');
		$situacao = $this->input->post('situacao');
		$recebe = $this->input->post('recebe');
		$codfor = $this->input->post('fornecedor');
		
		$session_data = $this->session->userdata('newadt');		
		if ($filial == '') {
			$filial = $session_data['usu_filial'];
		}
			
		$dados['unidade'] = $this->globals->unidadeif($dtini, $dtfim, $filial, $area, $dtpg, $dtpgfim);
		$dados['regiao'] = $this->globals->regiaoif($dtini, $dtfim, $filial, $area, $dtpg, $dtpgfim);
		$dados['area'] = $this->globals->areaif($dtini, $dtfim, $filial, $area, $dtpg, $dtpgfim);
		$dados['resultado'] = $this->consultas->listar($dtini, $dtfim, $filial, $area, $pedido, $situacao, $recebe, $codfor, $dtpg, $dtpgfim);
		
		if ($dados['resultado'] != null) {
			$heading = array('DATA','PEDIDO','EMPRESA','FILIAL','FORNECEDOR','COMPRADOR','VALOR PEDIDO','VALOR ADIANTAMENTO','VALOR APROVADO','VALOR PAGO','SITUACAO','TITULO','APROVADOR','APLIC/OBS','OBS','AREA','RECEBIMENTO');
				
			$this->load->library('Excel');
				
			$this->excel->setActiveSheetIndex(0);
			$this->excel->getActiveSheet()->setTitle('Consulta Adiantamentos');
				
			$rowNumberH = 1;
			$colH = 'A';
				
			foreach($heading as $h){
				$this->excel->getActiveSheet()->setCellValue($colH.$rowNumberH,$h);
				$this->excel->getActiveSheet()->getColumnDimension($colH)->setAutoSize(true);
				$colH++;
			}
				
			$this->excel->getActiveSheet()->getStyle('A1:Q1')->getFont()->setBold(true);
			$this->excel->getActiveSheet()->getStyle('A1:Q1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
			$this->excel->getActiveSheet()->getStyle('A1:Q1')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			$this->excel->getActiveSheet()->getStyle('A1:Q1')->getFill()->getStartColor()->setRGB('cbded0');
			$this->excel->getActiveSheet()->getStyle('A1:N1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$this->excel->getActiveSheet()->getStyle('P1:Q1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);			
			$this->excel->getActiveSheet()->setAutoFilter('A1:Q1');
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
				$this->excel->getActiveSheet()->getStyle('M'.$rowNumber.'')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$this->excel->getActiveSheet()->getStyle('N'.$rowNumber.'')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$this->excel->getActiveSheet()->getStyle('O'.$rowNumber.'')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$this->excel->getActiveSheet()->getStyle('P'.$rowNumber.'')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$this->excel->getActiveSheet()->getStyle('Q'.$rowNumber.'')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
									
				$this->excel->getActiveSheet()->setCellValue('A'.$rowNumber,$row->USU_DTLANC);
				$this->excel->getActiveSheet()->setCellValue('B'.$rowNumber,$row->USU_NUMOCP);
				$this->excel->getActiveSheet()->setCellValue('C'.$rowNumber,$row->USU_CODEMP);
				$this->excel->getActiveSheet()->setCellValue('D'.$rowNumber,$row->USU_FILIAL.' - '.$row->SIGFIL.' - '.$row->USU_INSTAN);
				$this->excel->getActiveSheet()->setCellValue('E'.$rowNumber,$row->APEFOR);
				$this->excel->getActiveSheet()->setCellValue('F'.$rowNumber,$row->USULANC);
				$this->excel->getActiveSheet()->setCellValue('G'.$rowNumber,str_replace("," , "." , $row->VLRLIQ), 2, ',', '.');				
				$this->excel->getActiveSheet()->setCellValue('H'.$rowNumber,str_replace("," , "." , $row->USU_VLRADT), 2, ',', '.');
				$this->excel->getActiveSheet()->setCellValue('I'.$rowNumber,str_replace("," , "." , $row->USU_VLRAPR), 2, ',', '.');
				$this->excel->getActiveSheet()->setCellValue('J'.$rowNumber,str_replace("," , "." , $row->VLRPAGO), 2, ',', '.');
				$this->excel->getActiveSheet()->setCellValue('K'.$rowNumber,$row->SITUACAO);
				$this->excel->getActiveSheet()->setCellValue('L'.$rowNumber,$row->USU_NUMTIT);
				$this->excel->getActiveSheet()->setCellValue('M'.$rowNumber,$row->USUAPR);
				$this->excel->getActiveSheet()->setCellValue('N'.$rowNumber,$row->USU_OBS);
				$this->excel->getActiveSheet()->setCellValue('O'.$rowNumber,$row->OBSOCP);
				$this->excel->getActiveSheet()->setCellValue('P'.$rowNumber,$row->USU_DESCAREA);
				$this->excel->getActiveSheet()->setCellValue('Q'.$rowNumber,$row->RECB);
				$this->excel->getActiveSheet()->getStyle('G'.$rowNumber)->getNumberFormat()->setFormatCode($currencyFormat);
				$this->excel->getActiveSheet()->getStyle('H'.$rowNumber)->getNumberFormat()->setFormatCode($currencyFormat);
				$this->excel->getActiveSheet()->getStyle('I'.$rowNumber)->getNumberFormat()->setFormatCode($currencyFormat);
				$this->excel->getActiveSheet()->getStyle('J'.$rowNumber)->getNumberFormat()->setFormatCode($currencyFormat);
				$rowNumber++;
				
				$total = $rowNumber;
			}
			//$ultima =  $total;
			//$total = $total+1;
			//$this->excel->getActiveSheet()->setCellValue('G'.$total,'=SOMA(G2:G'.$ultima.')');
			
			$filename = 'Consulta_Adiantamentos'; //save our workbook as this file name
				
			$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
			$objWriter->save(str_replace(__FILE__,APPPATH.'xls/'.$filename.'.xls',__FILE__));
			
			$dados['exporta'] = '<a class="btn btn-primary" href="'.base_url().'index.php/exporta/excel/'.$filename.'"><i class="fa fa-file-excel-o"></i> Exportar Excel</a>';						
		} else {
			$dados['exporta'] = '';
		}
			
		$data['uni'] = $this->load->view('unidade_view', $dados);
		$data['are'] = $this->load->view('area_view', $dados);
		$data['reg'] = $this->load->view('regiao_view', $dados);		
		$data['result'] = $this->load->view('pedido_view', $dados);
			
		return $data;
		
	}
	
	function busca_peditem() {	
		$emp = $this->input->post('emp');
		$filial = $this->input->post('filial');
		$oc = $this->input->post('oc');
		//list($pedido, $filial) = explode('-' , $codigo);
		$dados['result'] = $this->consultas->getItemPed($emp, $filial, $oc);		
		$this->load->view('itens_view', $dados);
	}
	
	function busca_adtfor() {
		$codfor = $this->input->post('codfor');				
		//list($pedido, $filial) = explode('-' , $codigo);
		$dados['resultado'] = $this->consultas->listar(null, null, null, null, null, null, null, $codfor, null, null);
		$dados['exporta'] = '';
		$this->load->view('pedidofor_view', $dados);
	}
	
}