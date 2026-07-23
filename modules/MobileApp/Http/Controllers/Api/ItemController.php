<?php

namespace Modules\MobileApp\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use App\Models\Tenant\Item;
use App\Models\Tenant\Configuration;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Modules\MobileApp\Http\Requests\Api\ItemRequest;
use Modules\Finance\Helpers\UploadFileHelper;
use Modules\MobileApp\Http\Resources\Api\{
    ItemCollection,
    ItemResource,
    ItemSaleCollection
};
use Modules\Item\Models\{
    Category
};
use App\Http\Controllers\Tenant\ItemController as ItemControllerWeb;
use App\Models\Tenant\Catalogs\AffectationIgvType;


class ItemController extends Controller
{

    /**
     *
     * Obtener tablas relacionadas
     *
     * @return array
     */
    public function tables()
    {
        return [
            'categories' => $this->table('categories')
        ];
    }


    /**
     *
     * @return array
     */
    public function table($table)
    {
        $data = [];

        switch ($table) {
            case 'categories':
                $data = Category::filterForTables()->get()->transform(function($row){
                    return $row->getRowResourceApi();
                });
                break;
            case 'affectation_igv_types':
                $data = AffectationIgvType::whereActive()->get();
                break;
        }

        return $data;
    }


    /**
     *
     * Obtener registros paginados
     * Mantenimiento items - App
     *
     * @param  Request $request
     * @return array
     */
    public function records(Request $request)
    {
        $records = Item::whereFilterRecordsApi($request->input, $request->search_by_barcode);

        return new ItemCollection($records->paginate(config('tenant.items_per_page')));
    }


    /**
     *
     * Obtener registros paginados para ventas - Modo POS App
     *
     * @param  Request $request
     * @return array
     */
    public function recordsSale(Request $request)
    {
        $records = Item::filterRecordsSaleApi($request);

        return new ItemSaleCollection($records->paginate(config('tenant.items_per_page')));
    }



    /**
     * obtener registro
     *
     * @param  int $id
     * @return ItemResource
     *
     */
    public function record($id)
    {
        return new ItemResource(Item::findOrFail($id));
    }


    /**
     *
     * Actualizar item
     *
     * @param  ItemRequest $request
     * @return array
     */
    public function update(ItemRequest $request)
    {

        $item = Item::findOrFail($request->id);
        $item->fill($request->all());
        $this->saveImage($item, $request);
        $item->update();

        return [
            'success' => true,
            'message' => 'Producto actualizado',
            'data' => [
                'id' => $item->id,
                'internal_id' => $item->internal_id,
                'item_code' => $item->item_code,
                'description' => $item->description,
                'name' => $item->name,
                'second_name' => $item->second_name,
                'unit_type_id' => $item->unit_type_id,
                'currency_type_id' => $item->currency_type_id,
                'sale_unit_price' => $item->sale_unit_price,
                'purchase_unit_price' => $item->purchase_unit_price,
                'has_isc' => $item->has_isc,
                'system_isc_type_id' => $item->system_isc_type_id,
                'percentage_isc' => $item->percentage_isc,
                'sale_affectation_igv_type_id' => $item->sale_affectation_igv_type_id,
                'purchase_affectation_igv_type_id' => $item->purchase_affectation_igv_type_id,
                'calculate_quantity' => $item->calculate_quantity,
                'has_igv' => $item->has_igv,
                'has_perception' => $item->has_perception,
                'percentage_of_profit' => $item->percentage_of_profit,
                'percentage_perception' => $item->percentage_perception,
                'category_id' => $item->category_id,
                'brand_id' => $item->brand_id,
                'barcode' => $item->barcode,
            ]
        ];
    }


    /**
     *
     * Eliminar item, usa método del proceso por web
     *
     * @param  int $id
     * @return array
     */
    public function destroy($id)
    {
        return app(ItemControllerWeb::class)->destroy($id);
    }


    /**
     *
     * Activar/Desactivar producto
     *
     * @param  int $id
     * @param  bool $active
     * @return array
     */
    public function changeActive($id, $active)
    {
        $record = Item::findOrFail($id);
        $record->active = $active;
        $record->save();

        return [
            'success' => true,
            'message' => $active ? 'Producto habilitado con éxito' : 'Producto inhabilitado con éxito'
        ];
    }


    /**
     *
     * Activar/Desactivar favorito
     *
     * @param  int $id
     * @param  bool $favorite
     * @return array
     */
    public function changeFavorite($id, $favorite)
    {
        $record = Item::findOrFail($id);
        $record->favorite = $favorite;
        $record->save();

        return [
            'success' => true,
            'message' => $favorite ? 'Agregado a favoritos' : 'Eliminado de favoritos'
        ];
    }


    /**
     *
     * Guardar imágen de diferentes tamaños
     *
     * @param  Item $item
     * @param  ItemRequest $request
     * @return void
     */
    public function saveImage(&$item, $request)
    {
        $temp_path = $request->temp_path;

        if($temp_path)
        {
            $old_filename = $request->image;
            $folder = 'items';

            $item->image = UploadFileHelper:: uploadImageFromTempFile($folder, $old_filename, $temp_path, "{$item->description}-{$item->id}", true);

            //size medium
            $image_medium = $item->getImageResize($temp_path, 512);
            $item->image_medium = UploadFileHelper:: uploadImageFromTempFile($folder, $old_filename, (string) $image_medium->encode('jpg', 30), "{$item->description}-{$item->id}", false, 'medium');

              //size small
            $image_small = $item->getImageResize($temp_path, 256);
            $item->image_small = UploadFileHelper:: uploadImageFromTempFile($folder, $old_filename, (string) $image_small->encode('jpg', 20), "{$item->description}-{$item->id}", false, 'small');
        }
        else if(!$request->image && !$request->temp_path && !$request->image_url)
        {
            $description = 'imagen-no-disponible.jpg';
            $item->image = $description;
            $item->image_medium = $description;
            $item->image_small = $description;
        }

    }


    /**
     *
     * Crear o actualizar un item recibiendo multipart/form-data.
     * - Si el request trae `id`, se actualiza ese item.
     * - Si no trae `id`, se crea uno nuevo.
     * - Si el request trae el archivo `file`, se procesa la imagen (3 tamaños).
     *
     * @param  ItemRequest $request
     * @return array
     */
    public function save(ItemRequest $request)
    {
        if ($request->hasFile('file')) {
            $validate = UploadFileHelper::validateUploadFile($request, 'file', 'jpg,jpeg,png,gif,svg,webp');
            if (!$validate['success']) {
                return $validate;
            }
        }

        $is_update = filled($request->input('id'));

        $item = DB::connection('tenant')->transaction(function () use ($request, $is_update) {

            $item = $is_update ? Item::findOrFail($request->input('id')) : new Item();

            if (!$is_update) {
                $item->item_type_id = '01';
                $item->amount_plastic_bag_taxes = Configuration::firstOrFail()->amount_plastic_bag_taxes;
            }

            $item->fill($request->except(['id', 'file', 'image', 'image_medium', 'image_small', 'temp_path', 'image_url']));
            $item->save();

            $has_new_image = $this->saveImageFromUpload($item, $request);

            if (!$has_new_image && !$is_update && !$item->image) {
                $placeholder = 'imagen-no-disponible.jpg';
                $item->image = $placeholder;
                $item->image_medium = $placeholder;
                $item->image_small = $placeholder;
            }

            if ($has_new_image || (!$is_update && !$item->wasChanged('image'))) {
                $item->save();
            }

            if (!$is_update) {
                app(ItemControllerWeb::class)->generateInternalId($item);
            }

            return $item->fresh();
        });

        return [
            'success' => true,
            'message' => $is_update ? 'Producto actualizado con éxito' : 'Producto registrado con éxito',
            'data' => $this->buildSavedResponse($item),
        ];
    }


    /**
     *
     * Procesa el archivo subido vía multipart y persiste las 3 versiones de la imagen.
     * Limpia los archivos previos del item para no acumular basura en storage.
     *
     * @param  Item $item
     * @param  Request $request
     * @return bool true si se procesó una imagen nueva
     */
    protected function saveImageFromUpload(Item $item, Request $request): bool
    {
        if (!$request->hasFile('file')) {
            return false;
        }

        $file = $request->file('file');
        $temp_path = $file->getRealPath();
        $old_filename = $file->getClientOriginalName();
        $folder = 'items';
        $name = "{$item->description}-{$item->id}";

        $this->deleteOldImages($item);

        $item->image = UploadFileHelper::uploadImageFromTempFile(
            $folder, $old_filename, $temp_path, $name, true
        );

        $image_medium = $item->getImageResize($temp_path, 512);
        $item->image_medium = UploadFileHelper::uploadImageFromTempFile(
            $folder, $old_filename, (string) $image_medium->encode('jpg', 30), $name, false, 'medium'
        );

        $image_small = $item->getImageResize($temp_path, 256);
        $item->image_small = UploadFileHelper::uploadImageFromTempFile(
            $folder, $old_filename, (string) $image_small->encode('jpg', 20), $name, false, 'small'
        );

        return true;
    }


    /**
     *
     * Elimina del storage las imágenes anteriores del item (si no son el placeholder).
     */
    protected function deleteOldImages(Item $item): void
    {
        $placeholder = 'imagen-no-disponible.jpg';
        $directory = 'public'.DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.'items'.DIRECTORY_SEPARATOR;

        foreach (['image', 'image_medium', 'image_small'] as $column) {
            $filename = $item->{$column};
            if ($filename && $filename !== $placeholder) {
                Storage::delete($directory.$filename);
            }
        }
    }


    /**
     *
     * Estructura de respuesta unificada para save().
     */
    protected function buildSavedResponse(Item $item): array
    {
        $full_description = $item->internal_id
            ? $item->internal_id.' - '.$item->description
            : $item->description;

        $image_url = $item->image !== 'imagen-no-disponible.jpg'
            ? url('/storage/uploads/items/'.$item->image)
            : url('/logo/'.$item->image);

        return [
            'id' => $item->id,
            'item_id' => $item->id,
            'name' => $item->name,
            'full_description' => $full_description,
            'description' => $item->description,
            'currency_type_id' => $item->currency_type_id,
            'internal_id' => $item->internal_id,
            'item_code' => $item->item_code,
            'barcode' => $item->barcode,
            'currency_type_symbol' => optional($item->currency_type)->symbol,
            'sale_unit_price' => number_format($item->sale_unit_price, 2),
            'purchase_unit_price' => $item->purchase_unit_price,
            'unit_type_id' => $item->unit_type_id,
            'sale_affectation_igv_type_id' => $item->sale_affectation_igv_type_id,
            'purchase_affectation_igv_type_id' => $item->purchase_affectation_igv_type_id,
            'calculate_quantity' => (bool) $item->calculate_quantity,
            'has_igv' => (bool) $item->has_igv,
            'is_set' => (bool) $item->is_set,
            'category_id' => $item->category_id,
            'brand_id' => $item->brand_id,
            'image' => $item->image,
            'image_medium' => $item->image_medium,
            'image_small' => $item->image_small,
            'image_url' => $image_url,
            'favorite' => (bool) $item->favorite,
            'aux_quantity' => 1,
        ];
    }


    /**
     *
     * Cargar imágen desde app
     *
     * @param  Request $request
     * @return array
     */
    public function uploadTempImage(Request $request)
    {

        $validate_upload = UploadFileHelper::validateUploadFile($request, 'file', 'jpg,jpeg,png,svg,webp');
        if(!$validate_upload['success']) return $validate_upload;


        if ($request->hasFile('file'))
        {
            $new_request = [
                'file' => $request->file('file'),
                'type' => $request->input('type') ?? null,
            ];

            return UploadFileHelper::getTempFile($new_request);
        }

        return [
            'success' => false,
            'message' =>  __('app.actions.upload.error'),
        ];
    }

    public function list_price()
    {
        return Item::getListPriceItems();
    }

    /**
     *
     * Obtener registros para scroll infinito
     * Se usa cursor-based pagination para mejor rendimiento
     *
     * Parámetros soportados:
     * - limit: cantidad de registros (máximo 100, default 15)
     * - cursor: posición actual (null en primera petición)
     * - favorite: filtrar por favoritos (true/false)
     * - category_id: filtrar por categoría
     * - search_by_barcode: buscar por código de barras
     * - input: búsqueda por nombre/descripción (desde request.input)
     *
     * @param  Request $request
     * @return array
     */
    public function byScroll(Request $request)
    {
        $limit = $request->input('limit', config('tenant.items_per_page', 15));
        $cursor = $request->input('cursor');

        // Validar límite máximo
        $limit = min($limit, 100);

        // filterRecordsSaleApi() aplica automáticamente todos los filtros disponibles:
        // favorite, category_id, search_by_barcode, input, etc.
        $query = Item::filterRecordsSaleApi($request)
            ->orderBy('id', 'asc');

        if ($cursor) {
            $records = $query->cursorPaginate($limit, ['*'], 'cursor', $cursor);
        } else {
            $records = $query->cursorPaginate($limit);
        }

        return [
            'data' => ItemSaleCollection::make($records),
            'pagination' => [
                'next_cursor' => $records->nextCursor()?->encode() ?? null,
                'has_more' => $records->hasMorePages(),
            ]
        ];
    }

}
