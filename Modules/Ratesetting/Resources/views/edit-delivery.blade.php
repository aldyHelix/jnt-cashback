<x-ladmin-auth-layout>
    <x-slot name="title">Collection Point Details</x-slot>

    <form action="{{ route('ladmin.ratesetting.delivery.update', $data->id) }}" method="POST">
        @csrf
        @method('PUT')

        @include('ratesetting::_parts._form-delivery', ['data' => $data] )


        <input type="hidden" name="id" value="{{ $data->id }}">

        <div class="text-end">
            <x-ladmin-button>Update</x-ladmin-button>
        </div>

    </form>

</x-ladmin-auth-layout>
