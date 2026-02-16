function generarTokenURL(filtros) {
    try {
        const json = JSON.stringify(filtros);
        return btoa(json);
    } catch (e) {
        console.error('Error al generar token de filtros:', e);
        return '';
    }
}
