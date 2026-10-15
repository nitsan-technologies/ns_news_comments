# Feature — Comments plugin

*Status: Done. Code-read; frontend not tested live in this setup (the Camino site has no news detail page
with the plugin).*

## Setup

1. Install EXT:news and add its detail plugin to a page.
2. Include the static template "News Comment" or add the site set `nitsan/ns-news-comments`.
3. Add the "News Comment" content element on the **same** news detail page.

Without a news uid the list action shows the flash message `tx_nsnewscomments_domain_model_comment.errorMessage`
("No News Entry Found").

## News uid resolution (`initializeAction()`)

- From `tx_news_pi1[news]` in GET/POST.
- Intended fallback: `$_SESSION['params']['originalSettings']['singleNews']`, written by the news
  `overrideSettings` hook (`Hooks/NewsController`), for news plugins configured with a fixed single news.
  The fallback is dead code: `$requestData['tx_news_pi1'] ?? []` is never `null`, so the `is_null()` branch never
  runs and a missing `tx_news_pi1` gives news uid 0 (code-read).

## Storage pid

1. `persistence.storagePid` from the plugin's "Record Storage Page" (`pages`) or TypoScript, else
2. request argument `tx_nsnewscomments_newscomment[Storagepid]` (the form sends `{pid}`), else
3. `settings.storagePid`, else
4. current page uid.

## Plugin options (FlexForm, one sheet "Main Configuration")

`settings.custom` (use custom formats), `customdate`, `customtime`, `dateFormat`, `timeFormat`, `captcha`
("Disable Captcha"). The premium strip sits above these fields (see `premium-cta.md`).

Code-read details (`Configuration/FlexForm/FlexForm.xml`):

- `custom` (check, reloads the form) switches between the required text fields `customdate` / `customtime`
  (shown only when ticked) and the radio fields `dateFormat` (`F j, Y`, `Y-m-d`, `m/d/Y`, `d/m/Y`; default
  `F j, Y` with comma, unlike the TypoScript default `F j Y`) / `timeFormat` (`g:i a`, `g:i A`, `H:i`).
  `Partials/Comment/Comment.html` uses the custom formats only if `custom`, `customdate` and `customtime` are set.
- `captcha` (check) defaults to `1`, i.e. captcha disabled.

## TypoScript / site set settings

`termsRequired`, `termsTypolinkParameter`, `dateFormat`, `timeFormat`, `captcha`, `storagePid`, asset paths
(`StyleCss`, `FontAwesomeCss`, `CustomJS`, `Minjs`), view paths. Keep `Configuration/TypoScript/constants.typoscript`
and `Configuration/Sets/NsNewsComments/settings.definitions.yaml` in sync.

The asset path constants / site settings have no effect (code-read): both `setup.typoscript` files hard-code
the paths in `page.includeCSS` / `includeJSFooter` (jQuery always 3.6.3), and `Layouts/Default.html` uses fixed
paths too. The site set's `setup.typoscript` imports the static setup, then maps only the view paths and
`termsRequired` … `storagePid` from `nsNewsComments.*`; the set has no dependencies.

## Comment flow

1. `listAction()` loads top-level comments (`comment = 0`) of the news, newest first; replies render recursively
   from `childcomment`.
2. If `settings.captcha` is `0` / empty, the form shows the image from `captcha.php` and `custom.js` checks the
   input against `verify.php` before submitting.
3. `custom.js` posts the form via AJAX to page type `1730800496` → `createAction()`:
   sets `crdate` and language, links the reply to `parentId`, persists, builds `paramlink` to the news detail
   page with `#comments-<uid>`, returns `{"<uid>": {"parentId": …, "comment": "comment"}}`.
4. JS reloads the list HTML (stripping captcha `<img>` sources so the session is not regenerated) and scrolls
   to the thank-you message.

New comments are visible immediately (no moderation in the free version). Editors hide or delete them in the
news record's "Comments" tab.

Known issues: `core.md` → Known issues.
