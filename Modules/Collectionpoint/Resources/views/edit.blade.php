<x-ladmin-auth-layout>
    <x-slot name="title">Collection Point Details</x-slot>

    <form action="{{ route('ladmin.collectionpoint.update', $data->id) }}" method="POST">
        @csrf
        @method('PUT')

        @include('collectionpoint::_parts._form', ['data' => $data] )


        <input type="hidden" name="id" value="{{ $data->id }}">

        <div class="text-end">
            <x-ladmin-button>Update</x-ladmin-button>
        </div>

    </form>

</x-ladmin-auth-layout>
