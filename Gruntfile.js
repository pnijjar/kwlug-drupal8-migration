module.exports = function(grunt) {
	grunt.initConfig({

		pkg: grunt.file.readJSON('package.json'),

		compass: {
			uikit: {
				options: {
          config: 'styles/uikit/config.rb',
          basePath: 'styles/uikit',
          bundleExec: true
				}
			},
			layout_plugin: {
				options: {
          config: 'styles/layout_plugin/config.rb',
          basePath: 'styles/layout_plugin',
          bundleExec: true
				}
			},
			layout: {
				options: {
          config: 'layout/site-builder/config.rb',
          basePath: 'layout/site-builder',
          bundleExec: true
				}
			}
		},
    postcss: {
      css: {
        src: 'styles/css/components/**.css',
        options: {
          map: {
            inline: false,
            annotation: 'styles/css/components/maps/',
          },
          processors: [
            require('autoprefixer')({browsers: 'last 5 versions'})
          ]
        }
      }
    },
    csslint: {
      options: {
        csslintrc: '.csslintrc'
      },
      strict: {
        options: {
          import: 2
        },
        src: ['styles/css/components/**.css']
      }
    },
		watch: {
			uikit: {
				files: 'styles/uikit/components/**/*.scss',
				tasks: ['compass:uikit', 'postcss:css']
			},
      layout_plugin: {
				files: 'styles/layout_plugin/sass/**/*.scss',
				tasks: ['compass:layout_plugin']
			},
			layout: {
				files: 'layout/site-builder/sass/**/*.scss',
				tasks: ['compass:layout']
			}
		}
	});

	grunt.loadNpmTasks('grunt-postcss');
	grunt.loadNpmTasks('grunt-contrib-compass');
	grunt.loadNpmTasks('grunt-contrib-csslint');
	grunt.loadNpmTasks('grunt-contrib-watch');

	grunt.registerTask('default', ['watch:uikit']);
}
