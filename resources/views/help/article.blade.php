@extends('help.layout')

@php
    use App\Support\HelpRoutes;

    $translation = $page->translation;
    $title = $translation->getAttribute('title');
    $others = array_diff_key($switch, [$page->locale => true]);
@endphp

@section('title', $translation->getAttribute('seo_title') ?: $title)

@if ($description = $translation->getAttribute('seo_description') ?: $translation->getAttribute('excerpt'))
    @section('description', $description)
@endif

@section('content')
    <div class="kb-wrap kb-page">
        @php $topic = $page->breadcrumb !== [] ? $page->breadcrumb[array_key_last($page->breadcrumb)] : null; @endphp
        @if ($topic)
            @include('help.partials.back', ['url' => HelpRoutes::url('category', $locale, ['slug' => $topic->slug]), 'label' => $topic->translation->getAttribute('name')])
        @else
            @include('help.partials.back', ['url' => HelpRoutes::url('home', $locale), 'label' => __('help.help')])
        @endif

        <div class="kb-article">
            <article lang="{{ $page->locale }}" data-article-slug="{{ $page->slug }}" data-article-locale="{{ $page->locale }}">
                @if ($page->isFallback)
                    <p class="kb-notice" lang="{{ $locale }}" data-fallback-notice>
                        {{ __('help.fallback_notice', [
                            'requested' => HelpRoutes::languageName($locale, $locale),
                            'served' => HelpRoutes::languageName($page->locale, $locale),
                        ]) }}
                    </p>
                @elseif ($isOutdated && $authoritativeLocale && $authoritativeLocale !== $page->locale)
                    <p class="kb-notice" lang="{{ $locale }}" data-outdated-notice>
                        {{ __('help.outdated_notice', ['source' => HelpRoutes::languageName($authoritativeLocale, $locale)]) }}
                        @if ($authoritativeUrl)
                            <a href="{{ $authoritativeUrl }}" hreflang="{{ $authoritativeLocale }}">{{ __('help.outdated_link', ['source' => HelpRoutes::languageName($authoritativeLocale, $locale)]) }}</a>
                        @endif
                    </p>
                @endif

                <h1 class="kb-title">{{ $title }}</h1>

                <div class="kb-meta" lang="{{ $locale }}">
                    @if ($updatedAt)
                        <span>{{ __('help.updated', ['date' => $updatedAt->locale($locale)->isoFormat('LL')]) }}</span>
                    @endif
                    @if ($others !== [])
                        <span>
                            {{ __('help.also_in') }}
                            @foreach ($others as $otherLocale => $otherUrl)
                                <a href="{{ $otherUrl }}" hreflang="{{ $otherLocale }}">{{ HelpRoutes::languageName($otherLocale, $locale) }}</a>@if (! $loop->last), @endif
                            @endforeach
                        </span>
                    @endif
                </div>

                @if (count($content['toc']) > 1)
                    <details class="kb-toc-inline" lang="{{ $locale }}">
                        <summary>{{ __('help.on_this_page') }}</summary>
                        <ul>
                            @foreach ($content['toc'] as $entry)
                                <li><a href="#{{ $entry['id'] }}">{{ $entry['text'] }}</a></li>
                            @endforeach
                        </ul>
                    </details>
                @endif

                <div class="kb-prose">
                    {!! $content['html'] !!}
                </div>

                @if (config('help-center.feedback', true) && config('knowledge-base.feedback_enabled', true))
                    <section class="kb-feedback" id="feedback" lang="{{ $locale }}" aria-live="polite">
                        @if ($feedbackGiven)
                            <p>{{ __('help.feedback_thanks') }}</p>
                        @else
                            <form method="post" action="{{ HelpRoutes::url('feedback', $locale, ['slug' => $page->slug]) }}">
                                @csrf
                                <p id="kb-helpful">{{ __('help.helpful_question') }}</p>
                                <button class="kb-button" type="submit" name="helpful" value="1" aria-describedby="kb-helpful">{{ __('help.helpful_yes') }}</button>
                                <button class="kb-button" type="submit" name="helpful" value="0" aria-describedby="kb-helpful">{{ __('help.helpful_no') }}</button>
                            </form>
                        @endif
                    </section>
                @endif
            </article>

            @if ($content['toc'] !== [] || $page->related !== [])
                <aside class="kb-aside" lang="{{ $locale }}">
                    @if (count($content['toc']) > 1)
                        <nav class="kb-toc" aria-labelledby="kb-toc-title">
                            <h2 id="kb-toc-title">{{ __('help.on_this_page') }}</h2>
                            <ul>
                                @foreach ($content['toc'] as $entry)
                                    <li><a href="#{{ $entry['id'] }}">{{ $entry['text'] }}</a></li>
                                @endforeach
                            </ul>
                        </nav>
                    @endif

                    @if ($page->related !== [])
                        <section aria-labelledby="kb-related-title">
                            <h2 id="kb-related-title">{{ __('help.related') }}</h2>
                            <ul>
                                @foreach ($page->related as $item)
                                    <li><a href="{{ HelpRoutes::url('article', $locale, ['slug' => $item->slug]) }}">{{ $item->translation->getAttribute('title') }}</a></li>
                                @endforeach
                            </ul>
                        </section>
                    @endif
                </aside>
            @endif
        </div>
    </div>
@endsection
