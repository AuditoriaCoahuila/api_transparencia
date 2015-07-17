<?php

class handler_action{
	
	private $web_services_registry;
	private $bd_obj;
	
	function __construct($bd_obj){
		$this->bd_obj = $bd_obj;
		$this->web_services_registry = array(
				//Detalle de cumplimiento de informacion de una entidad 
				"get_evaluacion_entidad" => array ("method" => "get_evaluacion_entidad", "object" => "entidad"), 
				//Detalle de cumplimiento de información de n entidades 
				"get_evaluacion_n_entidades" => array ("method" => "get_evaluacion_n_entidades", "object" => "entidad"),
				//Obtencion de relacion de documentos de una entidad 
				"get_documentos_entidad" => array ("method" => "get_documentos_entidad", "object" => "entidad"),
				//Obtencion de relacion de documentos de n entidades 
				"get_documentos_n_entidades" => array ("method" => "get_documentos_n_entidades", "object" => "entidad"),
				//Cumplimiento de un requerimiento de todas las entidades de un tipo 
				"get_cumplimiento_x_tipo_entidad_un_criterio" => array ("method" => "get_cumplimiento_x_tipo_entidad_un_criterio", "object" => "entidad"),
				//Cumplimiento de un requerimiento de todas las entidades
				"get_cumplimiento_todas_entidades_un_criterio" => array ("method" => "get_cumplimiento_todas_entidades_un_criterio", "object" => "entidad"),
				//Cumplimiento de n requerimientos de todas las entidades de un tipo 
				"get_cumplimiento_x_tipo_entidad_todos_criterios" => array ("method" => "get_cumplimiento_x_tipo_entidad_todos_criterios", "object" => "entidad"),
				//Cumplimiento de n requerimientos de todas las entidades 
				"get_cumplimiento_todas_entidades_todos_criterios" => array ("method" => "get_cumplimiento_todas_entidades_todos_criterios", "object" => "entidad"),
				//n entidades de un tipo con el mayor indice de cumplimiento 
				"get_n_entidades_mejor_indice_global_x_tipo" => array ("method" => "get_n_entidades_mejor_indice_global_x_tipo", "object" => "entidad"),
				//n entidades con el mayorindice de cumplimiento 
				"get_n_entidades_mejor_indice_global" => array ("method" => "get_n_entidades_mejor_indice_global", "object" => "entidad"),
				//Obtiene los valores de los conceptos del reporte del IMCO para una entidad
				"get_entidad_reporte_imco" => array ("method" => "get_entidad_reporte_imco", "object" => "entidad"),
				//Obtiene los valores de los conceptos del reporte del IMCO para n entidades
				"get_n_entidades_reporte_imco" => array ("method" => "get_n_entidades_reporte_imco", "object" => "entidad"),
				//Regresa los datos de ley de ingresos y presupuesto de egresos para una entidad
				"get_entidad_pe_li" => array ("method" => "get_entidad_pe_li", "object" => "entidad"),
				//Regresa los datos de ley de ingresos y presupuesto de egresos para n entidades
				"get_n_entidades_pe_li" => array ("method" => "get_n_entidades_pe_li", "object" => "entidad"),
				
				//Regresa las cuentas publicas de un municipio
				"get_entidad_cuentas_publicas" => array ("method" => "get_entidad_cuentas_publicas", "object" => "entidad"),
				//Regresa las cuentas publicas de n municipios
				"get_n_entidades_cuentas_publicas" => array ("method" => "get_n_entidades_cuentas_publicas", "object" => "entidad"),
				//Regresa la información presupuestal de un municipio
				"get_entidad_informacion_presupuestal" => array ("method" => "get_entidad_informacion_presupuestal", "object" => "entidad"),
				//Regresa la información presupuestal de n municipios
				"get_n_entidades_informacion_presupuestal" => array ("method" => "get_n_entidades_informacion_presupuestal", "object" => "entidad"),
				//Regresa la valuacion actuarial de un municipio
				"get_entidad_valuacion_actuarial" => array ("method" => "get_entidad_valuacion_actuarial", "object" => "entidad"),
				//Regresa la valuacion actuarial de n municipios
				"get_n_entidades_valuacion_actuarial" => array ("method" => "get_n_entidades_valuacion_actuarial", "object" => "entidad"),
				
				//Regresa todos los datos asociados a una entidad
				"get_entidad_todos_datos" => array ("method" => "get_entidad_todos_datos", "object" => "entidad"),
				//Regresa todos los datos asociados a n entidades
				"get_n_entidades_todos_datos" => array ("method" => "get_n_entidades_todos_datos", "object" => "entidad"),
				// Obtiene el catalogo de tipo de entidades 
				"get_catalogo_tipo_entidades" => array ("method" => "get_catalogo_tipo_entidades", "object" => "entidad"),
				// Obtiene el catalogo de un tipo de entidades 
				"get_catalogo_entidades_x_tipo_entidad" => array ("method" => "get_catalogo_entidades_x_tipo_entidad", "object" => "entidad"),
				//Obtiene el catalogo de todas las entidades 
				"get_catalogo_entidades_todos_tipos" => array ("method" => "get_catalogo_entidades_todos_tipos", "object" => "entidad"),
				//Obtiene el catalogo de criterios de evaluacion 
				"get_catalogo_criterios_evaluacion" => array ("method" => "get_catalogo_criterios_evaluacion", "object" => "entidad"),
				//Obtiene el catalogo de las vriables requeridas en el reporte del IMCO
				"get_catalogo_reporte_imco" => array ("method" => "get_catalogo_reporte_imco", "object" => "entidad"),
				//Obtiene el catalogo de los conceptos de las cuentas publicas de un municipio
				"get_catalogo_cuentas_publicas" => array ("method" => "get_catalogo_cuentas_publicas", "object" => "entidad"),
				//Obtiene el catalogo de los tipos de documentos no relacionados directamente con el indice del IMCO
				"get_catalogo_documentos_generales" => array ("method" => "get_catalogo_documentos_generales", "object" => "entidad"),
				//Obtiene el catalogo de los conceptos de la información presupuestal de un municipio
				"get_catalogo_informacion_presupuestal" => array ("method" => "get_catalogo_informacion_presupuestal", "object" => "entidad"),
				//Obtiene el catalogo de los conceptos de la valuacion actuarial de un municipio
				"get_catalogo_valuacion_actuarial" => array ("method" => "get_catalogo_valuacion_actuarial", "object" => "entidad"),
				//Busqueda de documentos por palabras clave 
				"get_documentos_x_palabras_clave" => array ("method" => "get_documentos_x_palabras_clave", "object" => "entidad"),
				//Obtencion de relacion de documentos por tema 
				"get_documentos_x_tema" => array ("method" => "get_documentos_x_tema", "object" => "entidad"),
			);
	}
	
	public function execute_action($accion, $datos){
		if ( isset($this->web_services_registry[$accion]["method"]) ){
			return $this->$accion($datos, $this->web_services_registry[$accion]["object"], $this->bd_obj);
		}
		else
			return "Error - Metodo no definido";
		
	}
	
	// ------------------------------------------------------------------------------------------------------
	// ------------------------------------------------------------------------------------------------------
	// ------------------------------------------------------------------------------------------------------
	private function get_evaluacion_entidad($datos, $object, $bd_obj){
		if ( isset($datos["id_entidad"])){
			if( ctype_digit($datos["id_entidad"]) ){
				include_once ("./objects/" . $object . ".php");
				$obj = new entidad();
				return $obj->get_evaluacion_entidad($datos["id_entidad"], $bd_obj);
			}
			else
				return "Error - Validacion de parametros incorrecta";
		}
		else
			return "Error - Validacion de parametros incorrecta";
	}
	
	private function get_evaluacion_n_entidades($datos, $object, $bd_obj){
		if ( isset($datos["id_entidad"]) ){
			$ids = explode(",", $datos["id_entidad"]);
			$res = true;
			foreach ($ids as $id){
				if ( ! ( ctype_digit($id) ) )
					$res = False;
			}
			if( $res == True){
				include_once ("./objects/" . $object . ".php");
				$obj = new entidad();
				return $obj->get_evaluacion_n_entidades($ids, $bd_obj);
			}
			else
				return "Error - Validacion de parametros incorrecta";
		}
		else
			return "Error - Validacion de parametros incorrecta";
	}
	
	private function get_documentos_entidad($datos, $object, $bd_obj){
		if ( isset($datos["id_entidad"]) ){
			if( ctype_digit($datos["id_entidad"]) ){
				include_once ("./objects/" . $object . ".php");
				$obj = new entidad();
				return $obj->get_documentos_entidad($datos["id_entidad"], $bd_obj);
			}
			else
				return "Error - Validacion de parametros incorrecta";
		}else
			return "Error - Validacion de parametros incorrecta";
	}
	
	private function get_documentos_n_entidades($datos, $object, $bd_obj){
		if ( isset($datos["id_entidad"]) ){
			$ids = explode(",", $datos["id_entidad"]);
			$res = true;
			foreach ($ids as $id){
				if ( ! ( ctype_digit($id) ) )
					$res = False;
			}
			if( $res == True){
				include_once ("./objects/" . $object . ".php");
				$obj = new entidad();
				return $obj->get_documentos_n_entidades($ids, $bd_obj);
			}
			else
				return "Error - Validacion de parametros incorrecta";
		}else
			return "Error - Validacion de parametros incorrecta";
	}
	
	private function get_cumplimiento_x_tipo_entidad_un_criterio($datos, $object, $bd_obj){
		if ( isset($datos["id_tipo_entidad"]) and isset($datos["id_criterio"]) ){
			if ( ctype_digit( $datos["id_criterio"] ) and ctype_digit( $datos["id_tipo_entidad"] ) ){				
				include_once ("./objects/" . $object . ".php");
				$obj = new entidad();
			return $obj->get_cumplimiento_x_tipo_entidad_un_criterio($datos["id_tipo_entidad"], $datos["id_criterio"], $bd_obj);
			}
			else
				return "Error - Validacion de parametros incorrecta";
		}
		else
			return "Error - Validacion de parametros incorrecta";
	}
	
	private function get_cumplimiento_todas_entidades_un_criterio($datos, $object, $bd_obj){
		if ( isset($datos["id_criterio"]) ){
			if ( ctype_digit( $datos["id_criterio"] ) ){				
				include_once ("./objects/" . $object . ".php");
				$obj = new entidad();
			return $obj->get_cumplimiento_todas_entidades_un_criterio($datos["id_criterio"], $bd_obj);
			}
			else
				return "Error - Validacion de parametros incorrecta";
		}else
			return "Error - Validacion de parametros incorrecta";
	}
	
	private function get_cumplimiento_x_tipo_entidad_todos_criterios($datos, $object, $bd_obj){
		if ( isset($datos["id_tipo_entidad"]) ){
			if ( ctype_digit( $datos["id_tipo_entidad"] ) ){				
				include_once ("./objects/" . $object . ".php");
				$obj = new entidad();
				return $obj->get_cumplimiento_x_tipo_entidad_todos_criterios($datos["id_tipo_entidad"], $bd_obj);
			}
			else
				return "Error - Validacion de parametros incorrecta";
		}
		else
			return "Error - Validacion de parametros incorrecta";
	}
	
	private function get_cumplimiento_todas_entidades_todos_criterios($datos, $object, $bd_obj){
		include_once ("./objects/" . $object . ".php");
		$obj = new entidad();
		return $obj->get_cumplimiento_todas_entidades_todos_criterios($bd_obj);
	}
	
	private function get_n_entidades_mejor_indice_global_x_tipo($datos, $object, $bd_obj){
		if ( isset($datos["n"]) and isset($datos["id_tipo_entidad"]) ){
			if ( ctype_digit( $datos["n"] ) and ctype_digit( $datos["id_tipo_entidad"] ) ){				
				include_once ("./objects/" . $object . ".php");
				$obj = new entidad();
				return $obj->get_n_entidades_mejor_indice_global_x_tipo($datos["n"], $datos["id_tipo_entidad"], $bd_obj);
			}else
				return "Error - Validacion de parametros incorrecta";
		}else
			return "Error - Validacion de parametros incorrecta";
	}
	
	private function get_n_entidades_mejor_indice_global($datos, $object, $bd_obj){
		if ( isset($datos["n"]) ){
			if ( ctype_digit( $datos["n"] ) ){
				include_once ("./objects/" . $object . ".php");
				$obj = new entidad();
				return $obj->get_n_entidades_mejor_indice_global($datos["n"], $bd_obj);
			}
			else
				return "Error - Validacion de parametros incorrecta";
		}
		else
			return "Error - Validacion de parametros incorrecta";
	}
	
	private function get_entidad_reporte_imco($datos, $object, $bd_obj){
		if ( isset($datos["id_entidad"]) ){
			if ( ctype_digit( $datos["id_entidad"] ) ){
				include_once ("./objects/" . $object . ".php");
				$obj = new entidad();
				return $obj->get_entidad_reporte_imco($datos["id_entidad"], $bd_obj);
			}
			else
				return "Error - Validacion de parametros incorrecta";
		}
		else
			return "Error - Validacion de parametros incorrecta";
	}
	
	private function get_n_entidades_reporte_imco($datos, $object, $bd_obj){
		if ( isset($datos["id_entidad"]) ){
			$ids = explode(",", $datos["id_entidad"]);
			$res = true;
			foreach ($ids as $id){
				if ( ! ( ctype_digit($id) ) )
					$res = False;
			}
			if( $res == True){
				include_once ("./objects/" . $object . ".php");
				$obj = new entidad();
				return $obj->get_n_entidades_reporte_imco($ids, $bd_obj);
			}
			else
				return "Error - Validacion de parametros incorrecta";
		}
		else
			return "Error - Validacion de parametros incorrecta";
	}
	
	private function get_entidad_pe_li($datos, $object, $bd_obj){
		if ( isset($datos["id_entidad"]) ){
			if ( ctype_digit( $datos["id_entidad"] ) ){
				include_once ("./objects/" . $object . ".php");
				$obj = new entidad();
				return $obj->get_entidad_pe_li($datos["id_entidad"], $bd_obj);
			}
			else
				return "Error - Validacion de parametros incorrecta";
		}
		else
			return "Error - Validacion de parametros incorrecta";
	}
	
	private function get_n_entidades_pe_li($datos, $object, $bd_obj){
		if ( isset($datos["id_entidad"]) ){
			$ids = explode(",", $datos["id_entidad"]);
			$res = true;
			foreach ($ids as $id){
				if ( ! ( ctype_digit($id) ) )
					$res = False;
			}
			if( $res == True){
				include_once ("./objects/" . $object . ".php");
				$obj = new entidad();
				return $obj->get_n_entidades_pe_li($ids, $bd_obj);
			}
			else
				return "Error - Validacion de parametros incorrecta";
		}
		else
			return "Error - Validacion de parametros incorrecta";
	}
	
	private function get_entidad_cuentas_publicas($datos, $object, $bd_obj){
		if ( isset($datos["id_entidad"]) ){
			if ( ctype_digit( $datos["id_entidad"] ) ){
				include_once ("./objects/" . $object . ".php");
				$obj = new entidad();
				return $obj->get_entidad_cuentas_publicas($datos["id_entidad"], $bd_obj);
			}
			else
				return "Error - Validacion de parametros incorrecta";
		}
		else
			return "Error - Validacion de parametros incorrecta";
	}
	
	private function get_n_entidades_cuentas_publicas($datos, $object, $bd_obj){
		if ( isset($datos["id_entidad"]) ){
			$ids = explode(",", $datos["id_entidad"]);
			$res = true;
			foreach ($ids as $id){
				if ( ! ( ctype_digit($id) ) )
					$res = False;
			}
			if( $res == True){
				include_once ("./objects/" . $object . ".php");
				$obj = new entidad();
				return $obj->get_n_entidades_cuentas_publicas($ids, $bd_obj);
			}
			else
				return "Error - Validacion de parametros incorrecta";
		}
		else
			return "Error - Validacion de parametros incorrecta";
	}
	
	private function get_entidad_informacion_presupuestal($datos, $object, $bd_obj){
		if ( isset($datos["id_entidad"]) ){
			if ( ctype_digit( $datos["id_entidad"] ) ){
				include_once ("./objects/" . $object . ".php");
				$obj = new entidad();
				return $obj->get_entidad_informacion_presupuestal($datos["id_entidad"], $bd_obj);
			}
			else
				return "Error - Validacion de parametros incorrecta";
		}
		else
			return "Error - Validacion de parametros incorrecta";
	}
	
	private function get_n_entidades_informacion_presupuestal($datos, $object, $bd_obj){
		if ( isset($datos["id_entidad"]) ){
			$ids = explode(",", $datos["id_entidad"]);
			$res = true;
			foreach ($ids as $id){
				if ( ! ( ctype_digit($id) ) )
					$res = False;
			}
			if( $res == True){
				include_once ("./objects/" . $object . ".php");
				$obj = new entidad();
				return $obj->get_n_entidades_informacion_presupuestal($ids, $bd_obj);
			}
			else
				return "Error - Validacion de parametros incorrecta";
		}
		else
			return "Error - Validacion de parametros incorrecta";
	}
	
	private function get_entidad_valuacion_actuarial($datos, $object, $bd_obj){
		if ( isset($datos["id_entidad"]) ){
			if ( ctype_digit( $datos["id_entidad"] ) ){
				include_once ("./objects/" . $object . ".php");
				$obj = new entidad();
				return $obj->get_entidad_valuacion_actuarial($datos["id_entidad"], $bd_obj);
			}
			else
				return "Error - Validacion de parametros incorrecta";
		}
		else
			return "Error - Validacion de parametros incorrecta";
	}
	
	private function get_n_entidades_valuacion_actuarial($datos, $object, $bd_obj){
		if ( isset($datos["id_entidad"]) ){
			$ids = explode(",", $datos["id_entidad"]);
			$res = true;
			foreach ($ids as $id){
				if ( ! ( ctype_digit($id) ) )
					$res = False;
			}
			if( $res == True){
				include_once ("./objects/" . $object . ".php");
				$obj = new entidad();
				return $obj->get_n_entidades_valuacion_actuarial($ids, $bd_obj);
			}
			else
				return "Error - Validacion de parametros incorrecta";
		}
		else
			return "Error - Validacion de parametros incorrecta";
	}
	
	private function get_entidad_todos_datos($datos, $object, $bd_obj){
		if ( isset($datos["id_entidad"]) ){
			if ( ctype_digit( $datos["id_entidad"] ) ){
				include_once ("./objects/" . $object . ".php");
				$obj = new entidad();
				return $obj->get_entidad_todos_datos($datos["id_entidad"], $bd_obj);
			}
			else
				return "Error - Validacion de parametros incorrecta";
		}
		else
			return "Error - Validacion de parametros incorrecta";
	}
	
	private function get_n_entidades_todos_datos($datos, $object, $bd_obj){
		if ( isset($datos["id_entidad"]) ){
			$ids = explode(",", $datos["id_entidad"]);
			$res = true;
			foreach ($ids as $id){
				if ( ! ( ctype_digit($id) ) )
					$res = False;
			}
			if( $res == True){
				include_once ("./objects/" . $object . ".php");
				$obj = new entidad();
				return $obj->get_n_entidades_todos_datos($ids, $bd_obj);
			}
			else
				return "Error - Validacion de parametros incorrecta";
		}
		else
			return "Error - Validacion de parametros incorrecta";
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
			return "Error - Validacion de parametros incorrecta";
	}
	
	private function get_documentos_x_tema($datos, $bd_obj){
		if ( isset($datos["q"]) ){
			$palabras_busqueda = split(",", $datos["q"])
			include_once ("./objects/busqueda_solr.php");
			$obj = new busqueda_solr();
			return $obj->get_documentos_x_tema($palabras_busqueda, $bd_obj);
		}else
			return "Error - Validacion de parametros incorrecta";
	}
	*/
	
	// -------------------------------------------------------------------------------------------
	// -------------------------------------------------------------------------------------------
	// -------------------------------------------------------------------------------------------
	private function get_catalogo_tipo_entidades($datos, $object, $bd_obj){
		include_once ("./objects/" . $object . ".php");
		$obj = new entidad();
		return $obj->get_catalogo_tipo_entidades($bd_obj);
	}
	
	private function get_catalogo_entidades_x_tipo_entidad($datos, $object, $bd_obj){
		if ( isset($datos["id_tipo_entidad"]) ){
			include_once ("./objects/" . $object . ".php");
			$obj = new entidad();
			return $obj->get_catalogo_entidades_x_tipo_entidad($datos["id_tipo_entidad"], $bd_obj);
		}else
			return "Error - Validacion de parametros incorrecta";
	}		
			
	private function get_catalogo_entidades_todos_tipos($datos, $object, $bd_obj){
		include_once ("./objects/" . $object . ".php");
		$obj = new entidad();
		return $obj->get_catalogo_entidades_todos_tipos($bd_obj);
	}
	
	private function get_catalogo_criterios_evaluacion($datos, $object, $bd_obj){
		include_once ("./objects/" . $object . ".php");
		$obj = new entidad();
		return $obj->get_catalogo_criterios_evaluacion($bd_obj);
	}
	
	private function get_catalogo_reporte_imco($datos, $object, $bd_obj){
		include_once ("./objects/" . $object . ".php");
		$obj = new entidad();
		return $obj->get_catalogo_reporte_imco($bd_obj);
	}
	
	private function get_catalogo_cuentas_publicas($datos, $object, $bd_obj){
		include_once ("./objects/" . $object . ".php");
		$obj = new entidad();
		return $obj->get_catalogo_cuentas_publicas($bd_obj);
	}
	
	private function get_catalogo_documentos_generales($datos, $object, $bd_obj){
		include_once ("./objects/" . $object . ".php");
		$obj = new entidad();
		return $obj->get_catalogo_documentos_generales($bd_obj);
	}
	
	private function get_catalogo_informacion_presupuestal($datos, $object, $bd_obj){
		include_once ("./objects/" . $object . ".php");
		$obj = new entidad();
		return $obj->get_catalogo_informacion_presupuestal($bd_obj);
	}
	
	private function get_catalogo_valuacion_actuarial($datos, $object, $bd_obj){
		include_once ("./objects/" . $object . ".php");
		$obj = new entidad();
		return $obj->get_catalogo_valuacion_actuarial($bd_obj);
	}
	
}
?>