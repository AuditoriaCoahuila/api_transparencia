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
		$sql = "SELECT e.id, t.nombre as tipo_entidad, e.nombre, e.descripcion, e.nombre_completo  ";
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
		
		$sql = "SELECT count(id) as total FROM criterio_evaluacion_catalogo ";
		$sql .= " WHERE vigente = True ";
		$res = $bd_obj->ejecutarConsulta($sql);
		$fila = pg_fetch_assoc($res);
		$total = $fila["total"];
		
		$sql = "SELECT id_entidad, sum(porcentaje_cumplimiento) as suma FROM criterio_evaluacion_entidades ";
		$sql .= " WHERE id_entidad IN ( " . $this->get_cadena_ids($ids_entidad) . ")";
		$sql .= " GROUP BY id_entidad ";
		$res = $bd_obj->ejecutarConsulta($sql);
		
		$resultados = array();
		while ($fila = pg_fetch_assoc($res)){
			$resultados[$fila["id_entidad"]] =  array(
				"porcentaje_cumplimiento" => ( ( $fila["suma"] * 1.0 ) / $total ),
				"datos_entidad" => $datos_entidades[$fila["id_entidad"]]
				);
		}
		return $resultados;
	}
	
	public function procesa_documentos_entidad($ids_entidad, $bd_obj){
		$datos_entidades = $this->get_datos_entidades( $ids_entidad , $bd_obj);
		$sql = "SELECT d.id as id_documento, d.nombre_documento, d.url, d.validado, ";
		$sql .= " cev.concepto as criterio, cee.id_entidad ";
		$sql .= " FROM criterio_evaluacion_entidades_documento d, criterio_evaluacion_entidades cee, ";
		$sql .= " criterio_evaluacion_catalogo cev";
		$sql .= " WHERE d.id_criterio_evaluacion = cee.id AND cee.id_criterio_evaluacion = cev.id AND ";
		$sql .= " cee.id_entidad IN ( " . $this->get_cadena_ids( $ids_entidad ) . ")";
		$res = $bd_obj->ejecutarConsulta($sql);
		$resultados = array();
		while ($fila = pg_fetch_assoc($res)){
			if ( ! isset( $resultados[$fila["id_entidad"]] ) ){
				$resultados[$fila["id_entidad"]] = array( "datos_entidad" => $datos_entidades[$fila["id_entidad"]], "documentos" => array() );
			}
			$resultados[$fila["id_entidad"]]["documentos"][$fila["id_documento"]] =  array(
															"nombre_documento" => $fila["nombre_documento"],
															"url" => $fila["url"],
															"validado" => $fila["validado"],
				);
		}
		return $resultados;
	}
	
	public function procesa_cumplimiento_entidad($sql_where, $bd_obj){
		$sql = "SELECT  ec.id as id_entidad, etc.nombre as tipo_entidad, ec.nombre, ec.descripcion, ec.nombre_completo, ";
		$sql .= " cee.id_criterio_evaluacion, cee.porcentaje_cumplimiento, cee.texto as respuesta_criterio, ";
		$sql .= " cec.concepto ";
		$sql .= " FROM criterio_evaluacion_entidades cee, criterio_evaluacion_catalogo cec, entidades_catalogo ec, entidades_tipo_catalogo etc";
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
																				"descripcion" => $fila["descripcion"],
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
		
		$sql = "SELECT count(id) as total FROM criterio_evaluacion_catalogo ";
		$sql .= " WHERE vigente = True ";
		$res = $bd_obj->ejecutarConsulta($sql);
		$fila = pg_fetch_assoc($res);
		$total = $fila["total"];
		
		$res = $bd_obj->ejecutarConsulta($sql_best);
		while ($fila = pg_fetch_assoc($res)){
			$resultados[$fila["id_entidad"]] =  array( "porcentaje_cumplimiento" => ( ( $fila["suma"] * 1.0 ) / $total ) );
			$entidades_mejores[] = $fila["id_entidad"];
		}
		
		$sql = "SELECT  ec.id as id_entidad, etc.nombre as tipo_entidad, ec.nombre, ec.descripcion, ec.nombre_completo, ";
		$sql .= " cee.id_criterio_evaluacion, cee.porcentaje_cumplimiento, cee.texto as respuesta_criterio, ";
		$sql .= " cec.concepto ";
		$sql .= " FROM criterio_evaluacion_entidades cee, criterio_evaluacion_catalogo cec, entidades_catalogo ec, entidades_tipo_catalogo etc";
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
																		"descripcion" => $fila["descripcion"],
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
		$sql = "SELECT e.id, t.nombre as tipo_entidad, e.nombre, e.descripcion, e.nombre_completo, ";
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
																	"descripcion" => $fila["descripcion"],
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
		$sql = "SELECT id, concepto, tipo_dato FROM entidades_reporte_imco_catalogo ";
		$res = $bd_obj->ejecutarConsulta($sql);
		$set_indicadores = array();
		while ($fila = pg_fetch_assoc($res))
			$set_indicadores[$fila["id"]] = $fila;
		
		//Carga los datos de las entidades relevantes
		$resultados = array();
		$sql = "SELECT e.id, t.nombre as tipo_entidad, e.nombre, e.descripcion, e.nombre_completo";
		$sql .= " FROM entidades_catalogo e, entidades_tipo_catalogo t ";
		$sql .= " WHERE t.id = e.id_tipo_entidad AND e.id IN ( " . $this->get_cadena_ids( $ids_entidad ) . " ) ";
		$sql .= " ORDER BY e.nombre";
		$resultado = $bd_obj->ejecutarConsulta($sql);
		$resultados = array();
		while ($fila = pg_fetch_assoc($resultado)){
			if( ! isset ( $resultados[$fila["id"]] ) ){
				$resultados[$fila["id"]] = array( "datos_entidad" =>$fila, "indicadores"  => $set_indicadores );
			}
		}
		
		//Carga el set de valores del reporte del imco
		$sqls = array();
		$sqls[] = "SELECT id_entidad, anio, id_concepto, valor FROM entidades_reporte_imco_valor_boolean WHERE id_entidad IN ( " . $this->get_cadena_ids( $ids_entidad ) . " )";
		$sqls[] = "SELECT id_entidad, anio, id_concepto, valor FROM entidades_reporte_imco_valor_numeric WHERE id_entidad IN ( " . $this->get_cadena_ids( $ids_entidad ) . " )";
		$sqls[] = "SELECT id_entidad, anio, id_concepto, valor FROM entidades_reporte_imco_valor_text WHERE id_entidad IN ( " . $this->get_cadena_ids( $ids_entidad ) . " )";
		$sqls[] = "SELECT id_entidad, anio, id_concepto, valor FROM entidades_reporte_imco_valor_varchar WHERE id_entidad IN ( " . $this->get_cadena_ids( $ids_entidad ) . " )";
		foreach($sqls as $sql){
			$res = $bd_obj->ejecutarConsulta($sql);
			while ($fila = pg_fetch_assoc($res)){
				$resultados[$fila["id_entidad"]]["indicadores"][$fila["id_concepto"]]["anio"] = $fila["anio"];
				$resultados[$fila["id_entidad"]]["indicadores"][$fila["id_concepto"]]["valor"] = $fila["valor"];
			}
		}
		return $resultados;
	}
	
	public function procesa_toda_info_entidades($ids_entidades, $bd_obj){
		$resultados = $this->procesa_evaluacion_entidad($ids_entidades, $bd_obj);
		
		$temp_1 = $this->procesa_documentos_entidad($ids_entidades, $bd_obj); //documentos
		$temp_2 = $this->procesa_cumplimiento_entidad(" AND ec.id IN (" . $this->get_cadena_ids( $ids_entidades ) . ") ", $bd_obj); //criterios
		$temp_3 = $this->procesa_li_pe($ids_entidades, $bd_obj); //li_pe
		$temp_4 = $this->procesa_reporte_imco($ids_entidades, $bd_obj); //indicadores
		
		foreach($ids_entidades as $id){
			$resultados[$id]["documentos"] = $temp_1[$id]["documentos"];
			$resultados[$id]["criterios"] = $temp_2[$id]["criterios"];
			$resultados[$id]["li_pe"] = $temp_3[$id]["li_pe"];
			$resultados[$id]["indicadores"] = $temp_4[$id]["indicadores"];
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
		$sql .= " FROM criterio_evaluacion_entidades cee, entidades_catalogo ec ";
		$sql .= " WHERE cee.id_entidad = ec.id AND ec.id_tipo_entidad = " . $id_tipo_entidad;
		$sql .= " GROUP BY cee.id_entidad ";
		$sql .= " ORDER BY suma DESC ";
		$sql .= " LIMIT " . $n;			
		return $this->procesa_mejor_indice_global($sql, $bd_obj);
	}
	
	public function get_n_entidades_mejor_indice_global($n, $bd_obj){
		$sql = "SELECT cee.id_entidad, sum(cee.porcentaje_cumplimiento) as suma ";
		$sql .= " FROM criterio_evaluacion_entidades cee, entidades_catalogo ec ";
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
		$sql = "SELECT e.id, t.nombre as tipo_entidad, e.nombre, e.descripcion, e.nombre_completo  ";
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
		$sql = "SELECT e.id, t.nombre as tipo_entidad, e.nombre, e.descripcion, e.nombre_completo  ";
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
		$sql .= " FROM criterio_evaluacion_catalogo ";
		$res = $bd_obj->ejecutarConsulta($sql);
		$resultados = array();
		while ($fila = pg_fetch_assoc($res))
			$resultados[$fila["id"]] = $fila;
		return $resultados;
	}
	
	public function get_catalogo_reporte_imco($bd_obj){
		$sql = "SELECT id, concepto, tipo_dato ";
		$sql .= " FROM entidades_reporte_imco_catalogo ";
		$res = $bd_obj->ejecutarConsulta($sql);
		$resultados = array();
		while ($fila = pg_fetch_assoc($res))
			$resultados[$fila["id"]] = $fila;
		return $resultados;
	}
	
	
}
?>