<?php

namespace App\Mail;

use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InvoiceIssued extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Invoice $invoice) {}

    public function build()
    {
        return $this
            ->subject('Your invoice '.$this->invoice->number)
            ->view('emails.invoice_issued', [
                'invoice' => $this->invoice->loadMissing('items','subscription','user'),
            ]);
    }
}
