<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
 
class String {
    
    function Rmacento($sub){
    	$acentos = array(
    			'À','Á','Ã','Â', 'à','á','ã','â',
    			'Ê', 'É',
    			'Í', 'í',
    			'Ó','Õ','Ô', 'ó', 'õ', 'ô',
    			'Ú','Ü',
    			'Ç', 'ç',
    			'é','ê',
    			'ú','ü',
    	);
    	$remove_acentos = array(
    			'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a',
    			'e', 'e',
    			'i', 'i',
    			'o', 'o','o', 'o', 'o','o',
    			'u', 'u',
    			'c', 'c',
    			'e', 'e',
    			'u', 'u',
    	);
    	return str_replace($acentos, $remove_acentos, urldecode($sub));
    }
    
    
}