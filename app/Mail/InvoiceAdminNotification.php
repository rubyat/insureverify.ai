<?php

namespace App\Mail;

use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InvoiceAdminNotification extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Invoice $invoice) {}

    public function build()
    {
        return $this
            ->subject('New invoice issued: '.$this->invoice->number)
            ->view('emails.invoice_admin_notification', [
                'invoice' => $this->invoice->loadMissing('items','subscription','user'),
            ]);
    }
}
