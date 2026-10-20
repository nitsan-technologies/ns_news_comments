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
| `ext_emconf.php` | `12.4.37-14.9.99` | `11.0.0-14.9.99` | not declared |
| `composer.json` | `^12.4.37 \|\| ^13 \|\| ^14` | `^11 \|\| ^12 \|\| ^13 \|\| ^14` | not declared |
| ns_license 14.5.4 (dependency) | `12.4.18-14.9.99` | — | — |

- Code uses typed properties, `catch (X)` without variable and `enum_exists()` → PHP 8.1+ (TYPO3 12 minimum anyway).
- Verified live only on TYPO3 14.3.7 (Classic mode, DDEV). v12 / v13 are code-read.

## Dependencies

| Package | Constraint | Why |
|---|---|---|
| `typo3/cms-core` | see above | core |
| `georgringer/news` / `news` | see above | comments belong to news records; controller injects `NewsRepository`; hook on news `overrideSettings` |
| `nitsan/ns-license` / `ns_license` | `^14.5.4` / `14.5.4-14.9.99` | TYPO3 Shop module + free-trial deep link (see `features/premium-cta.md`) |
| `typo3/cms-install` | not declared | `CommentController` and `Hooks/NewsController` use `TYPO3\CMS\Install\Service\SessionService` |
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

- Captcha is checked only in the browser (`custom.js` → `Resources/Public/PHP/verify.php`).
  `createAction()` never validates it, so a direct POST to page type `1730800496` skips it.
- Captcha is **off by default**: the setting is called `captcha` but means "Disable Captcha"
  (FlexForm label `flexform.captcha`); static constant `captcha = 1`, site set default `true`.
- When no `persistence.storagePid` is configured, `initializeAction()` takes the storage pid from the request
  (`tx_nsnewscomments_newscomment[Storagepid]`, sent by the form), so the client chooses where comments are stored.
- `createAction()` loads `parentId` with `findByUid()` without a null check or news match: an invalid id
  fatals, a foreign id attaches the reply to another news item's comment.
- Comments go live immediately (`hidden` = 0); moderation is a premium feature.

Runtime:

- `Hooks/NewsController::modify()` and `initializeAction()` start the Install tool `SessionService` on every
  news / comment request to pass the news uid via `$_SESSION`; the reading side is dead code
  (`features/comments-plugin.md` → News uid resolution).
- `getCommentsByNews()` respects the storage page; `CountViewHelper` / `LastCommentViewHelper` don't, so counts
  can include comments the list doesn't show.
- Static TypoScript adds style.css, font-awesome, jQuery and custom.js to **every** page
  (`page.includeCSS` / `includeJSFooter`); `Layouts/Default.html` adds the same files again via `f:asset`
  on plugin pages.
- TypoScript `switchableControllerActions` (AJAX page type, `lib.tx_nsnewscomments.comments`) is ignored since v12.

Housekeeping:

- `Configuration/Services.yaml` doesn't exclude `Classes/Domain/Model/*` from autowiring.
- `Configuration/TCA/Overrides/tt_content.php` uses `ExtensionManagementUtility::addPiFlexFormValue()`,
  deprecated in TYPO3 14.0 (#107047, removed in v15): triggers `E_USER_DEPRECATED` on v14.
- `ext_tables.sql` still has `cruser_id` and the full legacy `t3ver_*` column set; `ext_tables.php` has a
  TYPO3 v11-only branch that can't run (minimum is 12.4.37).
- README badge says v13.0.1 / TYPO3 13; links use `t3planet.com` while the sheet uses `t3planet.de`.
- `Documentation/Introduction` claims TYPO3 6.x–9.x compatibility and admin e-mail notifications (not in the
  free code).
- `ter-release.yml` uses PHP 7.4 and the deprecated `::set-output`.
- `composer.json` licence `GPL-2.0+` is a deprecated SPDX id (`composer validate` warning).
