<div class="flex flex-col gap-2 font-ui text-[10px] uppercase text-base-content/45 sm:flex-row sm:items-center sm:justify-between sm:gap-6">
    <p class="inline-flex shrink-0 items-center gap-3 self-start font-medium leading-6">
        <span class="font-mono tracking-[0.2em] text-base-content/60">Links</span>
        <span class="sr-only">Chaotic</span>
        <span class="inline-flex items-center gap-0.5 font-black text-coral" aria-hidden="true">
            <span class="-translate-y-0.5 -rotate-12">C</span>
            <span class="translate-y-0.5 rotate-6">h</span>
            <span class="-rotate-6">a</span>
            <span class="-translate-y-0.5 rotate-12">o</span>
            <span class="translate-y-0.5 -rotate-12">t</span>
            <span class="-translate-y-0.5 rotate-6">i</span>
            <span class="rotate-12">c</span>
        </span>
        <span class="rotate-3 rounded-sm bg-base-100 px-2 py-0.5 font-black leading-4 tracking-[0.16em] text-acid ring-1 ring-inset ring-acid/60">Lincotic</span>
    </p>

    <ul class="flex flex-wrap gap-x-4 gap-y-1 tracking-[0.08em]">
        <li>
            <span class="font-mono font-semibold text-acid"><?= number_format((int) ($totalLinkCount ?? 0)) ?></span> links collected
        </li>
        <li>
            <span class="font-mono font-semibold text-acid"><?= number_format((int) ($totalClickCount ?? 0)) ?></span> times hammered
        </li>
    </ul>
</div>
