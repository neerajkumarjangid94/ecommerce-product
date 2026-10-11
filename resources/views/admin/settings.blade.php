<x-app-layout>
    <div class="py-8">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-bold text-gray-800 dark:text-white">Settings</h2>
                <a href="javascript:history.back()" class="px-3 py-1.5 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 text-sm font-medium rounded-md shadow-sm transition-colors inline-flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back
                </a>
            </div>

            {{-- Success Message --}}
            @if (session('success'))
            <div class="mb-4 p-3 bg-green-50 border border-green-300 rounded-md text-sm text-green-700">
                {{ session('success') }}
            </div>
            @endif

            {{-- COLORS --}}
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg mb-5">
                <div class="px-5 py-4 border-b border-gray-100">
                    <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-200">Colors</h3>
                </div>

                <div class="px-5 py-4">
                    {{-- Add Form --}}
                    <form action="{{ route('admin.settings.color.store') }}" method="POST">
                        @csrf
                        <div class="flex items-center gap-2 mb-1">
                            <input type="text" name="color_name"
                                value="{{ old('color_name') }}"
                                placeholder="Color name e.g. Red"
                                class="flex-1 border border-gray-300 rounded-md text-sm px-3 py-2 focus:outline-none focus:ring-1 focus:ring-blue-500
                                    {{ $errors->has('color_name') ? 'border-red-500 bg-red-50' : '' }}">
                            <input type="color" name="color_hex_code"
                                value="{{ old('color_hex_code', '#3b82f6') }}"
                                class="w-9 h-9 rounded border border-gray-300 p-0.5 cursor-pointer">
                            <button type="submit"
                                class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded-md">
                                Add
                            </button>
                        </div>
                        @error('color_name')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                        @error('color_hex_code')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-xs text-gray-400 mt-2">
                            Step 1: Select a color using the picker &nbsp;|&nbsp; Step 2: Type the color name &nbsp;|&nbsp; Step 3: Click <strong>Add</strong>
                        </p>
                    </form>
                </div>

                {{-- List --}}
                @if ($colors->isEmpty())
                <div class="px-5 pb-4 text-sm text-gray-400">No colors added yet.</div>
                @else
                <ul class="px-5 pb-4 space-y-2">
                    @foreach ($colors as $color)
                    <li class="flex items-center justify-between py-2 border-t border-gray-100">
                        <div class="flex items-center gap-3">
                            <span class="w-5 h-5 rounded-full border border-gray-200 flex-shrink-0"
                                style="background-color: {{ $color->hex_code ?? '#ccc' }}"></span>
                            <span class="text-sm text-gray-700 dark:text-gray-300">{{ $color->name }}</span>
                            @if($color->hex_code)
                            <span class="text-xs text-gray-400">{{ $color->hex_code }}</span>
                            @endif
                        </div>
                        <form action="{{ route('admin.settings.color.destroy', $color->id) }}" method="POST"
                            onsubmit="return confirm('Delete {{ $color->name }}?')">
                            @csrf @method('DELETE')
                            <button class="text-xs text-red-500 hover:text-red-700">Delete</button>
                        </form>
                    </li>
                    @endforeach
                </ul>
                @endif
            </div>

            {{-- SIZES --}}
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                <div class="px-5 py-4 border-b border-gray-100">
                    <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-200">Sizes</h3>
                </div>

                <div class="px-5 py-4">
                    {{-- Add Form --}}
                    <form action="{{ route('admin.settings.size.store') }}" method="POST">
                        @csrf
                        <div class="flex items-center gap-2 mb-1">
                            <input type="text" name="size_name"
                                value="{{ old('size_name') }}"
                                placeholder="Size name e.g. M, XL, 42"
                                class="flex-1 border border-gray-300 rounded-md text-sm px-3 py-2 focus:outline-none focus:ring-1 focus:ring-blue-500
                                    {{ $errors->has('size_name') ? 'border-red-500 bg-red-50' : '' }}">
                            <button type="submit"
                                class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded-md">
                                Add
                            </button>
                        </div>
                        @error('size_name')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-xs text-gray-400 mt-2">
                            Enter size name e.g. <strong>S</strong>, <strong>M</strong>, <strong>L</strong>, <strong>XL</strong>, <strong>42</strong> &nbsp;|&nbsp; Click <strong>Add</strong>
                        </p>
                    </form>
                </div>

                {{-- List --}}
                @if ($sizes->isEmpty())
                <div class="px-5 pb-4 text-sm text-gray-400">No sizes added yet.</div>
                @else
                <ul class="px-5 pb-4 space-y-2">
                    @foreach ($sizes as $size)
                    <li class="flex items-center justify-between py-2 border-t border-gray-100">
                        <span class="text-sm text-gray-700 dark:text-gray-300">{{ $size->name }}</span>
                        <form action="{{ route('admin.settings.size.destroy', $size->id) }}" method="POST"
                            onsubmit="return confirm('Delete {{ $size->name }}?')">
                            @csrf @method('DELETE')
                            <button class="text-xs text-red-500 hover:text-red-700">Delete</button>
                        </form>
                    </li>
                    @endforeach
                </ul>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>