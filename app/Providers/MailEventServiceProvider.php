<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use Illuminate\Mail\Events\MessageSending;
use Illuminate\Mail\Events\MessageSent;
use App\Listeners\LogEmailBeforeSending;
use App\Listeners\MarkEmailSent;

class MailEventServiceProvider extends ServiceProvider
{

    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Event::listen(
            MessageSending::class, [LogEmailBeforeSending::class, 'handle']
        );
        Event::listen(
            MessageSent::class, [MarkEmailSent::class, 'handle']
        );
    }

}
