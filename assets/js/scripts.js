document.addEventListener('DOMContentLoaded', function () {
    var cookieConsentModal = document.getElementById('lgpd-cookie-consent');

    // Função para verificar a preferência do cookie
    function checkCookieConsent() {
        var consent = lgpd_cookie_toolkit_get_cookie('cookie_consent');
        return consent !== null;
    }

    // Exibir ou ocultar o modal com base na preferência do cookie
    function toggleCookieConsentModal() {
        if (cookieConsentModal) {
            cookieConsentModal.style.display = checkCookieConsent() ? 'none' : 'block';
        }
    }

    // Exibir a caixa de consentimento ao carregar a página
    toggleCookieConsentModal();

    // Ação ao clicar no botão "Aceitar cookies"
    var acceptCookiesButton = document.getElementById('accept-cookies');
    if (acceptCookiesButton) {
        acceptCookiesButton.addEventListener('click', function () {
            // Ação para aceitar cookies
            lgpd_cookie_toolkit_set_cookie('cookie_consent', 'accepted', 365 * 24 * 60 * 60);
            toggleCookieConsentModal();
        });
    }

    // Ação ao clicar no botão "Rejeitar cookies"
    var rejectCookiesButton = document.getElementById('reject-cookies');
    if (rejectCookiesButton) {
        rejectCookiesButton.addEventListener('click', function () {
            // Ação para rejeitar cookies
            lgpd_cookie_toolkit_set_cookie('cookie_consent', 'rejected', 365 * 24 * 60 * 60);
            toggleCookieConsentModal();
        });
    }

    // Ação ao clicar no botão "Personalizar cookies"
    var customizeCookiesButton = document.getElementById('customize-cookies');
    if (customizeCookiesButton) {
        customizeCookiesButton.addEventListener('click', function () {
            // Implemente a personalização conforme necessário
        });
    }
});
