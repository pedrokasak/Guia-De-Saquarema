<?php
$empLink = $Link->getData()['empresa_link'];
$cat = $Link->getData()['empresa_cat'];
?>
<!--HOME CONTENT-->
<div class="site-container">

    <section class="page_empresas">
        <header class="emp_header">
            <h2><?= $cat; ?></h2>
            <p class="tagline">Conheça as empresas cadastradas no seu guia online. Encontre aqui empresas <?= $cat; ?></p>
        </header>

        <?php
        $getPage = (!empty($Link->getLocal()[2]) ? $Link->getLocal()[2] : 1);
        $pager = new Pager(HOME . '/empresas/' . $empLink . '/');
        $pager->ExePager($getPage, 5);

        $readEmp = new Read;
        $readEmp->ExeRead("app_empresas", "WHERE empresa_status = 1 AND empresa_categoria = :cat ORDER BY empresa_date DESC LIMIT :limit OFFSET :offset", "cat={$empLink}&limit={$pager->getLimit()}&offset={$pager->getOffset()}");
        if (!$readEmp->getResult()):
            $pager->ReturnPage();
            WSErro("Desculpe mais ainda não há empresas cadastradas {$cat}, Volte outro dia", WS_INFOR);
        else:
            $View = new View();
            $tpl = $View->Load('empresa_list');
            foreach ($readEmp->getResult() as $emp):

                $cidade = new Read;
                $cidade->ExeRead("app_cidades", "WHERE cidade_id = :cidadeid", "cidadeid={$emp['empresa_cidade']}");
                $cidade = $cidade->getResult()[0]['cidade_nome'];

                $estado = new Read;
                $estado->ExeRead("app_estados", "WHERE estado_id = :estadoid", "estadoid={$emp['empresa_uf']}");
                $estado = $estado->getResult()[0]['estado_uf'];

                $emp['empresa_cidade'] = $cidade;
                $emp['empresa_uf'] = $estado;

                $View->Show($emp, $tpl);
            endforeach;

            echo '<footer>';
            echo '<nav class="paginator">';
            echo '<h2>Mais resultados para NOME DA CATEGORIA</h2>';
            
            $pager->ExePaginator("app_empresas", "WHERE empresa_status = 1 AND empresa_categoria = :cat", "cat={$empLink}");
            echo $pager->getPaginator();
            
            echo '</nav>';
            echo '</footer>';

        endif;
        ?>

    </section>

    <!--SIDEBAR-->
    <?php require(REQUIRE_PATH . '/inc/sidebar.inc.php'); ?>

    <div class="clear"></div>
</div><!--/ site container -->