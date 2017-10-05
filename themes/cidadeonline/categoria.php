<?php
if ($Link->getData()):
    extract($Link->getData());
else:
    header('Location: ' . HOME . DIRECTORY_SEPARATOR . '404');
endif;
?>
<!--HOME CONTENT-->
<div class="site-container">

    <section class="page_categorias">
        <header class="cat_header">
            <h2><?= $category_title; ?></h2>
            <p class="tagline"><?= $category_content; ?></p>
        </header>

        <?php
        $getPage = (!empty($Link->getLocal()[2]) ? $Link->getLocal()[2] : 1);
        $pager = new Pager(HOME . '/categoria/' . $category_name . '/');
        $pager->ExePager($getPage, 12);

        $readCat = new Read();
        $readCat->ExeRead("ws_posts", "WHERE post_status = 1 AND (post_category = :cat OR post_cat_parent = :cat) ORDER BY post_date DESC LIMIT :limit OFFSET :offset", "cat={$category_id}&limit={$pager->getLimit()}&offset={$pager->getOffset()}");
        if(!$readCat->getResult()):
           $pager->ReturnPage();
            WSErro("Desculpe,  a categoria {$category_title} ainda não tem artigos publicados", WS_INFOR);
        else:
            $countCat = 0;
            $View = new View;
            $tpl_cat = $View->Load('article_m');
            foreach ($readCat->getResult() as $cat):
                $countCat++;
                $class = ($countCat % 3 == 0 ? ' class="right"' : null);
                echo "<span{$class}>";
                $cat['post_title'] = Check::Words($cat['post_title'], 20);
                $cat['post_content'] = Check::Words($cat['post_title'], 40);
                $cat['post_title'] = Check::Words($cat['post_title'], 20);
                $cat['datetime'] = date('Y-m-d', strtotime($cat['post_date']));
                $cat['pubdate'] = date('d/m/Y H:i', strtotime($cat['post_date']));
                $View->Show($cat, $tpl_cat);
                echo "</span>";
            endforeach;
        endif;

        echo '<nav class="paginator">';
        echo '<h2>Mais resultados para NOME DA CATEGORIA</h2>';

        $pager->ExePaginator("ws_posts", "WHERE post_status = 1 AND (post_category = :cat OR post_cat_parent = :cat)", "cat={$category_id}");
        echo $pager->getPaginator();

        echo '</nav> ';
        ?>
    </section>

    <div class="clear"></div>
</div><!--/ site container -->