<html>

<head>
    <style>
        table {
            border-spacing: 0;
        }

        table,
        th,
        td {
            border: 1px solid black;
            border-collapse: collapse;
            font: message-box;
        }

        table tbody tr td:first-child {

            /*text-align: right;*/
            /* background-color: #f2f2f2; */
        }

        p strong {
            font: message-box;
            font-weight: 600;
        }

        .header-title {
            width: 79%;
            display: inline-block;
            vertical-align: middle;
        }

        .header-title p {
            text-align: center;
            margin: 8px 0;
        }
        header {
            position: fixed;
            top: -120px;
            float:right;
            right: 0px!important;
        }
        @page {
            margin: 120px 25px 100px;
        }
        /* .header-logo {
            width: 20%;
            display: inline-block;
            vertical-align: middle;
            text-align: right;
        } */

        .header-logo img {
            height: 80px;
            margin: 20px 0;
        }
    </style>
</head>

<body>
<header>
<div class="header-logo">
        <img src="{{asset('images/contract/spring_logo.jpg')}}" />
    </div>
</header>
    <div class="header-title">
        <p><strong>Pensilvania &ndash; Resumen de Contrato</strong></p>
        <p><strong>Gas</strong></p>
    </div>
    
    <table>
        <tbody>
            <tr>
                <td>
                    <p><span style="font-weight: 400;">Informaci&oacute;n del Proveedor de Gas Natural (&ldquo;NGS&rdquo;):</span></p>
                </td>
                <td>
                    <p><span style="font-weight: 400;">Spring Energy RRH, LLC d/b/a Spring Power &amp; Gas&nbsp;</span></p>
                    <p><span style="font-weight: 400;">111 East 14</span><span style="font-weight: 400;">th</span><span style="font-weight: 400;"> Street, #105, New York, NY 10003</span></p>
                    <p><span style="font-weight: 400;">Tel&eacute;fono: 1.888.710.4782&nbsp;</span></p>
                    <p><span style="font-weight: 400;">P&aacute;gina web: www.springpowerandgas.us</span></p>
                    <p><span style="font-weight: 400;">Spring es responsable de su producto b&aacute;sico de gas/cargos de suministro.</span></p>
                </td>
            </tr>
            <tr>
                <td>
                    <p><span style="font-weight: 400;">Estructura de precio</span></p>
                </td>
                <td>
                    <p><span style="font-weight: 400;">Precio variable. El precio de todo el gas natural vendido en virtud del presente Acuerdo deber&aacute; ser un precio variable por termia que habr&aacute; de reflejar el costo de Spring en obtener gas natural de todas fuentes&nbsp; (incluyendo producto b&aacute;sico, capacidad, almacenamiento y balanceo),&nbsp;</span></p>
                    <p><span style="font-weight: 400;">compensaciones, transporte hasta el Punto de Entrega, y otros factores relacionados con el mercado, adem&aacute;s de todos los impuestos aplicables, tasas, cargos u otras evaluaciones y costos, gastos y m&aacute;rgenes de Spring. No hay l&iacute;mite en la tasa variable, y no hay l&iacute;mite en cu&aacute;nto puede cambiar el precio de un ciclo de facturaci&oacute;n al siguiente. Se le notificar&aacute; sobre los cambios de precio cuando el nuevo precio aparezca en su facture. Por favor este al tanto, si en caso de un cambio en capacidad, transmicion, cargos relacionados con transimicion, y/o regulatorios u otros, incluyendo cambios en la etiquetas ICAP, Spring reserva el derecho de incrementar el precio y/o terminar este Acuerdo.</span></p>
                </td>
            </tr>
            <tr>
                <td>
                    <p><span style="font-weight: 400;">Precio del suministro de gas natural</span></p>
                </td>
                <td>
                    
                    <p><span style="font-weight: 400;">Su precio inicial bajo este Acuerdo de tasa variable es </span><span style="font-weight: 400;">{{($gasData['rate']) ?? ''}}</span><span style="font-weight: 400;"> por {{($gasData['unit']) ?? ''}}, efectivo en su primer ciclo de facturaci&oacute;n. El precio por el segundo ciclo de facuracion es de $</span><span style="font-weight: 400;">{{$gasData['Rate2'] ?? ''}}</span><span style="font-weight: 400;"> por Ccf, y luego variar&aacute; mes a mes seg&uacute;n los factores descriptos anteriormente.</span></p>
                </td>
            </tr>
            <tr>
                <td>
                    <p><span style="font-weight: 400;">Declaraci&oacute;n sobre ahorros</span></p>
                </td>
                <td>
                    <p><span style="font-weight: 400;">El precio del suministro de gas natural de Spring puede ser m&aacute;s alto que el precio de la </span><span style="font-weight: 400;">NGDC</span><span style="font-weight: 400;"> en cualquier mes dado, y no hay garant&iacute;a de ahorro.</span><span style="font-weight: 400;">&nbsp;</span></p>
                </td>
            </tr>
            <tr>
                <td>
                    <p><span style="font-weight: 400;">Fecha de inicio del contrato</span></p>
                </td>
                <td>
                    <p><span style="font-weight: 400;">Spring comenzar&aacute; a suministrar el servicio de suministro de gas natural en una fecha establecida por la NGDC.</span></p>
                </td>
            </tr>
            <tr>
                <td>
                    <p><span style="font-weight: 400;">Incentivos</span></p>
                </td>
                <td>
                    <p><span style="font-weight: 400;">El plan Ecogold Zero Gas de Spring asegura que Spring compensa las emisiones de di&oacute;xido de carbono asociadas con el consumo de gas natural comprando compensaciones de carbono. Las compensaciones de carbono proceden de&nbsp; proyectos forestales. Ver secci&oacute;n 6 para m&aacute;s detalle. El Cliente puede seleccionar una de las dos opciones de recompensas: YA SEA (1) "5% Recompensas Ecogold" O (2) "3% Reembolso en efectivo". Las recompensas se calculan en funci&oacute;n de los cargos de suministro de productos b&aacute;sicos del Cliente. Vea la Secci&oacute;n 4 de Recompensas de Suministro para m&aacute;s detalles.</span></p>
                </td>
            </tr>
            <tr>
                <td>
                    <p><span style="font-weight: 400;">Requisitos de dep&oacute;sito</span></p>
                </td>
                <td>
                    <p><span style="font-weight: 400;">Ninguno</span></p>
                </td>
            </tr>
            <tr>
                <td>
                    <p><span style="font-weight: 400;">Plazo del Contrato/Duraci&oacute;n</span></p>
                </td>
                <td>
                    <p><span style="font-weight: 400;">Mes a mes</span></p>
                </td>
            </tr>
            <tr>
                <td>
                    <p><span style="font-weight: 400;">Cancelaci&oacute;n</span></p>
                    <p><span style="font-weight: 400;">/Cargo por Cancelaci&oacute;n anticipada</span></p>
                </td>
                <td>
                    <p><span style="font-weight: 400;">No hay cargo por cancelaci&oacute;n anticipada.</span></p>
                </td>
            </tr>
            <tr>
                <td>
                    <p><span style="font-weight: 400;">Fin de Contrato</span></p>
                </td>
                <td>
                    <p><span style="font-weight: 400;">Este Acuerdo continuar&aacute; mes a mes hasta que sea cancelado por el Cliente o por Spring.</span></p>
                </td>
            </tr>
        </tbody>
    </table>
    <p><span style="font-weight: 400;">Version 08012020</span></p>
</body>

</html>