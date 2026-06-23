<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body class="font-sans bg-dashboard text-text-primary">
@stack('styles')
    <div class="flex min-h-screen">

        @include('layouts.sidebar')

        <div class="flex flex-col flex-1">

            @include('layouts.topbar')

            <main class="p-8">
                @yield('content')
            </main>

        </div>

    </div>
    {{-- AI Floating Button --}}
<button
    id="ai-chat-toggle"
    class="
        fixed
        bottom-6
        right-6
        w-16
        h-16
        rounded-full
        text-white
        shadow-xl
        z-50
        text-2xl
    "
    style="
        background:
        linear-gradient(
            99deg,
            #FF5700 9.65%,
            #FF894C 90.35%
        );
    ">

    🤖

</button>

    <div
        id="ai-popup"
        class="
            hidden
            fixed
            bottom-24
            right-6
            w-[420px]
            h-[600px]
            bg-white
            border
            border-border
            rounded-3xl
            shadow-2xl
            z-50
            overflow-hidden
            flex
            flex-col
        ">

        {{-- HEADER --}}
        <div
            class="
                h-16
                px-5
                flex
                items-center
                justify-between
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

            <div>

                <div class="font-semibold">
                    AI Assistant
                </div>

                <div class="text-xs opacity-80">
                    Plainthing Studio
                </div>

            </div>

            <button
                id="close-popup">

                ✕

            </button>

        </div>

        {{-- CHAT --}}
        <div
            id="popup-chat-container"
            class="
                flex-1
                overflow-y-auto
                p-4
                space-y-4
                bg-gray-50
            ">

            <div
                class="
                    bg-white
                    p-3
                    rounded-2xl
                    max-w-[85%]
                ">

                👋 Halo, saya AI Assistant.
                Ada yang bisa saya bantu?

            </div>

        </div>

        {{-- INPUT --}}
        <form
            id="popup-chat-form"
            class="
                p-4
                border-t
                border-border
            ">

            <div
                class="flex gap-2">

                <input
                    id="popup-user-input"
                    type="text"
                    placeholder="Ask something..."
                    class="
                        flex-1
                        h-11
                        border
                        border-border
                        rounded-xl
                        px-3
                    ">

                <button
                    type="submit"
                    class="
                        px-5
                        rounded-xl
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

    </div>
    <script>

    const popup =
        document.getElementById(
            'ai-popup'
        );

    document
    .getElementById(
        'ai-chat-toggle'
    )
    .addEventListener(
        'click',
        () =>
    {
        popup.classList.toggle(
            'hidden'
        );
    });

    document
    .getElementById(
        'close-popup'
    )
    .addEventListener(
        'click',
        () =>
    {
        popup.classList.add(
            'hidden'
        );
    });

    const popupForm =
        document.getElementById(
            'popup-chat-form'
        );

    popupForm.addEventListener(
        'submit',
        async function(e)
    {
        e.preventDefault();

        const input =
            document.getElementById(
                'popup-user-input'
            );

        const container =
            document.getElementById(
                'popup-chat-container'
            );

        const message =
            input.value;

        if(!message) return;

        appendPopupMessage(
            message,
            'user'
        );

        input.value = '';

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

        appendPopupMessage(
            data.message,
            'bot'
        );

    });

    function appendPopupMessage(
        text,
        side
    )
    {
        const container =
            document.getElementById(
                'popup-chat-container'
            );

        const div =
            document.createElement(
                'div'
            );

        if(side === 'user')
        {
            div.className =
                'ml-auto text-white p-3 rounded-2xl max-w-[80%]';

            div.style.background =
                'linear-gradient(99deg,#FF5700 9.65%,#FF894C 90.35%)';
        }
        else
        {
            div.className =
                'bg-white p-3 rounded-2xl max-w-[80%]';
        }

        div.innerHTML =
            text.replace(
                /\n/g,
                '<br>'
            );

        container.appendChild(
            div
        );

        container.scrollTop =
            container.scrollHeight;
    }

    </script>
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>

<script>
document.addEventListener(
    'DOMContentLoaded',
    function () {

        document
        .querySelectorAll('.tom-assignee')
        .forEach(select => {

            new TomSelect(
                select,
                {
                    plugins: [
                        'remove_button'
                    ],

                    create: false,

                    persist: false,

                    maxItems: null
                }
            );

        });

    }
);
</script>

@stack('scripts') 
</body>

</html>