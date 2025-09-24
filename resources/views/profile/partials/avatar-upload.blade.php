<div class="mb-6">
    <label class="block text-sm font-medium text-gray-700 mb-2">Foto Profil</label>
    <div class="flex items-center">
        <div class="h-20 w-20 rounded-full overflow-hidden border border-gray-200">
            @if($user->avatar)
                <img src="{{ $user->avatar_url }}" alt="Avatar" class="w-full h-full object-cover">
            @else
                <div class="w-full h-full bg-gray-100 flex items-center justify-center">
                    <i class="fas fa-user text-gray-400 text-2xl"></i>
                </div>
            @endif
        </div>
        <div class="ml-4">
            <input type="file" name="avatar" accept="image/*" class="block w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-red-50 file:text-red-600 hover:file:bg-red-100"/>
            @error('avatar')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
    </div>
</div>
