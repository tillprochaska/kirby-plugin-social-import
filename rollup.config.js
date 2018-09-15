import resolve from 'rollup-plugin-node-resolve';
import commonjs from 'rollup-plugin-commonjs';
import { terser } from 'rollup-plugin-terser';
import vue from 'rollup-plugin-vue';

const production = !process.env.ROLLUP_WATCH;

export default {
    input: './panel/src/index.js',
    output: {
        file: './index.js',
        format: 'iife',
        sourcemap: true,
    },
    plugins: [
        resolve(),
        commonjs(),
        vue(),
        production && terser(),
    ]
};