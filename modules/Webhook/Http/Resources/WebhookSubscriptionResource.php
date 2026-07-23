<?php

namespace Modules\Webhook\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;
use Modules\Webhook\Services\WebhookEvents;

class WebhookSubscriptionResource extends JsonResource
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
            'name' => $this->name,
            'url' => $this->url,
            // en el listado solo se expone una pista del secret
            'secret_hint' => Str::limit($this->secret, 12),
            'events' => $this->events,
            'event_labels' => collect($this->events)
                ->map(fn ($event) => WebhookEvents::label($event) ?? $event)
                ->all(),
            'is_active' => $this->is_active,
            'consecutive_failures' => $this->consecutive_failures,
            'disabled_at' => optional($this->disabled_at)->format('Y-m-d H:i:s'),
            'created_at' => optional($this->created_at)->format('Y-m-d H:i:s'),
        ];
    }
}
