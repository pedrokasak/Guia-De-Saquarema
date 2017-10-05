<?php

/**
 * Description of [MODEL ADMIN]
 * RESPONSÁVEL POR GERENCIAR AS CATEGORIAS DO SISTEMA NO ADMIN
 * @copyright (c) 2016, Pedro Henrique CodeBR<>
 */
class AdminCategory {

    private $Data;
    private $CatId;
    private $Error;
    private $Result;

    //Nome da tabela no Baco de dados
    const Entity = 'ws_categories';

    public function exeCreate(array $Data) {
        $this->Data = $Data;

        if (in_array('', $this->Data)):
            $this->Result = false;
            $this->Error = ['<b>Erro ao Cadastrar:</b> Para cadastrar uma categoria, preencha todos os campos!', WS_ALERT];
        else:
            $this->setData();
            $this->setName();
            $this->Create();
        endif;
    }

    public function exeUpdate($CategoryId, array $Data) {
        $this->CatId = (int) $CategoryId;
        $this->Data = $Data;

        if (in_array('', $this->Data)):
            $this->Result = false;
            $this->Error = ["<b>Erro ao Atualizar:</b> Para atualizar a categoria {$this->Data['category_title']}, preencha todos os campos!", WS_ALERT];
        else:
            $this->setData();
            $this->setName();
            $this->Update();
        endif;
    }

    public function exeDelete($CategoryId) {
        $this->CatId = (int) $CategoryId;

        $read = new Read();
        $read->ExeRead(self::Entity, "WHERE category_id = :delid", "delid={$CategoryId}");
        if (!$read->getResult()):
            $this->Result = false;
            $this->Error = ["Oppsss, Você tentou removar uma categoria que nao existe no sistema!", WS_INFOR];

        else:
            extract($read->getResult()[0]);
            if (!$category_parent && !$this->checkCats()):
                $this->Result = false;
                $this->Error = ["A <b>{$category_title}</b> possui categorias cadastradas. Para deletar, antes altere ou remova as subcategorias", WS_ALERT];
            elseif ($category_parent && !$this->checkPosts()):
                $this->Result = false;
                $this->Error = ["A <b>categoria{$category_title}</b> possui artigos cadastrados. Para deletar, antes remova todos os posts desta categorias", WS_ALERT];
            else:
                $delete = new Delete;
                $delete->ExeDelete(self::Entity, "WHERE category_id  = :deletaid", "deletaid={$this->CatId}");
                
                $tipo = ( empty($category_parent) ? 'seção' : 'categoria');
                $this->Result = true;
                $this->Error = ["A <b>{$tipo} {$category_title}</b> foi removida com sucesso do sistema!", WS_ACCEPT];
            endif;

        endif;
    }

    function getError() {
        return $this->Error;
    }

    function getResult() {
        return $this->Result;
    }

    /*
     * ***************************************
     * **********  PRIVATE METHODS  **********
     * ***************************************
     */

    private function setData() {
        $this->Data = array_map('strip_tags', $this->Data);
        $this->Data = array_map('trim', $this->Data);
        $this->Data['category_name'] = Check::Name($this->Data['category_title']);
        $this->Data['category_date'] = Check::Data($this->Data['category_date']);
        $this->Data['category_parent'] = ($this->Data['category_parent'] == 'null' ? null : $this->Data['category_parent']);
    }

    private function setName() {
        $Where = (!empty($this->CatId) ? "category_id !={$this->CatId} AND" : '' );

        $readName = new Read;
        $readName->ExeRead(self::Entity, "WHERE {$Where} category_title = :t", "t={$this->Data['category_title']}");
        if ($readName->getResult()):
            $this->Data['category_name'] = $this->Data['category_name'] . '-' . $readName->getRowCount();
        endif;
    }

    //Verifica Categoria da Seção

    private function checkCats() {
        $readSes = new Read();
        $readSes->ExeRead(self::Entity, "WHERE category_parent = :parent", "parent={$this->CatId}");
        if ($readSes->getResult()):
            return false;
        else:
            return true;
        endif;
    }

    //Verifica artigos da categoria

    private function checkPosts() {
        $readPosts = new Read;
        $readPosts->ExeRead("ws_posts", "WHERE post_category = :category", "category={$this->CatId}");
        if ($readPosts->getResult()):
            return false;
        else:
            return true;
        endif;
    }

    private function Create() {
        $Create = new Create;
        $Create->ExeCreate(self::Entity, $this->Data);
        if ($Create->getResult()):
            $this->Result = $Create->getResult();
            $this->Error = ["<b>Sucesso:</b> A Categoria {$this->Data['category_title']} já foi cadastrada no sistema!", WS_ACCEPT];
        endif;
    }

    private function Update() {
        $Update = new Update;
        $Update->ExeUpdate(self::Entity, $this->Data, "WHERE category_id = :catid", "catid={$this->CatId}");
        if ($Update->getResult()):
            $tipo = (empty($this->Data['category_parent']) ? 'seção' : 'categoria');
            $this->Result = true;
            $this->Error = ["<b>Sucesso:</b> A Categoria {$tipo}{$this->Data['category_title']} já foi atualizada no sistema!", WS_ACCEPT];
        endif;
    }

    private function Delete() {
        
    }

}
