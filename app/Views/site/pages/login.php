<?= $this->extend('site/layouts/default') ?>

<?= $this->section('title') ?>Sign in | Lincotic<?= $this->endSection() ?>

<?= $this->section('content') ?>
<main class="relative isolate flex min-h-screen items-center overflow-hidden px-6 py-10 sm:py-16">
    <div class="pointer-events-none absolute inset-0 -z-10" aria-hidden="true">
        <div class="absolute -top-40 left-1/2 size-96 -translate-x-1/2 rounded-full bg-coral/10 blur-3xl"></div>
        <div class="absolute -bottom-48 -right-32 size-[32rem] rounded-full bg-acid/5 blur-3xl"></div>
        <div class="absolute inset-0 opacity-30 [background-image:linear-gradient(to_right,color-mix(in_srgb,var(--color-base-content)_6%,transparent)_1px,transparent_1px),linear-gradient(to_bottom,color-mix(in_srgb,var(--color-base-content)_6%,transparent)_1px,transparent_1px)] [background-size:3rem_3rem]"></div>
    </div>

    <div class="mx-auto grid w-full max-w-5xl overflow-hidden rounded-3xl border border-base-content/15 bg-base-200 shadow-2xl shadow-black/25 lg:grid-cols-[0.9fr_1.1fr]">
        <section class="relative flex min-h-72 flex-col justify-between overflow-hidden border-b border-base-content/10 bg-base-100 p-8 sm:p-10 lg:min-h-[38rem] lg:border-r lg:border-b-0">
            <div class="pointer-events-none absolute -right-32 -bottom-40 size-96 rounded-full border-[4rem] border-acid/10" aria-hidden="true"></div>

            <a href="<?= esc(site_url('/')) ?>" class="relative block w-40 transition-transform duration-300 motion-safe:hover:scale-105" aria-label="Lincotic home">
                <img
                    src="<?= esc(base_url('assets/images/logo.svg')) ?>?v=<?= APP_VERSION ?>"
                    alt="Lincotic"
                    class="h-auto w-full">
            </a>

            <div class="relative mt-16 max-w-sm lg:mt-0">
                <p class="font-mono text-xs font-bold uppercase tracking-[0.2em] text-acid">Private access</p>
                <h1 class="mt-4 text-4xl font-bold tracking-tight sm:text-5xl">Your links,<br>right where you left them.</h1>
                <p class="mt-5 max-w-xs text-base leading-relaxed text-base-content/60">
                    Sign in to manage your collection and keep your workspace in order.
                </p>
            </div>
        </section>

        <section class="flex items-center p-8 sm:p-10 lg:p-14" aria-labelledby="login-title">
            <div class="w-full">
                <div>
                    <p class="font-mono text-xs font-bold uppercase tracking-[0.2em] text-coral-light">Welcome back</p>
                    <h2 id="login-title" class="mt-3 text-3xl font-bold tracking-tight">Sign in to Lincotic</h2>
                    <p class="mt-2 text-base-content/60">Enter your account details to continue.</p>
                </div>

                <?php if (session('error') !== null): ?>
                    <div class="alert alert-error mt-6" role="alert">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="size-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9" /><path d="M12 8v4" /><path d="M12 16h.01" /></svg>
                        <span><?= esc(session('error')) ?></span>
                    </div>
                <?php elseif (session('errors') !== null): ?>
                    <div class="alert alert-error mt-6" role="alert">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="size-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9" /><path d="M12 8v4" /><path d="M12 16h.01" /></svg>
                        <?php if (is_array(session('errors'))): ?>
                            <ul class="list-disc space-y-1 pl-5">
                                <?php foreach (session('errors') as $error): ?>
                                    <li><?= esc($error) ?></li>
                                <?php endforeach ?>
                            </ul>
                        <?php else: ?>
                            <span><?= esc(session('errors')) ?></span>
                        <?php endif ?>
                    </div>
                <?php endif ?>

                <?php if (session('message') !== null): ?>
                    <div class="alert alert-success mt-6" role="status">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="size-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9" /><path d="M8 12l2.5 2.5L16 9" /></svg>
                        <span><?= esc(session('message')) ?></span>
                    </div>
                <?php endif ?>

                <form action="<?= esc(url_to('login')) ?>" method="post" class="mt-8 space-y-5">
                    <?= csrf_field() ?>

                    <fieldset class="fieldset">
                        <label class="fieldset-legend" for="email">Email address</label>
                        <label class="input input-lg w-full focus-within:outline-2 focus-within:outline-offset-2">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="size-5 text-base-content/45" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 7a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v10a2 2 0 0 1 -2 2H5a2 2 0 0 1 -2 -2z" /><path d="m3 7 9 6 9 -6" /></svg>
                            <input
                                id="email"
                                type="email"
                                name="email"
                                inputmode="email"
                                autocomplete="email"
                                placeholder="name@example.com"
                                value="<?= esc(old('email') ?? '', 'attr') ?>"
                                required
                                autofocus>
                        </label>
                    </fieldset>

                    <fieldset class="fieldset">
                        <label class="fieldset-legend" for="password">Password</label>
                        <label class="input input-lg w-full focus-within:outline-2 focus-within:outline-offset-2">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="size-5 text-base-content/45" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="5" y="10" width="14" height="10" rx="2" /><path d="M8 10V7a4 4 0 0 1 8 0v3" /><path d="M12 14v2" /></svg>
                            <input
                                id="password"
                                type="password"
                                name="password"
                                autocomplete="current-password"
                                placeholder="Enter your password"
                                required>
                        </label>
                    </fieldset>

                    <?php if (setting('Auth.sessionConfig')['allowRemembering']): ?>
                        <label class="label w-fit cursor-pointer gap-3" for="remember">
                            <input
                                id="remember"
                                type="checkbox"
                                name="remember"
                                class="checkbox checkbox-coral checkbox-sm"
                                <?= old('remember') ? 'checked' : '' ?>>
                            <span>Remember me</span>
                        </label>
                    <?php endif ?>

                    <button type="submit" class="btn btn-coral btn-lg btn-block">
                        Sign in
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="size-5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14" /><path d="m13 6 6 6-6 6" /></svg>
                    </button>
                </form>

                <a href="<?= esc(site_url('/')) ?>" class="link link-hover mt-8 inline-flex items-center gap-2 text-sm text-base-content/55 hover:text-coral-light">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="size-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M19 12H5" /><path d="m11 18-6-6 6-6" /></svg>
                    Back to homepage
                </a>
            </div>
        </section>
    </div>
</main>
<?= $this->endSection() ?>
