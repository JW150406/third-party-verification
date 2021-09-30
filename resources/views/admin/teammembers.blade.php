@extends('layouts.admin')

@section('content')

<div class="container-wrapper">
    <ol class="breadcrumb">
          <li class="breadcrumb-item active">Team Members</li>
      </ol>
    <div class="row">
      <div class="col-12">
           <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
              <thead>
                <tr>
                  <th>Name</th>
                  <th>Email</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tfoot>
                <tr>
                  <th>Name</th>
                  <th>Email</th>
                  <th>Action</th>
                </tr>
              </tfoot>
              <tbody>
              <?php foreach ($users as $user) {
              	?>
				<tr>
                  <td><?php echo  $user->name; ?></td>
                  <td><?php echo  $user->email; ?></td>
                  <td></td>
                </tr>

				    
				<?php } ?>
                
               </tbody>
             </table>
          </div>

       </div>
    </div>
    
</div>
@endsection
