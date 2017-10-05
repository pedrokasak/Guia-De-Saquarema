<?php

/**
 * Login [Model]
 * Responsável por autenticar validar e checar usuários do sistema de login!
 * @copyright (c) 2016, Pedro Henrique SaquaTech<>
 */
class Login {

    private $Level;
    private $Email;
    private $Senha;
    private $Error;
    private $Result;

    function __construct($Level) {
        $this->Level = (int) $Level;
    }

    public function exeLogin(array $userData) {
        $this->Email = (string) strip_tags(trim($userData['user']));
        $this->Senha = (string) strip_tags(trim($userData['pass']));
        $this->setLogin();
    }

    function getError() {
        return $this->Error;
    }

    function getResult() {
        return $this->Result;
    }

    /**
     * <b>Checar Login:</b> execute esses métodos para verificar o email e senha são válidos.
     */
    public function checkLogin() {
        if (empty($_SESSION['userlogin']) || $_SESSION['userlogin']['user_level'] < $this->Level):
            unset($_SESSION['userlogin']);
            return false;
        else:
            return true;
        endif;
    }

    /*
     * ***************************************
     * **********  PRIVATE METHODS  **********
     * ***************************************
     */

    private function setLogin() {
        if (!$this->Email || !$this->Senha || !Check::Email($this->Email)):
            $this->Error = ['Informe seu email e senha para efetuar o login', WS_INFOR];
            $this->Result = false;
        elseif (!$this->getUser()):
            $this->Error = ['Os dados informados não são compatíveis!', WS_ALERT];
            $this->Result = false;
        elseif ($this->Result['user_level'] < $this->Level):
            $this->Error = ["Desculpe {$this->Result['user_name']}, você não tem permissão para acessar essa área", WS_ERROR];
            $this->Result = false;
        else:
            $this->Execute();
        endif;
    }

    private function getUser() {
        $this->Senha = md5($this->Senha);

        $read = new Read;
        $read->ExeRead("ws_users", "WHERE user_email = :email AND user_password = :pass", "email={$this->Email}&pass={$this->Senha}");
        if ($read->getResult()):
            $this->Result = $read->getResult()[0];
            return true;
        else:
            return false;
        endif;
    }

    private function Execute() {
        if (!session_id()):
            session_start();
        endif;

        $_SESSION['userlogin'] = $this->Result;
        $this->Error = ["Olá {$this->Result['user_name']}, seja bem vindo(a). Aguarde redirecionamento!", WS_ACCEPT];
        $this->Result = TRUE;
    }

}
