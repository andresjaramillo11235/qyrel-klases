<?php
function capitalizarPalabras($cadena)
{
  // Convertir toda la cadena a minúsculas
  $cadena = strtolower($cadena);
  // Capitalizar la primera letra de cada palabra
  $cadena = ucwords($cadena);
  return $cadena;
}
