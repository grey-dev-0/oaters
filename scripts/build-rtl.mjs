/**
 * Post-build RTL step (spec 003): mirrors every stylesheet emitted by the
 * Vite build into an `.rtl.css` sibling using rtlcss.
 *
 * Sheets whose rules are already scoped under `[dir="rtl"]` (the overrides
 * entry) are skipped: they must not be mirrored again.
 *
 * Only the mirrored theme bundle is actually referenced by the layout
 * (loaded when the interface direction is RTL); per-component sheets are
 * injected by Vite's runtime with their LTR filenames, so their directional
 * rules are corrected by the overrides entry instead.
 */
import {existsSync} from 'node:fs';
import {readFile, writeFile} from 'node:fs/promises';
import rtlcss from 'rtlcss';

const buildDir = 'public/build';
const manifestPaths = [`${buildDir}/manifest.json`, `${buildDir}/.vite/manifest.json`];
const manifestPath = manifestPaths.find(path => existsSync(path));

if(manifestPath === undefined){
    console.warn('[build-rtl] no build manifest found - skipping (dev/hot mode)');
    process.exit(0);
}

const manifest = JSON.parse(await readFile(manifestPath, 'utf8'));

const stylesheets = new Set();
for(const entry of Object.values(manifest)){
    if(entry.file?.endsWith('.css'))
        stylesheets.add(entry.file);
    for(const css of entry.css ?? [])
        stylesheets.add(css);
}

let mirrored = 0;
for(const file of stylesheets){
    if(file.endsWith('.rtl.css') || file.includes('rtl-overrides'))
        continue;

    const source = await readFile(`${buildDir}/${file}`, 'utf8');
    await writeFile(`${buildDir}/${file.replace(/\.css$/, '.rtl.css')}`, rtlcss.process(source));
    mirrored++;
}

console.log(`[build-rtl] mirrored ${mirrored} stylesheets`);
