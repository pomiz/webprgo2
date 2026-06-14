@props(['product', 'existingReview' => null])

<div x-data="{ rating: {{ $existingReview?->rating ?? 0 }}, hoverRating: 0 }" class="bg-white dark:bg-surface-800 rounded-xl border border-gray-100 dark:border-gray-700 p-6">
    <h3 class="font-serif text-lg font-semibold text-gray-900 dark:text-white mb-4">
        {{ $existingReview ? 'Edit Review Anda' : 'Tulis Review' }}
    </h3>

    <form action="{{ route('review.store', $product->id) }}" method="POST">
        @csrf

        {{-- Star Rating Input --}}
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Rating</label>
            <div class="flex items-center gap-1">
                @for($i = 1; $i <= 5; $i++)
                    <button type="button"
                            @click="rating = {{ $i }}"
                            @mouseenter="hoverRating = {{ $i }}"
                            @mouseleave="hoverRating = 0"
                            class="focus:outline-none">
                        <svg class="w-8 h-8 transition-colors"
                             :class="(hoverRating || rating) >= {{ $i }} ? 'text-brand-500' : 'text-gray-300 dark:text-gray-600'"
                             fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                    </button>
                @endfor
            </div>
            <input type="hidden" name="rating" :value="rating">
            @error('rating')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Comment --}}
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Komentar (opsional)</label>
            <textarea name="comment" rows="3"
                      class="w-full rounded-lg border-gray-200 dark:border-gray-700 dark:bg-surface-900 dark:text-white text-sm focus:ring-brand-500 focus:border-brand-500"
                      placeholder="Bagikan pengalaman Anda...">{{ $existingReview?->comment }}</textarea>
            @error('comment')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" class="btn-primary text-sm">
            {{ $existingReview ? 'Update Review' : 'Kirim Review' }}
        </button>
    </form>
</div>
