<?php


Route::get('/', function () {
    return view('welcome');
});


Route::group(['middleware'=>['api']],function()
{
    Route::post('validate/user',['uses'=>'Validation\UserController@user',]);
});

Auth::routes();
Route::get('/home','HomeController@index');




//Información del header de la cotización
Route::resource('estimate', 'EstimateController');
Route::get('getEstimateBaseInfo','EstimateController@BaseInfo');
//taxes
Route::resource('taxes', 'TaxesController');
Route::get('getTaxesBaseInfo','TaxesController@BaseInfo');
//Retentions
Route::resource('retention', 'RetentionController');
Route::get('getRetentionBaseInfo','RetentionController@BaseInfo');
//payment terms
Route::resource('payterms', 'PaymentTermsController');

//profile
Route::resource('profile', 'ProfileController');
Route::get('getProfileInfo','ProfileController@BaseInfo');
//Bank accounts
Route::resource('bank_account', 'BankAccountController');
Route::get('getBankAccount_Type','BankAccountController@BaseInfo');

//precio de lista
Route::resource('list_price', 'ListPriceController');

//Contacts
Route::resource('contact', 'ContactsController');
Route::get('getContactBaseInfo','ContactsController@BaseInfo');

//Seller
Route::resource('seller', 'SellerController');
