@php use App\Support\HelpRoutes; @endphp
{{-- $articles: list<ResolvedTranslation> from the core's HelpCenter API. --}}
<ul class="kb-list">
    @foreach ($articles as $item)
        <li>
            <a class="kb-list__title" href="{{ HelpRoutes::url('article', $locale, ['slug' => $item->slug]) }}">{{ $item->translation->getAttribute('title') }}</a>
            @if ($excerpt = $item->translation->getAttribute('excerpt'))
                <p class="kb-list__excerpt">{{ $excerpt }}</p>
            @endif
            @if ($item->locale !== $locale)
                <div class="kb-list__meta"><span class="kb-tag" lang="{{ $item->locale }}">{{ __('help.in_language', ['language' => HelpRoutes::languageName($item->locale, $locale)]) }}</span></div>
            @endif
        </li>
    @endforeach
</ul>
