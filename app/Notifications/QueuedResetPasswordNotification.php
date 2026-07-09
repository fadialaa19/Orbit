<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword as BaseResetPasswordNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

class QueuedResetPasswordNotification extends BaseResetPasswordNotification implements ShouldQueue
{
}
