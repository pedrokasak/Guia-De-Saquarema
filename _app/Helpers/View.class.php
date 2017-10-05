<?php


/**
 * View.class [HELPER MVC]
 * RESPONSAVEL POR CARREGAR A TEMPLATE, POVOAR E INCLUIR ARQUIVOS PHP NO SISTEMA.
 * ARQUITETURAMVC!
 *  
 * @copyright (c) 2016, Pedro Henrique CodeBR<>
 */
class View {
    
    private $Data;
    private $Keys;
    private $Values;
    private $Template;
    
    public function Load($Template) {
        $this->Template = REQUIRE_PATH . DIRECTORY_SEPARATOR . '_tpl' . DIRECTORY_SEPARATOR . (string) $Template;
        $this->Template = file_get_contents($this->Template . '.tpl.html');
        return $this->Template;
    }
    
    public function Show(array $Data,$View) {
        $this->setKeys($Data);
        $this->setValues();
        $this->showView($View);
    }
    
    public function Request($File, array $Data) {
        extract($Data);
        require("{$File}.inc.php");
        
    }
    
    /*
     * ***************************************
     * **********  PRIVATE METHODS  **********
     * ***************************************
     */
    
    private function setKeys($Data) {
        $this->Data = $Data;
        $this->Data['HOME'] = HOME;
        
        $this->Keys = explode('&','#' . implode("#&#", array_keys($this->Data)) . '#');
        $this->Keys[] = '#HOME#';
    }
    
    private function setValues() {
        $this->Values = array_values($this->Data);
    }
    
    private function showView($View) {
        $this->Template = $View;
        echo str_replace($this->Keys, $this->Values, $this->Template);
    }
    
}
