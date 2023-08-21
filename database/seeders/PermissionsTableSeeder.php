<?php

namespace Database\Seeders;

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
        DB::table('permissions')->insert([
            [
                'name' => 'users',
                'module' => 'Users',
                'display_name' => 'Access Users',
                'guard_name' => 'web',
            ],
            [
                'name' => 'users.index',
                'module' => 'Users',
                'display_name' => 'View Users',
                'guard_name' => 'web',
            ],
            [
                'name' => 'users.create',
                'module' => 'Users',
                'display_name' => 'Create Users',
                'guard_name' => 'web',
            ],
            [
                'name' => 'users.update',
                'module' => 'Users',
                'display_name' => 'Update Users',
                'guard_name' => 'web',
            ],
            [
                'name' => 'users.destroy',
                'module' => 'Users',
                'display_name' => 'Delete Users',
                'guard_name' => 'web',
            ],
            [
                'name' => 'users.roles',
                'module' => 'Users',
                'display_name' => 'Access Roles',
                'guard_name' => 'web',
            ],
            [
                'name' => 'users.roles.index',
                'module' => 'Users',
                'display_name' => 'View Roles',
                'guard_name' => 'web',
            ],
            [
                'name' => 'users.roles.create',
                'module' => 'Users',
                'display_name' => 'Create Roles',
                'guard_name' => 'web',
            ],
            [
                'name' => 'users.roles.update',
                'module' => 'Users',
                'display_name' => 'Update Roles',
                'guard_name' => 'web',
            ],
            [
                'name' => 'users.roles.destroy',
                'module' => 'Users',
                'display_name' => 'Delete Roles',
                'guard_name' => 'web',
            ],
            [
                'name' => 'clients',
                'module' => 'Clients',
                'display_name' => 'Access Clients',
                'guard_name' => 'web',
            ],
            [
                'name' => 'clients.index',
                'module' => 'Clients',
                'display_name' => 'View Clients',
                'guard_name' => 'web',
            ],
            [
                'name' => 'clients.create',
                'module' => 'Clients',
                'display_name' => 'Create Clients',
                'guard_name' => 'web',
            ],
            [
                'name' => 'clients.update',
                'module' => 'Clients',
                'display_name' => 'Update Clients',
                'guard_name' => 'web',
            ],
            [
                'name' => 'clients.destroy',
                'module' => 'Clients',
                'display_name' => 'Delete Clients',
                'guard_name' => 'web',
            ],
            [
                'name' => 'clients.files.index',
                'module' => 'Clients',
                'display_name' => 'View Client Files',
                'guard_name' => 'web',
            ],
            [
                'name' => 'clients.files.create',
                'module' => 'Clients',
                'display_name' => 'Create Client Files',
                'guard_name' => 'web',
            ],
            [
                'name' => 'clients.files.update',
                'module' => 'Clients',
                'display_name' => 'Update Client Files',
                'guard_name' => 'web',
            ],
            [
                'name' => 'clients.files.destroy',
                'module' => 'Clients',
                'display_name' => 'Delete Client Files',
                'guard_name' => 'web',
            ],
            [
                'name' => 'branches',
                'module' => 'Branches',
                'display_name' => 'Access Branches',
                'guard_name' => 'web',
            ],
            [
                'name' => 'branches.index',
                'module' => 'Branches',
                'display_name' => 'View Branches',
                'guard_name' => 'web',
            ],
            [
                'name' => 'branches.create',
                'module' => 'Branches',
                'display_name' => 'Create Branches',
                'guard_name' => 'web',
            ],
            [
                'name' => 'branches.update',
                'module' => 'Branches',
                'display_name' => 'Update Branches',
                'guard_name' => 'web',
            ],
            [
                'name' => 'branches.destroy',
                'module' => 'Branches',
                'display_name' => 'Delete Branches',
                'guard_name' => 'web',
            ],
            [
                'name' => 'locations',
                'module' => 'Locations',
                'display_name' => 'Access Locations',
                'guard_name' => 'web',
            ],
            [
                'name' => 'locations.index',
                'module' => 'Locations',
                'display_name' => 'View Locations',
                'guard_name' => 'web',
            ],
            [
                'name' => 'locations.create',
                'module' => 'Locations',
                'display_name' => 'Create Locations',
                'guard_name' => 'web',
            ],
            [
                'name' => 'locations.update',
                'module' => 'Accounting',
                'display_name' => 'Update Locations',
                'guard_name' => 'web',
            ],
            [
                'name' => 'locations.destroy',
                'module' => 'Locations',
                'display_name' => 'Delete Locations',
                'guard_name' => 'web',
            ],

            [
                'name' => 'loans',
                'module' => 'Loans',
                'display_name' => 'Access Loans Module',
                'guard_name' => 'web',
            ],
            [
                'name' => 'loans.applications.index',
                'module' => 'Loans',
                'display_name' => 'View Loan Applications',
                'guard_name' => 'web',
            ], [
                'name' => 'loans.applications.view_assigned_loan_applications_only',
                'module' => 'Loans',
                'display_name' => 'View Assigned Loan Applications Only',
                'guard_name' => 'web',
            ],
            [
                'name' => 'loans.applications.create',
                'module' => 'Loans',
                'display_name' => 'Create Loan Applications',
                'guard_name' => 'web',
            ],
            [
                'name' => 'loans.applications.update',
                'module' => 'Loans',
                'display_name' => 'Update Loan Applications',
                'guard_name' => 'web',
            ],
            [
                'name' => 'loans.applications.approve',
                'module' => 'Loans',
                'display_name' => 'Approve Loan Applications',
                'guard_name' => 'web',
            ],
            [
                'name' => 'loans.applications.destroy',
                'module' => 'Loans',
                'display_name' => 'Delete Loan Applications',
                'guard_name' => 'web',
            ],
            [
                'name' => 'loans.products.index',
                'module' => 'Loans',
                'display_name' => 'View Loan Products',
                'guard_name' => 'web',
            ],
            [
                'name' => 'loans.products.create',
                'module' => 'Loans',
                'display_name' => 'Create Loan Products',
                'guard_name' => 'web',
            ],
            [
                'name' => 'loans.products.update',
                'module' => 'Loans',
                'display_name' => 'Update Loan Products',
                'guard_name' => 'web',
            ],
            [
                'name' => 'loans.products.destroy',
                'module' => 'Loans',
                'display_name' => 'Delete Loan Products',
                'guard_name' => 'web',
            ],
            [
                'name' => 'loans.scoring_attributes.index',
                'module' => 'Loans',
                'display_name' => 'View Scoring Attributes',
                'guard_name' => 'web',
            ],
            [
                'name' => 'loans.scoring_attributes.create',
                'module' => 'Loans',
                'display_name' => 'Create Scoring Attributes',
                'guard_name' => 'web',
            ],
            [
                'name' => 'loans.scoring_attributes.update',
                'module' => 'Loans',
                'display_name' => 'Update Scoring Attributes',
                'guard_name' => 'web',
            ],
            [
                'name' => 'loans.scoring_attributes.destroy',
                'module' => 'Loans',
                'display_name' => 'Delete Scoring Attributes',
                'guard_name' => 'web',
            ],
            [
                'name' => 'communication',
                'module' => 'Communication',
                'display_name' => 'Access communication Module',
                'guard_name' => 'web',
            ],
            [
                'name' => 'communication.sms_gateways.index',
                'module' => 'Communication',
                'display_name' => 'View SMS Gateways',
                'guard_name' => 'web',
            ],
            [
                'name' => 'communication.sms_gateways.create',
                'module' => 'Communication',
                'display_name' => 'Create SMS Gateways',
                'guard_name' => 'web',
            ],
            [
                'name' => 'communication.sms_gateways.update',
                'module' => 'Communication',
                'display_name' => 'Update SMS Gateways',
                'guard_name' => 'web'
            ],
            [
                'name' => 'communication.sms_gateways.destroy',
                'module' => 'Communication',
                'display_name' => 'Delete SMS Gateways',
                'guard_name' => 'web'
            ],
            [
                'name' => 'communication.campaigns.index',
                'module' => 'Communication',
                'display_name' => 'View Campaigns',
                'guard_name' => 'web'
            ],
            [
                'name' => 'communication.campaigns.create',
                'module' => 'Communication',
                'display_name' => 'Create Campaigns',
                'guard_name' => 'web'
            ],
            [
                'name' => 'communication.campaigns.update',
                'module' => 'Communication',
                'display_name' => 'Update Campaigns',
                'guard_name' => 'web'
            ],
            [
                'name' => 'communication.campaigns.destroy',
                'module' => 'Communication',
                'display_name' => 'Delete Campaigns',
                'guard_name' => 'web'
            ],
            [
                'name' => 'communication.templates.index',
                'module' => 'Communication',
                'display_name' => 'View Templates',
                'guard_name' => 'web'
            ],
            [
                'name' => 'communication.templates.create',
                'module' => 'Communication',
                'display_name' => 'Create Templates',
                'guard_name' => 'web'
            ],
            [
                'name' => 'communication.templates.update',
                'module' => 'Communication',
                'display_name' => 'Update Templates',
                'guard_name' => 'web'
            ],
            [
                'name' => 'communication.templates.destroy',
                'module' => 'Communication',
                'display_name' => 'Delete Templates',
                'guard_name' => 'web'
            ],
            //reports
            [
                'name' => 'reports',
                'module' => 'Reports',
                'display_name' => 'Access Reports',
                'guard_name' => 'web',
            ],
            //audit logs
            [
                'name' => 'activity_logs',
                'module' => 'Activity Logs',
                'display_name' => 'View Activity Logs',
                'guard_name' => 'web',
            ],
            [
                'name' => 'settings',
                'module' => 'Settings',
                'display_name' => 'Access Settings',
                'guard_name' => 'web',
            ],
            [
                'name' => 'settings.configuration',
                'module' => 'Settings',
                'display_name' => 'Access Configuration Menu',
                'guard_name' => 'web',
            ],
            [
                'name' => 'settings.update',
                'module' => 'Settings',
                'display_name' => 'Update Settings',
                'guard_name' => 'web',
            ],
            [
                'name' => 'dashboard',
                'module' => 'Dashboard',
                'display_name' => 'Access Dashboard',
                'guard_name' => 'web',
            ],

        ]);
    }
}
