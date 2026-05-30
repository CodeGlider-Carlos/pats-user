{{-- Floating Chatbot Bubble --}}
<style>
    #chatbot-bubble {
        position: fixed;
        bottom: 24px;
        left: 24px;
        z-index: 100000;
        font-family: 'DM Sans', sans-serif;
    }

    #chatbot-toggle {
        width: 56px;
        height: 56px;
        border-radius: 50%;
        background: linear-gradient(135deg, #2d6cdf, #1a4aa8);
        border: none;
        cursor: pointer;
        box-shadow: 0 4px 16px rgba(45, 108, 223, 0.45);
        display: flex;
        align-items: center;
        justify-content: center;
        transition: transform 0.2s, box-shadow 0.2s;
        color: #fff;
        font-size: 22px;
    }

    #chatbot-toggle:hover {
        transform: scale(1.08);
        box-shadow: 0 6px 20px rgba(45, 108, 223, 0.55);
    }

    #chatbot-window {
        display: none;
        flex-direction: column;
        position: absolute;
        bottom: 68px;
        left: 0;
        width: 340px;
        max-width: calc(100vw - 48px);
        height: 460px;
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 8px 32px rgba(0,0,0,0.16);
        overflow: hidden;
    }

    #chatbot-window.open {
        display: flex;
    }

    #chatbot-header {
        background: linear-gradient(135deg, #2d6cdf, #1a4aa8);
        color: #fff;
        padding: 14px 16px;
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 15px;
        font-weight: 600;
        flex-shrink: 0;
    }

    #chatbot-header .bot-avatar {
        width: 32px;
        height: 32px;
        background: rgba(255,255,255,0.2);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
    }

    #chatbot-header .close-btn {
        margin-left: auto;
        background: none;
        border: none;
        color: #fff;
        font-size: 18px;
        cursor: pointer;
        line-height: 1;
        opacity: 0.8;
    }

    #chatbot-header .close-btn:hover { opacity: 1; }

    #chatbot-messages {
        flex: 1;
        overflow-y: auto;
        padding: 14px;
        display: flex;
        flex-direction: column;
        gap: 10px;
        background: #f5f7fb;
    }

    .chat-msg {
        max-width: 85%;
        padding: 9px 13px;
        border-radius: 12px;
        font-size: 13.5px;
        line-height: 1.5;
        word-break: break-word;
    }

    .chat-msg.bot {
        background: #fff;
        color: #333;
        align-self: flex-start;
        box-shadow: 0 1px 4px rgba(0,0,0,0.08);
        border-bottom-left-radius: 4px;
    }

    .chat-msg.user {
        background: linear-gradient(135deg, #2d6cdf, #1a4aa8);
        color: #fff;
        align-self: flex-end;
        border-bottom-right-radius: 4px;
    }

    #chatbot-input-area {
        display: flex;
        gap: 8px;
        padding: 10px 12px;
        background: #fff;
        border-top: 1px solid #eee;
        flex-shrink: 0;
    }

    #chatbot-input {
        flex: 1;
        border: 1px solid #dde2ef;
        border-radius: 20px;
        padding: 8px 14px;
        font-size: 13.5px;
        outline: none;
        font-family: 'DM Sans', sans-serif;
        transition: border-color 0.2s;
    }

    #chatbot-input:focus { border-color: #2d6cdf; }

    #chatbot-send {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: #2d6cdf;
        border: none;
        cursor: pointer;
        color: #fff;
        font-size: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        transition: background 0.2s;
    }

    #chatbot-send:hover { background: #1a4aa8; }

    .chat-msg.bot a {
        color: #2d6cdf;
        text-decoration: underline;
        word-break: break-all;
    }

    .chat-msg.bot a:hover { color: #1a4aa8; }

    @media (max-width: 480px) {
        #chatbot-bubble {
            bottom: 16px;
            left: 16px;
        }
        #chatbot-window {
            width: calc(100vw - 32px);
            height: 400px;
        }
    }
</style>

<div id="chatbot-bubble">
    <div id="chatbot-window" role="dialog" aria-label="Asistente PATS">
        <div id="chatbot-header">
            <div class="bot-avatar">🤖</div>
            <span>Asistente PATS</span>
            <button class="close-btn" id="chatbot-close" aria-label="Cerrar chat">✕</button>
        </div>
        <div id="chatbot-messages"></div>
        <div id="chatbot-input-area">
            <input type="text" id="chatbot-input" placeholder="Escribe tu pregunta..." autocomplete="off" />
            <button id="chatbot-send" aria-label="Enviar">&#9658;</button>
        </div>
    </div>
    <button id="chatbot-toggle" aria-label="Abrir asistente">💬</button>
</div>

<script>
(function () {
    const toggle   = document.getElementById('chatbot-toggle');
    const closeBtn = document.getElementById('chatbot-close');
    const win      = document.getElementById('chatbot-window');
    const messages = document.getElementById('chatbot-messages');
    const input    = document.getElementById('chatbot-input');
    const sendBtn  = document.getElementById('chatbot-send');

    let knowledge = [];
    let loaded    = false;

    function openChat() {
        win.classList.add('open');
        toggle.setAttribute('aria-expanded', 'true');
        if (!loaded) { loadKnowledge(); }
        setTimeout(() => input.focus(), 100);
    }

    function closeChat() {
        win.classList.remove('open');
        toggle.setAttribute('aria-expanded', 'false');
    }

    toggle.addEventListener('click', () => win.classList.contains('open') ? closeChat() : openChat());
    closeBtn.addEventListener('click', closeChat);

    // Push bubble above any fixed bottom banner (e.g. PWA install prompt)
    function adjustForBanner() {
        var fixed = Array.from(document.querySelectorAll('body > div[style*="position:fixed"][style*="bottom:0"], body > div[style*="position: fixed"][style*="bottom: 0"]'));
        var offset = 0;
        fixed.forEach(function(el) {
            if (el.id !== 'chatbot-bubble') { offset = Math.max(offset, el.offsetHeight); }
        });
        document.getElementById('chatbot-bubble').style.bottom = (offset > 0 ? offset + 8 : 24) + 'px';
    }
    var bannerObserver = new MutationObserver(adjustForBanner);
    bannerObserver.observe(document.body, { childList: true, subtree: false, attributes: true, attributeFilter: ['style'] });
    adjustForBanner();

    function renderLinks(text) {
        return text
            .replace(/\[([^\]]+)\]\(([^\)]+)\)/g, function(_, label, href) {
                var external = /^https?:/.test(href);
                return '<a href="' + href + '"' + (external ? ' target="_blank" rel="noopener noreferrer"' : '') + '>' + label + '</a>';
            })
            .replace(/(^|[\s(])(https?:\/\/[^\s)]+)/g, '$1<a href="$2" target="_blank" rel="noopener noreferrer">$2</a>');
    }

    function addMessage(text, role) {
        const el = document.createElement('div');
        el.classList.add('chat-msg', role);
        if (role === 'bot') {
            el.innerHTML = renderLinks(text);
        } else {
            el.textContent = text;
        }
        messages.appendChild(el);
        messages.scrollTop = messages.scrollHeight;
    }

    function loadKnowledge() {
        fetch('{{ route("chatbot.knowledge") }}')
            .then(r => r.text())
            .then(md => {
                knowledge = parseMarkdown(md);
                loaded = true;
                addMessage('¡Hola! Soy el asistente PATS. ¿En qué puedo ayudarte hoy?', 'bot');
            })
            .catch(() => addMessage('No pude cargar la información. Intenta más tarde.', 'bot'));
    }

    function parseMarkdown(md) {
        const sections = [];
        const blocks   = md.split(/\n##\s+/);
        blocks.forEach(block => {
            const lines    = block.trim().split('\n');
            const question = lines[0].replace(/^#+\s*/, '').trim();
            const answer   = lines.slice(1).join(' ').replace(/\s+/g, ' ').trim();
            if (question && answer) {
                sections.push({ question, answer });
            }
        });
        return sections;
    }

    function tokenize(text) {
        return text.toLowerCase()
            .normalize('NFD').replace(/[̀-ͯ]/g, '')
            .replace(/[^a-z0-9\s]/g, '')
            .split(/\s+/)
            .filter(w => w.length > 2);
    }

    function findAnswer(query) {
        if (!knowledge.length) {
            return 'Aún estoy cargando la información. Por favor espera un momento.';
        }

        const qTokens = tokenize(query);
        let best = null, bestScore = 0;

        knowledge.forEach(section => {
            const haystack = tokenize(section.question + ' ' + section.answer);
            const score    = qTokens.reduce((acc, t) => acc + (haystack.includes(t) ? 1 : 0), 0);
            if (score > bestScore) { bestScore = score; best = section; }
        });

        if (bestScore === 0 || !best) {
            return 'No encontré información sobre eso. Puedes contactar a soporte desde la sección "Soporte" en el menú.';
        }

        return best.answer;
    }

    function handleSend() {
        const text = input.value.trim();
        if (!text) return;
        addMessage(text, 'user');
        input.value = '';
        setTimeout(() => addMessage(findAnswer(text), 'bot'), 300);
    }

    sendBtn.addEventListener('click', handleSend);
    input.addEventListener('keydown', e => { if (e.key === 'Enter') { handleSend(); } });
})();
</script>
