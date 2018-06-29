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
                transform: [
                    ['babelify', {presets: ['es2015']}],
                    ['jstify'] //html -> underscore templates
                ],
            },
            dev: {
                files: {
                    'web/js/<%= pkg.name %>.dev.js': ['frontend/src/app/KaroApp.js'],
                },
                browserifyOptions: {
                    debug: true,
                },
            },
            dist: {
                files: {
                    'web/js/<%= pkg.name %>.src.js': ['frontend/src/app/KaroApp.js'],
                },
            },
            gamestepup: {
                files: {
                    'backbone/public/js/GameStepUp.js': ['backbone/public/js/GameStepUp.src.js'],
                },
            }
        },
        uglify: {
            dev: {
                files: {
                    'web/js/<%= pkg.name %>.dev.js': 'web/js/<%= pkg.name %>.src.js',
                },
                options: {
                    banner: '/*! <%= pkg.name %> <%= grunt.template.today("yyyy-mm-dd HH:MM:ss") %> */\n',
                    beautify: true,
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
                tasks: ['build:dev', 'bust'],
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
            web: {
                files: ['web/**/*.php', 'src/**/*'],
                options: {
                    interrupt: true,
                    livereload: livereloadConfig,
                },
            },
            gamestepup: {
                files: ['backbone/src/**/*.js', 'backbone/public/js/GameStepUp.src.js'],
                tasks: ['build:dev', 'bust'],
                options: {
                    interrupt: true,
                    livereload: livereloadConfig,
                },
            }
        },
        less: {
            app: {
                options: {
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
        },
        eslint: {
            target: ['frontend/src/**/*.js'],
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
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-less');
    grunt.loadNpmTasks('grunt-contrib-nodeunit');
    grunt.loadNpmTasks('grunt-eslint');
    grunt.loadNpmTasks('grunt-shell');

    // Default task(s).
    grunt.registerTask('build', ['build:prod', 'less', 'bust']);
    grunt.registerTask('build:dev', ['browserify:dev', 'browserify:gamestepup', 'style', 'test']);
    grunt.registerTask('build:prod', ['browserify:dist', 'browserify:gamestepup', 'uglify', 'style']);
    grunt.registerTask('style', ['eslint']);
    grunt.registerTask('bust', ['shell:bust']);
    grunt.registerTask('default', ['build:dev', 'less', 'watch']);
    grunt.registerTask('test', 'nodeunit');
};
