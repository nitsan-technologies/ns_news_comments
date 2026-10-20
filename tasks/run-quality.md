# Run quality checks

The repo has no PHPUnit, PHPStan or CS config. Available checks:

```bash
# PHP syntax, once per installed PHP version (8.1+)
for f in $(find . -name "*.php" -not -path "./.git/*"); do php -l "$f"; done

# composer.json (expected warning: deprecated licence id "GPL-2.0+")
composer validate --no-check-publish

# XLF well-formedness
for f in Resources/Private/Language/*.xlf; do xmllint --noout "$f"; done
```

Fluid templates can't be checked with `xmllint` (conditions like `{a} && {b}` are not valid XML).
After adding or renaming classes in Classic mode run `typo3 dumpautoload`, then `typo3 cache:flush`.

## Manual test (per TYPO3 major 12 / 13 / 14)

Setup: EXT:news with a detail page; static template "News Comment" or site set `nitsan/ns-news-comments`;
"News Comment" content element on the news detail page.

1. Backend edit form of the content element → "Configuration" tab: one premium strip directly below
   "Plugin Options", above "Custom Date Format". Other tabs and other plugins' FlexForms have none.
2. Page module: one strip at the top of the News Comment preview, above the settings table. Other content
   elements have none.
3. "Start Free Trial" opens the ns_license module and its trial modal for `ns_news_comments`
   (system maintainer). Check light and dark backend themes.
4. Frontend, news detail page: form and list render. Submit a comment → thank-you message, comment appears
   without reload, `paramlink` ends with `#comments-<uid>`. Reply to it → nested under the parent.
5. Untick "Disable Captcha" in the plugin → captcha image shows; a wrong code blocks the submit.
6. `termsRequired = 1` → terms checkbox required.
7. Backend: open the news record → "Comments" tab lists the comments; hide one → gone in the frontend.

Remove test comments, content elements and temporary backend users afterwards.
