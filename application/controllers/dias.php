<?php
Class Dias extends CI_Model {
	function listar($year, $month) {
		$mesano = $month.'/'.$year;
		$query = $this->db->query("select USU_CODDIA ,TO_CHAR(USU_DT,'DD/MM/YYYY') USU_DT ,USU_STDIA from USU_TADTDIA WHERE TO_CHAR(USU_DT,'MM/YYYY') = '$mesano' order by 2");
		//var_dump($query);
		if($query -> num_rows() > 0) {
			return $query->result();
		} else {
			return false;
		}
	}
	
	function verifica_dia($id) {		
		$query = $this->db->query("select USU_CODDIA ,TO_CHAR(USU_DT,'DD/MM/YYYY') USU_DT ,USU_STDIA from USU_TADTDIA WHERE USU_CODDIA = $id");
		if($query->num_rows() > 0) {
			return $query->result();
		} else {
			return false;
		}
	}
	
	function crud($sql, $dados) {
		$this->db->trans_begin();
	
		$this->db->query($sql, $dados);
	
		if ($this->db->trans_status() == FALSE) {
			$this->db->trans_rollback();
			return false;
		} else {
			$this->db->trans_commit();
			return TRUE;
		}
	}
	
	function check_dia($dia) {
		$query = $this->db->query("select * from USU_TADTDIA WHERE USU_DT = '$dia' and USU_STDIA <> 'F'");
		if($query->num_rows() > 0) {
			return $query->num_rows();
		} else {
			return false;
		}
	}

	function check_india($dia) {
		$query = $this->db->query("select USU_STDIA from USU_TADTDIA WHERE USU_DT = '$dia'");
		if($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return false;
		}
	}
}