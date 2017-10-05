<?php


/**
 * Description of Conn [conexao]
 * CLASSE ABSTRATA DE SINGLETON PADRÃO CONEXÃO
 * RETORNA UM OBJETO PDO POR UM METÓDO ESTÁTICO getConn()
 * @copyright (c) 2016, Pedro Henrique CodeBR<>
 */
class Conn {
    
    private static $host = HOST;
    private static $user = USER;
    private static $pass = PASS;
    private static $dbsa = DBSA;
    
    /**
     * @var PDO Description
     */
    
    private static $connect = null;
    
    private static function Conectar() {
        try {
            if(self::$connect == NULL):
                
                $dsn = 'mysql:host=' .self::$host. ';dbname='.self::$dbsa;
                $options = [PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES UTF8'];
                self::$connect = new PDO($dsn, self::$user, self::$pass, $options);
            endif;
        } catch (PDOException $e) {
            PHPErro($e->getCode(), $e->getMessage(), $e->getFile(), $e->getLine());
            die;
        }
        
        self::$connect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return self::$connect;
    }
    /**
     * 
     * Retorna um objeto PDO Singleton
     */
    public static function getConn() {
        return self::Conectar();
    }
}
