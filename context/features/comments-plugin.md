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

From `tx_news_pi1[news]` in GET/POST (PSR-7 query + parsed body). `createAction()` always writes that value
onto the new comment and ignores the hidden form `newsuid`. Parent replies must belong to the same news uid
or the request returns HTTP 400.

## Storage pid

1. `persistence.storagePid` from the plugin's "Record Storage Page" (`pages`) or TypoScript, else
2. form argument `Storagepid` **only if** `storagePidHmac` matches (HMAC of the pid from `listAction()`), else
3. `settings.storagePid`, else
4. current page uid.

Unsigned / missing hmac values are ignored so the client cannot pick an arbitrary storage page.

## Plugin options (FlexForm, one sheet "Main Configuration")

`settings.custom` (use custom formats), `customdate`, `customtime`, `dateFormat`, `timeFormat`, `captcha`
("Disable Captcha"), `approveComment` (off by default). The premium strip sits above these fields
(see `premium-cta.md`).

Code-read details (`Configuration/FlexForm/FlexForm.xml`):

- `custom` (check, reloads the form) switches between the required text fields `customdate` / `customtime`
  (shown only when ticked) and the radio fields `dateFormat` (`F j, Y`, `Y-m-d`, `m/d/Y`, `d/m/Y`; default
  `F j, Y` with comma, unlike the TypoScript default `F j Y`) / `timeFormat` (`g:i a`, `g:i A`, `H:i`).
  `Partials/Comment/Comment.html` uses the custom formats only if `custom`, `customdate` and `customtime` are set.
- `captcha` (check) defaults to `1`, i.e. captcha disabled.
- `approveComment` (check) defaults to `0`. When on, `createAction` stores `hidden=1`.

## TypoScript / site set settings

`termsRequired`, `termsTypolinkParameter`, `dateFormat`, `timeFormat`, `captcha`, `approveComment`, `storagePid`, asset paths
(`StyleCss`, `FontAwesomeCss`, `CustomJS`, `Minjs`), view paths. Keep `Configuration/TypoScript/constants.typoscript`
and `Configuration/Sets/NsNewsComments/settings.definitions.yaml` in sync.

The asset path constants / site settings have no effect (code-read): both `setup.typoscript` files hard-code
the paths in `page.includeCSS` / `includeJSFooter` (jQuery always 3.6.3), and `Layouts/Default.html` uses fixed
paths too. The site set's `setup.typoscript` imports the static setup, then maps only the view paths and
`termsRequired` … `storagePid` from `nsNewsComments.*`; the set has no dependencies.

AJAX PAGE `nsNewsCommentsAjaxCall` (type `1730800496`, inline in static and Site Set setup, also registered from `ext_localconf.php`) and `lib.tx_nsnewscomments.comments` set
`pluginName` / `controller` / `action` (no `switchableControllerActions`) and copy plugin settings.

## Comment flow

1. `listAction()` loads top-level comments (`comment = 0`) of the news, newest first; replies render recursively
   from `childcomment`. Comment text is escaped, then `nl2br`.
2. If `settings.captcha` is `0` / empty, the form shows the image from `captcha.php` and `custom.js` checks the
   input against `verify.php` before submitting.
3. `custom.js` posts the form via AJAX to page type `1730800496` → `createAction()`:
   validates captcha server-side (session `ns_news_comments_captcha`) when captcha is
   required or `captcha_code` is posted; rejects empty/whitespace `username`, `usermail` and `description`
   (HTTP 400 `{error: validation}`) and stores the trimmed values; forces `newsuid` / `pageid`; links the
   reply to `parentId` only if that comment exists and belongs to the same news; when `approveComment` is `1`
   (plugin FlexForm, signed as `approval` + `approvalHmac`, or TypoScript / site set if hmac is absent)
   sets `hidden=1` before persist and returns `{status: pending}`; otherwise persists visible and returns
   `{"<uid>": {"parentId": …, "comment": "comment"}}`. Failures return HTTP 400 JSON.
4. JS: `{status: pending}` shows `form.approveMessage` and does not reload the list as a live comment.
   Otherwise it reloads the list HTML (stripping captcha `<img>` sources so the session is not regenerated)
   and scrolls to the thank-you message.

`approveComment` defaults to `0` (comments go live immediately). When enabled, new comments stay off the
frontend until an editor unhides them on the news record's Comments tab (Access / hidden). There is no
dedicated backend Comment module in the free version.

Known issues: `core.md` → Known issues.
