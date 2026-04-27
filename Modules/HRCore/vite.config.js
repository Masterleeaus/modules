import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { readdirSync, statSync } from 'fs';
import { join,relative,dirname } from 'path';
import { fileURLToPath } from 'url';

export default defineConfig({
    build: {
        outDir: '../../public/build-hrcore',
        emptyOutDir: true,
        manifest: true,
    },
    plugins: [
        laravel({
            publicDirectory: '../../public',
            buildDirectory: 'build-hrcore',
            input: [
                __dirname + '/resources/assets/sass/app.scss',
                __dirname + '/resources/assets/js/app.js',
                __dirname + '/resources/assets/js/expense-types.js',
                __dirname + '/resources/assets/js/expenses.js',
                __dirname + '/resources/assets/js/expense-details.js',
                __dirname + '/resources/assets/css/expense-details.css',
                __dirname + '/resources/assets/js/my-expenses.js',
                __dirname + '/resources/assets/js/expense-create.js',
                __dirname + '/resources/assets/js/expense-edit.js',
                __dirname + '/resources/assets/js/compensatory-off.js',
                __dirname + '/resources/assets/js/my-regularization.js',
                __dirname + '/resources/assets/js/my-leaves.js'
            ],
            refresh: true,
        }),
    ],
});
// Scen all resources for assets file. Return array
//function getFilePaths(dir) {
//    const filePaths = [];
//
//    function walkDirectory(currentPath) {
//        const files = readdirSync(currentPath);
//        for (const file of files) {
//            const filePath = join(currentPath, file);
//            const stats = statSync(filePath);
//            if (stats.isFile() && !file.startsWith('.')) {
//                const relativePath = 'Modules/HRCore/'+relative(__dirname, filePath);
//                filePaths.push(relativePath);
//            } else if (stats.isDirectory()) {
//                walkDirectory(filePath);
//            }
//        }
//    }
//
//    walkDirectory(dir);
//    return filePaths;
//}

//const __filename = fileURLToPath(import.meta.url);
//const __dirname = dirname(__filename);

//const assetsDir = join(__dirname, 'resources/assets');
//export const paths = getFilePaths(assetsDir);


//export const paths = [
//    'Modules/HRCore/resources/assets/sass/app.scss',
//    'Modules/HRCore/resources/assets/js/app.js',
//];
