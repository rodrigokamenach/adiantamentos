<?php
Class Globals extends CI_Model {
	function lista_filial($filial =null) {
		$condicao = '';
		
		if($filial) {
			$condicao = 'where codfil in ('.$filial.')';
		}
		
		$query = $this->db->query("SELECT codfil, SIGFIL, USU_INSTAN FROM e070fil $condicao order by codfil");
		if($query -> num_rows() > 0) {
			return $query->result();
		} else {
			return false;
		}
	}
	
	function lista_fornec() {
		$query = $this->db->query("SELECT distinct a.codfor, a.apefor FROM e095for a
									inner join e420ocp b
									on a.codfor = b.codfor
									inner join usu_tadtmov c
									on b.codemp = c.USU_CODEMP
									and b.codfil = c.USU_FILIAL
									and b.numocp = c.USU_NUMOCP
									order by a.apefor");
		if($query -> num_rows() > 0) {
			return $query->result();
		} else {
			return false;
		}
	}
	
	function lista_area($area) {		
		$condicao = "";
		if ($area !== null) {
			$area = str_replace(",", "','", $area);
			$condicao = "where usu_codarea in ('$area')";
		}
		
		$query = $this->db->query("SELECT * FROM usu_tadtarea $condicao order by usu_codarea");
		if($query -> num_rows() > 0) {
			return $query->result();
		} else {
			return false;
		}
	}
	
	function unidade($dia, $fil, $area) {
		$c1 = '';
		$c2 = '';
		if($fil != null) {
			$c1 .= "AND A.CODFIL in ($fil)";			
		}
		
		if($area != null) {
			$c1 .= "AND B.USU_AREA = '$area'";
			$c2 .= "AND B.USU_CODAREA = '$area'";
		}
		
		$query = $this->db->query("SELECT case 
											WHEN D.SIGFIL IS NOT NULL THEN D.SIGFIL
											ELSE E.SIGFIL
											END SIGFIL, 
											CASE WHEN D.USU_INSTAN IS NOT NULL THEN D.USU_INSTAN
											ELSE E.USU_INSTAN
											END USU_INSTAN, 
											D.CODFIL, 
											NVL(E.VLRPRE,0) AS VLRPRE, 
											NVL(D.VLR_ADT,0) VLR_ADT, 
											NVL(D.VLR_APR,0) VLR_APR  FROM (SELECT 
											a.SIGFIL,
											a.USU_INSTAN,
											A.CODFIL,
											sum(nvl(b.USU_VLRADT,0)) vlr_adt,
											sum(nvl(b.USU_VLRAPR,0)) vlr_apr 
											FROM e070fil a
											inner join USU_TADTMOV b
											on a.CODFIL = b.USU_FILIAL
											where to_char(b.USU_DTLANC, 'dd/mm/yyyy') = to_date('$dia','dd/mm/yyyy')										
											$c1
											group by a.SIGFIL, a.USU_INSTAN, A.CODFIL) D
											RIGHT JOIN
											(SELECT C.SIGFIL,
												   C.USU_INSTAN,       
											       SUM(B.USU_VLRPRE) VLRPRE
											FROM USU_TADTPREV A
											INNER JOIN USU_TPREVDET B
											ON A.USU_CODPREV = B.USU_CODPREV
											INNER JOIN e070fil C
											ON A.USU_CODFIL = C.CODFIL
											WHERE to_date('$dia','dd/mm/yyyy') BETWEEN TO_CHAR(A.USU_DATINI,'DD/MM/YYYY') AND TO_CHAR(A.USU_DATFIM,'DD/MM/YYYY')										
											$c2
											GROUP BY C.SIGFIL, C.USU_INSTAN) E
											ON D.SIGFIL = E.SIGFIL
											AND D.USU_INSTAN = E.USU_INSTAN");				
		if($query -> num_rows() > 0) {
			return $query->result();
		} else {
			return false;
		}
	}
	
	function regiao($dia, $fil, $area) {
		$c1 = '';
		$c2 = '';
		if($fil != null) {
			$c1 .= "AND A.CODFIL in ($fil)";
			$c2 .= "AND A.USU_CODFIL IN ($fil)";
		}
		
		if($area != null) {
			$c1 .= "AND B.USU_AREA = '$area'";
			$c2 .= "AND b.USU_codAREA = '$area'";
		}
		
		$query = $this->db->query("SELECT case 
											when D.SIGUFS is not null then D.SIGUFS
											else E.SIGUFS
											end SIGUFS, NVL(E.VLRPRE,0) VLRPRE, 
											nvl(D.VLR_ADT,0) VLR_ADT, 
											nvl(D.VLR_APR,0) VLR_APR 
											FROM (SELECT 
											a.SIGUFS,
											sum(nvl(b.USU_VLRADT,0)) vlr_adt,
											sum(nvl(b.USU_VLRAPR,0)) vlr_apr 
											FROM e070fil a
											inner join USU_TADTMOV b
											on a.CODFIL = b.USU_FILIAL
											where to_char(b.USU_DTLANC, 'dd/mm/yyyy') = to_date('$dia','dd/mm/yyyy')
											$c1
											group by a.SIGUFS, b.USU_DTLANC) D
											right JOIN
											  (SELECT * FROM (SELECT C.SIGUFS,      
											    SUM(B.USU_VLRPRE) VLRPRE
											  FROM USU_TADTPREV A
											  INNER JOIN USU_TPREVDET B
											  ON A.USU_CODPREV = B.USU_CODPREV
											  INNER JOIN e070fil C
											  ON A.USU_CODFIL = C.CODFIL
											  WHERE to_date('$dia','dd/mm/yyyy') BETWEEN TO_CHAR(A.USU_DATINI,'DD/MM/YYYY') AND TO_CHAR(A.USU_DATFIM,'DD/MM/YYYY')
											  $c2											  
											  GROUP BY C.SIGUFS) F
											  ) E 
											ON D.SIGUFS = E.SIGUFS");
		if($query -> num_rows() > 0) {
			return $query->result();
		} else {
			return false;
		}
	}
	
	function area($dia, $fil, $area) {
		$c1 = '';
		$c2 = '';
		if($fil != null) {
			$c1 .= "AND A.USU_FILIAL in ($fil)";
			$c2 .= "AND A.USU_CODFIL in ($fil)";
		} 
		
		if($area != null) {
			$c1 .= "AND B.USU_CODAREA = '$area'";
			$c2 .= "AND D.USU_CODAREA = '$area'";
		}
		$query = $this->db->query("SELECT case 
											when G.USU_DESCAREA is not null then G.USU_DESCAREA
											else E.USU_DESCAREA
											END USU_DESCAREA, NVL(E.VLRPRE,0) AS VLRPRE,
										  G.VLR_ADT,
										  G.VLR_APR FROM (SELECT 
										 B.USU_DESCAREA,
										 B.USU_CODAREA,
										  NVL(SUM(A.USU_VLRADT),0) vlr_adt,
										  NVL(SUM(A.USU_VLRAPR),0) vlr_apr
										FROM USU_TADTMOV A
										LEFT JOIN USU_TADTAREA B
										ON A.USU_AREA = B.USU_CODAREA
										WHERE A.USU_AREA IS NOT NULL
										AND to_char(USU_DTLANC, 'dd/mm/yyyy') = to_date('$dia','dd/mm/yyyy')
										$c1
										GROUP BY B.USU_DESCAREA,B.USU_CODAREA
										ORDER BY B.USU_DESCAREA) G
										right JOIN
										  (SELECT * FROM (SELECT D.USU_DESCAREA,D.USU_CODAREA,      
										    SUM(B.USU_VLRPRE) VLRPRE
										  FROM USU_TADTPREV A
										  INNER JOIN USU_TPREVDET B
										  ON A.USU_CODPREV = B.USU_CODPREV
										  INNER JOIN e070fil C
										  ON A.USU_CODFIL = C.CODFIL
										  INNER JOIN USU_TADTAREA D
										  ON B.USU_CODAREA = D.USU_CODAREA
										  WHERE to_date('$dia','dd/mm/yyyy') BETWEEN TO_CHAR(A.USU_DATINI,'DD/MM/YYYY') AND TO_CHAR(A.USU_DATFIM,'DD/MM/YYYY')
										  $c2										  
										  GROUP BY D.USU_DESCAREA,D.USU_CODAREA) F
										  ) E 
										ON G.USU_DESCAREA = E.USU_DESCAREA");
		if($query -> num_rows() > 0) {
			return $query->result();
		} else {
			return false;
		}
	}
	
	function unidadeif($diaini, $diafim, $fil, $area, $dtpg) {
		$c1 = "where to_char(b.USU_DTLANC, 'dd/mm/yyyy') between to_date('$diaini','dd/mm/yyyy') and to_date('$diafim','dd/mm/yyyy')";
		$c2 = "WHERE (TO_DATE(A.usu_datini), TO_DATE(A.usu_datfim)) overlaps (TO_DATE('$diaini'), TO_DATE('$diafim'))";		
		if($fil != null) {
			$c1 .= "AND A.CODFIL in ($fil)";
			$c2 .= "AND A.USU_CODFIL in ($fil)";
		}
		
		if($area != null) {
			$c1 .= "AND B.USU_AREA = '$area'";
			$c2 .= "AND B.USU_CODAREA = '$area'";
		}
		
		if ($dtpg != null) {
			$c1 = "where c.datpre = '$dtpg' and c.codtns = '90530'";
			$c2 = "where (TO_DATE(A.usu_datini), TO_DATE(A.usu_datfim)) overlaps (TO_DATE('$dtpg'), TO_DATE('$dtpg'))";
		}
		
		$query = $this->db->query("SELECT case 
											WHEN D.SIGFIL IS NOT NULL THEN D.SIGFIL
											ELSE E.SIGFIL
											END SIGFIL, 
											CASE WHEN D.USU_INSTAN IS NOT NULL THEN D.USU_INSTAN
											ELSE E.USU_INSTAN
											END USU_INSTAN,
										  D.CODFIL,
										  NVL(E.VLRPRE,0) AS VLRPRE,
										  NVL(D.VLR_ADT,0) VLR_ADT,
										  NVL(D.VLR_APR,0) VLR_APR
										FROM
										  (SELECT a.SIGFIL,
										    a.USU_INSTAN,
										    A.CODFIL,
										    SUM(NVL(b.USU_VLRADT,0)) vlr_adt,
										    SUM(NVL(b.USU_VLRAPR,0)) vlr_apr
										  FROM e070fil a
										  INNER JOIN USU_TADTMOV b
										  ON a.CODFIL = b.USU_FILIAL  
										  $c1
										  GROUP BY a.SIGFIL,
										    a.USU_INSTAN,
										    A.CODFIL
										  ) D
										LEFT JOIN
										  (SELECT * FROM (SELECT C.SIGFIL,
										    C.USU_INSTAN,										    
										    SUM(B.USU_VLRPRE) VLRPRE
										  FROM USU_TADTPREV A
										  INNER JOIN USU_TPREVDET B
										  ON A.USU_CODPREV = B.USU_CODPREV
										  INNER JOIN e070fil C
										  ON A.USU_CODFIL = C.CODFIL
										  $c2
										  GROUP BY C.SIGFIL,
										    C.USU_INSTAN) F										   
										  ) E ON D.SIGFIL = E.SIGFIL
										AND D.USU_INSTAN  = E.USU_INSTAN");
		if($query -> num_rows() > 0) {
			return $query->result();
		} else {
			return false;
		}
	}
	
	function regiaoif($diaini, $diafim, $fil, $area, $dtpg) {
		$c1 = "where to_char(b.USU_DTLANC, 'dd/mm/yyyy') between to_date('$diaini','dd/mm/yyyy') and to_date('$diafim','dd/mm/yyyy')";
		$c2 = "WHERE (TO_DATE(A.usu_datini), TO_DATE(A.usu_datfim)) overlaps (TO_DATE('$diaini'), TO_DATE('$diafim'))";
		if($fil != null) {
			$c1 .= "AND A.CODFIL in ($fil)";
			$c2 .= "AND A.USU_CODFIL in ($fil)";
		}
		
		if($area != null) {
			$c1 .= "AND B.USU_AREA = '$area'";
			$c2 .= "AND B.USU_CODAREA = '$area'";
		}
		
		if ($dtpg != null) {
			$c1 = "where c.datpre = '$dtpg' and c.codtns = '90530'";
			$c2 = "where (TO_DATE(A.usu_datini), TO_DATE(A.usu_datfim)) overlaps (TO_DATE('$dtpg'), TO_DATE('$dtpg'))";
		}
		
		$query = $this->db->query("SELECT case 
											when D.SIGUFS is not null then D.SIGUFS
											else E.SIGUFS
											end SIGUFS, 
										  	NVL(E.VLRPRE,0) VLRPRE, 
											nvl(D.VLR_ADT,0) VLR_ADT, 
											D.VLR_APR 
											FROM (SELECT 
											a.SIGUFS,
											sum(nvl(b.USU_VLRADT,0)) vlr_adt,
											sum(nvl(b.USU_VLRAPR,0)) vlr_apr 
											FROM e070fil a
											inner join USU_TADTMOV b
											on a.CODFIL = b.USU_FILIAL											
											$c1
											group by a.SIGUFS) D
											LEFT JOIN
											  (SELECT * FROM (SELECT C.SIGUFS,      
											    SUM(B.USU_VLRPRE) VLRPRE
											  FROM USU_TADTPREV A
											  INNER JOIN USU_TPREVDET B
											  ON A.USU_CODPREV = B.USU_CODPREV
											  INNER JOIN e070fil C
											  ON A.USU_CODFIL = C.CODFIL											  
											  $c2											  
											  GROUP BY C.SIGUFS) F
											  ) E 
											ON D.SIGUFS = E.SIGUFS");
		if($query -> num_rows() > 0) {
			return $query->result();
		} else {
			return false;
		}
	}
	
	function areaif($diaini, $diafim, $fil, $area, $dtpg, $dtpgfim) {
		$c1 = "AND to_char(A.USU_DTLANC, 'dd/mm/yyyy') between to_date('$diaini','dd/mm/yyyy') and to_date('$diafim','dd/mm/yyyy')";
		$c2 = "WHERE (TO_DATE(A.usu_datini), TO_DATE(A.usu_datfim)) overlaps (TO_DATE('$diaini'), TO_DATE('$diafim'))";
		if($fil != null) {
			$c1 .= "AND A.USU_FILIAL in ($fil)";
			$c2 .= "AND A.USU_CODFIL in ($fil)";
		}
		
		if($area != null) {
			$c1 .= "AND USU_AREA = '$area'";
			$c2 .= "AND B.USU_CODAREA = '$area'";
		}
		
		if ($dtpg != null) {
			$c1 = "and c.datpre = '$dtpg' and c.codtns = '90530'";
			$c2 = "where (TO_DATE(A.usu_datini), TO_DATE(A.usu_datfim)) overlaps (TO_DATE('$dtpg'), TO_DATE('$dtpg'))";
		}
		
		$query = $this->db->query("SELECT case 
											when G.USU_DESCAREA is not null then G.USU_DESCAREA
											else E.USU_DESCAREA
											END USU_DESCAREA, NVL(E.VLRPRE,0) AS VLRPRE,
										  nvl(G.VLR_ADT,0) VLR_ADT,
										  nvl(G.VLR_APR,0) VLR_APR FROM (SELECT 
										 B.USU_DESCAREA,
										  NVL(SUM(A.USU_VLRADT),0) vlr_adt,
										  NVL(SUM(A.USU_VLRAPR),0) vlr_apr
										FROM USU_TADTMOV A
										LEFT JOIN USU_TADTAREA B
										ON A.USU_AREA = B.USU_CODAREA
										WHERE A.USU_AREA IS NOT NULL										
										$c1
										GROUP BY B.USU_DESCAREA
										ORDER BY B.USU_DESCAREA) G
										FULL JOIN
										  (SELECT * FROM (SELECT D.USU_DESCAREA,      
										    SUM(B.USU_VLRPRE) VLRPRE
										  FROM USU_TADTPREV A
										  INNER JOIN USU_TPREVDET B
										  ON A.USU_CODPREV = B.USU_CODPREV
										  INNER JOIN e070fil C
										  ON A.USU_CODFIL = C.CODFIL
										  INNER JOIN USU_TADTAREA D
										  ON B.USU_CODAREA = D.USU_CODAREA										  
										  $c2										  
										  GROUP BY D.USU_DESCAREA) F
										  ) E 
										ON G.USU_DESCAREA = E.USU_DESCAREA");
		if($query -> num_rows() > 0) {
			return $query->result();
		} else {
			return false;
		}
	}
	
}