# AGENTS.md — ns_news_comments (News Comment)

Agent router for the News Comment free extension (comments for EXT:news detail pages). Load only what your
task needs.

> **Principles:** `context/principles.md`
> **Session log:** `context/session-state.md`
> **Doc index:** `context/docs-map.md`

---

## Always load

| File | Why |
|---|---|
| `context/principles.md` | Guardrails, "nothing that works may break" rule |
| `context/core.md` | Identity, TYPO3/PHP/news/ns_license constraints, tooling, known issues |

---

## Task router

| Task | Load |
|---|---|
| Frontend plugin, comment form, AJAX create, captcha, replies, TypoScript / site set settings | `context/features/comments-plugin.md` |
| Premium strip / free trial / ns_license dependency | `context/features/premium-cta.md` |
| Classes, tables, TCA, templates, assets, upgrade wizard | `context/architecture.md` |
| Lint / validate / manual test | `tasks/run-quality.md` |
| Update agent context after a change | `tasks/context-update.md` |

---

## Feature status

| Feature | Status | Agent entry |
|---|---|---|
| Comment list + form on the news detail page, nested replies, AJAX submit, image captcha, terms checkbox | Done | `context/features/comments-plugin.md` |
| Comments managed inline in the news record ("Comments" tab) | Done | `context/architecture.md` |
| Premium strip (plugin options + Page module preview) + ns_license free-trial deep link | Done (needs ns_license 14.5.4 release) | `context/features/premium-cta.md` |

---

## Quick commands

```bash
cd packages/ns_news_comments       # or typo3conf/ext/ns_news_comments in Classic mode
for f in $(find . -name "*.php" -not -path "./.git/*"); do php -l "$f"; done
composer validate --no-check-publish
for f in Resources/Private/Language/*.xlf; do xmllint --noout "$f"; done
```
