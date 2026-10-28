# ns_news_comments — Architecture

## Registration

| What | Where |
|---|---|
| Plugin `Newscomment` (CType `nsnewscomments_newscomment`), actions `list, new, create`, all uncached (`new` has no `newAction()` in the controller) | `ext_localconf.php` |
| FormEngine node resolver `1791957358` on `flexFormNoTabsContainer` (premium strip) | `ext_localconf.php` |
| Content element, "Configuration" tab with `pi_flexform,pages`, FlexForm for the CType. v14: `columnsOverrides` string `ds`; v12/v13: `addPiFlexFormValue()` | `Configuration/TCA/Overrides/tt_content.php` |
| Static template "News Comment" | `Configuration/TCA/Overrides/sys_template.php` |
| Site set `nitsan/ns-news-comments` | `Configuration/Sets/NsNewsComments/` |
| New content element wizard entry (group `plugins`) + Page module preview template | `Configuration/page.tsconfig` (auto-loaded globally; the set's `page.tsconfig` imports it again) |
| Icon `ext-ns-comment-icon` | `Configuration/Icons.php` |
| Upgrade wizard `txNsNewsCommentsPluginMigration` (`CType=list` + `list_type` → CType) | `Classes/Updates/PluginMigration.php` |

## Classes

| Class | Role |
|---|---|
| `Controller\CommentController` | `initializeAction()` resolves news uid + HMAC-checked storage pid; `listAction()`; `createAction()` (JSON; captcha + parent checks) |
| `Domain\Model\Comment` | Comment entity; `childcomment` ObjectStorage (PHP attribute + annotation cascade remove) for replies |
| `Domain\Repository\CommentRepository` | `getCommentsByNews()`, `getLastCommentOfNews()`, `getCountOfComments()` |
| `ViewHelpers\CountViewHelper`, `LastCommentViewHelper` | For news templates: comment count / last comment by `newsuid` (repository injected) |
| `ViewHelpers\Be\PremiumStripViewHelper` | Page module preview strip (see `features/premium-cta.md`) |
| `Backend\PremiumStrip` | Shared strip HTML + trial URL |
| `Form\Resolver\PremiumStripFlexFormNoTabsContainerResolver`, `Form\Container\PremiumStripFlexFormNoTabsContainer` | Edit-form strip above the plugin options |
| `Updates\PluginMigration` | Upgrade wizard |

All of `Classes/*` is autowired (`Configuration/Services.yaml`).

## Tables

`tx_nsnewscomments_domain_model_comment` (`ext_tables.sql`, TCA `hideTable = true`):

- `newsuid` (news uid), `comment` (parent comment uid, 0 = top level), `childcomment` (reply count/relation),
  `username`, `usermail`, `description`, `paramlink` (detail URL with `#comments-<uid>`), `terms`.
- Standard `hidden`, `starttime`/`endtime`/`crdate` (`type=datetime`), `deleted`, language fields. SQL still has unused legacy `cruser_id` and `t3ver_*` columns.

`tx_news_domain_model_news.comments` — inline field (`foreign_field = newsuid`, no "new" button) shown in an
extra tab of the news record. This is the only backend UI for comments.

## Views and assets

| File | Use |
|---|---|
| `Templates/Comment/List.html` | Comment list + form (`f:form` with `pageType='1730800496'`) |
| `Partials/Comment/Comment.html` | One comment, recursive for replies (`description` is escaped + `nl2br`) |
| `Partials/Comment/FormFields.html` | Name, e-mail, text, captcha, terms, hidden `newsuid` / `parentId` / `pageid` |
| `Layouts/Default.html` | Wraps in `.tx_nsnewscomments`, adds CSS/JS via `f:asset` |
| `Backend/NsNewscomment.html` (v12/13), `Backend/NsNewscommentV14.html` (v14) | Page module preview: premium strip + FlexForm settings table. `mod.web_layout.tt_content.preview.nsnewscomments_newscomment` picks the V14 file under `[typo3.branch >= "14.0"]`; it reads `record.pi_flexform.sheets.sDEF.settings` (v14 only assigns `record`), the v12/13 file `pi_flexform_transformed.settings` (code-read) |
| `Public/PHP/captcha.php`, `verify.php`, `phptextClass.php` | Image captcha + verification (own PHP session `ns_news_comments_captcha`) |
| `Public/js/custom.js` | AJAX submit, reply form, client-side validation, captcha check |
| `Public/css/style.css`, `font-awesome.min.css`, `backend-premium-strip.css` | Frontend / backend styles |

## TypoScript

- `plugin.tx_nsnewscomments.view|settings` (constants in `Configuration/TypoScript/constants.typoscript`,
  set settings `nsNewsComments.*`).
- `nsNewsCommentsAjaxCall` PAGE, `typeNum = 1730800496`, runs the plugin (`controller = Comment`, `action = create`) and copies `plugin.tx_nsnewscomments.settings`. Defined inline in static and Site Set setup; also injected from `ext_localconf.php` so the typeNum exists on Site Set-only v13/v14 before the TypoScript include-tree cache is built.
- `lib.tx_nsnewscomments.comments` USER object for embedding the list in news templates (`action = list`).
