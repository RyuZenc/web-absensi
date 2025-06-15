<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Guru') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('admin.guru.update', $guru->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <h3 class="font-semibold text-md mb-3 text-gray-700">Data Guru</h3>
                        <div class="mb-4">
                            <label for="nip" class="block text-sm font-medium text-gray-700">NIP (Opsional)</label>
                            <input type="text" name="nip" id="nip"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                                value="{{ old('nip', $guru->nip) }}">
                            @error('nip')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-6">
                            <label for="nama_lengkap" class="block text-sm font-medium text-gray-700">Nama
                                Lengkap</label>
                            <input type="text" name="nama_lengkap" id="nama_lengkap"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                                value="{{ old('nama_lengkap', $guru->nama_lengkap) }}" required>
                            @error('nama_lengkap')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <h3 class="font-semibold text-md mb-3 text-gray-700">Data Akun Login Guru</h3>
                        <div class="mb-4">
                            <label for="email" class="block text-sm font-medium text-gray-700">Email Akun
                                Login</label>
                            <input type="email" name="email" id="email"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                                value="{{ old('email', $guru->user->email ?? '') }}" required>
                            @error('email')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="password" class="block text-sm font-medium text-gray-700">Password Baru
                                (Opsional)</label>
                            <input type="password" name="password" id="password"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            <p class="text-gray-500 text-xs mt-1">Kosongkan jika tidak ingin mengubah password.</p>
                            @error('password')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="password_confirmation"
                                class="block text-sm font-medium text-gray-700">Konfirmasi Password Baru</label>
                            <input type="password" name="password_confirmation" id="password_confirmation"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            @error('password_confirmation')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('admin.guru.index') }}"
                                class="mr-4 px-4 py-2 text-gray-600 rounded-md border border-gray-300 hover:bg-gray-100">Batal</a>
                            <x-primary-button type="submit">Update Guru</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
