<?php

declare(strict_types=1);

namespace App\Data;

use App\Enums\TextoLanding;
use Illuminate\Support\HtmlString;

/** Textos de la landing: lo guardado en el panel o, si no hay, el texto por defecto. */
final class TextosLanding
{
    /** @param array<string, string> $guardados clave => valor */
    public function __construct(private readonly array $guardados)
    {
    }

    public function texto(TextoLanding $clave): string
    {
        $valor = trim($this->guardados[$clave->value] ?? '');

        return $valor !== '' ? $valor : $clave->porDefecto();
    }

    /** Escapado, con *palabra* -> <em>palabra</em> (cursiva dorada de los títulos). */
    public function html(TextoLanding $clave): HtmlString
    {
        $seguro = e($this->texto($clave));

        return new HtmlString((string) preg_replace('/\*(.+?)\*/u', '<em>$1</em>', $seguro));
    }

    /** Texto plano sin los asteriscos (para atributos y metadatos). */
    public function plano(TextoLanding $clave): string
    {
        return str_replace('*', '', $this->texto($clave));
    }

    /** @return list<string> párrafos separados por una línea en blanco */
    public function parrafos(TextoLanding $clave): array
    {
        return array_values(array_filter(
            array_map('trim', preg_split('/\R\s*\R/u', $this->texto($clave)) ?: []),
        ));
    }
}
