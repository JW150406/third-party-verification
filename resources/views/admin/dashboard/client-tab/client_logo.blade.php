
<div class="client-logo-div text-center dashboard-box">
    <img src="" alt="Client Logo" id="client-logo" height="{{ $height }}" width="200">
</div>

@push('scripts')
<script>
    function loadClientLogo(data)
    {
        $.ajax({
            url: '{{route("dashboard.client-logo")}}',
            method:'post',
            data:data,
            success:function(data)
            {
                if(data.status == "success" && data.data != "")
                {
                    $("#client-logo").attr('src',data.data);
                }
            }
        });
    }
</script>
@endpush
