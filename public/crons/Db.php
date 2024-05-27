<?php

/**
 * Clase de conexión a la base de datos
 */
class Db extends PDO
{
	const FILE_CONFIG_DATABASE = __DIR__ . '/../../config/database.json';
	
    private $host;
    private $user;
    private $password;
    private $dbname;
    private $port;
    private $dsn;

    public static $instance = null;

    public static function getInstance()
    {
        if (self::$instance == null) {
            return new self();
        }

        return self::$instance;
    }

    public function __construct()
    {
        $credentials = json_decode(file_get_contents(self::FILE_CONFIG_DATABASE), true);
        
		// Los de prod u otro...
		$this->host = $credentials['host'];
		$this->user = $credentials['user'];
		$this->password = $credentials['password'];
		$this->dbname = $credentials['dbname'];
        

        $this->port = '3306';
        $this->dsn = "mysql:host=$this->host;dbname=$this->dbname;port=$this->port";

        self::$instance = parent::__construct($this->dsn, $this->user, $this->password);
        return self::$instance;
    }
}