protected function schedule(Schedule $schedule)
{
    // Rappels 24h avant
    $schedule->call(function () {
        $appointments = Appointment::where('date_time', '>=', now())
            ->where('date_time', '<=', now()->addDay())
            ->where('status', 'confirmed')
            ->where('reminder_sent', false)
            ->get();

        foreach ($appointments as $appointment) {
            $appointment->patient->user->notify(new AppointmentReminder($appointment, 'reminder'));
            $appointment->update(['reminder_sent' => true]);
        }
    })->dailyAt('08:00');
}