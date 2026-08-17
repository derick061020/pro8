<?php

namespace Modules\Hotel\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Hotel\Models\HotelLandingSetting;
use App\Models\Tenant\Establishment;
use Modules\Finance\Helpers\UploadFileHelper;

/**
 * Personalización de la web pública de reservas (landing) desde el admin:
 * slider, galería, parallax, testimonios, ventajas, "sobre el hotel", textos y
 * apariencia. Una configuración por establecimiento.
 */
class HotelLandingSettingController extends Controller
{
    const IMAGES_FOLDER = HotelLandingSetting::IMAGES_FOLDER;

    /**
     * Vista contenedora del editor (monta el componente Vue).
     */
    public function index()
    {
        $user            = auth()->user();
        $establishments  = Establishment::select('id', 'description')->get();
        $userType        = $user->type;
        $establishmentId = $user->establishment_id;

        return view('hotel::landing-settings.index', compact('establishments', 'userType', 'establishmentId'));
    }

    /**
     * Devuelve la configuración (fusionada con defaults) y metadatos para el
     * editor. `raw` contiene sólo lo guardado; `config` lo efectivo.
     */
    public function get(Request $request)
    {
        $establishmentId = $this->resolveEstablishmentId($request);
        $setting         = HotelLandingSetting::forEstablishment($establishmentId);

        $config = HotelLandingSetting::mergeDefaults($setting && is_array($setting->data) ? $setting->data : []);

        // Diapositivas de fábrica (las 4 fotos incluidas + la portada de
        // respaldo): permiten restaurarlas aunque el hotel ya tenga una
        // configuración guardada de antes.
        $defaultSlides = $this->withImageUrls(HotelLandingSetting::mergeDefaults([]))['slides'];

        return response()->json([
            'success'        => true,
            'config'         => $this->withImageUrls($config),
            'default_slides' => $defaultSlides,
            'colors'         => ['turquoise', 'blue', 'green', 'orange', 'purple', 'red', 'brown', 'black'],
            // La web vive en la raíz; con ?preview=1 el admin logueado la ve
            // igual que un cliente en vez de rebotar al panel.
            'landing_url'    => url('/?preview=1'),
        ], 200);
    }

    /**
     * Guarda la configuración. Sube las imágenes nuevas (las que llegan como
     * objeto con temp_path) y conserva las existentes.
     */
    public function update(Request $request)
    {
        $establishmentId = $this->resolveEstablishmentId($request);
        $incoming        = $request->input('config', []);

        if (!is_array($incoming)) {
            $incoming = [];
        }

        // Configuración previa (para conservar imágenes ya guardadas).
        $setting  = HotelLandingSetting::forEstablishment($establishmentId);
        $previous = ($setting && is_array($setting->data)) ? $setting->data : [];

        $clean = $this->processImages($incoming, $previous);

        if (!$setting) {
            $setting = new HotelLandingSetting();
            $setting->establishment_id = $establishmentId;
        }

        $setting->data = $clean;
        $setting->save();

        $config = HotelLandingSetting::mergeDefaults($clean);

        return response()->json([
            'success' => true,
            'message' => 'Web actualizada correctamente.',
            'config'  => $this->withImageUrls($config),
        ], 200);
    }

    /**
     * Recorre la estructura y resuelve todas las referencias de imagen,
     * conservando las existentes y subiendo las nuevas.
     */
    private function processImages(array $data, array $previous)
    {
        // Slider
        if (isset($data['slides']) && is_array($data['slides'])) {
            foreach ($data['slides'] as $i => $slide) {
                $data['slides'][$i]['image'] = $this->processImage(
                    $slide['image'] ?? null,
                    $previous['slides'][$i]['image'] ?? null,
                    'slide'
                );
            }
        }

        // Parallax
        if (isset($data['parallax']) && is_array($data['parallax'])) {
            $data['parallax']['image'] = $this->processImage(
                $data['parallax']['image'] ?? null,
                $previous['parallax']['image'] ?? null,
                'parallax'
            );
        }

        // Galería (array de referencias de imagen)
        if (isset($data['gallery']) && is_array($data['gallery'])) {
            foreach ($data['gallery'] as $i => $img) {
                $data['gallery'][$i] = $this->processImage($img, $previous['gallery'][$i] ?? null, 'galeria');
            }
            // Compactar nulos al final para no dejar huecos.
            $data['gallery'] = array_values($data['gallery']);
        }

        // Testimonios
        if (isset($data['testimonials']) && is_array($data['testimonials'])) {
            foreach ($data['testimonials'] as $i => $t) {
                $data['testimonials'][$i]['image'] = $this->processImage(
                    $t['image'] ?? null,
                    $previous['testimonials'][$i]['image'] ?? null,
                    'review'
                );
            }
        }

        // Sobre el hotel
        if (isset($data['about']) && is_array($data['about'])) {
            $data['about']['image'] = $this->processImage(
                $data['about']['image'] ?? null,
                $previous['about']['image'] ?? null,
                'about'
            );
        }

        return $data;
    }

    /**
     * Procesa una referencia de imagen. Acepta:
     *  - objeto { filename, temp_path } -> sube y devuelve el nombre guardado.
     *  - objeto { filename } o string    -> conserva el nombre existente.
     *  - null / vacío                    -> devuelve null (sin imagen).
     */
    private function processImage($image, $current, $name)
    {
        if (empty($image)) {
            return null;
        }

        // String: nombre de archivo o URL ya almacenada -> se conserva.
        if (is_string($image)) {
            return $image;
        }

        $image     = (array) $image;
        $filename  = $image['filename']  ?? null;
        $temp_path = $image['temp_path'] ?? null;

        if ($temp_path && $filename) {
            try {
                UploadFileHelper::checkIfValidFile($filename, $temp_path, true);
                return UploadFileHelper::uploadImageFromTempFile(
                    self::IMAGES_FOLDER,
                    $filename,
                    $temp_path,
                    $name,
                    true,
                    uniqid()
                );
            } catch (\Throwable $th) {
                return $current;
            }
        }

        // Sin temp_path: conservar el nombre existente si viene, o el actual.
        return $filename ?: $current;
    }

    /**
     * Añade a la configuración las URLs resueltas de cada imagen para que el
     * editor pueda previsualizarlas (bajo la clave `*_url`), sin perder el
     * nombre de archivo original.
     */
    private function withImageUrls(array $config)
    {
        foreach ($config['slides'] as $i => $slide) {
            $config['slides'][$i]['image_url'] = HotelLandingSetting::imageUrl($slide['image'] ?? null, HotelLandingSetting::DEFAULT_SLIDE);
        }

        $config['parallax']['image_url'] = HotelLandingSetting::imageUrl($config['parallax']['image'] ?? null, HotelLandingSetting::DEFAULT_PARALLAX);

        $config['gallery_urls'] = [];
        foreach ($config['gallery'] as $img) {
            $config['gallery_urls'][] = HotelLandingSetting::imageUrl($img, HotelLandingSetting::DEFAULT_GALLERY);
        }

        foreach ($config['testimonials'] as $i => $t) {
            $config['testimonials'][$i]['image_url'] = HotelLandingSetting::imageUrl($t['image'] ?? null, HotelLandingSetting::DEFAULT_REVIEW);
        }

        $config['about']['image_url'] = HotelLandingSetting::imageUrl($config['about']['image'] ?? null, HotelLandingSetting::DEFAULT_ABOUT);

        return $config;
    }

    /**
     * Establecimiento sobre el que se opera: admin puede indicar uno, el resto
     * usa el suyo.
     */
    private function resolveEstablishmentId(Request $request)
    {
        $user = auth()->user();

        if ($user->type === 'admin' && $request->filled('establishment_id')) {
            return (int) $request->input('establishment_id');
        }

        return (int) $user->establishment_id;
    }
}
