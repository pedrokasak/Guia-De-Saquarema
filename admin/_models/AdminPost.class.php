<?php

/**
 * Description of AdminPost[MODELS]
 * Responsável por gerenciar no admin do sistema
 * @copyright (c) 2016, Pedro Henrique CodeBR<>
 */
class AdminPost {

    private $Data;
    private $Post;
    private $Error;
    private $Result;

    //Nome da Tabela do banco de dados
    const Entity = 'ws_posts';

    public function exeCreate(array $Data) {
        $this->Data = $Data;

        if (in_array('', $this->Data)):
            $this->Error = ["<b>Erro ao Cadastrar:</b> Para criar um post favor preencha todos os campos!", WS_ALERT];
            $this->Result = false;
        else:
            $this->setData();
            $this->setName();

            if ($this->Data['post_cover']):
                $upload = new Upload;
                $upload->Image($this->Data['post_cover'], $this->Data['post_name']);
            endif;
            if (isset($upload) && $upload->getResult()):
                $this->Data['post_cover'] = $upload->getResult();
                $this->Create();
            else:
                $this->Data['post_cover'] = null;
                $_SESSION['errCapa'] = ["<b>ERRO AO ENVIAR A CAPA:</b> Tipo de arquivo inválido, envie imagens JPG, PNG, GIG ou BITMAP"];
                $this->Create();
            endif;
        endif;
    }

    public function ExeUpdate($PostId, array $Data) {
        $this->Post = (int) $PostId;
        $this->Data = $Data;

        if (in_array('', $this->Data)):
            $this->Error = ["Para atualizar estes posts favor preencha todos os campos (capa não precisa ser enviada) !"];
            $this->Result = false;
        else:

            $this->setData();
            $this->setName();
            /**
             * @if tem a função de reenvio da capa 
             */
            if (is_array($this->Data['post_cover'])):
                $readCapa = new Read;
                $readCapa->ExeRead(self::Entity, "WHERE post_id = :post", "post={$this->Post}");
                $capa = '../uploads/' . $readCapa->getResult()[0]['post_cover'];
                if (file_exists($capa) && !is_dir($capa)):
                    unlink($capa);
                endif;

                $uploadCapa = new Upload;
                $uploadCapa->Image($this->Data['post_cover'], $this->Data['post_name']);
            endif;

            if (isset($uploadCapa) && $uploadCapa->getResult()):
                $this->Data['post_cover'] = $uploadCapa->getResult();
                $this->Update();
            else:
                unset($this->Data['post_cover']);
                if (!empty($uploadCapa) && $uploadCapa->getError()):
                    WSErro("<b>ERRO AO ENVIAR A CAPA:</b>" . $uploadCapa->getError(), E_USER_WARNING);
                endif;
                $this->Update();
            endif;
        endif;
    }

    public function exeDelete($PostId) {
        $this->Post = (int) $PostId;
        $readPost = new Read;
        $readPost->ExeRead(self::Entity, "WHERE post_id = :post", "post={$this->Post}");
        if (!$readPost->getResult()):
            $this->Error = ["O post que você tentou deletar não existe no sistema", WS_ERROR];
            $this->Result = false;
        else:
            $postDelete = $readPost->getResult()[0];
            if (file_exists('../uploads/' . $postDelete['post_cover']) && !is_dir('../uploads/' . $postDelete['post_cover'])):
                unlink('../uploads/' . $postDelete['post_cover']);
            endif;
            $readGallery = new Read;
            $readGallery->ExeRead("ws_posts_gallery", "WHERE post_id = :id", "id={$this->Post}");
            if ($readGallery->getResult()):
                foreach ($readGallery->getResult() as $gbDel):
                    if (file_exists('../uploads/' . $gbDel['gallery_image']) && !is_dir('../uploads/' . $gbDel['gallery_image'])):
                        unlink('../uploads/' . $gbDel['gallery_image']);
                    endif;
                endforeach;
            endif;

            $Deleta = new Delete();
            $Deleta->ExeDelete("ws_posts_gallery", "WHERE post_id = :gbpost", "gbpost={$this->Post}");
            $Deleta->ExeDelete(self::Entity, "WHERE post_id = :postid", "postid={$this->Post}");

            $this->Error = ["O post <b>{$postDelete['post_title']}</b> foi removido com sucesso do sistema!", WS_ACCEPT];
            $this->Result = true;
        endif;
    }

    public function exeStatus($PostId, $PostStatus) {
        $this->Post = (int) $PostId;
        $this->Data['post_status'] = (string) $PostStatus;
        $Update = new Update();
        $Update->ExeUpdate(self::Entity, $this->Data, "WHERE post_id = :id", "id={$this->Post}");
    }

    public function gallerySend(array $Images, $PostId) {
        $this->Post = (int) $PostId;
        $this->Data = $Images;

        $ImageName = new Read;
        $ImageName->ExeRead(self::Entity, "WHERE post_id = :id", "id={$this->Post}");

        if (!$ImageName->getResult()):
            $this->Error = ["Erro ao enviar galeria. O índice {$this->Post} não foi encontrado no banco", WS_ERROR];
            $this->Result = false;
        else:
            $ImageName = $ImageName->getResult()[0]['post_name'];

            $gbFiles = array();
            $gbCount = count($this->Data['tmp_name']);
            $gbKeys = array_keys($this->Data);

            for ($gb = 0; $gb < $gbCount; $gb++):
                foreach ($gbKeys as $Keys):
                    $gbFiles[$gb][$Keys] = $this->Data[$Keys][$gb];
                endforeach;
            endfor;

            $gbSend = new Upload();
            $i = 0;
            $u = 0;

            foreach ($gbFiles as $gbUpload):
                $i++; // Conta a Quantidade de Imagens Enviadas
                $imgName = "{$ImageName}-gb-{$this->Post}-" . (substr(md5(time() + $i), 0, 5));
                $gbSend->Image($gbUpload, $imgName);

                if ($gbSend->getResult()):
                    $gbImage = $gbSend->getResult();
                    $gbCreate = ['post_id' => $this->Post, "gallery_image" => $gbImage, "gallery_date" => date('Y-m-d H:i:s')];
                    $insertGb = new Create;
                    $insertGb->ExeCreate('ws_posts_gallery', $gbCreate);
                    $u++; // e aqui quantas foram upadas
                endif;
            endforeach;

            if ($u > 1):
                $this->Error = ["<b>Galeria Atualizada:</b> foram enviadas {$u} imagens para a galeria deste posts!", WS_ACCEPT];
                $this->Result = true;
            endif;
        endif;
    }

    function getError() {
        return $this->Error;
    }

    function getResult() {
        return $this->Result;
    }

    public function gbRemove($GbImageId) {
        $this->Post = (int) $GbImageId;
        $readGb = new Read();
        $readGb->ExeRead("ws_posts_gallery", "WHERE gallery_id = :gb", "gb={$this->Post}");
        if ($readGb->getResult()):
            $Imagem = '../uploads/' . $readGb->getResult()[0]['gallery_image'];
            if (file_exists($Imagem) && !is_dir($Imagem)):
                unlink($Imagem);
            endif;

            $Deleta = new Delete();
            $Deleta->ExeDelete("ws_posts_gallery", "WHERE gallery_id = :id", "id={$this->Post}");
            if ($Deleta->getResult()):
                $this->Error = ["A imagem foi removida com sucesso da galeria!", WS_ACCEPT];
                $this->Result = true;
            endif;

        endif;
    }

    /*
     * ***************************************
     * **********  PRIVATE METHODS  **********
     * ***************************************
     */

    private function setData() {
        $Cover = $this->Data['post_cover'];
        $Content = $this->Data['post_content'];
        unset($this->Data['post_cover'], $this->Data['post_content']);

        $this->Data = array_map('strip_tags', $this->Data);
        $this->Data = array_map('trim', $this->Data);

        $this->Data['post_name'] = Check::Name($this->Data['post_title']);
        $this->Data['post_date'] = Check::Data($this->Data['post_date']);
        $this->Data['post_type'] = 'post';

        $this->Data['post_cover'] = $Cover;
        $this->Data['post_content'] = $Content;
        $this->Data['post_cat_parent'] = $this->getCatParent();
    }

    private function getCatParent() {
        $rcat = new Read;
        $rcat->ExeRead("ws_categories", "WHERE category_id = :id", "id={$this->Data['post_category']}");
        if ($rcat->getResult()):
            return $rcat->getResult()[0]['category_parent'];
        else:
            return null;
        endif;
    }

    private function setName() {
        $Where = (isset($this->Post) ? "post_id != {$this->Post} AND" : '');
        $readName = new Read();
        $readName->ExeRead(self::Entity, "WHERE {$Where} post_title = :t", "t={$this->Data['post_title']}");
        if ($readName->getResult()):
            $this->Data['post_name'] = $this->Data['post_name'] . '-' . $readName->getRowCount();
        endif;
    }

    private function Create() {
        $cadastra = new Create();
        $cadastra->ExeCreate(self::Entity, $this->Data);
        if ($cadastra->getResult()):
            $this->Error = ["O post {$this->Data['post_title']} foi cadastrado com sucesso no sistema!", WS_ACCEPT];
            $this->Result = $cadastra->getResult();
        endif;
    }

    private function Update() {
        $Update = new Update;
        $Update->ExeUpdate(self::Entity, $this->Data, "WHERE post_id = :id", "id={$this->Post}");
        if ($Update->getResult()):
            $this->Error = ["O post <b>{$this->Data['post_title']}</b> foi atualizado com sucesso no sistema!", WS_ACCEPT];
            $this->Result = true;
        endif;
    }

}
