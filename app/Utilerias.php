<?php

namespace App;

class Utilerias
{
  /**
   * Normaliza un texto: quita acentos, caracteres especiales, normaliza espacios y convierte a mayúsculas.
   *
   * @param string $texto
   * @return string
   */
  public static function normalizarTexto(string $texto): string
  {
    // Quitar acentos (incluye ñ → n)
    $texto = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $texto);

    // Quitar caracteres que no sean letras, números o espacios
    $texto = preg_replace('/[^A-Za-z0-9 ]/', '', $texto);

    // Reemplazar múltiples espacios por uno solo
    $texto = preg_replace('/\s+/', ' ', $texto);

    // Quitar espacios al inicio y final
    $texto = trim($texto);

    // Convertir a mayúsculas
    return strtoupper($texto);
  }
}
