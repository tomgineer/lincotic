import { spawn } from 'node:child_process';

const commands = [
    'tailwindcss -i theme/css/input.css -o public/css/app-dist.css --minify',
    'esbuild theme/js/app.js --bundle --minify --outfile=public/js/app-dist.js',
];

commands.forEach((command) => spawn(command, {
    shell: true,
    stdio: 'inherit',
}));
