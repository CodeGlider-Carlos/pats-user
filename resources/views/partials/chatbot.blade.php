{{-- PatsBot — Asistente PATS flotante --}}
<link rel="stylesheet" href="{{ asset('css/catbot_widget.css') }}">

<style>
    /* ─── Floating bubble ─── */
    #chatbot-bubble {
        position: fixed;
        bottom: 24px;
        left: 24px;
        z-index: 100000;
        font-family: 'DM Sans', system-ui, -apple-system, sans-serif;
    }

    #chatbot-toggle {
        width: 52px;
        height: 52px;
        border-radius: 50%;
        background: linear-gradient(135deg, #1e3a5f, #2563eb);
        border: none;
        cursor: pointer;
        box-shadow: 0 4px 18px rgba(37, 99, 235, .4);
        display: flex;
        align-items: center;
        justify-content: center;
        transition: transform .2s, box-shadow .2s;
    }

    #chatbot-toggle:hover {
        transform: scale(1.08);
        box-shadow: 0 6px 24px rgba(37, 99, 235, .55);
    }

    /* Chat icon SVG */
    #chatbot-toggle svg { display: block; }

    /* ─── Popup window ─── */
    #chatbot-window {
        display: none;
        position: absolute;
        bottom: 62px;
        left: 0;
        width: 360px;
        max-width: calc(100vw - 40px);
    }

    #chatbot-window.open { display: block; }

    /* ─── Card overrides — compact for floating popup ─── */
    #chatbot-window .pats-cw-card {
        border-radius: 16px;
        max-height: calc(100dvh - 108px);
        display: flex;
        flex-direction: column;
        overflow: hidden;
        box-shadow: 0 10px 36px rgba(13, 36, 97, .22);
    }

    /* Compact header */
    #chatbot-window .pats-cw-head {
        padding: 11px 13px;
        flex-shrink: 0;
        align-items: center;
    }

    #chatbot-window .pats-cw-brand {
        gap: 9px;
        align-items: center;
        min-width: 0;
    }

    #chatbot-window .pats-cw-mark {
        flex: 0 0 34px;
        width: 34px;
        height: 34px;
        border-radius: 10px;
        font-size: 18px;
    }

    #chatbot-window .pats-cw-brand > div {
        min-width: 0;
    }

    #chatbot-window .pats-cw-brand span {
        font-size: 9px;
        letter-spacing: .1em;
    }

    #chatbot-window .pats-cw-brand h2 {
        font-size: 15px !important;
        line-height: 1.2;
        margin: 1px 0 0;
    }

    #chatbot-window .pats-cw-brand p { display: none; }

    #chatbot-window .pats-cw-live {
        padding: 5px 8px;
        font-size: 10px;
        gap: 5px;
        white-space: nowrap;
    }

    #chatbot-window .pats-cw-live i {
        width: 6px;
        height: 6px;
    }

    /* Close button */
    #chatbot-close-btn {
        flex-shrink: 0;
        background: none;
        border: none;
        color: rgba(255, 255, 255, .75);
        font-size: 16px;
        line-height: 1;
        cursor: pointer;
        padding: 4px;
        margin-left: 2px;
        border-radius: 6px;
        transition: color .15s, background .15s;
    }

    #chatbot-close-btn:hover {
        color: #fff;
        background: rgba(255, 255, 255, .15);
    }

    /* Quick prompts — compact chips */
    #chatbot-window .pats-cw-quick {
        padding: 8px 10px;
        gap: 6px;
        flex-shrink: 0;
    }

    #chatbot-window .pats-cw-quick button {
        min-height: 28px;
        font-size: 11px;
        padding: 0 10px;
        border-radius: 999px;
        white-space: nowrap;
    }

    /* Body — single column, no padding/gap */
    #chatbot-window .pats-cw-body {
        display: flex;
        flex-direction: column;
        padding: 0;
        gap: 0;
        flex: 1;
        overflow: hidden;
        min-height: 0;
    }

    /* Chat section fills the body */
    #chatbot-window .pats-cw-chat {
        flex: 1;
        display: flex;
        flex-direction: column;
        min-height: 0;
        border: none;
        border-radius: 0;
    }

    /* Hide the inner redundant "Escribe tu duda" header */
    #chatbot-window .pats-cw-chat-head { display: none; }

    /* Messages */
    #chatbot-window .pats-cw-messages {
        flex: 1;
        min-height: 160px;
        max-height: 260px;
        padding: 12px 12px 8px;
        gap: 8px;
    }

    #chatbot-window .pats-cw-avatar {
        flex: 0 0 28px;
        width: 28px;
        height: 28px;
        border-radius: 10px;
        font-size: 14px;
    }

    #chatbot-window .pats-cw-bubble {
        padding: 8px 11px;
        border-radius: 12px;
    }

    #chatbot-window .pats-cw-bubble strong {
        font-size: 11px;
        margin-bottom: 3px;
    }

    #chatbot-window .pats-cw-bubble p {
        font-size: 13px;
        line-height: 1.45;
    }

    /* Loading dots */
    #chatbot-window .pats-cw-dots i {
        width: 4px;
        height: 4px;
    }

    /* Input form — always 2-column */
    #chatbot-window .pats-cw-form {
        display: grid;
        grid-template-columns: 1fr auto;
        gap: 8px;
        padding: 9px 10px;
        border-top: 1px solid var(--cw-line);
        flex-shrink: 0;
        background: #fff;
    }

    #chatbot-window .pats-cw-form textarea {
        min-height: 40px;
        max-height: 40px;
        font-size: 13.5px;
        padding: 9px 12px;
        resize: none;
        border-radius: 12px;
        line-height: 1.35;
    }

    #chatbot-window .pats-cw-form button[data-cw-send] {
        min-height: 40px;
        width: auto;
        padding: 0 16px;
        font-size: 13px;
        border-radius: 12px;
    }

    #chatbot-window .pats-cw-form button[data-cw-send]:disabled {
        opacity: .55;
        cursor: wait;
    }

    /* Hide result panel — response shows in chat */
    #chatbot-window [data-cw-result] { display: none !important; }

    @media (max-width: 767px) {
        #chatbot-bubble {
            bottom: calc(58px + env(safe-area-inset-bottom, 0px) + 12px) !important;
            left: 16px !important;
        }
        #chatbot-window { width: calc(100vw - 32px); }
    }
</style>

<div id="chatbot-bubble">
    <div id="chatbot-window" role="dialog" aria-label="Asistente PATS" aria-modal="true">
        <section
            data-pats-catbot-widget
            data-config="{{ json_encode([
                'askEndpoint'         => route('chatbot.ask'),
                'feedbackEndpoint'    => route('chatbot.feedback'),
                'defaultMode'         => 'manual',
                'audiencia'           => 'usuario',
                'contexto'            => 'chatbot_web',
                'mostrarFeedback'     => false,
                'mostrarContexto'     => false,
                'mostrarQuickPrompts' => true,
                'role'                => '',
                'user'                => '',
                'nombre'              => '',
                'region'              => '',
                'unidad'              => '',
                'contextoInicial'     => (object)[],
            ]) }}"
            class="pats-cw is-compact no-context is-user-widget"
        >
            <div class="pats-cw-card">

                {{-- Header --}}
                <header class="pats-cw-head">
                    <div class="pats-cw-brand">
                        <div class="pats-cw-mark">P</div>
                        <div>
                            <span>Asistente PATS</span>
                            <h2>¿En qué te ayudo?</h2>
                        </div>
                    </div>
                    <div style="display:flex;align-items:center;gap:6px;flex-shrink:0;margin-left:auto;">
                        <div class="pats-cw-live"><i></i><b>Activo</b></div>
                        <button id="chatbot-close-btn" aria-label="Cerrar">✕</button>
                    </div>
                </header>

                {{-- Quick-prompt chips --}}
                <div class="pats-cw-quick" aria-label="Preguntas rápidas">
                    <button type="button" data-cw-prompt="Qué es PATS">Qué es PATS</button>
                    <button type="button" data-cw-prompt="Cuánto cuesta PATS">Costo</button>
                    <button type="button" data-cw-prompt="Qué incluye PATS">Incluye</button>
                    <button type="button" data-cw-prompt="Cómo valido si PATS está activo">Vigencia</button>
                    <button type="button" data-cw-prompt="Dónde puedo pagar PATS">Pago</button>
                    <button type="button" data-cw-prompt="Cómo uso PATS en urgencias">Urgencias</button>
                </div>

                {{-- Body --}}
                <main class="pats-cw-body">
                    <section class="pats-cw-chat">

                        {{-- Inner header (hidden via CSS) --}}
                        <div class="pats-cw-chat-head" aria-hidden="true">
                            <div><span data-cw-mode-label>Consulta PATS</span><h3>Escribe tu duda</h3></div>
                            <button type="button" data-cw-clear-chat>Limpiar</button>
                        </div>

                        {{-- Messages --}}
                        <div class="pats-cw-messages" data-cw-messages aria-live="polite">
                            <article class="pats-cw-msg is-bot">
                                <div class="pats-cw-avatar">P</div>
                                <div class="pats-cw-bubble">
                                    <strong>Asistente PATS</strong>
                                    <p>¡Hola! ¿En qué te puedo ayudar hoy?</p>
                                </div>
                            </article>
                        </div>

                        {{-- Form --}}
                        <form class="pats-cw-form" data-cw-form autocomplete="off">
                            <textarea
                                data-cw-question
                                rows="1"
                                maxlength="1200"
                                placeholder="Escribe tu pregunta..."
                                required
                            ></textarea>
                            <button type="submit" data-cw-send>Enviar</button>
                        </form>

                    </section>

                    {{-- Result panel — hidden in floating mode --}}
                    <aside data-cw-result></aside>
                </main>

            </div>
        </section>
    </div>

    {{-- Trigger button --}}
    <button id="chatbot-toggle" aria-label="Abrir asistente PATS" aria-expanded="false">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M21 15C21 15.5304 20.7893 16.0391 20.4142 16.4142C20.0391 16.7893 19.5304 17 19 17H7L3 21V5C3 4.46957 3.21071 3.96086 3.58579 3.58579C3.96086 3.21071 4.46957 3 5 3H19C19.5304 3 20.0391 3.21071 20.4142 3.58579C20.7893 3.96086 21 4.46957 21 5V15Z" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
    </button>
</div>

<script src="{{ asset('js/catbot_widget.js') }}"></script>
<script>
(function () {
    var toggle   = document.getElementById('chatbot-toggle');
    var closeBtn = document.getElementById('chatbot-close-btn');
    var win      = document.getElementById('chatbot-window');

    function openChat() {
        win.classList.add('open');
        toggle.setAttribute('aria-expanded', 'true');
        var ta = win.querySelector('[data-cw-question]');
        if (ta) { setTimeout(function () { ta.focus(); }, 80); }
    }

    function closeChat() {
        win.classList.remove('open');
        toggle.setAttribute('aria-expanded', 'false');
    }

    toggle.addEventListener('click', function () {
        win.classList.contains('open') ? closeChat() : openChat();
    });

    if (closeBtn) { closeBtn.addEventListener('click', closeChat); }

    /* Keep bubble above fixed bottom banners (e.g. PWA prompt) */
    function adjustForBanner() {
        /* On mobile, CSS !important handles the bottom nav offset — skip JS override. */
        if (window.matchMedia('(max-width: 767px)').matches) { return; }

        var els = document.querySelectorAll(
            'body > div[style*="position:fixed"][style*="bottom:0"],' +
            'body > div[style*="position: fixed"][style*="bottom: 0"]'
        );
        var offset = 0;
        els.forEach(function (el) {
            if (el.id !== 'chatbot-bubble') { offset = Math.max(offset, el.offsetHeight); }
        });
        document.getElementById('chatbot-bubble').style.bottom = (offset > 0 ? offset + 8 : 24) + 'px';
    }

    new MutationObserver(adjustForBanner).observe(document.body, {
        childList: true, subtree: false, attributes: true, attributeFilter: ['style']
    });
    adjustForBanner();
})();
</script>
