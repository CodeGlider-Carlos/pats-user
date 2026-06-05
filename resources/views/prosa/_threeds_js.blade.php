{{--
    Helpers de cliente para 3-D Secure 2 (Prosa/OPPWA).
    Incluir con: @include('prosa._threeds_js')

    - prosaBrowserData(): recolecta los datos del navegador requeridos por 3DS2.
    - prosaHandleChallenge(challenge): envía el navegador al ACS del emisor
      construyendo y auto-enviando el formulario de reto.
--}}
<script>
    window.prosaBrowserData = function () {
        return {
            language: navigator.language || 'es-MX',
            screenHeight: String(window.screen ? window.screen.height : ''),
            screenWidth: String(window.screen ? window.screen.width : ''),
            timezone: String(new Date().getTimezoneOffset()),
            colorDepth: String(window.screen ? window.screen.colorDepth : 24),
            javaEnabled: (navigator.javaEnabled && navigator.javaEnabled()) ? true : false,
        };
    };

    window.prosaHandleChallenge = function (challenge) {
        if (!challenge || !challenge.url) {
            return false;
        }
        const form = document.createElement('form');
        form.method = (challenge.method || 'POST').toUpperCase();
        form.action = challenge.url;
        (challenge.parameters || []).forEach(function (p) {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = p.name;
            input.value = p.value;
            form.appendChild(input);
        });
        document.body.appendChild(form);
        form.submit();
        return true;
    };
</script>
