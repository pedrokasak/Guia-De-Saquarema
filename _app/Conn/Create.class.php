<?php

/**
 * Description of Create.class
 * Classe responsável por cadastros genéricos no banco de dados
 * @copyright (c) 2016, Pedro Henrique CodeBR<>
 */
class Create extends Conn {

    private $tabela;
    private $Dados;
    private $resultado;

    /**
     * @var PDOStatements 
     */
    private $Create;

    /** @var PDO */
    private $Conn;

    /**
     * 
     * @param STRING $Tabela adiciona os valores na tabela informada ex: 'nome da tupla'=> 'resultado'; 
     * @param ARRAY $Dados adiciona o resultado da tupla inserida na tabela;
     */
    public function ExeCreate($Tabela, array $Dados) {
        $this->tabela = (string) $Tabela;
        $this->Dados = $Dados;

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

    /**
     * ****************************************
     * *********** PRIVATE METODOS ************
     * ****************************************
     */
    //Obtém o PDO e Prepara a query
    private function Connect() {
        $this->Conn = parent::getConn();
        $this->Create = $this->Conn->prepare($this->Create);
    }

//Cria a sintaxe da query para Prepared Statements
    private function getSyntax() {
        $Fileds = implode(', ', array_keys($this->Dados));
        $Places = ':' . implode(', :', array_keys($this->Dados));
        $this->Create = "INSERT INTO {$this->tabela} ({$Fileds}) VALUES ({$Places})";
    }

    private function Execute() {
        $this->Connect();
        try {
            $this->Create->execute($this->Dados);
            $this->resultado = $this->Conn->lastInsertId();
        } catch (PDOException $e) {
            $this->resultado = null;
            WSErro("<b>Erro ao cadastrar : </b>{$e->getMessage()}", $e->getCode());
        }
    }

}
