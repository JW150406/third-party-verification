@extends('layouts.admin')
@section('content')

<?php
$breadcrum = array();
if (Auth::user()->access_level == 'tpv') {
    $breadcrum[] =  array('link' => route('client.index'), 'text' =>  'Clients');
}
$breadcrum[] = array('link' => route('client.show', array_get($client, 'id')), 'text' =>  array_get($client, 'name'));
$breadcrum[] = array('link' => route('client.show', array_get($client, 'id')) . "#EnrollmentForm", 'text' =>  'Forms');
$breadcrum[] = array('link' => route('client.contact-page-layout', array(array_get($client, 'id'),  array_get($form, 'id'))), 'text' =>  array_get($form, 'name'));
$breadcrum[] = array('link' => "", 'text' =>  'Scripts');

breadcrum($breadcrum);
?>

<div class="tpv-contbx">
    <div class="container">
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="cont_bx3">
                    <div class="tpvbtn message">
                        @if (session()->has('success'))
                            <div class="alert alert-success alert-dismissable" data-auto-dismiss="2000">
                                <?php echo session()->get('success'); ?>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif
                        @if (session()->has('error'))
                            <div class="alert alert-error alert-dismissable">
                                <?php echo session()->get('error'); ?>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif
                    </div>
                    <div class="col-xs-12 col-sm-12 col-md-12 sales_tablebx">
                        <div class="client-bg-white min-height-solve">
                            <div class="row">
                                <div class="col-md-6 col-sm-12">
                                    <h1 class="mt10">{{ $form->formname}} Scripts</h1>

                                </div>
                                <div class="col-md-6 col-sm-12">
                                    @if($client->isActive())

                                    @if(auth()->user()->hasPermissionTo('upload-scripts'))

                                    <div class="btn-group pull-right btn-sales-all">

                                        <button type="button" class="btn btn-green dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                            Upload Scripts <span class="caret"></span>
                                        </button>
                                        <ul class="dropdown-menu upload-script-menu" role="menu">

                                            <li><a href="{{route('admin.clients.import.question',[$client->id,$form->id,config('constants.script_upload_id.bulk_upload')])}}" type="button">Bulk Upload</a>
                                            </li>
                                            <li><a href="{{route('admin.clients.import.question',[$client->id,$form->id,config('constants.script_upload_id.single_script')])}}" type="button">Single Script</a>
                                            </li>

                                        </ul>
                                    </div>
                                    @endif
                                    @endif

                                </div>
                            </div>
                            <h4 class="script-type-name"> Verification Scripts</h4>
                            <div class="table-responsive">
                                <table class="table script-table script-view-list script-list mt30" id="script-table">
                                    <thead>
                                        <tr>
                                            <td>Sr.No.</td>
                                            <td>Script Name</td>
                                            <td>Script Type</td>
                                            <td>English</td>
                                            <td>Spanish</td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                        $formScripts = config()->get('constants.scripts-new-name');
                                        $scriptType = config()->get('constants.script_type');
                                        $i = 0;
                                        @endphp
                                        @foreach($formScripts as $key => $formScript)
                                        @if(!in_array($key,config('constants.general_scripts')))
                                        <?php $i = $i + 1; ?>
                                        <tr>
                                            <td>
                                                {{ $i }}
                                            </td>

                                            <td>
                                                {{ $formScript }}
                                            </td>
                                            <td>
                                                {{ config()->get('constants.script_type.'.$key) }}

                                            </td>

                                            <td>
                                                @if($key == "salesagentintro" || $key == "after_lead_decline" || $key == "closing" || $key == "agent_not_found" || $key == "customer_call_in_verification" || $key == "identity_verification" || $key == "lead_not_found")
                                                @php
                                                $isEnglishScriptExist = $client->scripts()->where('client_id', array_get($client, 'id'))->where('form_id', 0)->where('scriptFor', $key)->where('language', 'en')->first();
                                                @endphp
                                                @if (!empty($isEnglishScriptExist))
                                                <a class="theme-color" href="{{ route('client.lead-forms.scripts.show', array(array_get($client, 'id'), array_get($form, 'id'), array_get($isEnglishScriptExist, 'id'))) }}">View</a>
                                                @else
                                                -
                                                @endif
                                                @else
                                                @php
                                                $stateEnScript = $form->scripts()->where('client_id', array_get($client, 'id'))->where('scriptFor', $key)->where('language', 'en')->where('state','!=', "all")->orderBy('state')->get();
                                                @endphp
                                                @php

                                                @endphp
                                                @if (!empty($stateEnScript))

                                                @forelse ($stateEnScript as $enState)

                                                <a class="round-state" href="{{ route('client.lead-forms.scripts.show', array(array_get($client, 'id'), array_get($form, 'id'), array_get($enState, 'id'))) }}?st={{array_get($enState, 'state')}}">{{array_get($enState, 'state')}}</a>
                                                <span class="close-script" data-id="{{ array_get($enState, 'id') }}"><i class="fa fa-close"></i></span>
                                                @empty
                                                -
                                                @endforelse
                                                @else
                                                -
                                                @endif
                                                @endif
                                            </td>
                                            <td>
                                                @if($key == "salesagentintro" || $key == "after_lead_decline" || $key == "closing" || $key == "agent_not_found" || $key == "customer_call_in_verification" || $key == "identity_verification" || $key == "lead_not_found")
                                                @php
                                                $isEsScriptExist = $client->scripts()->where('form_id', 0)->where('client_id', array_get($client, 'id'))->where('scriptFor', $key)->where('language', 'es')->first();
                                                @endphp
                                                @if (!empty($isEsScriptExist))
                                                <a class="theme-color" href="{{ route('client.lead-forms.scripts.show', array(array_get($client, 'id'), array_get($form, 'id'), array_get($isEsScriptExist, 'id'))) }}">View</a>
                                                @else
                                                -
                                                @endif
                                                @else
                                                @php
                                                $stateEsScript = $form->scripts()->where('client_id', array_get($client, 'id'))->where('scriptFor', $key)->where('language', 'es')->where('state','!=', "all")->orderBy('state')->get();
                                                @endphp
                                                @if (!empty($stateEsScript))
                                                @forelse ($stateEsScript as $esState)
                                                <a class="round-state" href="{{ route('client.lead-forms.scripts.show', array(array_get($client, 'id'), array_get($form, 'id'), array_get($esState, 'id'))) }}?st={{array_get($esState, 'state')}}">{{ array_get($esState, 'state') }}</a>
                                                <span class="close-script" data-id="{{ array_get($esState, 'id') }}"><i class="fa fa-close"></i></span>
                                                @empty
                                                -
                                                @endforelse
                                                @else
                                                -
                                                @endif
                                                @endif
                                            </td>
                                        </tr>
                                        @endif
                                        @endforeach
                                    </tbody>
                                </table>

                            </div>
                            <h4 class="script-type-name">General Scripts</h4>
                            <div class="table-responsive">
                                <table class="table script-table script-view-list script-list mt30" id="script-table">
                                    <thead>
                                        <tr>
                                            <td>Sr.No.</td>
                                            <td>Script Name</td>
                                            <td>Script Type</td>
                                            <td>English</td>
                                            <td>Spanish</td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                        $formScripts = config()->get('constants.scripts-new-name');
                                        $j = 0;
                                        @endphp
                                        @foreach($formScripts as $key => $formScript)

                                        @if($isGeneralScript=in_array($key,config('constants.general_scripts')))
                                        <tr>
                                            @php
                                            $j = $j + 1;
                                            @endphp
                                            <td>
                                                {{ $j }}
                                            </td>

                                            <td>
                                                {{ $formScript }}
                                            </td>
                                            <td>
                                                {{ config()->get('constants.script_type.'.$key) }}

                                            </td>

                                            <td>
                                                @if($isGeneralScript)
                                                @php
                                                $isEnglishScriptExist = $client->scripts()->where('client_id', array_get($client, 'id'))->where('form_id', 0)->where('scriptFor', $key)->where('language', 'en')->first();
                                                @endphp
                                                @if (!empty($isEnglishScriptExist))
                                                <a class="theme-color" href="{{ route('client.lead-forms.scripts.show', array(array_get($client, 'id'), array_get($form, 'id'), array_get($isEnglishScriptExist, 'id'))) }}">View</a>
                                                @else
                                                -
                                                @endif
                                                @endif
                                            </td>
                                            <td>
                                                @if($isGeneralScript)
                                                @php
                                                $isEsScriptExist = $client->scripts()->where('form_id', 0)->where('client_id', array_get($client, 'id'))->where('scriptFor', $key)->where('language', 'es')->first();
                                                @endphp
                                                @if (!empty($isEsScriptExist))
                                                <a class="theme-color" href="{{ route('client.lead-forms.scripts.show', array(array_get($client, 'id'), array_get($form, 'id'), array_get($isEsScriptExist, 'id'))) }}">View</a>
                                                @else
                                                -
                                                @endif

                                                @endif
                                            </td>
                                        </tr>
                                        @endif
                                        @endforeach
                                    </tbody>
                                </table>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!---script-remove-popup--->
<div class="modal fade confirmation-model" id="remove_script">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body text-center">
                <div class="mt15 text-center mb15">
                    <img src="{{ url('images/alert-danger.png') }}">
                    <p class="logout-title">Are you sure?</p>
                </div>
                <div class="mt20">
                    Are you sure you want to delete this script?
                </div>
            </div>

            <div class="modal-footer pd0">
                <div class="btnintable bottom_btns pd0">
                    <div class="btn-group">
                        <form method="post" action="{{ route('scripts.delete') }}">
                            {{csrf_field()}}
                            <input type="hidden" id="script_id" name="script_id" value="" />
                            <button type="submit" class="btn btn-green">Confirm</button>
                            <button type="button" class="btn btn-red" data-dismiss="modal">Cancel</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
    <script>
        $(document).on('click', '.close-script', function () {
            $("#remove_script").modal('show');
            $("#script_id").val($(this).data('id'));
        });
    </script>
@endpush


