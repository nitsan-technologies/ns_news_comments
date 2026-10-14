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
- If `tx_news_pi1` is missing, from `$_SESSION['params']['originalSettings']['singleNews']`, written by the
  news `overrideSettings` hook (`Hooks/NewsController`), for news plugins configured with a fixed single news.

## Storage pid

1. `persistence.storagePid` from the plugin's "Record Storage Page" (`pages`) or TypoScript, else
2. request argument `tx_nsnewscomments_newscomment[Storagepid]` (the form sends `{pid}`), else
3. `settings.storagePid`, else
4. current page uid.

## Plugin options (FlexForm, one sheet "Main Configuration")

`settings.custom` (use custom formats), `customdate`, `customtime`, `dateFormat`, `timeFormat`, `captcha`
("Disable Captcha"). The premium strip sits above these fields (see `premium-cta.md`).

## TypoScript / site set settings

`termsRequired`, `termsTypolinkParameter`, `dateFormat`, `timeFormat`, `captcha`, `storagePid`, asset paths
(`StyleCss`, `FontAwesomeCss`, `CustomJS`, `Minjs`), view paths. Keep `Configuration/TypoScript/constants.typoscript`
and `Configuration/Sets/NsNewsComments/settings.definitions.yaml` in sync.

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
