<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::group(['namespace' => 'App\Http\Controllers\API', 'middleware' => ['cors', 'json.response']],  function () {
    //  all apis comes under here

    // public routes
   /*********************************************************** 
    Route::post('/login', 'AuthController@login')->name('login.api');
    Route::post('/register', 'AuthController@register')->name('register.api');
    Route::post('/verify_email_code', 'AuthController@verify_email_code')->name('verify_email_code.api');

    Route::post('/update_profile', 'UserController@update')->name('update_profile.api');

    Route::get('/fetch_profile_info', 'UserController@index')->name('fetch_profile_info.api');
    
    //bank account detail insertion and update as a single route
    Route::post('/enter_bank_detail', 'UserController@create_bank_detail')->name('enter_bank_detail.api');
    //bank account detail delete route
    Route::delete('/remove_bank_detail/{id}', 'UserController@delete_bank_detail')->name('remove_bank_detail.api');

    Route::post('/validate_bank_account', 'UserController@validate_bank_account')->name('validate_bank_account.api');

    

    Route::post('/logout', 'AuthController@logout')->name('logout.api');

    Route::get('/refresh', 'AuthController@refresh')->name('refresh.api');

    Route::post('/createNewToken', 'AuthController@createNewToken')->name('createNewToken.api');

    Route::post('/generate_code', 'AuthController@generate_code')->name('generate_code.api');
    *********************************************************************/

    // blog apis starts here 
    Route::group(['prefix' => '/books', 'as' => 'books.'], function () {

        Route::get('/fetch_all_books', 'BookController@fetch_books_endpoint')->name('fetch_all_books.api');
        Route::get('/fetch_single_book/{param}', 'BookController@fetch_single_book')->name('fetch_single_book.api');
        Route::get('/sort_character_name/{param}', 'BookController@sort_character_name')->name('sort_character_name.api');
        Route::get('/sort_character_name/{param}', 'BookController@sort_character_name')->name('sort_character_name.api');
        Route::get('/sort_character_gender/{param}', 'BookController@sort_character_gender')->name('sort_character_gender.api');
        Route::get('/fetch_book_names_only', 'BookController@fetch_book_names_only')->name('fetch_book_names_only.api');

        Route::post('/make_comment', 'CommentController@create_comment')->name('make_comment.api');
        
        /*********************************************************************** 
        Route::get('/fetch_one_blog/{id}', 'BlogController@fetch_one_blog')->name('fetch_one_blog.api');

        Route::post('/create_blog', 'BlogController@create_blog')->name('create_blog.api');

        Route::post('/edit_blog', 'BlogController@edit_blog')->name('edit_blog.api');

        Route::delete('/delete_blog/{id}', 'blogController@delete_blog')->name('delete_blog.api');

        Route::get('/like_blog/{blog_id}', 'BlogController@like_blog')->name('like_blog.api');

        Route::get('/unlike_blog/{blog_id}', 'BlogController@unlike_blog')->name('unlike_blog.api');

        Route::post('/create_category', 'CategoryController@create_category')->name('create_category.api');

        Route::get('/fetch_all_category', 'CategoryController@fetch_all_category')->name('fetch_all_category.api');

        Route::get('/fetch_one_category/{id}', 'CategoryController@fetch_one_category')->name('fetch_one_category.api');

        Route::post('/edit_category', 'CategoryController@edit_category')->name('edit_category.api');

        Route::delete('/delete_category/{id}', 'CategoryController@delete_category')->name('delete_category.api');
    *********************************************************************/
    });

    // blog api ends here 



    //Transaction grouped route starts here
    /***********************************************************
    Route::group(['prefix'=>'/transaction','as'=>'transaction.'], function(){
    Route::post('/create_bill_payment', 'TransactionController@create_bill_payment')->name('create_bill_payment.api');
    Route::get('/fetch_bill_categories', 'FlutterwaveController@get_bill_categories')->name('fetch_bill_categories.api');
    Route::get('/fetch_bill_payment_status/{ref}', 'FlutterwaveController@get_bill_payment_status')->name('fetch_bill_payment_status.api');
    Route::get('/fetch_airtime_bill_categorie', 'FlutterwaveController@get_airtime_bills')->name('fetch_airtime_bill_categorie.api');
    Route::get('/fetch_data_bundle_bill_categorie', 'FlutterwaveController@get_data_bundle_bills')->name('fetch_data_bundle_bill_categorie.api');
    Route::get('/fetch_electric_bill_categorie', 'FlutterwaveController@get_electric_bills')->name('fetch_electric_bill_categorie.api');
    Route::get('/fetch_gotv_bill_categorie', 'FlutterwaveController@get_gotv_bills')->name('fetch_gotv_bill_categorie.api');
    Route::get('/fetch_dstv_bill_categorie', 'FlutterwaveController@get_dstv_bills')->name('fetch_dstv_bill_categorie.api');
    Route::get('/fetch_startimes_bill_categorie', 'FlutterwaveController@get_startimes_bills')->name('fetch_startimes_bill_categorie.api');

});
**************************************************/
//Transaction grouped route ends here

//notification grouped route
/*********************************************** *
Route::group(['prefix'=>'/notification','as'=>'notification.'], function(){
//Notification routes
Route::get('/fetch_all_notification', 'NotificationController@index')->name('fetch_all_notification.api');
Route::get('/count_unread_notification', 'NotificationController@count_unread_notification')->name('count_unread_notification.api');
Route::get('/read_notification/{id}', 'NotificationController@read_notification')->name('read_notification.api');
});

//support ticket group
Route::group(['prefix'=>'/support','as'=>'support.'], function(){
//Support routes
Route::get('/fetch_all_support', 'SupportController@index')->name('fetch_all_support.api');
Route::get('/read_support_ticket/{id}', 'SupportController@read_support_ticket')->name('read_support_ticket.api');
Route::post('/create_support_ticket', 'SupportController@store')->name('create_support_ticket.api');
});

Route::group(['prefix' => '/conversion', 'as' => 'conversion.'], function () {
    Route::get('/last_twenty_four_hours/{currency_pair}', 'ConversionController@get_last_twenty_four_hours_rate')->name('get_last_twenty_four_hours_rate.api');
    Route::get('/last_one_hour/{currency_pair}', 'ConversionController@get_last_one_hour_rates')->name('get_last_one_hour_rates.api');
    Route::get('/fetch_all_currency_pairs', 'ConversionController@fetch_all_currency_pairs')->name('fetch_all_currency_pairs.api');
    Route::get('/fetch_one_currency_pair/{currency_pair}', 'ConversionController@fetch_one_currency_pair')->name('fetch_one_currency_pair.api');
    Route::get('/fetch_realtime_trading_pairs', 'ConversionController@get_realtime_trading_pairs')->name('get_realtime_trading_pairs.api');
    Route::get('/last_minute_rates/{currency_pair}', 'ConversionController@get_last_minute_rates')->name('get_last_minute_rates.api');
    Route::get('/fetch_rates_with_options/{currency_pair}/{timeframe}/{limit}', 'ConversionController@fetch_rates_with_options')->name('fetch_rates_with_options.api');
    
});


    Route::post('/verify_customer', 'PaystackCustomerController@verify_customer')->name('verify_customer.api');

    Route::get('/initialiseTransaction/{email}/{amount}/{first_name}/{last_name}', 'PaymentController@initialiseTransaction')->name('initialiseTransaction.api');

    Route::post('/verify_customer', 'UserController@verify_paystack_customer')->name('verify_customer.api');


    Route::get('/paystackVerifyPayment/{reference_no}', 'PaymentController@paystackVerifyPayment')->name('paystackVerifyPayment.api');

    *****************************************************************/

});

    // comment api starts here 
     /********************************************************* 
    Route::group(['prefix' => '/comment', 'as' => 'comment.'], function () {
        Route::post('/create_comment', 'CommentController@create_comment')->name('create_comment.api');

        Route::get('/fetch_blog_comment/{blog_id}', 'CommentController@fetch_blog_comment')->name('fetch_blog_comment.api');

        Route::get('/like_comment/{comment_id}', 'CommentController@like_comment')->name('like_comment.api');

        Route::get('/unlike_comment/{comment_id}', 'CommentController@unlike_comment')->name('unlike_comment.api');

        Route::post('/reply_comment', 'CommentController@reply_comment')->name('reply_comment.api');

        Route::post('/edit_comment', 'CommentController@edit_comment')->name('edit_comment.api');

        Route::delete('/delete_comment/{id}', 'CommentController@delete_comment')->name('delete_comment.api');

        Route::get('/fetch_comment_replies/{comment_id}', 'CommentController@fetch_comment_replies')->name('fetch_comment_replies.api');

        Route::get('/fetch_comment_replies_by_blog_id/{blog_id}/{comment_id}', 'CommentController@fetch_comment_replies_by_blog_id')->name('fetch_comment_replies_by_blog_id.api');

        Route::delete('/delete_comment_reply/{reply_id}', 'CommentController@delete_comment_reply')->name('delete_comment_reply.api');

        Route::post('/edit_comment_reply', 'CommentController@edit_comment_reply')->name('edit_comment_reply.api');
    });

    // career type starts here 

    Route::group(['prefix' => '/career', 'as' => 'career.'], function () {

        Route::post('/create_career_type', 'CareerTypeController@create_career_type')->name('create_career_type.api');

        Route::get('/fetch_all_career_type', 'CareerTypeController@fetch_all_career_type')->name('fetch_all_career_type.api');

        Route::get('/fetch_one_career_type/{id}', 'CareerTypeController@fetch_one_career_type')->name('fetch_one_career_type.api');

        Route::post('/edit_career_type', 'CareerTypeController@edit_career_type')->name('edit_career_type.api');

        Route::delete('/delete_career_type/{id}', 'CareerTypeController@delete_career_type')->name('delete_career_type.api');

        // career type ends here 

        // career starts here

        Route::post('/create_career', 'CareerController@create_career')->name('create_career.api');
        Route::get('/fetch_all_careers', 'CareerController@fetch_all_careers')->name('fetch_all_careers.api');

        Route::get('/fetch_career_by_type/{career_type_id}', 'CareerController@fetch_career_by_type')->name('fetch_career_by_type.api');

        Route::post('/edit_career', 'CareerController@edit_career')->name('edit_career.api');

        Route::delete('/delete_career/{id}', 'CareerController@delete_career')->name('delete_career.api');

        Route::post('/submit_application', 'CareerController@submit_application')->name('submit_application.api');


        // career starts here 

    });

    

    // 

    // dashboard functions starts here 
    // Route::group(['middleware' => ['jwt']],  function () {
    //  all apis comes under here
    


    //Transaction grouped route starts here
    Route::group(['prefix' => '/transaction', 'as' => 'transaction.'], function () {
        Route::post('/create_bill_payment', 'TransactionController@create_bill_payment')->name('create_bill_payment.api');
    });
    *********************************************************/
    //Transaction grouped route ends here
        // dashboard functions end here
    // black petals 



