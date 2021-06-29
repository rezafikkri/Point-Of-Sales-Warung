import { terser } from 'rollup-plugin-terser';
import { nodeResolve } from '@rollup/plugin-node-resolve';
import commonjs from '@rollup/plugin-commonjs';

// use immediately invoked function expression to auto generate array object for rollup config
export default (function(){
    const banner = `/*!
 * Point Of Sales Warung
 * Licensed under GPL (https://github.com/rezafikkri/Point-Of-Sales-Warung/blob/master/LICENSE)
 */`;
    const fileNames = [
        //'posw',
        //'signin.posw',
        //'admin.posw',
        //'user.posw',
        //'create_user.posw',
        //'update_user.posw',
        'category_product.posw',
        //'product.posw',
        //'create_product.posw',
        //'update_product.posw',
        //'cashier.posw',
        //'transaction.posw',
    ];

    let configs = [];
    for (const fn of fileNames) {
        configs.push({
            input: `public/src/js/${fn}.js`,
            output:
            {
                file: `public/dist/js/${fn}.min.js`,
                format: 'iife',
                banner: banner
            },
            plugins: [
                terser(),
                commonjs(),
                nodeResolve()
            ]
        });
    }

    return configs;
})()
