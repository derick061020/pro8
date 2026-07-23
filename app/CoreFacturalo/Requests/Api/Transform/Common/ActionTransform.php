<?php

namespace App\CoreFacturalo\Requests\Api\Transform\Common;

use App\CoreFacturalo\Requests\Api\Transform\Functions;

class ActionTransform
{
    public static function transform($inputs)
    {
        if(key_exists('acciones', $inputs)) {
            $actions = $inputs['acciones'];
            return [
                'send_email' => Functions::valueKeyInArray($actions, 'enviar_email'),
                'send_xml_signed' => Functions::valueKeyInArray($actions, 'enviar_xml_firmado'),
                'format_pdf' => Functions::valueKeyInArray($actions, 'formato_pdf'),
                // Impresión automática server-side (app mozo). El wrapper "acciones"
                // se mantiene en español; estos campos nuevos van en inglés.
                'auto_print' => Functions::valueKeyInArray($actions, 'auto_print', false),
                'name_printer' => Functions::valueKeyInArray($actions, 'name_printer', null),
                'client_public_ip' => Functions::valueKeyInArray($actions, 'client_public_ip', null),
            ];
        }
        return null;
    }
}