@props(['reviews'])

<div class="space-y-4">
    @forelse($reviews as $review)
        <div class="bg-white dark:bg-surface-800 rounded-xl border border-gray-100 dark:border-gray-700 p-5">
            <div class="flex items-center justify-between mb-2">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-brand-100 dark:bg-brand-900/30 flex items-center justify-center">
                        <span class="text-sm font-semibold text-brand-700 dark:text-brand-300">
                            {{ strtoupper(substr($review->user->username, 0, 1)) }}
                        </span>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $review->user->username }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $review->created_at->diffForHumans() }}</p>
                    </div>
                </div>
                <x-star-rating :rating="$review->rating" size="w-4 h-4" />
            </div>
            @if($review->comment)
                <p class="text-sm text-gray-600 dark:text-gray-300 mt-2">{{ $review->comment }}</p>
            @endif

            {{-- Delete button if own review --}}
            @if(auth()->id() === $review->user_id)
                <form action="{{ route('review.destroy', $review->product_id) }}" method="POST" class="mt-3">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-xs text-red-500 hover:text-red-700">Hapus review</button>
                </form>
            @endif
        </div>
    @empty
        <p class="text-sm text-gray-500 dark:text-gray-400 text-center py-4">Belum ada review untuk produk ini.</p>
    @endforelse
</div>
