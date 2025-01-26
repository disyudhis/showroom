<div x-data="dynamicImageCarousel({{ json_encode($images) }})" class="space-y-4">
    <div class="relative w-full overflow-hidden rounded-2xl shadow-lg">
        <div x-ref="carousel" class="flex transition-transform duration-500 ease-in-out"
            :style="`transform: translateX(-${currentIndex * 100}%)`">
            @php
                $images = $images ?? [];
            @endphp

            @forelse($images as $image)
                <div class="w-full flex-shrink-0">
                    <img src="{{ Storage::url($image->image) }}"
                        alt="{{ $title ?? 'Image' }} - Image {{ $loop->iteration }}"
                        class="w-full h-[500px] object-cover">
                </div>
            @empty
                <div class="w-full h-[500px] bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
                    <i class="ri-image-line text-4xl text-gray-500 dark:text-gray-400"></i>
                    <span class="ml-2 text-gray-500 dark:text-gray-400">No Images Available</span>
                </div>
            @endforelse
        </div>

        @if (count($images) > 1)
            <div class="absolute inset-0 flex items-center justify-between px-4">
                <button @click="prev"
                    class="bg-white/50 dark:bg-gray-800/50 p-3 rounded-full hover:bg-white/70 dark:hover:bg-gray-800/70 transition-all">
                    <i class="ri-arrow-left-line text-2xl"></i>
                </button>
                <button @click="next"
                    class="bg-white/50 dark:bg-gray-800/50 p-3 rounded-full hover:bg-white/70 dark:hover:bg-gray-800/70 transition-all">
                    <i class="ri-arrow-right-line text-2xl"></i>
                </button>
            </div>
        @endif
    </div>

    @if (count($images) > 1)
        <div class="flex space-x-2 overflow-x-auto py-2 px-1">
            @foreach ($images as $index => $image)
                <button @click="goToSlide({{ $index }})"
                    class="flex-shrink-0 w-20 h-20 rounded-lg overflow-hidden transition-all duration-300 border-2"
                    :class="{
                        'border-blue-500': currentIndex === {{ $index }},
                        'border-transparent opacity-60 hover:opacity-100': currentIndex !== {{ $index }}
                    }">
                    <img src="{{ Storage::url($image->image) }}" alt="Thumbnail {{ $index + 1 }}"
                        class="w-full h-full object-cover">
                </button>
            @endforeach
        </div>
    @endif

    @push('script')
        <script>
            function dynamicImageCarousel(images) {
                return {
                    images: images,
                    currentIndex: 0,
                    next() {
                        this.currentIndex = (this.currentIndex + 1) % this.images.length;
                    },
                    prev() {
                        this.currentIndex = (this.currentIndex - 1 + this.images.length) % this.images.length;
                    },
                    goToSlide(index) {
                        this.currentIndex = index;
                    }
                }
            }
        </script>
    @endpush
</div>
