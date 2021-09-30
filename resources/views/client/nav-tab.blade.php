<div class="hs-wrapper">
    <ul class="nav nav-tabs" id="myTab" role="tablist">
        @if(auth()->user()->hasPermissionTo('view-client-info'))
            <li role="presentation" class="active"><a href="#About" id="about-tab" aria-controls="About" role="tab"
                                                    data-toggle="tab" aria-expanded="true">About</a></li>
        @endif
        @if((auth()->user()->hasPermissionTo('view-workflow') || auth()->user()->hasPermissionTo('view-twilio-number'))  && $client->isActive())
            <li role="presentation" class=""><a href="#PhoneNumbers" id="twilio-tab" aria-controls="PhoneNumbers" role="tab"
                                                data-toggle="tab" aria-expanded="true">Phone Numbers</a></li>
        @endif
        @if(auth()->user()->hasPermissionTo('view-sales-center'))
            <li role="presentation" class=""><a href="#SalesCenter" id="salse-tab" aria-controls="SalesCenter" role="tab"
                                                data-toggle="tab" aria-expanded="false">Sales Centers</a></li>
        @endif
        @if(Auth::user()->hasPermissionTo('view-commodities') && $client->isActive())
            <li role="presentation" class=""><a href="#commodities" id="tpv-tab" aria-controls="commodities" role="tab"
                                                data-toggle="tab" aria-expanded="false">Commodities</a></li>
        @endif
        @if($client->isActive())
        @if(Auth::user()->hasPermissionTo('view-brand-contcts'))
                <li role="presentation" class=""><a href="#BrandContacts" id="contact-tab" aria-controls="Script" role="tab"
                                                    data-toggle="tab" aria-expanded="false">Brands</a></li>
            @endif
        @endif
        @if(auth()->user()->hasPermissionTo('view-utility'))
            <li role="presentation" class=""><a href="#Utilities" id="Utilities-tab" aria-controls="Utilities" role="tab"
                                                data-toggle="tab" aria-expanded="false">Utilities</a></li>
        @endif
        @if(auth()->user()->hasPermissionTo('view-programs') && $client->isActive())
            <li role="presentation" class=""><a href="#Programs" id="Programs-tab" aria-controls="Programs" role="tab"
                                                data-toggle="tab" aria-expanded="false">Programs</a></li>
        @endif
        
        @if($client->isActive())
        @if(auth()->user()->hasPermissionTo('view-customer-type'))
            <li role="presentation" class=""><a href="#customer-types" id="customer-types-tab" aria-controls="customer-types" role="tab" data-toggle="tab" aria-expanded="false">Customer Type</a></li>
            @endif
        @endif
        @if(auth()->user()->hasPermissionTo('view-forms'))
            <li role="presentation" class=""><a href="#EnrollmentForm" id="Lead-tab" aria-controls="EnrollmentForm"
                                                role="tab" data-toggle="tab" aria-expanded="false">Enrollment Forms</a></li>
        @endif
        @if(auth()->user()->hasPermissionTo('view-client-user'))
            <li role="presentation" class=""><a href="#Users" id="about-tab" aria-controls="Script" role="tab"
                                                data-toggle="tab" aria-expanded="false">Users</a></li>
        @endif
        
        @if(auth()->user()->hasPermissionTo('view-dispositions') && $client->isActive())
            <li role="presentation" class=""><a href="#Dispositions" id="dispositions-tab" aria-controls="dispositions"
                                            role="tab" data-toggle="tab" aria-expanded="false">Dispositions</a></li>
        @endif

        @if(auth()->user()->hasPermissionTo('view-alerts') && $client->isActive())
            <li role="presentation" class=""><a href="#fraud_alerts" id="fraud_alerts-tab" aria-controls="fraud_alerts" role="tab" 
                    data-toggle="tab" aria-expanded="false">Alerts</a></li>
        @endif

        @if(auth()->user()->hasPermissionTo('view-do-not-enroll') && $client->isActive())
            <li role="presentation" class=""><a href="#doNotEnroll" id="do-not-enroll-tab" aria-controls="doNotEnroll" role="tab" 
                    data-toggle="tab" aria-expanded="false">Do Not Enroll</a></li>
        @endif

        @if(auth()->user()->hasPermissionTo('view-client-settings') && $client->isActive())
            <li role="presentation" class=""><a href="#Settings" id="Settings-tab" aria-controls="Settings" role="tab"
                                                data-toggle="tab" aria-expanded="false">Settings</a></li>

        @endif
    </ul>
</div>