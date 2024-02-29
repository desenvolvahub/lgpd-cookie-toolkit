(function($) {
  $(document).ready(function() {
      // Verifica o consentimento existente ao carregar a página
      checkCookieConsent();

      // Aceitar cookies
      $('#accept-cookies').on('click', function() {
          var preferences = {
              essential: true,
              functional: true,
              analytics: true,
              advertising: true
          };
          saveCookiePreferences(preferences);
      });

      // Rejeitar cookies
      $('#reject-cookies').on('click', function() {
          hideCookieConsentModal();
      });

    // Personalizar cookies
    $('#customize-cookies').on('click', function() {
      $('#cookieCustomizationModal').modal('show');
      hideCookieConsentModal(); // Garante que o aviso de consentimento será ocultado
  });

      // Salvar preferências personalizadas
      // Note que removemos a segunda chamada $(document).ready e unificamos as chamadas
      $('#saveCookiePreferences').on('click', function() {
          var preferences = {
              essential: true,
              functional: $('#functionalCookies').is(':checked'),
              analytics: $('#analyticsCookies').is(':checked'),
              advertising: $('#advertisingCookies').is(':checked')
          };
          saveCookiePreferences(preferences);
          $('#cookieCustomizationModal').modal('hide');
      });

      $('#confirmMyChoice').on('click', function() {
        var preferences = {
            essential: true,
            functional: $('#functionalCookies').is(':checked'),
            analytics: $('#analyticsCookies').is(':checked'),
            advertising: $('#adsCookies').is(':checked') // Certifique-se de que o ID está correto
        };
        saveCookiePreferences(preferences);
        $('#cookieCustomizationModal').modal('hide');
    });
    $('.btn-secondary[data-dismiss="modal"]').on('click', function() {
      // Aqui, você pode optar por não fazer nada, pois o evento 'hidden.bs.modal' já cuidará de mostrar novamente o aviso de consentimento,
      // a menos que você queira executar alguma lógica adicional aqui.
  });

  function showCookieConsentModal() {
    $('#lgpd-cookie-consent').show();
}

        // Evento disparado quando o modal de personalização é fechado
        $('#cookieCustomizationModal').on('hidden.bs.modal', function () {
          // Verifica se as preferências de cookies não foram salvas/aceitas
          if (!getCookie('cookiePreferences')) {
              showCookieConsentModal(); // Mostra o aviso de consentimento novamente
          }
      });

      function saveCookiePreferences(preferences) {
          // Converter preferências para string JSON para armazenamento em cookie
          setCookie('cookiePreferences', JSON.stringify(preferences), 365);
          hideCookieConsentModal();

          // Enviar preferências para o servidor usando AJAX
          $.ajax({
              url: ajax_object.ajax_url, // Corrigido para usar a variável definida por wp_localize_script
              type: 'POST',
              data: {
                  action: 'save_cookie_preferences',
                  preferences: JSON.stringify(preferences),
                  security: ajax_object.nonce // Incluindo o nonce para segurança
              },
              success: function(response) {
                  console.log("Preferências salvas no servidor: ", response);
              },
              error: function(xhr, status, error) {
                  console.error("Erro ao salvar preferências no servidor: ", error);
              }
          });
      }

      function setCookie(name, value, days) {
          var expires = "";
          if (days) {
              var date = new Date();
              date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
              expires = "; expires=" + date.toUTCString();
          }
          document.cookie = name + "=" + (value || "") + expires + "; path=/";
      }

      function getCookie(name) {
          var nameEQ = name + "=";
          var ca = document.cookie.split(';');
          for(var i=0; i < ca.length; i++) {
              var c = ca[i];
              while (c.charAt(0) == ' ') c = c.substring(1, c.length);
              if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
          }
          return null;
      }

      function checkCookieConsent() {
          var preferences = getCookie('cookiePreferences');
          if (!preferences) {
              $('#lgpd-cookie-consent').css('display', 'flex');
          } else {
              $('#lgpd-cookie-consent').hide();
          }
      }

      function hideCookieConsentModal() {
        $('#lgpd-cookie-consent').hide();
    }
  });
})(jQuery);