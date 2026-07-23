<?php
namespace Modules\Ecommerce\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Models\Tenant\Configuration;
use App\Models\Tenant\Order;
use App\Models\Tenant\StatusOrder;
use Modules\Ecommerce\Mail\OrderStatusChanged;

class SendOrderStatusEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 2;

    protected int $orderId;
    protected int $statusOrderId;
    protected string $routeList;

    public function __construct(int $orderId, int $statusOrderId, string $routeList)
    {
        $this->orderId       = $orderId;
        $this->statusOrderId = $statusOrderId;
        $this->routeList     = $routeList;
    }

    public function handle(): void
    {
        // Aplicar configuración SMTP del tenant
        Configuration::setConfigSmtpMail();

        $order  = Order::find($this->orderId);
        $status = StatusOrder::find($this->statusOrderId);

        if (!$order) {
            Log::warning("SendOrderStatusEmail: Order {$this->orderId} not found");
            return;
        }

        // El correo vive en el JSON customer->correo_electronico
        $customerEmail = $order->customer->correo_electronico ?? null;

        if (empty($customerEmail)) {
            Log::info("SendOrderStatusEmail: No customer email for order {$this->orderId}");
            return;
        }

        $data = [
            'order' => [
                'id'               => $order->id,
                'number_document'  => $order->number_document,
                'order_id'         => str_pad($order->id, 6, '0', STR_PAD_LEFT),
                'total'            => $order->total,
                'reference_payment'=> strtoupper($order->reference_payment ?? ''),
                'created_at'       => $order->created_at->format('Y-m-d H:i'),
                'updated_at'       => $order->updated_at->format('Y-m-d H:i'),
            ],
            'status' => [
                'id'    => $status ? $status->id    : $this->statusOrderId,
                'name'  => $status ? $status->description  : null,
                'color' => $status ? ($status->color ?? '#066FD1') : '#066FD1',
            ],
            'customer' => [
                'name'  => $order->customer->apellidos_y_nombres_o_razon_social ?? null,
                'email' => $customerEmail,
            ],
            'items' => collect($order->items ?? [])->map(fn ($item) => [
                'description'         => $item->description ?? ($item->descripcion ?? 'Producto'),
                'cantidad'            => $item->cantidad    ?? ($item->quantity    ?? 1),
                'sub_total'           => $item->sub_total   ?? '',
                'currency_type_symbol'=> $item->currency_type_symbol ?? 'S/',
            ])->toArray(),
            'route_list' => $this->routeList,
        ];

        Mail::to($customerEmail)->send(new OrderStatusChanged($data));

        Log::info("SendOrderStatusEmail: correo enviado — order {$this->orderId}, status {$this->statusOrderId}");
    }


}
