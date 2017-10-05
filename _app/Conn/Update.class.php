<?php

/**
 * Description of Update.class
 * Classe responsável pela atualização genéricas no banco de dados
 * @copyright (c) 2016, Pedro Henrique CodeBR<>
 */
class Update extends Conn {

    private $tabela;
    private $dados;
    private $termos;
    
    private $places;
    private $resultado;

    /**
     * @var PDOStatements 
     */
    private $Update;

    /** @var PDO */
    private $Conn;

    /**
     * 
     * @param STRING $Tabela adiciona os valores na tabela informada ex: 'nome da tupla'=> 'resultado'; 
     */
    public function ExeUpdate($Tabela, array $dados,$termos,$ParseString) {
        $this->tabela = (string) $Tabela;
        $this->dados = $dados;
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
        return $this->Update->rowCount();
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
    
    
    /**@function Connect Obtém o PDO e Prepara a query */
    private function Connect() {
        $this->Conn = parent::getConn();
        $this->Update = $this->Conn->prepare($this->Update);
        
    }

//Cria a sintaxe da query para Prepared Statements
    private function getSyntax() {

            foreach ($this->dados as $key => $valor):
               $places[] = $key .' = :'. $key;
            endforeach;
            
            $places = implode(', ', $places);
            $this->Update = "UPDATE {$this->tabela} SET {$places} {$this->termos}";
    }

    private function Execute() {
        $this->Connect();
        try {
            $this->Update->execute(array_merge($this->dados, $this->places));
            $this->resultado  = true;
        } catch (PDOException $e) {
            $this->resultado = null;
            WSErro("<b>Erro ao ler : </b>{$e->getMessage()}", $e->getCode());
        }
    }

}
