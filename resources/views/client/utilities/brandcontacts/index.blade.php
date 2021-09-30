@extends('layouts.admin')
@section('content')
<?php
$breadcrum = array();

$breadcrum[] =  array('link' => '', 'text' =>  'Brand Contacts');

breadcrum($breadcrum);
?>

<div class="tpv-contbx">
  <div class="container">
    <div class="col-xs-12 col-sm-12 col-md-12">
      <div class="cont_bx3">
        @if (count($errors) > 0)
        <div class="alert alert-danger">
          <strong>Whoops!</strong> There were some problems with your input.<br><br>
          <ul>
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
        @endif
        @if ($message = Session::get('success'))
        <div class="alert alert-success">
          <p>{{ $message }}</p>
        </div>
        @endif
        <div class="col-xs-12 col-sm-12 col-md-12">
          <div class="client-bg-white">
            <h1>Brand Contacts</h1>
            <div class="row">
              <div class="col-xs-12 col-sm-12 col-md-12 sales_tablebx">


                <?php if (Auth::user()->can(['create-update-utility'])) { ?>
                  <a href="javascript:void();" class="btn btn-green pull-right" data-toggle="modal" data-target="#addnew_contact">
                    Add New</a>
                  <br />
                  <br />
                <?php } ?>


                <div class="clearfix"></div>
                <div class="table-responsive">
                  <table class="table mt30">
                    <thead>
                      <tr class="heading">
                        <th>Name</th>
                        <th>Contact</th>
                        <th>Action</th>

                      </tr>
                    </thead>
                    <tbody>
                      <?php $i = 0; ?>
                      @foreach ($contacts_list as $key => $contact)
                      <?php
                      $i++;
                      if ($i % 2 == 0) {
                        $first_last_td_class = "light_c";
                        $second_and_middle_td_class = "white_c";
                      } else {
                        $first_last_td_class = "dark_c";
                        $second_and_middle_td_class = "grey_c";
                      }
                      ?>
                      <tr class="list-users">
                        <td class="{{$first_last_td_class}}">{{ $contact->name }}</td>
                        <td class="{{$second_and_middle_td_class}}">{{ $contact->contact }}</td>

                        <td class="{{$first_last_td_class}}">

                          <a class="btn" href="{{ route('brandcontact.edit',$contact->id) }}" data-toggle="tooltip" data-placement="top" data-container="body" title="" data-original-title="Edit Contact" role="button"><?php echo getimage("images/edit.png"); ?></a>

                          <a class="btn delete-brandcontact" href="javascript:void(0);" data-toggle="tooltip" data-placement="top" data-container="body" title="" data-original-title="Delete Contact" data-cname="{{ $contact->name }}" data-id="{{$contact->id }}" role="button"><?php echo getimage("images/cancel.png"); ?></a>

                        </td>

                      </tr>
                      @endforeach

                      @if(count($contacts_list)==0)
                      <tr class="list-users">
                        <td colspan="3" class="text-center">No Record Found</td>
                      </tr>
                      @endif
                    </tbody>
                  </table>

                  @if(count($contacts_list)>0)
                  <div class="btnintable bottom_btns">
                    {!! $contacts_list->links() !!}
                  </div>
                  @endif

                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>


@include('client.utilities.brandcontacts.deletecontactpopup')

<!-- Modal Starts -->

<div class="team-addnewmodal">
  <div class="modal fade" id="addnew_contact" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title" id="myModalLabel"><span><?php echo getimage('images/info-modal.png'); ?></span>Add brand contact</h4>
        </div>
        <div class="modal-body">

          <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="arrow-up"></div>
            <div class="modal-form">
              <div class="col-xs-12 col-sm-12 col-md-12">

                <form class="" id="addnewbrandcontact" enctype="multipart/form-data" role="form" method="POST" action="{{ route('brandcontact.savenewcontact') }}">
                  {{ csrf_field() }}
                  <div class="ajax-response"></div>
                  <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                      <label for="name">Select Brand</label>
                      <select name="brandname" id="brandname" class="selectsearch">
                        <option value="">Select</option>
                        @if( count($brandnames) > 0 )
                        @foreach($brandnames as $brand)
                        <option value="{{$brand->utilityname}}">{{$brand->utilityname}}</option>
                        @endforeach
                        @endif
                      </select>

                    </div>
                  </div>
                  <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                      <label for="clientcode">Contact Number</label>
                      <input autocomplete="off" type="text" class="form-control required" name="contact" value="" placeholder="Contact number">

                    </div>
                  </div>
                  <div class="col-xs-12 col-sm-12 col-md-12 modalbtns">
                    <div class="col-xs-12 col-sm-12 col-md-12 text-center">
                      <div class="btn-group">
                        <button type="submit" class="btn btn-green"><span class="save-text">Save</span></button>
                        <button type="button" class="btn btn-red" data-dismiss="modal">Cancel</button>

                      </div>
                    </div>
                  </div>
                </form>

              </div>
            </div>
          </div>

        </div>
        <div class="modal-footer"></div>
      </div>
    </div>
  </div>
</div>

<!-- Modal ends -->
@endsection
