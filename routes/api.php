<?php

use App\Http\Controllers\VideoController;
use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('video', [VideoController::class, 'store']);

Route::get('/video/secret/{key}', function ($key) {
    return Storage::disk('secrets')->download($key);
})->name('video.key');

Route::get('/video/segment/{segment}', function ($segment) {
    return Storage::disk('public')->download("streamable_videos/$segment");
})->name('video.segment');

Route::get('/video/{playlist}', function ($playlist) {
    return FFMpeg::dynamicHLSPlaylist()
        ->fromDisk('public')
        ->open('streamable_videos/' . $playlist)
        ->setKeyUrlResolver(function ($key) {
            return route('video.key', ['key' => $key]);
        })
        ->setMediaUrlResolver(function ($mediaFilename) {
            return route('video.segment', ['segment' => $mediaFilename]);
        })
        ->setPlaylistUrlResolver(function ($playlistFilename) {
            return route('video.playlist', ['playlist' => $playlistFilename]);
        });
})->name('video.playlist');
