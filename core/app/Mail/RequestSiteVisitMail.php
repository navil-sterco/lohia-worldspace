<?php

namespace App\Mail;

use App\Models\RequestSiteVisit;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RequestSiteVisitMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public RequestSiteVisit $siteVisit) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Thank you for your site visit request',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.request-site-visit',
        );
    }

    public function attachments(): array
    {
        $relativePdfPath = 'assets/img/modules/3/module_1789635996_6aabad9c000b2.pdf';
        $pdfPath = dirname(base_path()) . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relativePdfPath);

        if (!is_file($pdfPath)) {
            $pdfPath = public_path($relativePdfPath);
        }

        if (!is_file($pdfPath)) {
            throw new \RuntimeException('The site visit brochure PDF could not be found.');
        }

        return [
            Attachment::fromPath($pdfPath)
                ->as('Lohia-Worldspace-Brochure.pdf')
                ->withMime('application/pdf'),
        ];
    }
}
