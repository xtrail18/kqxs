<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AdvController;
use App\Http\Controllers\Admin\ArticleController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\GenreController;
use App\Http\Controllers\Admin\PageController as AdminPageController;
use App\Http\Controllers\Admin\ProxyController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\UploadController;
use App\Http\Controllers\AssetProxyController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\XosoAjaxController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Symfony\Component\Process\Process;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/sitemap.xml', function () {
    return response()->file(storage_path('app/public/sitemaps/sitemap.xml'), [
        'Content-Type' => 'application/xml'
    ]);
});

Route::middleware(['admin'])
    ->name('admin.')
    ->prefix('admin')
    ->group(function () {
        Route::get('/', [AdminController::class, 'index'])->name('dashboard');

        // account
        Route::get('/accounts', [AdminController::class, 'accounts'])->name('account.index');
        Route::post('/account/store', [AdminController::class, 'store'])->name('account.store');
        Route::get('/upgrading', [AdminController::class, 'upgrading'])->name('upgrading');


        // genres
        Route::prefix('genres')->name('genres.')->group(function () {
            Route::get('/', [GenreController::class, 'index'])->name('index');
            Route::get('/create', [GenreController::class, 'create'])->name('create');
            Route::get('/edit/{id}', [GenreController::class, 'edit'])->name('edit');

            Route::put('/update/{id}', [GenreController::class, 'update'])->name('update');
            Route::post('/store', [GenreController::class, 'store'])->name('store');
        });

        // proxies
        Route::prefix('proxies')->name('proxies.')->group(function () {
            Route::get('/', [ProxyController::class, 'index'])->name('index');
            Route::get('/create', [ProxyController::class, 'create'])->name('create');
            Route::get('/edit/{id}', [ProxyController::class, 'edit'])->name('edit');

            Route::put('/update/{id}', [ProxyController::class, 'update'])->name('update');
            Route::post('/store', [ProxyController::class, 'store'])->name('store');
        });


        // news
        Route::prefix('articles')->name('articles.')->group(function () {
            Route::get('/', [ArticleController::class, 'index'])->name('index');
            Route::get('/scheduled', [ArticleController::class, 'scheduled'])->name('scheduled');
            Route::get('/hidden', [ArticleController::class, 'hidden'])->name('hidden');

            Route::get('/create', [ArticleController::class, 'create'])->name('create');
            Route::get('/edit/{id}', [ArticleController::class, 'edit'])->name('edit');

            Route::put('/update/{id}', [ArticleController::class, 'update'])->name('update');
            Route::post('/store', [ArticleController::class, 'store'])->name('store');

            Route::delete('/destroy/{id}', [ArticleController::class, 'destroy'])->name('destroy');
            Route::post('/active', [ArticleController::class, 'active'])->name('active');
            Route::post('/highlight', [ArticleController::class, 'highlight'])->name('highlight');
        });

        // routes/web.php
        Route::post('/tinymce/upload', [UploadController::class, 'tinymce'])->name('tinymce.upload');

        // setting
        Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
        Route::post('/settings/store', [SettingController::class, 'store'])->name('settings.store');

        Route::get('settings/custom-css', [SettingController::class, 'editCustomCss'])->name('settings.css.edit');
        Route::post('settings/custom-css', [SettingController::class, 'updateCustomCss'])->name('settings.css.update');

        //adv
        Route::get('/advs', [AdvController::class, 'index'])->name('adv.index');

        Route::prefix('adv')->name('adv.')->group(function () {

            Route::post('/store', [AdvController::class, 'store'])->name('store');
            Route::get('/banner', [AdvController::class, 'banner'])->name('banner');
            Route::get('/banner-script', [AdvController::class, 'bannerScript'])->name('banner-script');
            Route::get('/catfish', [AdvController::class, 'catfish'])->name('catfish');
            Route::get('/preload', [AdvController::class, 'preload'])->name('preload');
            Route::get('/pushjs', [AdvController::class, 'pushjs'])->name('pushjs');
            Route::get('/popupjs', [AdvController::class, 'popupjs'])->name('popupjs');
            Route::get('/text-link', [AdvController::class, 'textLink'])->name('text-link');
            Route::get('/header', [AdvController::class, 'header'])->name('header');
            Route::get('/bottom', [AdvController::class, 'bottom'])->name('bottom');
            Route::post('/refresh', [AdvController::class, 'refresh'])->name('refresh');
            Route::post('/active', [AdvController::class, 'active'])->name('active');
            Route::post('/delete/{id}', [AdvController::class, 'delete'])->name('delete');
            Route::get('/popunder', [AdvController::class, 'popunder'])->name('popunder');
            Route::post('/popunder', [AdvController::class, 'storePopunder'])->name('popunder.store');
        });

        Route::get('/pages/term',  [AdminPageController::class, 'editTerm'])->name('pages.term.edit');
        Route::post('/pages/term', [AdminPageController::class, 'upsertTerm'])->name('pages.term.upsert');

        Route::get('/pages/contact',  [AdminPageController::class, 'editContact'])->name('pages.contact.edit');
        Route::post('/pages/contact', [AdminPageController::class, 'upsertContact'])->name('pages.contact.upsert');


        Route::get('/artisan', function () {
            return view('admin.setting.artisan-runner');
        });

        Route::post('/artisan', function (\Illuminate\Http\Request $request) {
            $phpPath = '/usr/bin/php';
            $command = $phpPath . ' artisan ' . escapeshellcmd($request->input('command'));

            $process = Symfony\Component\Process\Process::fromShellCommandline($command, base_path());
            $process->run();

            return response()->json([
                'success' => $process->isSuccessful(),
                'output' => $process->getOutput(),
                'error' => $process->getErrorOutput(),
            ]);
        });

        Route::get('/refresh-storage-link', function () {
            $storageLink = public_path('storage');

            // Xoá symbolic link hoặc thư mục thật
            if (is_link($storageLink)) {
                unlink($storageLink);
            } elseif (file_exists($storageLink)) {
                File::deleteDirectory($storageLink);
            }

            // Tạo lại symbolic link
            $phpBinary = '/usr/bin/php';
            $command = [$phpBinary, 'artisan', 'storage:link'];

            $process = new Process($command, base_path());
            $process->run();

            return response()->json([
                'success' => $process->isSuccessful(),
                'output'  => $process->getOutput(),
                'error'   => $process->getErrorOutput(),
            ]);
        });

        Route::prefix('brands')->name('brand.')->group(function () {
            Route::get('/', [BrandController::class, 'index'])->name('index');
            Route::post('/store', [BrandController::class, 'store'])->name('store');
            Route::delete('/{brand}', [BrandController::class, 'destroy'])->name('destroy');
        });
    });

Route::get('/api/account/detail/{id}', [AdminController::class, 'detail'])->name('detail');

Route::group(['prefix' => 'filemanager', 'middleware' => ['web', 'admin']], function () {
    \UniSharp\LaravelFilemanager\Lfm::routes();
});

// ads
Route::get('/storage/uploads/advs/{path?}', function ($path) {
    $cacheKey = 'adv_' . $path;

    if (Cache::store('file')->has($cacheKey)) {
        $imageString = Cache::store('file')->get($cacheKey);
    } else {
        $imagePath = storage_path('app/public/uploads/advs/' . $path);

        if (!file_exists($imagePath)) {
            $imagePath = public_path('system/img/no-image.png');
        }

        $imageString = file_get_contents($imagePath);

        Cache::store('file')->put($cacheKey, $imageString, now()->addMinutes(60));
    }

    $response = response($imageString)->header('Content-Type', 'image/gif');
    $response->header('Cache-Control', 'public, max-age=31536000');
    return $response;
})->name('web.adv.banner');

// Admin auth routes
Route::get('/login', [AdminController::class, 'login'])->name('admin.login');
Route::post('/login',  [AdminController::class, 'postLogin'])->name('admin.post.login');
Route::post('/logout',  [AdminController::class, 'logout'])->name('admin.logout');

// Frontend user auth routes
Route::get('/dang-nhap', [AuthController::class, 'showLogin'])->name('login');
Route::post('/dang-nhap', [AuthController::class, 'login']);
Route::get('/dang-ky', [AuthController::class, 'showRegister'])->name('register');
Route::post('/dang-ky', [AuthController::class, 'register']);
Route::post('/dang-xuat', [AuthController::class, 'logout'])->name('logout');

// web
Route::any('ThongKeAjax/{action?}', [XosoAjaxController::class, 'forward'])->where('action', '.*')->defaults('service', 'ThongKeAjax');
Route::any('ThongKeService/{action?}', [XosoAjaxController::class, 'forward'])->where('action', '.*')->defaults('service', 'ThongKeService');
Route::any('Ajax/{action?}', [XosoAjaxController::class, 'forward'])->where('action', '.*')->defaults('service', 'Ajax');
Route::any('Keno/{action?}', [XosoAjaxController::class, 'forward'])->where('action', '.*')->defaults('service', 'Keno');
Route::any('XSDienToan/{action?}', [XosoAjaxController::class, 'forward'])->where('action', '.*')->defaults('service', 'XSDienToan');
Route::any('TinTuc/{action?}', [XosoAjaxController::class, 'forward'])->where('action', '.*')->defaults('service', 'TinTuc');

Route::get('/muc/{slug}', [PageController::class, 'genre'])->name('genre');
Route::get('/tin-xo-so/{slug}.html', [PageController::class, 'article'])->name('article');



// =============================
// FRONTEND MIRROR & ASSET PROXY
// =============================

// 1️⃣ ASSET PROXY: chỉ cho phép các thư mục asset frontend, bỏ qua /admin/*
Route::get('{prefix}/{path}', [AssetProxyController::class, 'proxy'])
    ->where([
        // không match nếu prefix bắt đầu bằng admin
        'prefix' => '^(?!(admin))(?:medias|images|content|static|cdn|css|js|img|fonts|assets)$',
        'path'   => '.*',
    ])
    ->name('asset.proxy');

// 2️⃣ CATCH-ALL: HomeController cho các trang frontend (trừ admin, ajax services)
Route::get('{any?}', [HomeController::class, 'index'])
    ->where('any', '^(?!admin)(?!(?:ThongKeAjax|ThongKeService|Ajax)(?:/|$)).*$')
    ->name('home.index');
