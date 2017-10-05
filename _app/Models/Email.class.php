<?php
require('_app/Library/PHPMailer/class.phpmailer.php');
require ('_app/Library/PHPMailer/class.smtp.php');

/**
 * Email [MODELS]
 * Modelo responsável por configurar a PHPMailer, Validar os dados e disparar e-mails do sistema!
 * @copyright (c) 2016, Pedro Henrique CodeBR<>
 */
class Email {

    /** @var PHPMailer */
    private $email;

    /** Email DATA * */
    private $data;

    /** Corpo do Email * */
    private $assunto;
    private $mensagem;

    /** Remetente * */
    private $remetenteNome;
    private $remetenteEmail;

    /** Destino * */
    private $destinoNome;
    private $destinoEmail;

    /** CONTROLE* */
    private $error;
    private $result;

    function __construct() {
        $this->email = new PHPMailer;
        $this->email->Host = MAILHOST;
        $this->email->Port = MAILPORT;
        $this->email->Username = MAILUSER;
        $this->email->Password = MAILPASS;
        $this->email->CharSet = 'UTF-8';
    }

    public function Enviar(array $data) {
        $this->data = $data;
        $this->Clear();

        if (in_array('', $this->data)):
            $this->error = ['<b>Erro ao enviar a mensagem:</b> Para enviar essee-mail preencha os campos requisitados!', WS_INFOR];
            $this->result = false;
        elseif (!Check::Email($this->data['remetenteEmail'])):
            $this->error = ['<b>Erro ao enviar a mensagem:</b> O e-mail que você informou não tem um formato válido. Informe seu e-mail', WS_ERROR];
            $this->result = false;
        else:
            $this->setMail();
            $this->setConfig();
            $this->sendMail();
        endif;
    }
   
     function getResult() {
        return $this->result;
    }
    
     function getError() {
        return $this->error;
    }
    
    /*
     * ***************************************
     * **********  PRIVATE METHODS  **********
     * ***************************************
     */
    
    private function Clear() {
        array_map('strip_tags', $this->data);
        array_map('trim', $this->data);
    }

    private function setMail() {
        $this->assunto = $this->data['Assunto'];
        $this->mensagem = $this->data['Mensagem'];
        $this->remetenteNome = $this->data['remetenteNome'];
        $this->remetenteEmail = $this->data['remetenteEmail'];
        $this->destinoNome = $this->data['destinoNome'];
        $this->destinoEmail = $this->data['destinoEmail'];
        $this->data = null;
        $this->setMsg();
    }

    private function setMsg() {
        $this->mensagem = "{$this->mensagem}<hr><small>Recebida em : " . date('d/m/Y H:i') . ""
                . " De {$this->remetenteNome} : {$this->remetenteEmail}";
//                . ".Assunto{$this->assunto}</small>";
    }
    
    private function setConfig() {
        //SMTP AUTH
        $this->email->isSMTP();
        $this->email->SMTPAuth = TRUE;
        $this->email->isHTML();
        $this->email->SMTPSecure = 'tls';
        
        //REMETENTE E RETORNO
        $this->email->From = MAILUSER;
        $this->email->FromName = $this->remetenteNome;
        $this->email->addReplyTo($this->remetenteEmail, $this->remetenteNome);
        
        //ASSUNTO, MENSAGEM E DESTINO
        $this->email->Subject = $this->assunto;
        $this->email->Body = $this->mensagem;
        $this->email->addAddress($this->destinoEmail, $this->destinoNome);
    }
    
    private function sendMail() {
        if($this->email->send()):
            $this->error = ['<b>Obrigado por Entrar em Contato:</b> Recebemos sua mensagem e estaremos respondendo em breve!', WS_ACCEPT];
            $this->result = true;
        else:
            $this->error = ["<b>Erro ao Enviar:</b> Entre em contao com Administrador.({$this->email->ErrorInfo})", WS_ERROR];
            $this->result = false;
        endif;
    }

}
