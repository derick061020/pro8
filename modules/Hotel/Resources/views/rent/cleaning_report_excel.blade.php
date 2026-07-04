<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="Content-Type" content="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet; charset=utf-8" />
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Reporte limpieza</title>
    </head>
    <body>
        <div>
            <h3 align="center" class="title"><strong>Reporte limpieza</strong></h3>
        </div>
        <br>
        <div style="margin-top:20px; margin-bottom:15px;">
            <table>
                <tr>
                    <td>
                        <p><b>Empresa: </b></p>
                    </td>
                    <td align="center">
                        <p><strong>{{$company->name}}</strong></p>
                    </td>
                    <td>
                        <p><strong>Fecha: </strong></p>
                    </td>
                    <td align="center">
                        <p><strong>{{date('Y-m-d')}}</strong></p>
                    </td>
                </tr>
                <tr>
                    <td>
                        <p><strong>Ruc: </strong></p>
                    </td>
                    <td align="center">{{$company->number}}</td>
                    <td>
                        <p><strong>Establecimiento: </strong></p>
                    </td>
                    <td align="center">{{$establishment->address}} - {{$establishment->department->description}} - {{$establishment->district->description}}</td>
                </tr>
            </table>
        </div>
        <br>
        @if(!empty($records) && count($records) > 0)
            <div class="">
                <div class=" ">
                    <table class="">
                        <thead>
                            <tr>
                                <th class="">#</th>
                                <th class="">Habitación</th>
                                <th class="">Tipo Habitación</th>
                                <th class="">Limpiador</th>
                                <th class="">Estado</th>
                                <th class="">Fecha de inicio</th>
                                <th class="">Fecha de término</th>
                                <th class="">Duración (min)</th>
                                <th class="">Notas</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($records as $key => $value)
                            <?php
                            $iteracion = $loop->iteration;
                            ?>
                            <tr>
                                <td class="celda">
                                    {{$iteracion}}
                                </td>
                                <td class="celda">
                                    {{$value["room_name"]}}
                                </td>
                                <td class="celda">
                                    {{$value["category"]}}
                                </td>
                                <td class="celda">
                                    {{$value["cleaner_name"]}}
                                </td>
                                <td class="celda">
                                    {{$value["status"]}}
                                </td>
                                <td class="celda">
                                    {{$value["start_time"]}}
                                </td>
                                <td class="celda">
                                    {{$value["end_time"]}}
                                </td>
                                <td class="celda">
                                    {{$value["duration"]}}
                                </td>
                                <td class="celda">
                                    {{$value["notes"]}}
                                </td>
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
