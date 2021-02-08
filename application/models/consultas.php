<?php
Class Consultas extends CI_Model {
	function listar($dtini, $dtfim, $filial, $area, $pedido, $situacao, $recebe, $codfor, $dtpg, $dtpgfim) {
		$condicao = "WHERE I.USU_DTLANC between to_date('$dtini', 'dd/mm/yyyy') and to_date('$dtfim', 'dd/mm/yyyy')";
		
		if($filial != null) {
			$condicao .= "AND I.USU_FILIAL in ($filial)";			
		}
		
		if($area != null) {
			$condicao .= "AND I.USU_AREA = '$area'";
		}				
		
		if($recebe != null) {
			$condicao .= "AND I.RECB = '$recebe'";		
		}
		
		if($situacao != null) {
			$condicao .= "AND I.SITUACAO = '$situacao'";
		}
		
		
		if($dtpg != null)  {
			if ($dtpgfim == null) {
				$condicao = "WHERE TO_CHAR(I.DATPRE, 'DD/MM/YYYY') = to_date('$dtpg', 'dd/mm/yyyy')";
			} else {
				$condicao = "WHERE TO_CHAR(I.DATPRE, 'DD/MM/YYYY') between to_date('$dtpg', 'dd/mm/yyyy') and to_date('$dtpgfim', 'dd/mm/yyyy')";
			}
		}
		
		if($codfor != null) {
			if ($dtini != null) {
				$condicao .= "AND I.CODFOR = $codfor"; 	
			} else {
				$condicao = "WHERE I.CODFOR = $codfor AND I.USU_NUMTIT IS NOT NULL AND I.VLRABE > 0 AND I.USU_DTLANC >= to_date('01/05/2016', 'dd/mm/yyyy') and I.CODTNS = '90530'";
			}
		}
		
		if($pedido != null) {
			$condicao = "WHERE I.USU_NUMOCP = $pedido";
		}
		
		$query = $this->db->query("SELECT I.USU_DTLANC,
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
										NVL(I.VLRPAGO,0) VLRPAGO,
										NVL((I.USU_VLRAPR-I.VLRPAGO),0) SALDO,                    
                    					NVL(I.VLRABE,0) VLRABE,
										I.SITUACAO,
					                    I.CODTNS,
					                    TO_CHAR(I.DATPRE, 'DD/MM/YY') DATPRE,
										I.USU_NUMTIT,
										I.USUAPR,
										I.USUDIR,
										I.USU_OBS,
										I.USU_ADTPRI,
										(SELECT DISTINCT trim(l.obssol) obssol
										    FROM E410LCO j
										    INNER JOIN e405sol l
										    ON j.codemp    = l.codemp
										    AND j.numcot   = l.numcot
										    AND l.filsol   = j.filocp
										    AND l.seqsol   = 1
										    where j.numocp = I.USU_NUMOCP   
										    AND j.filocp   = I.USU_FILIAL
										    AND j.codemp   = I.USU_CODEMP
										    AND ROWNUM = 1
										  ) OBSOCP,
										I.USU_AREA,
                    					I.USU_DESCAREA,
										I.RECB FROM (SELECT to_char(A.USU_DTLANC,'dd/mm/yy')USU_DTLANC,
										  A.USU_NUMOCP,
										  A.USU_CODEMP,
										  A.USU_FILIAL,
										  B.SIGFIL,
										  D.APEFOR,
										  D.CODFOR,
										  B.USU_INSTAN,  
										  E.NOMUSU USULANC,
										  case 
											when C.VLRORI IS NULL OR C.VLRORI = 0 THEN C.VLRLIQ
											ELSE C.VLRORI
										  END VLRLIQ,
										  A.USU_VLRADT,
										  nvl(A.USU_VLRAPR,0) USU_VLRAPR,                      
					                      CASE 
					                        WHEN F.VLRABE = A.USU_VLRAPR THEN A.USU_VLRAPR
					                        ELSE A.USU_VLRAPR-F.VLRABE
					                      END VLRPAGO,
					            			F.VLRABE,
										  case
										    when A.USU_VLRAPR is NULL and A.USU_APRDIR IS NULL AND A.USU_APRUSU IS NULL AND A.USU_NUMTIT IS NULL then 'Aguar Aprov Gerente'
											when A.USU_APRDIR IS NULL AND A.USU_APRUSU IS NOT NULL AND A.USU_NUMTIT IS NULL then 'Aguar Aprov Diretor'
											when A.USU_VLRAPR is not NULL and A.USU_NUMTIT is null AND A.USU_APRDIR IS NOT NULL then 'Aprovado'
                        					when F.CODTNS = '90530' AND nvl((A.USU_VLRAPR-F.VLRABE),0) = 0 then 'Pago'
										    when F.CODTNS = '90514' AND nvl((A.USU_VLRAPR-F.VLRABE),0) = 0 then 'Aberto'                        
										    when NVL((A.USU_VLRAPR-F.VLRABE),0) > 0 and C.VLRORI > NVL((C.VLRORI-F.VLRABE),0) then 'Aberto Parcial'
										    when A.USU_VLRADT = NVL((A.USU_VLRADT-F.VLRABE),0) then 'Pago'                        					
										  end SITUACAO,
					                      F.CODTNS,
					                      CASE 
					                        WHEN F.DATPPT = NULL OR F.DATPPT = '31/12/1900' THEN NULL
					                        ELSE F.DATPPT
					                      END DATPRE,
										  A.USU_NUMTIT,
										  G.NOMUSU USUAPR,
										  L.NOMUSU USUDIR,
										  A.USU_OBS,
										  A.USU_ADTPRI,
										  C.OBSOCP,
										  A.USU_AREA,
                      					  J.USU_DESCAREA,
										  CASE 
										    WHEN H.QTDABE = 0 THEN 'Total'
										    WHEN H.QTDPED > H.QTDREC AND H.QTDABE > 0 AND H.QTDREC > 0 THEN 'Parcial'
										    WHEN H.QTDPED = H.QTDABE THEN 'A Receber'
										  END RECB
										FROM USU_TADTMOV A
										INNER JOIN E070FIL B
										ON A.USU_CODEMP  = B.CODEMP
										AND A.USU_FILIAL = B.CODFIL                  
										LEFT JOIN E420OCP C
										ON A.USU_CODEMP  = C.CODEMP
										AND A.USU_FILIAL = C.CODFIL
										AND A.USU_NUMOCP = C.NUMOCP										
										LEFT JOIN E095FOR D
										ON C.CODFOR = D.CODFOR
										LEFT JOIN e099USU E
										ON A.USU_LANCUSU = E.CODUSU
										AND E.CODEMP = 1
										LEFT JOIN e099USU L
										ON A.USU_APRDIR = L.CODUSU
										AND L.CODEMP = 1						
										LEFT JOIN (SELECT CODEMP,    
										    FILOCP,
										    NUMOCP,
					                        CODTNS,
					                        DATPPT,
					              			NUMTIT,
										    SUM(VLRABE) VLRABE
										  FROM E501TCP
										  WHERE CODTPT IN ('ADT', 'PRC', 'CRE')
										  AND NUMOCP <> 0
										  GROUP BY CODEMP,    
										    CODFIL,
										    FILOCP,
										    NUMOCP,
                        					CODTNS,
                        					DATPPT,
					              			NUMTIT
										  ) F
										ON A.USU_CODEMP  = F.CODEMP
										AND A.USU_FILIAL = F.FILOCP
										AND (A.USU_NUMOCP = F.NUMOCP and A.USU_NUMTIT = F.NUMTIT)
										LEFT JOIN e099USU G
										ON A.USU_APRUSU = G.CODUSU
										AND G.CODEMP = 1
										LEFT JOIN (SELECT CODEMP, CODFIL, NUMOCP, SUM(QTDPED) QTDPED, SUM(QTDREC)QTDREC, SUM(QTDABE)QTDABE FROM((SELECT CODEMP, CODFIL, NUMOCP, SUM(QTDPED)QTDPED, SUM(QTDREC)QTDREC, SUM(QTDABE)QTDABE FROM E420IPO
										            GROUP BY CODEMP, CODFIL, NUMOCP)
										            UNION 
										           (SELECT CODEMP, CODFIL, NUMOCP, SUM(QTDPED)QTDPED, SUM(QTDREC)QTDREC, SUM(QTDABE)QTDABE FROM E420ISO
										           GROUP BY CODEMP, CODFIL, NUMOCP))
										GROUP BY CODEMP, CODFIL, NUMOCP) H
										ON A.USU_CODEMP  = H.CODEMP
										AND A.USU_FILIAL = H.CODFIL
										AND A.USU_NUMOCP = H.NUMOCP
					                    LEFT JOIN USU_TADTAREA J
					                    ON A.USU_AREA = J.USU_CODAREA) I
										$condicao
										ORDER BY 3,4,11 desc");
		//$query = $this->db->query("select * from MGCLI.EST_ADT_MOVITEMS WHERE ROWNUM BETWEEN $start AND $limit order by 1 desc");	
		if($query -> num_rows() > 0) {
			return $query->result();	
		} else {
			return false;
		}
	}
	
	function getItemPed($emp, $filial, $oc) {
				
		$query = $this->db->query("SELECT CODEMP CODEMP,
										CODFIL CODFIL,
										NUMOCP NUMOCP,
										CODPRO proser,
										DESpro DESCRI,
										PREUNI PREUNI,
										QTDPED QTDPED FROM (SELECT a.codemp, a.codfil, a.numocp, B.SEQIPO, b.codpro, d.despro, b.preuni, b.qtdped FROM e420ocp a
										inner join e420ipo b
										on a.codemp = b.codemp
										and a.codfil = b.codfil
										and a.numocp = b.numocp
					                    inner join e075pro d
					                    on b.codemp = d.codemp
					                    and b.codpro = d.codpro
										union
										SELECT a.codemp, a.codfil, a.numocp, C.SEQISO, c.codser, e.desser, C.PREUNI, C.QTDPED FROM e420ocp a
										inner join e420iso c
										on a.codemp = c.codemp
										and a.codfil = c.codfil
										and a.numocp = c.numocp
					                    inner join E080SER e
					                    on a.codemp = e.codemp
					                    and c.codser = e.CODSER)
										WHERE NUMOCP   = $oc
										AND CODFIL     = $filial
										AND CODEMP = $emp");
		if($query -> num_rows() > 0) {
			return $query->result();
		} else {
			return false;
		}
		
	}
	
	
}