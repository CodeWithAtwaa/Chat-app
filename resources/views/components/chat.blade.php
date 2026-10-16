<div class="relative mb-6 w-full">
    <flux:heading size="xl" level="1">{{ __('Chat') }}</flux:heading>
    <flux:subheading size="lg" class="mb-6">{{ __('Manage your profile and Chat') }}</flux:subheading>
    <flux:separator variant="subtle" />

    <!-- Main Container Panel -->
    <div class="flex h-[600px] text-sm border border-gray-200 rounded-xl shadow-xs overflow-hidden bg-white">

        <!-- Left Sidebar: Users List (w-1/4) -->
        <div class="w-1/4 border-r border-gray-200 bg-white flex flex-col">
            <div class="p-4 border-b border-gray-100">
                <span class="font-semibold text-gray-700">Users</span>
            </div>

            <div class="flex-1 overflow-y-auto p-2 space-y-1">
                <!-- Example Active/Selected User Item -->
                <div class="divide-y">
                    @foreach ($users as $user)
                        <div wire:click="selectUser({{ $user->id }})"
                            class="p-3  cursor-pointer hover:bg-blue-100 trasition {{ $selectedUser->id === $user->id ? 'bg-blue-50 font-semibold' : '' }}">
                            <div class="text-gray-800">{{ $user->name }}</div>
                            <div class="text-gray-500 text-xs">{{ $user->email }}</div>
                        </div>
                    @endforeach
                </div>



                <div id="typing-indicator" class="px-4 pb-1 text-xs text-gray-400 italic"></div>

            </div>
        </div>

        <!-- Right Section: Chat Window (w-3/4) -->
        <div class="w-3/4 flex flex-col bg-white">

            <!-- Chat Header -->
            <div class="p-4 border-b border-gray-200 bg-white flex flex-col justify-center">
                <h2 class="font-semibold text-gray-900 text-base">{{ $selectedUser->name }}</h2>
                <span class="text-xs text-gray-500">{{ $selectedUser->email }}</span>
            </div>

            <!-- Messages Window Pane -->
            <div x-data="{
                scrollToBottom() {
                    $el.scrollTo({ top: $el.scrollHeight, behavior: 'smooth' });
                }
            }" x-init="scrollToBottom();" @scroll-bottom.window="scrollToBottom()"
                class="flex-1 p-4 overflow-y-auto space-y-3 bg-gray-50/50 scrollbar-thin scrollbar-thumb-gray-300 scrollbar-track-transparent hover:scrollbar-thumb-gray-400 transition-colors duration-200"
                style="scrollbar-width: thin; scroll-behavior: smooth;">

                @foreach ($messages as $message)
                    <div
                        class="flex {{ $message->sender_id === auth()->id() ? 'justify-end' : 'justify-start' }} {{ $loop->last ? 'mb-16' : '' }}">
                        <div
                            class="max-w-xs px-4 py-2 rounded-2xl shadow-sm text-white {{ $message->sender_id === auth()->id() ? 'bg-blue-600 rounded-tr-none' : 'bg-gray-500 rounded-tl-none' }}">
                            {{ $message->message }}
                        </div>
                    </div>
                @endforeach

            </div>



            <!-- Input Form Field (Bottom Anchored) -->
            <form wire:submit="submit" action="#" method="POST"
                class="p-4 border-t border-gray-200 bg-white flex items-center gap-2">
                @csrf
                <input type="text" name="message" wire:model.live="newmessage"
                    class="flex-1 border border-gray-300 rounded-full px-4 py-2 text-sm text-black placeholder-gray-400 focus:outline-hidden focus:ring-1 focus:ring-blue-500 focus:border-blue-500 transition"
                    placeholder="Type your message..." required />
                <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-medium text-sm px-5 py-2 rounded-full cursor-pointer transition">
                    Send
                </button>
            </form>

        </div>

    </div>


</div>



<script>
    document.addEventListener('livewire:initialized', () => {
        Livewire.on('userTyping', (event) => {
            console.log(event);
            window.Echo.private(`chat.${event.SelectedUserId}`).whisper("typing", {
                userId: event.userId,
                userName: event.userName
            })
        });

        window.Echo.private(`chat.{{ $loginId }}`).listenForWhisper('typing', (event) => {
            var t = document.getElementById('typing-indicator');
            t.innerText = `${event.userName} Is Typing...`;

            setTimeout(() => {
                t.innerText = '';
            }, 2000);
        });
    });
</script>
