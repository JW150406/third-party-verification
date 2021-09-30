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

            text-align: right;
            background-color: #f2f2f2;
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
        <img src="{{asset('images/rrh_contract_logo.png')}}"  />
    </div>
</header>
    <div class="header-title">
        <p><strong>Resumen de Contrato de Gas de Maryland</strong></p>
        <p><strong>{utility} OFERTA SOLO VALIDA DE {from_date} A {to_date}</strong></p>
    </div>
    
    <table>
        <tbody>
            <tr>
                <td>
                    <p><strong>Informaci&oacute;n del Proveedor de</strong></p>
                    <p><strong>Gas:</strong></p>
                </td>
                <td>
                    <p><span style="font-weight: 400;">Spring Power &amp; Gas</span></p>
                    <p><span style="font-weight: 400;">111 East 14</span><span style="font-weight: 400;">th</span><span style="font-weight: 400;"> Street #105</span></p>
                    <p><span style="font-weight: 400;">New York, NY 10003</span></p>
                    <p><span style="font-weight: 400;">Tel&eacute;fono: 1.888.710.4782&nbsp;&nbsp;</span></p>
                    <p><span style="font-weight: 400;">info@springpowerandgas.us</span></p>
                    <p><span style="font-weight: 400;">www.springpowerandgas.us</span></p>
                    <p><span style="font-weight: 400;">Licencia de Electricidad MD No: IR-3537</span></p>
                </td>
            </tr>
            <tr>
                <td>
                    <p><strong>Estructura de Precio:</strong></p>
                </td>
                <td>
                    <p><span style="font-weight: 400;">El precio de todo el gas natural vendido en virtud del presente Acuerdo ser&aacute; un precio variable por termia basado en los costos de suministro reales y estimados de Spring que habr&aacute;n de reflejar el costo de Spring para obtener gas natural de todas las fuentes (incluyendo energ&iacute;a, capacidad, almacenamiento y elementos auxiliares), las compensaciones de carbono, transmisiones relacionadas y cargos de distribuci&oacute;n y otros factores relacionados, m&aacute;s todos los impuestos, tasas, cargos u otras evaluaciones aplicables y costos, gastos, y m&aacute;rgenes de ganancias de Spring. No hay l&iacute;mite en la tasa variable, y no hay l&iacute;mite en cu&aacute;nto puede cambiar el precio de un ciclo de facturaci&oacute;n al otro. Por favor, tenga en cuenta que en el caso de que se produzcan cambios en la capacidad, transmisi&oacute;n o cargos relacionados con la transmisi&oacute;n, y/o haya cambios regulatorios o de otro tipo, incluyendo cambios en las etiquetas ICAP, Spring se reserva el derecho de incrementar el precio y/o rescindir este acuerdo.</span></p>
                </td>
            </tr>
            <tr>
                <td>
                    <p><strong>Precio de Suministro:</strong></p>
                </td>
                <td>
                    <?php $rateArr = explode('per', $program_info['Rate']); ?>
                    <p><span style="font-weight: 400;">Su precio inicial bajo este contrato de tasa variable es {{$gasData['rate']}} por termia para su primer ciclo de facturaci&oacute;n. A partir de entonces, su precio variar&aacute; con cada ciclo de facturaci&oacute;n seg&uacute;n los factores descritos anteriormente. El precio mensual de Spring est&aacute; disponible llamando a Spring en 1.888.710.4782.</span></p>
                </td>
            </tr>
            <tr>
                <td>
                    <p><strong>Ahorros:</strong></p>
                </td>
                <td>
                    <p><span style="font-weight: 400;">No hay ahorros garantizados.</span></p>
                </td>
            </tr>
            <tr>
                <td>
                    <p><strong>Incentivos:</strong></p>
                </td>
                <td>
                    <p><span style="font-weight: 400;">El plan de Spring de Ecogold Zero Gas asegura que Spring compensa las emisiones de di&oacute;xido de carbono asociadas con el consumo de gas natural mediante la compra de compensaciones de carbono. Las compensaciones de carbono proceden de proyectos forestales. Consulte la Secci&oacute;n 8 de los t&eacute;rminos y condiciones para obtener m&aacute;s detalles. El cliente puede seleccionar una de las dos opciones de recompensa, YA SEA: (1) "5% de Recompensas Ecogold" O (2) " 3% en dinero efectivo". Las recompensas se calculan en funci&oacute;n de los cargos de suministro de Spring. Vea la Secci&oacute;n 6 Recompensas del suministro para m&aacute;s detalles.</span></p>
                </td>
            </tr>
            <tr>
                <td>
                    <p><strong>Fecha de Inicio del contrato:</strong></p>
                </td>
                <td>
                    <p><span style="font-weight: 400;">Este Acuerdo comenzar&aacute; cuando su Empresa de Servicio P&uacute;blico (utility) procese la inscripci&oacute;n.</span></p>
                </td>
            </tr>
            <tr>
                <td>
                    <p><strong>Duraci&oacute;n del Contracto:</strong></p>
                </td>
                <td>
                    <p><span style="font-weight: 400;">Este Acuerdo continuar&aacute; hasta que sea cancelado por usted o Spring.</span></p>
                </td>
            </tr>
            <tr>
                <td>
                    <p><strong>Cancelaci&oacute;n / Cargos por cancelaci&oacute;n anticipada:</strong></p>
                </td>
                <td>
                    <p><span style="font-weight: 400;">No hay cargo por cancelaci&oacute;n anticipada.</span></p>
                </td>
            </tr>
            <tr>
                <td>
                    <p><strong>Rescisi&oacute;n:</strong></p>
                </td>
                <td>
                    <p><span style="font-weight: 400;">Un cliente residencial puede rescindir este Acuerdo dentro de los 3 d&iacute;as h&aacute;biles siguientes a la firma o recepci&oacute;n de este Acuerdo, lo que ocurra primero, poni&eacute;ndose en contacto con Spring.</span></p>
                </td>
            </tr>
            <tr>
                <td>
                    <p><strong>Renovaci&oacute;n&nbsp;</strong></p>
                </td>
                <td>
                    <p><span style="font-weight: 400;">No aplica</span></p>
                </td>
            </tr>
        </tbody>
    </table>
    <p><strong>Para obtener informaci&oacute;n adicional, consulte los T&eacute;rminos y condiciones. Guarde este documento para su archivo. Si tiene alguna pregunta con respecto a este Acuerdo, comun&iacute;quese con su proveedor competitivo utilizando la informaci&oacute;n anterior.</strong></p>
</body>

</html>