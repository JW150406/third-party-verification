<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	<h4 class="modal-title" id="myModalLabel"><span><img src="{{ asset('images/info-modal.png') }}" /></span>New Sales Center Info</h4>
</div>
<div class="modal-body v-star">

	<div class="col-xs-12 col-sm-12 col-md-12">
		<div class="arrow-up"></div>
		<div class="modal-form">
			<div class="col-xs-12 col-sm-12 col-md-12">

				<form id="addnewclient_salescenter" enctype="multipart/form-data" role="form" method="POST" action="{{ route('client.salescenter.store',$client_id)}}">
					{{ csrf_field() }}
					{{ method_field('POST') }}

					<div class="ajax-response"></div>
					<div class="col-xs-12 col-sm-12 col-md-12">
						<div class="form-group">
							<label for="name">Center Name</label>
							<input id="name" autocomplete="off" type="text" class="form-control required" name="name" value="{{ old('name') }}" required placeholder="Name">
							<?php echo getFormIconImage('images/form-name.png');  ?>
						</div>
					</div>
					<div class="col-xs-12 col-sm-12 col-md-12">
						<div class="form-group">
							<label for="code">Code</label>
							<input id="code" autocomplete="off" placeholder="Code" type="text" class=" form-control required" name="code" value="{{old('code')}}" required>
							<?php echo getFormIconImage('images/code.png');  ?>
						</div>
					</div>
					<div class="col-xs-12 col-sm-12 col-md-12">
						<div class="form-group">
							<label for="street">Address</label>
							<input id="street" autocomplete="off" type="text" class="form-control required" name="street" value="{{ old('street') }}" required placeholder="Street">
							<?php echo getFormIconImage('images/location.png');  ?>
						</div>
					</div>
					<div class="col-xs-12 col-sm-6 col-md-6">
						<div class="form-group">
							<label for="city"></label>
							<input id="city" autocomplete="off" type="text" class="required" name="city" value="{{ old('city') }}" required placeholder="City">
						</div>
					</div>
					<div class="col-xs-12 col-sm-6 col-md-6">
						<div class="form-group">
							<label for="state"></label>
							<input id="state" autocomplete="off" type="text" class="required" name="state" value="{{ old('state') }}" required placeholder="State">
						</div>
					</div>
					<div class="col-xs-12 col-sm-6 col-md-6">
						<div class="form-group">
							<label for="country"></label>
							<input id="country" autocomplete="off" type="text" class="required" name="country" value="{{ old('country') }}" required placeholder="Country">
						</div>
					</div>
					<div class="col-xs-12 col-sm-6 col-md-6">
						<div class="form-group">
							<label for="zip"></label>
							<input id="zip" autocomplete="off" type="text" class="form-control" name="zip" value="{{ old('zip') }}" required placeholder="Zipcode" maxlength="7">
						</div>
					</div>
					<div class="col-xs-12 col-sm-12 col-md-12">
						<div class="form-group">
							<label for="contact">Contact</label>
							<input id="contact" type="text" value="1234567890" placeholder="Contact" class="form-control required" name="contact" required="" autofocus="">
						</div>
					</div>
					<div class="col-xs-12 col-sm-12 col-md-12">
						<h1 class="user-detail">User Detail</h1>
					</div>
					<div class="col-xs-12 col-sm-12 col-md-12">
						<div class="form-group">
							<label for="firstname">First Name</label>
							<input id="firstname" autocomplete="off" type="text" class="form-control required" name="first_name" value="" required placeholder="First Name">
							<?php echo getFormIconImage('images/form-name.png');  ?>
						</div>
					</div>
					<div class="col-xs-12 col-sm-12 col-md-12">
						<div class="form-group">
							<label for="lastname">Last Name</label>
							<input id="lastname" autocomplete="off" type="text" class="form-control required" name="last_name" value="{{ old('last_name') }}" placeholder="Last Name">
							<?php echo getFormIconImage('images/form-name.png');  ?>
						</div>
					</div>
					<div class="col-xs-12 col-sm-12 col-md-12">
						<div class="form-group">
							<label for="email">Email</label>
							<input id="email" type="text" class="form-control required" placeholder="Email" name="email" value="{{ old('email') }}" required>
							<?php echo getFormIconImage('images/form-email.png');  ?>
						</div>
					</div>
					<div class="col-xs-12 col-sm-12 col-md-12 modalbtns">
						<div class="btn-group">
							<button type="submit" class="btn btn-green">Save<span class="add"><?php echo getimage('images/save.png');  ?> </span></button>
						</div>
					</div>
				</form>

			</div>
		</div>
	</div>

</div>
<div class="modal-footer"></div>
</div>