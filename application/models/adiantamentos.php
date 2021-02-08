<?php
Class Adiantamentos extends CI_Model {
	function listar($limit, $start, $valor, $dia, $pedido) {
		$limit = $limit+$start;
		$condicao = '';
		if ($valor !== '') {
			$condicao .= "and p.USU_FILIAL = '$valor'";
		} 		
		if ($dia !== '') {
			$condicao .= "AND to_char(p.USU_DTLANC, 'dd/mm/YYYY') = to_date('$dia', 'dd/mm/yyyy')";
		}
		if ($pedido !== '') {
			$condicao = "AND p.USU_NUMOCP = '$pedido'";
		}
		
		//echo $condicao;
		$query = $this->db->query("SELECT 									
									USU_NUMOCP,
									USU_CODEMP,
									USU_FILIAL, 
									to_char(USU_DTLANC, 'dd/mm/yyyy') USU_DTLANC,
									USU_VLRADT,
									USU_LANCUSU,
									USU_VLRAPR,
									USU_APRUSU,
									USU_DTAPR,
									USU_ADTPRI,
									USU_OBS,
									USU_AREA,
									USU_DESCAREA,
									USU_ID
									from (
									SELECT row_number() OVER (order by p.USU_DTLANC desc,p.USU_FILIAL,p.USU_VLRADT desc,p.USU_NUMOCP) linha, p.*, b.* from USU_TADTMOV p
				                    left join USU_TADTAREA b
				                    on p.usu_area = b.usu_codarea
									where p.USU_VLRAPR is null
									$condicao
									) WHERE linha BETWEEN $start AND $limit");		
		//$query = $this->db->query("select * from MGCLI.EST_ADT_MOVITEMS WHERE ROWNUM BETWEEN $start AND $limit order by 1 desc");
		//var_dump($query);
		//exit();
		if($query -> num_rows() > 0) {
			return $query->result();	
		} else {
			return false;			
		}
	}
	
	function conta($valor, $dia, $pedido) {
		
		$condicao = '';
		if ($valor !== '') {
			$condicao .= "and USU_FILIAL = '$valor'";
		} 		
		if ($dia !== '') {
			$condicao .= "AND to_char(USU_DTLANC, 'dd/mm/YYYY') = '$dia'";
		}
		if ($pedido !== '') {
			$condicao .= "AND USU_NUMOCP = '$pedido'";
		}
		
		//echo "select * from USU_TADTMOV where USU_VLRAPR is null $condicao order by 1 desc";
		$query = $this->db->query("select * from USU_TADTMOV where USU_VLRAPR is null $condicao order by 1 desc");
		//var_dump($query);
		if($query -> num_rows() > 0) {
			return $query->num_rows();
		} else {
			return false;
		}
	}
	
	function verifica_pedido($pedido, $filial) {
		
		$query = $this->db->query("SELECT A.CODEMP,
										  A.CODFIL,
										  A.NUMOCP,
										  A.CODFOR,
										  case 
											when A.VLRORI IS NULL OR A.VLRORI = 0 THEN A.VLRLIQ
											ELSE A.VLRORI
										  END VLRLIQ,  
										  B.VLRABE,
										  NVL(C.USU_VLRADT,0) USU_VLRADT,
										  NVL(C.USU_VLRAPR,0) USU_VLRAPR,
										  QTDE
										FROM E420OCP A
										LEFT JOIN
										  (SELECT CODEMP,
										    CODFIL,
										    FILOCP,
										    NUMOCP,
										    SUM(VLRABE) VLRABE
										  FROM E501TCP
										  WHERE SITTIT <> 'LQ'
										  and CODTPT IN ('ADT', 'PRC', 'CRE')
										  GROUP BY CODEMP,
										    CODFIL,
										    FILOCP,
										    NUMOCP
										  ) B
										ON A.CODEMP  = B.CODEMP
										AND A.CODFIL = B.FILOCP
										AND A.NUMOCP = B.NUMOCP
										LEFT JOIN
										  (SELECT USU_CODEMP,
										    USU_FILIAL,
										    USU_NUMOCP,
										    SUM(USU_VLRADT) USU_VLRADT,
										    SUM(USU_VLRAPR) USU_VLRAPR
										  FROM USU_TADTMOV
										  GROUP BY USU_CODEMP,
										    USU_FILIAL,
										    USU_NUMOCP
										  ) C
										ON A.CODEMP     = C.USU_CODEMP
										AND A.CODFIL    = C.USU_FILIAL
										AND A.NUMOCP    = C.USU_NUMOCP
										left JOIN (SELECT USU_FILIAL, USU_NUMOCP, COUNT(*) QTDE FROM USU_TADTMOV GROUP BY USU_FILIAL, USU_NUMOCP) D
					                    ON A.CODFIL = D.USU_FILIAL
					                    AND A.NUMOCP = D.USU_NUMOCP
										WHERE A.SITOCP IN (1,2)
										AND A.CODFIL    = $filial
										AND A.NUMOCP    = $pedido
										ORDER BY A.CODEMP,
										  A.CODFIL,
										  A.NUMOCP");
		if($query->num_rows() > 0) {
			return $query->result_array();
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
	
	function verifica_adt($fil, $oc, $id) {
		$query = $this->db->query("SELECT USU_NUMOCP ,USU_FILIAL ,to_char(USU_DTLANC,'dd/mm/yyyy') USU_DTLANC ,USU_VLRADT ,USU_LANCUSU ,USU_VLRAPR ,USU_APRUSU ,to_char(USU_DTAPR,'dd/mm/yyyy') USU_DTAPR, USU_ADTPRI ,USU_OBS ,USU_AREA ,USU_CODEMP ,USU_NUMTIT, USU_ID FROM USU_TADTMOV WHERE USU_FILIAL = '$fil' and USU_NUMOCP = $oc and USU_ID = $id");
		if($query->num_rows() > 0) {
			return $query->result();
		} else {
			return false;
		}
	}
	
	function getFor($oc, $fil) {
		$query = $this->db->query("SELECT A.CODFOR, B.APEFOR FROM E420OCP A
									INNER JOIN E095FOR B
									ON A.CODFOR = B.CODFOR
									WHERE A.CODFIL = '$fil' and A.NUMOCP = $oc");
		if($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return false;
		}
	}
	
	function getSitFor($codfor) {
		$query = $this->db->query("SELECT A.CODFOR, D.APEFOR, SUM(C.VLRABE) VLRABE FROM E420OCP A 
									INNER JOIN USU_TADTMOV B
									ON A.CODEMP = B.USU_CODEMP
									AND A.CODFIL = B.USU_FILIAL
									AND A.NUMOCP = B.USU_NUMOCP 
									INNER JOIN E501TCP C
									ON A.CODEMP = C.CODEMP
									AND A.CODFIL = C.CODFIL
									AND B.USU_NUMTIT = C.NUMTIT
									INNER JOIN E095FOR D
									ON A.CODFOR = D.CODFOR
									WHERE B.USU_NUMTIT IS NOT NULL
									AND C.SITTIT <> 'LQ'
									AND A.CODFOR = $codfor
									AND B.USU_DTLANC >= TO_DATE('01/05/2016', 'DD/MM/YYYY')
									GROUP BY A.CODFOR, D.APEFOR");
		if($query->num_rows() > 0) {
			return $query->result();
		} else {
			return false;
		}
	}
	
	function sitped($fil, $oc) {
		$query = $this->db->query("SELECT distinct a.codemp, a.codfil, a.numocp, a.sitocp, b.numocp ocpnfcp, c.numocp pcpnfcs FROM e420ocp a
										left join e440ipc b
										on a.codemp = b.codemp
										and a.codfil = b.filocp
										and a.numocp = b.numocp
										left join e440isc c
										on a.codemp = c.codemp
										and a.codfil = c.filocp
										and a.numocp = c.numocp
										where a.codfil = $fil
										and a.numocp = $oc");
		if($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return false;
		}		
	}
}