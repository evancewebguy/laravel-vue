<?php

use App\Support\AdvancedRoute;

Route::group([
    'as'     => '/v1',
    'prefix' => '/v1',
], function () {

    Route::get('/books/search', [\App\Http\Controllers\API\BookAPIController::class, 'search']);

    Route::get('/loans/extend/{loan_is}', [\App\Http\Controllers\API\LoanAPIController::class, 'putExtend']);


    AdvancedRoute::controllers([
        'authors' => \App\Http\Controllers\API\AuthorAPIController::class,
        'books'   => \App\Http\Controllers\API\BookAPIController::class,
        'loans'   => \App\Http\Controllers\API\LoanAPIController::class,
        'users'   => \App\Http\Controllers\API\UserAPIController::class,
    ]);
});
