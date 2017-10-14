module.exports = function(grunt) {
    // Project configuration.
    require('time-grunt')(grunt);

    var livereloadConfig = {
        port: 20001,
        key: grunt.file.read("/etc/ssl/panamapapier/privkey.pem"),
        cert: grunt.file.read("/etc/ssl/panamapapier/cert.pem"),
    };


    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),
        browserify: {
            options: {
                transform: [
                    ['jstify'], //html -> underscore templates
                ],
                browserifyOptions: {
                    debug: true
                }
            },
            dist: {
                files: {
                    "web/js/<%= pkg.name %>.src.js": ['frontend/src/app.js']
                }
            }
        },
        uglify: {
            dev: {
                files: {
                    "web/js/<%= pkg.name %>.dev.js": "web/js/<%= pkg.name %>.src.js"
                },
                options: {
                    sourceMapIncludeSources: true,
                    sourceMap: true,
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
                files: ['frontend/**/*.js'],
                tasks: ['build:js'],
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
        }
    });

    // Load the plugins
    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks('grunt-browserify');
    grunt.loadNpmTasks('grunt-contrib-uglify');

    // Default task(s).
    grunt.registerTask('build', ['build:js']);
    grunt.registerTask('build:js', ['browserify', 'uglify']);
    grunt.registerTask('default', ['build', 'watch']);

};
