<?php

/**
 * Link.class [MODELS]
 *
 * @author Pedro Henrique
 * @copyright (c) 2017, Pedro Henrique
 */
class Link{

    private $file;
    private $link;
    /**
     * @var DATA
     */
    private $local;
    private $patch;
    private $tags;
    private $data;

    /**
     * @var Seo
     */
    private $seo;
    
    function __construct() {
        $this->local = strip_tags(trim(filter_input(INPUT_GET, 'url', FILTER_DEFAULT)));
        $this->local = ($this->local ? $this->local: 'index');
        $this->local = explode('/', $this->local); 
        $this->file = (isset($this->local[0]) ? $this->local[0] : 'index');
        $this->link = (isset($this->local[1]) ? $this->local[1] : null);
        $this->seo = new Seo($this->file, $this->link);
    }

    public function getTags() {
        $this->tags = $this->seo->getTags();
        echo $this->tags;
    }
    
    public function getData() {
        $this->data = $this->seo->getData();
        return $this->data;
    }
    
    function getLocal() {
        return $this->local;
    }

    function getPatch() {
        $this->setPatch();
        return $this->patch;
    }

    //PRIVATES
    
    private function setPatch() {
        if(file_exists(REQUIRE_PATH. DIRECTORY_SEPARATOR . $this->file . '.php')):
            $this->patch = REQUIRE_PATH . DIRECTORY_SEPARATOR . $this->file . '.php';
        elseif(file_exists(REQUIRE_PATH . DIRECTORY_SEPARATOR . $this->file . DIRECTORY_SEPARATOR . $this->link. '.php')):
            $this->patch = REQUIRE_PATH . DIRECTORY_SEPARATOR . $this->file . DIRECTORY_SEPARATOR . $this->link. '.php';
        else:
            $this->patch = REQUIRE_PATH. DIRECTORY_SEPARATOR .'404.php';
        endif;
    }
}
