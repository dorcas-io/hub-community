<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use GuzzleHttp\Psr7\Uri;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

$request = app()->make('request');
$currentHost = $request->header('host');
$defaultUri = new Uri(config('app.url'));
try {
    $domainInfo = (new App\Http\Middleware\ResolveCustomSubdomain())->splitHost($currentHost);
} catch (RuntimeException $e) {
    $domainInfo = null;
}
$storeSubDomain = !empty($domainInfo) && $domainInfo->getService() === 'store' ?
    $currentHost : 'store' . $defaultUri->getHost();

Route::prefix('store')->group(function () {
    Route::get('/', 'WebStore\RedirectRoute@index');
    Route::get('/categories/{id?}', 'WebStore\RedirectRoute@index');
    Route::get('/products/{id?}', 'WebStore\RedirectRoute@index');
    Route::get('/cart', 'WebStore\RedirectRoute@index');
});

Route::domain($storeSubDomain)->namespace('WebStore')->middleware(['web_store'])->group(function () {
    Route::get('/', 'Home@index')->name('webstore');
    Route::get('/categories', 'Home@categories')->name('webstore.categories');
    Route::get('/categories/{id}', 'Home@index')->name('webstore.categories.single');
    Route::get('/products', 'Home@products')->name('webstore.products');
    Route::get('/products/{id}', 'Home@productDetails')->name('webstore.products.details');
    Route::get('/cart', 'Cart@index')->name('webstore.cart');
    Route::get('/product-quick-view/{id}', 'Home@quickView')->name('webstore.quick-view');
    Route::delete('/xhr/cart', 'Cart@removeFromCartXhr');
    Route::post('/xhr/cart', 'Cart@addToCartXhr');
    Route::post('/xhr/cart/checkout', 'Cart@checkoutXhr');
    Route::put('/xhr/cart/update-quantities', 'Cart@updateCartQuantitiesXhr');
});

Route::get('/', 'Index@index');

Auth::routes();
Route::get('/logout', 'Auth\LoginController@logout');
Route::get('/forgot-password', 'Auth\ForgotPasswordController@showLinkRequestForm');
Route::post('/forgot-password', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('forgot-password');
Route::get('/reset-password/{token}', 'Auth\ResetPasswordController@showResetForm');
Route::post('/reset-password/{token}', 'Auth\ResetPasswordController@reset')->name('forgot-password-reset');
Route::get('/home', 'HomeController@index')->name('home');
Route::get('/verify-email/{id}', 'Auth\Verify@verifyEmail')->name('verify-email');

Route::get('/sso', 'Auth\Partners\SingleSignOn@sso')->middleware('guest');
Route::get('/sso-silent', 'Auth\Partners\SingleSignOn@ssoSilent')->middleware('guest');

Route::get('/home', 'HomeController@index')->name('home');
Route::post('/home', 'HomeController@post');
Route::get('/invites/{id}', 'Invites@index')->name('invite');
Route::post('/invites/{id}', 'Invites@post');
Route::get('/register/professionals', 'Auth\RegisterController@showProfessionalRegistrationForm')->name('professional.register');
Route::get('/register/vendors', 'Auth\RegisterController@showVendorRegistrationForm')->name('vendor.register');
Route::get('/professionals', 'Auth\RegisterController@showOldProfessionalRegistrationForm');

/**
 * Route Group for XHR: /xhr/...
 */
Route::group(['middleware' => ['auth'], 'namespace' => 'Ajax', 'prefix' => 'xhr'], function () {
    Route::post('/account/resend-verification', 'Account\Account@resendVerification');
    
    Route::get('/app-store', 'AppStore\AppStore@search');
    Route::post('/app-store/{id}', 'AppStore\AppStore@installApp');
    Route::delete('/app-store/{id}', 'AppStore\AppStore@uninstallApp');
    
    Route::get('/access-grants', 'AccessGrantRequests@search')->name('xhr.access-grants');
    Route::delete('/access-grants/{id}', 'AccessGrantRequests@deleteRequest');
    Route::get('/access-grants-for-user', 'AccessGrantRequests@searchByUser')->name('xhr.access-grants.user');
    Route::delete('/access-grants-for-user/{id}', 'AccessGrantRequests@deleteRequestForUser');
    
    Route::post('/billing/verify', 'Billing\Billing@verifyTransaction');

    Route::get('/businesses', 'Business\Businesses@search');
    Route::post('/business/departments', 'Business\Department@create');
    Route::delete('/business/departments/{id}', 'Business\Department@delete');
    Route::put('/business/departments/{id}', 'Business\Department@update');
    Route::delete('/business/departments/{id}/employees', 'Business\Department@removeEmployees');

    Route::post('/business/employees', 'Business\Employee@create');
    Route::delete('/business/employees/{id}', 'Business\Employee@delete');
    Route::put('/business/employees/{id}', 'Business\Employee@update');
    Route::delete('/business/employees/{id}/teams', 'Business\Employee@removeTeams');

    Route::post('/business/teams', 'Business\Team@create');
    Route::delete('/business/teams/{id}', 'Business\Team@delete');
    Route::put('/business/teams/{id}', 'Business\Team@update');
    Route::delete('/business/teams/{id}/employees', 'Business\Team@removeEmployees');

    Route::get('/crm/custom-fields', 'Crm\CustomFields@search');
    Route::post('/crm/custom-fields', 'Crm\CustomFields@create');
    Route::delete('/crm/custom-fields/{id}', 'Crm\CustomField@delete');
    Route::put('/crm/custom-fields/{id}', 'Crm\CustomField@update');
    Route::get('/crm/customers', 'Crm\Customers@search');
    Route::delete('/crm/customers/{id}', 'Crm\Customer@delete');
    Route::put('/crm/customers/{id}', 'Crm\Customer@update');
    Route::delete('/crm/customers/{id}/notes', 'Crm\Customer@deleteNote');
    Route::get('/crm/customers/{id}/notes', 'Crm\Customer@readNotes');
    Route::post('/crm/customers/{id}/notes', 'Crm\Customer@addNote');
    
    Route::get('/crm/customers/{id}/deals', 'Crm\Deals@search');
    Route::post('/crm/customers/{id}/deals', 'Crm\Deals@create');
    
    Route::post('/crm/deals/{id}', 'Crm\Deals@delete');
    
    Route::delete('/crm/groups/{id}', 'Crm\Groups@delete');
    Route::delete('/crm/groups/{id}/customers', 'Crm\Groups@deleteCustomers');
    Route::post('/crm/groups/{id}/customers', 'Crm\Groups@addCustomers');
    
    Route::get('/directory', 'Directory\Directory@search');
    
    Route::get('/directory/contacts', 'Directory\Directory@vendorContacts');
    Route::delete('/directory/contacts/{id}', 'Directory\Directory@removeContact');
    
    Route::post('/directory/credentials', 'Directory\Profile@addCredential');
    Route::delete('/directory/credentials/{id}', 'Directory\Profile@deleteCredential');
    Route::post('/directory/experiences', 'Directory\Profile@addExperience');
    Route::delete('/directory/experiences/{id}', 'Directory\Profile@deleteExperience');
    Route::post('/directory/services', 'Directory\Profile@manageServices');
    Route::delete('/directory/services/{id}', 'Directory\Profile@deleteService');
    Route::delete('/directory/social-connections', 'Directory\Profile@deleteSocialConnection');
    Route::post('/directory/social-connections', 'Directory\Profile@addSocialConnection');
    Route::get('/directory/service-requests', 'Directory\Profile@getServiceRequests');
    Route::put('/directory/service-requests/{id}', 'Directory\Profile@updateServiceRequest');
    
    Route::delete('/ecommerce/adverts/{id}', 'ECommerce\Adverts@delete');
    
    Route::post('/ecommerce/blog/categories', 'ECommerce\Blog@createCategory');
    Route::delete('/ecommerce/blog/categories/{id}', 'ECommerce\Blog@deleteCategory');
    Route::put('/ecommerce/blog/categories/{id}', 'ECommerce\Blog@updateCategory');
    
    Route::post('/ecommerce/blog', 'ECommerce\Blog@searchPosts');
    Route::delete('/ecommerce/blog/{id}', 'ECommerce\Blog@deletePost');
    
    Route::delete('/ecommerce/domains/issuances/{id}', 'ECommerce\Issuances@releaseSubdomain');
    Route::get('/ecommerce/domains/issuances/availability', 'ECommerce\Issuances@checkAvailability');
    
    Route::get('/ecommerce/domains/availability', 'ECommerce\Domains@checkAvailability');
    Route::delete('/ecommerce/domains/{id}', 'ECommerce\Domains@releaseDomain');
    
    Route::delete('/ecommerce/emails/{username}', 'ECommerce\Emails@delete');
    
    Route::post('/finance/install', 'Finance\Accounts@install');
    Route::delete('/finance/accounts/{id}', 'Finance\Accounts@delete');
    Route::put('/finance/accounts/{id}', 'Finance\Accounts@update');
    Route::get('/finance/entries', 'Finance\Entries@search');
    Route::delete('/finance/entries/{id}', 'Finance\Entries@delete');
    Route::put('/finance/entries/{id}', 'Finance\Entries@update');
    Route::post('/finance/reports', 'Finance\Reports@createReport');
    Route::post('/finance/transtrak/fetch', 'Finance\Transtrak@fetch');
    Route::post('/finance/transtrak/login', 'Finance\Transtrak@login');
    Route::post('/finance/transtrak/enable-auto-processing', 'Finance\Transtrak@enableAutoProcessing');

    Route::post('/integrations', 'Integrations\Integrations@install');
    Route::delete('/integrations/{id}', 'Integrations\Integrations@uninstall');
    Route::put('/integrations/{id}', 'Integrations\Integrations@update');
    
    Route::post('/inventory/categories', 'Inventory\Categories@create');
    Route::delete('/inventory/categories/{id}', 'Inventory\Categories@delete');
    Route::put('/inventory/categories/{id}', 'Inventory\Categories@update');

    Route::get('/inventory/products', 'Inventory\Products@search');
    Route::delete('/inventory/products/{id}', 'Inventory\Products@delete');
    
    Route::delete('/inventory/products/{id}/categories', 'Inventory\Products@deleteCategory');
    Route::delete('/inventory/products/{id}/images', 'Inventory\Products@deleteImage');

    Route::get('/inventory/products/{id}/stocks', 'Inventory\Products@stocks');

    Route::get('/inventory/orders', 'Inventory\Orders@search');
    Route::delete('/inventory/orders/{id}', 'Inventory\Orders@delete');
    Route::put('/inventory/orders/{id}', 'Inventory\Orders@update');
    Route::delete('/inventory/orders/{id}/customers', 'Inventory\Orders@deleteCustomer');
    Route::put('/inventory/orders/{id}/customers', 'Inventory\Orders@updateCustomerOrder');

    Route::post('/plans', 'Plans@switch');
    Route::post('/settings', 'Settings@update');
    
    Route::group(['middleware' => ['require_role:partner'], 'prefix' => 'vpanel', 'namespace' => 'vPanel'], function () {
        Route::get('/companies', 'Businesses@search')->name('xhr.vpanel.companies');
        Route::delete('/companies/{id}', 'Businesses@delete');
        Route::get('/invites', 'Businesses@searchInvites')->name('xhr.vpanel.invites');
        Route::delete('/invites/{id}', 'Businesses@deleteInvite');
        Route::get('/users', 'Users@search')->name('xhr.vpanel.users');
        Route::delete('/users/{id}', 'Users@delete');
    });
});

Route::group(['middleware' => ['auth']], function () {
    Route::get('/plans', 'UpgradePlan@index')->name('plans');
    Route::get('/subscription', 'Subscription@index')->name('subscription');
    Route::post('/subscription', 'Subscription@post');
});

Route::group(['middleware' => ['auth'], 'namespace' => 'Businesses', 'prefix' => 'apps/people'], function () {
    Route::get('/', 'Business@index')->name('business');

    Route::get('/departments', 'Departments\Departments@index')->name('business.departments');
    Route::get('/departments/new', 'Departments\Departments@index')->name('business.departments.new');
    Route::get('/departments/{id}', 'Departments\Department@index')->name('business.departments.single');
    Route::post('/departments/{id}', 'Departments\Department@post');

    Route::get('/employees', 'Employees\Employees@index')->name('business.employees');
    Route::get('/employees/new', 'Employees\NewEmployee@index')->name('business.employees.new');
    Route::post('/employees/new', 'Employees\NewEmployee@create');
    Route::get('/employees/{id}', 'Employees\Employee@index')->name('business.employees.single');
    Route::post('/employees/{id}', 'Employees\Employee@post');

    Route::get('/teams', 'Teams\Teams@index')->name('business.teams');
    Route::get('/teams/{id}', 'Teams\Team@index')->name('business.teams.single');
    Route::post('/teams/{id}', 'Teams\Team@post');
});

Route::group(['middleware' => ['auth'], 'prefix' => 'app-store', 'namespace' => 'AppStore'], function () {
    Route::get('/', 'Listing@index')->name('app-store');
    Route::get('/installed', 'Installed@index')->name('app-store.installed');
});



Route::group(['middleware' => ['auth'], 'prefix' => 'apps'], function () {
    Route::get('/crm', 'Crm\Crm@index')->name('apps.crm');
});

Route::group(['middleware' => ['auth'], 'namespace' => 'Crm', 'prefix' => 'apps/crm'], function () {
    Route::get('/custom-fields', 'ContactFields\CustomField@index')->name('apps.crm.custom-fields');
    Route::delete('/customers', 'Customers\Customers@delete');
    Route::get('/customers', 'Customers\Customers@index')->name('apps.crm.customers');
    Route::post('/customers', 'Customers\Customers@create');
    Route::get('/customers/new', 'Customers\NewCustomer@index')->name('apps.crm.customers.new');
    Route::post('/customers/new', 'Customers\NewCustomer@create');
    Route::get('/customers/{id}', 'Customers\Customer@index')->name('app.crm.customers.single');
    Route::post('/customers/{id}', 'Customers\Customer@post');
    Route::get('/groups', 'Groups@index')->name('apps.crm.groups');
    Route::post('/groups', 'Groups@post');
});

Route::group(['middleware' => ['auth'], 'namespace' => 'ECommerce', 'prefix' => 'apps/ecommerce'], function () {
    Route::get('/', 'ECommerce@index')->name('apps.ecommerce');
    
    Route::group(['prefix' => 'adverts'], function () {
        Route::get('/', 'Adverts@index')->name('apps.ecommerce.adverts');
        Route::post('/', 'Adverts@post');
    });
    
    Route::group(['prefix' => 'blog', 'namespace' => 'Blog'], function () {
        Route::get('/', 'Dashboard@index')->name('apps.ecommerce.blog');
        Route::post('/', 'Dashboard@blogSettings');
        Route::get('/categories', 'Categories@index')->name('apps.ecommerce.blog.categories');
    });
    
    Route::get('/domains', 'Domains\Domains@index')->name('apps.ecommerce.domains');
    Route::post('/domains', 'Domains\Domains@create');
    Route::group(['middleware' => ['pay_gate']], function () {
        Route::post('/domains/purchase', 'Domains\Domains@purchaseDomain')->name('apps.ecommerce.domains-purchase');
    });
    
    Route::get('/emails', 'Emails@index')->name('apps.ecommerce.emails');
    Route::post('/emails', 'Emails@post');
    Route::get('/online-store', 'OnlineStore@index')->name('apps.ecommerce.store');
    Route::get('/online-store/dashboard', 'OnlineStore@dashboard')->name('apps.ecommerce.store.dashboard');
    Route::post('/online-store/dashboard', 'OnlineStore@storeSettings');
    Route::get('/website', 'Website@index')->name('apps.ecommerce.website');
    Route::post('/website', 'Website@post');
});

Route::group(['middleware' => ['auth'], 'namespace' => 'Finance', 'prefix' => 'apps/finance'], function () {
    Route::get('/', 'Accounts@index')->name('apps.finance');
    Route::get('/entries', 'Entries@index')->name('apps.finance.entries');
    Route::post('/entries', 'Entries@create');
    Route::get('/entries/{id}', 'Entries@showEntry')->name('apps.finance.entry.confirmation');
    Route::post('/entries/{id}', 'Entries@update');
    
    Route::group(['middleware' => ['pay_gate']], function () {
        Route::get('/reports', 'Reports@index')->name('apps.finance.reports');
        Route::get('/reports/configure', 'ConfigureReport@index')->name('apps.finance.reports.configure');
        Route::post('/reports/configure', 'ConfigureReport@configure');
        Route::get('/reports/configure/{id}', 'ConfigureReport@index');
        Route::post('/reports/configure/{id}', 'ConfigureReport@configure');
        Route::get('/reports/{id}', 'Reports@showReportsManager')->name('apps.finance.reports.documents');
    
        Route::get('/transtrak', 'Transtrak@index')->name('apps.finance.transtrak');
    });
    
    Route::get('/{id}', 'Accounts@index');
    Route::post('/{id}', 'Accounts@create');
});

Route::group(['middleware' => ['auth'], 'namespace' => 'Inventory', 'prefix' => 'apps/inventory'], function () {
    Route::get('/categories', 'Categories@index')->name('apps.inventory.categories');
    
    Route::get('/products', 'Products@index')->name('apps.inventory');
    Route::post('/products', 'Products@create');
    Route::get('/products/import', 'Products@index')->name('apps.inventory.import');
    Route::get('/products/new', 'Products@index')->name('apps.inventory.new');
    Route::get('/products/{id}', 'Product@index')->name('apps.inventory.single');
    Route::put('/products/{id}', 'Product@update');
    
    Route::get('/products/{id}/categories', 'Product@redirect');
    Route::post('/products/{id}/categories', 'Product@addCategories')->name('apps.inventory.single.categories');

    Route::get('/products/{id}/images', 'Product@redirect');
    Route::post('/products/{id}/images', 'Product@addImage')->name('apps.inventory.single.images');

    Route::get('/products/{id}/stocks', 'Product@redirect');
    Route::post('/products/{id}/stocks', 'Product@updateStocks')->name('apps.inventory.single.stocks');
});

Route::group(['middleware' => ['auth'], 'namespace' => 'Invoicing', 'prefix' => 'apps/invoicing'], function () {
    Route::get('/orders', 'Orders@index')->name('apps.invoicing.orders');
    Route::get('/orders/new', 'NewOrder@index')->name('apps.invoicing.orders.new');
    Route::post('/orders/new', 'NewOrder@create');
    Route::get('/orders/{id}', 'Order@index')->name('apps.invoicing.orders.single');
});

Route::group(['middleware' => ['auth'], 'namespace' => 'Integrations'], function () {
    Route::get('/integrations', 'Integrations@index')->name('integrations');
    Route::get('/integrations/install', 'Install@index')->name('integrations.install');
});

Route::group(['middleware' => ['auth'], 'namespace' => 'Settings'], function () {
    Route::get('/settings', 'Settings@index')->name('settings');
    Route::get('/settings/bank-accounts', 'BankAccount@index')->name('settings.bank-account');
    Route::post('/settings/bank-accounts', 'BankAccount@post');
    Route::get('/settings/business', 'Business@index')->name('settings.business');
    Route::post('/settings/business', 'Business@post');
    Route::get('/settings/billing', 'Billing@index')->name('settings.billing');
    Route::post('/settings/billing', 'Billing@post');
    Route::get('/settings/customisation', 'Customisation@index')->name('settings.customise');
    Route::post('/settings/customisation', 'Customisation@post');
    Route::get('/settings/account', 'Personal@index')->name('settings.personal');
    Route::post('/settings/account', 'Personal@post');
    Route::get('/settings/security', 'Security@index')->name('settings.security');
    Route::post('/settings/security', 'Security@post');
});

Route::group(['middleware' => ['auth'], 'namespace' => 'Directory', 'prefix' => 'directory'], function () {
    Route::get('/', 'Directory@search')->name('directory');
    
    Route::get('/access-grants', 'AccessGrants@index')->name('directory.access-grant');
    Route::post('/access-grants', 'AccessGrants@post');
    
    Route::get('/profile', 'Profile@index')->name('directory.profile')->middleware('professional_only');
    Route::post('/profile', 'Profile@post')->middleware('professional_only');
    Route::get('/request-manager', 'RequestsManager@index')->name('directory.requests')->middleware('professional_only');
    Route::post('/request-manager', 'RequestsManager@post')->middleware('professional_only');
    
    Route::get('/vendors', 'Directory@searchVendors')->name('directory.vendors');
    Route::get('/vendors/profile', 'Profile@vendorsIndex')->name('directory.vendors.profile')->middleware('vendor_only');
    Route::post('/vendors/profile', 'Profile@vendorsPost')->middleware('vendor_only');
    
    Route::get('/vendors/{id}', 'PayVendor@index')->name('directory.vendors.pay');
    
    Route::get('/{id}', 'Service@index')->name('directory.service');
    Route::post('/{id}', 'Service@request');
});

Route::group(['namespace' => 'Blog', 'middleware' => ['blog_verifier']], function () {
    Route::group(['prefix' => 'blog'], function () {
        Route::get('/', 'Home@index')->name('blog');
        Route::get('/posts', 'Home@index')->name('blog.posts');
        Route::get('/posts/{id}', 'Home@postDetails')->name('blog.posts.details');
        Route::get('/categories', 'Home@categories')->name('blog.categories');
        Route::get('/categories/{id}', 'Home@index')->name('blog.categories.single');
    });
    
    Route::group(['prefix' => 'blog-admin', 'middleware' => ['auth']], function () {
        Route::get('/new-post', 'Posts@newPost')->name('blog.admin.new-post');
        Route::post('/new-post', 'Posts@createPost');
        Route::get('/{id}/edit', 'Posts@editPost')->name('blog.admin.edit-post');
        Route::post('/{id}/edit', 'Posts@updatePost');
        
        Route::delete('/xhr/posts/{id}', 'Posts@deletePostXhr');
    });
});

Route::group(['middleware' => ['auth'], 'prefix' => 'access-grants', 'namespace' => 'AccessGrants'], function () {
    Route::get('/', 'AccessGrantRequests@index')->name('access-grants');
    Route::get('/{id}', 'AccessGrantRequests@index');
    Route::post('/', 'AccessGrantRequests@post');
    Route::post('/{id}', 'AccessGrantRequests@post');
});

Route::group(['namespace' => 'vPanel', 'prefix' => 'vpanel', 'middleware' => ['auth', 'require_role:partner']], function () {
    Route::get('/', 'Businesses\Businesses@index')->name('vpanel.dashboard');
    Route::get('/businesses', 'Businesses\Businesses@index')->name('vpanel.businesses');
    Route::post('/businesses', 'Invites@post');
    Route::get('/businesses/{id}', 'Businesses\Business@index')->name('vpanel.businesses.profile');
    Route::post('/businesses/{id}', 'Businesses\Business@post');
    Route::get('/invites', 'Invites@index')->name('vpanel.invites');
    Route::post('/invites', 'Invites@post');
    Route::get('/customisation', 'Customisation@index')->name('vpanel.customise');
    Route::post('/customisation', 'Customisation@post');
    Route::get('/users', 'Users\Users@index')->name('vpanel.users');
    Route::get('/managers', 'Users\Users@managers')->name('vpanel.users.managers');
    Route::post('/managers', 'Users\Users@post');
    Route::get('/settings', 'Settings@index')->name('vpanel.settings');
    Route::post('/settings', 'Settings@post');
});
