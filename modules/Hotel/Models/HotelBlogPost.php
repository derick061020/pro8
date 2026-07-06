<?php

namespace Modules\Hotel\Models;

use App\Models\Tenant\ModelTenant;
use App\Models\Tenant\Establishment;
use Illuminate\Support\Str;
use Carbon\Carbon;

/**
 * Entrada de blog de la web pública de reservas (landing).
 * Se administra desde Hotel -> Blog.
 */
class HotelBlogPost extends ModelTenant
{
    protected $table = 'hotel_blog_posts';

    protected $fillable = [
        'title',
        'slug',
        'author',
        'image',
        'cover_type',
        'video_url',
        'excerpt',
        'content',
        'published',
        'published_at',
        'establishment_id',
    ];

    protected $casts = [
        'published'    => 'boolean',
        'published_at' => 'datetime',
    ];

    /** Carpeta (dentro de storage/app/public/uploads) de las imágenes del blog. */
    const IMAGES_FOLDER = 'hotel/blog';

    /**
     * URL pública de la imagen de portada, o null si no tiene.
     * Acepta tanto nombres de archivo guardados como URLs absolutas.
     */
    public function getImageUrlAttribute()
    {
        $image = $this->image;

        if (!$image) {
            return null;
        }

        if (is_string($image) && str_starts_with($image, 'http')) {
            return $image;
        }

        return asset('storage/uploads/' . self::IMAGES_FOLDER . '/' . $image);
    }

    /**
     * Indica si la portada de la entrada es un video (YouTube, Vimeo o embed).
     */
    public function hasVideoCover()
    {
        return $this->cover_type === 'video' && !empty($this->video_url);
    }

    /**
     * Devuelve la URL lista para incrustar en un <iframe> a partir de la URL
     * del video pegada por el usuario. Soporta YouTube (watch, youtu.be, shorts,
     * embed), Vimeo y, como fallback, la URL tal cual si ya es incrustable.
     * Devuelve null si no hay video.
     */
    public function getVideoEmbedUrlAttribute()
    {
        if (!$this->hasVideoCover()) {
            return null;
        }

        return self::toEmbedUrl($this->video_url);
    }

    /**
     * ID del video de YouTube si la URL corresponde a YouTube, o null.
     */
    public function getYoutubeIdAttribute()
    {
        return self::extractYoutubeId($this->video_url);
    }

    /**
     * Imagen representativa de la portada para listados/tarjetas: la imagen
     * subida si existe, o la miniatura del video de YouTube como fallback.
     */
    public function getCoverImageAttribute()
    {
        if ($this->image) {
            return $this->image_url;
        }

        if ($this->hasVideoCover()) {
            $ytId = self::extractYoutubeId($this->video_url);
            if ($ytId) {
                return 'https://img.youtube.com/vi/' . $ytId . '/hqdefault.jpg';
            }
        }

        return null;
    }

    /**
     * Normaliza una URL de video a su forma incrustable en <iframe>.
     */
    public static function toEmbedUrl($url)
    {
        $url = trim((string) $url);

        if ($url === '') {
            return null;
        }

        // Si pegaron el código <iframe ... src="URL" ...>, extraemos el src.
        if (preg_match('/<iframe[^>]*\ssrc=["\']([^"\']+)["\']/i', $url, $m)) {
            $url = $m[1];
        }

        $ytId = self::extractYoutubeId($url);
        if ($ytId) {
            return 'https://www.youtube.com/embed/' . $ytId;
        }

        // Vimeo: https://vimeo.com/123456789
        if (preg_match('#vimeo\.com/(?:video/)?(\d+)#i', $url, $m)) {
            return 'https://player.vimeo.com/video/' . $m[1];
        }

        // Ya es una URL incrustable (embed/player) u otro proveedor.
        return $url;
    }

    /**
     * Extrae el ID de un video de YouTube desde cualquiera de sus formatos.
     */
    public static function extractYoutubeId($url)
    {
        $url = trim((string) $url);

        if ($url === '') {
            return null;
        }

        $patterns = [
            '#youtube\.com/watch\?(?:.*&)?v=([\w-]{11})#i',
            '#youtu\.be/([\w-]{11})#i',
            '#youtube\.com/embed/([\w-]{11})#i',
            '#youtube\.com/shorts/([\w-]{11})#i',
            '#youtube\.com/v/([\w-]{11})#i',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $url, $m)) {
                return $m[1];
            }
        }

        return null;
    }

    public function establishment()
    {
        return $this->belongsTo(Establishment::class)->select('id', 'description');
    }

    /**
     * Genera un slug único a partir del título.
     */
    public static function makeUniqueSlug($title, $ignoreId = null)
    {
        $base = Str::slug($title) ?: 'nota';
        $slug = $base;
        $i    = 2;

        while (self::where('slug', $slug)
            ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
            ->exists()) {
            $slug = $base . '-' . $i;
            $i++;
        }

        return $slug;
    }
}
