# ns_news_comments — Session state

## 2026-10-28 — Translation keys in de/es/fr/tr

- Review comment on 14zervyxj9p: new English labels had no targets in other languages.
- Added every missing `locallang.xlf` / `locallang_db.xlf` id to de, es, fr and tr
  (`form.approveMessage`, `premium.strip.*`, `flexform.approveComment`, date/time
  descriptions, `newsComment`, jQuery v2/v3, and tr terms keys).

## 2026-10-28 — Optional approve-before-publish (14zervyx998)

- ClickUp 14zervyx998 is free-only (`createAction` never set `hidden`). Premium already gates with
  `approveComment=1` + `setHidden(1)` and a BE Comment module — left unchanged.
- Free: optional `approveComment` default `0` (FlexForm, TypoScript, site set). When `1`, `createAction`
  sets `hidden=1` before persist and returns `{status: pending}`; JS shows `form.approveMessage`.
  The AJAX PAGE has no plugin FlexForm, so `listAction` signs the flag (`approval` + `approvalHmac`).
  Editors unhide via the news record Comments tab. No BE Comment module added.

## 2026-10-28 — Reject empty/whitespace comments

- ClickUp 14zervyx997: `createAction()` unicode-trims `username`, `usermail` and `description` from
  the posted arguments, returns HTTP 400 `{error: validation, fields: [...]}` without persisting,
  and `custom.js` shows the existing required-field errors instead of treating it as a successful post.

## 2026-10-28 — Site Set AJAX typeNum 1730800496

- ClickUp 14zervyx92b: PAGE `nsNewsCommentsAjaxCall` is inline in static and Site Set `setup.typoscript`.
  The same typeNum is also registered from `ext_localconf.php` (`addTypoScriptSetup`, Site Sets on v13+)
  so create works on a Site Set-only instance before the TypoScript include-tree cache is built.

## 2026-10-28 — Premium strip product name

- Headline/description use "News Comments" (no "NS" prefix). Labels `premium.strip.headline` / `premium.strip.description`.

## 2026-10-28 — CGL blockers + majors (v12–v14, PHP 8.2–8.5)

- Blockers: escaped comment text; server-side captcha via session `ns_news_comments_captcha`;
  trusted `newsuid`/`pageid` + parent-news check; HMAC-checked storage pid; `#[Cascade]` + annotation on replies.
- Majors: FlexForm `columnsOverrides` on v14 / `addPiFlexFormValue` on v12–v13; System Resource API behind
  `class_exists`; `normalizedParams` site URL; no `$_REQUEST` / Install `SessionService` / news hook /
  `ext_tables.php` / `switchableControllerActions`; ViewHelper constructor DI; TCA datetime + dropped obsolete
  ctrl/column keys; `Package.providesPackages: {}`. Composer no longer requires `typo3/cms-install`.
- PHP 8.1-safe syntax kept (v12). AJAX type `1730800496` unchanged.

## 2026-10-28 — PHP 8.1–8.5 × TYPO3 v12/v13/v14

- Official matrix: v12 = PHP 8.1–8.4; v13/v14 = PHP 8.2–8.5. Extension constraint is the union (`8.1.0-8.5.99` / `>=8.1 <8.6`).
- All 23 PHP files lint clean on 8.1, 8.2, 8.3, 8.4, 8.5. Captcha `phpcaptcha()` produces a JPEG with zero deprecations on each.
- No PHP 8.2+ only syntax (so v12/PHP 8.1 parses). No implicit-nullable params (PHP 8.4). `imagedestroy()` already removed (PHP 8.5).
- TYPO3 API switches already in place: `TSFE->id` vs `frontend.page.information` (v12 vs v13+), `IconSize` enum vs `'small'`, FormEngine constructor vs `setData()`, `ContextualFeedbackSeverity` (v12+).
- User-verified: works as expected in Composer and Classic (non-Composer) setups. v12 / v13 not live-tested here.

## 2026-10-14 — ns_license integration + premium strip + agent context

- Branch `master` (uncommitted). Added `nitsan/ns-license ^14.5.4` / `ns_license 14.5.4-14.9.99`.
- Premium strip "Start Free Trial" → ns_license module `?trial=ns_news_comments#free-section` in the plugin
  edit form (below "Plugin Options") and the Page module preview (see `features/premium-cta.md`).
- Verified on TYPO3 14.3.7 with a temporary hidden content element and temp admin (both removed): one strip in
  each place, rocket icon, blue palette, correct link; `nsgoogledocs` FlexForm has no strip. v12/v13 not tested.
- Created agent context (`AGENTS.md`, `CLAUDE.md`, `context/`, `tasks/`); no previous context existed.
- Open: ns_license 14.5.4 not yet published; trial modal click-through only code-read; frontend comment flow
  only code-read.

## 2026-10-14 — Banner redesign from review

- Team-wide standard, same in every NITSAN strip except the name: "You're using the free version of News
  Comments" / "Unlock premium features and get more out of News Comments.", two lines, "Start Free Trial →",
  14px 18px padding, 6px radius, inset 16px inside the FlexForm panel (see `features/premium-cta.md`).
  Version 14.0.2 → 14.1.0. Edit form and Page module verified live on 14.3.7 (element 59; temporary admin
  removed). Uncommitted.

## 2026-10-15 — Context audit

- Code audit: session news-uid fallback is dead code; asset settings unused; FlexForm details, `new` without
  action, preview template switch, `addPiFlexFormValue()` deprecation and duplicate set TSconfig documented.
