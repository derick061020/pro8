@php
    $company = App\Models\Tenant\Company::first();
    $serie = $data['serie'];
    $number = $data['number'];
    $motivo = $data['motivo'];  
    $created_at = $data['created_at'];
    $document_type = 'NOTA DE TRASLADO';
    $user = $data['user'];
@endphp
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type"
          content="application/pdf; charset=utf-8"/>
    <meta http-equiv="X-UA-Compatible"
          content="ie=edge">
    <title>Reporte</title>
    @include('inventory::transfers.export.partial.style')


</head>
<body>
<div>

    <table class="no-border">
        <tr>
            <td
                colspan="4"
                align="left"
                style="max-width: 300px; height: auto;"
            >
                <table style="border:2px solid black; max-width: 150px;" >
                    <tr>
                        <td
                            align="center"
                        >
                            <h3 class="font-bold">{{ 'R.U.C. '.$company->number }}</h3>
                            <h3 class="text-center font-bold">{{ $document_type }}</h3>
                            <h3 class="text-center font-bold">{{ $serie }} - {{ $number }}</h3>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</div>
<div style="margin-top: 20px">
    <table  class="no-border">
        <tr>
            <td>
                <table  class="no-border">
                    <tr>
                        <td
                            {{-- colspan="2" --}}
                        >
                            <strong>MOTIVO</strong>
                        </td>
                        <td
                            {{-- colspan="2" --}}
                        >
                            {{ $motivo }}
                        </td>
                        <td>
                            <strong>FECHA DOCUMENTO:</strong>
                        </td>
                        <td>
                            {{$created_at->format('d/m/Y')}}
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <strong>RESPONSABLE:</strong>
                        </td>
                        <td>
                            {{ $user->getName() }}
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</div>

@if (count($data) > 0)
<div class="" style="margin-top: 30px">
    <div class=" ">
        <table class="full-width">
            <thead>
            <tr>
                <th class="five-width text-center">ITEM</th>
                <th class="ten-width text-left">CODIGO INTERNO</th>
                <th class="text-left" style="width: 30%">DESCRIPCIÓN PRODUCTO</th>

                <th class="ten-width">INCIAL</th>
                <th class="ten-width">DESTINO</th>
                <th class="ten-width">CANTIDAD</th>
                {{-- <th class="ten-width">LOTE/SERIE</th> --}}
                <!--        <th width="10%">SERIE</th>-->
            </tr>
            </thead>
            <tbody>
                @foreach ($data['items'] as $item)
                    <tr>
                        <td class="celda text-center">{{$loop->index}}</td>
                        <td class="celda text-left">{{$item['internal_id']}}</td>
                        <td class="celda text-left">{{$item['description']}}</td>
                        <td class="celda text-center">{{$item['origin']}}</td>
                        <td class="celda text-center">{{$item['destination']}}</td>
                        <td class="celda">{{$item['quantity']}}</td>
                    </tr>
                @endforeach

            </tbody>
        </table>
    </div>
</div>
    
@else 
    <br>
    <div>
        <p>No se encontraron registros.</p>
    </div>

@endif

</body>
</html>


