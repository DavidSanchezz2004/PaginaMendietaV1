const items = [];
const tbody = document.querySelector("#tbl tbody");
const out = document.querySelector("#out");
const lookupMsg = document.querySelector("#lookupMsg");

// nuevos (si existen en tu blade actualizado)
const consultaMsg = document.querySelector("#consultaMsg");
const btnConsultar = document.querySelector("#btnConsultar");
const btnXml = document.querySelector("#btnXml");
const btnCdr = document.querySelector("#btnCdr");
const btnPdf = document.querySelector("#btnPdf");

function money(n) {
    return (Math.round(n * 100) / 100).toFixed(2);
}

function recalcTotals() {
    let total = 0;
    for (const it of items) total += it.cantidad * it.precio_unitario;
    const grav = total / 1.18;
    const igv = total - grav;

    document.querySelector("#t_tot").textContent = money(total);
    document.querySelector("#t_grav").textContent = money(grav);
    document.querySelector("#t_igv").textContent = money(igv);
}

function render() {
    tbody.innerHTML = "";
    items.forEach((it, idx) => {
        const tr = document.createElement("tr");
        tr.innerHTML = `
      <td>${idx + 1}</td>
      <td>${it.tipo}</td>
      <td>${it.codigo}</td>
      <td>${it.unidad}</td>
      <td>${it.descripcion}</td>
      <td>${it.cantidad}</td>
      <td>${money(it.precio_unitario)}</td>
      <td>${money(it.cantidad * it.precio_unitario)}</td>
      <td><button type="button" data-i="${idx}">Quitar</button></td>
    `;
        tr.querySelector("button").addEventListener("click", (e) => {
            const i = parseInt(e.target.getAttribute("data-i"));
            items.splice(i, 1);
            render();
            recalcTotals();
        });
        tbody.appendChild(tr);
    });
}

document.querySelector("#btnAdd").addEventListener("click", () => {
    const tipo = document.querySelector("#it_tipo").value;
    const codigo =
        document.querySelector("#it_codigo").value.trim() ||
        (tipo === "P" ? "P01" : "S01");
    const unidad = document.querySelector("#it_unidad").value;
    const descripcion =
        document.querySelector("#it_desc").value.trim() || "ITEM";
    const cantidad = parseFloat(
        document.querySelector("#it_cant").value || "1"
    );
    const precio_unitario = parseFloat(
        document.querySelector("#it_precio").value || "0"
    );

    items.push({
        tipo,
        codigo,
        unidad,
        descripcion,
        cantidad,
        precio_unitario,
    });
    render();
    recalcTotals();
});

function formToObj(fd) {
    const o = {};
    for (const [k, v] of fd.entries()) o[k] = v;
    return o;
}

// ------------------------------
// UX: mostrar/ocultar Crédito y Detracción
// ------------------------------
function syncPagoUI() {
    const sel = document.querySelector("#forma_pago");
    const wrapV = document.querySelector("#wrap_venc");
    const wrapC = document.querySelector("#wrap_cuota");

    if (!sel || !wrapV || !wrapC) return;

    const v = sel.value;
    const on = v === "credito";

    wrapV.style.display = on ? "" : "none";
    wrapC.style.display = on ? "" : "none";

    if (!on) {
        const fv = document.querySelector("#fecha_vencimiento");
        const mc = document.querySelector("#monto_cuota");
        if (fv) fv.value = "";
        if (mc) mc.value = "";
    }
}

function syncDetraUI() {
    const sel = document.querySelector("#detraccion_activa");
    const detras = document.querySelectorAll(".detra");
    if (!sel || !detras.length) return;

    const on = sel.value === "1";
    detras.forEach((el) => (el.style.display = on ? "" : "none"));

    if (!on) {
        const ids = [
            "detraccion_porcentaje",
            "detraccion_bien_servicio",
            "detraccion_medio_pago",
            "detraccion_cta_bn",
        ];
        ids.forEach((id) => {
            const el = document.querySelector("#" + id);
            if (el) el.value = "";
        });
    }
}

document.querySelector("#forma_pago")?.addEventListener("change", syncPagoUI);
document
    .querySelector("#detraccion_activa")
    ?.addEventListener("change", syncDetraUI);

// inicial
syncPagoUI();
syncDetraUI();

// ------------------------------
// Lookup (solo RUC para Factura 01)
// ------------------------------
async function lookup() {
    lookupMsg.textContent = "Buscando...";
    const tipo = document.querySelector("#cliente_tipo_doc").value;
    const num = document.querySelector("#cliente_numero_doc").value.trim();

    // Factura 01: solo RUC
    if (tipo !== "6") {
        lookupMsg.textContent = "Factura (01) solo permite RUC (tipo 6).";
        return;
    }

    if (!/^\d{11}$/.test(num)) {
        lookupMsg.textContent = "RUC inválido. Debe tener 11 dígitos.";
        return;
    }

    const base = window.FEASY.lookupRucUrl;
    const url = `${base}/${encodeURIComponent(num)}`;

    const res = await fetch(url, { headers: { Accept: "application/json" } });
    const data = await res.json();

    // ✅ Tu backend: { ok, status, json }
    if (!data?.ok || !data?.json?.success || !data?.json?.data) {
        lookupMsg.textContent = `No encontrado / error (HTTP ${
            data?.status ?? res.status
        }).`;
        out.style.display = "block";
        out.textContent = JSON.stringify(data, null, 2);
        return;
    }

    lookupMsg.textContent =
        "Datos encontrados ✅ (revisa y corrige si falta algo).";

    const j = data.json.data;

    const nombre =
        j.nombre_o_razon_social ||
        j.nombre_completo ||
        j.name ||
        [j.nombres, j.apellido_paterno, j.apellido_materno]
            .filter(Boolean)
            .join(" ") ||
        "";

    const direccion = j.direccion_completa || j.address || j.direccion || "";

    if (nombre) document.querySelector("#cliente_nombre").value = nombre;
    if (direccion)
        document.querySelector("#cliente_direccion").value = direccion;
}

document.querySelector("#btnLookup").addEventListener("click", lookup);
document
    .querySelector("#cliente_numero_doc")
    .addEventListener("keydown", (e) => {
        if (e.key === "Enter") {
            e.preventDefault();
            lookup();
        }
    });

// ------------------------------
// Preview
// ------------------------------
document.querySelector("#btnPreview").addEventListener("click", () => {
    const fd = new FormData(document.querySelector("#frm"));
    const base = formToObj(fd);
    const payload = { ...base, items };
    out.style.display = "block";
    out.textContent = JSON.stringify(payload, null, 2);
});

// ------------------------------
// Consultar + links
// ------------------------------
function hideDownloads() {
    if (btnXml) btnXml.style.display = "none";
    if (btnCdr) btnCdr.style.display = "none";
    if (btnPdf) btnPdf.style.display = "none";
}

function setDownloadLink(anchorEl, remoteUrl) {
    if (!anchorEl || !remoteUrl) return;
    // tu controller hace redirect()->away(url)
    anchorEl.href =
        window.FEASY.descargarUrl + "?url=" + encodeURIComponent(remoteUrl);
    anchorEl.style.display = "";
}

async function consultarActual() {
    if (!window.FEASY.consultarUrl) {
        alert("Falta FEASY.consultarUrl en el blade.");
        return;
    }
    hideDownloads();
    if (consultaMsg) consultaMsg.textContent = "Consultando...";

    const serie = (
        document.querySelector('input[name="serie"]')?.value || ""
    ).trim();
    const numero = (
        document.querySelector('input[name="numero"]')?.value || ""
    ).trim();

    if (!serie || !numero) {
        if (consultaMsg) consultaMsg.textContent = "Ingresa serie y número.";
        return;
    }

    const res = await fetch(window.FEASY.consultarUrl, {
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": window.FEASY.csrf,
            Accept: "application/json",
            "Content-Type": "application/json",
        },
        body: JSON.stringify({
            serie_documento: serie,
            numero_documento: numero,
        }),
    });

    const data = await res.json();
    out.style.display = "block";
    out.textContent = JSON.stringify(data, null, 2);

    if (!data?.success) {
        if (consultaMsg)
            consultaMsg.textContent = "Consulta falló (revisa el log).";
        return;
    }

    const d = data?.json?.data;
    if (consultaMsg) {
        consultaMsg.textContent =
            d?.mensaje_respuesta ||
            "Consulta OK ✅ (revisa botones de descarga).";
    }

    // links si están
    setDownloadLink(btnXml, d?.ruta_xml);
    setDownloadLink(btnCdr, d?.ruta_cdr);
    setDownloadLink(btnPdf, d?.ruta_reporte);
}

btnConsultar?.addEventListener("click", consultarActual);

// ------------------------------
// Submit emitir
// ------------------------------
document.querySelector("#frm").addEventListener("submit", async (e) => {
    e.preventDefault();

    if (items.length === 0) {
        alert("Agrega al menos 1 ítem");
        return;
    }

    hideDownloads();
    if (consultaMsg) consultaMsg.textContent = "";

    const fd = new FormData(e.target);
    const base = formToObj(fd);

    const res = await fetch(window.FEASY.storeUrl, {
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": window.FEASY.csrf,
            Accept: "application/json",
            "Content-Type": "application/json",
        },
        body: JSON.stringify({ ...base, items }),
    });

    const data = await res.json();
    out.style.display = "block";
    out.textContent = JSON.stringify(data, null, 2);

    // si emitió ok, auto-consultar para sacar rutas
    if (data?.success) {
        if (consultaMsg)
            consultaMsg.textContent =
                "Emitido ✅. Consultando para descargas...";
        try {
            await consultarActual();
        } catch (err) {
            if (consultaMsg)
                consultaMsg.textContent = "Emitido ✅. (Consulta falló)";
        }
    } else {
        if (consultaMsg)
            consultaMsg.textContent = "No emitió (revisa el error).";
    }
});
