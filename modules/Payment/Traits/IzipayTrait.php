<?php
namespace Modules\Payment\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

trait IzipayTrait
{

    const URL = "https://api.micuentaweb.pe/api-payment/V4/Charge/CreatePayment";
    const URL_GET_TRANSACTION = "https://api.micuentaweb.pe/api-payment/V4/Transaction/Get";

    /**
     * @param $credentials {username_izipay, password_izipay, publickey_izipay, sha256key_izipay}
     * @param $data
     */
    public function createPayment($credentials, $data) :? string
    {
        $token = base64_encode($credentials['username_izipay'] . ":" . $credentials['password_izipay']);
        $response = Http::withHeaders([
            'Authorization' => 'Basic ' . $token,
            'Content-Type' => 'application/json',
        ])->post(self::URL, $data);


        if ($response->successful()) {
            $body = $response->json();

            if (($body['status'] ?? null) === 'SUCCESS' && isset($body['answer']['formToken'])) {
                return $body['answer']['formToken'];
            }
        }

        return null;

    }

    public function verifyHash(Request $request, $sha256key): bool
    {

        $krAnswer = str_replace('\/', '/',  $request["kr-answer"]);
        
        $calculateHash = hash_hmac("sha256", $krAnswer, $sha256key);

        return ($calculateHash == $request["kr-hash"]);

    }

    /**
     * @link https://secure.micuentaweb.pe/doc/es-PE/rest/V4.0/api/playground/Transaction/Get
     */

    public function getTransaction($credentials, $uuid): ?array {
        $token = base64_encode($credentials['username_izipay'] . ":" . $credentials['password_izipay']);
        $response = Http::withHeaders([
            'Authorization' => 'Basic ' . $token,
            'Content-Type' => 'application/json',
        ])->post(self::URL_GET_TRANSACTION, [
            "uuid" => $uuid
        ]);

        return $response->successful() ? $response->json() : null;
    }
}