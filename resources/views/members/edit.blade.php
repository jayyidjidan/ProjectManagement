@extends('layouts.app')

@section('content')

<div class="max-w-3xl mx-auto">

@if(session('success'))

<div
    class="mb-6 rounded-xl border border-green-200 bg-green-50 p-4 text-green-700">

```
{{ session('success') }}
```

</div>

@endif

@if($errors->any())

<div
    class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4">

```
<ul class="list-disc pl-5 text-red-600">

    @foreach($errors->all() as $error)

        <li>{{ $error }}</li>

    @endforeach

</ul>
```

</div>

@endif

<x-card>

```
<div class="flex items-center justify-between mb-6">

    <div>

        <h1 class="text-3xl font-bold">
            Edit Employee
        </h1>

        <p class="text-gray-500 mt-1">
            Update employee information
        </p>

    </div>

    <a
        href="{{ route('members.show',$member) }}">

        <x-button-secondary>
            Back
        </x-button-secondary>

    </a>

</div>

<form
    action="{{ route('members.update',$member) }}"
    method="POST"
    enctype="multipart/form-data">

    @csrf
    @method('PUT')

    <div class="space-y-5">

        <div>

            <label class="block mb-2 font-medium">
                Current Profile Photo
            </label>

            @if($member->profile_photo)

                <img
                    src="{{ asset('storage/'.$member->profile_photo) }}"
                    class="w-28 h-28 rounded-full object-cover border border-border">

            @else

                <img
                    src="https://ui-avatars.com/api/?name={{ urlencode($member->member_name) }}"
                    class="w-28 h-28 rounded-full border border-border">

            @endif

        </div>

        <div>

            <label class="block mb-2 font-medium">
                New Profile Photo
            </label>

            <input
                type="file"
                name="profile_photo"
                accept="image/*"
                class="w-full rounded-xl border border-border p-3">

        </div>

        <div>

            <label class="block mb-2 font-medium">
                Username
            </label>

            <input
                type="text"
                value="{{ $member->user?->username }}"
                readonly
                class="w-full rounded-xl border border-border bg-gray-50 p-3">

        </div>

        <div>

            <label class="block mb-2 font-medium">
                Email
            </label>

            <input
                type="email"
                value="{{ $member->user?->email }}"
                readonly
                class="w-full rounded-xl border border-border bg-gray-50 p-3">

        </div>

        <div>

            <label class="block mb-2 font-medium">
                Role
            </label>

            <input
                type="text"
                value="{{ $member->user?->role?->role_name }}"
                readonly
                class="w-full rounded-xl border border-border bg-gray-50 p-3">

        </div>

        <div>

            <label class="block mb-2 font-medium">
                Position
            </label>

            <select
                name="id_position"
                class="w-full rounded-xl border border-border p-3">

                @foreach($positions as $position)

                    <option
                        value="{{ $position->id_position }}"
                        @selected(
                            $member->id_position
                            ==
                            $position->id_position
                        )>

                        {{ $position->position_name }}

                    </option>

                @endforeach

            </select>

        </div>

        <div>

            <label class="block mb-2 font-medium">
                Full Name
            </label>

            <input
                type="text"
                name="member_name"
                value="{{ old('member_name',$member->member_name) }}"
                class="w-full rounded-xl border border-border p-3">

        </div>

        <div>

            <label class="block mb-2 font-medium">
                Skills
            </label>

            <div
                id="skill-badges"
                class="flex flex-wrap gap-2 mb-3">

            </div>

            <div
                id="skill-hidden-inputs">

            </div>

            <input
                type="text"
                id="skill-input"
                placeholder="Search skill or add new skill..."
                class="w-full rounded-xl border border-border p-3">

            <div
                id="skill-suggestions"
                class="hidden mt-2 rounded-xl border border-border bg-white shadow-sm overflow-hidden">

            </div>

        </div>

    </div>

    <div class="flex justify-end mt-8">

        <x-button-primary>
            Update Employee
        </x-button-primary>

    </div>

</form>
```

</x-card>

</div>

<script>

const skills =
@json(
    $skills->map(
        fn($skill) => [

            'id' =>
                $skill->id_skill,

            'name' =>
                $skill->skill_name

        ]
    )
);

const memberSkills =
@json(
    $member->skills->map(
        fn($skill) => [

            'id' =>
                $skill->id_skill,

            'name' =>
                $skill->skill_name

        ]
    )
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
    if(
        selectedSkills.includes(
            value
        )
    ){
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
        'flex items-center gap-2 px-3 py-1 rounded-full border border-border text-sm';

    badge.innerHTML =
        `
        <span>${label}</span>
        <button
            type="button"
            class="font-bold">
            ×
        </button>
        `;

    const hidden =
        document.createElement(
            'input'
        );

    hidden.type =
        'hidden';

    hidden.name =
        'skills[]';

    hidden.value =
        value;

    badge.querySelector(
        'button'
    ).onclick = () => {

        selectedSkills =
            selectedSkills.filter(
                s => s !== value
            );

        badge.remove();

        hidden.remove();
    };

    badgeContainer.appendChild(
        badge
    );

    hiddenContainer.appendChild(
        hidden
    );
}

memberSkills.forEach(
    skill =>
    addSkill(
        skill.id,
        skill.name
    )
);

input.addEventListener(
    'input',
    function()
    {
        const keyword =
            this.value
            .toLowerCase();

        suggestionBox.innerHTML =
            '';

        if(
            keyword.length < 1
        ){
            suggestionBox.classList.add(
                'hidden'
            );
            return;
        }

        skills
        .filter(
            skill =>
            skill.name
                .toLowerCase()
                .includes(
                    keyword
                )
        )
        .forEach(
            skill =>
            {
                const item =
                    document.createElement(
                        'div'
                    );

                item.className =
                    'px-4 py-2 hover:bg-gray-100 cursor-pointer';

                item.innerText =
                    skill.name;

                item.onclick =
                    () =>
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
        if(
            e.key === 'Enter'
        )
        {
            e.preventDefault();

            const value =
                this.value.trim();

            if(
                value === ''
            ){
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

@endsection
