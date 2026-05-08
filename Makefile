.PHONY: ci cs test phpunit phpcs psalm phpstan

ci: test cs
cs: phpcs phpstan psalm
test: phpunit

phpunit:
	composer phpunit

phpcs:
	cd ../.. && vendor/bin/phpcs -p -s --standard=$(shell pwd)/phpcs.xml

psalm:
	../../vendor/bin/psalm --config=psalm.xml

phpstan:
	../../vendor/bin/phpstan analyse --configuration=phpstan.neon --memory-limit=2G
