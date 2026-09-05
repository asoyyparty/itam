# Design — ITAM System (IT Asset & Infrastructure Management)

A locked design system for this app. Every page redesign reads this file before emitting code. Do not regenerate per page — extend or amend this file when the system needs to grow.

## Genre
`modern-minimal` (High-density B2B Enterprise IT Infrastructure & Asset Management dashboard)

## Macrostructure Family
- Marketing / Login pages: Stat-Led / Bento Grid
- App / Dashboard pages: Workbench (Header toolbar + Filter Bar + Data Grid + Glass Modal Stack)
- Content / Settings pages: Document-Led with Nav Pills Sidebar

## Theme
### Dark Mode (Default)
- `--color-paper`: `oklch(18% 0.02 260)` (#0f172a / Slate 900)
- `--color-paper-2`: `oklch(23% 0.025 260)` (#1e293b / Slate 800)
- `--color-ink`: `oklch(96% 0.01 260)` (#f8fafc / Slate 50)
- `--color-ink-2`: `oklch(72% 0.02 260)` (#94a3b8 / Slate 400)
- `--color-rule`: `rgba(148, 163, 184, 0.12)`
- `--color-accent`: `oklch(62% 0.22 260)` (#6366f1 / Indigo 500)
- `--color-accent-hover`: `oklch(55% 0.24 260)` (#4f46e5 / Indigo 600)
- `--color-focus`: `oklch(70% 0.18 245)` (#60a5fa / Blue 400)

### Light Mode
- `--color-paper`: `oklch(99% 0.005 260)` (#ffffff)
- `--color-paper-2`: `oklch(97% 0.008 260)` (#f8fafc)
- `--color-ink`: `oklch(15% 0.02 260)` (#0f172a)
- `--color-ink-2`: `oklch(45% 0.02 260)` (#64748b)
- `--color-rule`: `rgba(226, 232, 240, 0.8)`
- `--color-accent`: `oklch(55% 0.24 260)` (#4f46e5)

## Typography
- Display: `Outfit`, sans-serif, weight 600-700
- Body: `Inter`, sans-serif, weight 400-500
- Mono: `JetBrains Mono`, monospace, weight 400-600
- Display tracking: `-0.02em`
- All headers are STRICTLY roman (`font-style: normal`). No italic headers.

## Spacing (4-point scale)
- `--space-3xs`: 0.25rem (4px)
- `--space-2xs`: 0.5rem (8px)
- `--space-xs`: 0.75rem (12px)
- `--space-sm`: 1.0rem (16px)
- `--space-md`: 1.5rem (24px)
- `--space-lg`: 2.0rem (32px)
- `--space-xl`: 3.0rem (48px)

## Motion & Microinteractions
- `--ease-out`: `cubic-bezier(0.16, 1, 0.3, 1)`
- `--dur-short`: 180ms
- Hover states animate ONLY `background-color`, `border-color`, `box-shadow`, `transform` (max 1px translate). `transition: all` is strictly forbidden.
- Focus rings appear instantly (`0ms` delay on outline).
- Silent success for state changes; toast alerts only for async errors/confirmations.

## Component Voice
- Navigators & Toolbars: Glassmorphic cards with `backdrop-filter: blur(12px)` and 16px radius.
- Buttons: 14px border radius, 42px height, subtle gradient glow, 0 4px 14px shadow.
- Tables: Monospace IP/numeric fields with `font-variant-numeric: tabular-nums`, clean status pills.
- Dropdowns & Selects: Custom SVG chevron arrow with 38px right padding.
- Inputs & Password Groups: Seamless joined input-group without gap borders.

## What Pages MUST Share
- Global Admin Layout header & sidebar navigation.
- The `Outfit` display + `Inter` body + `JetBrains Mono` font stack.
- The `--color-accent` (Electric Indigo) anchor hue.
- Seamless input groups & custom SVG select dropdown styling.

## What Pages MAY Differ On
- Macrostructure archetype within the Workbench family (e.g. Settings uses side-nav pills, Dashboard uses Bento grid, Data Views use Workbench grid).
