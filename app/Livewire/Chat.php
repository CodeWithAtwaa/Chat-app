<?php

namespace App\Livewire;

use App\Events\MessageSendEvent;
use App\Models\ChatMessage;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Chat extends Component
{
    public $users;

    public $selectedUser;

    public $newmessage;

    public $loginId;

    public $messages = [];

    public function mount()
    {
        $authId = Auth::id();
        $this->users = User::where('id', '!=', $authId)
            ->addSelect([
                'last_message_text' => ChatMessage::select('message')
                    ->where(function ($q) use ($authId) {
                        $q->where('sender_id', $authId)->whereColumn('receiver_id', 'users.id');
                    })
                    ->orWhere(function ($q) use ($authId) {
                        $q->whereColumn('sender_id', 'users.id')->where('receiver_id', $authId);
                    })
                    ->latest()
                    ->limit(1),
                'last_message_time' => ChatMessage::select('created_at')
                    ->where(function ($q) use ($authId) {
                        $q->where('sender_id', $authId)->whereColumn('receiver_id', 'users.id');
                    })
                    ->orWhere(function ($q) use ($authId) {
                        $q->whereColumn('sender_id', 'users.id')->where('receiver_id', $authId);
                    })
                    ->latest()
                    ->limit(1),
            ])
            ->orderByRaw('last_message_time IS NULL ASC, last_message_time DESC')
            ->get();

        $this->selectedUser = $this->users->first();

        if ($this->selectedUser) {
            $this->loadMessages();
        }

        $this->loginId = Auth::id();
    }

    public function selectUser($id)
    {
        $this->selectedUser = User::find($id);
        $this->loadMessages();
    }

    public function submit()
    {
        if (! $this->newmessage) {
            return;
        }

        $message = ChatMessage::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $this->selectedUser->id,
            'message' => $this->newmessage,
        ]);

        $this->newmessage = '';

        $this->loadMessages();
        $this->dispatch('scroll-bottom');
        broadcast(new MessageSendEvent($message));
    }

    public function getListeners()
    {
        return [
            "echo-private:chat.{$this->loginId},MessageSendEvent" => 'newChatMessageNotifications',
        ];
    }

    public function newChatMessageNotifications($event)
    {
        if (is_string($event)) {
            $event = json_decode($event, true);
        }

        if (! is_array($event) || ! isset($event['sender_id'], $event['id'])) {
            return;
        }

        if ($this->selectedUser && (int) $event['sender_id'] === (int) $this->selectedUser->id) {
            $message = ChatMessage::find($event['id']);

            if ($message) {
                $this->messages[] = $message;
                $this->dispatch('scroll-bottom');
            }
        }
    }

    /**
     * Helper method to fetch the correct chat history
     */
    protected function loadMessages()
    {
        if (! $this->selectedUser) {
            return;
        }

        $this->messages = ChatMessage::query()
            ->where(function ($q) {
                $q->where('sender_id', Auth::id())
                    ->where('receiver_id', $this->selectedUser->id);
            })
            ->orWhere(function ($q) {
                $q->where('sender_id', $this->selectedUser->id)
                    ->where('receiver_id', Auth::id());
            })
            ->oldest()
            ->get()
            ->all();
    }

    public function updatedNewMessage($val)
    {
        if (! $this->selectedUser) {
            return;
        }

        $this->dispatch('userTyping', userId: $this->loginId, userName: Auth::user()->name, SelectedUserId: $this->selectedUser->id);
    }

    public function render()
    {
        return view('components.chat');
    }
}
