<?php

namespace App\Providers;

use App\Events\ChangePipeline;
use App\Events\ChangeResponsible;
use App\Events\ChangeStage;
use App\Events\CreateDeal;
use App\Events\CreateTask;
use App\Listeners\ChangePipelineNotification;
use App\Listeners\ChangeResponsibleNotification;
use App\Listeners\ChangeStageNotification;
use App\Listeners\CreateDealNotification;
use App\Listeners\CreateTaskNotification;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        ChangePipeline::class => [
            ChangePipelineNotification::class
        ],
        ChangeStage::class => [
            ChangeStageNotification::class
        ],
        ChangeResponsible::class => [
            ChangeResponsibleNotification::class
        ],
        CreateDeal::class => [
            CreateDealNotification::class
        ],
        CreateTask::class => [
            CreateTaskNotification::class
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     *
     * @return bool
     */
    public function shouldDiscoverEvents()
    {
        return false;
    }
}
