<?php
Class Aprovados extends CI_Model {
	function listar($dia, $filial, $area, $pedido, $codfor, $areauser, $gerente, $diretor, $acao) {
		$condicao = "WHERE to_char(I.USU_DTLANC,'dd/mm/yyyy') = to_date('$dia', 'dd/mm/yyyy') AND (RECB <> 'Total' or RECB is null)";
		
		if($filial) {
			$condicao .= "AND I.USU_FILIAL in ($filial)";			
		}
		
		if($area) {
			$condicao .= "AND I.USU_AREA = '$area'";
		} elseif ($areauser) {
			$areauser = str_replace(",", "','", $areauser);		
			$condicao .= "AND I.USU_AREA in ('$areauser')";
		}
		
		if($codfor) {
			$condicao .= "AND I.CODFOR = $codfor";
		}

		if($pedido) {
			$condicao = "WHERE I.USU_NUMOCP = $pedido";
		}
		
		if ($gerente == 'S') {
			if ($acao == 'APR') {
				$condapr = 'AND nvl(a.USU_VLRAPR,0) = 0 AND A.USU_APRUSU IS NULL';
			} else {
				$condapr = 'AND A.USU_APRUSU IS NOT NULL';
			}
		} 
		
		if ($diretor == 'S') {
			if($acao == 'APR') {
				$condapr = 'AND nvl(a.USU_VLRAPR,0) <> 0 AND A.USU_NUMTIT IS NULL AND A.USU_APRDIR IS NULL';
			} else {
				$condapr = 'AND nvl(a.USU_VLRAPR,0) <> 0 AND A.USU_NUMTIT IS NULL AND A.USU_APRDIR IS NOT NULL';
			}
		}
		
		//echo $condapr;
		
		$query = $this->db->query("SELECT I.USU_DTLANC,
										I.USU_ID,
										I.USU_NUMOCP,
										I.USU_CODEMP,
										I.USU_FILIAL,
										I.SIGFIL,
										I.APEFOR,
					                    I.CODFOR,
										I.SIGUFS,
					                    (SELECT NVL(SUM((
											  CASE
											    WHEN M.VLRABE = M.VLRORI and TO_date(L.USU_DTLANC) <= sysdate-20 THEN M.VLRORI
											    WHEN TO_date(L.USU_DTLANC) <= sysdate-20 THEN M.VLRABE
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
					                    AND J.CODFOR = I.CODFOR
										AND L.USU_DTLANC >= '01/05/2016'										
					                    GROUP BY J.CODFOR, N.APEFOR) VLRABE,
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
										I.RECB FROM (SELECT A.USU_DTLANC,
										  A.USU_ID,
										  A.USU_NUMOCP,
										  A.USU_CODEMP,
										  A.USU_FILIAL,
										  B.SIGFIL,
										  D.APEFOR,
                      					  D.CODFOR,
										  D.SIGUFS,
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
										    when NVL((C.VLRORI-F.VLRABE),0) > 0 and C.VLRORI > NVL((C.VLRLIQ-F.VLRABE),0) then 'Aberto Parcial'
										    when C.VLRORI = NVL((C.VLRORI-F.VLRABE),0) then 'Liquidado'    
										  end SITUACAO,
										  A.USU_NUMTIT,
										  G.NOMUSU USUAPR,
										  A.USU_APRDIR,
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
										AND C.SITOCP IN (1,2)
										INNER JOIN E095FOR D
										ON C.CODFOR = D.CODFOR
										LEFT JOIN R999USU E
										ON A.USU_LANCUSU = E.CODUSU
										LEFT JOIN R999USU M
            							ON A.USU_APRDIR = M.CODUSU
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
										where J.USU_STDIA = 'F' --nvl(a.USU_VLRAPR,0) = 0
										$condapr
										) I
										$condicao 
										ORDER BY 4,5,14 desc");
		//$query = $this->db->query("select * from MGCLI.EST_ADT_MOVITEMS WHERE ROWNUM BETWEEN $start AND $limit order by 1 desc");	
		if($query -> num_rows() > 0) {
			return $query->result();	
		} else {
			return false;
		}
	}
		
	
	function getDadosPedido($id) {
		$query = $this->db->query("SELECT * FROM USU_TADTMOV where USU_CODEMP||USU_FILIAL||USU_NUMOCP||USU_ID = $id");
		
		if($query->num_rows() > 0) {
			return $query->result();
		} else {
			return false;
		}
	}
	
	function getDadosOC($id) {
		$query = $this->db->query("SELECT A.USU_NUMOCP,
									A.USU_CODEMP,
									A.USU_DTAPR,
									A.USU_FILIAL,
								A.USU_VLRADT,
								A.USU_VLRAPR,
								B.VLRORI,
								B.TNSPRO,
								B.TNSSER,
								B.CODFOR,
								C.APEFOR,
								A.USU_DTAPR,
								B.OBSOCP,
								B.CODFPG,
								B.CODMOE,
								A.USU_LANCUSU,
								C.CGCCPF,
								D.CODBAN,
								E.NOMBAN,
								D.CODAGE,
								D.CCBFOR
									FROM USU_TADTMOV a
									inner join e420ocp b
									on A.USU_CODEMP = b.codemp
									and A.USU_FILIAL = b.codfil
									and A.USU_NUMOCP = b.numocp
					                INNER join e095for c                  
					                on b.codfor = c.CODFOR
					                LEFT JOIN E095HFO D
					                ON B.CODEMP = D.CODEMP
					                AND B.CODFIL = D.CODFIL
					                AND B.CODFOR = D.CODFOR
					                LEFT JOIN E030BAN E
					                ON D.CODBAN = E.CODBAN
									where b.codemp||b.codfil||b.numocp||a.usu_id = $id");
	
		if($query->num_rows() > 0) {
			return $query->result();
		} else {
			return false;
		}
	}
	
	function getOcRateio($emp, $filial, $oc) {
		$query = $this->db->query("SELECT DISTINCT ctafin,
										  ctared,
										  codccu,
										  seqrat,
										  crirat,
										  somsub,
										  numprj,
										  codfpj,
										  perrat
										FROM e420rat
										WHERE numocp = $oc
										AND codfil   = $filial
										AND codemp   = $emp");
	
		if($query->num_rows() > 0) {
			return $query->result();
		} else {
			return false;
		}
	}
	
	function getNumRateio($emp, $filial, $oc) {
		$query = $this->db->query("SELECT distinct ctafin
				FROM e420rat
				WHERE numocp = $oc
				AND codfil   = $filial
				AND codemp   = $emp");
	
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
	
}