var gulp = require('gulp');
var sass = require('gulp-sass');
var minify = require('gulp-minify');
var cleanCSS = require('gulp-clean-css');
var rename = require('gulp-rename');
var insert = require('gulp-insert');
var inject = require('gulp-inject-string');
var deletefile = require('gulp-delete-file');
var themeDir = 'public/themes/default/';


// admin.scss
gulp.task('admin', function() {
  gulp.src('public/static/scss/admin.scss')
    .pipe(sass().on('error', sass.logError))
    .pipe(gulp.dest('public/static/css/'));
});


// theme
gulp.task('scss', function() {
  gulp.src(themeDir + 'scss/*.scss')
    .pipe(sass().on('error', sass.logError))
    .pipe(gulp.dest(themeDir + 'css/'));
});

gulp.task('css', function() {
  gulp.src(themeDir + 'css/style.css')
    .pipe(cleanCSS({compatibility: 'ie8'}))
    .pipe(rename({
        suffix: '.min'
    }))
    .pipe(gulp.dest(themeDir + 'css/'));
});

gulp.task('js', function() {
  gulp.src(themeDir + 'js/script.js')
    .pipe(minify())
    .pipe(gulp.dest(themeDir + 'js/'))
});

gulp.task('css2pug', function() {
  return gulp.src(themeDir + 'css/style.min.css')
    .pipe(inject.prepend('style.\n  '))
    .pipe(rename({
        extname: '.css.pug'
    }))
    .pipe(gulp.dest(themeDir + 'views/snippet'));
});

gulp.task('deletefile', function () {
  var regexp = /\w*(\-\w{8}\.js){1}$|\w*(\-\w{8}\.css){1}$/;
  return gulp.src(themeDir + 'views/snippet/style.min.css.pug')
    .pipe(deletefile({
      reg: regexp,
      deleteMatch: false
    }));
});

gulp.task('default',function() {
  gulp.watch('public/static/scss/admin.scss',['admin']);
  gulp.watch(themeDir + 'scss/*.scss',['deletefile', 'css', 'scss', 'css2pug']);
  gulp.watch(themeDir + 'js/script.js',['js']);
});

gulp.task('build', ['deletefile', 'scss', 'css', 'js', 'css2pug', 'admin']);
