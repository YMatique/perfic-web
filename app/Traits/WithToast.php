<?php
// app/Traits/WithToast.php
namespace App\Traits;

trait WithToast
{
    public function toast($title, $message = '', $type = 'info', $duration = 5000)
    {
        $this->dispatch('toast', [
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'duration' => $duration
        ]);
    }

    public function toastSuccess($title, $message = '')
    {
        $this->toast($title, $message, 'success');
    }

    public function toastError($title, $message = '')
    {
        $this->toast($title, $message, 'error');
    }

    public function toastWarning($title, $message = '')
    {
        $this->toast($title, $message, 'warning');
    }
}