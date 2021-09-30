<?php
use App\models\Role;
use App\models\Permission;
use App\models\PermissionAccessLevel;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /*DB::statement('SET FOREIGN_KEY_CHECKS=0');
        //DB::table('permission_role')->truncate();
        DB::table('permissions')->truncate();
        DB::table('permission_access_levels')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');*/
        $permissions =  [
            [
                "name" => "dashboard",
                "display_name" => "Dashboard",
                "description" => "Dashboard",
                "group" => "Dashboard",
                "group_order" => 1,
            ],
            [
                "name" => "all-clients",
                "display_name" => "All clients",
                "description" => "All clients",
                "group" => "Clients",
                "group_order" => 2
            ],
            [
                "name" => "add-client",
                "display_name" => "Add client",
                "description" => "Add client",
                "group" => "Clients",
                "group_order" => 2
            ],
            [
                "name" => "view-client-info",
                "display_name" => "View client info",
                "description" => "View client info",
                "group" => "Clients",
                "group_order" => 2
            ],
            [
                "name" => "edit-client-info",
                "display_name" => "Edit client info",
                "description" => "Edit client info",
                "group" => "Clients",
                "group_order" => 2
            ],
            [
                "name" => "deactivate-client",
                "display_name" => "Deactivate client",
                "description" => "Deactivate client",
                "group" => "Clients",
                "group_order" => 2
            ],
            [
                "name" => "delete-client",
                "display_name" => "Delete client",
                "description" => "Delete client",
                "group" => "Clients",
                "group_order" => 2
            ],
            [
                "name" => "view-workflow",
                "display_name" => "View workflows",
                "description" => "View workflows",
                "group" => "Phone Numbers",
                "group_order" => 3
            ],
            [
                "name" => "edit-workflow",
                "display_name" => "Edit workflows",
                "description" => "Edit workflows",
                "group" => "Phone Numbers",
                "group_order" => 3
            ],
            [
                "name" => "delete-workflow",
                "display_name" => "Delete workflows",
                "description" => "Delete workflows",
                "group" => "Phone Numbers",
                "group_order" => 3
            ],
            [
                "name" => "add-workflow",
                "display_name" => "Add workflows",
                "description" => "Add workflows",
                "group" => "Phone Numbers",
                "group_order" => 3
            ],
            [
                "name" => "view-twilio-number",
                "display_name" => "View Twilio numbers",
                "description" => "View Twilio numbers",
                "group" => "Phone Numbers",
                "group_order" => 3
            ],
            [
                "name" => "edit-twilio-number",
                "display_name" => "Edit Twilio numbers",
                "description" => "Edit Twilio numbers",
                "group" => "Phone Numbers",
                "group_order" => 3
            ],
            [
                "name" => "delete-twilio-number",
                "display_name" => "Delete Twilio numbers",
                "description" => "Delete Twilio numbers",
                "group" => "Phone Numbers",
                "group_order" => 3
            ],
            [
                "name" => "add-twilio-number",
                "display_name" => "Add Twilio numbers",
                "description" => "Add Twilio numbers",
                "group" => "Phone Numbers",
                "group_order" => 3
            ],
            [
                "name" => "add-sales-center",
                "display_name" => "Add sales center",
                "description" => "Add sales center",
                "group" => "Sales Centers",
                "group_order" => 4
            ],
            [
                "name" => "view-sales-center",
                "display_name" => "View sales center",
                "description" => "View sales center",
                "group" => "Sales Centers",
                "group_order" => 4
            ],
            [
                "name" => "edit-sales-center",
                "display_name" => "Edit sales center",
                "description" => "Edit sales center",
                "group" => "Sales Centers",
                "group_order" => 4
            ],
            [
                "name" => "deactivate-sales-center",
                "display_name" => "Deactivate sales center",
                "description" => "Deactivate sales center",
                "group" => "Sales Centers",
                "group_order" => 4
            ],
            [
                "name" => "delete-sales-center",
                "display_name" => "Delete sales center",
                "description" => "Delete sales center",
                "group" => "Sales Centers",
                "group_order" => 4
            ],
            [
                "name" => "delete-sales-center-location",
                "display_name" => "Delete sales center location",
                "description" => "Delete sales center location",
                "group" => "Sales Centers",
                "group_order" => 4
            ],
            [
                "name" => "view-brand-info",
                "display_name" => "View Brand Info",
                "description" => "View Brand Info",
                "group" => "Sales Centers",
                "group_order" => 4
            ],
            [
                "name" => "edit-brand-info",
                "display_name" => "Edit Brand Info",
                "description" => "Edit Brand Info",
                "group" => "Sales Centers",
                "group_order" => 4
            ],
            [
                "name" => "view-commodities",
                "display_name" => "View Commodities",
                "description" => "View Commodities",
                "group" => "Commodities",
                "group_order" => 5
            ],
            [
                "name" => "add-new-commodity",
                "display_name" => "Add new commodity",
                "description" => "Add new commodity",
                "group" => "Commodities",
                "group_order" => 5
            ],
            [
                "name" => "edit-commodity",
                "display_name" => "Edit commodity",
                "description" => "Edit commodity",
                "group" => "Commodities",
                "group_order" => 5
            ],
            [
                "name" => "delete-commodity",
                "display_name" => "Delete commodity",
                "description" => "Delete commodity",
                "group" => "Commodities",
                "group_order" => 5
            ],
            [
                "name" => "add-utility-provider",
                "display_name" => "Add utility provider",
                "description" => "Add utility provider",
                "group" => "Utilities",
                "group_order" => 6
            ],
            [
                "name" => "view-utility",
                "display_name" => "View utility",
                "description" => "View utility",
                "group" => "Utilities",
                "group_order" => 6
            ],
            [
                "name" => "edit-utility",
                "display_name" => "Edit utility",
                "description" => "Edit utility",
                "group" => "Utilities",
                "group_order" => 6
            ],
            [
                "name" => "bulk-upload-utility",
                "display_name" => "Bulk upload utility",
                "description" => "Bulk upload utility",
                "group" => "Utilities",
                "group_order" => 6
            ],
            [
                "name" => "export-utility",
                "display_name" => "Export utility",
                "description" => "Export utility",
                "group" => "Utilities",
                "group_order" => 6
            ],
            [
                "name" => "delete-utility",
                "display_name" => "Delete utility",
                "description" => "Delete utility",
                "group" => "Utilities",
                "group_order" => 6
            ],
            [
                "name" => "add-program",
                "display_name" => "Add program",
                "description" => "Add program",
                "group" => "Programs",
                "group_order" => 7
            ],
            [
                "name" => "edit-program",
                "display_name" => "Edit program",
                "description" => "Edit program",
                "group" => "Programs",
                "group_order" => 7
            ],
            [
                "name" => "view-programs",
                "display_name" => "View programs",
                "description" => "View programs",
                "group" => "Programs",
                "group_order" => 7
            ],
            [
                "name" => "bulk-upload-program",
                "display_name" => "Bulk upload programs",
                "description" => "Bulk upload programs",
                "group" => "Programs",
                "group_order" => 7
            ],
            [
                "name" => "export-program",
                "display_name" => "Export programs",
                "description" => "Export programs",
                "group" => "Programs",
                "group_order" => 7
            ],
            [
                "name" => "deactivate-program",
                "display_name" => "Deactivate program",
                "description" => "Deactivate program",
                "group" => "Programs",
                "group_order" => 7
            ],
            [
                "name" => "delete-program",
                "display_name" => "Delete program",
                "description" => "Delete program",
                "group" => "Programs",
                "group_order" => 7
            ],
            [
                "name" => "add-new-form",
                "display_name" => "Add new form",
                "description" => "Add new form",
                "group" => "Lead Forms",
                "group_order" => 9
            ],
            [
                "name" => "view-forms",
                "display_name" => "View form",
                "description" => "View form",
                "group" => "Lead Forms",
                "group_order" => 9
            ],
            [
                "name" => "edit-form",
                "display_name" => "Edit form",
                "description" => "Edit form",
                "group" => "Lead Forms",
                "group_order" => 9
            ],
            [
                "name" => "copy-form",
                "display_name" => "Clone form",
                "description" => "Clone form",
                "group" => "Lead Forms",
                "group_order" => 9
            ],
            [
                "name" => "deactivate-form",
                "display_name" => "Deactivate form",
                "description" => "Deactivate form",
                "group" => "Lead Forms",
                "group_order" => 9
            ],
            [
                "name" => "delete-form",
                "display_name" => "Delete form",
                "description" => "Delete form",
                "group" => "Lead Forms",
                "group_order" => 9
            ],
            [
                "name" => "view-scripts",
                "display_name" => "View scripts",
                "description" => "View scripts",
                "group" => "Lead Forms",
                "group_order" => 9
            ],
            [
                "name" => "upload-scripts",
                "display_name" => "Upload scripts",
                "description" => "Upload scripts",
                "group" => "Lead Forms",
                "group_order" => 9
            ],
            [
                "name" => "view-client-user",
                "display_name" => "View client user",
                "description" => "View client user",
                "group" => "Client Users",
                "group_order" => 10
            ],
            [
                "name" => "add-client-user",
                "display_name" => "Add client user",
                "description" => "Add client user",
                "group" => "Client Users",
                "group_order" => 10
            ],
            [
                "name" => "edit-client-user",
                "display_name" => "Edit client user",
                "description" => "Edit client user",
                "group" => "Client Users",
                "group_order" => 10
            ],
            [
                "name" => "deactivate-client-user",
                "display_name" => "Deactivate client user",
                "description" => "Deactivate client user",
                "group" => "Client Users",
                "group_order" => 10
            ],
            [
                "name" => "delete-client-user",
                "display_name" => "Delete client user",
                "description" => "Delete client user",
                "group" => "Client Users",
                "group_order" => 10
            ],
            [
                "name" => "view-brand-contcts",
                "display_name" => "View brand contacts",
                "description" => "View brand contacts",
                "group" => "Brand Contacts",
                "group_order" => 11
            ],
            [
                "name" => "add-new-brand-contact",
                "display_name" => "Add new brand contacts",
                "description" => "Add new brand contacts",
                "group" => "Brand Contacts",
                "group_order" => 11
            ],
            [
                "name" => "edit-brand-contact",
                "display_name" => "Edit brand contacts",
                "description" => "Edit brand contacts",
                "group" => "Brand Contacts",
                "group_order" => 11
            ],
            [
                "name" => "delete-brand-contact",
                "display_name" => "Delete brand contacts",
                "description" => "Delete brand contacts",
                "group" => "Brand Contacts",
                "group_order" => 11
            ],
            [
                "name" => "view-dispositions",
                "display_name" => "View dispositions",
                "description" => "View dispositions",
                "group" => "Dispositions",
                "group_order" => 12
            ],
            [
                "name" => "add-dispositions",
                "display_name" => "Add dispositions",
                "description" => "Add dispositions",
                "group" => "Dispositions",
                "group_order" => 12
            ],
            [
                "name" => "edit-dispositions",
                "display_name" => "Edit dispositions",
                "description" => "Edit dispositions",
                "group" => "Dispositions",
                "group_order" => 12
            ],
            [
                "name" => "delete-dispositions",
                "display_name" => "Delete dispositions",
                "description" => "Delete dispositions",
                "group" => "Dispositions",
                "group_order" => 12
            ],
            [
                "name" => "bulk-upload-dispositions",
                "display_name" => "Bulk upload dispositions",
                "description" => "Bulk upload dispositions",
                "group" => "Dispositions",
                "group_order" => 12
            ],
            [
                "name" => "export-dispositions",
                "display_name" => "Export dispositions",
                "description" => "Export dispositions",
                "group" => "Dispositions",
                "group_order" => 12
            ],
            [
                "name" => "view-alerts",
                "display_name" => "View alerts",
                "description" => "View alerts",
                "group" => "Alerts",
                "group_order" => 13
            ],
            [
                "name" => "edit-alerts",
                "display_name" => "Edit alerts",
                "description" => "Edit alerts",
                "group" => "Alerts",
                "group_order" => 13
            ],
            [
                "name" => "view-do-not-enroll",
                "display_name" => "View do not enroll",
                "description" => "View do not enroll",
                "group" => "Do Not Enroll",
                "group_order" => 14
            ],
            [
                "name" => "add-do-not-enroll",
                "display_name" => "Add do not enroll",
                "description" => "Add do not enroll",
                "group" => "Do Not Enroll",
                "group_order" => 14
            ],
            [
                "name" => "delete-do-not-enroll",
                "display_name" => "Delete do not enroll",
                "description" => "Delete do not enroll",
                "group" => "Do Not Enroll",
                "group_order" => 14
            ],
            [
                "name" => "bulk-upload-do-not-enroll",
                "display_name" => "Bulk upload do not enroll",
                "description" => "Bulk upload do not enroll",
                "group" => "Do Not Enroll",
                "group_order" => 14
            ],
            [
                "name" => "export-do-not-enroll",
                "display_name" => "Export do not enroll",
                "description" => "Export do not enroll",
                "group" => "Do Not Enroll",
                "group_order" => 14
            ],
            [
                "name" => "view-client-settings",
                "display_name" => "View Settings",
                "description" => "Edit Settings",
                "group" => "Settings",
                "group_order" => 15
            ],
            [
                "name" => "edit-client-settings",
                "display_name" => "Edit Settings",
                "description" => "Edit Settings",
                "group" => "Settings",
                "group_order" => 15
            ],            
            [
                "name" => "generate-enrollment-report",
                "display_name" => "Generate enrollment report",
                "description" => "Generate enrollment report",
                "group" => "Analytics",
                "group_order" => 21
            ],
            [
                "name" => "export-enrollment-report",
                "display_name" => "Export enrollment report",
                "description" => "Export enrollment report",
                "group" => "Analytics",
                "group_order" => 21
            ],
            [
                "name" => "filter-enrollment-report",
                "display_name" => "Filter enrollment report",
                "description" => "Filter enrollment report",
                "group" => "Analytics",
                "group_order" => 21
            ],
            [
                "name" => "generate-sales-activity-report",
                "display_name" => "Generate sales activity report",
                "description" => "Generate sales activity report",
                "group" => "Analytics",
                "group_order" => 21
            ],
            [
                "name" => "export-sales-activity-report",
                "display_name" => "Export sales activity report",
                "description" => "Export sales activity report",
                "group" => "Analytics",
                "group_order" => 21
            ],
            [
                "name" => "filter-sales-activity-report",
                "display_name" => "Filter sales activity report",
                "description" => "Filter sales activity report",
                "group" => "Analytics",
                "group_order" => 21
            ],
            [
                "name" => "generate-lead-detail-report",
                "display_name" => "Generate lead detail report",
                "description" => "Generate lead detail report",
                "group" => "Analytics",
                "group_order" => 21
            ],
            [
                "name" => "filter-lead-detail-report",
                "display_name" => "Filter lead detail report",
                "description" => "Filter lead detail report",
                "group" => "Analytics",
                "group_order" => 21
            ],
            [
                "name" => "delete-lead-detail-report",
                "display_name" => "Delete lead detail report",
                "description" => "Delete lead detail report",
                "group" => "Analytics",
                "group_order" => 21
            ],
            [
                "name" => "generate-critical-alert-report",
                "display_name" => "Generate critical alert report",
                "description" => "Generate critical alert report",
                "group" => "Analytics",
                "group_order" => 21
            ],
            [
                "name" => "export-critical-alert-report",
                "display_name" => "Export critical alert report",
                "description" => "Export critical alert report",
                "group" => "Analytics",
                "group_order" => 21
            ],
            [
                "name" => "filter-critical-alert-report",
                "display_name" => "Filter critical alert report",
                "description" => "Filter critical alert report",
                "group" => "Analytics",
                "group_order" => 21
            ],
            [
                "name" => "generate-recordings-report",
                "display_name" => "Generate TPV recordings report",
                "description" => "Generate TPV recordings report",
                "group" => "Analytics",
                "group_order" => 21
            ],
            [
                "name" => "filter-recordings-report",
                "display_name" => "Filter TPV recordings report",
                "description" => "Filter TPV recordings report",
                "group" => "Analytics",
                "group_order" => 21
            ],
            [
                "name" => "generate-sales-agent-trail",
                "display_name" => "Generate sales agent trail",
                "description" => "Generate sales agent trail",
                "group" => "Analytics",
                "group_order" => 21
            ],
            [
                "name" => "filter-sales-agent-trail",
                "display_name" => "Filter sales agent trail",
                "description" => "Filter sales agent trail",
                "group" => "Analytics",
                "group_order" => 21
            ],
            [
                "name" => "generate-billing-report",
                "display_name" => "Generate Billing Duration Report",
                "description" => "Generate Billing Duration Report",
                "group" => "Analytics",
                "group_order" => 21
            ],
            [
                "name" => "generate-call-detail-report",
                "display_name" => "Generate Call Details Report",
                "description" => "Generate Call Details Report",
                "group" => "Analytics",
                "group_order" => 21
            ],
            [
                "name" => "view-user-roles",
                "display_name" => "View user roles",
                "description" => "View user roles",
                "group" => "User Roles",
                "group_order" => 22
            ],
            [
                "name" => "edit-permission-roles",
                "display_name" => "Edit Permission Roles",
                "description" => "Edit Permission Roles",
                "group" => "User Roles",
                "group_order" => 22
            ],
            [
                "name" => "all-users",
                "display_name" => "All users",
                "description" => "All users",
                "group" => "TPV Users",
                "group_order" => 23
            ],
            [
                "name" => "view-tpv-users",
                "display_name" => "View TPV users",
                "description" => "View TPV users",
                "group" => "TPV Users",
                "group_order" => 23
            ],
            [
                "name" => "edit-tpv-users",
                "display_name" => "Edit TPV users",
                "description" => "Edit TPV users",
                "group" => "TPV Users",
                "group_order" => 23
            ],
            [
                "name" => "add-tpv-users",
                "display_name" => "Add TPV users",
                "description" => "Add TPV users",
                "group" => "TPV Users",
                "group_order" => 23
            ],
            [
                "name" => "deactivate-global-admin",
                "display_name" => "Deactivate Global Admin",
                "description" => "Deactivate Global Admin",
                "group" => "TPV Users",
                "group_order" => 23
            ],
            [
                "name" => "deactivate-tpv-admin",
                "display_name" => "Deactivate TPV Admin",
                "description" => "Deactivate TPV Admin",
                "group" => "TPV Users",
                "group_order" => 23
            ],
            [
                "name" => "delete-tpv-admin",
                "display_name" => "Delete TPV Admin",
                "description" => "Delete TPV Admin",
                "group" => "TPV Users",
                "group_order" => 23
            ],
            [
                "name" => "deactivate-tpv-qa",
                "display_name" => "Deactivate TPV QA",
                "description" => "Deactivate TPV QA",
                "group" => "TPV Users",
                "group_order" => 23
            ],
            [
                "name" => "delete-tpv-qa",
                "display_name" => "Delete TPV QA",
                "description" => "Delete TPV QA",
                "group" => "TPV Users",
                "group_order" => 23
            ], 
            [
                "name" => "view-sales-users",
                "display_name" => "View sales users",
                "description" => "View sales users",
                "group" => "Sales Users",
                "group_order" => 24
            ],
            [
                "name" => "edit-sales-users",
                "display_name" => "Edit sales users",
                "description" => "Edit sales users",
                "group" => "Sales Users",
                "group_order" => 24
            ],
            [
                "name" => "add-sales-users",
                "display_name" => "Add sales users",
                "description" => "Add sales users",
                "group" => "Sales Users",
                "group_order" => 24
            ],
            [
                "name" => "bulk-upload-sales-users",
                "display_name" => "Bulk upload sales users",
                "description" => "Bulk upload sales users",
                "group" => "Sales Users",
                "group_order" => 24
            ],
            [
                "name" => "export-sales-users",
                "display_name" => "Export sales users",
                "description" => "Export sales users",
                "group" => "Sales Users",
                "group_order" => 24
            ],
            [
                "name" => "deactivate-sc-admin",
                "display_name" => "Deactivate SC Admin",
                "description" => "Deactivate SC Admin",
                "group" => "Sales Users",
                "group_order" => 24
            ],
            [
                "name" => "delete-sc-admin",
                "display_name" => "Delete SC Admin",
                "description" => "Delete SC Admin",
                "group" => "Sales Users",
                "group_order" => 24
            ],
            [
                "name" => "deactivate-sc-qa",
                "display_name" => "Deactivate SC QA",
                "description" => "Deactivate SC QA",
                "group" => "Sales Users",
                "group_order" => 24
            ],
            [
                "name" => "delete-sc-qa",
                "display_name" => "Delete SC QA",
                "description" => "Delete SC QA",
                "group" => "Sales Users",
                "group_order" => 24
            ],
            [
                "name" => "deactivate-sc-location-admin",
                "display_name" => "Deactivate SC Location Admin",
                "description" => "Deactivate SC Location Admin",
                "group" => "Sales Users",
                "group_order" => 24
            ],
            [
                "name" => "delete-sc-location-admin",
                "display_name" => "Delete SC Location Admin",
                "description" => "Delete SC Location Admin",
                "group" => "Sales Users",
                "group_order" => 24
            ],
            [
                "name" => "view-all-agents",
                "display_name" => "View all agents",
                "description" => "View all agents",
                "group" => "Sales Agents",
                "group_order" => 25
            ],
            [
                "name" => "view-sales-agents",
                "display_name" => "View sales agents",
                "description" => "View sales agents",
                "group" => "Sales Agents",
                "group_order" => 25
            ],
            [
                "name" => "add-sales-agents",
                "display_name" => "Add sales agents",
                "description" => "Add sales agents",
                "group" => "Sales Agents",
                "group_order" => 25
            ],
            [
                "name" => "edit-sales-agents",
                "display_name" => "Edit sales agents",
                "description" => "Edit sales agents",
                "group" => "Sales Agents",
                "group_order" => 25
            ],            
            [
                "name" => "deactivate-sales-agent",
                "display_name" => "Deactivate Sales Agent",
                "description" => "Deactivate Sales Agent",
                "group" => "Sales Agents",
                "group_order" => 25
            ],
            [
                "name" => "delete-sales-agent",
                "display_name" => "Delete Sales Agent",
                "description" => "Delete Sales Agent",
                "group" => "Sales Agents",
                "group_order" => 25
            ],
            [
                "name" => "view-tpv-agents",
                "display_name" => "View TPV agents",
                "description" => "View TPV agents",
                "group" => "TPV Agents",
                "group_order" => 26
            ],
            [
                "name" => "add-tpv-agents",
                "display_name" => "Add TPV agents",
                "description" => "Add TPV agents",
                "group" => "TPV Agents",
                "group_order" => 26
            ],
            [
                "name" => "edit-tpv-agents",
                "display_name" => "Edit TPV agents",
                "description" => "Edit TPV agents",
                "group" => "TPV Agents",
                "group_order" => 26
            ],          
            [
                "name" => "deactivate-tpv-agent",
                "display_name" => "Deactivate TPV Agent",
                "description" => "Deactivate TPV Agent",
                "group" => "TPV Agents",
                "group_order" => 26
            ],            
            [
                "name" => "delete-tpv-agent",
                "display_name" => "Delete TPV Agent",
                "description" => "Delete TPV Agent",
                "group" => "TPV Agents",
                "group_order" => 26
            ],            
            [
                "name" => "view-customer-type",
                "display_name" => "View customer type",
                "description" => "View customer type",
                "group" => "Customer Type",
                "group_order" => 8
            ],
            [
                "name" => "add-customer-type",
                "display_name" => "Add customer type",
                "description" => "Add customer type",
                "group" => "Customer Type",
                "group_order" => 8
            ],
            [
                "name" => "edit-customer-type",
                "display_name" => "Edit customer type",
                "description" => "Edit customer type",
                "group" => "Customer Type",
                "group_order" => 8
            ],
            [
                "name" => "delete-customer-type",
                "display_name" => "Delete customer type",
                "description" => "Delete customer type",
                "group" => "Customer Type",
                "group_order" => 8
            ],
            [
                "name" => "edit-settings",
                "display_name" => "Config",
                "description" => "Config",
                "group" => "Config",
                "group_order" => 27
            ],
            [
                "name" => "support",
                "display_name" => "Support",
                "description" => "Support",
                "group" => "Support",
                "group_order" => 28
            ],
	        [
		        "name" => "agent-dashboard",
		        "display_name" => "Agent dashboard",
		        "description" => "Agent dashboard",
		        "group" => "Dashboard",
		        "group_order" => 1,
	        ],
            [
                "name" => "update-lead-manually",
                "display_name" => "Update Lead Manually",
                "description" => "Update Lead Manually",
                "group" => "Clients",
                "group_order" => 2
            ],
            
        ];

        $clientsPermissions = config()->get('constants.roles.client_admin');
        $salescenterPermissions = config()->get('constants.roles.sales_center_admin');
        foreach ($permissions as $key => $value) {
            $permission = Permission::updateOrCreate(['name'=>$value['name']],$value);
            $data = [];
            $data[] = [ 'permission_id'=>$permission->id, 'access_level'=>'tpv' ];

            if (in_array($permission->name, $clientsPermissions)) {
                $data[] = ['permission_id' => $permission->id, 'access_level' => 'client'];
            }

            if (in_array($permission->name, $salescenterPermissions)) {
               $data[] = [ 'permission_id'=>$permission->id, 'access_level' => 'salescenter'];
            }
            foreach ($data as $accessLevel) {
                PermissionAccessLevel::updateOrCreate($accessLevel,$accessLevel);
            }

            if(!$permission->wasRecentlyCreated && $permission->wasChanged()){
                info("Permission updated: ".print_r($permission->toArray(),true));
            }
            if($permission->wasRecentlyCreated){
                info("Permission created: ".print_r($permission->toArray(),true));
                $role = Role::where('name','admin')->first();
                if ($role) {
                    $role->attachPermission($permission);
                }
            }
        }
        info("Permission succssfully created.");
    }
}
