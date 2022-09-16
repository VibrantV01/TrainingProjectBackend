<?php

namespace App\Providers;

use Laravel\Lumen\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        
        'App\Events\TaskAssigned' => [
            'App\Listeners\TaskAssignedListener',
           ],
        'App\Events\TaskEditted' => [
            'App\Listeners\TaskEditListener'
        ],   
        'App\Events\TaskUpdate' => [
            'App\Listeners\TaskUpdateListener'
        ],
        'App\Events\UserEdit' => [
            'App\Listeners\UserEditListener'
        ],
        'App\Events\NotificationEvent' => [
            'App\Listeners\NotificationListener',
        ],
    ];
}
