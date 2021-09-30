@extends('layouts.selfverify')
@push('styles')
<link rel="stylesheet" href="{{ asset('css/jquery.signature.css') }}">
<style>
    .kbw-signature { width: 100%; height: 200px;}

    #sig canvas{
        width: 100% !important;
        height: auto;
    }
</style>
@endpush
@section('content')
<div class="">
    <div class="container">
        <div class="row">
           <div class="col-md-6 offset-md-3 mt-5">
               <div class="card">
                   <div class="card-header">
                       <h5>Signature demo </h5>
                   </div>
                   <div class="card-body">
                        @if ($message = Session::get('success'))
                            <div class="alert alert-success  alert-dismissible">
                                <button type="button" class="close" data-dismiss="alert">×</button>
                                <strong>{{ $message }}</strong>
                            </div>
                        @endif
                        <div class="row">
                            <div class="col-md-2 col-xs-2">
                                Name:
                            </div>
                            <div class="col-md-10 col-xs-10">
                                Ashish
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-2 col-xs-2">
                                Phone:
                            </div>
                            <div class="col-md-10 col-xs-10">
                                1234567890
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-2 col-xs-2">
                                Email:
                            </div>
                            <div class="col-md-10 col-xs-10">
                                ash@gmail.com
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-2 col-xs-2">
                                Address:
                            </div>
                            <div class="col-md-10 col-xs-10">
                                TPV360, 6285 Northam Drive 4th Floor, Mississauga, Ontario L4V 1X5, Canada, +1 647-499-8163
                            </div>
                        </div>
                        <div class="row">
                            <form method="POST" action="">
                                @csrf
                                <div class="col-md-12 col-xs-12">
                                    <label class="" for="">Signature:</label>
                                    <br/>
                                    <div id="sig" ></div>
                                    <br/>
                                    <textarea id="signature64" name="signed" style="display: none"></textarea>
                                </div>
                                <br/>
                                <div class="col-md-10 col-xs-10">
                                    <button id="clear" class="btn btn-danger btn-sm">Clear Signature</button>
                                    <button class="btn btn-success" type="button">Save</button>
                                </div>

                            </form>
                        </div>

                   </div>

               </div>

           </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
    <script src="{{ asset('js/jquery.signature.js') }}"></script>
    <script src="{{ asset('js/jquery.ui.touch-punch.min.js') }}"></script>
    <script type="text/javascript">
        var sig = $('#sig').signature({syncField: '#signature64', syncFormat: 'PNG'});

        $('#clear').click(function(e) {

            e.preventDefault();

            sig.signature('clear');

            $("#signature64").val('');

        });
    </script>
@endpush