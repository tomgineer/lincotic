<ul class="text-base-content/55 uppercase flex flex-col font-ui text-[10px]">
    <li class="tracking-[0.5em]">Links • Chaotic • Lincotic</li>
    <li class="tracking-[0.1em]">
        <span class="text-acid font-semibold"><?= number_format((int) ($totalLinkCount ?? 0)) ?></span> links collected • <span class="text-acid font-semibold"><?= number_format((int) ($totalClickCount ?? 0)) ?></span> times hammered
    </li>
</ul>