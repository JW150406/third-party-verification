<div class="alert alert-warning" role="alert">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
    <div class="scrollbar-inner bk-ht">
        <p style="color: red; font-weight: bold;"> Bulk Upload Failed.</p>
        <?php    array_walk($errors,'printDataErrors'); ?>
    </div>
</div>