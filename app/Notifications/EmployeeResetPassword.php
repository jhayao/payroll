<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword as ResetPasswordNotification;

class EmployeeResetPassword extends ResetPasswordNotification
{
    /**
     * Get the reset password URL for the given notifiable.
     */
    protected function resetUrl($notifiable)
    {
        return url(route('employee.reset-password', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));
    }
}
