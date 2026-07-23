<?php

namespace Modules\WhatsAppBot\Services;

class IntentClassifier
{
    public const INTENT_YES = 'yes';
    public const INTENT_NO = 'no';
    public const INTENT_UNCLEAR = 'unclear';

    // SOLO confirmaciones inequivocas. Palabras como "vale", "ya", "listo",
    // "claro", "perfecto", "va", "correcto" son conectores comunes en frases
    // que NO son confirmacion ("vale, entonces dejalo en piero" no es un si).
    // Mejor errar al lado de UNCLEAR (delega al LLM) que confirmar por error.
    private const YES_KEYWORDS = [
        'si', 'sí', 'sí.', 'si.', 'sip', 'sii', 'sisi', 'sisí', 'sii.',
        'ok', 'okey', 'okay', 'okk',
        'dale', 'dele',
        'confirmo', 'confirmar', 'confirmado',
        'hazlo', 'hazlo!', 'emite', 'emítelo', 'emitelo', 'emítela', 'emitela',
        'emitir', 'emítela!', 'emitirla',
        'adelante', 'procede', 'procedamos',
        'afirmativo',
        '👍', '✅', '👌',
    ];

    // "para" y "alto" quedan fuera a propósito: son palabras comunes del
    // español (preposición / adjetivo) que casi nunca significan "detente",
    // pero aparecen constantemente en instrucciones normales ("para Juan",
    // "de gama alta") y disparaban falsos cancelados.
    private const NO_KEYWORDS = [
        'no', 'no.', 'nop', 'nope', 'cancela', 'cancelar', 'cancelado',
        'espera', 'aguarda', 'aguanta', 'detente',
        'mejor no', 'no por ahora', 'aún no', 'todavía no', 'todavia no',
        'negativo', 'olvídalo', 'olvidalo', '👎', '❌',
    ];

    // Mensajes largos casi nunca son un sí/no simple - son instrucciones
    // ("agrégale también el producto precio no exacto"). Sin este limite,
    // una palabra suelta como "no" o "para" dentro de una frase larga (o de
    // un nombre de producto/cliente) se confunde con cancelar, y el mensaje
    // nunca llega al LLM para que lo interprete en contexto.
    private const MAX_WORDS_FOR_KEYWORD_MATCH = 5;

    public function classify(string $message): string
    {
        $normalized = $this->normalize($message);

        if ($normalized === '') {
            return self::INTENT_UNCLEAR;
        }

        // normalize() ya colapsa espacios a uno solo, asi que contar
        // separadores es seguro con tildes/ñ (str_word_count no es UTF-8 safe).
        if (substr_count($normalized, ' ') + 1 > self::MAX_WORDS_FOR_KEYWORD_MATCH) {
            return self::INTENT_UNCLEAR;
        }

        $isNegative = $this->matches($normalized, self::NO_KEYWORDS);
        $isAffirmative = $this->matches($normalized, self::YES_KEYWORDS);

        if ($isNegative && !$isAffirmative) {
            return self::INTENT_NO;
        }

        if ($isAffirmative && !$isNegative) {
            return self::INTENT_YES;
        }

        return self::INTENT_UNCLEAR;
    }

    private function normalize(string $message): string
    {
        $message = trim(mb_strtolower($message));
        $message = preg_replace('/\s+/u', ' ', $message);
        return $message;
    }

    private function matches(string $normalized, array $keywords): bool
    {
        foreach ($keywords as $keyword) {
            $kw = preg_quote($keyword, '/');
            if (preg_match('/(^|\s|[[:punct:]])' . $kw . '($|\s|[[:punct:]])/u', $normalized)) {
                return true;
            }
        }
        return false;
    }
}
