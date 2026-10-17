# Learn Mode

Learn Mode turns a collection of links into a collection of knowledge. Instead of sending someone directly to another website, Lincotic can first explain what the destination is, why it exists, how it is used, and what a person should know before opening it.

![Learn Mode showing the documentation attached to a saved link](images/learn_mode.webp)

A normal bookmark preserves a location. Learn Mode preserves the context around that location.

## Why it matters

Modern work happens across browser-based systems: administration panels, internal portals, ticketing tools, dashboards, cloud consoles, reporting platforms, customer systems, documentation sites, and dozens of specialist applications. Their names and URLs rarely explain enough on their own.

A new employee may not know which system to use, what it controls, whether VPN or SSO is required, or where access must be requested. More experienced employees may know the broad purpose of a tool but not a particular workflow, the owning team, the correct environment, or the workaround used twice a year. When that knowledge lives only in chat history or in one colleague's memory, every question has to be answered again.

Learn Mode places that missing explanation beside the link itself. The hub becomes both the place where work starts and the place where people learn how that work is done.

## A knowledge layer for organizations

Teams, departments, and entire organizations can use a link's documentation to capture information such as:

- What the system does and which business process depends on it.
- Who owns or supports it and when an issue should be escalated.
- Where and how a new user requests access.
- Required roles, approvals, VPN connections, SSO groups, or environments.
- The difference between production, staging, testing, and administrative entry points.
- First-day instructions and the most common workflows.
- Safe command examples, API requests, code snippets, templates, or boilerplate.
- Known limitations, recurring mistakes, and recovery steps.
- Links between a tool and the policies or procedures surrounding it.
- The date or circumstances under which the instructions should be reviewed.

This is useful during onboarding, but it is not limited to new employees. Operational details are easy to forget when a task is performed infrequently. A short note beside the destination can prevent the wrong environment from being used, an access request from going to the wrong team, or a fragile manual procedure from being rediscovered from scratch.

Learn Mode also lowers the cost of sharing knowledge. A colleague can share a Lincotic hub instead of sending a bare list of URLs and then explaining every item separately.

## A memory layer for personal links

The same feature works as a private annotation system for an individual library. Useful pages are often saved in the moment and become mysterious months later. The title still identifies the page, but the reason for saving it has disappeared.

Personal documentation can record:

- Why the link was saved.
- The useful conclusion hidden inside a long article or discussion.
- Which section, setting, or example solved the original problem.
- How the resource fits into a project or research topic.
- A tested procedure to follow next time.
- Personal observations, caveats, comparisons, or follow-up questions.
- A reusable snippet or starter template.

This makes Lincotic more than a bookmark manager. It becomes a durable memory of what was found, what was learned, and how that knowledge can be used again.

## How to write documentation

Documentation belongs to an individual link and is managed by that link's owner.

1. Sign in and open your hub.
2. Enable **Edit mode** from the navigation bar.
3. Select the link you want to document, or create a new link.
4. Open the **Documentation** tab in the link editor.
5. Write the instructions, notes, or reference material and save the link.

The editor supports headings, emphasis, lists, blockquotes, horizontal rules, and code blocks. That is enough for anything from a two-line reminder to a detailed runbook. Empty documentation is stored as no document, so links do not carry unnecessary records.

### A practical organizational template

The following structure is a useful starting point for an internal system:

```text
Purpose
What this system does and when to use it.

Access
Where access is requested, required approvals, roles, VPN, and SSO details.

Common tasks
The small set of workflows most people need.

Environments
How production, staging, test, and admin URLs differ.

Examples
Safe commands, code snippets, request bodies, or boilerplate.

Support
Owning team, escalation route, and useful troubleshooting information.

Review
Who last verified these notes and when they should be checked again.
```

Write for the person who has the link but none of the surrounding context. Prefer concrete instructions over unexplained acronyms, and state explicitly when a step affects production data.

## How to read with Learn Mode

1. Enable **Learn mode** from the navigation bar.
2. Select any link card. Learn Mode intercepts the normal click and opens its information panel.
3. Read the description and documentation before deciding whether to continue.
4. Use **Open link** to visit the destination in a new tab.

The panel keeps the relevant context together:

- Link title, collection, destination, and favorite status.
- The short link description.
- Long-form documentation.
- Number of times the link has been opened.
- The last-opened, saved, and documentation-updated times.

Learn Mode and Edit mode are mutually exclusive so that a click always has one predictable meaning. The selected mode is remembered for the current browser-tab session while moving between pages in the same Lincotic installation.

## Capacity

Each documentation box accepts up to **16,000,000 bytes** of sanitized HTML. For mostly plain text, that is theoretically in the region of **1.5 million words per link**. The real number varies with language, character encoding, headings, lists, code, and other formatting markup.

The limit is intentionally generous: a short note, a complete operating procedure, and substantial reference material can all live beside the same destination. It is a capacity ceiling, not a target. Focused documents with useful headings are usually easier to maintain and faster for readers to scan.

## Visibility and safety

Documentation inherits the visibility rules of its link:

- Documentation on a public link can be read by visitors to that active user's hub.
- Documentation on a hidden link is available only to the link's owner.
- A link inside a hidden collection is also restricted to its owner.
- Links owned by inactive accounts are not exposed.

Learn Mode uses the same visibility checks as outbound redirects, so opening the panel does not provide a path around hidden-content rules.

There is no separate team-only visibility tier. Treat documentation on a visible link as readable by anyone who can reach that hub. An organization can still keep an entire installation behind its own network controls, but Lincotic's application-level choices remain public-to-the-hub or private-to-the-owner.

Saved content is sanitized against a strict formatting allowlist before storage. Scriptable or embedded content is not retained. This makes formatted notes and code examples safe to render, but it does not turn documentation into a secrets manager.

Do not store passwords, API tokens, private keys, recovery codes, or other credentials in Learn Mode. For access instructions, document where a person should request access and which role they need—not the credential itself. Confidential operational information should be attached only to links whose visibility is appropriate for that information.

## What Learn Mode is—and is not

Learn Mode is designed to keep practical context close to the place where it is needed. It works especially well for onboarding notes, lightweight runbooks, personal research, recurring procedures, and reusable examples.

It does not replace source control for maintained code, a password manager for secrets, an approval system for access, or a versioned documentation platform for regulated procedures. It provides the connective tissue between a useful destination and the human knowledge required to use it well.

[Return to the project overview](../README.md).
