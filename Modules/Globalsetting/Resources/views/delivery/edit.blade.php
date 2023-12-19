<x-ladmin-auth-layout>
    <x-slot name="title">Delivery Setting Detail</x-slot>

    <form action="{{ route('ladmin.globalsetting.delivery.update', $data->id) }}" method="POST">
        @csrf
        @method('PUT')

        @include('globalsetting::delivery._partials._form', ['data' => $data, 'cp' => $cp] )

        <div class="text-end">
            <x-ladmin-button>Update</x-ladmin-button>
        </div>

    </form>

</x-ladmin-auth-layout>
