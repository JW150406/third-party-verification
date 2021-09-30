<style>
    .select2-drop .select2-search {
    display: block !important;
}

</style>
<script>
    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#imagePreview').attr('src', e.target.result);
            }
            reader.readAsDataURL(input.files[0]);
            $("#file-remove").show();
        }
    }
    $("#file-input").change(function() {
        readURL(this);
    });
    $('#password').change(function() {
        if (this.value) {
            $('#password_confirmation').prop('required', true);
        } else {
            $('#password_confirmation').prop('required', false);
        }
    });
</script>
<script>
    $(document).ready(function() {
        $("#file-remove").click(function() {
            var default_profile_photo = "{{asset('images/default_profile_photo.png')}}";
            if($(this).attr('is_has') == 1) {
                $.ajax({
                    type: "POST",
                    url: "{{route('removeProfilePhoto')}}",
                    data: {_token:"{{csrf_token()}}"}, 
                    success: function(response) {
                        if (response.status == 'success') {
                            printAjaxSuccessMsg(response.message);                           
                        } else {
                            printAjaxErrorMsg(response.message);
                        }
                        $("#file-remove").attr('is_has',0); 
                    }
                }); 
            }
            $("#imagePreview").attr('src',default_profile_photo); 
            $("#file-remove").hide();               
        });

        @if(empty(Auth::user()->profile_picture))
            $("#file-remove").hide();
        @endif
        $("#timezone-select-id select2").select2();
    });
</script>