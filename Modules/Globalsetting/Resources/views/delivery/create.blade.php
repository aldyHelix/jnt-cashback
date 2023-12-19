<x-ladmin-auth-layout>
    <x-slot name="title">Buat Delivery Setting</x-slot>

    <form action="{{ route('ladmin.globalsetting.delivery.store') }}" method="POST">
        @csrf

        @include('globalsetting::delivery._partials._form', ['data' => $data, 'cp' => $cp])

        <div class="text-end">
            <x-ladmin-button>Submit</x-ladmin-button>
        </div>

    </form>

</x-ladmin-auth-layout>
