<?php
function klean($txt) {
  return str_replace("\n",'', $txt);
}

function doSomething(array $in){

foreach ($in as $key => &$value) {
  $in[$key] = klean($value);
}

foreach(explode(',', $in['csv_fields']) as $field) { $l=strlen($field);
$lengths[$l] = (int)$l;
}
$maxlength = max($lengths) + 18;
?>

<?php ob_start(); echo ''; echo ''; echo 'hp '."\n"; ?>
class Database {
  public static function PDOCreate( $uri, $cache = true )
  {
        if ($cache) {
            if (isset(self::$pdo_objects[$uri])) {
                self::$pdo_objects[$uri]['count']++;
                return self::$pdo_objects[$uri]['dbh'];
            }

            self::$pdo_objects[$uri]['count'] = 1;
        }

        $args = parse_url($uri);

        $dbtype = $args['scheme'];
        $server = $args['host'];
        $port = null;
        $user = $args['user'];
        $pass = $args['pass'];
        $database = $args['path'];

        while ($database[0] == '/')
          $database = substr($database,1);
          $db = null;

          /*
           * Here we will format the DSN string to match
           * how the PDO driver expects the data
           */

        $dsn = '';

        switch (strtolower($dbtype)) {
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


        if ($cache)
            self::$pdo_objects[$uri]['dbh'] = $db;

      $db->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );


      /*
      * Execute post-creation SQL
      */
      switch (strtolower($dbtype)) {
           case 'mysql':
            $db->prepare("set names 'UTF8'")->execute();
            break;
      }


        return $db;
    }
}





class Baseobject extends Database {
  public function __construct() {
    $dsn = sprintf('mysql://%s:%s@%s/%s','<?php echo $in['dbuser']; ?>', '<?php echo $in['dbpass']; ?>', 'localhost', '<?php echo $in['dbname']; ?>');

    $this->pdo = parent::PDOCreate($dsn , false);
  }
  public static function Connect() {
    $obj = new self;
    return $obj->pdo;
  }
}








class <?php echo $in['classname']; $par=null; if(isset($in['parent'])){ $par = ucfirst(strtolower($in['parent'])); echo " extends $par"; } ?>
{
<?php $field=null; foreach(explode(',', $in['csv_fields']) as $field) {
  if(isset($field)){ ?>public $<?php echo $field; ?>;
  <?php
} } ?>
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
<?php //foreach(explode(',', $in['csv_fields']) as $field) {
  ?>foreach($data as $prop=>$value) {
      $this->$prop = $value;
    }
  <?php //if(isset($field)) echo str_pad("        \$this->{$field} ", $maxlength); echo "= \$data['$field'];\n"; } ?>
     }

<?php foreach(explode(',', $in['csv_fields']) as $field) {
  if(isset($field) && $field === 'id') $getter = 'get'.str_replace('id','Id',str_replace(' ','',ucwords(str_replace('_', ' ', $field))));
  else 
    $getter = 'get'.str_replace('id','id',str_replace(' ','',ucwords(str_replace('_', ' ', $field))));
  echo "    public function {$getter}() {
        return \$this->$field;
    }";
} ?>
<?php foreach(explode(',', $in['csv_fields']) as $field) {
  if(isset($field)) $setter = 'set'.str_replace('id','Id',str_replace(' ','',ucwords(str_replace('_', ' ', $field))));
  echo "    private function {$setter}(\${$field}) {
        \$this->$field = \${$field};
    }";
} ?>


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

class <?php echo $in['classname']; ?>Table<?php if(isset($par)) echo " extends $par"; ?>
{
    public static function get($id, $data=null) {
    return new <?php echo $in['classname']; ?>($id, $data);
    }


  public static function getCount(){
     $pdo = parent::Connect();
     $stmt=$pdo->prepare("SELECT count(*) as ct
    FROM <?php echo $in['classname']; ?>");
  $stmt->execute();
  $row = $stmt->fetch(PDO::FETCH_ASSOC);
  return intval($row['ct']);
    }


    public static function getAll($offset=0,$limit=10) {


$pdo = parent::Connect();

  $stmt=$pdo->prepare("SELECT * FROM <?php echo $in['classname']; ?>  LIMIT $offset,$limit;");
  <?php foreach(explode(',', $in['csv_fields']) as $field) { ?>

  //$stmt->bindValue(':<?php echo $field; ?>', '<?php echo $field; ?>', PDO::PARAM_STR);
  <?php } ?>
  $stmt->execute();
  
   $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
      return $rows;
  }







public static function Create() {


$pdo = parent::Connect();

  $stmt=$pdo->prepare('INSERT INTO <?php echo $in['classname']; ?> (<?php

  echo $in['csv_fields']; $markers =explode(',', $in['csv_fields']);

  foreach($markers as &$marker) { $marker = ':'.$marker; }

  ?>) VALUES(<?php

  echo implode(',',$markers);

  ?>)');


  <?php foreach(explode(',', $in['csv_fields']) as $field) { ?>
  $stmt->bindValue(':<?php echo $field; ?>', $row['<?php echo $field; ?>'], PDO::PARAM_STR);
  <?php } ?>
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

  $stmt=$pdo->prepare('UPDATE <?php echo $in['classname']; ?> SET <?php $sqlpieces=array(); foreach( explode(',', $in['csv_fields']) as $fld) $sqlpieces[]= "$fld=:$fld"; echo implode(',', $sqlpieces) ?>');
  <?php foreach(explode(',', $in['csv_fields']) as $field) { ?>
  $stmt->bindValue(':<?php echo $field; ?>', $changes['<?php echo $field; ?>'], PDO::PARAM_STR);
  <?php }?>
  $stmt->execute();
    return $stmt->rowCount();
  }
}
<?php } ?>
