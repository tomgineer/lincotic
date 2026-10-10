import { spawn } from 'node:child_process';

const commands = [
    'tailwindcss -i theme/css/input.css -o public/css/app-dist.css --watch',
    'esbuild theme/js/app.js --bundle --outfile=public/js/app-dist.js --watch',
];

commands.forEach((command) => spawn(command, {
    shell: true,
    stdio: 'inherit',
}));
