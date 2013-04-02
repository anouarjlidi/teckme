 we<?php
/**
 * @Author JLIDI ANOUAR.
 * User: ANOUARHAK
 * Date: 10/02/13
 * Time: 12:25
 * Version 1.0.0 beta
 */

class DB
{
    protected $server;
    protected $user;
    protected $dbname;
    protected $dbpwd;
    protected  $dbcon;
    private $error;
    public static $instance = null;

    /*
      Constructor Database connection
      return void
     */

    public function __construct($mysql_dbserver, $mysql_dbuser, $mysql_dbname, $mysql_dbpwd)
    {
       $this->server = $mysql_dbserver;
       $this->user = $mysql_dbuser;
       $this->dbname = $mysql_dbname;
       $this->dbpwd = $mysql_dbpwd;
       $this->connectDB();
     //  $this->selectDB($mysql_dbname);
    }
    /*
     * Pattern singleton
     * Instance for DATABASE CLASS
     */
    public static function getInstance($server, $user, $dbname, $dbpwd)
    {
        if(!isset(self::$instance))
        {
             self::$instance = new DB($server, $user, $dbname, $dbpwd);
        }
        return self::$instance;
    }
    /*
     * Connect to Mysql database
     * PDO
     */
    public function connectDB()
    {
       /* $this->dbcon = @mysql_connect($this->server, $this->user, $this->dbpwd);

        if(!$this->dbcon)
        {

            throw new RuntimeException(
                __METHOD__ . ' Could not connect to Database ERROR'. mysql_error()
            );
        } */
        try
        {
          $this->dbcon = new PDO('mysql:host='.$this->server.
              ';dbname='.$this->dbname,
              $this->user,
              $this->dbpwd,
               array(
                   PDO::ATTR_PERSISTENT => true
               ));
        }
        catch(PDOException $ex)
        {
          echo 'Connection failed' . $ex->getMessage();
            $host  = $_SERVER['HTTP_HOST'];
            $uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
            $extra = 'error_page/errorDB.html.php';
            header("Location: http://$host$uri/$extra");
            exit;

        }
    }

    /*
     * INSERT DATA TO DATABASE
     * Return Mixed
     */

    public function InsertQ($table, $info)
    {
        $this->table = $table;
        // Test If $info is array or Not
        if (!is_array($info) || !count($info))
        {
            $info = array($info);
        }
        $bind = ':'.implode(',:', array_keys($info));
        // SQL QUERY
        $sql  = 'INSERT INTO '.$table.'('.implode(',', array_keys($info)).') '.
            'values ('.$bind.')';
        try{
         //PREPARE SQL STATEMENT
        $st = $this->dbcon->prepare($sql);
         //EXECUTE SQL STATEMENT
        $st->execute(array_combine(explode(',',$bind), array_values($info)));
        }
        catch(PDOException $ex)
        {
            $this->error = $ex->getMessage();
            return FALSE;
        }
       return $this->dbcon->lastInsertId();
    }

    /*
     *Function Delete Query From DATABASE
     *return boolean
     */

    public function Delete($table, $info)
    {
        $this->table = $table;

        // Test If $info is array or Not
        if (!is_array($info) || !count($info))
        {
            $info = array($info);
        }

        $bind = ':' .implode(',:', array_keys($info));
        //SQL QUERY
        $sql  = 'DELETE FROM '.$table.' WHERE '.implode(',', array_keys($info)).
            ' = '.$bind;
        // TRY TO DELETE QUERY
        try
        {
            // PREPARE SQL STATEMENT
            $st = $this->dbcon->prepare($sql);
            // EXECUTE SQL STATEMENT
            $st->execute(array_combine(explode(',',$bind), array_values($info)));

        }
        catch(PDOException $ex)
        {
            $this->error = $ex->getMessage();
            return FALSE;
        }

        return TRUE;
    }



    /*
     *
     */
    public function SelectRows()
    {

     /*  if (!is_array($info) || !count($info))
       {
           $info = array($info);
       }*/

         $sql = 'SELECT * FROM tckm_users';
        try
        {
            $st = $this->dbcon->prepare($sql);
            $st->execute();

        }
        catch(PDOException $ex)
        {
            $this->error = $ex->getMessage();
            return FALSE;
        }

        $result = $st->fetchAll(PDO::FETCH_ASSOC);
        if(!$result)
        {
            return FALSE;

        }
    return $result;
    }
    public function SelectOneRow($table, $info)
    {

        /*  if (!is_array($info) || !count($info))
          {
              $info = array($info);
          }*/

        $sql = 'SELECT * FROM tckm_users';
        try
        {
            $st = $this->dbcon->prepare($sql);
            $st->execute();

        }
        catch(PDOException $ex)
        {
            $this->error = $ex->getMessage();
            return FALSE;
        }

        $result = $st->fetchAll(PDO::FETCH_ASSOC);
        if(!$result)
        {
            return FALSE;

        }
        $this->table = $table;

        // Test If $info is array or Not
        if (!is_array($info) || !count($info))
        {
            $info = array($info);
        }

        $bind = ':' .implode(',:', array_keys($info));
        //SQL QUERY
        $sql  = 'DELETE FROM '.$table.' WHERE '.implode(',', array_keys($info)).
            ' = '.$bind;
        // TRY TO DELETE QUERY
        try
        {
            // PREPARE SQL STATEMENT
            $st = $this->dbcon->prepare($sql);
            // EXECUTE SQL STATEMENT
            $st->execute(array_combine(explode(',',$bind), array_values($info)));

        }
        catch(PDOException $ex)
        {
            $this->error = $ex->getMessage();
            return FALSE;
        }
        return $result;
    }


}
