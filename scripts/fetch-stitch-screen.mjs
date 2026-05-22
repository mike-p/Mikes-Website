#!/usr/bin/env node
/**
 * Download Stitch screen HTML + screenshot for local reference.
 * Usage: STITCH_API_KEY=... node scripts/fetch-stitch-screen.mjs
 */
import { mkdir, writeFile } from 'node:fs/promises';
import { execFile } from 'node:child_process';
import { promisify } from 'node:util';
import { stitch } from '@google/stitch-sdk';

const execFileAsync = promisify(execFile);

const PROJECT_ID = process.env.STITCH_PROJECT_ID ?? '6263942770602095798';
const SCREEN_ID = process.env.STITCH_SCREEN_ID ?? '347b2d6ad4c54a46b42812de7498f04a';
const OUT_DIR = new URL('../stitch-export/', import.meta.url);

async function curlDownload(url, outPath) {
	await execFileAsync('curl', ['-fsSL', url, '-o', outPath]);
}

async function main() {
	if (!process.env.STITCH_API_KEY && !process.env.STITCH_ACCESS_TOKEN) {
		console.error('Set STITCH_API_KEY (or STITCH_ACCESS_TOKEN + GOOGLE_CLOUD_PROJECT).');
		process.exit(1);
	}

	const project = stitch.project(PROJECT_ID);
	const screen = await project.getScreen(SCREEN_ID);
	const htmlUrl = await screen.getHtml();
	const imageUrl = await screen.getImage();

	console.log('HTML URL:', htmlUrl);
	console.log('Image URL:', imageUrl);

	await mkdir(OUT_DIR, { recursive: true });
	const htmlPath = new URL('alpine-archive-homepage.html', OUT_DIR).pathname;
	const pngPath = new URL('alpine-archive-homepage.png', OUT_DIR).pathname;
	const metaPath = new URL('manifest.json', OUT_DIR).pathname;

	await curlDownload(htmlUrl, htmlPath);
	await curlDownload(imageUrl, pngPath);
	await writeFile(
		metaPath,
		JSON.stringify(
			{
				projectId: PROJECT_ID,
				screenId: SCREEN_ID,
				title: 'The Alpine Archive - Homepage Rebuild',
				htmlUrl,
				imageUrl,
				htmlPath,
				pngPath,
			},
			null,
			2,
		),
	);

	console.log('Saved:', htmlPath, pngPath, metaPath);
}

main().catch((err) => {
	console.error(err.message ?? err);
	process.exit(1);
});
