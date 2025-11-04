// uploader.js - handles upload without reload, shows errors from server
document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("uploadForm");
    const input = document.getElementById("fileInput");
    const msg = document.getElementById("uploadMessage");
    const filesTable = document.getElementById("filesTable");

    form.addEventListener("submit", async function (e) {
        e.preventDefault();
        msg.textContent = "";

        if (!input.files.length) {
            msg.textContent = "Selecciona un archivo";
            return;
        }
        const f = input.files[0];
        // client-side quick ext check (UX only) - server is authoritative
        const ext = f.name.split(".").pop().toLowerCase();
        if (window.appData.forbidden.includes(ext)) {
            msg.textContent = `Error: El tipo de archivo '.${ext}' no está permitido (cliente)`;
            return;
        }

        const data = new FormData();
        data.append("file", f);
        data.append("_token", window.appData.csrfToken);

        const btn = document.getElementById("uploadBtn");
        btn.disabled = true;
        btn.textContent = "Subiendo...";

        try {
            const res = await fetch("/upload", { method: "POST", body: data });
            const json = await res.json();
            if (!res.ok) {
                msg.textContent = json.error || "Error subiendo";
            } else if (json.success) {
                msg.textContent = "Subida correcta";
                // append to table
                const tr = document.createElement("tr");
                const kb = (json.file.size / 1024).toFixed(2);
                tr.innerHTML = `<td>${json.file.name}</td><td>${kb} KB</td><td><a href="/files/download/${json.file.id}">Descargar</a></td>`;
                filesTable.prepend(tr);
                input.value = "";
            }
        } catch (err) {
            msg.textContent = "Error de red";
            console.error(err);
        } finally {
            btn.disabled = false;
            btn.textContent = "Subir";
        }
    });
});
