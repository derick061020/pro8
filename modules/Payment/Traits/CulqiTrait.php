<?php
namespace Modules\Payment\Traits;

use Culqi\Culqi;

trait CulqiTrait 
{
    public function charge($credentails, $data)
    {
        $privateKey = $credentails['private_key'];
        $culqi = new Culqi([
            'api_key' => $privateKey,
        ]);

        $charge = $culqi->Charges->create($data);
        return $charge;
    }

}