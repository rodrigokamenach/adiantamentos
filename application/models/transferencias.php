<?php
Class Transferencias extends CI_Model {
	function listar($dia, $filial, $area, $pedido, $codfor) {
		$condicao = "AND A.USU_DTLANC = to_date('$dia', 'dd/mm/yyyy')";
		
		if($filial) {
			$condicao .= "AND A.USU_FILIAL IN ($filial)";			
		}
		
		if($area) {
			$condicao .= "AND A.USU_AREA = '$area'";
		}
		
		if($codfor) {
			$condicao .= "AND C.CODFOR = $codfor";
		}
		
		if($pedido) {
			$condicao = "AND A.USU_NUMOCP = $pedido"; 
		}
		//echo $condicao;
		$query = $this->db->query("SELECT TO_CHAR(A.USU_DTLANC,'DD/MM/YYYY') USU_DTLANC,
										  A.USU_ID,
										  A.USU_NUMOCP,
										  A.USU_CODEMP,
										  A.USU_FILIAL,
										  B.SIGFIL,
										  B.USU_INSTAN,
										  E.APEFOR,
										  C.CODFOR,
										  CASE
										    WHEN F.NOMUSU IS NULL
										    THEN 'SEM USU'
										    ELSE F.NOMUSU
										  END NOMUSU,
										  CASE
										    WHEN C.VLRORI IS NULL
										    OR C.VLRORI    = 0
										    THEN C.VLRLIQ
										    ELSE C.VLRORI
										  END VLRLIQ,
										  A.USU_VLRADT,
										  (SELECT NVL(SUM((
											  CASE
											    WHEN M.VLRABE = M.VLRORI
											    THEN M.VLRORI
											    ELSE M.VLRABE
											  END)),0) VLRABE FROM E420OCP J 
						                    INNER JOIN USU_TADTMOV L
						                    ON J.CODEMP = L.USU_CODEMP
						                    AND J.CODFIL = L.USU_FILIAL
						                    AND J.NUMOCP = L.USU_NUMOCP 
						                    INNER JOIN E501TCP M
						                    ON J.CODEMP = M.CODEMP
						                    AND J.CODFIL = M.CODFIL
						                    AND L.USU_NUMTIT = M.NUMTIT
						                    INNER JOIN E095FOR N
						                    ON J.CODFOR = N.CODFOR
						                    WHERE L.USU_NUMTIT IS NOT NULL
						                    AND M.SITTIT <> 'LQ'
											AND M.CODTNS = '90530'
						                    AND J.CODFOR = C.CODFOR
											AND L.USU_DTLANC >= '01/05/2016'
						                    GROUP BY J.CODFOR, N.APEFOR) VLRABE,
										  (SELECT DISTINCT trim(l.obssol) obssol
										  FROM E410LCO j
										  INNER JOIN e405sol l
										  ON j.codemp    = l.codemp
										  AND j.numcot   = l.numcot
										  AND l.filsol   = j.filocp
										  AND l.seqsol   = 1
										  WHERE j.numocp = A.USU_NUMOCP
										  AND j.filocp   = A.USU_FILIAL
										  AND j.codemp   = A.USU_CODEMP
										  AND ROWNUM     = 1
										  ) OBSOCP,
										  A.USU_ADTPRI,
										  A.USU_OBS,
										  A.USU_AREA,
										  H.USU_DESCAREA,
										  CASE
										    WHEN D.QTDABE = 0
										    THEN 'LQ'
										    WHEN D.QTDPED > D.QTDREC
										    AND D.QTDABE  > 0
										    THEN 'AP'
										    WHEN D.QTDPED = D.QTDABE
										    THEN 'AT'
										  END RECB
										FROM USU_TADTMOV A
										INNER JOIN E070FIL B
										ON A.USU_CODEMP  = B.CODEMP
										AND A.USU_FILIAL = B.CODFIL
										INNER JOIN E420OCP C
										ON A.USU_CODEMP  = C.CODEMP
										AND A.USU_FILIAL = C.CODFIL
										AND A.USU_NUMOCP = C.NUMOCP
										AND C.SITOCP    IN (1,2)
										LEFT JOIN
										  (SELECT CODEMP,
										    CODFIL,
										    NUMOCP,
										    SUM(QTDPED)QTDPED,
										    SUM(QTDREC)QTDREC,
										    SUM(QTDABE)QTDABE
										  FROM E420IPO
										  GROUP BY CODEMP,
										    CODFIL,
										    NUMOCP
										  ) D
										ON C.CODEMP  = D.CODEMP
										AND C.CODFIL = D.CODFIL
										AND C.NUMOCP = D.NUMOCP
										INNER JOIN E095FOR E
										ON C.CODFOR = E.CODFOR
										LEFT JOIN R999USU F
										ON A.USU_LANCUSU = F.CODUSU
										INNER JOIN USU_TADTDIA G
										ON A.USU_DTLANC         = G.USU_DT
										AND G.USU_STDIA         = 'A'
										LEFT JOIN USU_TADTAREA H
										ON A.USU_AREA = H.USU_CODAREA
										WHERE a.usu_numtit     IS NULL
										--AND NVL(a.usu_vlrapr,0) = 0
										AND A.USU_APRDIR IS NULL
										$condicao
										ORDER BY 4,5,12 desc");
		//$query = $this->db->query("select * from MGCLI.EST_ADT_MOVITEMS WHERE ROWNUM BETWEEN $start AND $limit order by 1 desc");	
		if($query -> num_rows() > 0) {
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
}