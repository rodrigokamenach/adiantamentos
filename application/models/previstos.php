<?php
Class Previstos extends CI_Model {
	
	function listar() {
	
		$query = $this->db->query("SELECT A.USU_CODPREV, A.USU_CODFIL, A.USU_DATINI, A.USU_DATFIM, SUM(B.USU_VLRPRE) VLRTOTAL FROM USU_TADTPREV A
									INNER JOIN USU_TPREVDET B
									ON A.USU_CODPREV = B.USU_CODPREV
									GROUP BY A.USU_CODPREV, A.USU_CODFIL, A.USU_DATINI, A.USU_DATFIM");
	
		if($query -> num_rows() > 0) {
			return $query->result();
		} else {
			return false;
		}
	}
	
	function valida_prev($fil) {
		$query = $this->db->query("SELECT * FROM USU_TADTPREV WHERE USU_CODFIL = $fil");
		
		if($query -> num_rows() > 0) {
			return $query->result();
		} else {
			return false;
		}
	}
	
	function buscaprv($id) {
		$query = $this->db->query("SELECT * FROM USU_TADTPREV A
									INNER JOIN USU_TPREVDET B
									ON A.USU_CODPREV = B.USU_CODPREV
									WHERE A.USU_CODPREV = $id");
		if($query -> num_rows() > 0) {
			return $query->result();
		} else {
			return false;
		}
	}
	
	function checkprv($dt, $area) {
		$query = $this->db->query("SELECT A.USU_CODPREV, A.USU_CODFIL, A.USU_DATINI, A.USU_DATFIM, SUM(B.USU_VLRPRE) VLRTOTAL, 
									(SELECT SUM(C.USU_VLRADT) FROM USU_TADTMOV C WHERE C.USU_DTLANC BETWEEN  A.USU_DATINI AND A.USU_DATFIM AND C.USU_FILIAL = A.USU_CODFIL) VLRADT
									FROM USU_TADTPREV A
									INNER JOIN USU_TPREVDET B
									ON A.USU_CODPREV = B.USU_CODPREV
									WHERE TO_DATE('$dt', 'DD/MM/YYYY') BETWEEN TO_CHAR(A.USU_DATINI,'DD/MM/YYYY') AND TO_CHAR(A.USU_DATFIM,'DD/MM/YYYY')
									AND B.USU_CODAREA = '$area'
									GROUP BY A.USU_CODPREV, A.USU_CODFIL, A.USU_DATINI, A.USU_DATFIM");
		
		if($query -> num_rows() > 0) {
			return $query->result_array();
		} else {
			return false;
		}
	}
	
}