<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use App\Models\Borrow;
use App\Models\StudentSubmission;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\View;
use App\Observers\BorrowObserver;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    
public function boot(): void
{
    \Illuminate\Pagination\Paginator::useBootstrapFive();
    View::composer('backend.layout.master', function ($view) {
        $pendingSubmissionCount = StudentSubmission::where('is_borrow_approved', false)->count();

        $overdueCount = Borrow::whereNull('return_date')
            ->where('borrow_date', '<', now()->subDays(2))
            ->whereIn('status', ['BORROWED', 'OVERDUE'])
            ->count();

        $lateReturnedCount = Borrow::where('status', 'RETURNED')
            ->whereNotNull('return_date')
            ->whereColumn('return_date', '>', 'due_date')
            ->count();

        $view->with(compact(
            'pendingSubmissionCount',
            'overdueCount',
            'lateReturnedCount'
        ));
    });
    Borrow::observe(BorrowObserver::class);

}
}