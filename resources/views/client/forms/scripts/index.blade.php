@extends('layouts.admin')
@section('content')
<?php
$breadcrum = array();
if (Auth::user()->access_level == 'tpv') {
  $breadcrum[] = array('link' => route('client.index'), 'text' =>  'Clients');
  $breadcrum[] = array('link' => route('client.show', $client->id), 'text' =>  $client->name);
}
$breadcrum[] = array('link' =>  route('client.contact-forms', ['id' => $client->id]), 'text' =>  'Forms');
$breadcrum[] = array('link' => route('client.contact-page-layout', ['id' => $client->id, 'form_id' => $form_id]), 'text' =>  $form_detail->formname);
$breadcrum[] = array('link' => '', 'text' =>  'Languages');
breadcrum($breadcrum);
?>


<div class="tpv-contbx">
  <div class="container">
    <div class="row">
      <div class="col-sm-12">

        @if ($message = Session::get('success'))
        <div class="alert alert-success">
          <p>{{ $message }}</p>
        </div>
        @endif
      </div>
    </div>

    <div class="row">
      <div class="col-sm-12">
        @foreach($languages as $language_shortname => $language_long_name)
        <div class="col-sm-3 script-language">
          <a href="{{ route('client.contact-forms-scripts',['client_id' => $client->id, 'form_id' => $form_id,'language' => $language_shortname]) }}" class="d">
            <div class="tile-stats tile-red">
              <div class="num">{{$language_shortname }}</div>
              <h3>{{$language_long_name}}</h3>
            </div>
          </a>
        </div>
        @endforeach

      </div>
    </div>
  </div>
</div>



@endsection