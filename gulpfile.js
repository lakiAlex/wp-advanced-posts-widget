"use strict";

// Load Dependencies
const {src, dest, series, parallel, watch} = require("gulp");
const browsersync = require("browser-sync").create();
const sass = require("gulp-sass");
const postcss = require("gulp-postcss");
const autoprefixer = require("autoprefixer");
const sourcemaps = require("gulp-sourcemaps");

const babel = require("gulp-babel");
const uglify = require("gulp-uglify");
const concat = require("gulp-concat");
const rename = require("gulp-rename");
const clean = require("gulp-clean");

const wpPot = require("gulp-wp-pot");
const gulpZip = require("gulp-zip");

// Plugin slug
const domain = "wpapw";

// Synchronizing file changes
function browserSync(done) {
	browsersync.init({
		proxy: "127.0.0.1:8080/wordpress/",
		port: 4000
	});
	done();
}

// Compile Sass
function css() {
	return src("./src/sass/**/*.scss")
		.pipe(sourcemaps.init())
		.pipe(sass({outputStyle: "expanded"}).on("error", sass.logError))
		.pipe(postcss([autoprefixer()]))
		.pipe(sourcemaps.write("./"))
		.pipe(dest("./dist/css/"))
		.pipe(browsersync.stream());
}

// Compile, minify and rename admin script
function jsAdmin() {
	return src("./src/js/admin.js")
		.pipe(babel({
            presets: ['@babel/env']
        }))
		.pipe(uglify())
		.pipe(rename('admin.min.js'))
		.pipe(dest("./dist/js/"))
		.pipe(browsersync.stream());
}

// Compile, minify and rename main script
function jsMain() {
	return src("./src/js/main.js")
		.pipe(babel({
            presets: ['@babel/env']
        }))
		.pipe(uglify())
		.pipe(rename('main.min.js'))
		.pipe(dest("./dist/js/"))
		.pipe(browsersync.stream());
}

// Generate pot file
function lang() {
	return src("./**/*.php")
		.pipe(
			wpPot({
				domain: domain,
				package: domain
			})
		)
		.pipe(dest("./lang/" + domain + ".pot"));
}

// Build theme zip file
function zip() {
	return src([
		"./**/*",
		"!./{node_modules,node_modules/**/*}",
		"!./{_build,_build/**/*}",
		"!./{src,src/**/*}",
		"!./gulpfile.js",
		"!./package.json",
		"!./package-lock.json",
		"!./{.git,.git/**/*}",
		"!./readme.md",
		"!.DS_Store"
	])
		.pipe(gulpZip(domain + ".zip"))
		.pipe(dest("./_build/"));
}

// Watch files
function watchFiles() {
	watch("./src/sass/**/*.scss", series(css));
	watch("./src/js/admin.js", series(jsAdmin));
	watch("./src/js/main.js", series(jsMain));
}

// Export tasks
exports.zip = zip;
exports.lang = lang;
exports.jsAdmin = jsAdmin;
exports.jsMain = jsMain;
exports.css = css;

exports.build = parallel(css, series(jsAdmin, jsMain), lang, zip);
exports.default = parallel(watchFiles, browserSync);
