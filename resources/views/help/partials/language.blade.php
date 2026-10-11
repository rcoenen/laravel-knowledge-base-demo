@php use App\Support\HelpRoutes; @endphp
@if ($flag = HelpRoutes::flag($code))
<span class="kb-flag" aria-hidden="true">{!! $flag !!}</span><span class="kb-visually-hidden">{{ HelpRoutes::languageName($code) }}</span>
@else
{{ strtoupper($code) }}
@endif
