<!DOCTYPE html
    PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Contract</title>
    <style>
        @page {
            margin: 100px 25px 50px;
        }
        header {
            position: fixed;
            top: -80px;
            left: 0px;
            right: 0px;
            height: 50px;
            text-align: left;
            line-height: 35px;
        }
        body {
            font-family: "Helvetica";
            padding: 15px;
            font-size: 14px;
            border: 1px solid #000;
        }
        .logo-wrapper {
            display: block;
        }
        .logo-wrapper img {
            display: block;
            margin: 0 auto;
        }
        div {
            display: block;
        }
        .text-center {
            text-align: center;
        }
        h3 {
            margin-bottom: 0px;
        }
        table {
            width: 100%;
        }
        table p span {
            color: #727171;
        }
        .customer-sign img {
            border: 2px solid #d6d6d6;
            margin-top: 20px;
        }
        .customer-sign h5 {
            font-size: 14px;
            margin: 0;
            margin-top: 15px;
        }
        .gps-title {
            margin-top: 40px;
            margin-bottom: 20px;
        }
        .mtb10{
            margin-top: 10px;
            margin-bottom: 10px;
        }
        .map-img{
            border: 2px solid #ddd;
            padding: 2px;
        }
        .clr-gry{
            color: #727171;
        }
        .marker-label {
            height: 40px
        }
        .marker-td {
            vertical-align: top
        }
    </style>
</head>

<body>

    <h3 class="text-center"> Reconozco que al firmar este contrato o acuerdo, estoy optando voluntariamente por cambiar la entidad que me suministra el servicio de gas natural. 
    </h3>

    {{-- <p>Al firmar a continuación, reconozco y acepto lo anterior también, que soy el titular de la cuenta y deseo celebrar este Acuerdo con Bolt Energy.</p> --}}

    <table>
        <tr>
            <td align="left">
                <p style="margin-bottom:20px;">
                    Nombre del cliente: <span class="underline-bottom">{{$customer_name ?? ''}}</span>
                </p>
                <p>
                    Firma del cliente: <span class="underline-bottom"><img style="height: 80px;width: 350px;" src="{{Storage::disk('s3')->url($signature)}}"></span>
                </p>
                <p>
                    Fecha: <span class="underline-bottom">{{$date ?? ''}}</span>
                </p>

            </td>
        </tr>
    </table>
</body>
</html>