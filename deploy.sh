#!/bin/bash

docker login -e="." -u="$QUAY_USERNAME" -p="$QUAY_PASSWORD" quay.io
docker tag keboola/s3-extractor quay.io/keboola/s3-extractor:$TRAVIS_TAG
docker tag keboola/s3-extractor quay.io/keboola/s3-extractor:latest
docker images
docker push quay.io/keboola/s3-extractor:$TRAVIS_TAG
docker push quay.io/keboola/s3-extractor:latest

pip install --user awscli
# put aws in the path
export PATH=$PATH:$HOME/.local/bin
# needs AWS_ACCESS_KEY_ID and AWS_SECRET_ACCESS_KEY envvars
eval $(aws ecr get-login --region us-east-1)
docker tag keboola/s3-extractor:latest 147946154733.dkr.ecr.us-east-1.amazonaws.com/keboola/ex-s3:$TRAVIS_TAG
docker tag keboola/s3-extractor:latest 147946154733.dkr.ecr.us-east-1.amazonaws.com/keboola/ex-s3:latest
docker push 147946154733.dkr.ecr.us-east-1.amazonaws.com/keboola/ex-s3:$TRAVIS_TAG
docker push 147946154733.dkr.ecr.us-east-1.amazonaws.com/keboola/ex-s3:latest
