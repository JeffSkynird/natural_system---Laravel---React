<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        * {
            font-family: Verdana, Geneva, sans-serif;
        }

        .title {
            display: flex;
            justify-content: center;
        }

        .personal_data>h3 {
            margin-top: 10px;
            margin-bottom: 10px;
            border-bottom-color: black;
            border-bottom-style: dashed;
            border-bottom-width: 2px;
            padding-bottom: 5px;
        }

        .personal_data {
            margin: 10px;
            background-color: whitesmoke;
            padding: 5px;
        }

        ul {
            margin: 0px;
            padding: 0px;
        }

        li {
            font-weight: bold;
            list-style: none;
            font-size: 14px;
            margin-bottom: 5px;
        }

        li>span {
            font-weight: normal;
        }

        #customers {
            font-family: Arial, Helvetica, sans-serif;
            border-collapse: collapse;
            width: 100%;
        }

        #customers td,
        #customers th {
            border: 1px solid #ddd;
            padding: 8px;
        }

        #customers tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        #customers tr:hover {
            background-color: #ddd;
        }

        #customers th {
            padding-top: 12px;
            padding-bottom: 12px;
            text-align: left;
            background-color: #3f51b5;
            color: white;
        }
    </style>
</head>

<body>
    <header class="title" style="padding-bottom:0px;margin:0px;">
        <h2 style="text-align:center;margin:0px;">REPORTE {{strtoupper($tipo)}}</h2>

    </header>
    <h3 style="text-align:center;margin-top:5px;">TOTAL {{count($data )}}</h3>
    <br>
    <section class="personal_data" style="margin-bottom:30px;">
        <table id="customers">
            <thead>

                <tr>
                    <th>Producto</th>
                    <th>Código B.</th>
                    <th>Concepto</th>
                    <th>Cantidad</th>
                    <th>Stock</th>
                    <th>Tipo</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($data as $dt)
                <tr style="background-color:#E5E5E5;">
                    <td>{{ $dt['name'] }}</td>

                    
                    <td>{{ $dt['bar_code'] }}</td>
                    <td>{{ $dt['concept']=='E'?'Entrada':'Salida'  }}</td>
                    <td>{{ $dt['quantity'] }}</td>
                    <td>{{ $dt['stock'] }}</td>
                    <td>{{$dt['type']=='V'?'Venta':($dt['type']=='C'?'Compra':'Ajuste') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </section>




</body>

</html>