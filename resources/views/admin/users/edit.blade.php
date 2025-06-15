<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit User') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <label for="name" class="block text-sm font-medium text-gray-700">Nama User</label>
                            <input type="text" name="name" id="name"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                                value="{{ old('name', $user->name) }}" required>
                            @error('name')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                            <input type="email" name="email" id="email"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                                value="{{ old('email', $user->email) }}" required>
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

                        <div class="mb-4">
                            <label for="role" class="block text-sm font-medium text-gray-700">Role</label>
                            <select name="role" id="role"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required
                                onchange="toggleRelatedId()">
                                <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Admin
                                </option>
                                <option value="guru" {{ old('role', $user->role) == 'guru' ? 'selected' : '' }}>Guru
                                </option>
                                {{-- Siswa tidak bisa diedit di sini, karena terkait data siswa. Buat modul terpisah jika perlu --}}
                            </select>
                            @error('role')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div id="related_id_field"
                            class="mb-4 {{ old('role', $user->role) == 'guru' ? '' : 'hidden' }}">
                            <label for="related_id" class="block text-sm font-medium text-gray-700">Pilih Guru
                                Terkait</label>
                            <select name="related_id" id="related_id"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                <option value="">Tidak ada (untuk Admin)</option>
                                @foreach ($gurus as $guru)
                                    <option value="{{ $guru->id }}"
                                        {{ old('related_id', $user->related_id) == $guru->id ? 'selected' : '' }}>
                                        {{ $guru->nama_lengkap }} ({{ $guru->nip ?? '-' }})</option>
                                @endforeach
                            </select>
                            @error('related_id')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('admin.users.index') }}"
                                class="mr-4 px-4 py-2 text-gray-600 rounded-md border border-gray-300 hover:bg-gray-100">Batal</a>
                            <x-primary-button type="submit">Update User</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function toggleRelatedId() {
                const roleSelect = document.getElementById('role');
                const relatedIdField = document.getElementById('related_id_field');
                if (roleSelect.value === 'guru') {
                    relatedIdField.classList.remove('hidden');
                } else {
                    relatedIdField.classList.add('hidden');
                    document.getElementById('related_id').value = ''; // Kosongkan nilai jika field disembunyikan
                }
            }
            document.addEventListener('DOMContentLoaded', toggleRelatedId);
        </script>
    @endpush
</x-app-layout>
