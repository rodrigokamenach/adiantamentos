<?php
Class Gerados extends CI_Model {
	function listar($dia, $filial, $area, $codfor) {
		$condicao = "WHERE to_CHAR(I.USU_DTLANC, 'dd/mm/yyyy') = to_date('$dia', 'dd/mm/yyyy')";
		
		if($filial) {
			$condicao .= "AND I.USU_FILIAL = $filial";			
		}
		
		if($area) {
			$condicao .= "AND I.USU_AREA = '$area'";
		}
		
		if($codfor) {
			$condicao .= "AND I.CODFOR = $codfor";
		}
		
		$query = $this->db->query("SELECT I.USU_DTLANC,
										I.USU_ID,
										I.USU_NUMOCP,
										I.USU_CODEMP,
										I.USU_FILIAL,
										I.SIGFIL,
										I.APEFOR,
										I.CODFOR,
										I.USU_INSTAN,
										I.USULANC,
										I.VLRLIQ,
										I.USU_VLRADT,
										I.USU_VLRAPR,
										I.VLRPAGO,
										I.USU_NUMTIT,
										I.USUAPR,
										I.USUDIR,
										I.USU_OBS,
										I.USU_ADTPRI,
										I.OBSOCP,
										I.USU_AREA,
                    					I.USU_DESCAREA,
										I.RECB FROM (SELECT A.USU_DTLANC,
										  A.USU_ID,
										  A.USU_NUMOCP,
										  A.USU_CODEMP,
										  A.USU_FILIAL,
										  B.SIGFIL,
										  D.APEFOR,
										  C.CODFOR,
										  B.USU_INSTAN,  
										  E.NOMUSU USULANC,
										  case 
											when C.VLRORI IS NULL OR C.VLRORI = 0 THEN C.VLRLIQ
											ELSE C.VLRORI
										  END VLRLIQ,
										  A.USU_VLRADT,
										  A.USU_VLRAPR,
										  nvl((C.VLRORI-F.VLRABE),0) VLRPAGO,
										  case
										    when A.USU_VLRAPR is NULL then 'Aguar. Aprov'
										    when nvl((C.VLRORI-F.VLRABE),0) = 0 then 'Aberto Total'
										    when NVL((C.VLRORI-F.VLRABE),0) > 0 and C.VLRORI > NVL((C.VLRORI-F.VLRABE),0) then 'Aberto Parcial'
										    when C.VLRORI = NVL((C.VLRORI-F.VLRABE),0) then 'Liquidado'    
										  end SITUACAO,
										  A.USU_NUMTIT,
										  G.NOMUSU USUAPR,
										  M.NOMUSU USUDIR,
										  A.USU_OBS,
										  A.USU_ADTPRI,
										  C.OBSOCP,
										  A.USU_AREA,
                      					  L.USU_DESCAREA,
										  CASE 
										    WHEN H.QTDABE = 0 THEN 'Recebido'
										    WHEN H.QTDPED > H.QTDREC AND H.QTDABE > 0 THEN 'Recebido Parcial'
										    WHEN H.QTDPED = H.QTDABE THEN 'A Receber'
										  END RECB
										FROM USU_TADTMOV A
										INNER JOIN E070FIL B
										ON A.USU_CODEMP  = B.CODEMP
										AND A.USU_FILIAL = B.CODFIL
										INNER JOIN E420OCP C
										ON A.USU_CODEMP  = C.CODEMP
										AND A.USU_FILIAL = C.CODFIL
										AND A.USU_NUMOCP = C.NUMOCP
										INNER JOIN E095FOR D
										ON C.CODFOR = D.CODFOR
										LEFT JOIN R999USU E
										ON A.USU_LANCUSU = E.CODUSU
										LEFT JOIN (SELECT CODEMP,    
										    FILOCP,
										    NUMOCP,
										    SUM(VLRABE) VLRABE
										  FROM E501TCP
										  WHERE CODTPT IN ('ADT', 'PRC', 'CRE')
										  AND NUMOCP <> 0
										  GROUP BY CODEMP,    
										    CODFIL,
										    FILOCP,
										    NUMOCP
										  ) F
										ON A.USU_CODEMP  = F.CODEMP
										AND A.USU_FILIAL = F.FILOCP
										AND A.USU_NUMOCP = F.NUMOCP
										LEFT JOIN R999USU G
										ON A.USU_APRUSU = G.CODUSU
										LEFT JOIN R999USU M
										ON A.USU_APRDIR = M.CODUSU
										LEFT JOIN (SELECT CODEMP, CODFIL, NUMOCP, SUM(QTDPED) QTDPED, SUM(QTDREC)QTDREC, SUM(QTDABE)QTDABE FROM((SELECT CODEMP, CODFIL, NUMOCP, SUM(QTDPED)QTDPED, SUM(QTDREC)QTDREC, SUM(QTDABE)QTDABE FROM E420IPO
										            GROUP BY CODEMP, CODFIL, NUMOCP)
										            UNION 
										           (SELECT CODEMP, CODFIL, NUMOCP, SUM(QTDPED)QTDPED, SUM(QTDREC)QTDREC, SUM(QTDABE)QTDABE FROM E420ISO
										           GROUP BY CODEMP, CODFIL, NUMOCP))
										GROUP BY CODEMP, CODFIL, NUMOCP) H
										ON A.USU_CODEMP  = H.CODEMP
										AND A.USU_FILIAL = H.CODFIL
										AND A.USU_NUMOCP = H.NUMOCP
										LEFT join USU_TADTDIA J
										ON TO_CHAR(A.USU_DTLANC,'DD/MM/YYYY') = TO_CHAR(J.USU_DT,'DD/MM/YYYY')
					                    LEFT JOIN USU_TADTAREA L
					                    ON A.USU_AREA = L.USU_CODAREA
										where nvl(a.USU_VLRAPR,0) <> 0
										AND A.USU_APRDIR IS NOT NULL
										AND J.USU_STDIA = 'F'
										AND A.USU_NUMTIT IS NULL) I
										$condicao
										order by 4,5, 12 desc");
		//$query = $this->db->query("select * from MGCLI.EST_ADT_MOVITEMS WHERE ROWNUM BETWEEN $start AND $limit order by 1 desc");	
		if($query -> num_rows() > 0) {
			return $query->result();	
		} else {
			return false;
		}
	}
				
	
	function list_excel($id) {
		$query = $this->db->query("SELECT I.USU_DTLANC,
										I.USU_NUMOCP,
										I.USU_CODEMP,
										I.USU_FILIAL,
										I.SIGFIL,
										I.CODFOR,
										I.APEFOR,
										I.USU_INSTAN,
										I.USULANC,
										I.VLRLIQ,
										I.USU_VLRADT,
										I.USU_VLRAPR,
										I.VLRPAGO,
										I.SITUACAO,
										I.USU_NUMTIT,
										I.USUAPR,
										I.USU_OBS,
										I.USU_ADTPRI,
										I.OBSOCP,
										I.USU_AREA,
										I.USU_DESCAREA,
										I.RECB,
					                    I.CGCCPF,
					                    I.CODBAN,
					                    I.NOMBAN,
					                    I.CODAGE,
					                    I.CCBFOR FROM (SELECT A.USU_DTLANC,
										  A.USU_NUMOCP,
										  A.USU_CODEMP,
										  A.USU_FILIAL,
										  B.SIGFIL,
										  C.CODFOR,
										  D.APEFOR,
										  B.USU_INSTAN,  
										  E.NOMUSU USULANC,
										  case 
											when C.VLRORI IS NULL OR C.VLRORI = 0 THEN C.VLRLIQ
											ELSE C.VLRORI
										  END VLRLIQ,
										  A.USU_VLRADT,
										  A.USU_VLRAPR,
										  nvl((C.VLRLIQ-F.VLRABE),0) VLRPAGO,
										  case
										    when A.USU_VLRAPR is NULL then 'Aguar. Aprov'
										    when nvl((C.VLRORI-F.VLRABE),0) = 0 then 'Aberto Total'
										    when NVL((C.VLRORI-F.VLRABE),0) > 0 and C.VLRORI > NVL((C.VLRORI-F.VLRABE),0) then 'Aberto Parcial'
										    when C.VLRORI = NVL((C.VLRORI-F.VLRABE),0) then 'Liquidado'    
										  end SITUACAO,
										  A.USU_NUMTIT,
										  G.NOMUSU USUAPR,
										  A.USU_OBS,
										  A.USU_ADTPRI,
										  C.OBSOCP,
										  A.USU_AREA,
										  O.USU_DESCAREA,
										  CASE 
										    WHEN H.QTDABE = 0 THEN 'Recebido'
										    WHEN H.QTDPED > H.QTDREC AND H.QTDABE > 0 THEN 'Recebido Parcial'
										    WHEN H.QTDPED = H.QTDABE THEN 'A Receber'
										  END RECB,
					                      D.CGCCPF,
					                      N.CODBAN,
					                      N.NOMBAN,
					                      M.CODAGE,
					                      M.CCBFOR
										FROM USU_TADTMOV A
										INNER JOIN E070FIL B
										ON A.USU_CODEMP  = B.CODEMP
										AND A.USU_FILIAL = B.CODFIL
										INNER JOIN E420OCP C
										ON A.USU_CODEMP  = C.CODEMP
										AND A.USU_FILIAL = C.CODFIL
										AND A.USU_NUMOCP = C.NUMOCP
										LEFT JOIN E095FOR D
										ON C.CODFOR = D.CODFOR
										LEFT JOIN R999USU E
										ON A.USU_LANCUSU = E.CODUSU
										LEFT JOIN (SELECT CODEMP,    
										    FILOCP,
										    NUMOCP,
										    SUM(VLRABE) VLRABE
										  FROM E501TCP
										  WHERE CODTPT IN ('ADT', 'PRC')
										  AND NUMOCP <> 0
										  GROUP BY CODEMP,    
										    CODFIL,
										    FILOCP,
										    NUMOCP
										  ) F
										ON A.USU_CODEMP  = F.CODEMP
										AND A.USU_FILIAL = F.FILOCP
										AND A.USU_NUMOCP = F.NUMOCP
										LEFT JOIN R999USU G
										ON A.USU_APRUSU = G.CODUSU
										LEFT JOIN (SELECT CODEMP, CODFIL, NUMOCP, SUM(QTDPED) QTDPED, SUM(QTDREC)QTDREC, SUM(QTDABE)QTDABE FROM((SELECT CODEMP, CODFIL, NUMOCP, SUM(QTDPED)QTDPED, SUM(QTDREC)QTDREC, SUM(QTDABE)QTDABE FROM E420IPO
										            GROUP BY CODEMP, CODFIL, NUMOCP)
										            UNION 
										           (SELECT CODEMP, CODFIL, NUMOCP, SUM(QTDPED)QTDPED, SUM(QTDREC)QTDREC, SUM(QTDABE)QTDABE FROM E420ISO
										           GROUP BY CODEMP, CODFIL, NUMOCP))
										GROUP BY CODEMP, CODFIL, NUMOCP) H
										ON A.USU_CODEMP  = H.CODEMP
										AND A.USU_FILIAL = H.CODFIL
										AND A.USU_NUMOCP = H.NUMOCP
										LEFT join USU_TADTDIA J
										ON TO_CHAR(A.USU_DTLANC,'DD/MM/YYYY') = TO_CHAR(J.USU_DT,'DD/MM/YYYY')					                    
					                    LEFT JOIN E095HFO M
					                    ON C.CODEMP = M.CODEMP
					                    AND C.CODFIL = M.CODFIL
					                    AND C.CODFOR = M.CODFOR
					                    LEFT JOIN E030BAN N
					                    ON M.CODBAN = N.CODBAN
										LEFT JOIN USU_TADTAREA O
					                    ON A.USU_AREA = O.USU_CODAREA
										where nvl(a.USU_VLRAPR,0) <> 0
										AND J.USU_STDIA = 'F'
										AND A.USU_CODEMP||A.USU_FILIAL||A.USU_NUMOCP||A.USU_ID IN ($id)
                    					) I");
		
		if($query->num_rows() > 0) {
			return $query->result();
		} else {
			return false;
		}
	}
	
	function ConsultaTit($emp, $filial, $oc) {
		$query = $this->db->query("SELECT distinct a.codemp,
										  a.codfil,
										  a.numocp,
                      					  (SELECT max(to_number(case 
											  when INSTR(c.numtit,'_') > 0 then SUBSTR(c.numtit,INSTR(c.numtit,'_')+1,11)
											  when INSTR(c.numtit,'/') > 0 then SUBSTR(c.numtit,INSTR(c.numtit,'/')+1,11)
											  end)) FROM e501tcp c where c.codemp = a.codemp and c.codfil = a.codfil and c.numocp = a.numocp and INSTR(c.numtit, 'A') = 0) ultpar,                      										  
										  (select sum(b.vlrORI) from e501tcp b where b.codemp = a.codemp and b.codfil = a.codfil and b.numocp = a.numocp) vlrORI  
										FROM e501tcp a
										WHERE codtpt = 'ADT'
										AND numocp  <> 0
										AND numocp  IS NOT NULL
										AND codemp   = $emp
										AND codfil   = $filial
										AND NUMOCP = $oc");
		
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
}