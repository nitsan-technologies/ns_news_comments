# Feature — Premium strip and ns_license free trial

*Status: Done. Verified live on TYPO3 14.3.7 (edit form + Page module). Requires ns_license 14.5.4
(deep-link support) to be released.*

## Licensing status

Freemium per the NITSAN Free/Freemium sheet. Premium product: https://t3planet.de/typo3-nachrichten-kommentar-erweiterung
(not linked from the UI; the trial stays inside the TYPO3 backend).

Premium adds (T3Planet product page): comment moderation / admin approval, e-mail notifications (admin, news
author), honeypot and anti-spam, RTE comments, upvote/downvote, form and layout variations, dark mode.

## Dependency

`composer.json` `"nitsan/ns-license": "^14.5.4"`, `ext_emconf.php` `'ns_license' => '14.5.4-14.9.99'`.

## Strip

Same implementation pattern as ns_revolution_slider (`Classes/Backend/PremiumStrip.php`).

- `Backend\PremiumStrip::render()` returns the HTML, or `''` if the ns_license route is missing.
- Content: `actions-rocket` core icon (`IconSize::SMALL` on v13+, `'small'` on v12), headline and description
  on two lines, one `btn btn-primary` "Start Free Trial →" link (the arrow is `aria-hidden` markup, not part of
  the label; same tab).
- Wording and design are the team-wide standard from the 2026-10-14 review, identical in every NITSAN strip
  except for the extension name.
- Labels in `locallang.xlf`:
  - `premium.strip.headline`: "You're using the free version of NS News Comments"
  - `premium.strip.description`: "Unlock premium features and get more out of NS News Comments."
  - `premium.strip.trial`: "Start Free Trial"
- Styling: `.ns-news-comments-premium-strip` (+ `-icon`, `-headline`, `-description`) in
  `Resources/Public/css/backend-premium-strip.css` — light-blue card (`#e7f2fc`, border `#8eb6e8`, 4px `#2b6cb0`
  left accent, 6px radius, padding 14px 18px, text `#0d2c4a`), semibold 1.1em headline, `light-dark()` for dark
  mode, button `#007bff` (hover `#0062cc`, no wrap). Inside the FlexForm panel (`.panel > …`) it is inset by 16px.

## Placements

1. **Edit form, "Configuration" tab, below the "Plugin Options" legend, above the FlexForm fields.**
   Node resolver `1791957358` (priority 40) on `flexFormNoTabsContainer` (the FlexForm has a single sheet).
   `PremiumStripFlexFormNoTabsContainerResolver` only matches `tt_content`, field `pi_flexform`,
   `recordTypeValue === 'nsnewscomments_newscomment'`, not an inline child. The container extends core
   `FlexFormNoTabsContainer`, prepends the strip and adds the stylesheet via `stylesheetFiles`.
   Other plugins' FlexForms (e.g. `nsgoogledocs`) stay untouched (verified).
   If the FlexForm ever gets a second sheet, switch to `flexFormTabsContainer` as in ns_revolution_slider.
2. **Page module preview.** `<nc:be.premiumStrip />` at the top of `Backend/NsNewscomment.html` and
   `NsNewscommentV14.html`; `PremiumStripViewHelper` adds the CSS via `PageRenderer::addCssFile()`.
   A ViewHelper (not a `PageContentPreviewRenderingEvent` listener) because setting preview content in that
   event suppresses the TSconfig Fluid preview. Fluid previews are not wrapped in an edit link (v12–v14), so
   the button link is valid HTML.

## Trial link

`UriBuilder::buildUriFromRoute('nitsan_nslicensemodule', ['trial' => 'ns_news_comments'])` + `#free-section`.
ns_license's `get-license.js` (`maybeOpenTrialDeepLink()`) reads `?trial=`, removes it from the URL, activates
"My Extensions" or the Extensions catalog tab and opens its trial modal for `ns_news_comments`. The
ns_license module requires system maintainer access.
