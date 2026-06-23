<?php

use App\Http\Controllers\CommentController;
use App\Http\Controllers\IssueController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TagController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return redirect()->route('projects.index');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('projects', ProjectController::class);
    Route::resource('issues', IssueController::class);

    // Tags
    Route::get('tags', [TagController::class, 'index'])->name('tags.index');
    Route::post('tags', [TagController::class, 'store'])->name('tags.store');

    // Tag attach/detach on an issue (AJAX)
    Route::post('issues/{issue}/tags', [TagController::class, 'attach'])->name('issues.tags.attach');
    Route::delete('issues/{issue}/tags/{tag}', [TagController::class, 'detach'])->name('issues.tags.detach');

    // Comments on an issue (AJAX)
    Route::get('issues/{issue}/comments', [CommentController::class, 'index'])->name('issues.comments.index');
    Route::post('issues/{issue}/comments', [CommentController::class, 'store'])->name('issues.comments.store');

    // Member assignment on an issue (AJAX)
    Route::post('issues/{issue}/members', [MemberController::class, 'attach'])->name('issues.members.attach');
    Route::delete('issues/{issue}/members/{user}', [MemberController::class, 'detach'])->name('issues.members.detach');
});

require __DIR__.'/auth.php';
