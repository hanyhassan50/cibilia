'use strict';
// Plugins / Functions / Modules
const plugins = require('gulp-load-plugins')({
        pattern: ['*', '!gulp', '!gulp-load-plugins'],
        rename : {
            'browser-sync'    : 'browserSync',
            'fs-extra'        : 'fs',
            'gulp-multi-dest' : 'multiDest',
            'js-yaml'         : 'yaml',
            'marked-terminal' : 'markedTerminal',
            'merge-stream'    : 'mergeStream',
            'postcss-reporter': 'reporter',
            'run-sequence'    : 'runSequence'
        }
    }),
    config = {};

require('gulp-task-loader')({
    dir    : 'task',
    plugins: plugins,
    configs: config
});

var gulp = require('gulp');
var sass = require('gulp-sass');
var minify = require('gulp-minify');


gulp.task('sass', function() {
    return gulp.src('web/css/source/styles.scss') // Gets all files ending with .scss in app/scss and children dirs
        .pipe(sass())
        .pipe(gulp.dest('web/css'))
});

gulp.task('watch', function(){
    gulp.watch('web/css/source/**/*.scss', ['sass']);
});