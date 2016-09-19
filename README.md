# Keboola S3 Extractor 

Download files from S3 to `/data/out/files`. 

## Features
- Wildcard - use `*` or `%` at the end for wildcards; wildcards do not download subfolders.

## Development

### Preparation

- Create AWS S3 bucket and IAM user using [`aws-services.json`](./aws-services.json) CloudFormation template.
- Create `.env` file. Use output of `aws-services` CloudFront stack to fill the variables and your Redshift credentials.
```
AWS_S3_BUCKET=
AWS_REGION=
PREPARE_TESTS_AWS_ACCESS_KEY=
PREPARE_TESTS_AWS_SECRET_KEY=
TESTS_AWS_ACCESS_KEY=
TESTS_AWS_SECRET_KEY=
```

- Build Docker images
```
docker-compose build
```

- Install Composer packages

```
docker-compose run --rm php composer install --prefer-dist --no-interaction
```

Upload test fixtures to S3:
```
docker-compose run php php ./tests/loadS3.php
```

### Tests Execution
Run tests with following command.

```
docker-compose run --rm php-tests
```

Tests are executed against real S3. S3 credentials have to be provided.
