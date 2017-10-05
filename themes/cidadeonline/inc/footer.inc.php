<footer class="main-footer" id="contato">
    <section class="container">                
        <nav>
            <h3 class="line_title"><span>Categorias:</span></h3>
            <ul>
                <li><a href="<?= HOME ?>/cadastra-empresa" title="Home">Cadastre Sua Empresa</a></li>
                <li><a href="http://www.facebook.com/GuiaOnlineDeSaquarema/" target="_blank" title="Home">Guia de Saquarema No Facebook</a></li>
                <li><a href="<?= HOME ?>" title="Home">Voltar ao início</a></li>
                <li><a href="http://www.guiadesaquarema.com.br/quem-somos" target="_blank" title="Guia De Saquarema Quem Somos">Conheça o Guia De Saquarema</a></li>
            </ul>
        </nav>

        <section>
            <h3 class="line_title"><span>Um resumo:</span></h3>
            <p>Este site foi Desenvolvido para facilitar a busca de serviços que esta maravilhosa cidade tem a oferecer!!!.</p>
            <p>Uma ideia totalmente da <b>SaquaTech</b> que busca facilitar a vida dos turistas e principalmente dos moradores de Saquarema.</p>
            <p><a href="https://saquatech.com" target="blank" title="SaquaTech">Clique aqui e conheça a SaquaTech</a></p>
        </section>

        <section class="footer_contact">
            <h3 class="line_title"><span>Contato:</span></h3>
            <?php
            $contato = filter_input_array(INPUT_POST, FILTER_DEFAULT);
            if($contato && $contato['SendFormContato']):
                unset($contato['SendFormContato']);
                $contato['Assunto'] = 'Mensagem Via Guia De Saquarema';
                $contato['destinoNome'] = 'Suporte - Guia de Saquarema';
                $contato['destinoEmail'] = 'suporte@saquatech.com';
                
                $sendMail = new Email;
                $sendMail->Enviar($contato);
                
                if($sendMail->getError()):
                    WSErro($sendMail->getError()[0], $sendMail->getError()[1]);
                endif;
            endif;
            ?>
            
            <form name="FormContato" action="#contato" method="post">
                <label>
                    <span>nome:</span>
                    <input type="text" title="Informe seu nome" name="remetenteNome" required />
                </label>

                <label>
                    <span>e-mail:</span>
                    <input type="email" title="Informe seu e-mail" name="remetenteEmail" required />
                </label>

                <label>
                    <span>mensagem:</span>
                    <textarea title="Envie sua mensagem" name="Mensagem" required rows="3"></textarea>
                </label>

                <input type="submit" value="Enviar" name="SendFormContato" class="btn">                        
            </form>
        </section>
        <div class="clear"></div>
    </section><!-- /ontainer -->

    <div class="footer_logo">SaquaOnline - Eventos, Promoções e Novidades!</div><!-- footer logo -->
</footer>