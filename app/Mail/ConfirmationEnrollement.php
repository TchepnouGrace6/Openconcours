<?php

namespace App\Mail;

use App\Models\Enrollement;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ConfirmationEnrollement extends Mailable
{
    use Queueable, SerializesModels;

    public $enrollement; // Contiendra les infos du candidat et du concours

    /**
     * Crée une nouvelle instance
     */
    public function __construct(Enrollement $enrollement)
    {
        $this->enrollement = $enrollement;
    }

    /**
     * Construire le message
     */
    public function build()
    {
        // Ici on construit l'email HTML simple
        return $this->subject('Confirmation de votre candidature au concours')
            ->view('emails.confirmation_enrollement')
            ->with([
                'enrollement' => $this->enrollement,
            ]);
    }
}
