@extends('layouts.app')

@section('content')

@if(session('success'))

<div
    class="mb-6 p-4 rounded-2xl border border-green-200 bg-green-50 text-green-700">

    {{ session('success') }}

</div>

@endif

<div class="flex items-end justify-between mb-6">

    <div>

        <h1 class="text-4xl font-bold">
            Work Hours Settings
        </h1>

        <p class="text-gray-500 mt-2">
            Configure check in and check out schedule
        </p>

    </div>

</div>

<form
    action="{{ route('work-settings.update') }}"
    method="POST">

    @csrf
    @method('PUT')

    <div class="grid lg:grid-cols-2 gap-6">

        <x-card>

            <h2
                class="text-xl font-semibold mb-6">

                Check In Settings

            </h2>

            <div class="space-y-5">

                <div>

                    <label
                        class="block mb-2 font-medium">

                        Start Time

                    </label>

                    <input
                        type="time"
                        name="checkin_start"
                        value="{{ substr($setting->checkin_start,0,5) }}"
                        class="w-full rounded-xl border border-border p-3">

                </div>

                <div>

                    <label
                        class="block mb-2 font-medium">

                        End Time

                    </label>

                    <input
                        type="time"
                        name="checkin_end"
                        value="{{ substr($setting->checkin_end,0,5) }}"
                        class="w-full rounded-xl border border-border p-3">

                </div>

            </div>

        </x-card>

        <x-card>

            <h2
                class="text-xl font-semibold mb-6">

                Check Out Settings

            </h2>

            <div class="space-y-5">

                <div>

                    <label
                        class="block mb-2 font-medium">

                        Start Time

                    </label>

                    <input
                        type="time"
                        name="checkout_start"
                        value="{{ substr($setting->checkout_start,0,5) }}"
                        class="w-full rounded-xl border border-border p-3">

                </div>

                <div>

                    <label
                        class="block mb-2 font-medium">

                        End Time

                    </label>

                    <input
                        type="time"
                        name="checkout_end"
                        value="{{ substr($setting->checkout_end,0,5) }}"
                        class="w-full rounded-xl border border-border p-3">

                </div>

            </div>

        </x-card>

    </div>

    <div
        class="flex justify-end mt-6">

        <x-button-primary>

            Save Settings

        </x-button-primary>

    </div>

</form>

@endsection