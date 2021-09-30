<div class="team-addnewmodal v-star">
    <div class="modal fade" id="addnew_utility" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="myModalLabel"><span><?php echo getimage("images/info-modal.png"); ?></span>Utility Detail</h4>
			</div>
			<div class="modal-body">
				<div class="modal-form row">
					<div class="col-xs-12 col-sm-12 col-md-12">

						<form enctype="multipart/form-data" id="addnewutility" role="form" method="POST" action="{{ route('client.utility.addnew',['client' => $client_id]) }}">
							{{ csrf_field() }}
							{{ method_field('POST') }}
							<div class="ajax-response"></div>
							<div class="col-xs-12 col-sm-12 col-md-12">
								<label for="commodity">Commodity</label>
								<div class="dropdown form-group">
									<input id="commodity" autocomplete="off" type="text" class="form-control required" name="commodity" value="" required placeholder="Commodity">

								</div>
							</div>
							<div class="col-xs-12 col-sm-12 col-md-12">
								<div class="form-group">
									<label for="utilityname">Brand Name</label>
									<input id="utilityname" autocomplete="off" type="text" class="form-control required" name="utilityname" value="" required placeholder="Brand Name">
								</div>
							</div>
							<div class="col-xs-12 col-sm-12 col-md-12">
								<div class="form-group">
									<label for="market">Utility Provider</label>
									<input id="market" autocomplete="off" type="text" class="form-control required" name="market" value="" required placeholder="Utility Provider">
								</div>
							</div>
							<div class="col-xs-12 col-sm-12 col-md-12">
								<div class="form-group">
									<label for="abbreviation">Abbreviation</label>
									<input id="abbreviation" autocomplete="off" type="text" class="form-control required" name="market" value="" required placeholder="Abbreviation">
								</div>
							</div>
							<div class="col-xs-12 col-sm-12 col-md-12">
								<div class="form-group">
									<label for="abbreviation">Zipcodes</label>
									<div class="zipcode-all">
										<span class="tag">10001 <button type="button" class="close" aria-label="Close"><span aria-hidden="true">×</span></button></span>
										<span class="tag">10001 <button type="button" class="close" aria-label="Close"><span aria-hidden="true">×</span></button></span>
										<span class="tag">10001 <button type="button" class="close" aria-label="Close"><span aria-hidden="true">×</span></button></span>
										<span class="tag">10001 <button type="button" class="close" aria-label="Close"><span aria-hidden="true">×</span></button></span>
										<span class="tag">10001 <button type="button" class="close" aria-label="Close"><span aria-hidden="true">×</span></button></span>
										<span class="tag">10001 <button type="button" class="close" aria-label="Close"><span aria-hidden="true">×</span></button></span>
										<span class="tag">10001 <button type="button" class="close" aria-label="Close"><span aria-hidden="true">×</span></button></span>
										<span class="tag">10001 <button type="button" class="close" aria-label="Close"><span aria-hidden="true">×</span></button></span>
									</div>
								</div>
							</div>
							<div class="col-xs-12 col-sm-12 col-md-12">
								<div class="form-group">
									<label for="zip"></label>
									<input id="zip" autocomplete="off" type="text" class="form-control required" name="market" value="" required placeholder="Find & Add">
								</div>
							</div>


							<div class="col-xs-12 col-sm-12 col-md-12 modalbtns">
								<div class="btn-group">

									<button type="submit" class="btn btn-green"><span class="save-text">Save</span></button>

									<button type="button" class="btn btn-red" data-dismiss="modal">Cancel</button>

								</div>
							</div>
						</form>

					</div>
				</div>
			</div>
			<div class="modal-footer"></div>
		</div>
    </div>
</div>



<script>
	$('.selectmenucomodity').select2();
</script>