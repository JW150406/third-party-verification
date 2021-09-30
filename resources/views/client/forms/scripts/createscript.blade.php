<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <h4 class="modal-title" id="myModalLabel"><span><?php echo getimage('images/info-modal.png'); ?></span>New Script Info</h4>
</div>
<?php
$added_fields = 0;
$formid = 0;
?>
<div class="modal-body">
    <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="arrow-up"></div>
        <div class="modal-form">
            <div class="col-xs-12 col-sm-12 col-md-12">
                <form id="addnewscriptform" role="form" method="POST" action="{{  route('client.contact-forms-new-scripts',['client_id' => $client_id, 'form_id' => $form_id,'language' => $language] ) }}">
                    {{ csrf_field() }}
                    {{ method_field('POST') }}
                    <div class="ajax-response"></div>
                    <input type="hidden" class="clientid" name="client_id" value="{{$client_id}}">
                    <input type="hidden" class="form_id" name="form_id" value="{{$form_id}}">
                    <input type="hidden" class="language" name="language" value="{{$language}}">
                    <div class="col-xs-12 col-sm-12 col-md-12">
                        <div class="form-group">
                            <label for="scripttitle">Title</label>
                            <input class="form-control" name="title" id="scripttitle" value="" type="Text" placeholder="Title">
                            <?php echo getFormIconImage("images/title.png"); ?>
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-12 col-md-12">
                        <label for="scriptfor">Script for</label>
                        <select class="form-control selectforscript" required name="scriptfor" id="scriptfor">
                            <option value="">Select</option>
                            @foreach($script_for as $key => $val)
                            <option value="{{$key}}">{{$val}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-xs-12 col-sm-12 col-md-12 modalbtns">
                        <div class="btn-group">

                            <button type="submit" class="btn btn-green">Save<span class="add"><?php echo getimage("images/save.png") ?></span></button>

                            <button type="button" class="btn btn-red" data-dismiss="modal">Cancel<span class="del"><?php echo getimage("images/cancel_w.png") ?> </span></button>

                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
<div class="modal-footer"></div>
<script>
    $('.selectforscript').select2();
</script>