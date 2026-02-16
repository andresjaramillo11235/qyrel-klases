/**
 * Función que consulta la API para obtener el enlace del video y lo abre en una nueva pestaña.
 * @param {string} apiDeviceId - Identificador del dispositivo GPS.
 */

/**
 * Función que consulta la API de Tracksolid para obtener el enlace del video y lo abre en una nueva pestaña.
 * @param {string} apiDeviceId - Identificador del dispositivo GPS.
 */
function openCamera(imei) {
    console.log("🔍 [DEBUG] Solicitando enlace de video para IMEI: ", imei);

    $.getJSON("../api/getVehicleApiTracksolid.php?api_device_id=" + imei, function (response) {
        
        console.log("✅ [DEBUG] Respuesta de la API del servidor:", response);

        // Verificamos si la respuesta contiene 'video_url'
        if (response.video_url) {
            console.log("📡 [DEBUG] Abriendo enlace de video:", response.video_url);
            window.open(response.video_url, "_blank");
        } else {
            console.warn("⚠️ [DEBUG] No se encontró video_url en la respuesta.");
            alert("⚠️ No se pudo obtener el enlace del video.");
        }
    }).fail(function (jqxhr, textStatus, error) {
        console.error("❌ [DEBUG] Error al obtener los datos del servidor:", textStatus, error);
    });
}
