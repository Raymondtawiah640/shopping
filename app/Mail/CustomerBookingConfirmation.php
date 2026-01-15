<?php

namespace App\Mail;

use App\Models\HospitalityBooking;
use App\Models\Customer;
use App\Models\Vendor;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CustomerBookingConfirmation extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $booking;
    public $customer;
    public $vendor;
    public $service;

    /**
     * Create a new message instance.
     */
    public function __construct(HospitalityBooking $booking, Customer $customer, Vendor $vendor, $service)
    {
        $this->booking = $booking;
        $this->customer = $customer;
        $this->vendor = $vendor;
        $this->service = $service;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Booking Confirmation - ' . $this->booking->booking_id,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.customer_booking_confirmation',
            with: [
                'booking' => $this->booking,
                'customer' => $this->customer,
                'vendor' => $this->vendor,
                'service' => $this->service,
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