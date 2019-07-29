<?php
/**
 * 1. Соединение с БД через PDO (lazy load)
 * 2. Иметь объект подключения везде в любой точке проекта
 * 3. execute, select (fetchOne, fetchAll)
 * 4. профилирование (время запроса, запрос, affected_rows)
 * 5. уметь работать с несколькими базами
 */

class DB
{
    /** @var \PDO */
    private $_pdo;
    /**
     * @var array
     * [
     *  [0 => query_time, 1 => dbname, 2 => eventText, affectedRows, method]
     * ]
     */
    private $_log = [];
    private static $_instance;
    private function __construct()
    {
    }
    private function __clone()
    {
    }
    public static function instance()
    {
        if (!self::$_instance) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    private function getConnection()
    {
        if (!isset($this->_pdo)) {
            $dbHost = DB_HOST;
            $dbUser = DB_USER;
            $dbName = DB_NAME;
            $dbPassword = DB_PASSWORD;
            $t = microtime(1);
            $this->_pdo = new \PDO("mysql:host=$dbHost;dbname=$dbName", $dbUser, $dbPassword);
            $this->_log[] = [
                microtime(1) - $t,
                'connect',
                0,
                ''
            ];
        }
        return $this->_pdo;
    }
    public function exec(string $query, string $_method , array $params = []): int
    {
        $pdo = $this->getConnection();
        $prepared = $pdo->prepare($query);
        $t = microtime(1);
        $ret = $prepared->execute($params);
        if (!$ret) {
            $errorInfo = $prepared->errorInfo();
            trigger_error("{$errorInfo[0]}#{$errorInfo[1]}: " . $errorInfo[2]);
            return -1;
        }
        $affectedRows = $prepared->rowCount();
        $this->_log[] = [
            microtime(1) - $t,
            $this->getClearQuery($prepared, $params),
            $affectedRows,
            $_method
        ];
        return $affectedRows;
    }
    public function fetchAll(string $query, string $_method , array $params = []): array
    {
        $pdo = $this->getConnection();
        $prepared = $pdo->prepare($query);
        $t = microtime(1);
        $ret = $prepared->execute($params);
        if (!$ret) {
            $errorInfo = $prepared->errorInfo();
            trigger_error("{$errorInfo[0]}#{$errorInfo[1]}: " . $errorInfo[2]);
            return [];
        }
        $data = $prepared->fetchAll(\PDO::FETCH_ASSOC);
        $this->_log[] = [
            microtime(1) - $t,
            $this->getClearQuery($prepared, $params),
            $prepared->rowCount(),
            $_method
        ];
        return $data;
    }
    public function fetchOne(string $query, string $_method , array $params = []): array
    {
        $data = $this->fetchAll($query, $_method, $params);
        return $data ? reset($data) : [];
    }
    public function getLog(bool $asHtml = true)
    {
        if ($asHtml) {
            $html = '<br><br><hr><br>';
            if ($this->_log) {
                foreach ($this->_log as $item) {
                    list($queryTime, $text, $affectedRows, $method) = $item;
                    $html .= round($queryTime, 4) . ': ' . $text . " [$affectedRows] | $method<br>";
                }
            }
            return $html;
        } else {
            return $this->_log;
        }
    }
    private function getClearQuery(\PDOStatement $prepared, $params = [])
    {
        $query = $prepared->queryString;
        if ($params) {
            foreach ($params as $param => $value) {
                $query = str_replace($param, $value, $query);
            }
        }
        return $query;
    }

    public function getLastInsertId()
    {
        return $this->getConnection()->lastInsertId();
    }
}