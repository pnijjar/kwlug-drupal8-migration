#!/bin/bash -x

SCRIPTFILE=/tmp/typescript
BINDIR=~/kwlug-drupal-v05/bin

[[ -f $SCRIPTFILE ]] && mv $SCRIPTFILE $SCRIPTFILE.latest
[[ -f $SCRIPTFILE.clean ]] && mv $SCRIPTFILE.clean $SCRIPTFILE.latest.clean
script -c $BINDIR/restart-d8demo.sh $SCRIPTFILE
cat $SCRIPTFILE | $BINDIR/clean-typescript.pl > $SCRIPTFILE.clean

