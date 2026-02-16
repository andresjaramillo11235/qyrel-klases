<?php
// formulario-estatal.php  —  Versión mínima
// AJUSTA estas 2 constantes:
$EXT_ID  = 'dcpoplmechpdnnpmmleheendfomcjjib';                     // <-- Pega el ID que ves en chrome://extensions
$FORM_URL = 'https://cea.sisec.co:8174/HomeOperador/Index';  // <-- URL exacta del formulario estatal

// Payload de prueba (ajústalo a tus textos/IDs reales)
$payload = [
  'estudiante' => 'Ana Gómez 1002003000',
  'categoria'  => 'B1',
  'instructor' => 'Juan Pérez 12345678',
  'vehiculo'   => 'ABC123',
  'lugar'      => 'Sede Principal',
  'tipo_clase' => 'Práctica',
  'aula'       => 'Aula 1',
  'direccion'  => 'Cra 10 # 20-30',
  'fecha'      => '2025-08-25'
];
?>
<!doctype html>
<html lang="es">
<meta charset="utf-8">
<title>Prueba mínima — Enviar a extensión</title>
<body>
  <h1>Prueba mínima</h1>
  <p>ID extensión: <code><?=htmlspecialchars($EXT_ID)?></code></p>
  <p>Formulario: <code><?=htmlspecialchars($FORM_URL)?></code></p>

  <button id="btn-enviar">Abrir y rellenar (solo autocompletar)</button>

  <pre id="log" style="background:#111;color:#eee;padding:10px;white-space:pre-wrap;"></pre>

<script>
const EXT_ID   = "<?= addslashes($EXT_ID) ?>";
const FORM_URL = "<?= addslashes($FORM_URL) ?>";
const PAYLOAD  = <?= json_encode($payload, JSON_UNESCAPED_UNICODE) ?>;

const log = (m) => {
  const el = document.getElementById('log');
  el.textContent += (typeof m === 'string' ? m : JSON.stringify(m)) + "\n";
  console.log(m);
};

document.getElementById('btn-enviar').onclick = function () {
  if (!window.chrome || !chrome.runtime || !chrome.runtime.sendMessage) {
    alert("No se detecta la API de Chrome Extensions. Abre esta página en Chrome/Edge con la extensión cargada.");
    return;
  }

  log("Enviando a extensión…");

  chrome.runtime.sendMessage(EXT_ID, {
    type: "RellenarFormularioEstatal",
    formUrl: FORM_URL,
    payload: PAYLOAD
  }, (resp) => {
    if (chrome.runtime.lastError) {
      log("chrome.runtime.lastError: " + chrome.runtime.lastError.message);
      alert("Error al comunicar con la extensión.");
      return;
    }
    log({ respuesta: resp });
    if (!resp || !resp.ok) {
      alert("La extensión respondió con error.");
      return;
    }
    alert("OK: payload enviado. Revisa la pestaña del portal (debe autocompletar).");
  });
};
</script>
</body>
</html>
