<?php

class Database
{
 public static function PDOCreate( $uri, $cache = true )
    {

        if( $cache )
        {
            if( isset(self::$pdo_objects[$uri]) )
            {
                self::$pdo_objects[$uri]['count']++;
                return self::$pdo_objects[$uri]['dbh'];
            }

            self::$pdo_objects[$uri]['count'] = 1;
        }



        $args = parse_url($uri);

        $dbtype   = $args['scheme'];
        $server   = $args['host'];
    $port    = null;
        $user       = $args['user'];
        $pass       = $args['pass'];
        $database   = $args['path'];

        while( $database[0] == '/' )
            $database = substr($database,1);


        $db = null;

        /*
        * Here we will format the DSN string to match
        * how the PDO driver expects the data
        */

        $dsn = '';

        switch( strtolower($dbtype) )
        {
            case 'mysql':

              $dsn = 'mysql:host='.$server;

              //if( ! empty($port) )
                $dsn .= ';port=3307'; // .$port;

              if( ! empty($database) )
                $dsn .= ';dbname='.$database;

              $dsn .= ';charset=utf8';

                break;
      default:
        throw new Exception('Unknown database type: '.$uri);
        break;
        }



        //echo "\nCreating PDO with DSN: $dsn \n\n";

         $db = new PDO( $dsn, $user, $pass );


        if( $cache )
            self::$pdo_objects[$uri]['dbh'] = $db;


      $db->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );


      /*
      * Execute post-creation SQL
      */


      switch( strtolower($dbtype) )
        {
            case 'mysql':
            $db->prepare("set names 'UTF8'")->execute();
            break;
      }


        return $db;
    }
}





class Baseobject extends Database {
  public function __construct() {
    $dsn = sprintf('mysql://%s:%s@%s/%s','root', 'root', 'localhost', 'mydb');

    $this->pdo = parent::PDOCreate($dsn , false);
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
