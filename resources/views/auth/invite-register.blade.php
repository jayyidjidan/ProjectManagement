<!DOCTYPE html>
<html>

<head>

    <title>
        Complete Registration
    </title>

    @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])

</head>

<body
    class="min-h-screen bg-background flex items-center justify-center py-10">

<div
    class="w-full max-w-3xl px-4">

<div class="max-w-3xl mx-auto">

<x-card>

    <h1 class="text-3xl font-bold mb-2">
        Complete Registration
    </h1>

    <p class="text-gray-500 mb-6">
        Finish your employee registration
    </p>

    @if ($errors->any())

    <div
        class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4">

        <ul class="list-disc pl-5 text-red-600">

            @foreach ($errors->all() as $error)

                <li>{{ $error }}</li>

            @endforeach

        </ul>

    </div>

    @endif

    <form
        action="{{ route('invite.register.store',$invitation->token) }}"
        method="POST"
        enctype="multipart/form-data">

        @csrf

        <div class="space-y-5">

            {{-- EMAIL --}}
            <div>

                <label class="block mb-2 font-medium">
                    Email
                </label>

                <input
                    type="email"
                    value="{{ $invitation->email }}"
                    readonly
                    class="w-full rounded-xl border border-border p-3 bg-gray-50">

            </div>

            {{-- USERNAME --}}
            <div>

                <label class="block mb-2 font-medium">
                    Username
                </label>

                <input
                    type="text"
                    name="username"
                    value="{{ old('username') }}"
                    class="w-full rounded-xl border border-border p-3"
                    required>

            </div>

            {{-- PHOTO --}}
            <div>

                <label class="block mb-2 font-medium">
                    Profile Photo
                </label>

                <input
                    type="file"
                    name="profile_photo"
                    accept="image/*"
                    class="w-full rounded-xl border border-border p-3">

            </div>

            {{-- POSITION --}}
            <div>

                <label class="block mb-2 font-medium">
                    Position
                </label>

                <select
                    name="id_position"
                    class="w-full rounded-xl border border-border p-3"
                    required>

                    <option value="">
                        Select Position
                    </option>

                    @foreach($positions as $position)

                        <option
                            value="{{ $position->id_position }}"
                            @selected(old('id_position') == $position->id_position)>

                            {{ $position->position_name }}

                        </option>

                    @endforeach

                </select>

            </div>

            {{-- SKILLS --}}
            <div>

                <label class="block mb-2 font-medium">
                    Skills
                </label>

                {{-- Selected Skills --}}
                <div
                    id="skill-badges"
                    class="flex flex-wrap gap-2 mb-3 min-h-[40px]">

                </div>

                {{-- Hidden Inputs --}}
                <div
                    id="skill-hidden-inputs">

                </div>

                {{-- Search --}}
                <input
                    type="text"
                    id="skill-input"
                    placeholder="Search skill or type new skill then press Enter..."
                    class="w-full rounded-xl border border-border p-3">

                {{-- Suggestions --}}
                <div
                    id="skill-suggestions"
                    class="hidden mt-2 rounded-xl border border-border bg-white shadow-sm overflow-hidden">

                </div>

            </div>

            {{-- FULL NAME --}}
            <div>

                <label class="block mb-2 font-medium">
                    Full Name
                </label>

                <input
                    type="text"
                    name="member_name"
                    value="{{ old('member_name') }}"
                    class="w-full rounded-xl border border-border p-3"
                    required>

            </div>

            {{-- PASSWORD --}}
            <div>

                <label class="block mb-2 font-medium">
                    Password
                </label>

                <input
                    type="password"
                    name="password"
                    class="w-full rounded-xl border border-border p-3"
                    required>

            </div>

            {{-- CONFIRM PASSWORD --}}
            <div>

                <label class="block mb-2 font-medium">
                    Confirm Password
                </label>

                <input
                    type="password"
                    name="password_confirmation"
                    class="w-full rounded-xl border border-border p-3"
                    required>

            </div>

        </div>

        <div class="flex justify-end mt-8">

            <x-button-primary>
                Register
            </x-button-primary>

        </div>

    </form>

</x-card>

</div>

<script>

const skills = @json(
    $skills->map(function($skill){

        return [

            'id' => $skill->id_skill,

            'name' => $skill->skill_name

        ];

    })
);

const input =
    document.getElementById(
        'skill-input'
    );

const suggestionBox =
    document.getElementById(
        'skill-suggestions'
    );

const badgeContainer =
    document.getElementById(
        'skill-badges'
    );

const hiddenContainer =
    document.getElementById(
        'skill-hidden-inputs'
    );

let selectedSkills = [];

function addSkill(
    value,
    label
)
{
    if (
        selectedSkills.includes(
            value
        )
    ) {
        return;
    }

    selectedSkills.push(
        value
    );

    const badge =
        document.createElement(
            'div'
        );

    badge.className =
        'flex items-center gap-2 px-3 py-1 rounded-full bg-blue-100 text-blue-700 text-sm';

    badge.innerHTML =
        `
        <span>${label}</span>

        <button
            type="button"
            class="font-bold">

            ×

        </button>
        `;

    const hiddenInput =
        document.createElement(
            'input'
        );

    hiddenInput.type =
        'hidden';

    hiddenInput.name =
        'skills[]';

    hiddenInput.value =
        value;

    badge.querySelector(
        'button'
    ).addEventListener(
        'click',
        function()
        {
            badge.remove();

            hiddenInput.remove();

            selectedSkills =
                selectedSkills.filter(
                    skill =>
                        skill !== value
                );
        }
    );

    badgeContainer.appendChild(
        badge
    );

    hiddenContainer.appendChild(
        hiddenInput
    );
}

input.addEventListener(
    'input',
    function()
    {
        const keyword =
            this.value
                .toLowerCase();

        suggestionBox.innerHTML =
            '';

        if (
            keyword.length < 1
        ) {

            suggestionBox.classList.add(
                'hidden'
            );

            return;
        }

        const matches =
            skills.filter(
                skill =>
                    skill.name
                        .toLowerCase()
                        .includes(
                            keyword
                        )
            );

        matches.forEach(
            skill =>
            {
                const item =
                    document.createElement(
                        'div'
                    );

                item.className =
                    'px-4 py-2 hover:bg-gray-100 cursor-pointer';

                item.textContent =
                    skill.name;

                item.onclick =
                    function()
                    {
                        addSkill(
                            skill.id,
                            skill.name
                        );

                        input.value =
                            '';

                        suggestionBox.classList.add(
                            'hidden'
                        );
                    };

                suggestionBox.appendChild(
                    item
                );
            }
        );

        suggestionBox.classList.remove(
            'hidden'
        );
    }
);

input.addEventListener(
    'keydown',
    function(e)
    {
        if (
            e.key === 'Enter'
        ) {

            e.preventDefault();

            const value =
                this.value.trim();

            if (
                value === ''
            ) {
                return;
            }

            addSkill(
                value,
                value
            );

            this.value =
                '';

            suggestionBox.classList.add(
                'hidden'
            );
        }
    }
);

</script>

</div>

</body>

</html>