# Site Builder Compass/SASS Configuration
# NOTE: grunt will take its compass options from this file.

# Required
# -----------------------------------------------------------------------------
require 'sass-globbing'
require 'susy'


# Directory paths
# -----------------------------------------------------------------------------
css_dir = 'css'
sass_dir = 'sass'


# Precision
# -----------------------------------------------------------------------------
Sass::Script::Number.precision = 5


# Environment
# -----------------------------------------------------------------------------
environment = :production


# Output Style
# -----------------------------------------------------------------------------
output_style = :compact
