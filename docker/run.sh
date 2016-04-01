#!/bin/bash

IMAGE_NAME='bet4l1'
CID_FILE="/tmp/${IMAGE_NAME}.cid"
SCRIPT_DIR=$(dirname "$0")
WORKSPACE=`pwd`/..
DOCKER_WORKSPACE='/var/www/bet4l1'


docker rm -f -v "$IMAGE_NAME" > /dev/null 2>&1
rm -f "$CID_FILE"

echo "Running docker container"
docker run -d --cidfile="$CID_FILE" -v "${WORKSPACE}":${DOCKER_WORKSPACE}:rw -w ${DOCKER_WORKSPACE} --name $IMAGE_NAME $IMAGE_NAME
echo ""

while [ ! -f "$CID_FILE" ]
do
  sleep 1
done

CONTAINER_ID=$(cat "$CID_FILE")
CONTAINER_IP=$(docker inspect --format '{{ .NetworkSettings.IPAddress }}' $CONTAINER_ID)

echo "bet4l1 will be available at http://${CONTAINER_IP}"
echo " -> admin access: admin@bet4l1.fr / toto"
echo ""
