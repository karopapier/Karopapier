module.exports = function(grunt) {
    // Project configuration.
    require('time-grunt')(grunt);

    const keypath = '/etc/ssl/panamapapier/privkey.pem';
    const certpath = '/etc/ssl/panamapapier/cert.pem';
    const livereloadConfig = {
        port: 20012,
    };

    if (grunt.file.exists(keypath, certpath)) {
        livereloadConfig['key'] = grunt.file.read(keypath);
        livereloadConfig['cert'] = grunt.file.read(certpath);
    }

    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),
        asset_cachebuster: {
            options: {
                buster: Date.now(),
                ignore: [],
                htmlExtension: 'html',
            },
        },
        browserify: {
            options: {
                transform: [
                    [
                        'babelify', {'presets': ['env']},
                    ],
                ],
                browserifyOptions: {
                    debug: true,
                },
            },
            dist: {
                files: {
                    'public/js/<%= pkg.name %>.browserified.js': ['src/start.js'],
                },
            },
            gamestepup: {
                files: {
                    'public/js/GameStepUp.js': ['public/js/GameStepUp.src.js'],
                },
            }
        },
        uglify: {
            min: {
                files: {
                    // "src/<%= pkg.name %>.min.js": ["src/<%= pkg.name %>.browserified.js"]
                    'public/js/<%= pkg.name %>.min.js': [
                        'src/app/**/*.js',
                        'src/layout/**/*.js',
                        'src/model/**/*.js',
                        'src/collection/**/*.js',
                        'src/view/**/*.js',
                        'src/router/**/*.js'],
                },
                options: {
                    sourceMap: true,
                    sourceMapIncludeSources: true,
                    banner: '/*! <%= pkg.name %> backbone <%= grunt.template.today("yyyy-mm-dd") %> */\n',
                },
            },
            dev: {
                files: {
                    'public/js/<%= pkg.name %>.js': [
                        'src/app/**/*.js',
                        'src/layout/**/*.js',
                        'src/model/**/*.js',
                        'src/collection/**/*.js',
                        'src/view/**/*.js',
                        'src/router/**/*.js'],
                },
                options: {
                    sourceMapIncludeSources: true,
                    sourceMap: true,
                    banner: '/*! <%= pkg.name %> backbone dev <%= grunt.template.today("yyyy-mm-dd") %> */\n',
                    beautify: true,
                },
            },
        },
        cssmin: {
            options: {
                rebase: false,
                banner: '/*! <%= pkg.name %> cssmin <%= grunt.template.today("yyyy-mm-dd") %> */\n',
            },
            target: {
                files: {
                    'public/css/Karopapier.min.css': ['css/*.css', '!css/*.min.css'],
                },
            },
        },
        watch: {
            scripts: {
                files: ['src/**/*.js', '!src/<%= pkg.name %>*.js', 'test/**/*.js', 'public/js/GameStepUp.src.js'],
                tasks: ['build', 'publish', 'test'],
                options: {
                    interrupt: true,
                    livereload: livereloadConfig,
                },
            },
            templates: {
                files: ['templates/**/*.html', 'templates/**/*.tpl', 'index.template.html'],
                options: {
                    interrupt: true,
                    livereload: livereloadConfig,
                },
            },
            statics: {
                files: ['images/**/*', '!docs'],
                options: {
                    interrupt: true,
                    livereload: livereloadConfig,
                },
            },
            css: {
                files: ['css/**/*', '!css/**/*.min.css'],
                tasks: ['cssmin', 'asset_cachebuster'],
                options: {
                    interrupt: true,
                    livereload: livereloadConfig,
                },
            },
            tests: {
                files: ['public/test/**/*'],
                tasks: ['test'],
                options: {
                    interrupt: true,
                    livereload: livereloadConfig,
                },
            },
            spielwiese: {
                files: ['public/spielwiese/**/*'],
                options: {
                    interrupt: true,
                    livereload: livereloadConfig,
                },
            },
        },
        jsdoc: {
            dist: {
                src: [
                    'src/app/**/*.js',
                    'src/collection/**/*.js',
                    'src/layout/**/*.js',
                    'src/model/**/*.js',
                    'src/router/**/*.js',
                    'src/view/**/*.js',
                ],
                options: {
                    destination: 'public/doc',
                },
            },
        },
        nodeunit: {
            all: ['test/test.js'],
        },
        copy: {
            main: {
                files: [
                    {
                        expand: true,
                        src: ['src/**'],
                        dest: 'public/js/',
                    },
                ],
            },
        },
    });

    // Load the plugins
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks('grunt-contrib-cssmin');
    grunt.loadNpmTasks('grunt-contrib-copy');
    grunt.loadNpmTasks('grunt-jsdoc');
    grunt.loadNpmTasks('grunt-asset-cachebuster');
    grunt.loadNpmTasks('grunt-contrib-nodeunit');
    grunt.loadNpmTasks('grunt-browserify');

    // Default task(s).
    grunt.registerTask('build', ['copy', 'browserify', 'uglify', 'cssmin', 'asset_cachebuster']);
    grunt.registerTask('default', ['build', 'watch']);
    grunt.registerTask('build', ['browserify', 'uglify', 'cssmin', 'asset_cachebuster']);
    grunt.registerTask('spielwiese', ['spielwiese']);
    grunt.registerTask('publish', ['copy']);
    grunt.registerTask('test', 'nodeunit');
};
