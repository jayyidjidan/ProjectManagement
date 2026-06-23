@extends('layouts.app')

@section('content')

<div class="space-y-6">

    {{-- HEADER --}}
    <div class="flex items-center justify-between">

        <div>

            <h1 class="text-4xl font-bold">
                AI Assistant
            </h1>

            <p class="text-gray-500 mt-2">
                Ask anything about projects, tasks, attendance and team activities.
            </p>

        </div>

        <button
            id="new-chat"
            class="h-12 px-6 rounded-2xl text-white"
            style="
                background:
                linear-gradient(
                    99deg,
                    #FF5700 9.65%,
                    #FF894C 90.35%
                );
            ">

            New Chat

        </button>

    </div>

    {{-- CHAT --}}
    <x-card>

        <div
            id="chat-container"
            class="
                h-[600px]
                overflow-y-auto
                flex
                flex-col
                gap-4
                p-2
            ">

            <div
                class="
                    max-w-[75%]
                    rounded-2xl
                    px-4
                    py-3
                    bg-gray-100
                ">

                👋 Welcome! I am Manager AI.
                Ask me anything about your projects and tasks.

            </div>

        </div>

        <form
            id="chat-form"
            class="mt-6">

            <div class="flex gap-3">

                <input
                    type="text"
                    id="user-input"
                    placeholder="Ask about tasks, projects, attendance..."
                    class="
                        flex-1
                        h-12
                        rounded-2xl
                        border
                        border-border
                        px-4
                    "
                    required>

                <button
                    type="submit"
                    id="send-btn"
                    class="
                        h-12
                        px-6
                        rounded-2xl
                        text-white
                    "
                    style="
                        background:
                        linear-gradient(
                            99deg,
                            #FF5700 9.65%,
                            #FF894C 90.35%
                        );
                    ">

                    Send

                </button>

            </div>

        </form>

    </x-card>

</div>

<script>

const chatForm =
    document.getElementById('chat-form');

const userInput =
    document.getElementById('user-input');

const chatContainer =
    document.getElementById('chat-container');

const sendBtn =
    document.getElementById('send-btn');

chatForm.addEventListener(
    'submit',
    async (e) =>
{
    e.preventDefault();

    const message =
        userInput.value;

    addMessage(
        message,
        'user'
    );

    userInput.value = '';

    sendBtn.disabled = true;

    try {

        const response =
            await fetch(
                '{{ route("chat.send") }}',
                {
                    method:'POST',
                    headers:{
                        'Content-Type':
                        'application/json',

                        'X-CSRF-TOKEN':
                        '{{ csrf_token() }}'
                    },
                    body:JSON.stringify({
                        message
                    })
                }
            );

        const data =
            await response.json();

        if (
            data.status
            ===
            'success'
        ) {

            addMessage(
                data.message,
                'bot'
            );

        } else {

            addMessage(
                'Something went wrong.',
                'bot'
            );

        }

    } catch {

        addMessage(
            'Failed to connect server.',
            'bot'
        );

    } finally {

        sendBtn.disabled = false;

    }

});

function addMessage(
    text,
    side
) {

    const div =
        document.createElement(
            'div'
        );

    div.className =
        side === 'user'
        ?
        'ml-auto max-w-[75%] rounded-2xl px-4 py-3 text-white'
        :
        'max-w-[75%] rounded-2xl px-4 py-3 bg-gray-100';

    if (
        side === 'user'
    ) {

        div.style.background =
            'linear-gradient(99deg,#FF5700 9.65%,#FF894C 90.35%)';

    }

    div.innerHTML =
        text.replace(
            /\n/g,
            '<br>'
        );

    chatContainer.appendChild(
        div
    );

    chatContainer.scrollTop =
        chatContainer.scrollHeight;
}

document
.getElementById('new-chat')
.addEventListener(
    'click',
    async function()
    {
        await fetch(
            '{{ route("chat.reset") }}',
            {
                method:'POST',
                headers:{
                    'X-CSRF-TOKEN':
                    '{{ csrf_token() }}'
                }
            }
        );

        location.reload();
    }
);

</script>

@endsection