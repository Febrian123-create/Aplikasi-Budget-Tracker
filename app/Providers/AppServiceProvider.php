<?php

namespace App\Providers;

use App\Observers\BudgetObserver;
use App\Observers\ChartObserver;
use App\Observers\ReminderObserver;
use App\Observers\TransactionSubject;
use App\Repositories\BudgetRepository;
use App\Repositories\ReminderRepository;
use App\Services\BudgetService;
use App\Services\ChartService;
use App\Services\ReminderService;
use App\Strategies\Reminder\EmailReminderStrategy;
use App\Strategies\Reminder\GoogleCalendarReminderStrategy;
use App\Strategies\Reminder\PopupReminderStrategy;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ReminderService::class, function ($app) {
            $reminderRepo = $app->make(ReminderRepository::class);
            return new ReminderService($reminderRepo, [
                'email'            => new EmailReminderStrategy(),
                'popup'            => new PopupReminderStrategy($reminderRepo),
                'google_calendar'  => new GoogleCalendarReminderStrategy(),
            ]);
        });

        $this->app->singleton(BudgetService::class, function ($app) {
            $reminderRepo = $app->make(ReminderRepository::class);
            return new BudgetService(
                $app->make(BudgetRepository::class),
                $reminderRepo,
                [
                    'email'            => new EmailReminderStrategy(),
                    'popup'            => new PopupReminderStrategy($reminderRepo),
                    'google_calendar'  => new GoogleCalendarReminderStrategy(),
                ]
            );
        });
    }

    public function boot(): void
    {
        $subject = TransactionSubject::getInstance();

        $subject->subscribe(new ChartObserver(app(ChartService::class)));
        $subject->subscribe(new BudgetObserver(app(BudgetService::class)));
        $subject->subscribe(new ReminderObserver(app(ReminderService::class)));
    }
}
