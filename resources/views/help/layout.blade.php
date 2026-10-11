@php
    use App\Support\HelpRoutes;

    $brand = config('help-center.brand') ?: config('app.name');
    $logo = config('help-center.logo');
    $logoSvg = is_string($logo) && str_ends_with(strtolower($logo), '.svg') && is_file($logo) ? file_get_contents($logo) : null;
    $switch = $switch ?? [];
    $pageTitle = trim($__env->yieldContent('title'));
@endphp
<!doctype html>
<html lang="{{ $locale }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $pageTitle !== '' ? $pageTitle.' | ' : '' }}{{ __('help.help') }} | {{ $brand }}</title>
    @hasSection('description')
        <meta name="description" content="@yield('description')">
    @endif
    @if (! empty($canonical))
        <link rel="canonical" href="{{ $canonical }}">
    @endif
    @foreach ($switch as $alternateLocale => $alternateUrl)
        <link rel="alternate" hreflang="{{ $alternateLocale }}" href="{{ $alternateUrl }}">
    @endforeach
    @if (isset($switch[\JeffersonGoncalves\KnowledgeBase\Support\Locales::default()]))
        {{-- Readers whose language isn't offered get the authoritative version. --}}
        <link rel="alternate" hreflang="x-default" href="{{ $switch[\JeffersonGoncalves\KnowledgeBase\Support\Locales::default()] }}">
    @endif
    @if (is_string($logo) && $logo !== '' && ! $logoSvg)
        <link rel="icon" href="{{ $logo }}">
    @endif
    @include('help.partials.styles')
</head>
<body class="kb">
    <a class="kb-skip" href="#kb-main">{{ __('help.skip') }}</a>

    <div class="kb-demo-bar" data-demo-bar>
        <div class="kb-wrap kb-demo-bar__inner">
            <span>{{ __('help.demo_bar') }}</span>
            <span class="kb-demo-bar__links">
                <a href="{{ url('/admin') }}">{{ __('help.demo_bar_admin') }}</a>
                <a href="{{ config('help-center.links.demo') }}">{{ __('help.demo_bar_source') }}</a>
            </span>
        </div>
    </div>

    <header class="kb-header">
        <div class="kb-wrap kb-header__inner">
            <a class="kb-brand" href="{{ HelpRoutes::url('home', $locale) }}">
                @if ($logoSvg)
                    <span class="kb-brand__logo" data-logo>{!! $logoSvg !!}</span>
                @elseif (is_string($logo) && $logo !== '')
                    <span class="kb-brand__logo kb-brand__logo--image" data-logo><img src="{{ $logo }}" alt=""></span>
                @endif
                <span class="kb-brand__name">{{ $brand }}</span>
                <span class="kb-brand__section">{{ __('help.help') }}</span>
            </a>

            <div class="kb-header__tools">
                @unless ($__env->hasSection('no_header_search'))
                    @include('help.partials.header-search')
                @endunless

                @if (count(HelpRoutes::locales()) > 1)
                    <nav class="kb-langs" aria-label="{{ __('help.language') }}">
                        @foreach (HelpRoutes::locales() as $candidate)
                            @if ($candidate === $locale)
                                <span class="kb-langs__item" aria-current="true" lang="{{ $candidate }}" title="{{ HelpRoutes::languageName($candidate) }}">@include('help.partials.language', ['code' => $candidate])</span>
                            @elseif (isset($switch[$candidate]))
                                <a class="kb-langs__item" href="{{ $switch[$candidate] }}" hreflang="{{ $candidate }}" lang="{{ $candidate }}" title="{{ HelpRoutes::languageName($candidate) }}">@include('help.partials.language', ['code' => $candidate])</a>
                            @else
                                <span class="kb-langs__item kb-langs__item--off" lang="{{ $candidate }}" title="{{ HelpRoutes::languageName($candidate) }}">@include('help.partials.language', ['code' => $candidate])</span>
                            @endif
                        @endforeach
                    </nav>
                @endif
            </div>
        </div>
    </header>

    <main id="kb-main" class="kb-main" tabindex="-1">
        @yield('content')
    </main>

    <footer class="kb-footer">
        <div class="kb-wrap">
            <a href="{{ HelpRoutes::url('home', $locale) }}">{{ $brand }} {{ __('help.help') }}</a>
            <span class="kb-footer__credit" data-built-with>
                {{ __('help.built_with') }}
                <a href="{{ config('help-center.links.core') }}" target="_blank" rel="noopener">Laravel Knowledge Base</a>
            </span>
        </div>
    </footer>
</body>
</html>
