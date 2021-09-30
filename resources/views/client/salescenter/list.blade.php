@extends('layouts.admin')
@section('content')
<?php
$breadcrum = array();
$breadcrum[] = array('link' => '', 'text' => 'Sales Centers');
breadcrum($breadcrum);


?>
<div class="tpv-contbx">
    <div class="container">
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="cont_bx3">

                    <div class="col-xs-12 col-sm-12 col-md-12 top_sales">
                        <div class="client-bg-white">
                            <div class="row">
                                <div class="col-md-12">
                                    <a href="{{route('client.createsalescenter',['id' =>$client_id ])}}" class="btn btn-green pull-right" data-toggle="modal" data-target="#addsalescenter" type="button">Add Sales Center</a>
                                </div>
                            </div>
                            @if ($message = Session::get('success'))
                            <div class="alert alert-success alert-dismissable">
                                {{ $message }}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            @endif

                            <?php if (Auth::user()->access_level == 'client' || Auth::user()->hasPermissionTo('view-salescenters')) { ?>
                                <div class="sales_tablebx">
                                    <div class="table-responsive">
                                        <table class="table mt30">
                                            <thead>
                                                <tr class="heading acjin">
                                                    <th>No</th>
                                                    <th>Name</th>
                                                    <th>Address</th>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $i = 0;
                                                ?>
                                                @if(count($client_salescenters) > 0)
                                                @foreach ($client_salescenters as $key => $client_salescenter)
                                                <?php if ($i % 2 == 0) {
                                                    $first_last_td_class = "light_c";
                                                    $second_and_middle_td_class = "white_c";
                                                } else {
                                                    $first_last_td_class = "dark_c";
                                                    $second_and_middle_td_class = "grey_c";
                                                }
                                                ?>
                                                <tr class="list-users">
                                                    <td class="{{$first_last_td_class}}">{{ ++$i }}</td>
                                                    <td class="{{$second_and_middle_td_class}}"><a href="{{ route('client.salescenter.show',['id' => $client_id, 'salescenter_id' =>$client_salescenter->id  ]) }}">{{ $client_salescenter->name }}</a></td>
                                                    <td class="{{$second_and_middle_td_class}}">{{ $client_salescenter->street }} {{ $client_salescenter->city }} ,{{ $client_salescenter->state }},
                                                        {{ $client_salescenter->country }}, {{ $client_salescenter->zip }}
                                                    </td>

                                                    <td class="{{$first_last_td_class}}">
                                                        <div class="btn-group">
                                                            <?php if (Auth::user()->access_level == 'client' || Auth::user()->hasPermissionTo('view-salescenters')) { ?>
                                                                <a href="{{ route('client.salescenter.show',['id' => $client_id, 'salescenter_id' =>$client_salescenter->id  ]) }}" data-toggle="tooltip" data-placement="top" data-container="body" title="" data-original-title="View Sales Center" class="btn"><?php echo getimage('images/view.png') ?></a>
                                                            <?php } ?>
                                                            <?php if (Auth::user()->access_level == 'client' || Auth::user()->hasPermissionTo('update-salescenters')) { ?>
                                                                <a class="btn" class="edit-link" href="{{ route('client.salescenter.edit',['id' => $client_id, 'userid' =>$client_salescenter->id  ]) }}" data-toggle="tooltip" data-placement="top" data-container="body" title="" data-original-title="Edit Sales Center"><?php echo getimage('/images/edit.png') ?></a>
                                                            <?php } ?>
                                                            <?php if (Auth::user()->access_level == 'client' || Auth::user()->hasPermissionTo('delete-salescenters')) { ?>
                                                                <?php if ($client_salescenter->status == 'active') { ?>
                                                                    <a class="deactivate-clientuser btn delete-link" href="javascript:void(0)" data-toggle="tooltip" data-placement="top" data-container="body" title="" data-original-title="Deactivate Sales Center" data-vid="{{ $client_salescenter->id }}" id="delete-clientuser-{{ $client_salescenter->id }}" data-clientsalescenter="{{ $client_salescenter->name }}"><?php echo getimage('/images/deactivate_new.png') ?></a>
                                                                <?php } else { ?>
                                                                    <a class="activate-clientuser success-link btn" href="javascript:void(0)" data-toggle="tooltip" data-placement="top" data-container="body" title="" data-original-title="Activate Sales Center" data-vid="{{ $client_salescenter->id }}" id="delete-clientuser-{{ $client_salescenter->id }}" data-clientsalescenter="{{ $client_salescenter->name }}"><?php echo getimage('/images/activate_new.png') ?></a>
                                                                <?php } ?>
                                                            <?php } ?>
                                                        </div>
                                                    </td>
                                                </tr>
                                                @endforeach
                                                @endif
                                                @if(count($client_salescenters)==0)
                                                <tr class="list-users">
                                                    <td colspan="4" class="text-center">No Record Found</td>
                                                </tr>
                                                @endif
                                            </tbody>
                                        </table>

                                        {!! $client_salescenters->render() !!}
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



@include('client.salescenter.salescenterpoup')

<div class="team-addnewmodal">
    <div class="modal fade" id="addsalescenter" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">

            </div>
        </div>
    </div>


    @endsection
