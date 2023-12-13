<x-ladmin-auth-layout>
    <x-slot name="title">Buat Setting Zonasi</x-slot>

    <form action="{{ route('ladmin.ratesetting.grade.store') }}" method="POST">
        @csrf

        @include('globalSetting::_parts._form', ['data' => $data])

        <div class="text-end">
            <x-ladmin-button>Submit</x-ladmin-button>
        </div>

    </form>

</x-ladmin-auth-layout>
