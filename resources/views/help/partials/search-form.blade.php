@php use App\Support\HelpRoutes; @endphp
<form class="kb-search" method="get" action="{{ HelpRoutes::url('search', $locale) }}" role="search">
    <label class="kb-visually-hidden" for="kb-search-{{ $id ?? 'q' }}">{{ __('help.search_label') }}</label>
    <input id="kb-search-{{ $id ?? 'q' }}" type="search" name="q" value="{{ $query ?? '' }}"
           placeholder="{{ __('help.search_placeholder') }}" autocomplete="off"
           @if (! empty($autofocus)) autofocus @endif>
    <button type="submit">{{ __('help.search_button') }}</button>
</form>
