import * as esbuild from 'esbuild'
import { sassPlugin } from 'esbuild-sass-plugin'

async function watch() {
  let ctx = await esbuild.context({
    entryPoints: ['src/sass/index.sass'],
    bundle: true,
    outfile: 'style/main.css',
    plugins: [sassPlugin()]
  })
  await ctx.watch()
  console.log('Watching...')
}

watch()