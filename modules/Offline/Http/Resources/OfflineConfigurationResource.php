<?php

namespace Modules\Offline\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OfflineConfigurationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request) {
        return [
            'id'            => $this->id,
            'is_client'     => (bool) $this->is_client,
            'mode'          => $this->mode,
            'terminal_code' => $this->terminal_code,
            'terminal_name' => $this->terminal_name,
            'token_server'  => $this->token_server,
            'url_server'    => $this->url_server,
            'sync_enabled'  => (bool) $this->sync_enabled,
            'sync_interval' => (int) ($this->sync_interval ?: 60),
            'git_branch'    => $this->git_branch,
            'app_version'   => $this->app_version,
        ];
    }
}
