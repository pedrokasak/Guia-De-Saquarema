<!DOCTYPE html>
<?php
ob_start();
require('./_app/Config.inc.php');
include_once("analyticstracking.php");
?>
<html lang="pt-br" itemscope itemtype="https://schema.org/WebPage">
    <head>
        <meta charset="UTF-8">
        <?php   
        $Link = new Link;
        $Link->getTags();
        ?>

        <!--[if lt IE 9]>
            <script src="../../_cdn/html5.js"></script>
         <![endif]-->        

        <title><?= SITEDESC ?></title>

        <meta name="description" content="<?= SITEDESC ?>">
        <meta name="google-site-verification" content="0NV1qElXJ3qodIefy58nJmr-sJMPvV1boTZ435qHOQY" />
        <meta name="keywords" content="Saquarema, Guia de Saquarema, O Guia de Saquarema, Surf, Itaúna, Maracanã do Surf">
        <meta name="author" content="SaquaTech Tecnologia" itemscope itemtype="Organization">
        <meta name="robots" content="index, follow">
        <link rel="canonical" href="<?= HOME ?>"/>
        <link rel="base" href="<?= HOME; ?>"/>
        <link rel="alternate" type="application/rss+xml" href="<?= HOME; ?>/rss.xml"/>
        <link rel="shortcut icon" href="<?= INCLUDE_PATH; ?>/img/favicon.png">

        <link rel="stylesheet" href="<?= INCLUDE_PATH; ?>/css/reset.css">
        <link rel="stylesheet" href="<?= INCLUDE_PATH; ?>/css/style.css">
        <link rel="stylesheet" href="<?= HOME; ?>/_cdn/shadowbox/shadowbox.css">
        <link href='//fonts.googleapis.com/css?family=Baumans' rel='stylesheet' type='text/css'>
        
        <meta property="og:url" content="http://www.guiadesaquarema.com.br/<?php $Link->getData();?>" />
        <meta property="og:type" content="website" />
        <meta property="og:title" content="Guia de Saquarema" />
        <meta property="og:description" content="O Guia Mais Completo de Saquarema" />
        <meta property="og:image" content="http://www.guiadesaquarema.com.br/<?= INCLUDE_PATH; ?>/images/site.png" />


    </head>
    <body>

        <?php
        require(REQUIRE_PATH . '/inc/header.inc.php');
        
        if(!require($Link->getPatch())):
            WSErro('Erro ao incluir o Arquivo de navegação', WS_ERROR, TRUE);
        endif;

        require(REQUIRE_PATH . '/inc/footer.inc.php');
        ?>

    </body>

    <script src="<?= HOME ?>/_cdn/jquery.js"></script>
    <script src="<?= HOME ?>/_cdn/jcycle.js"></script>
    <script src="<?= HOME ?>/_cdn/jmask.js"></script>
    <script src="<?= HOME ?>/_cdn/shadowbox/shadowbox.js"></script>
    <script src="<?= HOME ?>/_cdn/_plugins.conf.js"></script>
    <script src="<?= HOME ?>/_cdn/_scripts.conf.js"></script>
    <script src="<?= HOME ?>/_cdn/fb_plugin.js"></script>

</html>
<?php
ob_end_flush();