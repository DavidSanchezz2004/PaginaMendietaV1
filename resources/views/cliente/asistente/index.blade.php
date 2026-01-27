@extends('layouts.app-cliente')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/asistente.css') }}">
@endpush

@section('content')
<div class="ai-shell">
  {{-- Empty state (tipo ChatGPT) --}}
  <section id="aiEmpty" class="ai-empty" aria-label="Bienvenida asistente">
    <div class="ai-empty-avatar" aria-hidden="true">
      {{-- Puedes reemplazar por tu logo --}}
      <svg viewBox="0 0 24 24" fill="none" stroke-width="2">
        <path d="M12 2a7 7 0 0 0-7 7v3a4 4 0 0 0 4 4h1l2 4 2-4h1a4 4 0 0 0 4-4V9a7 7 0 0 0-7-7z"></path>
        <path d="M9.5 10.5h.01M14.5 10.5h.01"></path>
      </svg>
    </div>

    <h1 class="ai-empty-title">Hola, soy tu Asistente IA</h1>
    <p class="ai-empty-sub">Respondo únicamente con documentos internos de nuestro <br> Estudio contable Mendieta</p>

    <div class="ai-suggest" id="chips">
      <button type="button" class="chip" data-q="¿Qué libros contables debo llevar según mi régimen?">📚 Libros</button>
      <button type="button" class="chip" data-q="¿Cuál es el plazo para declarar IGV este mes?">🗓️ Plazos</button>
      <button type="button" class="chip" data-q="¿Qué es una detracción y cuándo aplica?">🧾 Detracciones</button>
      <button type="button" class="chip" data-q="¿Cómo regularizar una omisión de declaración?">🧠 Regularización</button>
    </div>
  </section>

  {{-- Chat stream --}}
  <main id="chatWrap" class="ai-chatwrap">
    <div id="chat" class="ai-chat" aria-live="polite"></div>
  </main>

  {{-- Composer sticky --}}
  <footer class="ai-composer">
    <form id="assistantForm" class="composer-inner">
      @csrf

      <textarea
        id="q"
        name="text"
        rows="1"
        maxlength="1200"
        placeholder="Pregúntame lo que quieras..."
        autocomplete="off"
      ></textarea>

      <button id="btnAsk" type="submit" class="send" aria-label="Enviar">
  <svg viewBox="0 0 24 24" fill="none" stroke-width="2" aria-hidden="true">
    <!-- burbuja -->
    <path d="M21 15a4 4 0 0 1-4 4H7l-4 3V7a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4z"></path>
    <!-- flecha -->
    <path d="M9 12h6"></path>
    <path d="M12 9l3 3-3 3"></path>
  </svg>
</button>

    </form>

    <div class="ai-helper">
      <button id="btnClear" type="button" class="link"></button>
      <span id="status" class="status"></span>
      <span class="ai-footnote" >Este asistente puede cometer errores. Validar la  información importante.</span>
    </div>
  </footer>
</div>

<script>
(function () {
  const form = document.getElementById('assistantForm');
  const q = document.getElementById('q');
  const btnAsk = document.getElementById('btnAsk');
  const btnClear = document.getElementById('btnClear');
  const statusEl = document.getElementById('status');
  const chat = document.getElementById('chat');
  const chips = document.getElementById('chips');
  const aiEmpty = document.getElementById('aiEmpty');

  const endpoint = @json(route('cliente.assistant.query'));

  function scrollToBottom() {
    chat.scrollTop = chat.scrollHeight;
  }

  function setLoading(isLoading) {
    btnAsk.disabled = isLoading;
    q.disabled = isLoading;
  }

  function escapeHtml(str) {
    return String(str)
      .replaceAll('&','&amp;')
      .replaceAll('<','&lt;')
      .replaceAll('>','&gt;')
      .replaceAll('"','&quot;')
      .replaceAll("'","&#039;");
  }

  function updateEmptyState() {
    const hasMsgs = chat.querySelector('.msg');
    aiEmpty.style.display = hasMsgs ? 'none' : '';
  }

  function addUserMessage(text) {
    const el = document.createElement('div');
    el.className = 'msg user';
    el.innerHTML = `<div class="pill">${escapeHtml(text)}</div>`;
    chat.appendChild(el);
    updateEmptyState();
    scrollToBottom();
  }

  function addBotTyping() {
    const el = document.createElement('div');
    el.className = 'msg bot';
    el.innerHTML = `
      <div class="botblock">
        <div class="typing">
          Analizando documentos
          <span class="dots">
            <span class="dot"></span><span class="dot"></span><span class="dot"></span>
          </span>
        </div>
      </div>
    `;
    chat.appendChild(el);
    updateEmptyState();
    scrollToBottom();
    return el;
  }

  function setBotAnswer(typingEl, answer, sources) {
    const block = typingEl.querySelector('.botblock');
    const safeAnswer = escapeHtml(answer || 'No hay respuesta disponible.');

    let conf = null;
    if (Array.isArray(sources) && sources.length) {
      const best = sources
        .map(s => typeof s.score === 'number' ? s.score : null)
        .filter(v => v !== null)
        .sort((a,b)=>b-a)[0];
      if (typeof best === 'number') conf = best;
    }

    const confLabel =
      conf === null ? '—' :
      conf >= 0.82 ? 'Alta' :
      conf >= 0.65 ? 'Media' : 'Baja';

    const confPct = conf === null ? null : Math.round(conf * 100);

    let sourcesHtml = '';
    if (Array.isArray(sources) && sources.length) {
      const items = sources.slice(0, 6).map((s) => {
        const docId = String(s.doc_id ?? '—');
        const chunkId = String(s.chunk_id ?? '—');
        return `
          <span class="src-pill" title="doc: ${escapeHtml(docId)} • chunk: ${escapeHtml(chunkId)}">
            ${escapeHtml(docId)} <span class="mini">#${escapeHtml(chunkId)}</span>
          </span>
        `;
      }).join('');

      const extra = sources.length > 6 ? `<span class="src-more">+${sources.length - 6} más</span>` : '';

      sourcesHtml = `
        <details class="sources">
          <summary>
            Fuentes
            <span class="hint">${sources.length} fragmentos</span>
          </summary>
          <div class="src-wrap">
            ${items}
            ${extra}
          </div>
        </details>
      `;
    }

    block.innerHTML = `
      <div class="bothead">
        <span class="tag">Respuesta</span>
        <span class="metric">Confianza: <b>${confLabel}</b>${confPct !== null ? ` (${confPct}%)` : ''}</span>
      </div>
      <div class="bottext">${safeAnswer}</div>
      ${sourcesHtml}
    `;

    typingEl.classList.add('pop-in');
    setTimeout(() => typingEl.classList.remove('pop-in'), 240);

    scrollToBottom();
  }

  function autoGrow() {
    q.style.height = 'auto';
    q.style.height = Math.min(q.scrollHeight, 140) + 'px';
  }
  q.addEventListener('input', autoGrow);
  autoGrow();

  function clearChat() {
    chat.innerHTML = '';
    q.value = '';
    autoGrow();
    statusEl.textContent = '';
    q.focus();
    updateEmptyState();
  }
  btnClear.addEventListener('click', clearChat);

  chips?.addEventListener('click', (e) => {
    const chip = e.target.closest('.chip');
    if (!chip) return;
    q.value = chip.dataset.q || '';
    autoGrow();
    q.focus();
  });

  form.addEventListener('submit', async (e) => {
    e.preventDefault();

    const text = (q.value || '').trim();
    if (text.length < 2) {
      statusEl.textContent = 'Escribe una consulta más específica.';
      return;
    }

    addUserMessage(text);
    q.value = '';
    autoGrow();

    const typingEl = addBotTyping();
    setLoading(true);

    try {
      const res = await fetch(endpoint, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
        },
        body: JSON.stringify({ text }),
      });

      const data = await res.json().catch(() => ({}));

      if (!res.ok || data.ok === false) {
        setBotAnswer(typingEl, data.message || 'No se pudo procesar la consulta.', []);
        return;
      }

      setBotAnswer(typingEl, data.answer, data.sources || []);
    } catch (err) {
      setBotAnswer(typingEl, 'Error de red consultando al asistente.', []);
    } finally {
      setLoading(false);
    }
  });

  q.addEventListener('keydown', (e) => {
    if (e.key === 'Enter' && !e.shiftKey) {
      e.preventDefault();
      form.requestSubmit();
    }
  });

  // init
  updateEmptyState();
})();
</script>
@endsection
