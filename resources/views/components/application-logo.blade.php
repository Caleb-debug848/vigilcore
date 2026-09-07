@props(['full' => false])

<div {{ $attributes->merge(['class' => 'inline-flex items-center justify-center']) }}>
    @if($full)
        <img src="{{ asset('images/logo_vigilcore_full_transparent.png') }}" alt="VigilCore" class="h-full w-auto object-contain dark:hidden">
        <img src="{{ asset('images/logo_vigilcore_full_dark_transparent.png') }}" alt="VigilCore" class="h-full w-auto object-contain hidden dark:block">
    @else
        <img src="{{ asset('images/logo.svg') }}" alt="VigilCore" class="h-full w-auto object-contain">
    @endif
</div>
