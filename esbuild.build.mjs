import * as esbuild from 'esbuild'
import { sassPlugin } from 'esbuild-sass-plugin'

await esbuild.build({
  entryPoints: ['src/sass/index.sass'],
  bundle: true,
  outfile: 'style/main.css',
  plugins: [sassPlugin()]
})
