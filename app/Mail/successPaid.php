<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
// App\Mail\SuccessPaid.php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Order;

class SuccessPaid extends Mailable
{
    use Queueable, SerializesModels;

    /** @var Order */
    public $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function build()
    {
        return $this
            ->subject('تم الدفع بنجاح')
            ->view('emails.success_paid')
            ->with([
                'order' => $this->order,
            ]);
    }
}
