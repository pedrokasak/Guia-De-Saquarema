<?php
ob_start();
session_start();
require('../_app/Config.inc.php');
?>
<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <title>Site Admin</title>

        <link href='http://fonts.googleapis.com/css?family=Open+Sans:400,600,800' rel='stylesheet' type='text/css'>
        <link rel="stylesheet" href="css/reset.css" />
        <link rel="stylesheet" href="css/admin.css" />

    </head>
    <body class="login">

        <div id="login">
            <div class="boxin">
                <h1>Administrar Site</h1>

                <?php
                $login = new Login(3);

                if ($login->checkLogin()):
                    header('Location: painel.php');
                endif;

                $dataLogin = filter_input_array(INPUT_POST, FILTER_DEFAULT);
                if (!empty($dataLogin['adminLogin'])):

                    #echo md5($dataLogin['pass']);

                    $login->exeLogin($dataLogin);
                    if (!$login->getResult()):
                        WSErro($login->getError()[0], $login->getError()[1]);
                    else:
                        header('Location: painel.php');
                    endif;
                endif;

                $get = filter_input(INPUT_GET, 'exe', FILTER_DEFAULT);
                if (!empty($get)):
                    if ($get == 'restrito'):
                        WSErro("Oppsss: <b>Acesso Negado!</b> Favor efetue o Login para acessar o painel", WS_ALERT);
                    elseif ($get == 'logoff'):
                        WSErro("Deslogado com Sucesso! :P <b>sua sessão foi finalizada</b>", WS_ACCEPT);
                    endif;
                endif;
                ?>

                <form name="adminLoginForm" action="" method="post">
                    <label>
                        <span>E-mail:</span>
                        <input type="email" name="user" />
                    </label>

                    <label>
                        <span>Senha:</span>
                        <input type="password" name="pass" />
                    </label>  

                    <input type="submit" name="adminLogin" value="Logar" class="btn blue" />
                    <input type="submit" name="redirect" value="Voltar ao Site" href="../../" class="btn red" />

                </form>
            </div>
        </div>

    </body>
</html>
<?php
ob_end_flush();
