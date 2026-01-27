(function () {
    const endpoint = window.ASSISTANT.endpoint;
    const csrf = window.ASSISTANT.csrf;

    const form = document.getElementById("assistantForm");
    const q = document.getElementById("q");
    const chat = document.getElementById("chat");

    async function send(text) {
        const res = await fetch(endpoint, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                Accept: "application/json",
                "X-CSRF-TOKEN": csrf,
            },
            body: JSON.stringify({ text }),
        });

        return res.json();
    }

    function scrollToBottom() {
        chat.scrollTop = chat.scrollHeight;
    }

    function setLoading(isLoading) {
        btnAsk.disabled = isLoading;
        q.disabled = isLoading;
        statusEl.textContent = isLoading ? "Consultando…" : "";
    }

    function escapeHtml(str) {
        return String(str)
            .replaceAll("&", "&amp;")
            .replaceAll("<", "&lt;")
            .replaceAll(">", "&gt;")
            .replaceAll('"', "&quot;")
            .replaceAll("'", "&#039;");
    }

    function addUserMessage(text) {
        const el = document.createElement("div");
        el.className = "msg row user";
        el.innerHTML = `
      <div class="bubble">
        <div class="text">${escapeHtml(text)}</div>
      </div>
    `;
        chat.appendChild(el);
        scrollToBottom();
    }

    function addBotTyping() {
        const el = document.createElement("div");
        el.className = "msg row bot";
        el.innerHTML = `
      <div class="avatar" aria-hidden="true">
        <svg viewBox="0 0 24 24" fill="none" stroke-width="2">
          <path d="M12 2a7 7 0 0 0-7 7v3a4 4 0 0 0 4 4h1l2 4 2-4h1a4 4 0 0 0 4-4V9a7 7 0 0 0-7-7z"></path>
          <path d="M9.5 10.5h.01M14.5 10.5h.01"></path>
        </svg>
      </div>
      <div class="bubble">
        <div class="typing">
          <span class="dots">
            <span class="dot"></span><span class="dot"></span><span class="dot"></span>
          </span>
        </div>
      </div>
    `;
        chat.appendChild(el);
        scrollToBottom();
        return el;
    }

    function setBotAnswer(typingEl, answer, sources) {
        const bubble = typingEl.querySelector(".bubble");
        const safeAnswer = escapeHtml(answer || "No hay respuesta disponible.");

        let sourcesHtml = "";
        if (Array.isArray(sources) && sources.length) {
            const items = sources
                .map((s) => {
                    const docId = s.doc_id ?? "—";
                    const chunkId = s.chunk_id ?? "—";
                    const score =
                        typeof s.score === "number"
                            ? s.score.toFixed(2)
                            : s.score ?? "—";
                    return `<li>doc: ${escapeHtml(docId)} | chunk: ${escapeHtml(
                        chunkId
                    )} | score: ${escapeHtml(score)}</li>`;
                })
                .join("");

            sourcesHtml = `
        <div class="sources">
          Fuentes
          <ul>${items}</ul>
        </div>
      `;
        }

        bubble.innerHTML = `
      <div class="text">${safeAnswer}</div>
      ${sourcesHtml}
    `;
        scrollToBottom();
    }

    // Auto-grow textarea
    function autoGrow() {
        q.style.height = "auto";
        q.style.height = Math.min(q.scrollHeight, 140) + "px";
    }
    q.addEventListener("input", autoGrow);
    autoGrow();

    btnClear.addEventListener("click", () => {
        // limpia todo excepto el primer mensaje (opcional)
        const msgs = Array.from(chat.querySelectorAll(".msg"));
        msgs.slice(1).forEach((m) => m.remove());
        q.value = "";
        autoGrow();
        statusEl.textContent = "";
        q.focus();
        scrollToBottom();
    });

    form.addEventListener("submit", async (e) => {
        e.preventDefault();

        const text = (q.value || "").trim();
        if (text.length < 2) {
            statusEl.textContent = "Escribe una consulta más específica.";
            return;
        }

        addUserMessage(text);
        q.value = "";
        autoGrow();
        const typingEl = addBotTyping();

        setLoading(true);

        try {
            const res = await fetch(endpoint, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    Accept: "application/json",
                    "X-CSRF-TOKEN": document.querySelector(
                        'input[name="_token"]'
                    ).value,
                },
                body: JSON.stringify({ text }),
            });

            const data = await res.json().catch(() => ({}));

            if (!res.ok || data.ok === false) {
                const msg = data.message || "No se pudo procesar la consulta.";
                setBotAnswer(typingEl, msg, []);
                statusEl.textContent = "";
                return;
            }

            setBotAnswer(typingEl, data.answer, data.sources || []);
            statusEl.textContent = "";
        } catch (err) {
            setBotAnswer(
                typingEl,
                "Error de red consultando al asistente.",
                []
            );
        } finally {
            setLoading(false);
        }
    });

    // Enter para enviar (Shift+Enter para salto de línea)
    q.addEventListener("keydown", (e) => {
        if (e.key === "Enter" && !e.shiftKey) {
            e.preventDefault();
            form.requestSubmit();
        }
    });
})();
