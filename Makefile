.PHONY: ci cs test phpunit phpcs psalm phpstan

ci: test cs
cs: phpcs phpstan psalm
test: phpunit

phpunit:
	php ../../tests/phpunit/phpunit.php -c phpunit.xml.dist

phpcs:
	cd ../.. && vendor/bin/phpcbf -p -s --standard=$(shell pwd)/phpcs.xml

psalm:
	../../vendor/bin/psalm --config=psalm.xml

phpstan:
	../../vendor/bin/phpstan analyse --configuration=phpstan.neon --memory-limit=2G
