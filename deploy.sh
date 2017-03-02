#!/bin/bash
pip install --user awscli
# put aws in the path
export PATH=$PATH:$HOME/.local/bin
# needs AWS_ACCESS_KEY_ID and AWS_SECRET_ACCESS_KEY envvars
eval $(aws ecr get-login --region us-east-1)
docker tag keboola/s3-extractor:latest 147946154733.dkr.ecr.us-east-1.amazonaws.com/keboola/ex-s3:$TRAVIS_TAG
docker tag keboola/s3-extractor:latest 147946154733.dkr.ecr.us-east-1.amazonaws.com/keboola/ex-s3:latest
docker images
docker push 147946154733.dkr.ecr.us-east-1.amazonaws.com/keboola/ex-s3:$TRAVIS_TAG
docker push 147946154733.dkr.ecr.us-east-1.amazonaws.com/keboola/ex-s3:latest
