@unless ($unlinked)
    <a href="{{ route('profile', $user->username()) }}">
@endunless

<img
    @if ($url === $fallback) loading="lazy" @endif
    src="{{ $url }}"
    alt="{{ $user->name() }}"
    {{ $attributes->merge(['class' => 'bg-gray-50 rounded-full']) }}
/>


@unless ($unlinked)
    </a>
@endunless
