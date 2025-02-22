<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReportEmail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public $subject, public $description, public $excelPath, public $pdfPath) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->subject,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'mail.reports',
        );
    }

    public function attachments(): array
    {
        if (!$this->excelPath && !$this->pdfPath) {
            return [];
        }

        if ($this->excelPath && $this->pdfPath) {
            return [
                Attachment::fromPath($this->excelPath)
                    ->as(basename($this->excelPath))
                    ->withMime('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'),

                Attachment::fromPath($this->pdfPath)
                    ->as(basename($this->pdfPath))
                    ->withMime('application/pdf'),
            ];
        } else if ($this->excelPath) {
            return [
                Attachment::fromPath($this->excelPath)
                    ->as(basename($this->excelPath))
                    ->withMime('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'),
            ];
        } else {
            return [
                Attachment::fromPath($this->pdfPath)
                    ->as(basename($this->pdfPath))
                    ->withMime('application/pdf'),
            ];
        }
    }
}
