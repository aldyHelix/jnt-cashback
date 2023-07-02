<x-ladmin-auth-layout>
    <x-slot name="title">Add New Collection Point</x-slot>

    <form action="{{ route('ladmin.ratesetting.grade.store') }}" method="POST">
        @csrf

        @include('ratesetting::_parts._form', ['data' => $data])

        <input type="hidden" name="grade" value="{{ $grade }}">

        <div class="text-end">
            <x-ladmin-button>Submit</x-ladmin-button>
        </div>

    </form>

</x-ladmin-auth-layout>
