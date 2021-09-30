<div class="col-sm-2">
    <nav class="nav-sidebar">
        <ul class="nav">
            <!-- <li class="{{ (Request::route()->getName() == 'my-account') ? 'active' : '' }}"><a href="{{route('my-account')}}" >My Account</a></li> -->
            <li class="{{ (Request::route()->getName() == 'editprofile') ? 'active' : '' }}"><a href="{{route('editprofile')}}">Profile</a></li>
             <li class="{{ (Request::route()->getName() == 'profile.leads') ? 'active' : '' }}"><a href="{{route('profile.leads')}}">My leads</a></li>     
        </ul>
    </nav>
</div>
