.PHONY: ci cs test phpunit psalm phpstan

ci: phpstan phpunit psalm
cs: phpstan psalm
test: phpunit

phpunit:
	php ../../tests/phpunit/phpunit.php -c phpunit.xml.dist

psalm:
	./vendor/bin/psalm

phpstan:
	./vendor/bin/phpstan analyse -c phpstan.neon --no-progress
