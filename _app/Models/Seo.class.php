<?php

/**
 * Classe de apoio para o modelo do LINK. Pode ser utilizada para gerar SEO para as páginas do sistema.
 *
 * @author Pedro Henrique
 */
class Seo {

    private $file;
    private $link;
    private $data;
    private $tags;

    /* DADOS POVOADOS */
    private $seoTags;
    private $seoData;

    function __construct($file, $link) {
        $this->file = strip_tags(trim($file));
        $this->link = strip_tags(trim($link));
    }

    public function getTags() {
        $this->checkData();
        return $this->seoTags;
    }

    public function getData() {
        $this->checkData();
        return $this->seoData;
    }

    /*
     * ***************************************
     * **********  PRIVATE METHODS  **********
     * ***************************************
     */

    private function checkData() {
        if (!$this->seoData):
            $this->getSeo();
        endif;
    }

    private function getSeo() {
        $readSeo = new Read;

        switch ($this->file):
            case 'artigo':
                $Admin = (isset($_SESSION['userlogin']['userlevel']) && $_SESSION['userlogin']['userlevel'] == 3 ? TRUE : false);
                $Check = ($Admin ? '' : 'post_status = 1 AND');

                $readSeo->ExeRead("ws_posts", "WHERE {$Check} post_name = :link", "link={$this->link}");
                if (!$readSeo->getResult()):
                    $this->seoData = null;
                    $this->seoTags = null;
                else:
                    extract($readSeo->getResult()[0]);
                    $this->seoData = $readSeo->getResult()[0];
                    $this->data = [$post_title . ' - ' . SITENAME, $post_content, HOME . "/artigo/{$post_name}", HOME . "/uploads/{$post_cover}"];

                    #post::post_views

                    $ArrUpdate = ['post_views' => $post_views + 1];
                    $Update = new Update();
                    $Update->ExeUpdate("ws_posts", $ArrUpdate, "WHERE post_id =:postid", "postid={$post_id}");
                endif;
                break;

            //SEO:: Cateogrias
            case 'categoria':
                $readSeo->ExeRead("ws_categories", "WHERE category_name = :link", "link={$this->link}");
                if (!$readSeo->getResult()):
                    $this->seoData = null;
                    $this->seoTags = null;
                else:
                    extract($readSeo->getResult()[0]);
                    $this->seoData = $readSeo->getResult()[0];
                    $this->data = [$category_title . ' - ' . SITENAME, $category_content, HOME . "/categoria/{$category_name}", INCLUDE_PATH . '/images/site.png'];

                    #postconta views da categoria
                    $ArrUpdate = ['category_views' => $category_views + 1];
                    $Update = new Update();
                    $Update->ExeUpdate("ws_categories", $ArrUpdate, "WHERE category_id =:catid", "catid={$category_id}");
                endif;
                break;
                
            //SEO:: Pesquisa
            case 'pesquisa':
                $readSeo->ExeRead("ws_posts", "WHERE post_status = 1 AND (post_title LIKE '%' :link '%' OR post_content LIKE '%' :link '%')", "link={$this->link}");
                if (!$readSeo->getResult()):
                    $this->seoData = null;
                    $this->seoTags = null;
                else:
                    $this->seoData['count'] = $readSeo->getRowCount();
                    $this->data = ["Pesquisa por: {$this->link}" . ' - ' . SITENAME, "Sua pesquisa por {$this->link} retornou {$this->seoData['count']} resultados", HOME . "/pesquisa/{$this->link}", INCLUDE_PATH . '/images/site.png'];
                endif;
                break;

             //SEO:: Empresas
            case 'empresas':
                $name = ucwords(str_replace("-", " ", $this->link));
                $this->seoData = ["empresa_link" => $this->link, "empresa_cat" => $name];
                $this->data = ["Empresas {$this->link}". SITENAME ,"Confira o guia completo de sua cidade, e encontre empresas {$this->link}.", HOME . '/empresas/' . $this->link, INCLUDE_PATH . '/images/site.png'];
                break;
            
            //SEO:: Empresa SINGLE
            case 'empresa':
                $Admin = (isset($_SESSION['userlogin']['userlevel']) && $_SESSION['userlogin']['userlevel'] == 3 ? TRUE : false);
                $Check = ($Admin ? '' : 'empresa_status = 1 AND');

                $readSeo->ExeRead("app_empresas", "WHERE {$Check} empresa_name = :link", "link={$this->link}");
                if (!$readSeo->getResult()):
                    $this->seoData = null;
                    $this->seoTags = null;
                else:
                    extract($readSeo->getResult()[0]);
                    $this->seoData = $readSeo->getResult()[0];
                    $this->data = [$empresa_title . ' - ' . SITENAME, $empresa_sobre, HOME . "/empresa/{$empresa_name}", HOME . "/uploads/{$empresa_capa}"];

                    #empresa::atualização das visitas da empresa_views

                    $ArrUpdate = ['empresa_views' => $empresa_views + 1];
                    $Update = new Update();
                    $Update->ExeUpdate("app_empresas", $ArrUpdate, "WHERE empresa_id =:empresaid", "empresaid={$empresa_id}");
                endif;
                break;
             
            //SEO:: 404
            case 'index':
                $this->data = [SITENAME . ' Seu guia de empresas, eventos e muito maiss!! ' . SITEDESC, HOME, INCLUDE_PATH . '/images/site.png'];
                break;
            
            case 'quem-somos':
                $this->data = [SITENAME . ' Um pouco mais da nossa História' . SITEDESC, HOME, INCLUDE_PATH . '/images/quem-somos.png'];
                break;
            
            case 'cadastra-empresa':
                $this->data = [SITENAME . ' Gere mais Valor para sua empresa cadastrando ela em nosso Guia' . SITEDESC, HOME, INCLUDE_PATH . '/images/cadastra-empresa.png'];
                break;

            //SEO:: HOME - DEFAULT
            default :
                $this->data = ['Guia De Saquarema  - 404 Oppss, Nada Encontrado!! ' . SITEDESC, HOME . '/404' . INCLUDE_PATH . '/images/404.png'];
        endswitch;

        if ($this->data):
            $this->setTags();
        endif;
    }

    private function setTags() {
        $this->tags['Title'] = $this->data[0];
        $this->tags['Content'] = Check::Words(html_entity_decode($this->data[1]), 25);
        $this->tags['Link'] = $this->data[1];
        $this->tags['Image'] = $this->data[1];

        $this->tags = array_map('strip_tags', $this->tags);
        $this->tags = array_map('trim', $this->tags);

        $this->data = null;

        //NORMAL PAGE
        $this->seoTags = '<title>' . $this->tags['Title'] . '</title>' . "\n";
        $this->seoTags .= '<meta name="description" content="' . $this->tags['Content'] . '"/>' . "\n";
        $this->seoTags .= '<meta name="robots" content="index, follow"/>' . "\n";
        $this->seoTags .= '<link rel="canonical" href="' . $this->tags['Link'] . '"/>' . "\n";
        $this->seoTags .= "\n";

        //FACEBOOK        
        $this->seoTags .= '<meta property="og:site_name" content="' . SITENAME . '" />' . "\n";
        $this->seoTags .= '<meta property="og:locale" content="pt_BR" />' . "\n";
        $this->seoTags .= '<meta property="og:title" content="' . $this->tags['Title'] . '"/>' . "\n";
        $this->seoTags .= '<meta property="og:description" content="' . $this->tags['Content'] . '" />' . "\n";
        $this->seoTags .= '<meta itemprop="image" content="' . $this->tags['Image'] . '"/>' . "\n";
        $this->seoTags .= '<meta itemprop="url" content="' . $this->tags['Link'] . '"/>' . "\n";
        $this->seoTags .= '<meta property="og:type" content="article" />' . "\n";
        $this->seoTags .= "\n";

        //ITEM GROUP (TWITTER)        
        $this->seoTags .= '<meta itemprop="name" content="' . $this->tags['Title'] . '"/>' . "\n";
        $this->seoTags .= '<meta itemprop="description" content="' . $this->tags['Content'] . '"/>' . "\n";
        $this->seoTags .= '<meta itemprop="url" content="' . $this->tags['Link'] . '"/>' . "\n";
        $this->seoTags .= '<meta property="twitter:card" content="summary_large_image" />' . "\n";
        $this->seoTags .= '<meta property="twitter:image:src" content="' . $this->tags['Image'] . '" />' . "\n";

        $this->tags = null;
    }

}
