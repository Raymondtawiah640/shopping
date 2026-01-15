<?php

namespace App\Mail;

use App\Models\Order;
use App\Models\Customer;
use App\Models\Vendor;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class VendorOrderNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $order;
    public $customer;
    public $vendor;
    public $vendorItems;

    /**
     * Create a new message instance.
     */
    public function __construct(Order $order, Customer $customer, Vendor $vendor, array $vendorItems)
    {
        $this->order = $order;
        $this->customer = $customer;
        $this->vendor = $vendor;
        $this->vendorItems = $vendorItems;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Order for Your Products - ' . $this->order->order_id,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.vendor_order_notification',
            with: [
                'order' => $this->order,
                'customer' => $this->customer,
                'vendor' => $this->vendor,
                'vendorItems' => $this->vendorItems,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}