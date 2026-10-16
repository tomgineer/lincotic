# Lincotic

### Your links, organized into personal hubs.

Lincotic is a focused, self-hosted link library for individuals and small teams that have outgrown browser bookmark folders. Every user gets an independent hub for organizing, searching, sharing, and maintaining useful links.

Save with context. Find things quickly. Share only what should be public.

![Lincotic dashboard showing searchable collections, filters, link insights, and admin tools](lincotic.webp)

## One installation, multiple personal hubs

Lincotic keeps every user's library separate while making public resources easy to browse.

- The configured root user's hub is available at `/`.
- Every active user has a public route at `/@username`.
- Search, filters, collections, link totals, and visit totals are scoped to the selected user.
- Signing in takes users directly to their own hub.
- Inactive accounts and their outbound link redirects are not publicly accessible.

The root hub is selected with `lincotic.rootUsername` in the environment configuration. Usernames contain lowercase letters and numbers so hub URLs remain simple and predictable.

## A clear permission model

Public browsing, personal ownership, and account administration are separate capabilities.

| Capability | Visitor | User | Administrator |
| --- | --- | --- | --- |
| Browse public content in active hubs | Yes | Yes | Yes |
| View hidden content in own hub | No | Yes | Yes |
| Edit links and collections in own hub | No | Yes | Yes |
| View or edit another user's hidden content | No | No | No |
| Create and manage user accounts | No | No | Yes |

Administrator access does not override library ownership. An administrator can create accounts, assign the `user` or `admin` role, update credentials, and activate or deactivate accounts, but each user's private content remains visible and editable only to its owner.

## Organize links with purpose

Build a library around the way you think and work.

- Group links into named collections with descriptions and custom SVG icons.
- Feature the collections that belong on the main dashboard.
- Reorder collections to reflect your priorities.
- Recover links without a collection through the dedicated **Unassigned** view.
- Add titles and notes that preserve why a resource matters.
- Mark individual links or complete collections as hidden.

Each link and collection belongs to one user. Ownership checks also apply to editing requests, so one account cannot load, change, move, reorder, or delete another account's content.

## Find the right link in seconds

Search works inside the current hub and matches link titles, URLs, and descriptions. Results can include links that are not assigned to a collection.

Focused controls let you narrow a hub by:

- Collection, including featured and unassigned views
- Favorite, pending review, or unflagged status
- High, medium, low, or default importance

Sort links by importance, title, date added, date updated, popularity, most recent visit, or oldest first.

## Turn bookmarks into a working dashboard

Lincotic adds useful signals to every saved destination.

- **Importance levels** separate urgent resources from everyday references.
- **Favorites** keep essential destinations easy to isolate.
- **Pending review** identifies links that need another look.
- **Visit counts** show which resources are used most often.
- **Last visited timestamps** bring recent activity into view.
- **Hidden status** keeps selected content available only to its owner.

Hub totals respect the same visibility rules as the link list, so private content does not leak through public counters.

## Edit without leaving the hub

Owners can enable edit mode and manage their library through focused, responsive dialogs.

- Create, update, and remove links and collections.
- Move links between collections.
- Add descriptions, importance, favorites, review status, and visibility settings.
- Paste or test destinations directly from the link editor.
- Reorder collections from the interface.

Standard users have full control of their own library. They do not need an administrator role to manage their content.

## Share public resources, protect private ones

Visitors can browse the visible content of any active user's hub without an account. Hidden links and links inside hidden collections are available only to the hub owner, including when someone tries to open a direct outbound redirect.

Authentication and roles are powered by [CodeIgniter Shield](https://github.com/codeigniter4/shield). Public hub pages use owner-specific cache namespaces, and edits invalidate only the affected user's cached pages. This keeps public browsing fast without mixing content between users.

Because Lincotic is self-hosted, the application, database, accounts, link data, and usage data stay on infrastructure you control.

## Useful tools in the same interface

Lincotic includes four lightweight utilities alongside the link library.

### Password Generator

Generate standard or symbol-enhanced passwords in multiple lengths. Passwords use a cryptographically secure random source, are never stored, and can be copied with one click.

### Date & Time Calculator

See local time, ISO week information, calendar progress, Unix time, period countdowns, and long-range milestones in a live dashboard.

### Hash Generator

Turn text into MD5, SHA-1, SHA-256, SHA-384, and SHA-512 digests. Whitespace handling is explicit, byte count is visible, and every result is ready to copy.

### Work Time Tracker

Track an eight-hour net workday with automatic break deductions, progress feedback, and an estimated finish time. The selected start time is kept in browser local storage.

## Designed for everyday use

Lincotic is compact, responsive, and direct. The interface adapts from wide workstations to mobile screens, keeps primary navigation within reach, and simplifies controls when space is limited. Clear visual states, semantic controls, keyboard-friendly interactions, and focused editing flows make it comfortable to use throughout the day.

It is a natural fit for:

- Personal knowledge and reference libraries
- Shared start pages with individual user spaces
- Homelabs and infrastructure dashboards
- Project, research, and client resource collections
- Small teams that need public sharing and private ownership

## Why self-host Lincotic?

- **Ownership:** Keep links, accounts, and activity data on systems you control.
- **Separation:** Give every user an independent library and public hub.
- **Privacy:** Keep hidden content private even from other administrators.
- **Speed:** Reach useful destinations through search, filters, and activity-based sorting.
- **Focus:** Use a dedicated link workspace without the weight of a general-purpose knowledge platform.
- **Freedom:** Inspect, adapt, and extend an MIT-licensed codebase.

## Built on a maintainable foundation

Lincotic combines [CodeIgniter 4](https://codeigniter.com/) and CodeIgniter Shield on the backend with [Tailwind CSS](https://tailwindcss.com/), [daisyUI](https://daisyui.com/), and [esbuild](https://esbuild.github.io/) on the frontend. MySQL or MariaDB provides persistent storage.

The application uses server-rendered pages, focused JavaScript enhancements, explicit validation, user-scoped data access, visibility-aware redirects, isolated public caches, and automated tests around core behavior.

## Open source

Lincotic is released under the [MIT License](LICENSE). Explore the source, adapt it to your workflow, and help make link management more intentional.

[View the repository](https://github.com/tomgineer/lincotic) | [Report an issue](https://github.com/tomgineer/lincotic/issues)
