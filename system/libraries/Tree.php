<?php

/**
 * Clase que modela el nodo del Arbol
 *
 */
class Node {
	
	/**
	 * Id del nodo que se modela
	 *
	 * @var variant
	 * @access public
	 */
	public $id;
	
	/**
	 * Id del nodo padre.
	 *
	 * @var variant
	 * @access public
	 */
	public $parentId;
	
	/**
	 * Arreglo asociativo o datos de informacion del nodo.
	 *
	 * @var variant
	 */
	public $info;
	
	/**
	 * Nodo padre del nodo en cuestion
	 * 
	 * @var Node
	 */
	public $parentNode;
	
	/**
	 * Arreglo de nodos Hijos
	 *
	 * @var unknown_type
	 */
	public $childs= array();
	
	public function __construct($id, $parentId, $info){
		$this->id= $id;
		$this->parentId= $parentId;
		$this->info= $info;
	}//constructor
	
	/**
	 * Agregar un hijo al Nodo
	 *
	 * @param Node $Node
	 * @return bool : si la operacion se realizo con exito o no
	 */
	public function addChild(Node& $Node){
		//Chequeamos que el nodo a agregar no existe en el arreglo de hijos
		$find= $this->searchChild($Node->id);
		if ($find===true) {
			$Node->parentNode= $this;
			$this->childs[]= $Node;
			return true;
		}else{
			return false;
		}
	}
	
	/**
	 * Borrar un nodo hijo cuyo id es igual al pasado por parametro
	 *
	 * @param variant $id :id del nodo hijo a borrar
	 * @return bool :si se elimino o no
	 */
	public function delChild($id){
		foreach ($this->childs as $key=> $child){
			if ($child->id == $id) {
				unset($this->childs[$key]);
				return true;
			}
		}
		return false;
	}
	
	/**
	 * Buscar en el arreglo de hijos el hijo de id igual al pasado por parametro
	 *
	 * @param variant $id :id a buscar
	 * @return Node : si lo encuentra retorna el hijo en caso contrario returna false
	 */
	public function searchChild($id){
		if (count($this->childs)==0) {
			return true;
		}
		foreach ($this->childs as $child){
			if ($child->id == $id) {
				return $child;
			}
		}
		return true;
	}
	
	/**
	 * Funcion que obtiene el valor de la llave $key dentro del nodo
	 * 
	 * @param string $key :nombre del campo dentro del nodo para buscar
	 * @return variant :retorna el valor del nodo que encuentra o false en caso contrario
	 */
	public function getValor($key){
		if (isset($this->$key)){//si existe la llave en los datos del nodo
			return $this->$key;
		}elseif (isset($this->info[$key])){ //si existe la llave en la informacion del nodo
			return $this->info[$key];
		}else{
			return false;
		}
	}

}//End class Node


/**
 * Clase que modela un arbol en una tabla en la DB
 */
class Tree {
	/**
	 * Raiz del Arbol
	 *
	 * @var Node
	 */
	public $root;
	
	/**
	 * Nombre del Campo identificador en la Tabla que modela el arbol
	 *
	 * @var string
	 */
	public $idFieldName= 'id';
	
	/**
	 * Nombre del Campo identificador del padre en la Tabla que modela el arbol
	 *
	 * @var string
	 */
	public $idParentFieldName= 'parentid';
	
	/**
	 * Id a partir del cual fue cargado el arbol
	 */
	public $idApartitDe= null;
	
	/**
	 * Constructor de la Clase
	 * 
	 * @param Operation $operation :objeto de conexion a la base de datos.
	 * @param string $table :nombre de la tabla que contiene el arbol
	 * @param string $idFieldName :nombre del campo que es llave primaria en la tabla del arbol
	 * @param string $idParentFieldName :nombre del campo que es llave del id del padre en la tabla del arbol
	 */
	public function __construct($treeArr=null, $idFrom= null, $idFieldName='id', $idParentFieldName='parentid'){
		if (!is_null($idFieldName)){
			$this->idFieldName= $idFieldName;
		}
		
		if (!is_null($idParentFieldName)){
			$this->idParentFieldName= $idParentFieldName;
		}
		
		if (!is_null($idFrom) && is_array($treeArr) && count($treeArr)>0){
			//Cargar el arbol
			$this->loadTree($treeArr, $idFrom);
		}
	}
	
	/**
	 * Funcion que carga todo el Arbol en root a partir del nodo $idApartitDe con una sola consulta SQL y trabajando el arreglo
	 *
	 * @param variant $idApartitDe :id del nodo a partir del que se quieren cargar los datos
	 * @return bool :false si no encontro el id.
	 */
	public function loadTree($treeArr, $idFrom=null){
	
		if (!empty($idFrom)) {
			$root= $this->searchIdInArr($treeArr, $idFrom, false);
		}else {
			$root= $this->searchIdInArr($treeArr, '');
		}
		
		if (count($root)>0 && isset($root[0][$this->idFieldName]) && $root[0][$this->idFieldName]!='') {
			$info= $root[0];
			$info= $this->cleanInfoArr($info);
			$this->root= new Node($root[0][$this->idFieldName],$root[0][$this->idParentFieldName],$info);
			
			$this->loadTreeRecursive($this->root, $treeArr);
			return true;
		}else {
			return false;
		}
	}
	
	/**
	 * Funcion recursiva que carga los hijos de los hijos
	 */
	private function loadTreeRecursive(Node & $node, & $treeArr){
		$childs= $this->searchIdInArr($treeArr, $node->id);
		if ($childs!== false && count($childs)>0){
			foreach ($childs as $data){
				
				if (isset($data[$this->idFieldName]) && $data[$this->idFieldName]!='' && 
					isset($data[$this->idParentFieldName]) && $data[$this->idParentFieldName]!='') {
						$info= $this->cleanInfoArr($data);
						$tempNode= new Node($data[$this->idFieldName],$data[$this->idParentFieldName],$info);
						//echo "<pre>"; print_r($tempNode); echo "</pre>";
						$node->addChild($tempNode);
						$this->loadTreeRecursive($tempNode, $treeArr);
				}
			}
		}
	}
	
	/**
	 * Funcion para buscar dentro de un arreglo que contiene todo el arbol los elementos que tengan el id que se pasa por parametro
	 * 
	 * @param array $Arr :Arreglo que tiene el Arbol. Se eliminanran los elementos que se encontraron
	 * @param variant $id :valor a buscar
	 * @param bool $idpadre :si busca por el id de padre o por el id
	 * @return array :con el registro encontrado o false en caso de que no se encuentre
	 */
	private function searchIdInArr(& $Arr, $id, $searchByParentId= true){
		if(!is_array($Arr) || !count($Arr)>0){
			return false;
		}
		
		if ($searchByParentId)
			$fieldName= $this->idParentFieldName;
		else
			$fieldName= $this->idFieldName;
		
		$rst= array();
		foreach ($Arr as $key=> $value)
			if ($value[$fieldName]== $id){
				$rst[]= $value;
				unset($Arr[$key]);
			}
		if (count($rst)>0) return $rst;
		return false;
	}
	
	/**
	 * Devuelve dado un arreglo con los datos del nodo sacados de la BD un arreglo solo con la informacion del nodo
	 * 
	 * @param array $arrNodeData :arreglo con los datos del nodo sacados de la BD
	 * @return array :solo con la informacion del nodo
	 */
	private function cleanInfoArr($arrNodeData){
		$info= $arrNodeData;
		$keys= array_keys($info);
		foreach ($keys as $key){
			if (is_numeric($key)) {
				unset($info[$key]);
			}
		}
		
		unset($info[$this->idFieldName]);
		unset($info[$this->idParentFieldName]);
		
		return $info;
	}
	
	/**
	 * Permite relizar una busqueda de un Nodo cuyo id sea el pasado por parametro. No Realiza consultas en la BD
	 *
	 * @param variant $idNodo :valor del id del nodo a buscar
	 * @return Node :el nodo si lo encuentra o false en caso contrario
	 */
	public function search($idValue){
		if (!empty($this->root)){
			$root= $this->root;
			$find= false;
			$result= false;
			$this->searchRecursive($root,$idValue,$find,$result);
			return $result;
		}else{
			return false;
		}
	}
	
	/**
	 * Funcion para la busqueda recursiva
	 */
	private function searchRecursive(Node & $node, $idValue, & $find, & $result){
		if ($find===false){
			if ($node->id==$idValue) {
				$find= true;
				$result= $node;
				return true;
			}else{
				foreach ($node->childs as $child){
					if ($find===false){
						$this->buscarRecursivo($child,$idValue,$find,$result);
					}
				}
			}
		}
		
	}
	
	
	/**
	 * funcion que devuelve un arreglo de Nodos que comparte el mismo padre del nodo cuyo id es pasado por parametro
	 * 
	 * @param variant $id :id del nodo que se quiere saber sus hermanos
	 * @param bool $sql :si lo hace mediante consultas SQL o mediante los datos cargados en root
	 * @return array of Node :arreglo de nodos
	 */
	public function getBrothers($idValue){
		$rst= false;
			$node= $this->search($idValue);
			if ($node===false){
				return false;
			}else{
				if (!is_null($node->parentNode)){
					foreach($node->parentNode->childs as $child){
						$id= $child->id;
						$parentId= $child->parentId;
						$info= $child->info;
						$rst[]=  new Nodo($id,$parentId,$info);
					}
				}else{
					$rst[]= $node;
				}
			}
			return $rst;
	}//end function 
	
	/**
	 * funcion que devuelve un arbol de los padres del nodo cuyo id es pasado por parametro
	 * 
	 * @param variant $id :id del nodo que se quiere saber el camino de los padres
	 * @return array :el arbol de los padres
	 */
	public function getParents($idValue){
		$node= $this->search($idValue);
		if ($node!==false){
			$result= array();
			$result[]= $this->toArray($node);
			while (!is_null($node->parentNode)) {
				$node= $node->parentNode;
				$result[]= $this->toArray($node);
			}
			return $result;
		}else{
			return false;
		}
	}
	
	/**
	 * funcion que devuelve un arreglo de nodos con los hijos del primer nivel del nodo que se pasa por parametro
	 * 
	 * @param variant $id :id del nodo que se quiere sus hijos
	 * @return Nodo :el nodo que pertenece al id y el arreglo de sus hijos
	 */
	public function getChildsArr($idValue){
		$node= $this->search($idValue);
		if ($node!== false && count($node->childs)>0){
			$result= array();
			foreach ($node->childs as $childNode){
				$result[]= $this->toArray($childNode);
			}
			return $result;
		}else{
			return false;
		}
	}
	
	/**
	 * Devuelve un arreglo con los datos del nodo
	 * 
	 * @param Node $nodo :nodo a convertir
	 * @return array :arreglo asociativo con los datos del nodo
	 */
	public function toArray(Node $node){
		$result= array();
		$result[$this->idFieldName]= $node->id;
		$result[$this->idParentFieldName]= $node->parentId;
		if (is_array($node->info) && count($node->info)>0){
			foreach ($node->info as $key=> $value){
				$result[$key]= $value; 
			}
		}
		return $result;
	}
	
	/**
	 * Obtener un arreglo de arreglos recursivo del arbol
	 *
	 * @param string $childFieldName
	 * @param string $itemsFieldsName
	 * @return array
	 */
	public function getTreeArr($childFieldName='menu', $itemsFieldsName='items'){
		$result=array();
		$node= $this->root;
		if (is_array($node->childs) && count($node->childs)>0){
			$result[$childFieldName]= array($itemsFieldsName=>array());
			$result= $this->getTreeArrRecursive($node, $result[$childFieldName][$itemsFieldsName], $childFieldName, $itemsFieldsName);
		}
		return $result;
	}
	
	/**
	 * Obtener un arreglo de arreglos recursivo del arbol
	 *
	 * @param Node $node
	 * @param array $rst
	 * @param string $childFieldName
	 * @param string $itemsFieldsName
	 * @return array
	 */
	private function getTreeArrRecursive(Node & $node, $rst, $childFieldName='menu', $itemsFieldsName='items'){
		$i=0;
		foreach ($node->childs as $child){
			$rst[$i]= $child->info;
			if (is_array($child->childs) && count($child->childs)>0){
				$rst[$i][$childFieldName]=array($itemsFieldsName=>array());
				$rst[$i][$childFieldName][$itemsFieldsName]= $this->getTreeArrRecursive($child,$rst[$i][$childFieldName][$itemsFieldsName], $childFieldName, $itemsFieldsName);
			}
			$i++;
		}
		return $rst;
	}
}


?>