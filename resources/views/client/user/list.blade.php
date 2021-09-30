@extends('layouts.admin')
@section('content')
<?php
$breadcrum = array() ;
if( Auth::user()->access_level =='tpv')
{
    $breadcrum[] = array('link' => route('client.index'), 'text' =>  'Clients' );
    $breadcrum[] = array('link' => route('client.show',$client->id), 'text' =>  $client->name );
}
$breadcrum[] = array('link' => '', 'text' => 'Users');
breadcrum ($breadcrum)
?>





	<div class="tpv-contbx">
			<div class="container">
				<div class="row">
					<div class="col-xs-12 col-sm-12 col-md-12">
					  <div class="cont_bx3">

						  	<div class="col-xs-12 col-sm-12 col-md-12 tpv_heading">
								<h1>Users</h1>
							</div>
                            <?php if(Auth::user()->can(['user-create'])){ ?>
						  	<div class="tpvbtn">


                                <div class="col-xs-12 col-sm-12 col-md-12 top_sales">
                                <a class="btn btn-green" href="{{ route('client.createuser',$client_id) }}"  data-toggle="modal" data-target="#addclientuser"   >Create   @if( Auth::user()->access_level =='tpv') Client @endif  User<span class="add"><?php echo getimage('images/add.png') ?></span></a>

                             </div>
                          <?php } ?>
                          <div class="clearfix"></div>





								<div class="col-xs-12 col-sm-6 col-sm-offset-6 col-md-6 col-md-offset-6 sor_fil">
									<!-- <div class="col-xs-12 col-sm-4 col-md-4 sort">
										<div class="dropdown">
													<button class="btn dropdown-toggle" type="button" data-toggle="dropdown"><span class="sort_icon"><img src="{{ asset('images/sort.png') }}"/></span>Sort by
													<span class="drop_blk"><img src="{{ asset('images/dropicon_blk.png')}}"/></span></button>
													<ul class="dropdown-menu">
													  <li><a href="#">WTD</a></li>
													  <li><a href="#">YTD</a></li>
													  <li><a href="#">WTD</a></li>
													</ul>
										</div>
									</div> -->

									<!-- <div class="col-xs-12 col-sm-4 col-md-4 filter">
										<div class="dropdown">
													<button class="btn dropdown-toggle" type="button" data-toggle="dropdown"><span class="sort_icon"><img src="{{ asset('images/filter.png')}}"/></span>Filters
													<span class="drop_blk"><img src="{{ asset('images/dropicon_blk.png')}}"/></span></button>
													<ul class="dropdown-menu">
													  <li><a href="#">WTD</a></li>
													  <li><a href="#">YTD</a></li>
													  <li><a href="#">WTD</a></li>
													</ul>
										</div>
									</div> -->

									<!-- <div class="col-xs-12 col-sm-4 col-md-4 search">
										<div class="search-container">
											<form action="">
											   <button type="submit"><img src="{{ asset('images/search.png')}}"/></button>
											   <input type="text" placeholder="Search" name="search">
											</form>
										</div>
									</div> -->

								</div>
							</div>
							@if ($message = Session::get('success'))
                                <div class="tpvbtn">
                                    <div class="col-xs-12 col-sm-12 col-md-12">
                                    <div class="alert alert-success">
                                        <p>{{ $message }}</p>
                                    </div>
                                    </div>
                                </div>
                            @endif
						  <div class="col-xs-12 col-sm-12 col-md-12 sales_tablebx">
								<div class="table-responsive">

                            <table class="table responsive">
                            <thead>
                            <tr class="heading">
                                <th>No</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Title</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                           <?php $i = 0; ?>
                            @foreach ($client_users as $key => $client_user)
                                <?php if($i % 2 == 0){
                                     $first_last_td_class = "light_c";
                                     $second_and_middle_td_class = "white_c";
                                }else{
                                    $first_last_td_class = "dark_c";
                                    $second_and_middle_td_class = "grey_c";
                                }
                                ?>
                                <tr>
                                    <td class="{{$first_last_td_class}}">{{ ++$i }}</td>
                                    <td class="{{$second_and_middle_td_class}}"><a href="{{ route('client.user.show',['id' => $client_id, 'userid' =>$client_user->id  ]) }}">{{ $client_user->first_name }} {{ $client_user->last_name }}</a></td>
                                    <td class="{{$second_and_middle_td_class}}">{{ $client_user->email }}</td>
                                    <td class="{{$second_and_middle_td_class}}">{{ $client_user->title }}</td>
                                    <td class="{{$first_last_td_class}}">
                                       <div class="btn-group">
												<a class="btn"  data-toggle="tooltip" data-placement="top" data-container="body" title="" data-original-title="View User Info" href="{{ route('client.user.show',['id' => $client_id, 'userid' =>$client_user->id  ]) }}" role="button"><?php echo getimage("images/view.png"); ?> </a>
												<a class="btn"  data-toggle="tooltip" data-placement="top" data-container="body" title="" data-original-title="Edit User"  href="{{ route('client.user.edit',['id' => $client_id, 'userid' =>$client_user->id  ]) }}" role="button"><?php echo getimage("images/edit.png"); ?> </a>

											  </div>

                                              <?php if($client_user->status=='active'){ ?>
                                                    <a class="deactivate-clientuser btn"  href="javascript:void(0)" data-toggle="tooltip" data-placement="top" data-container="body" title="" data-original-title="Deactivate User" data-uid="{{ $client_user->id }}" id="delete-clientuser-{{ $client_user->id }}" data-clientusername="{{ $client_user->first_name }} {{ $client_user->last_name }}"> <?php echo getimage("images/activate_new.png"); ?></a>
                                                        <?php } else {?>
                                                            <a class="activate-clientuser btn"  href="javascript:void(0)" data-toggle="tooltip" data-placement="top" data-container="body" title="" data-original-title="Activate User"  data-uid="{{ $client_user->id }}" id="delete-clientuser-{{ $client_user->id }}" data-clientusername="{{ $client_user->first_name }} {{ $client_user->last_name }}"><?php echo getimage("images/deactivate_new.png"); ?></a>
                                                     <?php }?>
                                    </td>
                                </tr>
                            @endforeach

                            @if(count($client_users)==0)
                              <tr class="list-users">
                                 <td colspan="5" class="text-center">No Record Found</td>
                             </tr>
                            @endif
                            </tbody>
                        </table>
                        <div class="btnintable bottom_btns">
                        {!! $client_users->render() !!}
                        </div>





							 </div>
					    </div>
					</div>
				</div>
			</div>
		</div>
	</div>



    @include('client.user.clientuserpoup')
    @include('client.user.addnewclientuserpopup')

@endsection
