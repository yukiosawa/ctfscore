#!/bin/sh

# load common setting
. $(dirname $0)/common.sh

echo "Copying contents files."

ORG_CWD=$(pwd)

FILE_BKUP_DIR="$DIR/backup/files"
IMG_BKUP_FILE=$(date +%F-%H%M%S).img.tgz
AUDIO_BKUP_FILE=$(date +%F-%H%M%S).audio.tgz
ATTACH_BKUP_FILE=$(date +%F-%H%M%S).attachments.tgz

IMG_PATH="public/assets/img/usr"
AUDIO_PATH="public/assets/audio/usr"
ATTACH_PATH="ctfadmin/attachments"

if [ ! -e $FILE_BKUP_DIR ]; then
  mkdir -p $FILE_BKUP_DIR
fi

cd $APP

# image files
tar zcvf $ORG_CWD/$FILE_BKUP_DIR/$IMG_BKUP_FILE $IMG_PATH
if [ $? -ne 0 ]; then
    exit 1
fi
echo "+Done. Saved image files as $FILE_BKUP_DIR/$IMG_BKUP_FILE"

# audio files
tar zcvf $ORG_CWD/$FILE_BKUP_DIR/$AUDIO_BKUP_FILE $AUDIO_PATH
if [ $? -ne 0 ]; then
    exit 1
fi
echo "+Done. Saved audio files as $FILE_BKUP_DIR/$AUDIO_BKUP_FILE"

# attachment files
tar zcvf $ORG_CWD/$FILE_BKUP_DIR/$ATTACH_BKUP_FILE $ATTACH_PATH
if [ $? -eq 0 ]; then
    echo "+Done. Saved attachment files as $FILE_BKUP_DIR/$ATTACH_BKUP_FILE"
    exit 0
else
    exit 1
fi

