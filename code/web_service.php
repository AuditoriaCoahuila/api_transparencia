<?php

class web_services{
	private $bd_obj;
	private $funciones_busqueda;
	private $resultado;
		
	function __construct($accion, $datos){
		include_once ("./bd/gestorBD.php");
		$this->bd_obj = new gestorBD();
		
		//Detalle de cumplimiento de informacion de una entidad -------------------------------------------
		if($accion == "get_evaluacion_entidad")
			$this->resultado = $this->get_evaluacion_entidad($datos, $this->bd_obj);
		//Detalle de cumplimiento de información de n entidades -------------------------------------------
		elseif($accion == "get_evaluacion_n_entidades")
			$this->resultado = $this->get_evaluacion_n_entidades($datos, $this->bd_obj);
		//Obtencion de relacion de documentos de una entidad -------------------------------------------
		elseif($accion == "get_documentos_entidad")
			$this->resultado = $this->get_documentos_entidad($datos, $this->bd_obj);
		//Obtencion de relacion de documentos de n entidades -------------------------------------------
		elseif($accion == "get_documentos_n_entidades")
			$this->resultado = $this->get_documentos_n_entidades($datos, $this->bd_obj);
		//Cumplimiento de un requerimiento de todas las entidades de un tipo -------------------------------------------
		elseif($accion == "get_cumplimiento_x_tipo_entidad_un_criterio")
			$this->resultado = $this->get_cumplimiento_x_tipo_entidad_un_criterio($datos, $this->bd_obj);
		//Cumplimiento de un requerimiento de todas las entidades -------------------------------------------
		elseif($accion == "get_cumplimiento_todas_entidades_un_criterio")
			$this->resultado = $this->get_cumplimiento_todas_entidades_un_criterio($datos, $this->bd_obj);
		//Cumplimiento de n requerimientos de todas las entidades de un tipo -------------------------------------------
		elseif($accion == "get_cumplimiento_x_tipo_entidad_todos_criterios")
			$this->resultado = $this->get_cumplimiento_x_tipo_entidad_todos_criterios($datos, $this->bd_obj);
		//Cumplimiento de n requerimientos de todas las entidades -------------------------------------------
		elseif($accion == "get_cumplimiento_todas_entidades_todos_criterios")
			$this->resultado = $this->get_cumplimiento_todas_entidades_todos_criterios($datos, $this->bd_obj);
		//n entidades de un tipo con el mayor indice de cumplimiento -------------------------------------------
		elseif($accion == "get_n_entidades_mejor_indice_global_x_tipo")
			$this->resultado = $this->get_n_entidades_mejor_indice_global_x_tipo($datos, $this->bd_obj);
		//n entidades con el mayorindice de cumplimiento -------------------------------------------
		elseif($accion == "get_n_entidades_mejor_indice_global")
			$this->resultado = $this->get_n_entidades_mejor_indice_global($datos, $this->bd_obj);
		// -------------------------------------------
		elseif($accion == "get_entidad_reporte_imco")
			$this->resultado = $this->get_entidad_reporte_imco($datos, $this->bd_obj);
		// -------------------------------------------
		elseif($accion == "get_n_entidades_reporte_imco")
			$this->resultado = $this->get_n_entidades_reporte_imco($datos, $this->bd_obj);
		// -------------------------------------------
		elseif($accion == "get_entidad_pe_li")
			$this->resultado = $this->get_entidad_pe_li($datos, $this->bd_obj);
		// -------------------------------------------
		elseif($accion == "get_n_entidades_pe_li")
			$this->resultado = $this->get_n_entidades_pe_li($datos, $this->bd_obj);
		// -------------------------------------------------------------------------------------------------
		// -------------------------------------------------------------------------------------------------
		elseif($accion == "get_entidad_todos_datos")
			$this->resultado = $this->get_entidad_todos_datos($datos, $this->bd_obj);
		elseif($accion == "get_n_entidades_todos_datos")
			$this->resultado = $this->get_n_entidades_todos_datos($datos, $this->bd_obj);
		// -------------------------------------------------------------------------------------------------
		// -------------------------------------------------------------------------------------------------
		// Obtiene el catalogo de tipo de entidades -------------------------------------------
		elseif($accion == "get_catalogo_tipo_entidades")
			$this->resultado = $this->get_catalogo_tipo_entidades($datos, $this->bd_obj);
		// Obtiene el catalogo de un tipo de entidades -------------------------------------------
		elseif($accion == "get_catalogo_entidades_x_tipo_entidad")
			$this->resultado = $this->get_catalogo_entidades_x_tipo_entidad($datos, $this->bd_obj);
		//Obtiene el catalogo de todas las entidades -------------------------------------------
		elseif($accion == "get_catalogo_entidades_todos_tipos")
			$this->resultado = $this->get_catalogo_entidades_todos_tipos($datos, $this->bd_obj);
		//Obtiene el catalogo de criterios de evaluacion -------------------------------------------
		elseif($accion == "get_catalogo_criterios_evaluacion")
			$this->resultado = $this->get_catalogo_criterios_evaluacion($datos, $this->bd_obj);
		elseif($accion == "get_catalogo_reporte_imco")
			$this->resultado = $this->get_catalogo_reporte_imco($datos, $this->bd_obj);
		// -------------------------------------------------------------------------------------------------
		// -------------------------------------------------------------------------------------------------
		//Busqueda de documentos por palabras clave -------------------------------------------
		elseif($accion == "get_documentos_x_palabras_clave")
			$this->resultado = $this->get_documentos_x_palabras_clave($datos, $this->bd_obj);
		//Obtencion de relacion de documentos por tema -------------------------------------------
		elseif($accion == "get_documentos_x_tema")
			$this->resultado = $this->get_documentos_x_tema($datos, $this->bd_obj);
		// -------------------------------------------------------------------------------------------------
		// -------------------------------------------------------------------------------------------------
		else
			$this->resultado =  "Error - metodo no definido";
	}
	
	// ------------------------------------------------------------------------------------------------------
	// ------------------------------------------------------------------------------------------------------
	private function get_evaluacion_entidad($datos, $bd_obj){
		if ( isset($datos["id_entidad"])){
			if( ctype_digit($datos["id_entidad"]) ){
				include_once ("./objects/entidad.php");
				$obj = new entidad();
				return $obj->get_evaluacion_entidad($datos["id_entidad"], $bd_obj);
			}
			else
				return "error";
		}
		else
			return "error";
	}
	
	private function get_evaluacion_n_entidades($datos, $bd_obj){
		if ( isset($datos["id_entidad"]) ){
			$ids = explode(",", $datos["id_entidad"]);
			$res = true;
			foreach ($ids as $id){
				if ( ! ( ctype_digit($id) ) )
					$res = False;
			}
			if( $res == True){
				include_once ("./objects/entidad.php");
				$obj = new entidad();
				return $obj->get_evaluacion_n_entidades($ids, $bd_obj);
			}
			else
				return "error";
		}
		else
			return "error";
	}
	
	private function get_documentos_entidad($datos, $bd_obj){
		if ( isset($datos["id_entidad"]) ){
			if( ctype_digit($datos["id_entidad"]) ){
				include_once ("./objects/entidad.php");
				$obj = new entidad();
				return $obj->get_documentos_entidad($datos["id_entidad"], $bd_obj);
			}
			else
				return "error";
		}else
			return "error";
	}
	
	private function get_documentos_n_entidades($datos, $bd_obj){
		if ( isset($datos["id_entidad"]) ){
			$ids = explode(",", $datos["id_entidad"]);
			$res = true;
			foreach ($ids as $id){
				if ( ! ( ctype_digit($id) ) )
					$res = False;
			}
			if( $res == True){
				include_once ("./objects/entidad.php");
				$obj = new entidad();
				return $obj->get_documentos_n_entidades($ids, $bd_obj);
			}
			else
				return "error";
		}else
			return "error";
	}
	
	private function get_cumplimiento_x_tipo_entidad_un_criterio($datos, $bd_obj){
		if ( isset($datos["id_tipo_entidad"]) and isset($datos["id_criterio"]) ){
			if ( ctype_digit( $datos["id_criterio"] ) and ctype_digit( $datos["id_tipo_entidad"] ) ){				
				include_once ("./objects/entidad.php");
				$obj = new entidad();
			return $obj->get_cumplimiento_x_tipo_entidad_un_criterio($datos["id_tipo_entidad"], $datos["id_criterio"], $bd_obj);
			}
			else
				return "error";
		}
		else
			return "error";
	}
	
	private function get_cumplimiento_todas_entidades_un_criterio($datos, $bd_obj){
		if ( isset($datos["id_criterio"]) ){
			if ( ctype_digit( $datos["id_criterio"] ) ){				
				include_once ("./objects/entidad.php");
				$obj = new entidad();
			return $obj->get_cumplimiento_todas_entidades_un_criterio($datos["id_criterio"], $bd_obj);
			}
			else
				return "error";
		}else
			return "error";
	}
	
	private function get_cumplimiento_x_tipo_entidad_todos_criterios($datos, $bd_obj){
		if ( isset($datos["id_tipo_entidad"]) ){
			if ( ctype_digit( $datos["id_tipo_entidad"] ) ){				
				include_once ("./objects/entidad.php");
				$obj = new entidad();
				return $obj->get_cumplimiento_x_tipo_entidad_todos_criterios($datos["id_tipo_entidad"], $bd_obj);
			}
			else
				return "error";
		}
		else
			return "error";
	}
	
	private function get_cumplimiento_todas_entidades_todos_criterios($datos, $bd_obj){
		include_once ("./objects/entidad.php");
		$obj = new entidad();
		return $obj->get_cumplimiento_todas_entidades_todos_criterios($bd_obj);
	}
	
	private function get_n_entidades_mejor_indice_global_x_tipo($datos, $bd_obj){
		if ( isset($datos["n"]) and isset($datos["id_tipo_entidad"]) ){
			if ( ctype_digit( $datos["n"] ) and ctype_digit( $datos["id_tipo_entidad"] ) ){				
				include_once ("./objects/entidad.php");
				$obj = new entidad();
				return $obj->get_n_entidades_mejor_indice_global_x_tipo($datos["n"], $datos["id_tipo_entidad"], $bd_obj);
			}else
				return "error";
		}else
			return "error";
	}
	
	private function get_n_entidades_mejor_indice_global($datos, $bd_obj){
		if ( isset($datos["n"]) ){
			if ( ctype_digit( $datos["n"] ) ){
				include_once ("./objects/entidad.php");
				$obj = new entidad();
				return $obj->get_n_entidades_mejor_indice_global($datos["n"], $bd_obj);
			}
			else
				return "error";
		}
		else
			return "error";
	}
	
	private function get_entidad_reporte_imco($datos, $bd_obj){
		if ( isset($datos["id_entidad"]) ){
			if ( ctype_digit( $datos["id_entidad"] ) ){
				include_once ("./objects/entidad.php");
				$obj = new entidad();
				return $obj->get_entidad_reporte_imco($datos["id_entidad"], $bd_obj);
			}
			else
				return "error";
		}
		else
			return "error";
	}
	
	private function get_n_entidades_reporte_imco($datos, $bd_obj){
		if ( isset($datos["id_entidad"]) ){
			$ids = explode(",", $datos["id_entidad"]);
			$res = true;
			foreach ($ids as $id){
				if ( ! ( ctype_digit($id) ) )
					$res = False;
			}
			if( $res == True){
				include_once ("./objects/entidad.php");
				$obj = new entidad();
				return $obj->get_n_entidades_reporte_imco($ids, $bd_obj);
			}
			else
				return "error";
		}
		else
			return "error";	
	}
	
	private function get_entidad_pe_li($datos, $bd_obj){
		if ( isset($datos["id_entidad"]) ){
			if ( ctype_digit( $datos["id_entidad"] ) ){
				include_once ("./objects/entidad.php");
				$obj = new entidad();
				return $obj->get_entidad_pe_li($datos["id_entidad"], $bd_obj);
			}
			else
				return "error";
		}
		else
			return "error";	
	}
	
	private function get_n_entidades_pe_li($datos, $bd_obj){
		if ( isset($datos["id_entidad"]) ){
			$ids = explode(",", $datos["id_entidad"]);
			$res = true;
			foreach ($ids as $id){
				if ( ! ( ctype_digit($id) ) )
					$res = False;
			}
			if( $res == True){
				include_once ("./objects/entidad.php");
				$obj = new entidad();
				return $obj->get_n_entidades_pe_li($ids, $bd_obj);
			}
			else
				return "error";
		}
		else
			return "error";	
	}
	
	private function get_entidad_todos_datos($datos, $bd_obj){
		if ( isset($datos["id_entidad"]) ){
			if ( ctype_digit( $datos["id_entidad"] ) ){
				include_once ("./objects/entidad.php");
				$obj = new entidad();
				return $obj->get_entidad_todos_datos($datos["id_entidad"], $bd_obj);
			}
			else
				return "error";
		}
		else
			return "error";	
	}
	
	private function get_n_entidades_todos_datos($datos, $bd_obj){
		if ( isset($datos["id_entidad"]) ){
			$ids = explode(",", $datos["id_entidad"]);
			$res = true;
			foreach ($ids as $id){
				if ( ! ( ctype_digit($id) ) )
					$res = False;
			}
			if( $res == True){
				include_once ("./objects/entidad.php");
				$obj = new entidad();
				return $obj->get_n_entidades_todos_datos($ids, $bd_obj);
			}
			else
				return "error";
		}
		else
			return "error";	
	}
	
	// -------------------------------------------------------------------------------------------
	// -------------------------------------------------------------------------------------------
	// -------------------------------------------------------------------------------------------
	
	/*
	private function get_documentos_x_palabras_clave($datos, $bd_obj){
		if ( isset($datos["q"]) ){
			$palabras_busqueda = split(",", $datos["q"])
			include_once ("./objects/busqueda_solr.php");
			$obj = new busqueda_solr();
			return $obj->get_documentos_x_palabras_clave($palabras_busqueda, $bd_obj);
		}else
			return "error";
	}
	
	private function get_documentos_x_tema($datos, $bd_obj){
		if ( isset($datos["q"]) ){
			$palabras_busqueda = split(",", $datos["q"])
			include_once ("./objects/busqueda_solr.php");
			$obj = new busqueda_solr();
			return $obj->get_documentos_x_tema($palabras_busqueda, $bd_obj);
		}else
			return "error";
	}
	*/
	
	// -------------------------------------------------------------------------------------------
	// -------------------------------------------------------------------------------------------
	// -------------------------------------------------------------------------------------------
	private function get_catalogo_tipo_entidades($datos, $bd_obj){
		include_once ("./objects/entidad.php");
		$obj = new entidad();
		return $obj->get_catalogo_tipo_entidades($bd_obj);
	}
	
	private function get_catalogo_entidades_x_tipo_entidad($datos, $bd_obj){
		if ( isset($datos["id_tipo_entidad"]) ){
			include_once ("./objects/entidad.php");
			$obj = new entidad();
			return $obj->get_catalogo_entidades_x_tipo_entidad($datos["id_tipo_entidad"], $bd_obj);
		}else
			return "error";
	}		
			
	private function get_catalogo_entidades_todos_tipos($datos, $bd_obj){
		include_once ("./objects/entidad.php");
		$obj = new entidad();
		return $obj->get_catalogo_entidades_todos_tipos($bd_obj);
	}
	
	private function get_catalogo_criterios_evaluacion($datos, $bd_obj){
		include_once ("./objects/entidad.php");
		$obj = new entidad();
		return $obj->get_catalogo_criterios_evaluacion($bd_obj);
	}
	
	private function get_catalogo_reporte_imco($datos, $bd_obj){
		include_once ("./objects/entidad.php");
		$obj = new entidad();
		return $obj->get_catalogo_reporte_imco($bd_obj);
	}
			
	//--------------------------------------------------------------------------------------------------------
	//--------------------------------------------------------------------------------------------------------
	//Funcion que ordena un arreglo en base a parametros
	private function array_orderby(){
		$args = func_get_args();
		$data = array_shift($args);
		foreach ($args as $n => $field) {
			if (is_string($field)) {
				$tmp = array();
				foreach ($data as $key => $row)
					$tmp[$key] = $row[$field];
				$args[$n] = $tmp;
			}
		}
		$args[] = &$data;
		call_user_func_array('array_multisort', $args);
		return array_pop($args);
	}
	
	//Es la funcion que regresa el resultado en formato json
	public function get_resultado(){
		return json_encode($this->resultado);
	}	
}



if( isset($_GET["accion"]) ){
	$accion = $_GET["accion"];
	$datos = $_GET;
	unset($datos["accion"]);
	if (isset($accion) and isset($datos)){
		$caller = new web_services($accion, $datos);
		if(isset($_GET['callback']))
			$resultado = $_GET['callback'].'('.$caller->get_resultado().')';
		else	
			$resultado = $caller->get_resultado();
	}else{
		if(isset($_GET['callback']))
			$resultado = $_GET['callback'].'('.json_encode("Error - falla de parametros").')';
			$resultado = json_encode("Error - falla de parametros");
	}
}
else{
	$resultado = ( isset($_GET['callback']) ) ? $_GET['callback'].'('.json_encode("Error - falla ws").')' : json_encode("Error - falla ws");
}
echo $resultado;
?>
