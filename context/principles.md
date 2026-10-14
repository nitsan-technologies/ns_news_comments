# ns_news_comments — Operating Principles

*Every change to code, context or docs must serve the goals below.*

---

## Goal 1 — Nothing that works may break

The extension is declared for TYPO3 v12, v13 and v14 at the same time (Composer and Classic mode), and for
EXT:news 11–14.

- Every change must keep all three majors working. Follow the existing version switches
  (`VersionNumberUtility` in `CommentController`, the `[typo3.branch >= "14.0"]` condition in
  `Configuration/page.tsconfig`, `enum_exists(IconSize::class)` in `Backend/PremiumStrip.php`) instead of
  inventing new ones.
- FormEngine node resolvers must work with the v12 constructor `(NodeFactory, array $data)` and the v13+
  `setData()` call (see `Form/Resolver/PremiumStripFlexFormNoTabsContainerResolver.php`).
- Prefer the smallest change that fixes the root cause. No drive-by refactors.
- Run `tasks/run-quality.md` and re-test the affected flow in a frontend and backend before claiming done.

## Goal 2 — Token-cost efficiency (agent context)

- **DRY context:** each fact lives in exactly ONE file. Reference, don't copy.
- **Load on demand:** read `context/features/<area>.md` for the task, not everything.
- **Prune as you go:** keep `context/session-state.md` short.

## Goal 3 — Accuracy and trust

- Answer from context + code, never from invented API, table or field names.
- Mark facts as "verified live" or "code-read". Conflicting facts across files = fix the source file.

---

## Guardrails (non-negotiable)

- Comments store visitor name and e-mail (`username`, `usermail`); don't expose `usermail` in the frontend.
- Keep the static TypoScript constants (`Configuration/TypoScript/`) and the site set settings
  (`Configuration/Sets/NsNewsComments/`) in sync when adding or renaming a setting.
- The comment form and the AJAX `create` call share page type `1730800496`; change both or neither.
- Do not change ns_license from an ns_news_comments task; reuse its module and trial modal.
- All UI text goes into `Resources/Private/Language/locallang*.xlf` (English source only; de/es/fr/tr
  come from Crowdin), no hard-coded labels.

---

## Apply this whenever you

- Edit `context/` or `AGENTS.md`
- Land a feature or bugfix (update the matching `context/features/*.md` + `session-state.md`)
