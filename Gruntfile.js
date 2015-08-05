module.exports = function (grunt) {
    // Project configuration.
    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),
        watch: {
            web: {
                files: ['web/**/*.php', 'src/**/*'],
                options: {
                    interrupt: true,
                    livereload: {
                        port: 7776
                    }
                }
            }
        }
    });

    // Load the plugins
    grunt.loadNpmTasks('grunt-contrib-watch');

    // Default task(s).
    grunt.registerTask('default', ['watch']);

};
