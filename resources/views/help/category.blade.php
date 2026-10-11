@extends('help.layout')

@php use App\Support\HelpRoutes; @endphp

@section('title', $page->name)

@if ($description = $page->translation->getAttribute('description'))
    @section('description', $description)
@endif

@section('content')
    <div class="kb-wrap kb-page">
        @if ($parent)
            @include('help.partials.back', ['url' => HelpRoutes::url('category', $locale, ['slug' => $parent->slug]), 'label' => $parent->name])
        @else
            @include('help.partials.back', ['url' => HelpRoutes::url('home', $locale), 'label' => __('help.help')])
        @endif

        <h1 class="kb-title">{{ $page->name }}</h1>

        @if ($description = $page->translation->getAttribute('description'))
            <p class="kb-lead">{{ $description }}</p>
        @endif

        @if ($page->children !== [])
            <ul class="kb-chips" aria-label="{{ __('help.subtopics') }}">
                @foreach ($page->children as $child)
                    <li><a href="{{ HelpRoutes::url('category', $locale, ['slug' => $child->slug]) }}">{{ $child->name }} <span class="kb-count">{{ HelpRoutes::totalArticles($child) }}</span></a></li>
                @endforeach
            </ul>
        @endif

        @if ($page->articles !== [])
            @include('help.partials.article-list', ['articles' => $page->articles])
        @else
            <p class="kb-empty">{{ __('help.empty_category') }}</p>
        @endif
    </div>
@endsection
