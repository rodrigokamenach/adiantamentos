<?php
Class User extends CI_Model {
	function login($username) {	
				
		$query = $this->db->query("SELECT a.codusu, a.nomusu, b.USU_NOMUSU  FROM r999usu a
									left join (SELECT usu_tadtusu.USU_NOMUSU FROM usu_tadtusu) b
									on a.nomusu = b.USU_NOMUSU
									WHERE nomusu = '$username'");		
		if($query->num_rows() == 1) {
			return $query->result_array();			
		} else {
			return false;
		}
	}
	
	function getPermissao($nomuser) {
		$query = $this->db->query("select * from usu_tadtusu where USU_NOMUSU = '$nomuser'");
		if($query -> num_rows() > 0) {
			return $query->result_array();
		} else {
			return false;
		}
	}
	
	function listar() {
		$query = $this->db->query("select * from usu_tadtusu order by 2");
		if($query -> num_rows() > 0) {
			return $query->result();
		} else {
			return false;
		}
	}
	
	function busca_codusu($nomuser) {
		$query = $this->db->query("select CODUSU from r999usu where NOMUSU = '$nomuser'");
		if($query -> num_rows() > 0) {
			return $query->result_array();
		} else {
			return false;
		}
	}
	
	function verifica_user($user) {		
		$query = $this->db->query("select * from usu_tadtusu WHERE USU_CODUSU = $user");
		if($query->num_rows() > 0) {
			return $query->result();
		} else {
			return false;
		}
	}
	
	function inserir($sql, $dados) {
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
	
	function atualiza($sql, $dados) {		
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
		
}