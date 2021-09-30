<html>

<head>
    <style>
        table {
            border-spacing: 0;
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
        <p><strong>Electricidad y Certificados de Energ&iacute;a Renovable de Maryland - Resumen de Contrato</strong></p>
        <p><strong>{utility} OFERTA SOLO VALIDA DE {from_date} A {to_date}</strong></p>
    </div>
   
    <table>
        <tbody>
            <tr>
                <td>
                    <p><strong>Informaci&oacute;n del Proveedor de</strong></p>
                    <p><strong>Generaci&oacute;n El&eacute;ctrica:</strong></p>
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
                    <p><span style="font-weight: 400;">El precio de toda la electricidad vendida en virtud del presente Acuerdo ser&aacute; un precio variable por kWh que habr&aacute; de reflejar el costo de Spring para obtener electricidad de todas las fuentes (incluyendo, energ&iacute;a, capacidad, asentamiento y elementos auxiliares), Certificados de Energ&iacute;a Renovable (&ldquo;RECs&rdquo;, por sus siglas en ingl&eacute;s), cargos relacionados con la transmisi&oacute;n y distribuci&oacute;n, y otros factores relacionados, m&aacute;s todos los impuestos, tasas, cargos u otras evaluaciones aplicables y costos, gastos, y m&aacute;rgenes de ganancias de Spring. No hay l&iacute;mite en el tipo de inter&eacute;s variable, y no hay l&iacute;mite sobre cu&aacute;nto el precio puede cambiar de un ciclo de facturaci&oacute;n al siguiente. Por favor, tenga en cuenta que en el caso de que se produzcan cambios en la capacidad, transmisi&oacute;n o cargos relacionados con la transmisi&oacute;n, y/o haya cambios regulatorios o de otro tipo, incluyendo cambios en las etiquetas ICAP, Spring se reserva el derecho de incrementar el precio y/o rescindir este acuerdo.</span></p>
                </td>
            </tr>
            <tr>
                <td>
                    <p><strong>Precio de Suministro:</strong></p>
                </td>
                <td>
                    <?php $rateArr = explode('per', $program_info['Rate']); ?>
                    <p><span style="font-weight: 400;">Su precio inicial bajo este contrato de tasa variable es {{$electricData['rate']}} &cent; / {{$electricData['unit']}}, efectivo para su primer ciclo de facturaci&oacute;n. A partir de entonces, su precio variar&aacute; con cada ciclo de facturaci&oacute;n seg&uacute;n los factores descriptos anteriormente. Este precio anterior refleja el costo de la electricidad proporcionada a trav&eacute;s del programa Spring Green y el Wind REC. El precio mensual de Spring est&aacute; disponible llamando a Spring en 1.888.710.4782.</span></p>
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
                    <p><span style="font-weight: 400;">El programa Spring Green empareja el 100% de la electricidad del cliente con certificados de energ&iacute;a renovable provenientes de recursos renovables nacionales, adem&aacute;s de las obligaciones de contenido renovable requeridas por la ley de Maryland. Consulte la Secci&oacute;n 7 de los t&eacute;rminos y condiciones para obtener m&aacute;s detalles. El cliente puede seleccionar una de las dos opciones de recompensa, YA SEA: (1) "5% de Recompensas Ecogold" O (2) "</span> <span style="font-weight: 400;">recompensas en efectivo de 3%". Las recompensas se calculan sobre la base de los cargos de suministro de Spring. Vea la Secci&oacute;n 6 Recompensas del suministro para m&aacute;s detalles.</span></p>
                </td>
            </tr>
            <tr>
                <td>
                    <p><strong>Fecha de Inicio del contrato:</strong></p>
                </td>
                <td>
                    <p><span style="font-weight: 400;">Este Acuerdo comenzar&aacute; cuando su utilidad procese la inscripci&oacute;n.</span></p>
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
                    <p><span style="font-weight: 400;">Un cliente residencial puede rescindir este Acuerdo en el plazo de 3 d&iacute;as h&aacute;biles despu&eacute;s de la firma o la recepci&oacute;n de este Acuerdo, lo que ocurra primero, poni&eacute;ndose en contacto con Spring.</span></p>
                </td>
            </tr>
            <tr>
                <td>
                    <p><strong>Renovaci&oacute;n&nbsp;</strong></p>
                </td>
                <td>
                    <p><span style="font-weight: 400;">No aplicable.&nbsp;</span></p>
                </td>
            </tr>
        </tbody>
    </table>
    <p><strong>Para obtener informaci&oacute;n adicional, consulte los T&eacute;rminos y condiciones. Guarde este documento para su archivo. Si tiene alguna pregunta con respecto a este Acuerdo, comun&iacute;quese con su proveedor competitivo utilizando la informaci&oacute;n anterior. </strong><span style="font-weight: 400;"><br /></span>
</body>

</html>