<?php 

class busqueda_solr{
	
	/*
	get_documentos_x_palabras_clave
	get_documentos_x_tema
	*/
	
	public function get_estadisticas_dependencias($bd_obj, $funciones_busqueda){
		$query = "SELECT d.id, d.nombre, a.tipo_accion, count(a.id) as numero_ocurrencias ";
		$query .= " FROM dependencia d, dependencia_tramite dt, tramite t, tramite_accion a ";
		$query .= " WHERE d.id = dt.id_dependencia AND dt.id_tramite = t.id AND t.id = a.id_tramite ";
		$query .= " GROUP BY d.id, d.nombre, a.tipo_accion ";
		$resultado = $bd_obj->ejecutarConsulta($query);
		$numero_resultados = pg_num_rows($resultado);
		$resultados = array();
		while ($fila = pg_fetch_assoc($resultado)) {
			if(! isset($resultados[$fila["id"]]))
				$resultados[$fila["id"]] = array("id" => $fila["id"], "nombre" => $fila["nombre"]);
			$resultados[$fila["id"]][$fila["tipo_accion"]] = $fila["numero_ocurrencias"];
		}
		//var_dump($resultados);
		//El ultimo parametro indica bajo que conceptovan a organizar el arreglo 
		//y es uno de los valores admitidos para el campo tipo_accion de a base de datos
		if(count($resultados) > 1)
			$resultados = array_reverse($funciones_busqueda->quicksort($resultados,0,count($resultados)-1 ,"descargas"));
		return $resultados;
	}
	
	public function get_info_dependencia($id, $bd_obj){
		//Primero busca los datos generales
		$query = "SELECT * FROM dependencia WHERE id = ". $id;
		$resultado = $bd_obj->ejecutarConsulta($query);
		$resultados = array();
		while ($fila = pg_fetch_assoc($resultado)) 
			$resultados["datos_generales"] = $fila;
		//Busca los documentos relacionados al tramite
		$query = "SELECT * FROM dependencia_direccion WHERE id_dependencia = ". $id;
		$resultado = $bd_obj->ejecutarConsulta($query);
		while ($fila = pg_fetch_assoc($resultado)){ 
			$resultados["direcciones"][$fila["id"]] = $fila;
			$query = "SELECT id, tipo_dato, dato FROM dependencia_direccion_contacto WHERE id_direccion = ". $fila["id"];
			$resultado2 = $bd_obj->ejecutarConsulta($query);
			while ($fila2 = pg_fetch_assoc($resultado2))
				$resultados["direcciones"][$fila["id"]]["contactos"][$fila2["id"]] = $fila2;
		}
		return $resultados;
	}
	
	public function get_info_todas_dependencias($bd_obj){
		//Primero busca los datos generales
		$query = "SELECT * FROM dependencia ";
		$resultado = $bd_obj->ejecutarConsulta($query);
		$resultados = array();
		while ($fila = pg_fetch_assoc($resultado)){ 
			$resultados[$fila["id"]]["datos_generales"] = $fila;
			//Busca los documentos relacionados al tramite
			$query = "SELECT * FROM dependencia_direccion WHERE id_dependencia = ". $fila["id"];
			$resultado2 = $bd_obj->ejecutarConsulta($query);
			while ($fila2 = pg_fetch_assoc($resultado2)){ 
				$resultados[$fila["id"]]["direcciones"][$fila2["id"]] = $fila2;
				$query = "SELECT id, tipo_dato, dato FROM dependencia_direccion_contacto WHERE id_direccion = ". $fila2["id"];
				$resultado3 = $bd_obj->ejecutarConsulta($query);
				while ($fila3 = pg_fetch_assoc($resultado3))
					$resultados[$fila["id"]]["direcciones"][$fila2["id"]]["contactos"][$fila3["id"]] = $fila3;
			}
		}
		return $resultados;
	}
 
	public function agregar_dependencia($datos, $bd_obj){
		$query = " INSERT INTO dependencia (nombre) VALUES('".$datos["nombre"]."');";
		$resultado = $bd_obj->ejecutarInsercion($query);
		if($resultado!= False)
			return True;
		else
			return False;
	}
	
	public function agregar_dependencia_direccion($datos, $bd_obj){
		$query = " INSERT INTO dependencia_direccion (id_dependencia, calle, num_ext, num_int, colonia, municipio, codigo_postal, estado) ";
		$query .= " VALUES(".$datos["id_dependencia"].", '".$datos["calle"]."', '".$datos["num_ext"]."', '".$datos["num_int"]."', ";
		$query .= " '".$datos["colonia"]."', '".$datos["municipio"]."', ".$datos["codigo_postal"].", '".$datos["estado"]."');";
		$resultado = $bd_obj->ejecutarInsercion($query);
		if( $resultado != False)
			return True;
		else
			return False;
	}
	
	public function agregar_dependencia_contacto($datos, $bd_obj){
		$query = " INSERT INTO dependencia_direccion_contacto (id_direccion, tipo_dato, dato) ";
		$query .= " VALUES(".$datos["id_direccion"].", '".$datos["tipo_dato"]."', '".$datos["dato"]."');";
		$resultado = $bd_obj->ejecutarInsercion($query);
		if( $resultado != False)
			return True;
		else
			return False;
	}
	
}
?>
