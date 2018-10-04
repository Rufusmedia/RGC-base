//init scripts
let gulp         = require('gulp'),
    cleanCSS     = require('gulp-clean-css'),
    rename       = require("gulp-rename"),
    autoprefixer = require('gulp-autoprefixer'),
    uglify       = require('gulp-uglify'),
    imagemin     = require('gulp-imagemin'),
    concat       = require('gulp-concat'),
    browserSync  = require('browser-sync').create(),
    reload       = browserSync.reload;

//process css files
gulp.task('process-minify-css', () => {
  return gulp.src(['style.css', 'assets/base.css'])
    .pipe(autoprefixer({
        browsers: ['last 2 versions'],
        cascade: false
    }))
    .pipe(cleanCSS({compatibility: '*'}))
    .pipe(concat('style.min.css'))
    //.pipe(rename("style.min.css"))
    .pipe(gulp.dest('./'));
});

// process js files
gulp.task('process-minify-js', () => {
  return gulp.src('assets/scripts.js')
    .pipe(uglify())
    .pipe(rename("scripts.min.js"))
    .pipe(gulp.dest('assets/'));
});

// process image assets
gulp.task('process-images', () =>
    gulp.src('assets/src/*')
        .pipe(imagemin([
            imagemin.gifsicle({interlaced: true}),
            imagemin.jpegtran({progressive: true}),
            imagemin.optipng({optimizationLevel: 5}),
            imagemin.svgo({
                plugins: [
                    {removeViewBox: true},
                    {cleanupIDs: false}
                ]
            })
        ]))
        .pipe(gulp.dest('assets/dist'))
);

//watch file system for changes
gulp.task('watch', () => {
  // Watch .css files
  gulp.watch(['style.css', 'assets/base.css'], ['process-minify-css', reload]);
  gulp.watch('assets/scripts.js', ['process-minify-js', reload]);
  browserSync.init({
    files: ['./**/*.php'],
    proxy: 'http://localhost:8888/_base-2019/',
  });
});