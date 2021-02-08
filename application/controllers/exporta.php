<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
//we need to call PHP's session object to access it through CI
class Exporta extends CI_Controller {
	
	function __construct() {
		parent::__construct();		
	}
	
	function excel($filename) {
		$this->load->helper('download');
		$data = file_get_contents(APPPATH . 'xls/'.$filename.'.xls'); // Read the file's contents
		$name = $filename.'.xls';
		force_download($name, $data);
	}
}