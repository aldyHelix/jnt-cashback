<x-ladmin-auth-layout>
    <x-slot name="title">Add New Kategori Klien Pengiriman</x-slot>

    <form action="{{ route('ladmin.category.store') }}" method="POST">
        @csrf

        @include('category::_parts._form', ['data' => $data])

        <div class="text-end">
            <x-ladmin-button>Submit</x-ladmin-button>
        </div>

    </form>

</x-ladmin-auth-layout>
