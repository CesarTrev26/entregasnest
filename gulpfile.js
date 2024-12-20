

const { src, dest, watch, series } = require("gulp");
const sass = require("gulp-sass")(require('sass'));
const plumber = require("gulp-plumber");
const sharp = require('sharp');

const terser = require('gulp-terser');
const path = require('path');
const fs = require ('fs')
const { glob } = require('glob')

const { shrink, shrink_by_page2jpg } = require("shrink-pdf");

const paths = {
    scss: 'src/scss/**/*.scss',
    js: 'src/js/**/*.js',
    imagenes: 'src/img/**/*',
    pdf: 'src/pdf/**/*'
}

function js(done) {
    src(paths.js)
    .pipe(terser())
    .pipe(dest("./public/build/js"));
    done();
}

function css(done) {
    //Identificar el archivo de SASS
    src(paths.scss)
    .pipe(plumber())
    .pipe(sass({
        outputStyle: "compressed"
    })).on('error', sass.logError)//Compilarlo
    .pipe(dest("./public/build/css"));    //Almacenarla en el disco duro

    done();
}



async function imagenes(done) {
    const srcDir = './src/img';
    const buildDir = './public/build/img';
    const images =  await glob(`${paths.imagenes}{jpg,png}`)

    images.forEach(file => {
        const relativePath = path.relative(srcDir, path.dirname(file));
        const outputSubDir = path.join(buildDir, relativePath);
        procesarImagenes(file, outputSubDir);
    });
    done();
}

function procesarImagenes(file, outputSubDir) {
    if (!fs.existsSync(outputSubDir)) {
        fs.mkdirSync(outputSubDir, { recursive: true })
    }
    const baseName = path.basename(file, path.extname(file))
    const extName = path.extname(file)
    const outputFile = path.join(outputSubDir, `${baseName}${extName}`)
    const outputFileWebp = path.join(outputSubDir, `${baseName}.webp`)

    const options = { quality: 80 }
    sharp(file).jpeg(options).toFile(outputFile)
    sharp(file).webp(options).toFile(outputFileWebp)
}


async function pdfcompress(done) {
    const srcDir = './src/pdf';
    const buildDir = './public/build/pdf';
    const pdfFiles = await glob(`${paths.pdf}.pdf`);

    pdfFiles.forEach(file => {
        const relativePath = path.relative(srcDir, path.dirname(file));
        const outputSubDir = path.join(buildDir, relativePath);
        
        if (!fs.existsSync(outputSubDir)) {
            fs.mkdirSync(outputSubDir, { recursive: true });
        }

        const baseName = path.basename(file, path.extname(file));
        const out_file = path.join(outputSubDir, `${baseName}.pdf`);
        const another_out_file = path.join(outputSubDir, `${baseName}_another_compressed.pdf`);

        let options = {
            compatibilityLevel: 1.5,
            imageQuality: 100,
            resolution: 400,
            in_file: file,
            out_file: out_file
        };

        try {
            shrink(options);
            shrink_by_page2jpg({
                ...options,
                resolution: 150,
                out_file: another_out_file,
            });
        } catch (e) {
            console.error(e);
        }
    });
    done();
}


function dev(done) {
    watch(paths.scss, css);
    watch(paths.js, js);
    watch(`${paths.imagenes}.{png, jpg}`, imagenes);
    watch(`${paths.pdf}.pdf`, pdfcompress);
    done();
}

exports.css = css;
exports.dev = dev;
exports.default = series( js, css, imagenes, pdfcompress, dev)
exports.build = series( js, css, imagenes, pdfcompress)