<x-ladmin-auth-layout>
    <x-slot name="title">Collection Point Details</x-slot>

    <form action="{{ route('ladmin.ratesetting.grade.update', $data->id) }}" method="POST">
        @csrf
        @method('PUT')

        @include('ratesetting::_parts._form', ['data' => $data] )


        <input type="hidden" name="id" value="{{ $data->id }}">
        <input type="hidden" name="grade" value="{{ $data->grading_type }}">

        <div class="text-end">
            <x-ladmin-button>Update</x-ladmin-button>
        </div>

    </form>

</x-ladmin-auth-layout>
