<?php

namespace Modules\Webhook\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class WebhookDeliveryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'subscription_id' => $this->webhook_subscription_id,
            'subscription_name' => optional($this->subscription)->name,
            'subscription_url' => optional($this->subscription)->url,
            'event' => $this->event,
            'event_id' => $this->event_id,
            'status' => $this->status,
            'attempts' => $this->attempts,
            'response_code' => $this->response_code,
            'response_body' => $this->response_body,
            'error_message' => $this->error_message,
            'payload' => $this->payload,
            'sent_at' => optional($this->sent_at)->format('Y-m-d H:i:s'),
            'created_at' => optional($this->created_at)->format('Y-m-d H:i:s'),
        ];
    }
}
