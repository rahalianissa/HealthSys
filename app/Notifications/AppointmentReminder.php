<?php

namespace App\Notifications;

use App\Models\Appointment;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use InvalidArgumentException;
use RuntimeException;

class AppointmentReminder extends BaseAppointmentNotification
{
    private const REMINDER_TYPES = ['reminder', 'confirmation', 'cancellation'];

    private array $messages = [
        'reminder' => [
            'subject' => 'Rappel de rendez-vous - HealthSys',
            'greeting_prefix' => 'Rappel',
            'body' => 'Vous avez un rendez-vous prévu demain :',
            'button_text' => 'Confirmer ma présence',
            'database_title' => 'Rappel de rendez-vous',
            'database_message' => 'Rappel de rendez-vous demain',
        ],
        'confirmation' => [
            'subject' => 'Confirmation de rendez-vous - HealthSys',
            'greeting_prefix' => 'Confirmation',
            'body' => 'Votre rendez-vous a été confirmé.',
            'button_text' => 'Voir le rendez-vous',
            'database_title' => 'Rendez-vous confirmé',
            'database_message' => 'Votre rendez-vous a été confirmé',
        ],
        'cancellation' => [
            'subject' => 'Annulation de rendez-vous - HealthSys',
            'greeting_prefix' => 'Annulation',
            'body' => 'Votre rendez-vous a été annulé.',
            'button_text' => 'Prendre un nouveau rendez-vous',
            'database_title' => 'Rendez-vous annulé',
            'database_message' => 'Votre rendez-vous a été annulé',
        ],
    ];

    private Appointment $appointment;
    private string $reminderType;

    public function __construct(Appointment $appointment, string $type = 'reminder')
    {
        if (!in_array($type, self::REMINDER_TYPES)) {
            throw new InvalidArgumentException("Type de notification invalide: {$type}");
        }

        if (!$appointment->doctor || !$appointment->doctor->user) {
            throw new RuntimeException('Le rendez-vous n\'a pas de médecin associé');
        }

        $this->appointment = $appointment;
        $this->reminderType = $type;
        $notificationData = $this->buildNotificationData();

        parent::__construct(
            title: $notificationData['title'],
            message: $notificationData['message'],
            type: $type,
            actionUrl: $notificationData['action_url'],
            actionText: $notificationData['action_text'],
            data: $notificationData['data'],
            appointmentId: $appointment->id
        );
    }

    private function buildNotificationData(): array
    {
        $doctorName = $this->getDoctorName($this->appointment);
        $date = $this->formatAppointmentDate($this->appointment);
        $time = $this->formatAppointmentTime($this->appointment);
        $specialty = optional($this->appointment->doctor)->specialty ?? 'Généraliste';
        $location = $this->getAppointmentLocation();

        return match ($this->reminderType) {
            'reminder' => [
                'title' => 'Rappel de rendez-vous',
                'message' => "Rappel : Vous avez un rendez-vous avec Dr. {$doctorName} demain {$date} à {$time}.",
                'action_url' => url("/appointments/{$this->appointment->id}/confirm"),
                'action_text' => 'Confirmer ma présence',
                'data' => ['doctor' => $doctorName, 'specialty' => $specialty, 'date' => $date, 'time' => $time, 'location' => $location, 'reminder_type' => '24h']
            ],
            'confirmation' => [
                'title' => 'Rendez-vous confirmé',
                'message' => "Votre rendez-vous avec Dr. {$doctorName} a été confirmé pour le {$date} à {$time}.",
                'action_url' => url("/appointments/{$this->appointment->id}"),
                'action_text' => 'Voir le rendez-vous',
                'data' => ['doctor' => $doctorName, 'specialty' => $specialty, 'date' => $date, 'time' => $time, 'location' => $location]
            ],
            'cancellation' => [
                'title' => 'Rendez-vous annulé',
                'message' => "Votre rendez-vous avec Dr. {$doctorName} du {$date} à {$time} a été annulé.",
                'action_url' => url("/patient/appointments"),
                'action_text' => 'Prendre un nouveau rendez-vous',
                'data' => ['doctor' => $doctorName, 'specialty' => $specialty, 'date' => $date, 'time' => $time, 'location' => $location]
            ],
        };
    }

    public function via($notifiable): array
    {
        $channels = ['mail', 'database'];
        if ($this->shouldBroadcast()) $channels[] = 'broadcast';
        if ($this->shouldSendSms($notifiable)) $channels[] = 'vonage';
        return $channels;
    }

    public function toMail($notifiable): MailMessage
    {
        $message = (new MailMessage)
            ->subject($this->getEmailSubject())
            ->greeting($this->getEmailGreeting($notifiable))
            ->line($this->getEmailBody())
            ->line($this->getAppointmentDetails())
            ->action($this->getButtonText(), $this->actionUrl);

        if ($this->reminderType === 'confirmation') {
            $message->line('Merci de votre confiance !');
        }
        $message->salutation($this->getSalutation());
        return $message;
    }

    public function toDatabase($notifiable): array
    {
        return [
            'id' => (string) \Illuminate\Support\Str::uuid(),
            'appointment_id' => $this->appointment->id,
            'doctor_id' => $this->appointment->doctor_id,
            'doctor_name' => $this->getDoctorName($this->appointment),
            'patient_name' => $notifiable->name ?? 'Patient',
            'date_time' => $this->appointment->date_time->toIso8601String(),
            'date_formatted' => $this->formatAppointmentDate($this->appointment),
            'time_formatted' => $this->formatAppointmentTime($this->appointment),
            'location' => $this->getAppointmentLocation(),
            'type' => $this->type,
            'title' => $this->getDatabaseTitle(),
            'message' => $this->getDatabaseMessage(),
            'action_url' => $this->actionUrl,
            'action_text' => $this->getButtonText(),
            'data' => $this->data,
            'read_at' => null,
            'created_at' => now()->toIso8601String(),
        ];
    }

    public function toBroadcast($notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'appointment_id' => $this->appointment->id,
            'type' => $this->type,
            'title' => $this->getDatabaseTitle(),
            'message' => $this->getDatabaseMessage(),
            'doctor_name' => $this->getDoctorName($this->appointment),
            'date_time' => $this->formatAppointmentDate($this->appointment) . ' ' . $this->formatAppointmentTime($this->appointment),
            'action_url' => $this->actionUrl,
            'time_ago' => $this->appointment->date_time->diffForHumans(),
        ]);
    }

    protected function shouldBroadcast(): bool
    {
        return in_array($this->reminderType, ['confirmation', 'cancellation']);
    }

    private function shouldSendSms($notifiable): bool
    {
        return $this->reminderType === 'reminder' && !empty($notifiable->phone) && config('notifications.sms_enabled', false);
    }

    private function getAppointmentLocation(): string
    {
        $doctor = $this->appointment->doctor;
        if ($doctor && $doctor->address) return $doctor->address;
        if ($doctor && $doctor->user && $doctor->user->address) return $doctor->user->address;
        return config('app.default_clinic_address', 'Centre médical principal');
    }

    private function getAppointmentDetails(): string
    {
        return sprintf(
            "📅 Date: %s\n🕐 Heure: %s\n👨‍⚕️ Médecin: Dr. %s\n💊 Spécialité: %s\n📍 Lieu: %s",
            $this->formatAppointmentDate($this->appointment),
            $this->formatAppointmentTime($this->appointment),
            $this->getDoctorName($this->appointment),
            optional($this->appointment->doctor)->specialty ?? 'Généraliste',
            $this->getAppointmentLocation()
        );
    }

    private function getEmailSubject(): string
    {
        return $this->messages[$this->reminderType]['subject'];
    }

    private function getEmailGreeting($notifiable): string
    {
        $name = $notifiable->name ?? 'Cher patient';
        return "Bonjour {$name},";
    }

    private function getEmailBody(): string
    {
        return $this->messages[$this->reminderType]['body'];
    }

    private function getButtonText(): string
    {
        return $this->messages[$this->reminderType]['button_text'];
    }

    private function getSalutation(): string
    {
        return "Cordialement,\n" . config('app.name', 'HealthSys') . " Team";
    }

    private function getDatabaseTitle(): string
    {
        return $this->messages[$this->reminderType]['database_title'];
    }

    private function getDatabaseMessage(): string
    {
        return $this->messages[$this->reminderType]['database_message'];
    }
}