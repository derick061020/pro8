<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Cache;

/**
 * CacheHelper
 *
 * Provee métodos de caché compatibles con cualquier driver de Laravel.
 * Los drivers 'file' y 'database' no soportan etiquetas (tags). Este helper
 * detecta automáticamente si el driver activo soporta tags y, en caso contrario,
 * opera directamente sobre Cache sin tags para evitar BadMethodCallException.
 */
class CacheHelper
{
    /**
     * Drivers de caché que soportan etiquetas (tags).
     */
    protected static array $tagSupportedDrivers = ['redis', 'memcached'];

    /**
     * Indica si el driver de caché activo soporta tags.
     */
    public static function supportsTags(): bool
    {
        return in_array(config('cache.default'), static::$tagSupportedDrivers, true);
    }

    /**
     * Obtiene una instancia de caché con tags si el driver lo soporta,
     * o sin tags (Cache directamente) si no lo soporta.
     *
     * @param string|array $tags
     * @return \Illuminate\Cache\TaggedCache|\Illuminate\Cache\Repository
     */
    public static function store(string|array $tags): \Illuminate\Cache\Repository|\Illuminate\Cache\TaggedCache
    {
        if (static::supportsTags()) {
            return Cache::tags($tags);
        }

        return Cache::store(config('cache.default'));
    }

    /**
     * Recuerda un valor en caché usando tags si el driver lo soporta.
     *
     * @param string|array $tags
     * @param string $key
     * @param int $ttl Segundos
     * @param callable $callback
     * @return mixed
     */
    public static function remember(string|array $tags, string $key, int $ttl, callable $callback): mixed
    {
        if (static::supportsTags()) {
            return Cache::tags($tags)->remember($key, $ttl, $callback);
        }

        return Cache::remember($key, $ttl, $callback);
    }

    /**
     * Elimina una clave del caché. Usa tags si el driver lo soporta.
     *
     * @param string|array $tags
     * @param string $key
     */
    public static function forget(string|array $tags, string $key): void
    {
        if (static::supportsTags()) {
            Cache::tags($tags)->forget($key);
        } else {
            Cache::forget($key);
        }
    }

    /**
     * Vacía todas las entradas asociadas a un tag. Usa tags si el driver lo
     * soporta. Si no, simplemente no hace nada (no se puede vaciar por grupo
     * sin soporte de tags, pero tampoco hay caché estancada que limpiar).
     *
     * @param string|array $tags
     */
    public static function flush(string|array $tags): void
    {
        if (static::supportsTags()) {
            Cache::tags($tags)->flush();
        }
        // En drivers sin tags no hay entradas etiquetadas que vaciar.
        // La caché se invalida naturalmente por TTL o por forget() individual.
    }
}
