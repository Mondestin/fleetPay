<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use App\Models\User;

class UserCreated extends Mailable
{
    public function __construct(public User $user) {}

    public function build()
    {
        return $this->markdown('emails.user-created');
    }
} 