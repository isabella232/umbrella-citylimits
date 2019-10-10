module.exports = function(grunt) {
  'use strict';

  // Load all tasks
  require('load-grunt-tasks')(grunt);
  // Show elapsed time
  require('time-grunt')(grunt);

  // Force use of Unix newlines
  grunt.util.linefeed = '\n';

  // Find what the current theme's directory is, relative to the WordPress root
  var path = process.cwd().replace(/^[\s\S]+\/wp-content/, "\/wp-content");

  var cssLessFiles = {
    'css/child-style.css': 'less/child-style.less',
    'css/job-board.css': 'less/job-board.less'
  };

  grunt.initConfig({
    pkg: grunt.file.readJSON('package.json'),

    less: {
      compile: {
        options: {
          paths: ['less'],
          outputSourceFiles: true,
          sourceMapBasepath: path,
          compress: true
        },
        files: cssLessFiles
      }
    },

    watch: {
      less: {
        files: [
          'less/*.less',
        ],
        tasks: [
          'less:compile',
        }
      }
    },

    version: {
      src: [
        'package.json'
      ],
      css: {
        options: {
          prefix: 'Version: '
        },
        src: [
          'css/child-style.css',
        ]
      },
      readme: {
        options: {
          prefix: '\\*\\*Current version:\\*\\* v'
        },
        src: [
          'readme.md'
        ]
      }
    }
  });

  // Build assets, docs and language files
  grunt.registerTask('build', 'Build less files', [
    'less',
  ]);
}
