<style>
/* Help center styles. The colours live in the custom properties below. */
:root {
    --kb-ink: #1c2230;
    --kb-paper: #ffffff;
    --kb-mist: #eef1f4;
    --kb-rule: #d9dde3;
    --kb-slate: #566072;
    --kb-accent: #2f6b3a;
    --kb-accent-ink: #ffffff;
    --kb-notice-bg: #fdf3d7;
    --kb-notice-ink: #6b4b00;
    --kb-serif: Charter, "Bitstream Charter", "Iowan Old Style", "Sitka Text", Cambria, Georgia, serif;
    --kb-sans: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
    --kb-radius: 10px;
    --kb-measure: 68ch;
}

*, *::before, *::after { box-sizing: border-box; }

.kb {
    margin: 0;
    background: var(--kb-paper);
    color: var(--kb-ink);
    font: 400 1rem/1.6 var(--kb-sans);
    -webkit-font-smoothing: antialiased;
    text-rendering: optimizeLegibility;
}

.kb a { color: var(--kb-accent); text-underline-offset: 0.18em; text-decoration-thickness: 1px; }
.kb a:hover { text-decoration-thickness: 2px; }
.kb :focus-visible { outline: 3px solid var(--kb-accent); outline-offset: 2px; border-radius: 4px; }
.kb h1, .kb h2, .kb h3 { font-family: var(--kb-serif); font-weight: 600; line-height: 1.2; color: var(--kb-ink); }

.kb-wrap { max-width: 1080px; margin: 0 auto; padding: 0 24px; }

.kb-skip { position: absolute; left: 16px; top: -48px; padding: 8px 12px; background: var(--kb-ink); color: var(--kb-paper) !important; border-radius: 6px; z-index: 10; }
.kb-skip:focus { top: 12px; }

/* Header */
.kb-header { border-bottom: 1px solid var(--kb-rule); }
.kb-demo-bar { background: var(--kb-ink); color: #e9edf2; font-size: 0.85rem; }
.kb-demo-bar__inner { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 4px 16px; padding-top: 7px; padding-bottom: 7px; }
.kb-demo-bar a { color: #ffffff; }
.kb-demo-bar__links { display: flex; gap: 16px; }
.kb-about { display: flex; align-items: center; gap: 16px; margin: 0 0 40px; padding: 16px 20px; border: 1px solid color-mix(in srgb, var(--kb-accent) 35%, var(--kb-rule)); border-radius: 12px; background: color-mix(in srgb, var(--kb-accent) 7%, var(--kb-paper)); color: var(--kb-ink) !important; text-decoration: none; }
.kb-about:hover { border-color: var(--kb-accent); }
.kb-about__text { flex: 1; }
.kb-about__title { display: block; font-weight: 600; }
.kb-about__body { display: block; margin-top: 2px; color: var(--kb-slate); font-size: 0.95rem; }
.kb-header__inner { display: flex; align-items: center; justify-content: space-between; gap: 16px; min-height: 64px; }
.kb-brand { display: flex; align-items: center; gap: 10px; text-decoration: none; color: var(--kb-ink) !important; }
.kb-brand__logo { display: inline-flex; align-items: center; justify-content: center; width: 2rem; height: 2rem; flex: none; border-radius: 50%; background: var(--kb-accent); color: var(--kb-accent-ink); }
.kb-brand__logo svg { width: 62%; height: 62%; color: inherit; }
.kb-brand__logo--image { background: none; }
.kb-brand__logo--image img { width: 100%; height: 100%; object-fit: contain; border-radius: 50%; }
.kb-brand__name { font-weight: 650; font-size: 1.05rem; }
.kb-brand__section { font-family: var(--kb-serif); font-size: 1.05rem; color: var(--kb-slate); padding-left: 10px; border-left: 1px solid var(--kb-rule); }
.kb-header__tools { display: flex; align-items: center; gap: 20px; font-size: 0.9rem; }
.kb-langs { display: flex; gap: 4px; }
.kb-langs__item { display: inline-block; min-width: 2.4rem; padding: 4px 8px; text-align: center; font-size: 0.85rem; font-weight: 600; letter-spacing: 0.02em; color: var(--kb-slate) !important; text-decoration: none; border-radius: 6px; }
a.kb-langs__item:hover { background: var(--kb-mist); color: var(--kb-ink) !important; }
.kb-langs__item[aria-current] { color: var(--kb-ink) !important; box-shadow: inset 0 -2px 0 var(--kb-accent); border-radius: 0; }
.kb-langs__item--off { opacity: 0.4; }
.kb-flag { display: block; flex: none; width: 1.5rem; height: 1.125rem; overflow: hidden; border-radius: 3px; box-shadow: 0 0 0 1px rgb(0 0 0 / 0.12); }
.kb-flag svg { display: block; width: 100%; height: 100%; }
.kb-langs__item:has(.kb-flag) { display: inline-flex; align-items: center; gap: 6px; min-width: 0; padding: 6px 6px 8px; }

.kb-main { display: block; outline: none; }
.kb-visually-hidden { position: absolute; width: 1px; height: 1px; padding: 0; margin: -1px; overflow: hidden; clip: rect(0 0 0 0); white-space: nowrap; border: 0; }

/* Search */
.kb-search { display: flex; gap: 0; max-width: 680px; border: 2px solid var(--kb-ink); border-radius: var(--kb-radius); background: var(--kb-paper); }
.kb-search:focus-within { box-shadow: 0 0 0 4px color-mix(in srgb, var(--kb-accent) 22%, transparent); }
.kb-search input { flex: 1; min-width: 0; border: 0; background: transparent; padding: 0 16px; height: 56px; font: inherit; font-size: 1.05rem; color: var(--kb-ink); border-radius: var(--kb-radius); }
.kb-search input::placeholder { color: var(--kb-slate); }
.kb-search input:focus { outline: none; }
.kb-search button { margin: 6px; padding: 0 20px; border: 0; border-radius: 7px; background: var(--kb-accent); color: var(--kb-accent-ink); font: inherit; font-weight: 600; cursor: pointer; }
.kb-search button:hover { filter: brightness(1.08); }

/* Home */
.kb-hero { padding-top: 64px; padding-bottom: 48px; }
.kb-hero h1 { margin: 0 0 24px; max-width: 16ch; font-size: clamp(2.1rem, 1.3rem + 3vw, 3.25rem); letter-spacing: -0.015em; }
.kb-home { display: grid; grid-template-columns: minmax(0, 1.45fr) minmax(0, 1fr); gap: 64px; padding-top: 8px; padding-bottom: 72px; }
.kb-section-title { margin: 0 0 8px; font-size: 1.35rem; }

.kb-faq { margin: 0; padding: 0; list-style: none; border-bottom: 1px solid var(--kb-rule); }
.kb-faq details { border-top: 1px solid var(--kb-rule); }
.kb-faq summary .kb-tag { margin-left: 6px; font-weight: 400; vertical-align: 2px; }
.kb-faq summary { display: flex; justify-content: space-between; align-items: baseline; gap: 16px; padding: 16px 0; font-weight: 600; font-size: 1.05rem; cursor: pointer; list-style: none; }
.kb-faq summary::-webkit-details-marker { display: none; }
.kb-faq summary::after { content: "+"; flex: none; font-weight: 400; font-size: 1.4rem; line-height: 1; color: var(--kb-accent); }
.kb-faq details[open] summary::after { content: "–"; }
.kb-faq summary:hover { color: var(--kb-accent); }
.kb-faq--page { max-width: 760px; }
.kb-faq__answer { padding: 0 0 20px; color: var(--kb-ink); }
.kb-faq__answer p { margin: 0 0 12px; }
.kb-more { display: inline-block; margin-top: 16px; font-weight: 600; }

.kb-topics { margin: 0; padding: 0; list-style: none; }
.kb-topics > li { padding: 6px 0 10px; border-top: 1px solid var(--kb-rule); }
.kb-topics > li:last-child { border-bottom: 1px solid var(--kb-rule); }
.kb-topic { display: flex; align-items: center; gap: 12px; margin: 0 -10px; padding: 8px 10px; border-radius: 8px; color: var(--kb-ink) !important; text-decoration: none; }
.kb-topic:hover { background: var(--kb-mist); }
.kb-topic__text { flex: 1; min-width: 0; display: flex; flex-direction: column; }
.kb-topic__name { font-weight: 600; font-size: 1.05rem; }
.kb-topic:hover .kb-topic__name { color: var(--kb-accent); }
.kb-topic__description { font-size: 0.9rem; color: var(--kb-slate); }
.kb-topic__chevron { flex: none; font-size: 1.3rem; line-height: 1; color: var(--kb-slate); transition: transform .15s ease; }
.kb-topic:hover .kb-topic__chevron { color: var(--kb-accent); transform: translateX(2px); }
.kb-count { flex: none; font-size: 0.85rem; color: var(--kb-slate); }
.kb-subtopics { display: flex; flex-wrap: wrap; gap: 4px 16px; margin: 6px 0 0; padding: 0; list-style: none; font-size: 0.92rem; }
.kb-subtopics a { color: var(--kb-slate); }
.kb-subtopics a:hover { color: var(--kb-accent); }

/* Page heads */
.kb-page { padding-top: 40px; padding-bottom: 72px; }
.kb-page--flush { padding-top: 0; }
.kb-crumbs { margin: 0 0 16px; padding: 0; list-style: none; display: flex; flex-wrap: wrap; gap: 6px; font-size: 0.9rem; color: var(--kb-slate); }
.kb-crumbs li + li::before { content: "/"; margin-right: 6px; color: var(--kb-rule); }
.kb-crumbs a { color: var(--kb-slate); }
.kb-crumbs a:hover { color: var(--kb-accent); }
.kb-title { margin: 0 0 12px; max-width: 24ch; font-size: clamp(1.9rem, 1.4rem + 1.8vw, 2.6rem); letter-spacing: -0.01em; }
.kb-lead { margin: 0 0 32px; max-width: var(--kb-measure); font-size: 1.1rem; color: var(--kb-slate); }

/* Article lists */
.kb-list { margin: 0; padding: 0; list-style: none; max-width: 760px; border-bottom: 1px solid var(--kb-rule); }
.kb-list > li { padding: 18px 0; border-top: 1px solid var(--kb-rule); }
.kb-list__title { font-weight: 600; font-size: 1.1rem; color: var(--kb-ink) !important; text-decoration: none; }
.kb-list__title:hover { color: var(--kb-accent) !important; text-decoration: underline; }
.kb-list__excerpt { margin: 4px 0 0; color: var(--kb-slate); }
.kb-list__meta { display: flex; flex-wrap: wrap; gap: 4px 12px; margin-top: 6px; font-size: 0.85rem; color: var(--kb-slate); }
.kb-tag { display: inline-block; padding: 1px 8px; border-radius: 999px; background: var(--kb-mist); color: var(--kb-ink); font-size: 0.8rem; }
.kb-tag--topic { color: var(--kb-slate); }
.kb-chips { display: flex; flex-wrap: wrap; gap: 8px; margin: 0 0 32px; padding: 0; list-style: none; }
.kb-chips a { display: inline-flex; align-items: center; gap: 6px; padding: 6px 14px; border: 1px solid transparent; border-radius: 999px; background: var(--kb-mist); color: var(--kb-ink); text-decoration: none; font-size: 0.95rem; }
.kb-chips a:hover { border-color: color-mix(in srgb, var(--kb-accent) 40%, transparent); background: color-mix(in srgb, var(--kb-accent) 9%, var(--kb-paper)); color: var(--kb-accent); }
.kb-chips--small { margin: 2px 0 2px; gap: 6px; }
.kb-chips--small a { padding: 3px 11px; font-size: 0.85rem; }
.kb-back { display: inline-flex; align-items: center; gap: 8px; margin: 0 0 20px; padding: 6px 14px 6px 10px; border-radius: 999px; background: var(--kb-mist); color: var(--kb-ink) !important; text-decoration: none; font-size: 0.9rem; font-weight: 500; }
.kb-back:hover { background: color-mix(in srgb, var(--kb-accent) 10%, var(--kb-paper)); color: var(--kb-accent) !important; }
.kb-back__arrow { transition: transform .15s ease; }
.kb-back:hover .kb-back__arrow { transform: translateX(-2px); }
.kb-header-search input { width: 220px; height: 38px; padding: 0 12px 0 34px; border: 1px solid var(--kb-rule); border-radius: 999px; background: var(--kb-mist) url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20' fill='none' stroke='%23566072' stroke-width='2'%3E%3Ccircle cx='8.5' cy='8.5' r='5.5'/%3E%3Cpath d='m13 13 4 4'/%3E%3C/svg%3E") no-repeat 12px center / 15px; font: inherit; font-size: 0.9rem; color: var(--kb-ink); }
.kb-header-search input:focus { outline: none; border-color: var(--kb-accent); background-color: var(--kb-paper); box-shadow: 0 0 0 3px color-mix(in srgb, var(--kb-accent) 18%, transparent); }
.kb-empty { color: var(--kb-slate); }
.kb-chips--spaced { margin-top: 24px; }

/* Article */
.kb-article { display: grid; grid-template-columns: minmax(0, 1fr) 260px; gap: 56px; align-items: start; }
.kb-meta { display: flex; flex-wrap: wrap; gap: 6px 20px; margin: 0 0 32px; font-size: 0.92rem; color: var(--kb-slate); }
.kb-notice { max-width: var(--kb-measure); margin: 0 0 24px; padding: 12px 16px; border-radius: 8px; background: var(--kb-notice-bg); color: var(--kb-notice-ink); font-size: 0.95rem; }
.kb-notice a { color: inherit; font-weight: 600; }

.kb-prose { max-width: var(--kb-measure); font-size: 1.0625rem; line-height: 1.65; }
.kb-prose > :first-child { margin-top: 0; }
.kb-prose h2 { margin: 2em 0 0.5em; font-size: 1.5rem; scroll-margin-top: 24px; }
.kb-prose h3 { margin: 1.6em 0 0.4em; font-size: 1.2rem; }
.kb-prose p { margin: 0 0 1em; }
.kb-prose ul, .kb-prose ol { margin: 0 0 1em; padding-left: 1.4em; }
.kb-prose li { margin: 0.35em 0; }
.kb-prose li::marker { color: var(--kb-accent); }
.kb-prose strong { font-weight: 650; }
.kb-prose blockquote { margin: 1.5em 0; padding: 4px 0 4px 18px; border-left: 3px solid var(--kb-accent); color: var(--kb-slate); }
.kb-prose code { padding: 1px 5px; border-radius: 4px; background: var(--kb-mist); font-size: 0.92em; }
.kb-prose img, .kb-prose video, .kb-prose iframe { max-width: 100%; height: auto; border-radius: 8px; }
.kb-prose img { display: block; margin: 1.5em 0; }
.kb-prose table { width: 100%; margin: 1.5em 0; border-collapse: collapse; font-size: 0.95rem; }
.kb-prose th, .kb-prose td { padding: 8px 10px; border-bottom: 1px solid var(--kb-rule); text-align: left; vertical-align: top; }
.kb-prose th { font-weight: 650; }

.kb-aside { position: sticky; top: 24px; display: grid; gap: 28px; font-size: 0.95rem; }
.kb-aside h2 { margin: 0 0 10px; font-family: var(--kb-sans); font-size: 0.9rem; font-weight: 650; color: var(--kb-slate); }
.kb-toc { padding: 16px 20px; border-radius: var(--kb-radius); background: var(--kb-mist); }
.kb-aside ul { margin: 0; padding: 0; list-style: none; display: grid; gap: 8px; }
.kb-aside a { color: var(--kb-ink); text-decoration: none; }
.kb-aside a:hover { color: var(--kb-accent); text-decoration: underline; }

.kb-toc-inline { display: none; max-width: var(--kb-measure); margin: 0 0 28px; padding: 12px 16px; border-radius: var(--kb-radius); background: var(--kb-mist); }
.kb-toc-inline summary { font-weight: 650; cursor: pointer; }
.kb-toc-inline ul { margin: 10px 0 2px; padding: 0; list-style: none; display: grid; gap: 8px; }
.kb-toc-inline a { color: var(--kb-ink); text-decoration: none; }
.kb-feedback { max-width: var(--kb-measure); margin-top: 48px; padding-top: 24px; border-top: 1px solid var(--kb-rule); }
.kb-feedback form { display: flex; flex-wrap: wrap; align-items: center; gap: 12px; margin: 0; }
.kb-feedback p { margin: 0; font-weight: 600; }
.kb-button { padding: 8px 18px; border: 1px solid var(--kb-rule); border-radius: 8px; background: var(--kb-paper); color: var(--kb-ink); font: inherit; font-weight: 600; cursor: pointer; }
.kb-button:hover { border-color: var(--kb-ink); }

/* Search page */
.kb-search-head { padding-top: 40px; padding-bottom: 8px; }
.kb-search-head .kb-search { margin-bottom: 24px; }
.kb-results-count { margin: 0 0 8px; color: var(--kb-slate); }

.kb-footer { padding: 32px 0 48px; border-top: 1px solid var(--kb-rule); font-size: 0.9rem; }
.kb-footer a { color: var(--kb-slate); text-decoration: none; }
.kb-footer a:hover { color: var(--kb-ink); }
.kb-footer .kb-wrap { display: flex; flex-wrap: wrap; justify-content: space-between; gap: 8px 24px; }
.kb-footer .kb-wrap > a { font-weight: 600; }
.kb-footer__credit { color: var(--kb-slate); font-size: 0.82rem; font-style: italic; opacity: 0.8; }
.kb-footer__credit a { text-decoration: underline; text-underline-offset: 2px; }

@media (max-width: 820px) {
    .kb-wrap { padding-left: 16px; padding-right: 16px; }
    .kb-hero { padding-top: 40px; padding-bottom: 32px; }
    .kb-home { grid-template-columns: 1fr; gap: 48px; }
    .kb-article { grid-template-columns: 1fr; gap: 40px; }
    .kb-aside { position: static; }
    .kb-aside .kb-toc { display: none; }
    .kb-toc-inline { display: block; }
    .kb-feedback p { flex-basis: 100%; }
    .kb-header-search input { width: 150px; }
    .kb-search input { height: 52px; }
    .kb-search button { padding: 0 14px; }
}

@media (max-width: 560px) {
    .kb-header-search { display: none; }
    .kb-brand__section { display: none; }
}

@media (prefers-reduced-motion: reduce) {
    .kb * { scroll-behavior: auto !important; transition: none !important; }
}

@media print {
    .kb-header, .kb-footer, .kb-aside, .kb-feedback, .kb-skip { display: none; }
    .kb-article { display: block; }
}
</style>
