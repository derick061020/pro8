<?php

namespace Modules\Hotel\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Modules\Hotel\Models\HotelBlogPost;
use App\Models\Tenant\Establishment;
use Modules\Finance\Helpers\UploadFileHelper;

class HotelBlogController extends Controller
{
    /**
     * Listado de entradas de blog para el administrador.
     * @return Response
     */
    public function index()
    {
        $user = auth()->user();

        $query = HotelBlogPost::with('establishment')->orderBy('id', 'DESC');

        if (request()->ajax()) {
            if (request('establishment_id') && $user->type === 'admin') {
                $query->where('establishment_id', request('establishment_id'));
            }

            if ($user->type != 'admin') {
                $query->where('establishment_id', $user->establishment_id);
            }

            if (request('title')) {
                $query->where('title', 'like', '%' . request('title') . '%');
            }

            $posts = $query->paginate(25);
            $posts->getCollection()->transform(fn ($post) => $this->transform($post));

            return response()->json([
                'success' => true,
                'posts'   => $posts,
            ], 200);
        }

        $query->where('establishment_id', $user->establishment_id);

        $posts = $query->paginate(25);
        $posts->getCollection()->transform(fn ($post) => $this->transform($post));

        $establishments  = Establishment::select('id', 'description')->get();
        $userType        = $user->type;
        $establishmentId = $user->establishment_id;

        return view('hotel::blog.index', compact('posts', 'establishments', 'userType', 'establishmentId'));
    }

    /**
     * Almacena una nueva entrada.
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $data = $this->validateData($request);

        if ($data instanceof \Illuminate\Http\JsonResponse) {
            return $data;
        }

        $post = new HotelBlogPost();
        $this->fill($post, $request, $data);
        $post->save();

        return response()->json([
            'success' => true,
            'data'    => $this->transform($post),
        ], 200);
    }

    /**
     * Actualiza una entrada existente.
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        $post = HotelBlogPost::findOrFail($id);

        $data = $this->validateData($request);

        if ($data instanceof \Illuminate\Http\JsonResponse) {
            return $data;
        }

        $this->fill($post, $request, $data);
        $post->save();

        return response()->json([
            'success' => true,
            'data'    => $this->transform($post),
        ], 200);
    }

    /**
     * Elimina una entrada.
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        try {
            HotelBlogPost::where('id', $id)->delete();

            return response()->json([
                'success' => true,
                'message' => 'Información actualizada',
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'data'    => 'Ocurrió un error al procesar su petición. Detalles: ' . $th->getMessage(),
            ], 500);
        }
    }

    /**
     * Reglas de validación comunes a store/update.
     */
    private function validateData(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title'            => 'required|string|max:191',
            'author'           => 'nullable|string|max:120',
            'excerpt'          => 'nullable|string|max:500',
            'content'          => 'nullable|string',
            'cover_type'       => 'nullable|in:image,video',
            'video_url'        => 'nullable|string|max:500',
            'published'        => 'nullable|boolean',
            'published_at'     => 'nullable|date',
            'establishment_id' => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'errors'  => $validator->errors(),
            ], 422);
        }

        return $validator->validated();
    }

    /**
     * Asigna los datos validados al modelo, resuelve slug, imagen y sucursal.
     */
    private function fill(HotelBlogPost $post, Request $request, array $data)
    {
        $user = auth()->user();

        $post->title     = $data['title'];
        $post->author    = $data['author'] ?? null;
        $post->excerpt   = $data['excerpt'] ?? null;
        $post->content   = $data['content'] ?? null;
        $post->published = (bool) ($request->input('published', true));

        // Portada: imagen (por defecto) o video (YouTube/Vimeo/embed).
        $coverType = $data['cover_type'] ?? 'image';
        $post->cover_type = in_array($coverType, ['image', 'video'], true) ? $coverType : 'image';
        $post->video_url  = $post->cover_type === 'video' ? ($data['video_url'] ?? null) : null;

        $publishedAt = $data['published_at'] ?? null;
        $post->published_at = $publishedAt ? Carbon::parse($publishedAt) : ($post->published_at ?: Carbon::now());

        // Sucursal: admin puede elegir; el resto queda con la suya.
        if ($user->type === 'admin' && !empty($data['establishment_id'])) {
            $post->establishment_id = $data['establishment_id'];
        } elseif (!$post->establishment_id) {
            $post->establishment_id = $user->establishment_id;
        }

        // Slug: se genera del título si cambió o no existe.
        if (empty($post->slug) || $post->isDirty('title')) {
            $post->slug = HotelBlogPost::makeUniqueSlug($post->title, $post->id);
        }

        $post->image = $this->processImage($request->input('image'), $post->image);
    }

    /**
     * Procesa la imagen de portada. Acepta { filename, temp_path } (subida nueva)
     * o conserva la existente. Devuelve el nombre de archivo a guardar (o null).
     */
    private function processImage($image, $current)
    {
        if (empty($image)) {
            return null;
        }

        $image     = is_array($image) ? $image : (array) $image;
        $filename  = $image['filename']  ?? null;
        $temp_path = $image['temp_path'] ?? null;

        if ($temp_path && $filename) {
            try {
                UploadFileHelper::checkIfValidFile($filename, $temp_path, true);
                return UploadFileHelper::uploadImageFromTempFile(
                    HotelBlogPost::IMAGES_FOLDER,
                    $filename,
                    $temp_path,
                    'blog',
                    true,
                    uniqid()
                );
            } catch (\Throwable $th) {
                return $current;
            }
        }

        // Ya almacenada previamente: se conserva.
        return $filename ?: $current;
    }

    /**
     * Normaliza una entrada para el frontend del admin.
     */
    private function transform(HotelBlogPost $post)
    {
        return [
            'id'                 => $post->id,
            'title'              => $post->title,
            'slug'               => $post->slug,
            'author'             => $post->author,
            'excerpt'            => $post->excerpt,
            'content'            => $post->content,
            'image'              => $post->image,
            'image_url'          => $post->image_url,
            'cover_type'         => $post->cover_type ?: 'image',
            'video_url'          => $post->video_url,
            'video_embed_url'    => $post->video_embed_url,
            'cover_image'        => $post->cover_image,
            'published'          => (bool) $post->published,
            'published_at'       => optional($post->published_at)->format('Y-m-d'),
            'establishment_id'   => $post->establishment_id,
            'establishment_name' => $post->establishment->description ?? '',
        ];
    }
}
