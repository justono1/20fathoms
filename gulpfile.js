let gulp = require('gulp');
let uglify = require('gulp-uglify');
let sass = require('gulp-sass');
let sourcemaps = require('gulp-sourcemaps');
let browserSync = require('browser-sync').create();
let sassGlob = require('gulp-sass-glob');
let reload = browserSync.reload;
let livereload = require('gulp-livereload');
let concat = require('gulp-concat');
let minifyCss = require('gulp-minify-css');
let autoprefixer = require('gulp-autoprefixer');
let plumber = require('gulp-plumber');
let babel = require('gulp-babel');
let merge = require('merge-stream');
let ngrok = require('ngrok');

let ngrokURL;

// File paths
const SCRIPTS_PATH = 'wp-content/themes/theme/js/private/**/*.js';
const SCRIPTS_PATH_ES5 = 'wp-content/themes/theme/js/private-es5/**/*.js';
const SASS_SOURCE = 'wp-content/themes/theme/scss/style.scss';
const SASS_PATH = 'wp-content/themes/theme/scss/**/*.scss';
const CSS_PATH = 'wp-content/themes/theme/css/private/**/*.css';
const CSS_DEST = 'wp-content/themes/theme/css';
const HTML_PATH = 'wp-content/themes/theme/**/*.html';
const TWIG_PATH = 'wp-content/themes/theme/**/*.twig';
const PHP_PATH = 'wp-content/themes/theme/**/*.php';

gulp.task('styles', function() {
	let sassStream, cssStream;
	console.log('starting styles task');

	sassStream = gulp
		.src(SASS_SOURCE)
		.pipe(
			plumber(function(err) {
				console.log('Styles Task Error');
				console.log(err);
				this.emit('end');
			})
		)
		.pipe(sourcemaps.init())
		.pipe(autoprefixer())
		.pipe(sassGlob())
		.pipe(sass())
		//.pipe(minifyCss())
		.pipe(sourcemaps.write())
		.pipe(reload({ stream: true }));

	cssStream = gulp.src(CSS_PATH);

	return merge(sassStream, cssStream)
		.pipe(sourcemaps.init())
		.pipe(concat('style.css'))
		.pipe(sourcemaps.write())
		.pipe(gulp.dest(CSS_DEST))
		.pipe(reload({ stream: true }));
});

// Scripts
gulp.task('scripts', function() {
	let es5_stream, es6_stream;

	console.log('starting scripts task');

	es6_stream = gulp
		.src(['wp-content/themes/theme/js/private/stickyfill.min.js', SCRIPTS_PATH])
		.pipe(
			plumber(function(err) {
				console.log('Scripts Task Error');
				console.log(err);
				this.emit('end');
			})
		)
		.pipe(sourcemaps.init())
		.pipe(
			babel({
				presets: ['es2015']
			})
		)
		.pipe(uglify())
		.pipe(concat('scripts.js'));
	//.pipe(sourcemaps.write())

	es5_stream = gulp
		.src(SCRIPTS_PATH_ES5)
		.pipe(concat('scripts.js'))
		.pipe(uglify());

	return merge(es6_stream, es5_stream)
		.pipe(concat('scripts.js'))
		.pipe(gulp.dest('wp-content/themes/theme/js'));
});

gulp.task('scripts-refresh', ['scripts'], function() {
	console.log('starting scripts refresh task');
	browserSync.reload();
});

gulp.task('refresh', function() {
	console.log('starting refresh task');
	browserSync.reload();
});

let logNgrok = () => {
	console.log(ngrokURL);
};

gulp.task('build', function() {
	console.log('starting build task');
	return gulp
		.src(SASS_SOURCE)
		.pipe(
			plumber(function(err) {
				console.log('Build Task Error');
				console.log(err);
				this.emit('end');
			})
		)
		.pipe(sourcemaps.init())
		.pipe(autoprefixer())
		.pipe(sassGlob())
		.pipe(sass())
		.pipe(minifyCss())
		.pipe(sourcemaps.write())
		.pipe(gulp.dest(CSS_DEST));
});

gulp.task('default', ['styles', 'scripts'], function() {
	console.log('Starting default task');
});

gulp.task('serve', ['default'], function() {
	console.log('Starting serve task');
	browserSync.init(
		{
			proxy: 'localhost:8888'
		}
		// function(err, bs) {
		// 	ngrok.connect(
		// 		{
		// 			proto: 'http',
		// 			addr: bs.options.get('port'),
		// 			authtoken: '2AVoMzwAQ1hzzYzLPEzx1_SCuod2SsWcP4QAdoxgfM',
		// 			subdomain: 'brightly'
		// 		},
		// 		function(err, url) {
		// 			if (url != undefined) {
		// 				ngrokURL = url;
		// 				logNgrok();
		// 				//console.log(err);
		// 			} else {
		// 				ngrok.connect(
		// 					{
		// 						proto: 'http',
		// 						addr: bs.options.get('port'),
		// 						authtoken: '2AVoMzwAQ1hzzYzLPEzx1_SCuod2SsWcP4QAdoxgfM',
		// 						subdomain: 'brightly1'
		// 					},
		// 					function(err, url) {
		// 						ngrokURL = url;
		// 						logNgrok();
		// 					}
		// 				);
		// 			}
		// 		}
		// 	);
		//
		// 	try {
		// 	} catch (e) {
		// 		console.log('NGROK FAILED');
		// 		// ngrok.disconnect();
		// 		// ngrok.connect(
		// 		//   {
		// 		//       proto: 'http',
		// 		//       addr: bs.options.get('port'),
		// 		//       authtoken: '2AVoMzwAQ1hzzYzLPEzx1_SCuod2SsWcP4QAdoxgfM',
		// 		//       subdomain: 'brightly1'
		// 		//   },
		// 		//   function(err, url) {
		// 		//       ngrokURL = url;
		// 		//       logNgrok();
		// 		// 	});
		// 		// statements to handle any exceptions
		// 		logMyErrors(e); // pass exception object to error handler
		// 	}
		// }
	);

	gulp.watch([SASS_PATH, CSS_PATH], ['styles']);
	gulp.watch(SCRIPTS_PATH, ['scripts-refresh']);
	gulp.watch([HTML_PATH, TWIG_PATH, PHP_PATH], ['refresh']);
	gulp.watch(
		[HTML_PATH, TWIG_PATH, PHP_PATH, SCRIPTS_PATH, SASS_PATH, CSS_PATH],
		() => {
			// logNgrok();
		}
	);
});
