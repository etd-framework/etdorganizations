/*!
 * ETD Solutions's Gruntfile
 * http://etd-solutions.com
 * Copyright 2017 - 2018 ETD Solutions
 * Licensed under Apache-2.0
 */

module.exports = function(grunt) {

    // Configuration du projet
    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),
        uglify: {
            options: {
                mangle: true,
                compress: true,
                beautify: false,
                sourceMap: false,
                preserveComments: false,
                banner: '/**!\n * @package     <%= pkg.name %>\n *\n * @version     <%= pkg.version %>\n * @copyright   Copyright (C) 2017 - <%= grunt.template.today("yyyy") %> ETD Solutions. Tous droits réservés.\n * @license     <%= pkg.license %> \n * @author      ETD Solutions http://etd-solutions.com\n*/',
                screwIE8: true,
                quoteStyle: 0
            },
            template: {
                src: 'dist/js/organization.js',
                dest: 'dist/js/organization.min.js'
            }
        },
        less: {
            options: {
                optimization: 10,
                sourceMap: false
            },
            template: {
                options: {
                    paths: [
                        'less'
                    ]
                },
                files: {
                    'dist/css/organization.css': 'less/organization.less'
                }
            }
        },
        postcss: {
            options: {
                map: false,
                processors: [
                    require('postcss-flexbugs-fixes'), // add vendor prefixes
                    require('autoprefixer')({
                        browsers: [
                            //
                            // Official browser support policy:
                            // http://v4-alpha.getbootstrap.com/getting-started/browsers-devices/#supported-browsers
                            //
                            'Chrome >= 35', // Exact version number here is kinda arbitrary
                            // Rather than using Autoprefixer's native "Firefox ESR" version specifier string,
                            // we deliberately hardcode the number. This is to avoid unwittingly severely breaking the previous ESR in the event that:
                            // (a) we happen to ship a new Bootstrap release soon after the release of a new ESR,
                            //     such that folks haven't yet had a reasonable amount of time to upgrade; and
                            // (b) the new ESR has unprefixed CSS properties/values whose absence would severely break webpages
                            //     (e.g. `box-sizing`, as opposed to `background: linear-gradient(...)`).
                            //     Since they've been unprefixed, Autoprefixer will stop prefixing them,
                            //     thus causing them to not work in the previous ESR (where the prefixes were required).
                            'Firefox >= 38', // Current Firefox Extended Support Release (ESR); https://www.mozilla.org/en-US/firefox/organizations/faq/
                            // Note: Edge versions in Autoprefixer & Can I Use refer to the EdgeHTML rendering engine version,
                            // NOT the Edge app version shown in Edge's "About" screen.
                            // For example, at the time of writing, Edge 20 on an up-to-date system uses EdgeHTML 12.
                            // See also https://github.com/Fyrd/caniuse/issues/1928
                            'Edge >= 12',
                            'Explorer >= 9',
                            // Out of leniency, we prefix these 1 version further back than the official policy.
                            'iOS >= 8',
                            'Safari >= 8',
                            // The following remain NOT officially supported, but we're lenient and include their prefixes to avoid severely breaking in them.
                            'Android 2.3',
                            'Android >= 4',
                            'Opera >= 12'
                        ]
                    })
                ]
            },
            template: {
                src: [
                    'css/organization.css'
                ]
            }
        },
        cssmin: {
            options: {
                keepSpecialComments: false,
                sourceMap: false,
                advanced: false
            },
            template: {
                options: {
                    compatibility: 'ie9'
                },
                files: {
                    'dist/css/organization.min.css': 'dist/css/organization.css'
                }
            }
        },
        concat: {
            options: {
                separator: ''
            },
            dist: {
                src: ['js/organization.js'], // la source
                dest: 'dist/js/organization.js' // la destination finale
            }
        },
        sync: {
            vendor: {
                verbose: true,
                compareUsing: 'mtime',
                updateAndDelete: true,
                ignoreInDest: [".gitignore"],
                files: [
                ]
            }
        },
        watch: {
            js: {
                files: ['js/**/*.js','!js/**/*.min.js'],
                tasks: ['concat', 'uglify']
            },
            less: {
                files: ['less/**/*.less'],
                tasks: ['css']
            }
        }
    });

    // On charge le plugin qui donne la tâche "uglify".
    grunt.loadNpmTasks('grunt-contrib-uglify');

    // On charge le plugin qui donne la tâche "less".
    grunt.loadNpmTasks('grunt-contrib-less');

    // On charge le plugin qui donne la tâche "postcss".
    grunt.loadNpmTasks('grunt-postcss');

    // On charge le plugin qui donne la tâche "cssmin".
    grunt.loadNpmTasks('grunt-contrib-cssmin');

    //On charge le plugin qui donne la tâche "concat".
    grunt.loadNpmTasks('grunt-contrib-concat');

    // On charge le plugin qui donne la tâche "sync".
    grunt.loadNpmTasks('grunt-sync');

    // On charge le plugin qui donne la tâche "watch".
    grunt.loadNpmTasks('grunt-contrib-watch');

    // Construit le JS
    grunt.registerTask('js', ['concat', 'uglify']);

    // Construit le CSS
    grunt.registerTask('css', ['less', 'postcss', 'cssmin']);

    // Copie les fichiers vendor de Composer
    grunt.registerTask('vendor', ['sync']);

    // Les tâches par défaut.
    grunt.registerTask('default', ['js', 'css']);

};