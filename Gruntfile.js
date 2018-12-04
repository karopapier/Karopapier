const libraries = [
    'jquery',
    'underscore',
    'backbone',
    'backbone.marionette',
    'backbone.radio',
    // 'es6-promise/auto',
];

module.exports = function(grunt) {
    // Project configuration.
    require('time-grunt')(grunt);

    var keypath = '/etc/ssl/panamapapier/privkey.pem';
    var certpath = '/etc/ssl/panamapapier/cert.pem';
    var livereloadConfig = {
        port: 20001,
    };

    if (grunt.file.exists(keypath, certpath)) {
        livereloadConfig['key'] = grunt.file.read(keypath);
        livereloadConfig['cert'] = grunt.file.read(certpath);
    }

    var cleanCssOptions = {
        level: 2,
    };

    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),
        browserify: {
            options: {
                banner: '/*! <%= pkg.name %> <%= grunt.template.today("yyyy-mm-dd HH:MM:ss") %> */\n',
                transform: [
                    ['babelify', {presets: ['env']}],
                    ['jstify'], //html -> underscore templates
                ],
            },
            libs: {
                options: {
                    browserifyOptions: {
                        require: libraries,
                    },
                },
                files: {
                    './web/js/libs.src.js': ['./frontend/src/libload.js'],
                },
            },
            dev: {
                options: {
                    exclude: libraries,
                },
                files: {
                    'web/js/<%= pkg.name %>.dev.js': ['frontend/src/app.js'],
                },
                browserifyOptions: {
                    debug: true,
                },
            },
            dist: {
                options: {
                    banner: '/*! frontend App <%= grunt.template.today("yyyy-mm-dd HH:MM:ss") %> */\n',
                    exclude: libraries,
                },
                files: {
                    'web/js/<%= pkg.name %>.src.js': ['frontend/src/app.js'],
                },
            },
            bbapp: {
                options: {
                    banner: '/*! bb browserified <%= grunt.template.today("yyyy-mm-dd HH:MM:ss") %> */\n',
                },
                files: {
                    'web/js/Karopapier.src.js': ['backbone/src/start.js'],
                },
            },
            gamestepup: {
                options: {
                    banner: '/*! bb browseriefied <%= grunt.template.today("yyyy-mm-dd HH:MM:ss") %> */\n',
                },
                files: {
                    'web/js/GameStepUp.js': ['backbone/src/GameStepUp.src.js'],
                },
            },
        },
        uglify: {
            libs: {
                files: {
                    './web/js/libs.min.js': './web/js/libs.src.js',
                },
                options: {
                    sourceMap: false,
                    sourceMapIncludeSources: false,
                },
            },
            min: {
                files: {
                    'web/js/<%= pkg.name %>.min.js': 'web/js/<%= pkg.name %>.src.js',
                },
                options: {
                    sourceMapIncludeSources: false,
                    sourceMap: false,
                    banner: '/*! <%= pkg.name %> <%= grunt.template.today("yyyy-mm-dd") %> */\n',
                },
            },
        },
        watch: {
            app: {
                files: ['frontend/src/**/*', 'frontend/templates/**/*'],
                tasks: ['build:frontend', 'bust'],
                options: {
                    interrupt: true,
                    livereload: livereloadConfig,
                },
            },
            css: {
                files: ['frontend/css/**/*'],
                tasks: ['less', 'bust'],
                options: {
                    interrupt: true,
                    livereload: livereloadConfig,
                },
            },
            bbcss: {
                files: ['backbone/css/**/*'],
                tasks: ['less:bbcss'],
                options: {
                    interrupt: true,
                    livereload: livereloadConfig,
                },
            },
            web: {
                files: ['web/**/*.php', 'src/**/*', 'app/Resources/**/*'],
                options: {
                    interrupt: true,
                    livereload: livereloadConfig,
                },
            },
            gamestepup: {
                files: [
                    'backbone/src/**/*.js',
                    'backbone/templates/**/*',
                    'backbone/src/GameStepUp.src.js',
                ],
                tasks: ['build:bb', 'bust'],
                options: {
                    interrupt: true,
                    livereload: livereloadConfig,
                },
            },
        },
        jst: {
            options: {
                prettify: true,
                processName: function(filepath) {
                    var p = filepath;
                    p = p.replace('backbone/templates/', '');
                    p = p.replace(/\.html$/, '');
                    p = p.replace(/\.tpl$/, '');
                    return p;
                },
            },
            compile: {
                files: {
                    'web/js/JST.js': ['backbone/templates/**/*.html', 'backbone/templates/**/*.tpl'],
                },
            },

        },
        less: {
            app: {
                options: {
                    banner: '/*! <%= pkg.name %> <%= grunt.template.today("yyyy-mm-dd") %> */\n',
                    expand: true,
                    plugins: [
                        new (require('less-plugin-autoprefix')),
                        new (require('less-plugin-clean-css'))(cleanCssOptions),
                    ],
                },
                files: {
                    'web/css/app.css': 'frontend/css/KaroApp.less',
                },
            },
            css: {
                options: {
                    plugins: [
                        new (require('less-plugin-autoprefix')),
                        new (require('less-plugin-clean-css'))(cleanCssOptions),
                    ],
                },
                files: {
                    'web/css/previous.css': 'frontend/css/karo.css',
                    'web/css/theme.css': 'frontend/css/theme.css',
                },
            },
            bbcss: {
                options: {
                    banner: '/*! <%= pkg.name %> bbcss <%= grunt.template.today("yyyy-mm-dd") %> */\n',
                    plugins: [
                        new (require('less-plugin-autoprefix')),
                        new (require('less-plugin-clean-css'))(cleanCssOptions),
                    ],
                },
                files: {
                    'web/css/Karopapier.min.css': 'backbone/css/karopapier.less',
                },
            },
        },
        eslint: {
            options: {fix: true},
            target: [
                'frontend/src/**/*.js',
                'backbone/src/**/*.js',
            ],
        },
        shell: {
            bust: {
                command: 'php ./cachebust.php',
            },
        },
        nodeunit: {
            all: ['backbone/test/test.js'],
        },
    });

    // Load the plugins
    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks('grunt-browserify');
    grunt.loadNpmTasks('grunt-contrib-jst');
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-less');
    grunt.loadNpmTasks('grunt-contrib-nodeunit');
    grunt.loadNpmTasks('grunt-eslint');
    grunt.loadNpmTasks('grunt-shell');

    // Default task(s).
    grunt.registerTask('build', ['build:prod', 'less', 'jst', 'bust']);
    grunt.registerTask('build:frontend', ['browserify:dev', 'style', 'test']);
    grunt.registerTask('build:bb', ['browserify:gamestepup', 'browserify:bbapp', 'style', 'test', 'jst']);
    grunt.registerTask('build:prod', ['less', 'browserify', 'uglify', 'bust']);
    grunt.registerTask('style', ['eslint']);
    grunt.registerTask('bust', ['shell:bust']);
    grunt.registerTask('default', ['build:dev', 'less', 'bust', 'watch']);
    grunt.registerTask('test', 'nodeunit');
};
