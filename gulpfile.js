var gulp   = require('gulp');
var concat = require('gulp-concat');
var cssmin = require('gulp-cssmin');
var rename = require('gulp-rename');
var uglify = require('gulp-uglify');
var autoprefixer = require('gulp-autoprefixer');
var sass = require('gulp-sass');
var watch = require('gulp-watch');
var connect = require('gulp-connect-php');
var pump = require('pump');
var browserSync = require('browser-sync');
var del = require('del');

gulp.task('tSassToCss', function(){
    return gulp.src('public/sass/*.sass')
				.pipe(sass({
					outputStyle: 'expanded',
					indentType: 'tab',
					indentWidth: 1
				}).on('error', sass.logError))
				.pipe(autoprefixer({
					browsers: ['last 5 versions', '> 1%', 'ie 8', 'ie 7'],
					cascade: true
				}))
				.pipe(cssmin())
				.pipe(concat('all.min.css'))
				.pipe(gulp.dest('public/css/'))
				.pipe(browserSync.reload({
					stream: true
				}));
});
gulp.task('tBrowserSync', ['tSassToCss'], function() {
   	connect.server({}, function(){
		browserSync({
			proxy: 'kino:80',
			browser: 'firefox',
			notify: false,
			minify: false
    	});
	});
	
	gulp.watch('public/sass/*.sass', ['tSassToCss']);
	gulp.watch('resources/views/**/*.blade.php').on('change', function(){
		browserSync.reload();
	});
	gulp.watch('public/js/**/*.js').on('change', function(){
		browserSync.reload();
	});
});
gulp.task('default', ['tBrowserSync']);