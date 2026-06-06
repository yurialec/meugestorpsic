<?php

namespace App\Mail;

use App\Models\Tenants\Appointment;
use App\Models\Tenants\Patient;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AppointmentScheduled extends Mailable
{
    use Queueable, SerializesModels;

    public $patient;
    public $appointment;

    public function __construct(Patient $patient, Appointment $appointment)
    {
        $this->patient = $patient;
        $this->appointment = $appointment;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Seu agendamento foi confirmado')
                    ->view('tenant.emails.appointment-scheduled')
                    ->with([
                        'patient' => $this->patient,
                        'appointment' => $this->appointment,
                    ]);
    }
}