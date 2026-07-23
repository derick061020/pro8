<?php

namespace Modules\WhatsAppBot\Services\Session;

use App\Models\Tenant\Configuration;
use Carbon\Carbon;
use Modules\WhatsAppBot\Models\BotSession;

class SessionMachine
{
    public const STATE_IDLE = 'idle';
    public const STATE_ACTIVE = 'active';
    public const STATE_AWAITING_CONFIRM = 'awaiting_confirm';
    public const STATE_PAUSED = 'paused';
    public const STATE_CLOSED = 'closed';

    public function findOrCreate(string $customerPhone): BotSession
    {
        $session = BotSession::where('customer_phone', $customerPhone)
            ->whereIn('state', [self::STATE_ACTIVE, self::STATE_AWAITING_CONFIRM, self::STATE_PAUSED])
            ->orderByDesc('last_activity_at')
            ->first();

        if ($session && $this->isExpired($session)) {
            $session->state = self::STATE_CLOSED;
            $session->save();
            $session = null;
        }

        if (!$session) {
            $session = BotSession::create([
                'customer_phone' => $customerPhone,
                'state' => self::STATE_IDLE,
                'last_activity_at' => Carbon::now(),
            ]);
        }

        return $session;
    }

    public function activate(BotSession $session): void
    {
        $session->state = self::STATE_ACTIVE;
        $session->last_activity_at = Carbon::now();
        $session->save();
    }

    public function pause(BotSession $session): void
    {
        $session->state = self::STATE_PAUSED;
        $session->last_activity_at = Carbon::now();
        $session->save();
    }

    public function close(BotSession $session): void
    {
        $session->state = self::STATE_CLOSED;
        $session->last_activity_at = Carbon::now();
        $session->save();
    }

    public function touch(BotSession $session): void
    {
        $session->last_activity_at = Carbon::now();
        $session->save();
    }

    public function pauseSessionsForPhone(string $customerPhone): int
    {
        return BotSession::where('customer_phone', $customerPhone)
            ->whereIn('state', [self::STATE_ACTIVE, self::STATE_AWAITING_CONFIRM])
            ->update([
                'state' => self::STATE_PAUSED,
                'last_activity_at' => Carbon::now(),
            ]);
    }

    public function isExpired(BotSession $session): bool
    {
        $config = Configuration::first();
        $ttlMinutes = (int) ($config->bot_session_ttl_minutes ?? 30);

        if (!$session->last_activity_at) {
            return false;
        }

        return Carbon::parse($session->last_activity_at)
            ->addMinutes($ttlMinutes)
            ->isPast();
    }

    public function appendToContext(BotSession $session, string $role, string $content): void
    {
        $context = $session->context_json ?: [];
        $context[] = ['role' => $role, 'content' => $content];

        $maxTurns = 20;
        if (count($context) > $maxTurns) {
            $context = array_slice($context, -$maxTurns);
        }

        $session->context_json = $context;
        $session->last_activity_at = Carbon::now();
        $session->save();
    }
}
