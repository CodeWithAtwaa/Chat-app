<?php

use App\Events\MessageSendEvent;
use App\Livewire\Chat;
use App\Models\ChatMessage;
use App\Models\User;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

it('broadcasts message details on the recipient private channel', function () {
    $message = new ChatMessage([
        'sender_id' => 1,
        'receiver_id' => 2,
        'message' => 'Hello',
    ]);
    $message->id = 7;

    $event = new MessageSendEvent($message);

    expect($event->broadcastOn()[0])
        ->toBeInstanceOf(PrivateChannel::class)
        ->and($event->broadcastWith())
        ->toBe([
            'id' => 7,
            'sender_id' => 1,
            'receiver_id' => 2,
            'message' => 'Hello',
        ]);
});

it('displays an incoming broadcast message in the open conversation', function () {
    $recipient = User::factory()->create();
    $sender = User::factory()->create();

    $this->actingAs($recipient);

    $chat = Livewire::test(Chat::class);
    $message = ChatMessage::create([
        'sender_id' => $sender->id,
        'receiver_id' => $recipient->id,
        'message' => 'Incoming message',
    ]);

    $chat
        ->call('newChatMessageNotifications', [
            'id' => $message->id,
            'sender_id' => $sender->id,
            'receiver_id' => $recipient->id,
            'message' => $message->message,
        ])
        ->assertSee('Incoming message');
});
