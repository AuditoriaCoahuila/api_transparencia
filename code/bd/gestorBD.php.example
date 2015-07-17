<?php 
class gestorBD{
	protected $validacion;
	protected $usuario;
	protected $contrasena;
	protected $servidor;
	protected $puerto;
	protected $bd;

	function __construct(){
		$this->usuario = "user_name";
		$this->contrasena = "user_pwd";
		$this->servidor = "server_ip";
		$this->puerto = "db_port";
		$this->bd = "db_name";	
	}

	public function ejecutarConsulta($query){
		$dataTable =false;
		$conexion = pg_connect("host=" .$this->servidor ." dbname=" .$this->bd ." port=" .$this->puerto ." user=" .$this->usuario ." password=" .$this->contrasena ."")or die ("error de conexión");
		$dataTable = pg_query($conexion, $query);
		pg_close($conexion); 
		return $dataTable;
	}

	public function ejecutarInsercion($query){
		$dataTable =false;
		$conexion = pg_connect("host=" .$this->servidor ." dbname=" .$this->bd ." port=" .$this->puerto ." user=" .$this->usuario ." password=" .$this->contrasena ."");
		$dataTable = pg_query($conexion, $query) ;
		pg_close($conexion); 
		return $dataTable;
	}
	
	public function ejecutarActualizacion($query){
		$dataTable =false;
		$conexion = pg_connect("host=" .$this->servidor ." dbname=" .$this->bd ." port=" .$this->puerto ." user=" .$this->usuario ." password=" .$this->contrasena ."")or die ("error de conexión");
		$dataTable = pg_query($conexion, $query);
		pg_close($conexion); 
		return $dataTable;
	}
	
	public function ejecutarEliminacion($query){
		$dataTable =false;
		$conexion = pg_connect("host=" .$this->servidor ." dbname=" .$this->bd ." port=" .$this->puerto ." user=" .$this->usuario ." password=" .$this->contrasena ."")or die ("error de conexión");
		$dataTable = pg_query($conexion, $query);
		pg_close($conexion); 
		return $dataTable;
	}
}
?>
