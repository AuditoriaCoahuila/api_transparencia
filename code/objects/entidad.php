<?php 
error_reporting(E_ALL);
ini_set( "memory_limit","512M");
ini_set( "max_execution_time","60");
ini_set('display_errors', '1');

class entidad{
	
	//Funciones generales de procesamiento ------------------------------------------------------------------------------------------------
	//----------------------------------------------------------------------------------------------------------------
	//----------------------------------------------------------------------------------------------------------------
	private function get_cadena_ids( $ids ){
		$con = 0;
		foreach ($ids as $id){
			if( $con == 0 ){
				$cad = $id;  
				$con = 1;
			}
			else
				$cad .= "," . $id;  
		}
		return $cad;
	}
	
	private function get_datos_entidades( $ids, $bd_obj ){
		$sql = "SELECT e.id, t.nombre as tipo_entidad, e.nombre, e.descripcion_corta, e.descripcion_250, e.nombre_completo  ";
		$sql .= " FROM entidades_catalogo e, entidades_tipo_catalogo t ";
		$sql .= " WHERE t.id = e.id_tipo_entidad AND e.id IN ( " . $this->get_cadena_ids($ids) . " ) ";
		$sql .= " ORDER BY e.nombre";
		$resultado = $bd_obj->ejecutarConsulta($sql);
		$resultado_final = array();
		while ($fila = pg_fetch_assoc($resultado))
			$resultado_final[$fila["id"]] = $fila;
		return $resultado_final;
	}
	
	public function procesa_evaluacion_entidad($ids_entidad, $bd_obj){
		$datos_entidades = $this->get_datos_entidades(  $ids_entidad  , $bd_obj);
		
		$sql = "SELECT id_entidad, anio, porcentaje_cumplimiento, numero_normas_cumplidas FROM entidades_iipm ";
		$sql .= " WHERE id_entidad IN ( " . $this->get_cadena_ids($ids_entidad) . ")";
		$res = $bd_obj->ejecutarConsulta($sql);
		
		$resultados = array();
		while ($fila = pg_fetch_assoc($res)){
			if( ! isset( $resultados[$fila["id_entidad"]] ) ){
				$resultados[$fila["id_entidad"]] =  array(
					"porcentaje_cumplimiento" => array(),
					"datos_entidad" => $datos_entidades[$fila["id_entidad"]]
					);
			}
			$resultados[$fila["id_entidad"]]["porcentaje_cumplimiento"][$fila["anio"]] = array(
					"anio" => $fila["anio"],
					"porcentaje_cumplimiento" => round( $fila["porcentaje_cumplimiento"] * 100, 2) ,
					"numero_normas_cumplidas" => $fila["numero_normas_cumplidas"],  
					);
		}
		return $resultados;
	}
	
	public function procesa_documentos_entidad($ids_entidad, $bd_obj){
		$datos_entidades = $this->get_datos_entidades( $ids_entidad , $bd_obj);
		$sql = "SELECT d.id as id_documento, d.nombre_archivo, d.url,   ";
		$sql .= " cev.concepto as concepto, cee.id_entidad ";
		$sql .= " FROM evaluacion_transparencia_documentos_entidades d, evaluacion_transparencia_valores cee, ";
		$sql .= " evaluacion_transparencia_catalogo_criterios cev";
		$sql .= " WHERE d.id_criterio_evaluacion = cee.id AND cee.id_criterio_evaluacion = cev.id AND ";
		$sql .= " cee.id_entidad IN ( " . $this->get_cadena_ids( $ids_entidad ) . ")";
		$res = $bd_obj->ejecutarConsulta($sql);
		$resultados = array();
		while ($fila = pg_fetch_assoc($res)){
			if ( ! isset( $resultados[$fila["id_entidad"]] ) ){
				$resultados[$fila["id_entidad"]] = array( "datos_entidad" => $datos_entidades[$fila["id_entidad"]], 
						"documentos" => array( "reporte_imco" => array(), "generales" => array() ) );
			}
			$resultados[$fila["id_entidad"]]["documentos"]["reporte_imco"][$fila["id_documento"]] =  array(
															"nombre_archivo" => $fila["nombre_archivo"],
															"url" => $fila["url"],
															"concepto" => $fila["concepto"],
															//"anio" => $fila["anio"],
				);
		}
		
		$sql = "SELECT d.id as id_documento, d.nombre_archivo, d.url, d.id_entidad, d.anio, c.concepto  ";
		$sql .= " FROM documentos_generales_valores d, documentos_generales_catalogo_conceptos c ";
		$sql .= " WHERE d.id_tipo_documento = c.id AND ";
		$sql .= " d.id_entidad IN ( " . $this->get_cadena_ids( $ids_entidad ) . ")";
		$res = $bd_obj->ejecutarConsulta($sql);
		while ($fila = pg_fetch_assoc($res)){
			if ( ! isset( $resultados[$fila["id_entidad"]] ) ){
				$resultados[$fila["id_entidad"]] = array( "datos_entidad" => $datos_entidades[$fila["id_entidad"]], 
						"documentos" => array( "reporte_imco" => array(), "generales" => array() ) );
			}
			if ( ! isset( $resultados[$fila["id_entidad"]]["generales"][$fila["concepto"]] ) ){
				$resultados[$fila["id_entidad"]]["generales"][$fila["concepto"]] = array();
			}
			$resultados[$fila["id_entidad"]]["documentos"]["generales"][$fila["concepto"]][$fila["id_documento"]] =  array(
															"nombre_archivo" => $fila["nombre_archivo"],
															"url" => $fila["url"],
															"anio" => $fila["anio"],
				);
		}
		
		return $resultados;
	}
	
	public function procesa_cumplimiento_entidad($sql_where, $bd_obj){
		$sql = "SELECT  ec.id as id_entidad, etc.nombre as tipo_entidad, ec.nombre, ec.descripcion_250, ec.nombre_completo, ";
		$sql .= " cee.id_criterio_evaluacion, cee.porcentaje_cumplimiento, cee.texto as respuesta_criterio, ";
		$sql .= " cec.concepto ";
		$sql .= " FROM evaluacion_transparencia_valores cee, evaluacion_transparencia_catalogo_criterios cec, entidades_catalogo ec, entidades_tipo_catalogo etc";
		$sql .= " WHERE cee.id_entidad = ec.id AND cee.id_criterio_evaluacion = cec.id AND ec.id_tipo_entidad = etc.id ";
		$sql.= $sql_where;
		//var_dump($sql);
		$res = $bd_obj->ejecutarConsulta($sql);
		$resultados = array();
		while ($fila = pg_fetch_assoc($res)){
			if ( ! isset( $resultados[$fila["id_entidad"]] ) ){
				$resultados[$fila["id_entidad"]] = array( "datos_entidad" => array(
																				"id" => $fila["id_entidad"],
																				"tipo_entidad" => $fila["tipo_entidad"],
																				"nombre" => $fila["nombre"],
																				"nombre_completo" => $fila["nombre_completo"],
																				"descripcion" => $fila["descripcion_250"],
																			), 
															"criterios" => array() );
			}
			$resultados[$fila["id_entidad"]]["criterios"][$fila["id_criterio_evaluacion"]] =  array(
															"porcentaje_cumplimiento" => $fila["porcentaje_cumplimiento"],
															"concepto" => $fila["concepto"],
															"respuesta_criterio" => $fila["respuesta_criterio"],
				);
		}
		return $resultados;
	}
	
	public function procesa_mejor_indice_global ($sql_best, $bd_obj){
		$resultados = array();
		$entidades_mejores = array();
		
		$sql = "SELECT count(id) as total FROM evaluacion_transparencia_catalogo_criterios ";
		$sql .= " WHERE vigente = True ";
		$res = $bd_obj->ejecutarConsulta($sql);
		$fila = pg_fetch_assoc($res);
		$total = $fila["total"];
		
		$res = $bd_obj->ejecutarConsulta($sql_best);
		while ($fila = pg_fetch_assoc($res)){
			$resultados[$fila["id_entidad"]] =  array( "porcentaje_cumplimiento" => ( ( $fila["suma"] * 1.0 ) / $total ) );
			$entidades_mejores[] = $fila["id_entidad"];
		}
		
		$sql = "SELECT  ec.id as id_entidad, etc.nombre as tipo_entidad, ec.nombre, ec.descripcion_250, ec.nombre_completo, ";
		$sql .= " cee.id_criterio_evaluacion, cee.porcentaje_cumplimiento, cee.texto as respuesta_criterio, ";
		$sql .= " cec.concepto ";
		$sql .= " FROM evaluacion_transparencia_valores cee, evaluacion_transparencia_catalogo_criterios cec,";
		$sql .= " entidades_catalogo ec, entidades_tipo_catalogo etc";
		$sql .= " WHERE cee.id_entidad = ec.id AND cee.id_criterio_evaluacion = cec.id AND ec.id_tipo_entidad = etc.id AND ec.id IN (" . $this->get_cadena_ids($entidades_mejores) . ")";
		//var_dump($sql);
		$res = $bd_obj->ejecutarConsulta($sql);
		while ($fila = pg_fetch_assoc($res)){
			if ( ! isset( $resultados[$fila["id_entidad"]]["datos_entidad"] ) ){
				$resultados[$fila["id_entidad"]]["datos_entidad"] = array(
																		"id" => $fila["id_entidad"],
																		"tipo_entidad" => $fila["tipo_entidad"],
																		"nombre" => $fila["nombre"],
																		"nombre_completo" => $fila["nombre_completo"],
																		"descripcion" => $fila["descripcion_250"],
																	);
			}
			if ( ! isset( $resultados[$fila["id_entidad"]]["criterios"] ) )
				$resultados[$fila["id_entidad"]]["criterios"] = array();
			
			$resultados[$fila["id_entidad"]]["criterios"][$fila["id_criterio_evaluacion"]] =  array(
															"porcentaje_cumplimiento" => $fila["porcentaje_cumplimiento"],
															"concepto" => $fila["concepto"],
															"respuesta_criterio" => $fila["respuesta_criterio"],
				);
		}
		return $resultados;
	}
	
	public function procesa_li_pe($ids_entidad, $bd_obj){
		$resultados = array();
		$sql = "SELECT e.id, t.nombre as tipo_entidad, e.nombre, e.descripcion_250, e.nombre_completo, ";
		$sql .= " li.anio, li.pe_oficial, li.li_oficial, li.pe_auditoria, li.li_auditoria, li.li_diferencia, li.pe_diferencia  ";
		$sql .= " FROM entidades_catalogo e, entidades_tipo_catalogo t, entidades_li_pe li ";
		$sql .= " WHERE t.id = e.id_tipo_entidad AND e.id = li.id_entidad ";
		$sql .= " AND e.id IN ( " . $this->get_cadena_ids( $ids_entidad ) . " ) ";
		$sql .= " ORDER BY e.nombre";
		//var_dump($sql);
		$resultado = $bd_obj->ejecutarConsulta($sql);
		$resultados = array();
		while ($fila = pg_fetch_assoc($resultado)){
			$resultados[$fila["id"]] = array( "datos_entidad" => array(
																	"id" => $fila["id"],
																	"tipo_entidad" => $fila["tipo_entidad"],
																	"nombre" => $fila["nombre"],
																	"nombre_completo" => $fila["nombre_completo"],
																	"descripcion" => $fila["descripcion_250"],
																	),
												"li_pe" => array(
																"pe_oficial" => $fila["pe_oficial"],
																"li_oficial" => $fila["li_oficial"],
																"pe_auditoria" => $fila["pe_auditoria"],
																"li_auditoria" => $fila["li_auditoria"],
																"pe_diferencia" => $fila["pe_diferencia"],
																"li_diferencia" => $fila["li_diferencia"],
															)
											);
			
		}
			$resultados[$fila["id"]] = $fila;
		return $resultados;
	}
	
	public function procesa_reporte_imco($ids_entidad, $bd_obj){
		//Carga el set de conceptos del reporte del imco
		$sql = "SELECT id, concepto, tipo_dato FROM reporte_imco_catalogo_conceptos ";
		$res = $bd_obj->ejecutarConsulta($sql);
		$set_indicadores = array();
		while ($fila = pg_fetch_assoc($res))
			$set_indicadores[$fila["id"]] = $fila;
		
		//Carga los datos de las entidades relevantes
		$resultados = array();
		$sql = "SELECT e.id, t.nombre as tipo_entidad, e.nombre, e.descripcion_250, e.nombre_completo";
		$sql .= " FROM entidades_catalogo e, entidades_tipo_catalogo t ";
		$sql .= " WHERE t.id = e.id_tipo_entidad AND e.id IN ( " . $this->get_cadena_ids( $ids_entidad ) . " ) ";
		$sql .= " ORDER BY e.nombre";
		$resultado = $bd_obj->ejecutarConsulta($sql);
		$resultados = array();
		while ($fila = pg_fetch_assoc($resultado)){
			if( ! isset ( $resultados[$fila["id"]] ) ){
				$resultados[$fila["id"]] = array( "datos_entidad" => array(
																	"id" => $fila["id"],
																	"tipo_entidad" => $fila["tipo_entidad"],
																	"nombre" => $fila["nombre"],
																	"nombre_completo" => $fila["nombre_completo"],
																	"descripcion" => $fila["descripcion_250"],
																	), 
												"indicadores"  => $set_indicadores );
			}
		}
		
		//Carga el set de valores del reporte del imco
		$sqls = array();
		$sqls[] = "SELECT id_entidad, anio, id_concepto, valor FROM reporte_imco_valor_boolean WHERE id_entidad IN ( " . $this->get_cadena_ids( $ids_entidad ) . " )";
		$sqls[] = "SELECT id_entidad, anio, id_concepto, valor FROM reporte_imco_valor_numeric WHERE id_entidad IN ( " . $this->get_cadena_ids( $ids_entidad ) . " )";
		$sqls[] = "SELECT id_entidad, anio, id_concepto, valor FROM reporte_imco_valor_text WHERE id_entidad IN ( " . $this->get_cadena_ids( $ids_entidad ) . " )";
		$sqls[] = "SELECT id_entidad, anio, id_concepto, valor FROM reporte_imco_valor_varchar WHERE id_entidad IN ( " . $this->get_cadena_ids( $ids_entidad ) . " )";
		foreach($sqls as $sql){
			$res = $bd_obj->ejecutarConsulta($sql);
			while ($fila = pg_fetch_assoc($res)){
				$resultados[$fila["id_entidad"]]["indicadores"][$fila["id_concepto"]]["anio"] = $fila["anio"];
				$resultados[$fila["id_entidad"]]["indicadores"][$fila["id_concepto"]]["valor"] = $fila["valor"];
			}
		}
		return $resultados;
	}
	
	public function procesa_cuentas_publicas( $ids_entidad, $bd_obj){
		$sql = "SELECT e.id, t.nombre as tipo_entidad, e.nombre, e.descripcion_250, e.nombre_completo, ";
		$sql .= " cp.anio, cp.valor, cpc.tipo_informacion, cpc.concepto";
		$sql .= " FROM entidades_catalogo e, entidades_tipo_catalogo t, ";
		$sql .= "cuentas_publicas_valores cp, cuentas_publicas_catalogo_conceptos cpc ";
		$sql .= " WHERE t.id = e.id_tipo_entidad AND e.id = cp.id_entidad AND cp.id_concepto = cpc.id";
		$sql .= " AND e.id IN ( " . $this->get_cadena_ids( $ids_entidad ) . " ) ";
		$sql .= " ORDER BY e.nombre"; 
		$resultado = $bd_obj->ejecutarConsulta($sql);
		$resultados = array();
		while ($fila = pg_fetch_assoc($resultado)){
			if (! isset($resultados[$fila["id"]]) ){
				$resultados[$fila["id"]] = array( "datos_entidad" => array(
																	"id" => $fila["id"],
																	"tipo_entidad" => $fila["tipo_entidad"],
																	"nombre" => $fila["nombre"],
																	"nombre_completo" => $fila["nombre_completo"],
																	"descripcion" => $fila["descripcion_250"],
																	),
												"cuentas_publicas" => array()
										);
			}
			if (! isset($resultados[$fila["id"]]["cuentas_publicas"][$fila["anio"]]) )
				$resultados[$fila["id"]]["cuentas_publicas"][$fila["anio"]] = array();
			if (! isset($resultados[$fila["id"]]["cuentas_publicas"][$fila["anio"]][$fila["tipo_informacion"]]) )
				$resultados[$fila["id"]]["cuentas_publicas"][$fila["anio"]][$fila["tipo_informacion"]] = array();
			
			$resultados[$fila["id"]]["cuentas_publicas"][$fila["anio"]][$fila["tipo_informacion"]][$fila["concepto"]] = array(
												"concepto" => $fila["concepto"],
												"valor" => $fila["valor"]);
		}
		return $resultados;
	}
	
	public function procesa_informacion_presupuestal( $ids_entidad, $bd_obj){
		$sql = "SELECT e.id, t.nombre as tipo_entidad, e.nombre, e.descripcion_250, e.nombre_completo, ";
		$sql .= " ip.anio, ip.valor, ipc.tipo_informacion, ipc.concepto";
		$sql .= " FROM entidades_catalogo e, entidades_tipo_catalogo t, ";
		$sql .= "informacion_presupuestal_valores ip, informacion_presupuestal_catalogo_tipo ipc ";
		$sql .= " WHERE t.id = e.id_tipo_entidad AND e.id = ip.id_entidad AND ip.id_concepto = ipc.id";
		$sql .= " AND e.id IN ( " . $this->get_cadena_ids( $ids_entidad ) . " ) ";
		$sql .= " ORDER BY e.nombre"; 
		$resultado = $bd_obj->ejecutarConsulta($sql);
		$resultados = array();
		while ($fila = pg_fetch_assoc($resultado)){
			if (! isset($resultados[$fila["id"]]) ){
				$resultados[$fila["id"]] = array( "datos_entidad" => array(
																	"id" => $fila["id"],
																	"tipo_entidad" => $fila["tipo_entidad"],
																	"nombre" => $fila["nombre"],
																	"nombre_completo" => $fila["nombre_completo"],
																	"descripcion" => $fila["descripcion_250"],
																	),
												"informacion_presupuestal" => array()
										);
			}
			if (! isset($resultados[$fila["id"]]["informacion_presupuestal"][$fila["anio"]]) )
				$resultados[$fila["id"]]["informacion_presupuestal"][$fila["anio"]] = array();
			if (! isset($resultados[$fila["id"]]["informacion_presupuestal"][$fila["anio"]][$fila["tipo_informacion"]]) )
				$resultados[$fila["id"]]["informacion_presupuestal"][$fila["anio"]][$fila["tipo_informacion"]] = array();
			
			$resultados[$fila["id"]]["informacion_presupuestal"][$fila["anio"]][$fila["tipo_informacion"]][$fila["concepto"]] = array(
												"concepto" => $fila["concepto"],
												"valor" => $fila["valor"]);
		}
		return $resultados;
	}
	
	public function procesa_valuacion_actuarial( $ids_entidad, $bd_obj){
		$sql = "SELECT e.id, t.nombre as tipo_entidad, e.nombre, e.descripcion_250, e.nombre_completo, ";
		$sql .= " va.anio, va.valor_hombre, va.valor_mujer, va.valor_total,";
		$sql .= " vac.tipo_informacion, vac.concepto";
		$sql .= " FROM entidades_catalogo e, entidades_tipo_catalogo t, ";
		$sql .= "valuacion_actuarial_valores va, valuacion_actuarial_catalogo_conceptos vac ";
		$sql .= " WHERE t.id = e.id_tipo_entidad AND e.id = va.id_entidad AND va.id_concepto = vac.id";
		$sql .= " AND e.id IN ( " . $this->get_cadena_ids( $ids_entidad ) . " ) ";
		$sql .= " ORDER BY e.nombre"; 
		$resultado = $bd_obj->ejecutarConsulta($sql);
		$resultados = array();
		while ($fila = pg_fetch_assoc($resultado)){
			if (! isset($resultados[$fila["id"]]) ){
				$resultados[$fila["id"]] = array( "datos_entidad" => array(
																	"id" => $fila["id"],
																	"tipo_entidad" => $fila["tipo_entidad"],
																	"nombre" => $fila["nombre"],
																	"nombre_completo" => $fila["nombre_completo"],
																	"descripcion" => $fila["descripcion_250"],
																	),
												"valuacion_actuarial" => array()
										);
			}
			if (! isset($resultados[$fila["id"]]["valuacion_actuarial"][$fila["anio"]]) )
				$resultados[$fila["id"]]["valuacion_actuarial"][$fila["anio"]] = array();
			if (! isset($resultados[$fila["id"]]["valuacion_actuarial"][$fila["anio"]][$fila["tipo_informacion"]]) )
				$resultados[$fila["id"]]["valuacion_actuarial"][$fila["anio"]][$fila["tipo_informacion"]] = array();
			
			$resultados[$fila["id"]]["valuacion_actuarial"][$fila["anio"]][$fila["tipo_informacion"]][$fila["concepto"]] = array(
												"concepto" => $fila["concepto"],
												"valor_hombre" => $fila["valor_hombre"],
												"valor_mujer" => $fila["valor_mujer"],
												"valor_total" => $fila["valor_total"]);
		}
		return $resultados;
	}
	
	public function procesa_toda_info_entidades($ids_entidades, $bd_obj){
		$resultados = $this->procesa_evaluacion_entidad($ids_entidades, $bd_obj);
		
		$temp_1 = $this->procesa_documentos_entidad($ids_entidades, $bd_obj); //documentos
		$temp_2 = $this->procesa_cumplimiento_entidad(" AND ec.id IN (" . $this->get_cadena_ids( $ids_entidades ) . ") ", $bd_obj); //criterios
		$temp_3 = $this->procesa_li_pe($ids_entidades, $bd_obj); //li_pe
		$temp_4 = $this->procesa_reporte_imco($ids_entidades, $bd_obj); //indicadores
		$temp_5 = $this->procesa_cuentas_publicas($ids_entidades, $bd_obj); //Cuentas publicas
		$temp_6 = $this->procesa_informacion_presupuestal($ids_entidades, $bd_obj); //Informacion presupuetal
		$temp_7 = $this->procesa_valuacion_actuarial($ids_entidades, $bd_obj); //Valuacion Actuarial
		
		foreach($ids_entidades as $id){
			$resultados[$id]["documentos"] = $temp_1[$id]["documentos"];
			$resultados[$id]["criterios"] = $temp_2[$id]["criterios"];
			$resultados[$id]["li_pe"] = $temp_3[$id]["li_pe"];
			$resultados[$id]["indicadores"] = $temp_4[$id]["indicadores"];
			$resultados[$id]["cuentas_publicas"] = $temp_5[$id]["cuentas_publicas"];
			$resultados[$id]["informacion_presupuestal"] = $temp_6[$id]["informacion_presupuestal"];
			$resultados[$id]["valuacion_actuarial"] = $temp_7[$id]["valuacion_actuarial"];
		}
		
		return $resultados;
	}
	
	//----------------------------------------------------------------------------------------------------------------
	//----------------------------------------------------------------------------------------------------------------
	//----------------------------------------------------------------------------------------------------------------
	
	public function get_evaluacion_entidad($id_entidad, $bd_obj){
		return $this->procesa_evaluacion_entidad( array($id_entidad) , $bd_obj);
	}
	
	public function get_evaluacion_n_entidades($ids_entidad, $bd_obj){
		return $this->procesa_evaluacion_entidad($ids_entidad, $bd_obj);
	}
	
	public function get_documentos_entidad($id_entidad, $bd_obj){
		return $this->procesa_documentos_entidad( array ($id_entidad), $bd_obj);
	}
	
	public function get_documentos_n_entidades($ids_entidad, $bd_obj){
		return $this->procesa_documentos_entidad($ids_entidad, $bd_obj);
	}
	
	public function get_cumplimiento_x_tipo_entidad_un_criterio($id_tipo_entidad, $id_criterio, $bd_obj){
		$sql = " AND etc.id IN (" . $id_tipo_entidad . ") AND cec.id IN (" . $id_criterio . ")";
		return $this->procesa_cumplimiento_entidad($sql, $bd_obj);
	}
	
	public function get_cumplimiento_todas_entidades_un_criterio($id_criterio, $bd_obj){
		$sql = " AND cec.id IN (" . $id_criterio . ")";
		return $this->procesa_cumplimiento_entidad($sql, $bd_obj);
	}
	
	public function get_cumplimiento_x_tipo_entidad_todos_criterios($id_tipo_entidad, $bd_obj){
		$sql = " AND etc.id IN (" . $id_tipo_entidad . ")";
		return $this->procesa_cumplimiento_entidad($sql, $bd_obj);
	}
	
	public function get_cumplimiento_todas_entidades_todos_criterios($bd_obj){
		return $this->procesa_cumplimiento_entidad( "", $bd_obj);
	}
	
	public function get_n_entidades_mejor_indice_global_x_tipo($n, $id_tipo_entidad, $bd_obj){
		$sql = "SELECT cee.id_entidad, sum(cee.porcentaje_cumplimiento) as suma ";
		$sql .= " FROM evaluacion_transparencia_valores cee, entidades_catalogo ec ";
		$sql .= " WHERE cee.id_entidad = ec.id AND ec.id_tipo_entidad = " . $id_tipo_entidad;
		$sql .= " GROUP BY cee.id_entidad ";
		$sql .= " ORDER BY suma DESC ";
		$sql .= " LIMIT " . $n;			
		return $this->procesa_mejor_indice_global($sql, $bd_obj);
	}
	
	public function get_n_entidades_mejor_indice_global($n, $bd_obj){
		$sql = "SELECT cee.id_entidad, sum(cee.porcentaje_cumplimiento) as suma ";
		$sql .= " FROM evaluacion_transparencia_valores cee, entidades_catalogo ec ";
		$sql .= " WHERE cee.id_entidad = ec.id ";
		$sql .= " GROUP BY cee.id_entidad ";
		$sql .= " ORDER BY suma DESC ";
		$sql .= " LIMIT " . $n;			
		return $this->procesa_mejor_indice_global($sql, $bd_obj);
	}
	
	public function get_entidad_reporte_imco($id_entidad, $bd_obj){
		return $this->procesa_reporte_imco( array ($id_entidad), $bd_obj);
	}
	
	public function get_n_entidades_reporte_imco($ids_entidad, $bd_obj){
		return $this->procesa_reporte_imco( $ids_entidad, $bd_obj);
	}
	
	public function get_entidad_pe_li($id_entidad, $bd_obj){
		return $this->procesa_li_pe( array ($id_entidad), $bd_obj);
	}
	
	public function get_n_entidades_pe_li($ids_entidad, $bd_obj){
		return $this->procesa_li_pe( $ids_entidad, $bd_obj);
	}
	
	public function get_entidad_cuentas_publicas($id_entidad, $bd_obj){
		return $this->procesa_cuentas_publicas( array ($id_entidad), $bd_obj);
	}
	
	public function get_n_entidades_cuentas_publicas($ids_entidad, $bd_obj){
		return $this->procesa_cuentas_publicas( $ids_entidad, $bd_obj);
	}
	
	public function get_entidad_informacion_presupuestal($id_entidad, $bd_obj){
		return $this->procesa_informacion_presupuestal( array ($id_entidad), $bd_obj);
	}
	
	public function get_n_entidades_informacion_presupuestal($ids_entidad, $bd_obj){
		return $this->procesa_informacion_presupuestal( $ids_entidad, $bd_obj);
	}
	
	public function get_entidad_valuacion_actuarial($id_entidad, $bd_obj){
		return $this->procesa_valuacion_actuarial( array ($id_entidad), $bd_obj);
	}
	
	public function get_n_entidades_valuacion_actuarial($ids_entidad, $bd_obj){
		return $this->procesa_valuacion_actuarial( $ids_entidad, $bd_obj);
	}
	
	public function get_entidad_todos_datos($id_entidad, $bd_obj){
		return $this->procesa_toda_info_entidades( array ($id_entidad), $bd_obj);
	}
	
	public function get_n_entidades_todos_datos($ids_entidad, $bd_obj){
		return $this->procesa_toda_info_entidades( $ids_entidad, $bd_obj);
	}
	
	//----------------------------------------------------------------------------------------------------------------
	//----------------------------------------------------------------------------------------------------------------
	//----------------------------------------------------------------------------------------------------------------
	
	public function get_catalogo_tipo_entidades($bd_obj){
		$sql = "SELECT id, nombre, descripcion ";
		$sql .= " FROM entidades_tipo_catalogo ";
		$res = $bd_obj->ejecutarConsulta($sql);
		$resultados = array();
		while ($fila = pg_fetch_assoc($res))
			$resultados[$fila["id"]] = $fila;
		return $resultados;
	}
	
	public function get_catalogo_entidades_x_tipo_entidad($id_tipo_entidad, $bd_obj){
		$sql = "SELECT e.id, t.nombre as tipo_entidad, e.nombre, e.descripcion_250, e.nombre_completo  ";
		$sql .= " FROM entidades_catalogo e, entidades_tipo_catalogo t ";
		$sql .= " WHERE t.id = e.id_tipo_entidad AND e.id_tipo_entidad IN ( " . $id_tipo_entidad . " ) ";
		$sql .= " ORDER BY e.nombre";
		$resultado = $bd_obj->ejecutarConsulta($sql);
		$resultado_final = array();
		while ($fila = pg_fetch_assoc($resultado))
			$resultado_final[$fila["id"]] = $fila;
		return $resultado_final;
	}
	
	public function get_catalogo_entidades_todos_tipos($bd_obj){
		$sql = "SELECT e.id, t.nombre as tipo_entidad, e.nombre, e.descripcion_250, e.nombre_completo  ";
		$sql .= " FROM entidades_catalogo e, entidades_tipo_catalogo t ";
		$sql .= " ORDER BY e.nombre";
		$resultado = $bd_obj->ejecutarConsulta($sql);
		$resultado_final = array();
		while ($fila = pg_fetch_assoc($resultado))
			$resultado_final[$fila["id"]] = $fila;
		return $resultado_final;
	}
	
	public function get_catalogo_criterios_evaluacion($bd_obj){
		$sql = "SELECT id, concepto, explicacion, vigente ";
		$sql .= " FROM evaluacion_transparencia_catalogo_criterios ";
		$res = $bd_obj->ejecutarConsulta($sql);
		$resultados = array();
		while ($fila = pg_fetch_assoc($res))
			$resultados[$fila["id"]] = $fila;
		return $resultados;
	}
	
	public function get_catalogo_reporte_imco($bd_obj){
		$sql = "SELECT id, concepto, tipo_dato ";
		$sql .= " FROM reporte_imco_catalogo_conceptos ";
		$res = $bd_obj->ejecutarConsulta($sql);
		$resultados = array();
		while ($fila = pg_fetch_assoc($res))
			$resultados[$fila["id"]] = $fila;
		return $resultados;
	}
	
	public function get_catalogo_cuentas_publicas($bd_obj){
		$sql = "SELECT id, tipo_informacion, concepto, comentario ";
		$sql .= " FROM cuentas_publicas_catalogo_conceptos ";
		$res = $bd_obj->ejecutarConsulta($sql);
		$resultados = array();
		while ($fila = pg_fetch_assoc($res))
			$resultados[$fila["id"]] = $fila;
		return $resultados;
	}
	
	public function get_catalogo_documentos_generales($bd_obj){
		$sql = "SELECT id, concepto, comentario ";
		$sql .= " FROM documentos_generales_catalogo_conceptos ";
		$res = $bd_obj->ejecutarConsulta($sql);
		$resultados = array();
		while ($fila = pg_fetch_assoc($res))
			$resultados[$fila["id"]] = $fila;
		return $resultados;
	}
	
	public function get_catalogo_informacion_presupuestal($bd_obj){
		$sql = "SELECT id, tipo_informacion, concepto, comentario ";
		$sql .= " FROM informacion_presupuestal_catalogo_tipo ";
		$res = $bd_obj->ejecutarConsulta($sql);
		$resultados = array();
		while ($fila = pg_fetch_assoc($res))
			$resultados[$fila["id"]] = $fila;
		return $resultados;
	}
	
	public function get_catalogo_valuacion_actuarial($bd_obj){
		$sql = "SELECT id, tipo_informacion, concepto, comentario ";
		$sql .= " FROM valuacion_actuarial_catalogo_conceptos ";
		$res = $bd_obj->ejecutarConsulta($sql);
		$resultados = array();
		while ($fila = pg_fetch_assoc($res))
			$resultados[$fila["id"]] = $fila;
		return $resultados;
	}
	
}
?>