@extends('layouts.app')

@section('title', $media->title)
@section('description', $media->description)

@section('content')
    <!-- Hero Section -->
    <section class="relative min-h-[500px]">
        <div class="absolute inset-0 overflow-hidden">
            <img src="{{ Storage::url($media->image) }}" alt="{{ $media->title }}"
                class="w-full h-full object-cover object-center">
        </div>

        <div class="relative container mx-auto px-12 pt-36 pb-20">
            <div class="max-w-4xl">
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-white mb-6">
                    {{ $media->title }}
                </h1>
                </div>
                <p class="text-xl text-gray-300 max-w-2xl">
                    {{ $media->description }}
                </p>
            </div>
        </div>
    </section>

    <!-- Gallery Section -->
    <section class="py-16">
        @if($media->gallery && count($media->gallery) > 0)
            <div class="mb-16">
                <div class="text-center mb-12">
                    <h2 class="text-3xl font-bold text-orange-500 mb-3">Gallery</h2>
                    <div class="w-24 h-0.5 bg-orange-500 mx-auto"></div>
                </div>
                <div id="gallery-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($media->gallery as $index => $image)
                        <div
                            class="relative aspect-square group overflow-hidden rounded-xl shadow-lg transform hover:-translate-y-1 transition-all duration-300 cursor-pointer"
                            onclick="openGalleryModal('{{ Storage::url($image) }}', {{ $index }})">
                            <img src="{{ Storage::url($image) }}" alt="Gallery image"
                                class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-500">
                            <div
                                class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center">
                                <span
                                    class="w-14 h-14 rounded-full bg-white/90 backdrop-blur flex items-center justify-center transform translate-y-8 opacity-0 group-hover:translate-y-0 group-hover:opacity-100 transition-all duration-300 hover:bg-white">
                                    <i class="fas fa-expand text-gray-900 text-xl"></i>
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </section>

    <!-- Gallery Modal -->
    <div id="gallery-modal"
        class="hidden fixed inset-0 bg-black/90 flex items-center justify-center z-50">
        <button onclick="closeGalleryModal()" class="absolute top-4 right-4 text-white text-3xl">&times;</button>
        <button onclick="changeImage(-1)" class="absolute left-4 text-white text-3xl">&#10094;</button>
        <img id="modal-image" src="" alt="Modal Image"
            class="max-h-[80vh] max-w-[90vw] rounded-xl shadow-lg transition-all duration-200">
        <button onclick="changeImage(1)" class="absolute right-4 text-white text-3xl">&#10095;</button>
    </div>
@endsection
