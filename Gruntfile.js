module.exports = function(grunt) {
    // Project configuration.
    require('time-grunt')(grunt);

    var keypath = "/etc/ssl/panamapapier/privkey.pem";
    var certpath = "/etc/ssl/panamapapier/cert.pem";
    var livereloadConfig = {
        port: 20001,
    };

    if (grunt.file.exists(keypath, certpath)) {
        livereloadConfig["key"] = grunt.file.read(keypath);
        livereloadConfig["cert"] = grunt.file.read(certpath);
    }

    var cleanCssOptions = {
        level: 2
    };

    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),
        browserify: {
            options: {
                transform: [
                    ['babelify', {presets:["es2015"]}],
                    ['jstify'] //html -> underscore templates
                ],
                browserifyOptions: {
                    debug: true
                }
            },
            dist: {
                files: {
                    "web/js/<%= pkg.name %>.src.js": ['frontend/src/app/KaroApp.js']
                }
            }
        },
        uglify: {
            dev: {
                files: {
                    "web/js/<%= pkg.name %>.dev.js": "web/js/<%= pkg.name %>.src.js"
                },
                options: {
                    banner: '/*! <%= pkg.name %> <%= grunt.template.today("yyyy-mm-dd HH:MM:ss") %> */\n',
                    beautify: true
                }
            },
            min: {
                files: {
                    "web/js/<%= pkg.name %>.min.js": "web/js/<%= pkg.name %>.src.js"
                },
                options: {
                    sourceMapIncludeSources: false,
                    sourceMap: false,
                    banner: '/*! <%= pkg.name %> <%= grunt.template.today("yyyy-mm-dd") %> */\n'
                }
            }
        },
        watch: {
            app: {
                files: ['frontend/src/**/*', 'frontend/templates/**/*'],
                tasks: ['build:js'],
                options: {
                    interrupt: true,
                    livereload: livereloadConfig
                }
            },
            css: {
                files: ['frontend/css/**/*'],
                tasks: ['less'],
                options: {
                    interrupt: true,
                    livereload: livereloadConfig
                }
            },
            web: {
                files: ['web/**/*.php', 'src/**/*'],
                options: {
                    interrupt: true,
                    livereload: livereloadConfig
                }
            }
        },
        less: {
            app: {
                options: {
                    expand: true,
                    plugins: [
                        new (require('less-plugin-autoprefix')),
                        new (require('less-plugin-clean-css'))(cleanCssOptions)
                    ]
                },
                files: {
                    "web/css/app.css": "frontend/css/KaroApp.less"
                }
            },
            css: {
                options: {
                    plugins: [
                        new (require('less-plugin-autoprefix')),
                        new (require('less-plugin-clean-css'))(cleanCssOptions)
                    ]
                },
                files: {
                    "web/css/previous.css": "frontend/css/karo.css",
                    "web/css/theme.css": "frontend/css/theme.css"
                }
            }
        },
        eslint: {
            target: ['frontend/src/**/*.js']
        }
    });

    // Load the plugins
    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks('grunt-browserify');
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-less');
    grunt.loadNpmTasks('grunt-eslint');

    // Default task(s).
    grunt.registerTask('build', ['build:js', 'less']);
    grunt.registerTask('build:js', ['browserify', 'uglify', 'style']);
    grunt.registerTask('style', ['eslint']);
    grunt.registerTask('default', ['build', 'watch']);

};
