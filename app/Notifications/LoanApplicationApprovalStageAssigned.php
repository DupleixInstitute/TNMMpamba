<?php

namespace App\Notifications;

use App\Models\LoanApplicationLinkedApprovalStage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LoanApplicationApprovalStageAssigned extends Notification implements ShouldQueue
{
    use Queueable;

    public LoanApplicationLinkedApprovalStage $linkedStage;

    /**
     * Create a new notification instance.
     */
    public function __construct(LoanApplicationLinkedApprovalStage $linkedStage)
    {
        $this->linkedStage = $linkedStage;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Loan Application Approval Assigned')
            ->line('You have been assigned a loan application to approve.')
            ->line('Stage:' . $this->linkedStage->stage->name)
            ->action('View Loan Application', route('loan_applications.show', $this->linkedStage->loan_application_id))
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
