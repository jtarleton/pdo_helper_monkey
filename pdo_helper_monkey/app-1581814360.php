<?php
/**
 * \Drupal\Core\Database
 */
abstract class JtDrupalDb extends Database
{
  public static function db_query($query, array $args = array(), array $options = array()) {
    if (empty($options['target'])) {
      $options['target'] = 'default';
    }

    return parent::getConnection($options['target'])->query($query, $args, $options);
  }

  public static function db_select($table, $alias = NULL, array $options = array()) {
    if (empty($options['target'])) {
      $options['target'] = 'default';
    }
    return parent::getConnection($options['target'])->select($table, $alias, $options);
  }

  /**
   * db_insert
   * @author James Tarleton
   * @param  string $table
   * @param  array  $options
   * @return InsertQuery Object
   */
  public static function db_insert($table, array $options = array()){

    if (empty($options['target']) || $options['target'] == 'slave') {
      $options['target'] = 'default';
    }
    return parent::getConnection($options['target'])->insert($table, $options);
  }
}

class Baseespresso extends JtDrupalDb {
	public function __construct() {
		$dsn = sprintf('mysql://%s:%s@%s/%s','root', 'root', 'localhost', 'mydb');

		$this->pdo = parent::getConnection($options['target'])->query($query, $args, $options);
	}
	public static function Connect() {
		$obj = new self;
		return $obj->pdo;
	}
}

class Espresso extends Baseespresso{
public $cid;
	public $ad_title;
		/**
	 * Constructor
	 */
    public function __construct($id, $data=null){
      if(!isset($data)) {
  	    //$data = lookup data by id
      }
      $this->load($data);
    }

    /**
     * Load data
     */
     public function load(array $data) {
foreach($data as $prop=>$value) {
			$this->$prop = $value;
		}
	     }

    public function getCId() {
        return $this->cid;
    }    public function getAdTitle() {
        return $this->ad_title;
    }    private function setCId($cid) {
        $this->cid = $cid;
    }    private function setAdTitle($ad_title) {
        $this->ad_title = $ad_title;
    }

public function getTableHeading() {
		$html = '%s';
		$closure = function($fields){
			foreach ($fields as $k => &$value)
				$fields[$k]=ucwords(str_replace('_',' ',$value));
			return $fields;
		};

		return sprintf($html, implode('</th><th>',
				call_user_func($closure, array_keys(get_object_vars($this)))
			)
		);
	}

}

class EspressoTable extends Baseespresso{
    public static function get($id, $data=null) {
		return new Espresso($id, $data);
    }


  public static function getCount(){
   	$pdo = parent::Connect();
   	$stmt=$pdo->prepare("SELECT count(*) as ct
		FROM Espresso");
	$stmt->execute();
	$row = $stmt->fetch(PDO::FETCH_ASSOC);
	return intval($row['ct']);
    }


    public static function getAll($offset=0,$limit=10) {


$pdo = parent::Connect();

	$stmt=$pdo->prepare("SELECT * FROM Espresso  LIMIT $offset,$limit;");
	
	//$stmt->bindValue(':cid', 'cid', PDO::PARAM_STR);
	
	//$stmt->bindValue(':ad_title', 'ad_title', PDO::PARAM_STR);
		$stmt->execute();
	$objs = array();
	while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
		$obj = self::get(null,$row);
		$objs[] = $obj;
	}








    	return $objs;
	}







public static function Create() {


$pdo = parent::Connect();

	$stmt=$pdo->prepare('INSERT INTO Espresso (cid,ad_title) VALUES(:cid,:ad_title)');


		$stmt->bindValue(':cid', 'cid', PDO::PARAM_STR);
		$stmt->bindValue(':ad_title', 'ad_title', PDO::PARAM_STR);
		$stmt->execute();
    	return $stmt->rowCount();
	}


public static function Update(array $changes) {

$fields = explode(',', $in['csv_fields']);

$missing = array();
foreach($fields as $v){
	if(!isset($changes[$v])){
		$missing[] =  $v;
	}
}

if(count($missing))
	throw new Exception('Please provide a non-null value for the following fields: ' . implode(',',$missing) );

$pdo = parent::Connect();

	$stmt=$pdo->prepare('UPDATE Espresso SET cid=:cid,ad_title=:ad_title');
		$stmt->bindValue(':cid', $changes['cid'], PDO::PARAM_STR);
		$stmt->bindValue(':ad_title', $changes['ad_title'], PDO::PARAM_STR);
		$stmt->execute();
    return $stmt->rowCount();
  }
}
