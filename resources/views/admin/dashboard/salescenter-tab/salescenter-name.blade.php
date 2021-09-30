@foreach($salesCenters as $salesCenter)
@if($salesCenter->id == $sId)
@php $name = $salesCenter->name @endphp
@endif
@endforeach

<div class="dashboard-box" style="height:104px;">
  <div class="salescenter-text">
    <h1 class="parent">
      <span class="resize bold">{{$name}}</span>
    </h1>
  </div>
</div>

@push('scripts')

<style>
.resize{
  font-family:"DINRegular", sans-serif;
  color:#3a58a8;
}
  .salescenter-text {
    width: 100%;
    height: 100%;
    padding: 10px;
    
  }
  .parent{
    white-space:nowrap;
    margin: 0px;
    text-align: center;
    position: relative;
    top: 50%;
    transform: translateY(-50%);
    font-size: inherit;
  }
  
</style>

<script>
  $(document).ready(function() {
    resize();
  });

  var rtime;
  var timeout = false;
  var delta = 100;
  $(window).resize(function() {
    rtime = new Date();
    if (timeout === false) {
      timeout = true;
      setTimeout(resizeend, delta);
    }
  });

  function resizeend() {
    if (new Date() - rtime < delta) {
      setTimeout(resizeend, delta);
    } else {
      timeout = false;
      resize();
    }
  }

  function resize() {
    $('.resize').each(function(i, obj) {
      $(this).css('font-size', '2em');

      while ($(this).width() > $(this).parent().width()) {
        $(this).css('font-size', (parseInt($(this).css('font-size')) - 1) + "px");
        
        
      }
    });
  }
</script>

@endpush