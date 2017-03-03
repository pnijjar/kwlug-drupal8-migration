# Layout Plugin Compass/SASS Configuration
# NOTE: grunt will take its compass options from this file.

# Required
# -----------------------------------------------------------------------------
require 'susy'


# Directory paths
# -----------------------------------------------------------------------------
css_dir = 'css'
sass_dir = 'sass'


# Add the layouts assets directory, we can import base and any susy-panel for
# use in the UIKit. Change this if you are using a different layout plugin.
# -----------------------------------------------------------------------------
add_import_path '../../layout/site-builder/sass'


# Precision
# -----------------------------------------------------------------------------
Sass::Script::Number.precision = 5


# Environment
# -----------------------------------------------------------------------------
environment = :production


# Output Style
# -----------------------------------------------------------------------------
output_style = :compact
