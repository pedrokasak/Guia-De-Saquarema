<?php

/**
 * Description of Create.class
 * Classe responsável pela leitura genéricas no banco de dados
 * @copyright (c) 2016, Pedro Henrique CodeBR<>
 */
class Read extends Conn {

    private $select;
    private $places;
    private $resultado;

    /**
     * @var PDOStatements 
     */
    private $Read;

    /** @var PDO */
    private $Conn;

    /**
     * 
     * @param STRING $Tabela adiciona os valores na tabela informada ex: 'nome da tupla'=> 'resultado'; 
     */
    public function ExeRead($Tabela, $Termos = null, $ParseString = NULL) {
        if (!empty($ParseString)):
            parse_str($ParseString, $this->places);
        endif;

        $this->select = "SELECT * FROM {$Tabela} {$Termos}";
        $this->Execute();
    }

    /**
     * <b>Obter resultado:</b> Retorna o ID do registro inserido ou FALSE caso nem um registro seja inserido! 
     * @return INT $Variavel = lastInsertId OR FALSE
     */
    public function getResult() {
        return $this->resultado;
    }
    
    public function getRowCount() {
        return $this->Read->rowCount();
    }
    
    public function FullRead($Query,$ParseString = NULL) {
        $this->select = (string) $Query;
        if (!empty($ParseString)):
            parse_str($ParseString, $this->places);
        endif;
        $this->Execute();
    }
    
    public function setPlaces($ParseString) {
        parse_str($ParseString, $this->places);
        $this->Execute();
    }

    /**
     * ****************************************
     * *********** PRIVATE METODOS ************
     * ****************************************
     */
    //Obtém o PDO e Prepara a query
    private function Connect() {
        $this->Conn = parent::getConn();
        $this->Read = $this->Conn->prepare($this->select);
        $this->Read->setFetchMode(PDO::FETCH_ASSOC);
    }

//Cria a sintaxe da query para Prepared Statements
    private function getSyntax() {
        if ($this->places):
            foreach ($this->places as $vinculo => $valor):
                if ($vinculo == 'limit' || $vinculo == 'offset'):
                    $valor = (int) $valor;
                endif;
                $this->Read->bindValue(":{$vinculo}", $valor, (is_int($valor) ? PDO::PARAM_INT : PDO::PARAM_STR));
            endforeach;
        endif;
    }

    private function Execute() {
        $this->Connect();
        try {
            $this->getSyntax();
            $this->Read->execute();
            $this->resultado = $this->Read->fetchAll();
        } catch (PDOException $e) {
            $this->resultado = null;
            WSErro("<b>Erro ao ler : </b>{$e->getMessage()}", $e->getCode());
        }
    }

}
