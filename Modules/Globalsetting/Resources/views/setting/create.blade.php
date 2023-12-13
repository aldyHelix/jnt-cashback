<x-ladmin-auth-layout>
    <x-slot name="title">Buat General Setting</x-slot>

    <form action="{{ route('ladmin.globalsetting.setting.store') }}" method="POST">
        @csrf

        @include('globalsetting::setting._partials._form', ['data' => $data])

        <div class="text-end">
            <x-ladmin-button>Submit</x-ladmin-button>
        </div>

    </form>

</x-ladmin-auth-layout>
