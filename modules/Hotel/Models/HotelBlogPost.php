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
