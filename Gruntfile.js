module.exports = function(grunt) {

    // Project configuration
    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),
        concat: {
            options: {
                stripBanners: true,
                banner: '/*! <%= pkg.title %> - v<%= pkg.version %>\n' +
                    ' * <%= pkg.homepage %>\n' +
                    ' * Copyright (c) <%= grunt.template.today("yyyy") %>;' +
                    ' * Licensed GPLv2+' +
                    ' */\n'
            },
            admin: {
                src: ['assets/js/src/backstretch_block_admin.js'],
                dest: 'assets/js/backstretch_block_admin.js'
            },
            frontend: {
                src: ['assets/js/src/backstretch_block.js'],
                dest: 'assets/js/backstretch_block.js'
            },
            backstretchStart: {
                src: ['assets/js/src/backstretchStart.js'],
                dest: 'assets/js/backstretchStart.js'
            }
        },
        jshint: {
            all: [
                'Gruntfile.js',
                'assets/js/src/**/*.js',
                'assets/js/test/**/*.js'
            ],
            options: {
                curly: true,
                eqeqeq: true,
                immed: true,
                latedef: true,
                newcap: true,
                noarg: true,
                sub: true,
                undef: true,
                boss: true,
                eqnull: true,
                globals: {
                    exports: true,
                    module: false
                }
            }
        },
        uglify: {
            all: {
                files: {
                    'assets/js/backstretch_block_admin.min.js': ['assets/js/backstretch_block_admin.js'],
                    'assets/js/backstretch_block.min.js': ['assets/js/backstretch_block.js'],
                    'assets/js/backstretchStart.min.js': ['assets/js/backstretchStart.js']
                },
                options: {
                    banner: '/*! <%= pkg.title %> - v<%= pkg.version %>\n' +
                        ' * <%= pkg.homepage %>\n' +
                        ' * Copyright (c) <%= grunt.template.today("yyyy") %>;' +
                        ' * Licensed GPLv2+' +
                        ' */\n',
                    mangle: {
                        except: ['jQuery']
                    },
                    sourceMap: true
                }
            }
        },
        test: {
            files: ['assets/js/test/**/*.js']
        },

        sass: {
            all: {
                options: {
                    sourcemap: true
                },
                files: {
                    'assets/css/backstretch_block.css': 'assets/css/sass/backstretch_block.scss'
                }
            }
        },

        cssmin: {
            options: {
                banner: '/*! <%= pkg.title %> - v<%= pkg.version %>\n' +
                    ' * <%= pkg.homepage %>\n' +
                    ' * Copyright (c) <%= grunt.template.today("yyyy") %>;' +
                    ' * Licensed GPLv2+' +
                    ' */\n'
            },
            minify: {
                expand: true,

                cwd: 'assets/css/',
                src: ['backstretch_block.css'],

                dest: 'assets/css/',
                ext: '.min.css'
            }
        },
        watch: {

            sass: {
                files: ['assets/css/sass/*.scss'],
                tasks: ['sass', 'cssmin'],
                options: {
                    debounceDelay: 500,
                    livereload: true
                }
            },

            scripts: {
                files: ['assets/js/src/**/*.js', 'assets/js/vendor/**/*.js'],
                tasks: ['jshint', 'concat', 'uglify'],
                options: {
                    debounceDelay: 500
                }
            }
        },
        clean: {
            main: ['release/<%= pkg.version %>']
        },
        copy: {
            // Copy the plugin to a versioned release directory
            main: {
                src: [
                    '**',
                    '!node_modules/**',
                    '!release/**',
                    '!.git/**',
                    '!.sass-cache/**',
                    '!css/src/**',
                    '!js/src/**',
                    '!img/src/**',
                    '!Gruntfile.js',
                    '!package.json',
                    '!.gitignore',
                    '!.gitmodules'
                ],
                dest: 'release/<%= pkg.version %>/'
            }
        },
        compress: {
            main: {
                options: {
                    mode: 'zip',
                    archive: './release/backstretch_block.<%= pkg.version %>.zip'
                },
                expand: true,
                cwd: 'release/<%= pkg.version %>/',
                src: ['**/*'],
                dest: 'backstretch_block/'
            }
        }
    });

    // Load other tasks
    grunt.loadNpmTasks('grunt-contrib-jshint');
    grunt.loadNpmTasks('grunt-contrib-concat');
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-cssmin');

    grunt.loadNpmTasks('grunt-contrib-sass');

    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks('grunt-contrib-clean');
    grunt.loadNpmTasks('grunt-contrib-copy');
    grunt.loadNpmTasks('grunt-contrib-compress');

    // Default task.

    grunt.registerTask('default', ['jshint', 'concat', 'uglify', 'sass', 'cssmin']);


    grunt.registerTask('build', ['default', 'clean', 'copy', 'compress']);

    grunt.util.linefeed = '\n';
};