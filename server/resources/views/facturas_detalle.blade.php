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
         /*    padding-top: 12px;
            padding-bottom: 12px; */
            text-align: left;
            background-color: #3f51b5;
            color: white;
        }
    </style>
</head>

<body>
    <header class="title" style="padding-bottom:0px;margin:0px;">
        <h2 style="text-align:center;margin:0px;">REPORTE FACTURA {{$data['id']}} </h2>

    </header>
<div style="margin: 15px;">
<span style="display:block;margin-top:0px;text-align:left;">Fecha: {{$data['created_at']}}</span>
        <span style="display:block;margin-top:5px;text-align:left">Cédula / RUC: {{$data['document']!=null?$data['document']:'-'}}</span>
        <span style="display:block;margin-top:5px;text-align:left;">Cliente: {{$data['names']!=null?$data['names']:"Consumidor Final"}}</span>
      
        <h4 >Detalle:</h4>

</div>
       

    <section class="personal_data" style="margin-bottom:30px;">
        <table id="customers">
            <thead>

                <tr>
                    <th>Codigo B.</th>
                    <th>Producto</th>
                    <th>Cantidad</th>
                    <th>Total</th>
                   
                </tr>
            </thead>

            <tbody>
                @foreach ($body as $dt)
                <tr style="background-color:#E5E5E5;">
                    <td>{{ $dt['bar_code'] }}</td>
                    <td>{{ $dt['name'] }}</td>
                    <td>{{ $dt['quantity'] }}</td>
                    <td>{{ $dt['subtotal'] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </section>
    <span style="text-align:right;display:block;margin-right:10px;">Subtotal: ${{$subtotal}} </span>
    <span style="text-align:right;display:block;margin-right:10px;margin-top:5px;">IVA: ${{$iva}}</span>
    <span style="text-align:right;display:block;margin-right:10px;margin-top:5px;">Total: ${{$data['total']}} </span>


       


</body>

</html>