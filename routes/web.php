<?php

use Illuminate\Support\Facades\Route;
use UniSharp\LaravelFilemanager\Lfm;
use App\Http\Controllers\Front\{
    PostController as FrontPostController,
    CommentController as FrontCommentController,
    contactController as FrontContactController,
    PageController as FrontPageController
};
use App\Http\Controllers\Back\{
    AdminController,
    PostController as BackPostController
};
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

Route::name('home')->get('/', [FrontPostController::class, 'index']);

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

Route::group(['prefix' => 'laravel-filemanager', 'middleware' => 'auth'], function () {
    Lfm::routes();
});
Route::prefix('posts')->group(function () {
    Route::name('posts.display')->get('{slug}', [FrontPostController::class, 'show']);
    Route::name('posts.search')->get('', [FrontPostController::class, 'search']);
    Route::name('posts.comments')->get('{post}/comments', [FrontCommentController::class, 'comments']);
    Route::name('posts.comments.store')->post('{post}/comments', [FrontCommentController::class, 'store'])->middleware('auth');
});
Route::name('category')->get('category/{category:slug}', [FrontPostController::class, 'category']);

Route::name('author')->get('author/{user}', [FrontPostController::class, 'user']);

Route::name('tag')->get('tag/{tag:slug}', [FrontPostController::class, 'tag']);

Route::name('front.comments.destroy')->delete('comments/{comment}', [FrontCommentController::class, 'destroy']);

Route::resource('contacts', FrontContactController::class, ['only' => ['create', 'store']]);

Route::name('page')->get('page/{page:slug}', FrontPageController::class);

Route::prefix('admin')->group(function () {
    Route::middleware('redac')->group(function () {
        Route::name('admin')->get('/', [AdminController::class, 'index']);
        Route::name('purge')->put('purge/{model}', [AdminController::class, 'purge']);
        Route::resource('posts', BackPostController::class)->except('show');
    });
    Route::middleware('admin')->group(function () {
        Route::name('posts.indexnew')->get('newposts', [BackPostController::class, 'index']);
    });
});

require __DIR__.'/auth.php';
