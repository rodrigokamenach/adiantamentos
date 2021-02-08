<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
//we need to call PHP's session object to access it through CI
class Email extends CI_Controller {
	
	function __construct() {
		parent::__construct();		
	}
	
	function index() {
		ini_set("SMTP","smtp.grupofarias.com.br");
		ini_set("smtp_port","587");
		ini_set("sendmail_from","rodrigo.kamenach@grupofarias.com.br");
		ini_set("sendmail_path", "C:\wamp\sendmail.exe -t");
		
		$this->load->library('email');
				
		$dados_mail['msg_mail'] = null;
			$config = array (
					'mailtype' 		=> 'html',
					'charset'  		=> 'utf-8',
					'priority' 		=> '1',
					'protocol'  	=> 'smtp',
					'validate'		=> TRUE,
					//'mailpath'		=> 'C:\wamp\sendmail.exe',
					'smtp_host'		=> '192.168.49.12',
					'smtp_port'		=> '587',
					'smtp_timeout' 	=> '7',
					'smtp_user'    	=> 'rodrigo.kamenach@grupofarias.com.br',
					'smtp_pass'    	=> '0tfmbleK@',
					'wordwrap' 		=> TRUE,
					'newline'		=> '\r\n',
					'crlf'			=> '\n'
			);
			$this->email->initialize($config);
			$this->email->from('financeiro@grupofarias.com.br', 'Financeiro');
			//$this->email->to('rodrigo.kamenach@grupofarias.com.br, rogerio.silva@grupofarias.com.br, planilha.adiantamentos@grupofarias.com.br');
			$this->email->to('rodrigo.kamenach@grupofarias.com.br');
			$this->email->subject('Pagamentos aprovados e APs geradas!');
			//$this->email->attach(APPPATH.'xls/Adiantamentos-27-03-2017-10-12.xls');
			//$message = $this->load->view('email_view',$dados_mail,TRUE);
			$this->email->message('teste');
			$this->email->send();
			echo $this->email->print_debugger();
			$filename = 'Consulta_Adiantamentos';
			//$arquivo_origem = "C:\Aplicativos\adiantamentos\application\xls\'.$filename.'.xls";		
		 	//$arquivo_destino = '\\\\172.16.0.12\\Publica';
		 	//opendir($arquivo_destino);
		 	
		 	///$letter = "M";
		 	//$letter2 = "W";
		 	//$location = '\\172.16.0.12\Financeiro';
		 	//$location2 = "C:\Aplicativos\adiantamentos\application\xls";
		 	//$user = "financeiro";
		 	//$pass = "T1NTvugbQ";
		 	
		 	//$mapeia = system("net use " . $letter . ": \"".$location."\" ".$pass." /user:".$user." /persistent:no>nul 2>&1", $retva[]);
		 	//system("net use " . $letter2 . ": \"".$location2."\" ".$pass." /user:".$user." /persistent:no>nul 2>&1", $retva[]);		 
		 	$executa = system("copy C:\Aplicativos\adiantamentos\application\xls\\$filename.xls \\\\172.16.0.12\Financeiro\"\Relatorio de Pedidos Aprovados\"  2>&1", $retva[]);		 
		 	var_dump($retva);
			
// 			if (copy($arquivo_origem, $arquivo_destino)) {
// 				echo 'Arquivo copiado com Sucesso.';
// 			} else {
// 				echo 'Erro ao copiar arquivo';
// 			}		
}
}