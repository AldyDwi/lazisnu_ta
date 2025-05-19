<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
// Admin
// $routes->group('', ['namespace' => 'App\Controllers\Admin'], function ($routes) {
// });

// Auth Routes
$routes->group('', ['namespace' => 'App\Controllers\Admin'], function ($routes) {
    // Web Auth Pages
    $routes->get('login', 'Auth::index');
    $routes->get('register', 'Auth::register_page');
    $routes->get('forgot-password', 'Auth::forgot_password_page');
    $routes->get('validate-otp', 'Auth::validate_otp_page');
    $routes->get('reset-password', 'Auth::reset_password_page');

    // Web Auth Actions
    $routes->post('do-login', 'Auth::login');
    $routes->post('do-register', 'Auth::register');
    $routes->post('logout', 'Auth::logout');
    $routes->post('do-forgot-password', 'Auth::forgotPassword');
    $routes->post('do-validate-otp', 'Auth::validateOtp');
    $routes->post('do-reset-password', 'Auth::resetPassword');
    $routes->post('change-password', 'Auth::changePassword');
});

// API Auth Routes
$routes->group('api/auth', ['namespace' => 'App\Controllers\Admin'], function ($routes) {
    $routes->post('refresh', 'Auth::refresh');
});

// Login
$routes->group('login', ['namespace' => 'App\Controllers\Admin'], function ($routes) {
    $routes->get('/', 'Auth::index');
});

// ****************************************************************************************************
// Landing Page
// ****************************************************************************************************

$routes->group('', ['namespace' => 'App\Controllers\LandingPage'], function ($routes) {
    $routes->get('/', 'Home::index');
    $routes->get('get_data', 'Home::get_data');
    $routes->get('detail_list', 'Home::detail_list');
});

$routes->group('', ['namespace' => 'App\Controllers\LandingPage'], function ($routes) {
    $routes->get('get_detail', 'Detail::get_detail'); 
    $routes->get('detail/(:segment)', 'Detail::index/$1');
});

// ****************************************************************************************************
// Role Admin
// ****************************************************************************************************

// Profile Admin
$routes->group('profile', ['namespace' => 'App\Controllers\Admin', 'filter' => 'admin'], function ($routes) {
    $routes->get('/', 'Profile::index');
    $routes->get('list_data', 'Profile::list_data');
    $routes->post('update', 'Profile::update');
});

// Dashboard
$routes->group('dashboard', ['namespace' => 'App\Controllers\Admin', 'filter' => 'admin'], function ($routes) {
    $routes->get('/', 'Dashboard::index');
    $routes->get('list_data', 'Dashboard::list_data');
    $routes->get('get_year', 'Dashboard::get_year');
    $routes->get('get_chart_data/(:num)', 'Dashboard::get_chart_data/$1');
    $routes->get('get_latest_transactions', 'Dashboard::get_latest_transactions');
});

// Officers
$routes->group('officers', ['namespace' => 'App\Controllers\Admin', 'filter' => 'admin'], function ($routes) {
    $routes->get('/', 'Officers::index');
    $routes->get('list_data', 'Officers::list_data');
    $routes->get('dropdown', 'Officers::dropdown');
    $routes->post('save', 'Officers::save');
    $routes->post('update', 'Officers::update');
    $routes->post('delete', 'Officers::delete');
});

// Citizen
$routes->group('citizen', ['namespace' => 'App\Controllers\Admin', 'filter' => 'admin'], function ($routes) {
    $routes->get('/', 'Citizen::index');
    $routes->get('list_data', 'Citizen::list_data');
    $routes->get('list_data_not_rw', 'Citizen::list_data_not_rw');
    $routes->get('dropdown', 'Citizen::dropdown');
    $routes->post('save', 'Citizen::save');
    $routes->post('update', 'Citizen::update');
    $routes->post('delete', 'Citizen::delete');
});

// Programs
$routes->group('programs', ['namespace' => 'App\Controllers\Admin', 'filter' => 'admin'], function ($routes) {
    $routes->get('/', 'Programs::index');
    $routes->get('list_data', 'Programs::list_data');
    $routes->post('save', 'Programs::save');
    $routes->post('update', 'Programs::update');
    $routes->post('delete', 'Programs::delete');
});

// Program Allocation
$routes->group('program-allocation', ['namespace' => 'App\Controllers\Admin', 'filter' => 'admin'], function ($routes) {
    $routes->get('/', 'Program_Allocation::index');
    $routes->get('list_data', 'Program_Allocation::list_data');
    $routes->post('save', 'Program_Allocation::save');
    $routes->post('update', 'Program_Allocation::update');
    $routes->post('delete', 'Program_Allocation::delete');
});

// Beneficaries
$routes->group('beneficaries', ['namespace' => 'App\Controllers\Admin', 'filter' => 'admin'], function ($routes) {
    $routes->get('/', 'Beneficaries::index');
    $routes->get('list_data', 'Beneficaries::list_data');
    $routes->get('dropdown', 'Beneficaries::dropdown');
    $routes->post('save', 'Beneficaries::save');
    $routes->post('update', 'Beneficaries::update');
    $routes->post('delete', 'Beneficaries::delete');
});

// File Fund
$routes->group('file-fund', ['namespace' => 'App\Controllers\Admin', 'filter' => 'admin'], function ($routes) {
    $routes->get('/', 'FileFund::index');
    $routes->get('list_data', 'FileFund::list_data');
    $routes->post('save', 'FileFund::save');
    $routes->post('update', 'FileFund::update');
    $routes->post('delete', 'FileFund::delete');
});

// Donations
$routes->group('donations', ['namespace' => 'App\Controllers\Admin', 'filter' => 'admin'], function ($routes) {
    $routes->get('/', 'Donations::index');
    $routes->get('list_data', 'Donations::list_data');
    $routes->post('update', 'Donations::update');
    $routes->get('citizen_dropdown', 'Donations::citizen_dropdown');
});

// Officers_Commision
$routes->group('officers-commision', ['namespace' => 'App\Controllers\Admin', 'filter' => 'admin'], function ($routes) {
    $routes->get('/', 'Officers_Commision::index');
    $routes->get('list_data', 'Officers_Commision::list_data');
    $routes->post('save', 'Officers_Commision::save');
});

// Fund_Distribution
$routes->group('fund-distribution', ['namespace' => 'App\Controllers\Admin', 'filter' => 'admin'], function ($routes) {
    $routes->get('/', 'Fund_Distribution::index');
    $routes->get('list_data', 'Fund_Distribution::list_data');
    $routes->get('dropdown', 'Fund_Distribution::dropdown');
    $routes->get('get-income', 'Fund_Distribution::get_income');
    $routes->get('get-detail', 'Fund_Distribution::get_detail');
    $routes->post('save', 'Fund_Distribution::save');
    $routes->post('update', 'Fund_Distribution::update');
    $routes->post('delete', 'Fund_Distribution::delete');
});

// Transaction
$routes->group('transaction', ['namespace' => 'App\Controllers\Admin', 'filter' => 'admin'], function ($routes) {
    $routes->get('/', 'Transaction::index');
    $routes->get('list_data', 'Transaction::list_data');
    $routes->get('get_income', 'Transaction::get_income');
    $routes->post('save', 'Transaction::save');
    $routes->post('update', 'Transaction::update');
    $routes->post('get-by-rw','Transaction::get_by_rw');
    $routes->get('get-by-rw','Transaction::get_by_rw');
    $routes->get('get-no-rw','Transaction::get_no_rw');
    $routes->post('update_status', 'Transaction::update_status');
});

// Export Transaction
$routes->group('export_transaction', ['namespace' => 'App\Controllers\Admin', 'filter' => 'admin'], function ($routes) {
    $routes->get('excel', 'Export_Transaction::excel');
    $routes->post('pdf', 'Export_Transaction::pdf');
});

// Export Commision
$routes->group('export_commision', ['namespace' => 'App\Controllers\Admin', 'filter' => 'admin'], function ($routes) {
    $routes->post('excel', 'Export_Commision::excel');
    $routes->post('pdf', 'Export_Commision::pdf');
});

// ****************************************************************************************************
// Role Super Admin
// ****************************************************************************************************

// Dashboard
$routes->group('super-admin/dashboard', ['namespace' => 'App\Controllers\SuperAdmin', 'filter' => 'superadmin'], function ($routes) {
    $routes->get('/', 'Dashboard::index');
    $routes->get('list_data', 'Dashboard::list_data');
    $routes->get('get_year', 'Dashboard::get_year');
    $routes->get('get_branch', 'Dashboard::get_branch');
    $routes->get('get_chart_data/(:num)', 'Dashboard::get_chart_data/$1');
    $routes->get('get_latest_transactions', 'Dashboard::get_latest_transactions');
});

// Profile Super Admin
$routes->group('super-admin/profile', ['namespace' => 'App\Controllers\SuperAdmin', 'filter' => 'superadmin'], function ($routes) {
    $routes->get('/', 'Profile::index');
    $routes->get('list_data', 'Profile::list_data');
    $routes->post('update', 'Profile::update');
});

// Branches
$routes->group('super-admin/branches', ['namespace' => 'App\Controllers\SuperAdmin', 'filter' => 'superadmin'], function ($routes) {
    $routes->get('/', 'Branches::index');
    $routes->get('list_data', 'Branches::list_data');
    $routes->post('save', 'Branches::save');
    $routes->post('update', 'Branches::update');
    $routes->post('delete', 'Branches::delete');
});

// Cities
$routes->group('super-admin/cities', ['namespace' => 'App\Controllers\SuperAdmin', 'filter' => 'superadmin'], function ($routes) {
    $routes->get('/', 'Cities::index');
    $routes->get('list_data', 'Cities::list_data');
    $routes->post('save', 'Cities::save');
    $routes->post('update', 'Cities::update');
    $routes->post('delete', 'Cities::delete');
});

// District
$routes->group('super-admin/district', ['namespace' => 'App\Controllers\SuperAdmin', 'filter' => 'superadmin'], function ($routes) {
    $routes->get('/', 'District::index');
    $routes->get('list_data', 'District::list_data');
    $routes->post('save', 'District::save');
    $routes->post('update', 'District::update');
    $routes->post('delete', 'District::delete');
});

// Provience
$routes->group('super-admin/provience', ['namespace' => 'App\Controllers\SuperAdmin', 'filter' => 'superadmin'], function ($routes) {
    $routes->get('/', 'Provience::index');
    $routes->get('list_data', 'Provience::list_data');
    $routes->get('get_data', 'Provience::get_data');
    $routes->post('save', 'Provience::save');
    $routes->post('update', 'Provience::update');
    $routes->post('delete', 'Provience::delete');
});

// Region
$routes->group('super-admin/region', ['namespace' => 'App\Controllers\SuperAdmin', 'filter' => 'superadmin'], function ($routes) {
    $routes->get('/', 'Region::index');
    $routes->get('list_data', 'Region::list_data');
    $routes->post('save', 'Region::save');
    $routes->post('update', 'Region::update');
    $routes->post('delete', 'Region::delete');
});

// Rws
$routes->group('super-admin/rws', ['namespace' => 'App\Controllers\SuperAdmin', 'filter' => 'superadmin'], function ($routes) {
    $routes->get('/', 'Rws::index');
    $routes->get('list_data', 'Rws::list_data');
    // $routes->get('get_data', 'Rws::get_data');
    $routes->get('dropdown', 'Rws::dropdown');
    $routes->get('dropdown-admin', 'Rws::dropdown_admin');
    $routes->post('save', 'Rws::save');
    $routes->post('update', 'Rws::update');
    $routes->post('delete', 'Rws::delete');
});

// Admin
$routes->group('super-admin/admin', ['namespace' => 'App\Controllers\SuperAdmin', 'filter' => 'superadmin'], function ($routes) {
    $routes->get('/', 'Admin::index');
    $routes->get('list_data', 'Admin::list_data');
    $routes->post('save', 'Admin::save');
    $routes->post('update', 'Admin::update');
    $routes->post('delete', 'Admin::delete');
});

// Beneficaries_Type
$routes->group('super-admin/beneficaries-type', ['namespace' => 'App\Controllers\SuperAdmin', 'filter' => 'superadmin'], function ($routes) {
    $routes->get('/', 'Beneficaries_Type::index');
    $routes->get('list_data', 'Beneficaries_Type::list_data');
    $routes->get('get_data', 'Beneficaries_Type::get_data');
    $routes->post('save', 'Beneficaries_Type::save');
    $routes->post('update', 'Beneficaries_Type::update');
    $routes->post('delete', 'Beneficaries_Type::delete');
});

// Officers
$routes->group('super-admin/officers', ['namespace' => 'App\Controllers\SuperAdmin', 'filter' => 'superadmin'], function ($routes) {
    $routes->get('/', 'Officers::index');
    $routes->get('list_data', 'Officers::list_data');
    $routes->post('save', 'Officers::save');
    $routes->post('update', 'Officers::update');
    $routes->post('delete', 'Officers::delete');
});

// Citizen
$routes->group('super-admin/citizen', ['namespace' => 'App\Controllers\SuperAdmin', 'filter' => 'superadmin'], function ($routes) {
    $routes->get('/', 'Citizen::index');
    $routes->get('list_data', 'Citizen::list_data');
    $routes->get('list_data_not_rw', 'Citizen::list_data_not_rw');
    $routes->get('dropdown', 'Citizen::dropdown');
    $routes->post('save', 'Citizen::save');
    $routes->post('update', 'Citizen::update');
    $routes->post('delete', 'Citizen::delete');
});

// Programs
$routes->group('super-admin/programs', ['namespace' => 'App\Controllers\SuperAdmin', 'filter' => 'superadmin'], function ($routes) {
    $routes->get('/', 'Programs::index');
    $routes->get('list_data', 'Programs::list_data');
    $routes->post('save', 'Programs::save');
    $routes->post('update', 'Programs::update');
    $routes->post('delete', 'Programs::delete');
});

// Beneficaries
$routes->group('super-admin/beneficaries', ['namespace' => 'App\Controllers\SuperAdmin', 'filter' => 'superadmin'], function ($routes) {
    $routes->get('/', 'Beneficaries::index');
    $routes->get('list_data', 'Beneficaries::list_data');
    $routes->post('save', 'Beneficaries::save');
    $routes->post('update', 'Beneficaries::update');
    $routes->post('delete', 'Beneficaries::delete');
});

// Donations
$routes->group('super-admin/donations', ['namespace' => 'App\Controllers\SuperAdmin', 'filter' => 'superadmin'], function ($routes) {
    $routes->get('/', 'Donations::index');
    $routes->get('list_data', 'Donations::list_data');
    $routes->post('update', 'Donations::update');
    $routes->get('citizen_dropdown', 'Donations::citizen_dropdown');
});

// Officers_Commision
$routes->group('super-admin/officers-commision', ['namespace' => 'App\Controllers\SuperAdmin', 'filter' => 'superadmin'], function ($routes) {
    $routes->get('/', 'Officers_Commision::index');
    $routes->get('list_data', 'Officers_Commision::list_data');
    $routes->post('save', 'Officers_Commision::save');
});

// Fund_Distribution
$routes->group('super-admin/fund-distribution', ['namespace' => 'App\Controllers\SuperAdmin', 'filter' => 'superadmin'], function ($routes) {
    $routes->get('/', 'Fund_Distribution::index');
    $routes->get('list_data', 'Fund_Distribution::list_data');
    $routes->get('dropdown', 'Fund_Distribution::dropdown');
    $routes->get('get-income', 'Fund_Distribution::get_income');
    $routes->get('get-detail', 'Fund_Distribution::get_detail');
    $routes->post('save', 'Fund_Distribution::save');
    $routes->post('update', 'Fund_Distribution::update');
    $routes->post('delete', 'Fund_Distribution::delete');
});

// Transaction
$routes->group('super-admin/transaction', ['namespace' => 'App\Controllers\SuperAdmin', 'filter' => 'superadmin'], function ($routes) {
    $routes->get('/', 'Transaction::index');
    $routes->get('list_data', 'Transaction::list_data');
    $routes->get('get_income', 'Transaction::get_income');
    $routes->get('get_data', 'Transaction::get_data');
    $routes->post('save', 'Transaction::save');
    $routes->post('update', 'Transaction::update');
    $routes->post('get-by-rw','Transaction::get_by_rw');
    $routes->get('get-by-rw','Transaction::get_by_rw');
    $routes->post('update_status', 'Transaction::update_status');
});

// Export Transaction
$routes->group('super-admin/export_transaction', ['namespace' => 'App\Controllers\SuperAdmin', 'filter' => 'superadmin'], function ($routes) {
    $routes->get('excel', 'Export_Transaction::excel');
    $routes->post('pdf', 'Export_Transaction::pdf');
});

// Export Commision
$routes->group('super-admin/export_commision', ['namespace' => 'App\Controllers\SuperAdmin', 'filter' => 'superadmin'], function ($routes) {
    $routes->post('excel', 'Export_Commision::excel');
    $routes->post('pdf', 'Export_Commision::pdf');
});

// Program Page
$routes->group('super-admin/program-page', ['namespace' => 'App\Controllers\SuperAdmin', 'filter' => 'superadmin'], function ($routes) {
    $routes->get('/', 'Program_Page::index');
    $routes->get('list_data', 'Program_Page::list_data');
    $routes->post('save', 'Program_Page::save');
    $routes->post('update', 'Program_Page::update');
    $routes->post('delete', 'Program_Page::delete');
});

// Distribution Page
$routes->group('super-admin/distribution-page', ['namespace' => 'App\Controllers\SuperAdmin', 'filter' => 'superadmin'], function ($routes) {
    $routes->get('/', 'Distribution_Page::index');
    $routes->get('list_data', 'Distribution_Page::list_data');
    $routes->get('get_detail', 'Distribution_Page::get_detail');
    $routes->post('save', 'Distribution_Page::save');
    $routes->post('update', 'Distribution_Page::update');
    $routes->post('delete', 'Distribution_Page::delete');
});

// ****************************************************************************************************
// Role Petugas/Officer
// ****************************************************************************************************

// Profile Officer
$routes->group('officer/profile', ['namespace' => 'App\Controllers\Officer', 'filter' => 'officer'], function ($routes) {
    $routes->get('/', 'Profile::index');
    $routes->get('list_data', 'Profile::list_data');
    $routes->post('update', 'Profile::update');
});

// Dashboard
$routes->group('officer/dashboard', ['namespace' => 'App\Controllers\Officer', 'filter' => 'officer'], function ($routes) {
    $routes->get('/', 'Dashboard::index');
    $routes->get('list_data', 'Dashboard::list_data');
    $routes->get('get_year', 'Dashboard::get_year');
    $routes->get('get_chart_data/(:num)', 'Dashboard::get_chart_data/$1');
    $routes->get('get_latest_transactions', 'Dashboard::get_latest_transactions');
});

// Citizen
$routes->group('officer/citizen', ['namespace' => 'App\Controllers\Officer', 'filter' => 'officer'], function ($routes) {
    $routes->get('/', 'Citizen::index');
    $routes->get('list_data', 'Citizen::list_data');
    $routes->get('list_data_not_rw', 'Citizen::list_data_not_rw');
    $routes->post('save', 'Citizen::save');
    $routes->post('update', 'Citizen::update');
    $routes->post('delete', 'Citizen::delete');
});

// Donations
$routes->group('officer/donations', ['namespace' => 'App\Controllers\Officer', 'filter' => 'officer'], function ($routes) {
    $routes->get('/', 'Donations::index');
    $routes->get('list_data', 'Donations::list_data');
    $routes->get('btn_create', 'Donations::btn_create');
    $routes->get('dropdown', 'Donations::dropdown');
    $routes->post('save', 'Donations::save');
    $routes->post('update', 'Donations::update');
    $routes->get('citizen_dropdown', 'Donations::citizen_dropdown');
});

// Officers_Commision
$routes->group('officer/officers-commision', ['namespace' => 'App\Controllers\Officer', 'filter' => 'officer'], function ($routes) {
    $routes->post('get_income', 'Officers_Commision::get_income');
    $routes->post('list_commision', 'Officers_Commision::list_commision');
});

// Total
$routes->group('officer/total', ['namespace' => 'App\Controllers\Officer', 'filter' => 'officer'], function ($routes) {
    $routes->post('citizen_total', 'Total::citizen_total');
    $routes->post('beneficaries_total', 'Total::beneficaries_total');
});

// Transaction
$routes->group('officer/transaction', ['namespace' => 'App\Controllers\Officer', 'filter' => 'officer'], function ($routes) {
    $routes->get('/', 'Transaction::index');
    $routes->get('list_data', 'Transaction::list_data');
    $routes->get('get_income', 'Transaction::get_income');
    $routes->get('get_data', 'Transaction::get_data');
    $routes->post('report-infaq-per-rw', 'Transaction::reportInfaqPerRW');
    $routes->post('get-by-rw','Transaction::get_by_rw');
    $routes->get('get-by-rw','Transaction::get_by_rw');
    $routes->get('get-no-rw','Transaction::get_no_rw');
});


// Assets
$routes->add('assets/modules/(:any)', '');
