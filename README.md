# Lincotic

### Your links, finally working like a system.

Lincotic is a focused, self-hosted link library for people who have outgrown browser bookmark folders. It turns saved URLs into a structured, searchable workspace, complete with collections, priorities, review states, visibility controls, visit insights, and a polished administration experience.

Save less blindly. Find things faster. Keep the links that matter genuinely useful.

![Lincotic dashboard showing searchable collections, filters, link insights, and admin tools](lincotic.webp)

## Bookmarks are easy to save. Lincotic makes them easy to use.

A bookmark without context quickly becomes noise. Lincotic gives every link a clear place, a useful state, and enough information to remain valuable long after it was added.

Instead of scrolling through an endless list, you can move naturally from broad collections to precise results: search across your library, filter by intent, sort by real activity, and spot the links that need attention at a glance.

## Organize with purpose

Build a library around the way you actually think and work.

- Group links into named collections with descriptions and custom SVG icons.
- Feature the collections that belong on your everyday dashboard.
- Reorder collections to shape the workspace around your priorities.
- Recover links without a collection through a dedicated **Unassigned** view.
- Add titles and notes that preserve why a resource mattered in the first place.

Lincotic gives structure without turning link management into a project of its own.

## Find the right link in seconds

Search is global and practical, matching link titles, URLs, and descriptions. When browsing, focused controls let you narrow the library by collection, flag, and importance.

Sort results by:

- Title, ascending or descending
- Date added
- Date updated
- Most visited
- Most recently visited
- Oldest first

Whether you remember a name, part of a domain, a note, or simply that you used something recently, Lincotic gives you a direct path back to it.

## Turn a link list into a decision surface

Not every bookmark deserves the same attention. Lincotic makes that distinction visible.

- **Importance levels** separate high-priority resources from everyday references.
- **Favorites** keep essential destinations easy to isolate.
- **Pending review** flags links that need a second look.
- **Hidden links** remain available to signed-in users while staying out of guest views.
- **Visit counts and last-visited timestamps** reveal which resources are actually useful.

These signals make the dashboard more than an archive: it becomes a working view of what matters now.

## Manage everything without leaving the dashboard

Administrative controls stay out of the way until they are needed. Enable edit mode to manage the library through focused, responsive dialogs instead of navigating through a separate back office.

Administrators can:

- Create, update, and remove links and collections.
- Move links between collections and enrich them with descriptions and status metadata.
- Paste or test destinations directly from the link editor.
- Reorder collections from the interface.
- Create and manage user accounts, roles, and active status.

The result is a fast editorial workflow that keeps attention on the library itself.

## Share the useful parts, protect the rest

Lincotic supports both open browsing and authenticated access. Public visitors can use the visible link library without an account, while selected links can be restricted to signed-in users, including protection on direct outbound redirects.

Authentication and role-based administration are powered by CodeIgniter Shield. Cache-aware public pages keep repeat visits fast, while library changes invalidate stale guest views so published information stays current.

Because Lincotic is self-hosted, the application, database, accounts, and usage data remain under your control.

## More than bookmarks

Lincotic also brings four frequently useful tools into the same interface:

### Password Generator

Generate fresh standard and symbol-enhanced passwords across multiple lengths. Passwords are created with a cryptographically secure random source, are never stored, and can be copied with one click.

### Date & Time Calculator

See local time, ISO week information, calendar progress, Unix time, period countdowns, and long-range milestones in a live dashboard that updates every second.

### Hash Generator

Turn one text input into MD5, SHA-1, SHA-256, SHA-384, and SHA-512 digests. Whitespace handling is explicit, byte count is visible, and every result is ready to copy.

### Work Time Tracker

Track an eight-hour net workday with automatic break deductions, progress feedback, and an estimated finish time. The selected start time stays in the browser through local storage.

## Designed for everyday use

Lincotic is intentionally compact, responsive, and direct. The interface adapts from wide workstations to mobile screens, keeps primary navigation within reach, and reduces controls when space is limited. Clear visual states, semantic controls, keyboard-friendly interactions, and focused editing flows make it comfortable to use throughout the day.

It is a natural fit for:

- Personal knowledge and reference libraries
- Browser start pages and daily launchpads
- Homelabs and infrastructure dashboards
- Project, research, and client resource collections
- Small shared environments that need controlled administration

## Why self-host Lincotic?

- **Ownership**: keep your link data and accounts on infrastructure you control.
- **Clarity**: use purposeful metadata instead of deep, fragile folder trees.
- **Speed**: reach important destinations through search, filters, and activity-based sorting.
- **Privacy**: separate guest-visible resources from authenticated-only links.
- **Focus**: get a dedicated link workspace without the weight of a general-purpose knowledge platform.
- **Freedom**: inspect, adapt, and extend an MIT-licensed codebase.

## Built on a modern, maintainable foundation

Lincotic combines [CodeIgniter 4](https://codeigniter.com/) and [Shield](https://github.com/codeigniter4/shield) on the backend with [Tailwind CSS](https://tailwindcss.com/), [daisyUI](https://daisyui.com/), and [esbuild](https://esbuild.github.io/) on the frontend. MySQL or MariaDB provides persistent storage.

The project favors a small, understandable architecture: server-rendered pages, focused JavaScript enhancements, explicit validation, responsive components, and automated tests around core behavior.

## Open source

Lincotic is released under the [MIT License](LICENSE). Explore the source, shape it around your workflow, and help make link management more intentional.

[View the repository](https://github.com/tomgineer/lincotic) · [Report an issue](https://github.com/tomgineer/lincotic/issues)
