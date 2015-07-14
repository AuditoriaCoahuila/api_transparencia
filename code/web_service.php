<?php
error_reporting(E_ALL);
ini_set( "memory_limit","512M");
ini_set( "max_execution_time","60");
ini_set('display_errors', '1');

class web_services{
	private $bd_obj;
	private $handler_accion;
	
	private $resultado;
		
	function __construct(){
		include_once ("./bd/gestorBD.php");
		include_once ("./library/handler_action.php");
		
		$this->bd_obj = new gestorBD();
		$this->handler_accion = new handler_action($this->bd_obj);	
	}	
	
	public function execute_action($accion, $datos){
		$this->resultado = $this->handler_accion->execute_action($accion, $datos);
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
		$ws = new web_services();
		$ws->execute_action($accion, $datos);
		if(isset($_GET['callback']))
			$resultado = $_GET['callback'].'('.$ws->get_resultado().')';
		else	
			$resultado = $ws->get_resultado();
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