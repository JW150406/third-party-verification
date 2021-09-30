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
        <p><strong>Electricidad y Certificados de Energ&iacute;a Renovable</strong></p>
    </div>
    
    <table>
        <tbody>
            <tr>
                <td>
                    <p><span style="font-weight: 400;"> Informaci&oacute;n del Proveedor de Generaci&oacute;n El&eacute;ctrica (&ldquo;EGS&rdquo;):</span></p>
                </td>
                <td>
                    <p><span style="font-weight: 400;">Spring Energy RRH, LLC d/b/a Spring Power &amp; Gas&nbsp;</span></p>
                    <p><span style="font-weight: 400;">111 East 14</span><span style="font-weight: 400;">th</span><span style="font-weight: 400;"> Street, #105</span></p>
                    <p><span style="font-weight: 400;">New York, NY 10003</span></p>
                    <p><span style="font-weight: 400;">P&aacute;gina web: </span><span style="font-weight: 400;">www.springpowerandgas.us</span></p>
                    <p><span style="font-weight: 400;">Spring es responsable de su producto b&aacute;sico de electricidad/cargos de suministro</span></p>
                </td>
            </tr>
            <tr>
                <td>
                    <p><span style="font-weight: 400;">Estructura de precio</span></p>
                </td>
                <td>
                    <p><span style="font-weight: 400;">Precio variable. El precio de toda la electricidad vendida en virtud del presente Acuerdo ser&aacute; un precio variable por kWh que habr&aacute; de reflejar el costo de Spring en obtener electricidad de todas fuentes (incluyendo&nbsp; capacidad, balanceo de energ&iacute;a, liquidaci&oacute;n y auxiliares),&nbsp; Certificados de Energ&iacute;a Renovable (&ldquo;RECs&rdquo;, por sus siglas en ingl&eacute;s), cargos relacionados con la transmisi&oacute;n, la distribuci&oacute;n, y otros factores relacionados con el mercado, adem&aacute;s de todos los impuestos, tarifas, cargos u otras evaluaciones aplicables y los costos, gastos y m&aacute;rgenes. El precio variable puede cambiar de mes a mes.&nbsp; No hay l&iacute;mite en el tipo de inter&eacute;s variable, y no hay l&iacute;mite sobre cu&aacute;nto puede cambiar el precio de un ciclo de facturaci&oacute;n al siguiente. Se le notificar&aacute; sobre los cambios de precio cuando el nuevo precio aparezca en su factura. Por favor este al tanto, si en caso de un cambio en capacidad, transmicion, cargos relacionados con transimicion, y/o regulatorios u otros, incluyendo cambios en la etiquetas ICAP, Spring reserva el derecho de incrementar el precio y/o terminar este Acuerdo.</span></p>
                </td>
            </tr>
            <tr>
                <td>
                    <p><span style="font-weight: 400;">Precio de suministro de Generaci&oacute;n de Electricidad</span></p>
                </td>
                <td>
                    <?php $rateArr = explode('per', $program_info['Rate']); ?>
                    <p><span style="font-weight: 400;">Su precio inicial bajo este Acuerdo de tasa variable es </span><span style="font-weight: 400;">{{($electricData['rate']) ?? '' }}</span> <span style="font-weight: 400;">&cent;</span><span style="font-weight: 400;">/</span><span style="font-weight: 400;">por  {{($electricData['unit']) ?? '' }}, efectivo en su primer ciclo de facturaci&oacute;n. El precio por el segundo ciclo de facuracion es de </span><span style="font-weight: 400;">{{$gasData['Rate2'] ?? ''}}</span> <span style="font-weight: 400;">&cent;</span><span style="font-weight: 400;">/</span><span style="font-weight: 400;">por kWh y, posteriormente variar&aacute; mes a mes basado en los factores descriptos anteriormente.</span></p>
                </td>
            </tr>
            <tr>
                <td>
                    <p><span style="font-weight: 400;">Declaraci&oacute;n sobre ahorros</span></p>
                </td>
                <td>
                    <p><span style="font-weight: 400;">El precio del suministro de electricidad de Spring puede ser m&aacute;s alto que el precio de la EDC en cualquier mes dado, y no hay garant&iacute;a de ahorro.</span></p>
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
                    <p><span style="font-weight: 400;">Incentivos</span></p>
                </td>
                <td>
                    <p><span style="font-weight: 400;">El programa Spring Green equipara 100% del consumo de electricidad del cliente con certificados de energ&iacute;a renovable procedentes de recursos renovables nacionales. Ver Secci&oacute;n 5 para m&aacute;s detalle. El cliente puede seleccionar&nbsp; una de dos opciones de recompensas, YA SEA (1) &ldquo;5% Recompensas Ecogold&rdquo; O (2) &ldquo;3% Reembolso en Efectivo&rdquo;. Las recompensas se calculan en base a los costos de&nbsp; suministro de los productos b&aacute;sicos del Cliente. Ver Secci&oacute;n 4 para m&aacute;s detalles.</span></p>
                </td>
            </tr>
            <tr>
                <td>
                    <p><span style="font-weight: 400;">Fecha de inicio del contrato</span></p>
                </td>
                <td>
                    <p><span style="font-weight: 400;">Spring comenzar&aacute; a suministrar el servicio de suministro de electricidad en una fecha establecida por la EDC.</span></p>
                </td>
            </tr>
            <tr>
                <td>
                    <p><span style="font-weight: 400;">Plazo del Contrato/Duraci&oacute;n</span></p>
                </td>
                <td>
                    <p><span style="font-weight: 400;">Mes a mes&nbsp;</span></p>
                </td>
            </tr>
            <tr>
                <td>
                    <p><span style="font-weight: 400;">Cancelaci&oacute;n / Cargos por cancelaci&oacute;n anticipada</span></p>
                </td>
                <td>
                    <p><span style="font-weight: 400;">No hay cargo por cancelaci&oacute;n anticipada.</span></p>
                </td>
            </tr>
            <tr>
                <td>
                    <p><span style="font-weight: 400;">T&eacute;rminos de renovaci&oacute;n</span></p>
                </td>
                <td>
                    <p><span style="font-weight: 400;">Este Acuerdo continuar&aacute; mes a mes hasta que sea cancelado por el cliente o por&nbsp; Spring.</span></p>
                </td>
            </tr>
        </tbody>
    </table>
    <p><span style="font-weight: 400;">Version 08012020</span></p>
</body>

</html>