<?php

/**
 * Description of Update.class
 * Classe responsável por deletar genéricamente no banco de dados
 * @copyright (c) 2016, Pedro Henrique CodeBR<>
 */
class Delete extends Conn {

    private $tabela;
    private $termos;
    private $places;
    private $resultado;

    /**
     * @var PDOStatements 
     */
    private $Delete;

    /** @var PDO */
    private $Conn;

    /**
     * 
     * @param STRING $Tabela adiciona os valores na tabela informada ex: 'nome da tupla'=> 'resultado'; 
     */
    public function ExeDelete($Tabela, $termos, $ParseString) {
        $this->tabela = (string) $Tabela;
        $this->termos = (string) $termos;

        parse_str($ParseString, $this->places);
        $this->getSyntax();
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
        return $this->Delete->rowCount();
    }

    public function setPlaces($ParseString) {
        parse_str($ParseString, $this->places);
        $this->getSyntax();
        $this->Execute();
    }

    /**
     * ****************************************
     * *********** PRIVATE METODOS ************
     * ****************************************
     */
    /*     * @function Connect Obtém o PDO e Prepara a query */
    private function Connect() {
        $this->Conn = parent::getConn();
        $this->Delete = $this->Conn->prepare($this->Delete);
    }

//Cria a sintaxe da query para Prepared Statements
    private function getSyntax() {

        $this->Delete = "DELETE FROM {$this->tabela} {$this->termos}";
    }

    private function Execute() {
        $this->Connect();
        try {
            $this->Delete->execute($this->places);
            $this->resultado = true;
        } catch (PDOException $e) {
            $this->resultado = null;
            WSErro("<b>Erro ao Deletar:</b> {$e->getMessage()}",$e->getCode());
        }
    }

}
