<x-ladmin-auth-layout>
    <x-slot name="title">Add New Collection Point</x-slot>

    <form action="{{ route('ladmin.collectionpoint.store') }}" method="POST">
        @csrf

        @include('collectionpoint::_parts._form', ['data' => $data])

        <div class="text-end">
            <x-ladmin-button>Submit</x-ladmin-button>
        </div>

    </form>

</x-ladmin-auth-layout>
