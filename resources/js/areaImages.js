const areaImageMap = {
  administraciondeempresas: 'administraciondeempresas.png',
  cienciasdelacomputacion: 'cienciasdelacomputacion.png',
  contaduriapublica: 'contaduriapublica.png',
  derecho: 'derecho.png',
  finanzas: 'finanzas.png',
  maestriaenderechoconstitucionalyamparo: 'maestriaenderechoconstitucionalyamparo.png',
  // agrega más aquí según tus imágenes disponibles...
}

/**
 * Normaliza una cadena y obtiene el nombre de imagen correspondiente.
 * @param {string} areaNombre
 * @returns {string} ruta relativa del archivo de imagen
 */
export function obtenerNombreImagenArea(areaNombre) {
  const normalizado = areaNombre
  .normalize('NFD')
  .replace(/[\u0300-\u036f]/g, '') // quitar acentos
  .replace(/[^a-zA-Z0-9]/g, '')    // quitar especiales y espacios
  .toLowerCase()

  return `/images/${areaImageMap[normalizado] ?? 'default.png'}`
}
