<x-ladmin-auth-layout>
    <x-slot name="title">Add New Collection Point</x-slot>

    <form action="{{ route('ladmin.ratesetting.delivery.store') }}" method="POST">
        @csrf

        @include('ratesetting::_parts._form-delivery', ['data' => $data])

        <div class="text-end">
            <x-ladmin-button>Submit</x-ladmin-button>
        </div>

    </form>

</x-ladmin-auth-layout>
