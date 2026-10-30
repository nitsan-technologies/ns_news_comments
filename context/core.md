# ns_news_comments — Core facts

## Identity

| Item | Value |
|---|---|
| Extension key | `ns_news_comments` |
| Composer name | `nitsan/ns-news-comments` |
| Title (ext_emconf) / product name | TYPO3 News Comments Extension / News Comment |
| Namespace | `Nitsan\NsNewsComments\` → `Classes/` (note `Nitsan`, not `NITSAN`) |
| Version (ext_emconf) | 14.1.0 (latest tag 14.0.2) |
| Repository | https://github.com/nitsan-technologies/ns_news_comments (branch `master`) |
| Licensing status (NITSAN Free/Freemium sheet) | **Freemium** — this repo is the free version |
| Premium version (sheet) | https://t3planet.de/typo3-nachrichten-kommentar-erweiterung (€169/year) |

## Compatibility

| Source | TYPO3 | EXT:news | PHP |
|---|---|---|---|
| `ext_emconf.php` | `12.4.37-14.9.99` | `11.0.0-14.9.99` | `8.1.0-8.5.99` |
| `composer.json` | `^12.4.37 \|\| ^13 \|\| ^14` | `^11 \|\| ^12 \|\| ^13 \|\| ^14` | `>=8.1 <8.6` |
| ns_license 14.5.4 (dependency) | `12.4.18-14.9.99` | — | `>=8.1 <8.6` |

- Effective range is **TYPO3 12.4.37** and **PHP 8.1–8.5**. Official TYPO3×PHP matrix: v12 = 8.1–8.4 (not 8.5); v13/v14 = 8.2–8.5. Captcha no longer calls `imagedestroy()` (deprecated in 8.5).
- Syntax + captcha generation verified on PHP 8.1, 8.2, 8.3, 8.4 and 8.5 (no deprecations). TYPO3 APIs are version-switched (code-read).
- User-verified working as expected in **Composer and Classic** (non-Composer) setups. Live TYPO3: 14.3.7 (Classic, DDEV); v12 / v13 not live-tested here.

## Dependencies

| Package | Constraint | Why |
|---|---|---|
| `typo3/cms-core` | see above | core |
| `georgringer/news` / `news` | see above | comments belong to news records; controller injects `NewsRepository` |
| `nitsan/ns-license` / `ns_license` | `^14.5.4` / `14.5.4-14.9.99` | TYPO3 Shop module + free-trial deep link (see `features/premium-cta.md`) |
| jQuery | bundled | `custom.js` needs it; 3.6.3 (default) or 2.2.4 shipped in `Resources/Public/js/` |

## Tooling

- No PHPUnit, PHPStan, CS fixer or package.json in the repo.
- CI: `.github/workflows/ter-release.yml` (publishes tags to TER) and `t3planet-ai-bot.yml` (calls the
  shared T3Planet AI issue-resolver workflow).
- `.gitattributes` export-ignores `.crowdin.yml`, `.github/`, `.gitignore`, `AGENTS.md`, `CLAUDE.md`, `context/`,
  `tasks/` and itself, so they are not in the GitHub/Composer archive or the TER package (the TER release uploads
  the GitHub tag archive).
- Translations: Crowdin (`.crowdin.yml`), English sources `Resources/Private/Language/*.xlf`; de/es/fr/tr
  files are generated.
- User docs: `Documentation/` (RST) plus the external T3Planet docs (see `docs-map.md`).

## Conventions

- Extbase plugin `Newscomment` registered as content element (`ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT`),
  CType `nsnewscomments_newscomment`. No backend module.
- Labels via `<f:translate>` / `LLL:` from `locallang.xlf` (frontend + premium strip) and `locallang_db.xlf`
  (TCA, FlexForm, preview).
- Frontend CSS/JS in lower-case folders `Resources/Public/css/` and `Resources/Public/js/`.
- Backend premium strip CSS: `Resources/Public/css/backend-premium-strip.css` (`light-dark()` colours).

## Known issues (existing, not fixed)

Behaviour details: `features/comments-plugin.md`. All code-read unless marked otherwise.

Security / data:

- Captcha is **off by default**: the setting is called `captcha` but means "Disable Captcha"
  (FlexForm label `flexform.captcha`); static constant `captcha = 1`, site set default `true`.
- Comments go live immediately unless `approveComment` is enabled (`hidden` = 1 until an editor unhides
  the record on the news Comments tab). Dedicated approval module / e-mail approval remain premium.

Runtime:

- `getCommentsByNews()` respects the storage page; `CountViewHelper` / `LastCommentViewHelper` don't, so counts
  can include comments the list doesn't show.
- Static TypoScript adds style.css, font-awesome, jQuery and custom.js to **every** page
  (`page.includeCSS` / `includeJSFooter`); `Layouts/Default.html` adds the same files again via `f:asset`
  on plugin pages.

Housekeeping:

- `Configuration/Services.yaml` doesn't exclude `Classes/Domain/Model/*` from autowiring.
- `ext_tables.sql` still has unused `cruser_id` and the full legacy `t3ver_*` column set.
- README badge says v13.0.1 / TYPO3 13; links use `t3planet.com` while the sheet uses `t3planet.de`.
- `Documentation/Introduction` claims TYPO3 6.x–9.x compatibility and admin e-mail notifications (not in the
  free code).
- `ter-release.yml` uses PHP 7.4 and the deprecated `::set-output`.
- `composer.json` licence `GPL-2.0+` is a deprecated SPDX id (`composer validate` warning).
