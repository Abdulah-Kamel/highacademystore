<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BranchNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $order;
    public $customMessage;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($order, $customMessage)
    {
        $this->order = $order;
        $this->customMessage = $customMessage;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('طلبك جاهز للاستلام من الفرع - High Academy')
                    ->view('emails.branch_notification');
    }
}
