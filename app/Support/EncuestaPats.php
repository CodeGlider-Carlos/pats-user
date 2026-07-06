<?php

namespace App\Support;

class EncuestaPats
{
    public const HOSPITAL = 'HOSPITAL';
    public const CONSULTA = 'CONSULTA';
    public const LABORATORIO = 'LABORATORIO';
    public const FARMACIA = 'FARMACIA';
    public const IMAGENOLOGIA = 'IMAGENOLOGIA';

    /**
     * Clasifica el tipo de servicio a partir del texto de la columna
     * `modelo` de tarjetas_misional.
     */
    public static function clasificarServicio(?string $modelo): string
    {
        $m = self::normalizar((string) $modelo);

        return match (true) {
            str_contains($m, 'hospital') => self::HOSPITAL,
            str_contains($m, 'consulta') => self::CONSULTA,
            str_contains($m, 'imagen') => self::IMAGENOLOGIA,
            str_contains($m, 'laboratorio') => self::LABORATORIO,
            str_contains($m, 'farmacia') => self::FARMACIA,
            default => self::CONSULTA,
        };
    }

    /**
     * Etiqueta legible del tipo de servicio.
     */
    public static function etiquetaTipo(string $tipo): string
    {
        return [
            self::HOSPITAL => 'Atención Hospitalaria',
            self::CONSULTA => 'Consulta de Especialidad',
            self::LABORATORIO => 'Laboratorio',
            self::FARMACIA => 'Farmacia',
            self::IMAGENOLOGIA => 'Imagenología',
        ][$tipo] ?? 'Servicio';
    }

    /**
     * Definición completa del cuestionario, en orden.
     *
     * @return array<int, array{key:string, titulo:string, tipo:string, comentario:bool, servicios:array<int,string>}>
     */
    public static function preguntas(): array
    {
        $todos = [self::HOSPITAL, self::CONSULTA, self::LABORATORIO, self::FARMACIA, self::IMAGENOLOGIA];

        return [
            ['key' => 'adm_recepcion', 'titulo' => 'Atención por el personal de Admisión / Recepción', 'tipo' => 'escala5', 'comentario' => true, 'servicios' => $todos],
            ['key' => 'urgencias', 'titulo' => 'Atención en el Servicio de Urgencias', 'tipo' => 'escala5', 'comentario' => true, 'servicios' => [self::HOSPITAL]],
            ['key' => 'medico', 'titulo' => 'Atención recibida por el médico', 'tipo' => 'escala5', 'comentario' => true, 'servicios' => [self::HOSPITAL, self::CONSULTA]],
            ['key' => 'enfermeria', 'titulo' => 'Atención de Enfermería', 'tipo' => 'escala5', 'comentario' => true, 'servicios' => [self::HOSPITAL]],
            ['key' => 'personal', 'titulo' => 'Atención del personal', 'tipo' => 'escala5', 'comentario' => true, 'servicios' => [self::LABORATORIO, self::FARMACIA, self::IMAGENOLOGIA]],
            ['key' => 'instalaciones', 'titulo' => 'Instalaciones y Limpieza', 'tipo' => 'escala5', 'comentario' => true, 'servicios' => $todos],
            ['key' => 'pats_explicacion', 'titulo' => '¿El personal le explicó de forma clara los beneficios y descuentos del Pasaporte a tu Salud?', 'tipo' => 'escala5', 'comentario' => true, 'servicios' => $todos],
            ['key' => 'pats_descuentos', 'titulo' => '¿Qué tan satisfecho está con los descuentos de Pasaporte a tu Salud?', 'tipo' => 'escala5', 'comentario' => true, 'servicios' => $todos],
            ['key' => 'nps', 'titulo' => '¿Qué probabilidad existe de que recomiende Pasaporte a tu Salud a familiares o amigos?', 'tipo' => 'escala10', 'comentario' => true, 'servicios' => $todos],
            ['key' => 'lo_que_mas_gusto', 'titulo' => '¿Qué fue lo que más le gustó de su experiencia?', 'tipo' => 'texto', 'comentario' => false, 'servicios' => $todos],
            ['key' => 'que_mejorar', 'titulo' => '¿Qué aspecto considera que debemos mejorar?', 'tipo' => 'texto', 'comentario' => false, 'servicios' => $todos],
        ];
    }

    /**
     * Preguntas aplicables a un tipo de servicio, renumeradas para mostrar.
     *
     * @return array<int, array{key:string, numero:int, titulo:string, tipo:string, comentario:bool}>
     */
    public static function preguntasParaTipo(string $tipo): array
    {
        $out = [];
        $numero = 0;

        foreach (self::preguntas() as $pregunta) {
            if (! in_array($tipo, $pregunta['servicios'], true)) {
                continue;
            }

            $numero++;
            unset($pregunta['servicios']);
            $pregunta['numero'] = $numero;
            $out[] = $pregunta;
        }

        return $out;
    }

    private static function normalizar(string $texto): string
    {
        $texto = mb_strtolower($texto);

        return strtr($texto, [
            'á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u', 'ü' => 'u',
        ]);
    }
}
