@extends('help.layout')

@php use App\Support\HelpRoutes; @endphp

@section('title', $query !== '' ? $query : __('help.search_button'))
@section('no_header_search', '1')

@section('content')
    <div class="kb-wrap">
        <div class="kb-search-head">
            @include('help.partials.back', ['url' => HelpRoutes::url('home', $locale), 'label' => __('help.help')])
            <h1 class="kb-visually-hidden">{{ __('help.search_label') }}</h1>
            @include('help.partials.search-form', ['id' => 'page', 'query' => $query, 'autofocus' => ! $searched])
        </div>

        <div class="kb-page kb-page--flush">
            @if ($query !== '' && ! $searched)
                <p class="kb-results-count">{{ __('help.search_too_short', ['min' => config('help-center.search_min_length', 2)]) }}</p>
            @elseif ($searched && $results->isNotEmpty())
                <p class="kb-results-count" role="status">{{ trans_choice('help.results_count', $results->count(), ['query' => $query]) }}</p>
                <ul class="kb-list">
                    @foreach ($results as $result)
                        <li>
                            <a class="kb-list__title" href="{{ HelpRoutes::url('article', $locale, ['slug' => $result['slug']]) }}"
                               data-search-result="{{ $result['slug'] }}" data-served-locale="{{ $result['locale'] }}"
                               lang="{{ $result['locale'] }}">{{ $result['title'] }}</a>
                            @if ($result['excerpt'])
                                <p class="kb-list__excerpt" lang="{{ $result['locale'] }}">{{ $result['excerpt'] }}</p>
                            @endif
                            @if ($result['category'] || $result['locale'] !== $locale)
                                <div class="kb-list__meta">
                                    @if ($result['category'])
                                        <span class="kb-tag kb-tag--topic">{{ $result['category'] }}</span>
                                    @endif
                                    @if ($result['locale'] !== $locale)
                                        <span class="kb-tag">{{ __('help.in_language', ['language' => HelpRoutes::languageName($result['locale'], $locale)]) }}</span>
                                    @endif
                                </div>
                            @endif
                        </li>
                    @endforeach
                </ul>
            @elseif ($searched)
                <p class="kb-results-count" role="status"><strong>{{ __('help.no_results', ['query' => $query]) }}</strong> {{ __('help.no_results_hint') }}</p>
                @if ($categories !== [])
                    <ul class="kb-chips kb-chips--spaced">
                        @foreach ($categories as $node)
                            <li><a href="{{ HelpRoutes::url('category', $locale, ['slug' => $node->slug]) }}">{{ $node->name }}</a></li>
                        @endforeach
                    </ul>
                @endif
            @endif
        </div>
    </div>
@endsection
