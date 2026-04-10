<?php

namespace App\Notifications;

use App\Models\Appointment;
use Illuminate\Notifications\Messages\MailMessage;

class AppointmentNotification extends BaseAppointmentNotification
{
    public function toMail($notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject($this->getEmailSubject())
            ->greeting($this->getEmailGreeting($notifiable))
            ->line($this->getFormattedMessage());

        foreach ($this->data as $key => $value) {
            if (is_string($value) && !empty($value)) {
                $mail->line("**" . ucfirst($key) . ":** " . $value);
            }
        }

        if ($this->actionUrl && $this->actionText) {
            $mail->action($this->actionText, $this->actionUrl);
        }

        $mail->line($this->getClosingLine());
        return $mail;
    }

    public function toDatabase($notifiable): array
    {
        return [
            'title' => $this->title,
            'message' => $this->message,
            'type' => $this->type,
            'icon' => $this->getIcon(),
            'action_url' => $this->actionUrl,
            'action_text' => $this->actionText,
            'data' => $this->data,
            'appointment_id' => $this->appointmentId,
            'read_at' => null,
            'created_at' => now()->toISOString(),
        ];
    }

    public function toBroadcast($notifiable): array
    {
        return [
            'title' => $this->title,
            'message' => $this->message,
            'type' => $this->type,
            'icon' => $this->getIcon(),
            'action_url' => $this->actionUrl,
            'action_text' => $this->actionText,
            'data' => $this->data,
            'appointment_id' => $this->appointmentId,
            'created_at' => now()->toISOString(),
        ];
    }

    private function getEmailSubject(): string
    {
        $prefix = match($this->type) {
            'success' => '✓ Succès', 'error' => '✗ Erreur', 'warning' => '⚠ Attention',
            'reminder' => '🔔 Rappel', 'urgent' => '🚨 Urgent',
            'confirmation' => '✓ Confirmation', 'cancellation' => '✗ Annulation',
            default => 'ℹ Information',
        };
        return "[HealthSys] {$prefix} - {$this->title}";
    }

    private function getEmailGreeting($notifiable): string
    {
        $name = $notifiable->name ?? $notifiable->email ?? 'Cher patient';
        return "Bonjour {$name},";
    }

    private function getFormattedMessage(): string
    {
        return "{$this->getIcon()} {$this->message}";
    }

    private function getClosingLine(): string
    {
        return "Merci de votre confiance ! L'équipe HealthSys";
    }

    // Factory methods
    public static function success(string $title, string $message, ?string $actionUrl = null): self
    {
        return new self(title: $title, message: $message, type: 'success', actionUrl: $actionUrl, actionText: $actionUrl ? 'Voir les détails' : null);
    }

    public static function error(string $title, string $message, ?string $actionUrl = null): self
    {
        return new self(title: $title, message: $message, type: 'error', actionUrl: $actionUrl, actionText: $actionUrl ? 'Voir les détails' : null);
    }

    public static function warning(string $title, string $message, ?string $actionUrl = null): self
    {
        return new self(title: $title, message: $message, type: 'warning', actionUrl: $actionUrl, actionText: $actionUrl ? 'En savoir plus' : null);
    }

    public static function info(string $title, string $message, ?string $actionUrl = null): self
    {
        return new self(title: $title, message: $message, type: 'info', actionUrl: $actionUrl, actionText: $actionUrl ? 'En savoir plus' : null);
    }

    public static function reminder(string $title, string $message, ?string $actionUrl = null): self
    {
        return new self(title: $title, message: $message, type: 'reminder', actionUrl: $actionUrl, actionText: $actionUrl ? 'Confirmer ma présence' : null);
    }

    public static function urgent(string $title, string $message, ?string $actionUrl = null): self
    {
        return new self(title: $title, message: $message, type: 'urgent', actionUrl: $actionUrl, actionText: $actionUrl ? 'Agir maintenant' : null);
    }

    public static function appointmentConfirmation(Appointment $appointment): self
    {
        $doctorName = optional(optional($appointment->doctor)->user)->name ?? 'Médecin';
        $date = $appointment->date_time->format('d/m/Y');
        $time = $appointment->date_time->format('H:i');

        return new self(
            title: 'Rendez-vous confirmé',
            message: "Votre rendez-vous avec Dr. {$doctorName} a été confirmé pour le {$date} à {$time}.",
            type: 'confirmation',
            actionUrl: url("/appointments/{$appointment->id}"),
            actionText: 'Voir le rendez-vous',
            data: ['doctor' => $doctorName, 'date' => $date, 'time' => $time, 'location' => $appointment->location ?? 'Cabinet médical'],
            appointmentId: $appointment->id
        );
    }

    public static function appointmentCancellation(Appointment $appointment, ?string $reason = null): self
    {
        $doctorName = optional(optional($appointment->doctor)->user)->name ?? 'Médecin';
        $date = $appointment->date_time->format('d/m/Y');
        $time = $appointment->date_time->format('H:i');
        $message = "Votre rendez-vous avec Dr. {$doctorName} du {$date} à {$time} a été annulé.";
        if ($reason) $message .= " Raison: {$reason}";

        return new self(
            title: 'Rendez-vous annulé',
            message: $message,
            type: 'cancellation',
            actionUrl: url("/patient/appointments/create"),
            actionText: 'Prendre un nouveau rendez-vous',
            data: ['doctor' => $doctorName, 'date' => $date, 'time' => $time, 'reason' => $reason],
            appointmentId: $appointment->id
        );
    }

    public static function appointmentReminder(Appointment $appointment): self
    {
        $doctorName = optional(optional($appointment->doctor)->user)->name ?? 'Médecin';
        $date = $appointment->date_time->format('d/m/Y');
        $time = $appointment->date_time->format('H:i');

        return new self(
            title: 'Rappel de rendez-vous',
            message: "Rappel : Vous avez un rendez-vous avec Dr. {$doctorName} demain {$date} à {$time}.",
            type: 'reminder',
            actionUrl: url("/appointments/{$appointment->id}/confirm"),
            actionText: 'Confirmer ma présence',
            data: ['doctor' => $doctorName, 'date' => $date, 'time' => $time, 'reminder_type' => '24h'],
            appointmentId: $appointment->id
        );
    }

    public static function paymentReminder(float $amount, string $dueDate, ?int $appointmentId = null): self
    {
        return new self(
            title: 'Rappel de paiement',
            message: "Vous avez un paiement de {$amount} € à effectuer avant le {$dueDate}.",
            type: 'warning',
            actionUrl: url("/payments"),
            actionText: 'Payer maintenant',
            data: ['amount' => $amount, 'due_date' => $dueDate],
            appointmentId: $appointmentId
        );
    }
}