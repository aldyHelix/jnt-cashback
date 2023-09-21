<x-ladmin-auth-layout>
    <x-slot name="title">Kategory Klien Pengiriman Details</x-slot>

    <form action="{{ route('ladmin.category.update', $data->id) }}" method="POST">
        @csrf
        @method('PUT')

        @include('category::_parts._form', ['data' => $data] )


        <input type="hidden" name="id" value="{{ $data->id }}">

        <div class="text-end">
            <x-ladmin-button>Update</x-ladmin-button>
        </div>

    </form>

</x-ladmin-auth-layout>
