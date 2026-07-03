@php
    $max_prices_columns = $records->map(function ($item) {
        return $item->item_unit_types->unique('id')->count();
    })->max() ?: 0;
@endphp
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type"
          content="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet; charset=utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Productos</title>
</head>
<body>
<div>
    <h3 align="center" class="title"><strong>Reporte Productos</strong></h3>

</div>
<br>
@if(!empty($records))
    <div class="">
        <div class=" ">
            <table class="">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Código interno</th>
                    <th>Nombre</th>
                    <th>Nombre alternativo</th>
                    <th>Descripción</th>
                    <th>Modelo</th>
                    <th>Unidad de medida</th>
                    <th>Posee IGV</th>
                    <th>Categoría</th>
                    <th>Marca</th>
                    @foreach($extra_data as $item)
                        <?php
                        $txt = $item;
                        if($item == 'sanitary'){
                            $txt = 'R.S.';
                        }elseif($item == 'cod_digemid'){
                            $txt = 'Cod: DIGEMID';
                        }
                        ?>
                        <th>{{$txt}}</th>

                        @endforeach
                    <th>Precio</th>
                    <th>Fecha de vencimiento</th>
                    @for($i=0;$i<$max_prices_columns;$i++)
                        <th>Unidad</th>
                        <th>Descripcion</th>
                        <th>Factor</th>
                        @foreach($price_labels as $label)
                            <th>{{ $label->label }}</th>
                        @endforeach
                    @endfor
                </tr>
                </thead>
                <tbody>
                @foreach($records as $key => $value)
                    @php
                    /** @var \App\Models\Tenant\Item $value */
                        $item_unit_types = $value->item_unit_types->unique('id')->values();
                    @endphp
                    <tr>
                        <td class="celda">{{$loop->iteration}}</td>
                        <td class="celda">{{$value->internal_id}}</td>
                        <td class="celda">{{$value->name}}</td>
                        <td class="celda">{{$value->second_name }}</td>
                        <td class="celda">{{$value->description }}</td>
                        <td class="celda">{{$value->model }}</td>
                        <td class="celda">{{$value->unit_type_id }}</td>
                        <td class="celda">{{$value->has_igv }}</td>
                        <td class="celda">{{$value->category_id }}</td>
                        <td class="celda">{{$value->brand_id }}</td>

                        @foreach($extra_data as $item)
                            <?php
                            $txt = $value->{$item} ;
                            if($item == 'sanitary'){
                                $txt = $value->getSanitary();
                            }elseif($item == 'cod_digemid'){
                                $txt = $value->getCodDigemid();
                            }
                            ?>
                            <td class="celda">{{$txt}}</td>
                        @endforeach
                        <td class="celda">{{$value->sale_unit_price }}</td>
                        <td class="celda">{{$value->date_of_due }}</td>
                        @for($i=0;$i<$max_prices_columns;$i++)
                            @php
                                $temp = $item_unit_types[$i] ?? null;
                            @endphp
                            <td>{{ $temp ? $temp->unit_type_id : '' }}</td>
                            <td>{{ $temp ? $temp->description : '' }}</td>
                            <td>{{ $temp ? $temp->quantity_unit : '' }}</td>
                            @foreach($price_labels as $label)
                                <td>{{ $temp ? (optional($temp->prices->firstWhere('price_label_id', $label->id))->price ?? 0) : '' }}</td>
                            @endforeach
                        @endfor
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@else
    <div>
        <p>No se encontraron registros.</p>
    </div>
@endif
</body>
</html>
