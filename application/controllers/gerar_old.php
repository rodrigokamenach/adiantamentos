<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
//we need to call PHP's session object to access it through CI
class Gerar extends CI_Controller {
	
	function __construct() {
		parent::__construct();
		$this->load->model('globals','',TRUE);
		$this->load->model('gerados','',TRUE);
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
	
			if (empty($dia)) {
				$dia = date('d/m/Y');
			}
				
			$dados['unidade'] = $this->globals->unidade($dia);
			$dados['regiao'] = $this->globals->regiao($dia);
			$dados['area'] = $this->globals->area($dia);
			$dados['filiais'] = $this->globals->lista_filial();
			$dados['user_codigo'] = $data['usu_codigo'];
				
			$this->load->view('header_view', $data);
			$this->load->view('gerar_view', $dados);
			$this->load->view('footer_view');
		} else {
			//If no session, redirect to login page
			redirect('login', 'refresh');
		}
	}
	
	function carreg_ger() {
	
		$dtadt =  $this->input->post('dtadt');
		$filial =  $this->input->post('filial');
		$area =  $this->input->post('area');
		$session_data = $this->session->userdata('newadt');
		$dados['user_codigo'] = $session_data['usu_codigo'];
	
		$dados['unidade'] = $this->globals->unidade($dtadt);
		$dados['regiao'] = $this->globals->regiao($dtadt);
		$dados['area'] = $this->globals->area($dtadt);
		$dados['resultado'] = $this->gerados->listar($dtadt, $filial, $area);
	
		if ($dados['resultado']) {
			$heading = array('DATA','PEDIDO','EMPRESA','FILIAL','FORNECEDOR','COMPRADOR','VALOR PEDIDO','VALOR ADIANTAMENTO','VALOR APROVADO','APLIC/OBS','OBS','AREA');
				
			$this->load->library('Excel');
				
			$this->excel->setActiveSheetIndex(0);
			$this->excel->getActiveSheet()->setTitle('Adiantamentos para Gerar AP');
				
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
			$this->excel->getActiveSheet()->setAutoFilter('A1:K1');
			$this->excel->getActiveSheet()->getStyle('A1:I1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$this->excel->getActiveSheet()->getStyle('K1:L1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
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
				$this->excel->getActiveSheet()->setCellValue('I'.$rowNumber,str_replace("," , "." , $row->USU_VLRAPR), 2, ',', '.');
				$this->excel->getActiveSheet()->setCellValue('J'.$rowNumber,$row->OBSOCP);
				$this->excel->getActiveSheet()->setCellValue('K'.$rowNumber,$row->USU_OBS);
				$this->excel->getActiveSheet()->setCellValue('L'.$rowNumber,$row->USU_AREA);
				$this->excel->getActiveSheet()->getStyle('G'.$rowNumber)->getNumberFormat()->setFormatCode($currencyFormat);
				$this->excel->getActiveSheet()->getStyle('H'.$rowNumber)->getNumberFormat()->setFormatCode($currencyFormat);
				$this->excel->getActiveSheet()->getStyle('I'.$rowNumber)->getNumberFormat()->setFormatCode($currencyFormat);				
				$rowNumber++;
			}
				
			$filename = 'GeraTit_Adiantamentos'; //save our workbook as this file name
				
			$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
			$objWriter->save(str_replace(__FILE__,APPPATH.'xls/'.$filename.'.xls',__FILE__));
			
			$dados['exporta'] = '<a class="btn btn-primary" href="'.base_url().'index.php/exporta/excel/'.$filename.'"><i class="fa fa-file-excel-o"></i> Exportar Excel</a>';
			
			$data['uni'] = $this->load->view('unidade_view', $dados);
			$data['reg'] = $this->load->view('regiao_view', $dados);
			$data['are'] = $this->load->view('area_view', $dados);
			$data['result'] = $this->load->view('gerar_table_view', $dados);
		} else {
			echo '<div class="col-md-12">
					<div class="panel panel-custom">
						<div class="panel-heading">
			    			<h4><i class="fa fa-list"></i> Resultado Adiantamentos</h4>
						</div>
						<div class="panel-body">
							<div class="alert alert-warning"><i class="fa fa-exclamation-circle"></i> Não existem dados para exibir. Verifique se o dia está FECHADO e se existem adiantamentos APROVADOS!</div>
						</div>
					</div>';
			print_r($dados['resultado']);
			exit();
		}
			
		return $data;
	
	}
	
	function gera_ap() {
		$this->load->library('form_validation');
				
		$this->form_validation->set_message('required', '<div class="alert alert-danger">O campo %s é obrigatório</div>');
			
		$this->form_validation->set_rules('chek_ger[]', '', 'trim|required');
		
		if ($this->form_validation->run() == FALSE)	{
			echo json_encode(array('st'=>1, 'msg' => validation_errors()));
		} else {
			$id = $this->input->post('chek_ger[]', TRUE);
			$cd_user = $this->input->post('cd_user');
			//var_dump($id);
			//exit();
			$erro = 0;
			$msg_erro = '';
			$sucesso = 0;
			$msg_sucess = '';
			$msg_mail = '';
			$id_ok = '';
			//$cont_id = 0;
			//var_dump($id);
			//exit();
						
			foreach ($id as $codigo) {
				//echo $codigo;
				//exit();
				$id_ok .= $codigo.',';
				
				//var_dump($result_tempar);
				//exit();
				$result_pedido = $this->aprovados->getDadosOC($codigo);
				//var_dump($result_pedido);
				//exit();
				if ($result_pedido) {
					
					foreach ($result_pedido as $row) {
						$oc = $row->USU_NUMOCP;
						$emp = $row->USU_CODEMP;
						$dt = $row->USU_DTAPR;
						$filial = $row->USU_FILIAL;
						$vlr_adt = $row->USU_VLRADT;
						$vlr_apr = $row->USU_VLRAPR;
						$vlr_oc = $row->VLRLIQ;
						$tnspro = $row->TNSPRO;
						$tnsser = $row->TNSSER;
						$codfor = $row->CODFOR;
						$dtapr = $row->USU_DTAPR;
						$obs = $row->OBSOCP;
						$codfpg = $row->CODFPG;
						$codmoe = $row->CODMOE;
						$codusu = $row->USU_LANCUSU;
						$cnpj = $row->CGCCPF;
						$banco = $row->CODBAN;
						$nban = $row->NOMBAN;
						$agencia = $row->CODAGE;
						$cc = $row->CCBFOR;
						
						$result_vertit = $this->gerados->ConsultaTit($emp, $filial, $oc);
						//var_dump($result_vertit);
						//echo $vlr_oc;
						//exit();
							if ($result_vertit == false) {								
								$numtit = $oc.'_1';															
							} else {
								$parcela = $result_vertit[0]['ULTPAR'] + 1;
								$numtit = $oc.'_'.$parcela;
							}
							
							if (floatval($result_vertit[0]['VLRABE']) >= floatval($vlr_oc)) {
								$erro += 1;
								$msg_erro .= '<div class="alert alert-danger small">O pedido <strong>'.$oc.'</strong> - filial: <strong>'.$filial.'</strong> já possui título com o valor total.Não é possível gerar pois ocorrerá duplicidade!</div>';
							} else {
							
								$dados_tcp = array(
										intval($emp), //CODEMP
										intval($filial), //CODFIL
										$numtit, //NUMTIT
										'ADT', //CODTPT
										intval($codfor), //CODFOR
										'', //CODCRP
										'90514', //CODTNS
										0, //CODNTG
										'', //CODTRI
										'AB', //SITTIT
										'', //SITANT
										$dtapr, //DATEMI
										$dtapr, //DATENT
										$obs, //OBSTCP
										0, //CODFAV
										$dtapr, //VCTORI
										$vlr_apr, //VLRORI
										$codfpg, //CODFPG
										$dtapr, //VCTPRO
										'S', //PROJRS
										'', //CODMPT
										$dtapr, //datppt
										$vlr_apr, //vlrabe
										$codmoe, //codmoe
										0, //cotemi
										'', //codfrj
										'', //datdsc
										0, //TOLDSC
										0, //PERDSC
										0, //VLRDCS
										'', //ANTDSC
										0, //PERJRS
										'', //TIPJRS
										0, //JRSDIA
										0, //TOLJRS
										$dtapr, //DATCJM
										0, //PERMUL
										0, //TOLMUL
										'', //DATNEG
										0, //JRSNEG
										0, //MULNEG
										0, //DSCNEG								
										0, //OUTNEG									
										0, //USUNEG
										0, //COTNEG
										0, //CORNEG
										'9999', //CODPOR
										'99', //CODCRT
										'', //tipban
										0, //codusu
										'', //datapr
										0, //horapr
										'', //pgtapr
										0, //vlrapr
										0, //cotpapr
										0, //dscapr
										0, //odeapr
										0, //jrsapr
										0, //mulapr
										0, //encapr
										0, //corapr
										0, //oacapr
										0, //irfapr
										'', //vcrapr
										0, //empapr
										'', //ctaapr
										'', //seqapr
										'', //libapr
										'', //ultpgt
										'', //codban
										'', //codage,
										'', //ccbfor,
										'9999', //porant
										0, //NUMPRJ
										0, //CODFPJ
										0, //CTAFIN
										0, //CTARED
										'', //CODCCU
										'', //DATUCM
										0, //COTUCM
										0, //FILNFC
										0, //FORNFC
										0,//NUMNFC
										'',//SNFNFC
										0,//FILCTR
										0,//NUMCTR
										0,//SEQIMO
										0,//SEQCGT
										0,//FILNFF
										0,//NUMNFF
										0,//FORNFF
										0,//FILNFV
										'',//SNFNFV
										0,//NUMNFV
										0,//FPGAPR
										intval($filial),//FILOCP
										intval($oc),//NUMOCP
										0,//OCPFRE
										0,//OCPNRE
										0,//CTRFRE
										0,//CTRNRE
										'',//CODBAR
										0,//USUSIT
										'',//TIPEFE
										0,//TNSPRE
										'',//DATPRE
										0,//VLRPRE
										0,//NUMARB
										0,//NUMECO
										'',//TIPIMP
										'',	//FILIMP								
										0,//NUMIMP
										0,//TPTIMP
										0,//FORIMP
										0,//SEQIMP
										0,//VLRINS
										0,//PRIPGT
										'',//FILCCR
										'',//NUMCCR
										0,//TITCAR
										'',//TITPJR
										'',//GRIFIL
										'',//GRIIMP
										'',//GRIAPI
										0,//GRISEQ
										'',//CODSMA
										0,//NUMMAN
										'',//CPGSUB
										'N',//GERTEP
										'',//SITDDA
										intval($codusu),//USUGER
										//DATGER
										//HORGER
										0,//ROTSAP
										'',//NUMPGE
										0,//NUMDFS
										'',//AUTBAN
										'',//CTRBAN
										'',//TITJRS
										'',//TPTJRS
										0,//ROTNAP
										0,//NUMAPR
										'',//SITAPR
										'',//TNSAPR
										'',//IMPENT
										0,//CODPCA
										0,//CODSOL
										0,//VLRRBA
										0,//VLRINT
										'',//APRINI
										''//REATCP
								);
								
								$sql_tcp = "INSERT INTO E501TCP VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,TO_CHAR(SYSDATE,'DD/MM/YYYY'),((TO_CHAR(SYSDATE,'hh24')*60)+TO_CHAR(SYSDATE,'mi')),?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
							
								$result_tcp = $this->gerados->crud($sql_tcp, $dados_tcp);
								//var_dump($result_tcp);
							
								if ($result_tcp) {
								
									$dados_mcp = array(
											intval($emp), //CODEMP
											intval($filial), //CODFIL
											$numtit, //NUMTIT
											'ADT', //CODTPT
											intval($codfor), //CODFOR
											1,//seqmov
											'90514', //CODTNS
											$dtapr, //datmov
											'',//NUMDOC
											'',//OBSMCP
											$dtapr,//VCTPRO
											'',//PROJRS
											$vlr_apr,//VLRABE
											'',//CODFRJ
											'',//DATPGT
											0,//CODFPG
											0,//COTMCP
											0,//DIAATR
											0,//DIAJRS
											'',//SEQCHE
											'',//TNSCXB
											'',//DATLIB
											0,//CODUSU
											'',//DATAPR
											0,//HORAPR
											$vlr_apr,//VLRMOV
											0,//VLRDSC
											0,//VLRODE
											0,//VLRJRS
											0,//VLRMUL
											0,//VLRENC
											0,//VLRCOR
											0,//VLROAC
											0,//VLRIRF
											0,//ORIIRF
											0,//VLRISS
											0,//ORIISS
											0,//VLRINS
											0,//ORIINS
											0,//VLRPIT
											0,//PITCAL
											0,//ORIPIT
											0,//VLRBPT
											0,//VLROPT
											0,//VLRCRT
											0,//CRTCAL
											0,//ORICRT
											0,//VLRBCT
											0,//VLROCT
											0,//VLRCSL
											0,//CSLCAL
											0,//ORICSL
											0,//VLRBCL
											0,//VLROCL
											0,//VLROUR
											0,//OURCAL
											0,//ORIOUR
											0,//VLRBOR
											0,//VLROOR
											0,//VLRPIS
											0,//VLRBPR
											0,//VLRCOF
											0,//VLRBCR
											$vlr_apr,//VLRLIQ
											0,//PERJRS
											'',//ULTPGT
											'',//CJMANT
											0,//JRSCAL
											'9999',//CODPOR
											'99',//CODCRT
											'',//PORANT
											'',//CRTANT
											0,//EMPCCO
											'',//NUMCCO
											'',//DATCCO
											0,//SEQCCO
											0,//NUMPRJ
											0,//CODFPJ
											0,//CTAFIN
											0,//CTARED
											'',//CODCCU
											0,//FILRLC
											'',//NUMRLC
											'',//TPTRLC
											0,//FORRLC
											0,//SEQMCP
											0,//SEQMCR
											'',//TIPPGT
											'',//CODBAN
											'',//CODAGE
											'',//CCBFOR
											'',//INDVCR
											'S',//LCTFIN
											0,//LOTBAI
											0,//NUMLOT
											intval($codusu),//USUGER
											//DATGER
											//HORGER
											0,//INDEXP
											0,//FILFIX
											0,//NUMFIX
											'',//INTIMP
											0,//ROTNAP
											0,//NUMAPR
											'',//SITAPR
											0,//CODPCA
											0,//FORPCA
											'',//CHVLOT
											0,//VLRRBA
											0,//SEQLBA
											0,//VLRINT
											0,//FILORI
											'',//REAANB
											0,//NUMPDV
											''//PGTAPR
									);
								
									$sql_mcp = "INSERT INTO E501MCP VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,TO_CHAR(SYSDATE,'DD/MM/YYYY'),((TO_CHAR(SYSDATE,'hh24')*60)+TO_CHAR(SYSDATE,'mi')),?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
									
									$result_mcp = $this->gerados->crud($sql_mcp, $dados_mcp);
									//var_dump($result_mcp);
									//exit();
									if ($result_mcp) {
									
										$result_ocnumrat = $this->aprovados->getNumRateio($emp, $filial, $oc);
										//var_dump($result_ocnumrat);
										//exit();
										if ($result_ocnumrat) {
											$qtde = count($result_ocnumrat);										
											//$vlrrat = $vlr_adt/$result_ocnumrat[0]['NUM'];
											foreach ($result_ocnumrat as $cod => $conta) {
																						
												$ctafin_rat = $conta->CTAFIN;
																							
												$vlrcta[$ctafin_rat] = $vlr_apr/$qtde;
												$percta[$ctafin_rat] = 100/$qtde;
											}
										
											//var_dump($vlrcta);
											//var_dump($percta);
											//exit();
										
											$result_ocrat = $this->aprovados->getOcRateio($emp, $filial, $oc);
										
											$seqrat = 1;
										
											foreach ($result_ocrat as $rat) {
											
												$ctafin = $rat->CTAFIN;											
												$codccu = $rat->CODCCU;
												$crirat = $rat->CRIRAT;
												$somsub = $rat->SOMSUB;
												$numprj = $rat->NUMPRJ;
												$codfpj = $rat->CODFPJ;
												$perrat = $rat->PERRAT;
											
												$vlrrat = ($perrat/100)*$vlrcta[$ctafin];
											
												$dados_rat = array(
													$emp,//CODEMP
													$filial,//CODFIL
													$numtit,//NUMTIT
													'ADT',//CODTPT
													$codfor,//CODFOR
													1,//SEQMOV
													$seqrat,//SEQRAT
													$dtapr,//DATBAS
													'90514',//CODTNS
													//MESANO
													$crirat,//CRIRAT
													$somsub,//SOMSUB
													$numprj,//NUMPRJ
													$codfpj,//CODFPJ
													$ctafin,//CTAFIN
													0,//CTARED
													$percta[$ctafin],//PERCTA
													$vlrcta[$ctafin],//VLRCTA
													$codccu,//CODCCU
													$perrat,//PERRAT
													$vlrrat,//VLRRAT
													'',//OBSRAT
													$codusu,//USUGER
													//DATGER
													//HORGER
													'A',//TIPORI
													0,//USUALT
													'',//DATALT
													0//HORALT																		
												);
												//var_dump($dados_rat);
											
												$sql_rat = "INSERT INTO E501RAT VALUES(?,?,?,?,?,?,?,?,?,'01/'||TO_CHAR(SYSDATE, 'MM/YYYY'),?,?,?,?,?,?,?,?,?,?,?,?,?,TO_CHAR(SYSDATE,'DD/MM/YYYY'),((TO_CHAR(SYSDATE,'hh24')*60)+TO_CHAR(SYSDATE,'mi')),?,?,?,?)";
											
												$result_rat = $this->gerados->crud($sql_rat, $dados_rat);
											
												if ($result_rat) {
													$seqrat = $seqrat + 1;
																									
												} else {
													$erro += 1;
													$msg_erro .= '<div class="alert alert-danger small">Erro ao gerar rateio do pedido: <strong>'.$oc.'</strong> - filial: <strong>'.$filial.'</strong>.Tente novamente!</div>';
													
													$del_tit = array(
															$filial,
															$emp,
															$numtit														
													);
												
													$sql_del = "DELETE FROM E501MCP WHERE CODFIL = ? and codemp = ? and numtit = ? and codtpt = 'ADT'";
													if ($this->gerados->crud($del_mcp, $sql_del)) {
														$sql_deltcp = "DELETE FROM E501TCP WHERE CODFIL = ? and codemp = ? and numtit = ? and codtpt = 'ADT'";
														$this->gerados->crud($del_mcp, $sql_deltcp);													
													}
												
												}
											
											}
										//exit();
										} else {
											$erro += 1;
											$msg_erro .= '<div class="alert alert-danger small">Não foi possível gerar o título para o pedido <strong>'.$oc.'</strong> - filial: <strong>'.$filial.'</strong>. O PEDIDO NÃO POSSUIU RATEIO, ADICIONE O RATEIO NO PEDIDO PARA GERAR O TÍTULO</div>';
										
											$del_tit = array(
													$filial,
													$emp,
													$numtit
											);
										
											$sql_del = "DELETE FROM E501MCP WHERE CODFIL = ? and codemp = ? and numtit = ? and codtpt = 'ADT'";
											if ($this->gerados->crud($del_mcp, $sql_del)) {
												$sql_deltcp = "DELETE FROM E501TCP WHERE CODFIL = ? and codemp = ? and numtit = ? and codtpt = 'ADT'";
												$this->gerados->crud($del_mcp, $sql_deltcp);
											}
										}
								
									} else {
										$erro += 1;
										$msg_erro .= '<div class="alert alert-danger small">Não foi possível gerar o movimento do título para o pedido <strong>'.$oc.'</strong> - filial: <strong>'.$filial.'</strong>.Tente novamente!</div>';
										
										$del_tit = array(
												$filial,
												$emp,
												$numtit
										);
										
										$sql_deltcp = "DELETE FROM E501TCP WHERE CODFIL = ? and codemp = ? and numtit = ? and codtpt = 'ADT'";
										$this->gerados->crud($del_mcp, $sql_deltcp);
									}
								
								} else {
									$erro += 1;
									$msg_erro .= '<div class="alert alert-danger small">Não foi possível gerar o título para o pedido <strong>'.$oc.'</strong> - filial: <strong>'.$filial.'</strong>.Tente novamente!</div>';
								}
								
								//aqui
								$dados_tit = array(
										$numtit,
										$codigo
								);
								
								$sql_tit = "UPDATE USU_TADTMOV SET USU_NUMTIT = ? WHERE USU_CODEMP||USU_FILIAL||USU_NUMOCP||USU_ID = ?";
								$resp_adt = $this->aprovados->crud($sql_tit, $dados_tit);
								
								if ($resp_adt) {
									$sucesso += 1;
									$msg_sucess .= '<div class="alert alert-success small">Titulo: <strong>'.$numtit.'</strong> - Pedido: <strong>'.$oc.'</strong> - Filial: <strong>'.$filial.'</strong> - Valor: R$ <strong>'.$vlr_apr.'</strong></div>';
									$msg_mail .= '<tr><td class="text-center">'.$numtit.'</td><td class="text-center">'.$oc.'</td><td class="text-center">'.$filial.'</td><td class="text-right">'.$vlr_apr.'</td><td class="text-center">'.$codfor.'</td><td class="text-center">'.$cnpj.'</td><td class="text-left">'.$banco.' - '.$nban.'</td><td class="text-center">'.$agencia.'</td><td class="text-center">'.$cc.'</td></tr>';
								}
							}
						
							
						}
						
					} else {
						$erro += 1;
						$msg_erro .= '<div class="alert alert-danger small">O pedido <strong>'.$oc.'</strong> - filial: <strong>'.$filial.'</strong> não foi encontrado.</div>';
					}
																						
																																																																					
					}
				
				}
				
				$heading = array('PEDIDO','FILIAL','FORNECEDOR','COMPRADOR','VALOR PEDIDO','VALOR ADIANTAMENTO', 'VALOR APROVADO', 'SITUACAO', 'AP', 'APROVADOR', 'APLIC/OBS', 'OBS', 'AREA', 'CNPJ', 'BANCO', 'AGENCIA', 'CONTA');
				
				$this->load->library('Excel');
				
				$this->excel->setActiveSheetIndex(0);
				$this->excel->getActiveSheet()->setTitle('Adiatamentos Aprovados');
					
				$rowNumberH = 1;
				$colH = 'A';
					
				foreach($heading as $h){
					$this->excel->getActiveSheet()->setCellValue($colH.$rowNumberH,$h);
					$this->excel->getActiveSheet()->getColumnDimension($colH)->setAutoSize(true);
					$colH++;
				}
				
				$this->excel->getActiveSheet()->getStyle('A1:J1')->getFont()->setBold(true);
				$this->excel->getActiveSheet()->getStyle('A1:J1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
				$this->excel->getActiveSheet()->getStyle('A1:J1')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$this->excel->getActiveSheet()->getStyle('A1:J1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$this->excel->getActiveSheet()->getStyle('A1:Q1')->getFill()->getStartColor()->setRGB('cbded0');					
				$this->excel->getActiveSheet()->getStyle('L1:Q1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$this->excel->getActiveSheet()->setAutoFilter('A1:Q1');
				$currencyFormat = '_(R$* #,##0.00_);_(R$* (#,##0.00);_(R$* "-"??_);_(@_)';
				
				$id_ok = rtrim($id_ok, ',');
				$result_excel = $this->gerados->list_excel($id_ok);
				
				$rowNumber = 2;
				foreach ($result_excel as $row) {
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
				
					$this->excel->getActiveSheet()->setCellValue('A'.$rowNumber,$row->USU_NUMOCP);
					$this->excel->getActiveSheet()->setCellValue('B'.$rowNumber,$row->USU_FILIAL.' - '.$row->SIGFIL.' - '.$row->USU_INSTAN);
					$this->excel->getActiveSheet()->setCellValue('C'.$rowNumber,$row->CODFOR.' - '.$row->APEFOR);
					$this->excel->getActiveSheet()->setCellValue('D'.$rowNumber,$row->USULANC);
					$this->excel->getActiveSheet()->setCellValue('E'.$rowNumber,str_replace("," , "." , $row->VLRLIQ), 2, ',', '.');
					$this->excel->getActiveSheet()->setCellValue('F'.$rowNumber,str_replace("," , "." , $row->USU_VLRADT), 2, ',', '.');
					$this->excel->getActiveSheet()->setCellValue('G'.$rowNumber,str_replace("," , "." , $row->USU_VLRAPR), 2, ',', '.');
					$this->excel->getActiveSheet()->setCellValue('H'.$rowNumber,$row->SITUACAO);
					$this->excel->getActiveSheet()->setCellValue('I'.$rowNumber,$row->USU_NUMTIT);
					$this->excel->getActiveSheet()->setCellValue('J'.$rowNumber,$row->USUAPR);
					$this->excel->getActiveSheet()->setCellValue('K'.$rowNumber,$row->OBSOCP);
					$this->excel->getActiveSheet()->setCellValue('L'.$rowNumber,$row->USU_OBS);
					$this->excel->getActiveSheet()->setCellValue('M'.$rowNumber,$row->USU_AREA);
					$this->excel->getActiveSheet()->setCellValue('N'.$rowNumber,$row->CGCCPF);
					$this->excel->getActiveSheet()->setCellValue('O'.$rowNumber,$row->CODBAN.' - '.$row->NOMBAN);
					$this->excel->getActiveSheet()->setCellValue('P'.$rowNumber,$row->CODAGE);
					$this->excel->getActiveSheet()->setCellValue('Q'.$rowNumber,$row->CCBFOR);
					$this->excel->getActiveSheet()->getStyle('E'.$rowNumber)->getNumberFormat()->setFormatCode($currencyFormat);
					$this->excel->getActiveSheet()->getStyle('F'.$rowNumber)->getNumberFormat()->setFormatCode($currencyFormat);
					$this->excel->getActiveSheet()->getStyle('G'.$rowNumber)->getNumberFormat()->setFormatCode($currencyFormat);
					$rowNumber++;
				}
				
				$filename = 'Adiantamentos-'.date('d-m-Y-H-i'); //save our workbook as this file name
					
				$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
				$objWriter->save(str_replace(__FILE__,APPPATH.'xls/'.$filename.'.xls',__FILE__));
					
				$dados_mail['msg_mail'] = $msg_mail;
					
				ini_set("SMTP","smtp.grupofarias.com.br");
				ini_set("smtp_port","587");
				ini_set("sendmail_from","rodrigokamenach@hotmail.com");
				ini_set("sendmail_path", "C:\wamp\sendmail.exe -t");
					
				$this->load->library('email');
				$config = array (
						'mailtype' 		=> 'html',
						'charset'  		=> 'utf-8',
						'priority' 		=> '1',
						'protocol'  	=> 'mail',
						'smtp_host'		=> 'ssl://smtp.grupofarias.com.br',
						'smtp_port'		=> '587',
						'smtp_timeout' 	=> '7',
						'smtp_user'    	=> 'rodrigo.kamenach@grupofarias.com.br',
						'smtp_pass'    	=> 'a5U3D6o9q1m0.',
						'wordwrap'	 	=> TRUE,
						'newline'		=> '\r\n',
						'crlf'			=> '\n'
				);
				$this->email->initialize($config);
				$this->email->from('financeiro@grupofarias.com.br', 'Financeiro');
				$this->email->to('rodrigo.kamenach@grupofarias.com.br, sergio.pinto@grupofarias.com.br, rogerio.silva@grupofarias.com.br, kaio.baia@grupofarias.com.br, planilha.adiantamentos@grupofarias.com.br');
				//$this->email->to('rodrigo.kamenach@grupofarias.com.br');
				$this->email->subject('Pagamentos aprovados e Títulos geradas!');
				$this->email->attach(APPPATH.'xls/'.$filename.'.xls');
				$message = $this->load->view('email_view',$dados_mail,TRUE);
				$this->email->message($message);
				$this->email->send();
				
				//echo $this->email->print_debugger();
				if ($erro > 0) {
					echo '<legend>Foram encontrados '.$erro.' erro(s)!</legend>';
					echo $msg_erro.'<br>';
				}
				echo '<legend>Foram processados '.$sucesso.' pedido(s)!</legend>';
				if ($sucesso > 0) {
					echo $msg_sucess;
				}
			}
														
}