#!/bin/bash

LOGS_GABO_FOLDER="/var/log/gabos"
LOGS_UPDATER="/updater"
LOGS_API="/api"
LOGS_UTILS="/utils"
LOGS_CI="/codeigniter"

if [ ! -d "$LOGS_GABO_FOLDER" ]; then
    mkdir $LOGS_GABO_FOLDER
fi

if [ ! -d "$LOGS_GABO_FOLDER$LOGS_UPDATER" ]; then
    mkdir $LOGS_GABO_FOLDER$LOGS_UPDATER
fi

if [ ! -d "$LOGS_GABO_FOLDER$LOGS_API" ]; then
    mkdir $LOGS_GABO_FOLDER$LOGS_API
fi

if [ ! -d "$LOGS_GABO_FOLDER$LOGS_UTILS" ]; then
    mkdir $LOGS_GABO_FOLDER$LOGS_UTILS
fi

if [ ! -d "$LOGS_GABO_FOLDER$LOGS_CI" ]; then
    mkdir $LOGS_GABO_FOLDER$LOGS_CI
fi

chmod 777 -R $LOGS_GABO_FOLDER