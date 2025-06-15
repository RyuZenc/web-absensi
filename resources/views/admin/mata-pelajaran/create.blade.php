<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tambah Mata Pelajaran Baru') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('admin.mata-pelajaran.store') }}" method="POST">
                        @csrf

                        <div class="mb-4">
                            <label for="nama_mapel" class="block text-sm font-medium text-gray-700">Nama Mata
                                Pelajaran</label>
                            <input type="text" name="nama_mapel" id="nama_mapel"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                                value="{{ old('nama_mapel') }}" required>
                            @error('nama_mapel')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('admin.mata-pelajaran.index') }}"
                                class="mr-4 px-4 py-2 text-gray-600 rounded-md border border-gray-300 hover:bg-gray-100">Batal</a>
                            <x-primary-button type="submit">Simpan Mata Pelajaran</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
