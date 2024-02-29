<!-- Aviso de Consentimento de Cookies (Barra Fixa) -->

<div id="lgpd-cookie-consent" style="display:none;">
    <div class="cookie-consent-body">
        <div class="text">
            Utilizamos cookies para melhorar sua experiência. Acesse o link <a href="<?php echo esc_url(get_option('lgpd_cookie_toolkit_privacy_policy_url', '/privacidade-de-dados')); ?>" target="_blank">Política de privacidade</a>
 para saber mais sobre o assunto.
        </div>
        <div class="buttons">
            <button id="reject-cookies" class="btn-outline">Rejeitar cookies</button>
            <button id="customize-cookies" class="btn-outline">Personalizar</button>
            <button id="accept-cookies" class="btn-primary">Aceitar cookies</button>
        </div>
        <div class="cookie-consent-footer">
        Powered by: <img src="https://desenvolvaweb.com/wp-content/uploads/2022/06/admin-ajax.png" alt="Logo da Empresa" class="cookie-consent-logo"/>
    </div>
    </div>

</div>


<!-- Modal de Personalização de Cookies -->
<div class="modal fade" id="cookieCustomizationModal" tabindex="-1" role="dialog" aria-labelledby="cookieCustomizationModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="cookieCustomizationModalLabel">Personalize os cookies</h5>

      </div>
      <div class="modal-body">
        <h4>O que são cookies?</h4>
        <p>Cookies são arquivos de texto que são salvos em seu dispositivo para lembrar suas informações quando você acessa nosso site, como por exemplo dados de acesso. Porém, essas informações somente são armazenadas se você permitir.</p>

        <p>Nós utilizamos os cookies também para identificar como é a sua experiência com nossos canais, contar acessos, problemas de navegação e consequentemente, com esses dados, termos informação para melhorarmos a sua navegação.</p>
        <div class="modal-cookies">
        <h5 class="modal-title-category" id="cookieCustomizationModalLabel">Categoria dos cookies que utilizamos</h5>

      </div>
        <div class="cookie-category">
          <div class="flex-container">
            <div class="cookie-text">
              <h5>Cookies essenciais</h5>
              <p>São essencais para que algumas funcionalidades de nosso site e portal funcionem corretamente, como por exemplo distingui uma pessoa de um bot.</p>
            </div>
      
          </div>
        </div>
        <!-- Repetir para outras categorias -->

        <div class="cookie-category">
          <div class="flex-container">
            <div class="cookie-text">
              <h5>Cookies funcionais e de desempenho</h5>
              <p>Salvam suas preferências de uso, como por exemplo quando você faz o autopreenchimento de um formulário com as informações de nome, endereço, telefone.</p>
            </div>
            <div class="custom-switch">
            <label class="switch">
            <input type="checkbox" id="functionalCookies" checked >
                <span class="slider round"></span>
                </label>
            </div>
          </div>
        </div>
        <!-- Repetir para outras categorias -->

        <div class="cookie-category">
          <div class="flex-container">
            <div class="cookie-text">
              <h5>Cookies analíticos</h5>
              <p>São cookies que geram informação de tráfegos no site, gerando indicadores de comportamento. Todos os dados coletados são anônimos.</p>
            </div>
            <div class="custom-switch">
            <label class="switch">
            <input type="checkbox" id="analyticsCookies" checked >
                <span class="slider round"></span>
                </label>
            </div>
          </div>
        </div>
        <!-- Repetir para outras categorias -->

        <div class="cookie-category">
          <div class="flex-container">
            <div class="cookie-text">
              <h5>Cookies de publicidade</h5>
              <p>Esses são os que nos fornecem informação de assuntos de seu interesse e conseguirmos direcionar a publicidade que esteja de acordo com seu perfil.</p>
            </div>
            <div class="custom-switch">
            <label class="switch">
            <input type="checkbox" id="adsCookies" checked >
                <span class="slider round"></span>
                </label>
            </div>
          </div>
        </div>
        <!-- Repetir para outras categorias -->
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Deixar para depois</button>
        <button type="button" class="btn btn-primary" id="confirmMyChoice">Confirmar minha escolha</button>

      </div>
    </div>
  </div>
</div>
