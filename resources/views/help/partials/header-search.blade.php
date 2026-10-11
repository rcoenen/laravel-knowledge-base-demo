@php use App\Support\HelpRoutes; @endphp
<form class="kb-header-search" method="get" action="{{ HelpRoutes::url('search', $locale) }}" role="search" data-header-search>
    <label class="kb-visually-hidden" for="kb-search-header">{{ __('help.search_label') }}</label>
    <input id="kb-search-header" type="search" name="q" placeholder="{{ __('help.search_placeholder') }}" autocomplete="off">
</form>
