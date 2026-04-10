<?php

namespace App\Notifications;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use InvalidArgumentException;

abstract class BaseAppointmentNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected const VALID_TYPES = ['info', 'success', 'warning', 'error', 'reminder', 'urgent', 'confirmation', 'cancellation'];
    
    protected const ICON_MAP = [
        'success' => '✅', 'error' => '❌', 'warning' => '⚠️', 'reminder' => '🔔',
        'urgent' => '🚨', 'confirmation' => '✓', 'cancellation' => '✗', 'info' => 'ℹ️',
    ];

    public function __construct(
        protected string $title,
        protected string $message,
        protected string $type = 'info',
        protected ?string $actionUrl = null,
        protected ?string $actionText = null,
        protected array $data = [],
        protected ?int $appointmentId = null,
    ) {
        $this->validateType();
    }

    protected function validateType(): void
    {
        if (!in_array($this->type, self::VALID_TYPES)) {
            throw new InvalidArgumentException(
                "Type de notification invalide: {$this->type}. Types valides: " . implode(', ', self::VALID_TYPES)
            );
        }
    }

    public function via($notifiable): array
    {
        $channels = ['database'];
        if ($this->shouldSendMail()) $channels[] = 'mail';
        if ($this->shouldBroadcast()) $channels[] = 'broadcast';
        return $channels;
    }

    protected function shouldSendMail(): bool
    {
        return in_array($this->type, ['urgent', 'reminder', 'confirmation', 'cancellation']);
    }

    protected function shouldBroadcast(): bool
    {
        return $this->type !== 'cancellation';
    }

    protected function getIcon(): string
    {
        return self::ICON_MAP[$this->type] ?? self::ICON_MAP['info'];
    }

    protected function getDoctorName(?Appointment $appointment): string
    {
        if (!$appointment) return 'Médecin';
        return optional(optional($appointment->doctor)->user)->name ?? 'Médecin';
    }

    protected function formatAppointmentDate(Appointment $appointment): string
    {
        return $appointment->date_time->format('d/m/Y');
    }

    protected function formatAppointmentTime(Appointment $appointment): string
    {
        return $appointment->date_time->format('H:i');
    }

    public function toArray($notifiable): array
    {
        return [
            'title' => $this->title,
            'message' => $this->message,
            'type' => $this->type,
            'icon' => $this->getIcon(),
            'action_url' => $this->actionUrl,
            'data' => $this->data,
            'appointment_id' => $this->appointmentId,
            'created_at' => now()->toISOString(),
        ];
    }

    abstract public function toMail($notifiable);
    abstract public function toDatabase($notifiable): array;
    abstract public function toBroadcast($notifiable): array;
}