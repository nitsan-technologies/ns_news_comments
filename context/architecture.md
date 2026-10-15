# ns_news_comments — Architecture

## Registration

| What | Where |
|---|---|
| Plugin `Newscomment` (CType `nsnewscomments_newscomment`), actions `list, new, create`, all uncached (`new` has no `newAction()` in the controller) | `ext_localconf.php` |
| News hook `overrideSettings` → `Hooks\NewsController->modify` | `ext_localconf.php` |
| FormEngine node resolver `1791957358` on `flexFormNoTabsContainer` (premium strip) | `ext_localconf.php` |
| Content element, "Configuration" tab with `pi_flexform,pages`, FlexForm for the CType | `Configuration/TCA/Overrides/tt_content.php` |
| Static template "News Comment" | `Configuration/TCA/Overrides/sys_template.php` |
| Site set `nitsan/ns-news-comments` | `Configuration/Sets/NsNewsComments/` |
| New content element wizard entry (group `plugins`) + Page module preview template | `Configuration/page.tsconfig` (auto-loaded globally; the set's `page.tsconfig` imports it again) |
| Icon `ext-ns-comment-icon` | `Configuration/Icons.php` |
| Upgrade wizard `txNsNewsCommentsPluginMigration` (`CType=list` + `list_type` → CType) | `Classes/Updates/PluginMigration.php` |

## Classes

| Class | Role |
|---|---|
| `Controller\CommentController` | `initializeAction()` resolves news uid + storage pid; `listAction()`; `createAction()` (JSON response) |
| `Domain\Model\Comment` | Comment entity; `childcomment` ObjectStorage (cascade remove) for replies |
| `Domain\Repository\CommentRepository` | `getCommentsByNews()`, `getLastCommentOfNews()`, `getCountOfComments()` |
| `Hooks\NewsController` | Stores news plugin settings in `$_SESSION['params']` |
| `ViewHelpers\CountViewHelper`, `LastCommentViewHelper` | For news templates: comment count / last comment by `newsuid` |
| `ViewHelpers\Be\PremiumStripViewHelper` | Page module preview strip (see `features/premium-cta.md`) |
| `Backend\PremiumStrip` | Shared strip HTML + trial URL |
| `Form\Resolver\PremiumStripFlexFormNoTabsContainerResolver`, `Form\Container\PremiumStripFlexFormNoTabsContainer` | Edit-form strip above the plugin options |
| `Updates\PluginMigration` | Upgrade wizard |

All of `Classes/*` is autowired (`Configuration/Services.yaml`).

## Tables

`tx_nsnewscomments_domain_model_comment` (`ext_tables.sql`, TCA `hideTable = true`):

- `newsuid` (news uid), `comment` (parent comment uid, 0 = top level), `childcomment` (reply count/relation),
  `username`, `usermail`, `description`, `paramlink` (detail URL with `#comments-<uid>`), `terms`.
- Standard `hidden`, `starttime`, `endtime`, `deleted`, language fields; legacy `cruser_id`, `t3ver_*`.

`tx_news_domain_model_news.comments` — inline field (`foreign_field = newsuid`, no "new" button) shown in an
extra tab of the news record. This is the only backend UI for comments.

## Views and assets

| File | Use |
|---|---|
| `Templates/Comment/List.html` | Comment list + form (`f:form` with `pageType='1730800496'`) |
| `Partials/Comment/Comment.html` | One comment, recursive for replies |
| `Partials/Comment/FormFields.html` | Name, e-mail, text, captcha, terms, hidden `newsuid` / `parentId` / `pageid` |
| `Layouts/Default.html` | Wraps in `.tx_nsnewscomments`, adds CSS/JS via `f:asset` |
| `Backend/NsNewscomment.html` (v12/13), `Backend/NsNewscommentV14.html` (v14) | Page module preview: premium strip + FlexForm settings table. `mod.web_layout.tt_content.preview.nsnewscomments_newscomment` picks the V14 file under `[typo3.branch >= "14.0"]`; it reads `record.pi_flexform.sheets.sDEF.settings` (v14 only assigns `record`), the v12/13 file `pi_flexform_transformed.settings` (code-read) |
| `Public/PHP/captcha.php`, `verify.php`, `phptextClass.php` | Image captcha + verification (own PHP session `ns_news_comments_captcha`) |
| `Public/js/custom.js` | AJAX submit, reply form, client-side validation, captcha check |
| `Public/css/style.css`, `font-awesome.min.css`, `backend-premium-strip.css` | Frontend / backend styles |

## TypoScript

- `plugin.tx_nsnewscomments.view|settings` (constants in `Configuration/TypoScript/constants.typoscript`,
  set settings `nsNewsComments.*`).
- `nsNewsCommentsAjaxCall` PAGE, `typeNum = 1730800496`, runs the plugin (`create`).
- `lib.tx_nsnewscomments.comments` USER object for embedding the list in news templates.
