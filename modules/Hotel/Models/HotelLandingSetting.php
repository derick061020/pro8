<?php

namespace Modules\Hotel\Models;

use App\Models\Tenant\ModelTenant;

/**
 * Configuración de la web pública de reservas (landing) por establecimiento.
 *
 * Toda la personalización (slider, galería, parallax, testimonios, ventajas,
 * sección "sobre el hotel", textos de secciones, color del tema, etc.) se
 * guarda en la columna JSON `data`. El método config() fusiona lo guardado con
 * los valores por defecto, de modo que la landing siempre tiene contenido
 * aunque el usuario no haya personalizado nada todavía.
 */
class HotelLandingSetting extends ModelTenant
{
    protected $table = 'hotel_landing_settings';

    protected $fillable = [
        'establishment_id',
        'data',
    ];

    protected $casts = [
        'data' => 'array',
    ];

    /** Carpeta (dentro de storage/app/public/uploads) de las imágenes de la web. */
    const IMAGES_FOLDER = 'hotel/landing';

    /** Rutas de las imágenes de demostración del tema (fallback si no hay subida). */
    const DEFAULT_SLIDE    = '/landing-reservas/images/slides/1700x449.gif';
    const DEFAULT_PARALLAX = '/landing-reservas/images/parallax/1900x911.gif';
    const DEFAULT_GALLERY  = '/landing-reservas/images/gallery/800x504.gif';
    const DEFAULT_REVIEW   = '/landing-reservas/images/reviews/100x100.gif';
    const DEFAULT_ABOUT    = '/landing-reservas/images/tab/197x147.gif';

    /**
     * Devuelve (creando si hace falta) la configuración del establecimiento.
     * Si no se indica establecimiento se usa el primero registrado.
     */
    public static function forEstablishment($establishmentId = null)
    {
        $query = self::query();

        if ($establishmentId) {
            $query->where('establishment_id', $establishmentId);
        }

        return $query->orderBy('id')->first();
    }

    /**
     * Configuración final (guardada + valores por defecto) lista para la vista.
     */
    public function config()
    {
        return self::mergeDefaults(is_array($this->data) ? $this->data : []);
    }

    /**
     * Fusiona la configuración guardada con los valores por defecto. Las claves
     * ausentes toman su valor por defecto; los arrays de listas (slides,
     * gallery, features, testimonials, about.tabs) se usan tal cual si vienen
     * definidos, o por defecto si no.
     */
    public static function mergeDefaults(array $data)
    {
        $defaults = self::defaults();

        $merged = array_merge($defaults, $data);

        // Las listas anidadas: si el usuario las definió (aunque sea vacías),
        // se respetan; si no, valores por defecto.
        foreach (['slides', 'gallery', 'features', 'testimonials'] as $listKey) {
            if (!array_key_exists($listKey, $data) || !is_array($data[$listKey])) {
                $merged[$listKey] = $defaults[$listKey];
            }
        }

        // Bloque "about" (imagen + pestañas).
        $merged['about'] = array_merge($defaults['about'], is_array($data['about'] ?? null) ? $data['about'] : []);
        if (!isset($data['about']['tabs']) || !is_array($data['about']['tabs'])) {
            $merged['about']['tabs'] = $defaults['about']['tabs'];
        }

        // Bloque "parallax".
        $merged['parallax'] = array_merge($defaults['parallax'], is_array($data['parallax'] ?? null) ? $data['parallax'] : []);

        return $merged;
    }

    /**
     * Convierte una referencia de imagen guardada (nombre de archivo subido,
     * URL absoluta o vacío) en una URL pública utilizable. Si está vacía usa el
     * fallback indicado.
     */
    public static function imageUrl($value, $fallback = null)
    {
        if (empty($value)) {
            return $fallback;
        }

        if (is_array($value)) {
            $value = $value['filename'] ?? ($value['url'] ?? null);
            if (empty($value)) {
                return $fallback;
            }
        }

        if (is_string($value) && (str_starts_with($value, 'http') || str_starts_with($value, '/'))) {
            return $value;
        }

        return asset('storage/uploads/' . self::IMAGES_FOLDER . '/' . $value);
    }

    /**
     * Valores por defecto de la landing. Replican el contenido original del
     * tema para que la web se vea completa sin necesidad de configurar nada.
     */
    public static function defaults()
    {
        return [
            // ---- Slider principal ----
            'slides' => [
                [
                    'image'       => null,
                    'title'       => '',
                    'subtitle'    => 'Reserva tu estancia con nosotros',
                    'button_text' => 'Ver habitaciones',
                    'button_link' => '#rooms-results',
                    'stars'       => true,
                ],
                [
                    'image'       => null,
                    'title'       => 'Bienvenido',
                    'subtitle'    => 'Disponibilidad en tiempo real',
                    'button_text' => 'Reservar ahora',
                    'button_link' => '#reservation-form',
                    'stars'       => true,
                ],
            ],

            // ---- Sección de ventajas (USPs) ----
            'features_heading' => '¿Por qué reservar con nosotros?',
            'features'         => [
                ['icon' => 'fa-calendar-check-o', 'title' => 'Reserva en línea', 'text' => 'Consulta disponibilidad en tiempo real y reserva tu habitación en pocos pasos, sin llamadas ni esperas.', 'link_text' => 'Reservar', 'link' => '#reservation-form'],
                ['icon' => 'fa-credit-card', 'title' => 'Confirmación rápida', 'text' => 'Recibe la confirmación de tu reserva y coordina el pago directamente con el hotel de forma segura.', 'link_text' => 'Reservar', 'link' => '#reservation-form'],
                ['icon' => 'fa-bed', 'title' => 'Habitaciones cómodas', 'text' => 'Conoce el detalle, las fotos y los servicios de cada habitación antes de elegir la que más te conviene.', 'link_text' => 'Ver habitaciones', 'link' => '#rooms-results'],
                ['icon' => 'fa-headphones', 'title' => 'Atención dedicada', 'text' => 'Nuestro equipo te acompaña antes, durante y después de tu estancia para que todo sea perfecto.', 'link_text' => 'Contacto', 'link' => '#contacto'],
            ],

            // ---- Sección de habitaciones ----
            'rooms_heading'    => 'Nuestras habitaciones',
            'rooms_subheading' => 'Selecciona fechas para ver disponibilidad y precios.',

            // ---- Parallax ----
            'parallax' => [
                'image'       => null,
                'text'        => 'Vive una experiencia inolvidable',
                'button_text' => 'Ver habitaciones',
                'button_link' => '#rooms-results',
            ],

            // ---- Galería ----
            'gallery_heading' => 'Galería',
            'gallery'         => [null, null, null, null],

            // ---- Testimonios ----
            'testimonials_heading' => 'Lo que opinan nuestros huéspedes',
            'testimonials'         => [
                ['name' => 'María G., Habitación doble', 'text' => 'Excelente atención y habitaciones impecables. Volveremos sin dudarlo.', 'image' => null],
                ['name' => 'Carlos D., Habitación simple', 'text' => '¡Un 5 de 5! Personal amable, limpio y muy cómodo. Totalmente recomendado.', 'image' => null],
                ['name' => 'Rosa O., Habitación simple', 'text' => 'Un lugar encantador. La próxima vez reservaré una estancia más larga.', 'image' => null],
                ['name' => 'Luis A., Habitación simple', 'text' => '¡El mejor hotel de la ciudad! Buena ubicación y un servicio inmejorable.', 'image' => null],
            ],

            // ---- Sobre el hotel ----
            'about_heading' => 'Sobre el hotel',
            'about'         => [
                'image' => null,
                'tabs'  => [
                    ['title' => 'El hotel', 'content' => 'Te damos la bienvenida a un espacio pensado para tu descanso. Habitaciones cómodas, atención cercana y todo lo que necesitas para una estancia perfecta.'],
                    ['title' => 'Eventos', 'content' => 'Organizamos y recibimos eventos. Consúltanos por disponibilidad de salas y servicios para tu celebración o reunión.'],
                    ['title' => 'Familias', 'content' => 'Un lugar ideal para venir en familia. Contamos con opciones pensadas para que grandes y pequeños se sientan como en casa.'],
                    ['title' => 'Negocios', 'content' => '¿Viaje de negocios? Ofrecemos habitaciones equipadas, conexión y la comodidad que necesitas para trabajar y descansar.'],
                ],
            ],

            // ---- Call to action ----
            'cta_text'   => '¿Listo para tu próxima estancia? Reserva ahora en línea.',
            'cta_button' => 'Ver disponibilidad',

            // ---- Apariencia / visibilidad de secciones ----
            'color'             => 'turquoise',
            'show_features'     => true,
            'show_parallax'     => true,
            'show_gallery'      => true,
            'show_testimonials' => true,
            'show_about'        => true,
            'show_cta'          => true,
        ];
    }
}
